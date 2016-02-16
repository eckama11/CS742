<?php

class DBInterface {
	private $dbh;

    /** 
     * Constructs a new DBInterface instance with the specified connection parameters.
     * @param   String $dbServer    The name/IP of the MySQL server instance to connect to.
     * @param   String $dbName      The name of the initial database for the connection.
     * @param   String $dbUsername  The username to use for authentication.
     * @param   String $dbPassword  The password to use for authentication.
     */
    public function __construct( $dbServer, $dbName, $dbUsername, $dbPassword ) {
        $dsn = "mysql:host=$dbServer;dbname=$dbName";
        $this->dbh = new PDO($dsn, $dbUsername, $dbPassword);
    } // __construct

    public function formatErrorMessage($stmt, $message) {
        if (!$stmt)
            $stmt = $this->dbh;
        list($sqlState, $driverErrorCode, $driverErrorMessage) = $stmt->errorInfo();
        return $message .": [$sqlState] $driverErrorCode: $driverErrorMessage";
    } // formatSqlErrorMessage($pdoErrorInfo)

    /**
     * Reads a LoginSession object from the database.
     * @param   int $sessionId  The session ID of the LoginSession record to retrieve.
     * @return  LoginSession    The LoginSession instance for the specified session ID, if one exists.
     */
    public function readLoginSession( $sessionId ) {
        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "SELECT sessionId, authenticatedEmployee ".
                        "FROM loginSession ".
                        "WHERE sessionId=?"
                );

            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare login session query"));
        }


        $stmt->execute(Array($sessionId));
        $res = $stmt->fetchObject();
        if ($res === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to retrieve specified session from database"));

        return new LoginSession($res->sessionId, $this->readEmployee($res->authenticatedEmployee));
    } // readLoginSession

    /**
     * Updates a LoginSession object in the database.
     * @param   LoginSession $session  The session to update.
     * @return  LoginSession    The LoginSession which was passed in.
     */
    public function writeLoginSession( LoginSession $session ) {
        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "UPDATE loginSession ".
                        "SET authenticatedEmployee=:authenticatedEmployee ".
                        "WHERE sessionID=:sessionId"
                );

            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to login session update"));
        }

        $success = $stmt->execute(Array(
            ':sessionId' => $session->sessionId,
            ':authenticatedEmployee' => $session->authenticatedEmployee
        ));

        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to update specified session in database"));

        return $session;
    } // writeLoginSession

   /**
     * Authenticates an employee and creates a LoginSession.
     *
     * @param   String  $username   The username of the employee to authenticate.
     * @param   String  $password   The password to use for authentication.
     *
     * @return  LoginSession    A new LoginSession instance for the authenticated employee.
     */
    public function createLoginSession( $username, $password ) {
        // Authenticate the employee based on username/password
        static $loginStmt;
        static $insertStmt;
        if ($loginStmt == null) {
            $loginStmt = $this->dbh->prepare(
                  "SELECT id ".
                    "FROM employee ".
                    "WHERE username=:username ".
                        "AND password=:password "
                );

            if (!$loginStmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare login query"));

            $insertStmt = $this->dbh->prepare(
                    "INSERT INTO loginSession ( ".
                            "sessionId, authenticatedEmployee ".
                        ") VALUES ( ".
                            ":sessionId, :authenticatedEmployee ".
                        ")"
                );

            if (!$insertStmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare login session insert"));
        }

        $success = $loginStmt->execute(Array(
                ':username' => $username,
                ':password' => $password
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($loginStmt, "Unable to query database to authenticate employee"));

        $row = $loginStmt->fetchObject();
        if ($row === false)
            throw new Exception("Unable to authenticate employee, incorrect username or password");

        $authenticatedEmployee = $row->id;

        // Generate a new session ID
        // This may be somewhat predictable, but should be strong enough for purposes of the demo
        $sessionId = md5(uniqid(microtime()) . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        $rv = new LoginSession( $sessionId, $this->readEmployee($authenticatedEmployee) );
        // Create the loginSession record
        $success = $insertStmt->execute(Array(
                ':sessionId' => $sessionId,
                ':authenticatedEmployee' => $authenticatedEmployee
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($insertStmt, "Unable to create session record in database"));

        return $rv;
    } // createLoginSession

    /**
     * Removes a login session from the database.
     * @param   LoginSession    $session    The session to destroy.
     */
    public function destroyLoginSession( LoginSession $session ) {
        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "DELETE FROM loginSession ".
                        "WHERE sessionId = ?"
                );

            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare login session delete"));
        }

        $success = $stmt->execute(Array( $session->sessionId ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to destroy session record"));
    } // destroyLoginSession
	
	/**
     * Reads all Employees from the database given a division and active status.
     *
     * @param   string 			$div The division of the employee to retrieve.
     *
     * @return  ArrayEmployee   An instance of Employee.
     */
    public function readEmployeeFromDiv( $div ) {
        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "SELECT id, username, password, employeeType, name, status, division ".
                        "FROM employee ".
                        "WHERE division = ? ".
                        "AND status = 'Active'"
                );

            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare readEmployeeFromDiv query"));
        }

        $success = $stmt->execute(Array( $div ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readEmployeeFromDiv record"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Employee(
                $row->id,
                $row->username,
                $row->password,
                $row->employeeType,
                $row->name,
                $row->status,
                $row->division
        	);
        } // while
		//throw new Exception($row->employeeType);
        return $rv;
    } // readEmployee
	
	/**
     * Reads an Employee from the database.
     *
     * @param   int $id The ID of the employee to retrieve.
     * @param   DateTime|null $effectiveDate The date for which to determine the value for the
     *              current property.  If a DateTime is given, current will be set to the
     *              EmployeeHistory record that is active for the specified date, or null if no
     *              such record exists.  If not specified, defaults to the current date.
     *
     * @return  Employee    An instance of Employee.
     */
    public function readEmployee( $id ) {
        if (!is_numeric($id))
            throw new Exception("Parameter \$id must be an integer");
        $id = (int) $id;

        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "SELECT id, username, password, employeeType, name, status, division ".
                        "FROM employee ".
                        "WHERE id = ?"
                );

            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee query"));
        }

        $success = $stmt->execute(Array( $id ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for employee record"));

        $row = $stmt->fetchObject();
        if ($row === false)
            throw new Exception("No such employee: $id");
		//throw new Exception($row->employeeType);
        return new Employee(
                $row->id,
                $row->username,
                $row->password,
                $row->employeeType,
                $row->name,
                $row->status,
                $row->division
        );
         
    } // readEmployee
    
    /**
     * Reads a list of all employees from the database.
     * @return  Array[Employee] Array of matching Employee instances.
     */
    public function readEmployees() {
        static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, username, password, employeeType, name, status, division ".
        			"FROM employee ".
        			"ORDER BY name"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee query"));
        }
        
        $success = $stmt->execute(Array( ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for employee records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Employee(
                    $row->id,
                    $row->username,
                    $row->password,
                    $row->employeeType,
                    $row->name,
                    $row->status,
                    $row->division
                );
        } // while

        return $rv;
    } // readEmployees

    
     /**
     * Tests whether a specific username is in currently assigned to an Employee or not.
     *
     * @param   String  $username   The username to test for.
     *
     * @return  Boolean    True if the username is assigned to an existing Employee, false if not.
     */
    public function isUsernameInUse( $username ) {
        static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                  "SELECT id ".
                    "FROM employee ".
                    "WHERE username=:username"
                );
        }

        $success = $stmt->execute(Array(
                ':username' => $username
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for username"));

        $row = $stmt->fetchObject();
        return ($row !== false);
    } // isUsernameInUse
    
    /**
     * Adds Employee to employee database
     *
     * @param	Employee $emp 	employee to be added to database
     *
     * @return	Employee		returns employee added to database
     */
    public function hireEmployee( Employee $emp ) {
    	static $stmtInsert;
        static $stmtUpdate;
        if ($stmtInsert == null) {
            $stmtInsert = $this->dbh->prepare(
                    "INSERT INTO employee ( ".
                            "username, password, employeeType, name, status, division".
                        ") VALUES ( ".
                            ":username, :password, :employeeType, :name, :status, :division ".
                        ")"
                );

            if (!$stmtInsert)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee insert"));
    
    		$stmtUpdate = $this->dbh->prepare(
                    "UPDATE employee SET ".
                            "username = :username,
                             password = :password, 
                             employeeType = :employeeType,
                             name = :name,
                             status = :status,
                             division = :division ".
                        "WHERE id = :id"
                );

            if (!$stmtUpdate)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee update"));
        }
        
        $params = Array(
        		':username' => $emp->username,
                ':password' => $emp->password,
                ':employeeType' => $emp->employeeType,
                ':name' => $emp->name,
                ':status' => $emp->status,
                ':division' => $emp->division
            );

        if ($emp->id == 0) {
            $stmt = $stmtInsert;
        } else {
            $params[':id'] = $emp->id;
            $stmt = $stmtUpdate;
        }
        $success = $stmt->execute($params);
        if ($success == false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to store employee record in database"));

        if ($emp->id == 0)
            $newId = $this->dbh->lastInsertId();
        else
            $newId = $emp->id;
 
        return new Employee(
                $newId,
                $emp->username,
                $emp->password,
                $emp->employeeType,
                $emp->name,
                $emp->status,
                $emp->division
            );
    }//hireEmployee
    
    /**
     * Changes Employee status to Inactive
     *
     * @param	Employee $emp 	employee to be changed in database
     *
     * @return	Boolean			True if the updated Employee, false if not.
     */
    public function fireEmployee( $emp ) {
    	$emp = (int) $emp;
    	static $stmtUpdate;
    	if ($stmtUpdate == null) {
			$stmtUpdate = $this->dbh->prepare(
						"UPDATE employee SET ".
								"status = :status ".
							"WHERE id = :id"
			);
		
			if (!$stmtUpdate)
					throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee update"));
		}
		
		$success = $stmtUpdate->execute(Array(
                ':status' => "Inactive",
                ':id' => $emp
		));
		if ($success == false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to update employee status record in database"));
		
		$row = $stmtUpdate->fetchObject();
        return ($row !== false);
	}
	
	/**
     * Changes Employee division to new division
     *
     * @param	Int $emp		employee to be change division
     *
     * @param	String $div 	division to be changed in database for employee
     *
     * @return	Boolean			True if the updated Employee, false if not.
     */
     public function addEmployeeProjectList( $empId, $div ) {
     	$emp = (int) $emp;
    	static $stmtUpdate;
    	if ($stmtUpdate == null) {
			$stmtUpdate = $this->dbh->prepare(
						"UPDATE employee SET ".
								"division = :division ".
							"WHERE id = :id"
			);
		
			if (!$stmtUpdate)
					throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee update"));
		}
		
		$success = $stmtUpdate->execute(Array(
                ':division' => $div,
                ':id' => $emp
		));
		if ($success == false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to update employee division record in database"));
		
		$row = $stmtUpdate->fetchObject();
        return ($row !== false);
     }//moveEmployee
	
	/**
     * Changes Employee division to new division
     *
     * @param	Int $emp		employee to be change division
     *
     * @param	String $div 	division to be changed in database for employee
     *
     * @return	Boolean			True if the updated Employee, false if not.
     */
     public function moveEmployee( $emp, $div ) {
     	$emp = (int) $emp;
    	static $stmtUpdate;
    	if ($stmtUpdate == null) {
			$stmtUpdate = $this->dbh->prepare(
						"UPDATE employee SET ".
								"division = :division ".
							"WHERE id = :id"
			);
		
			if (!$stmtUpdate)
					throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee update"));
		}
		
		$success = $stmtUpdate->execute(Array(
                ':division' => $div,
                ':id' => $emp
		));
		if ($success == false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to update employee division record in database"));
		
		$row = $stmtUpdate->fetchObject();
        return ($row !== false);
     }//moveEmployee
    
    /**
     * Reads a list of all employeeProjectList  
     * from the database that are assign to an employee.
     *
     * @param	$div	Division that is being read
     *
     * @return  Array[EmployeeProjectList] Array of matching EmployeeProjectList instances.
     **/
     public function readDivProjects( $div ) {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT C.id, projectName B FROM employee C ".
        			"INNER JOIN employeeProjectList B ".
        				"ON C.id = B.employeeID ".
        			"INNER JOIN projectDivisionList A ".
        				"ON B.projectID = A.projectID ".
        				"AND A.divisionName = :div"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare deassign query"));
        }
        
        $success = $stmt->execute(Array(
        		':div' => $div	
        ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for deassign records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new DeassignObject(
                    $row->id,
                    $row->B
                );
        } // while
        return $rv;
	 }//readDivProjects
     
	/**
     * Reads a list of all employeeProjectList from the database. 
     * This is a list of employees and the projects they are assigned.
     *
     * @return  Array[EmployeeProjectList] Array of matching EmployeeProjectList instances.
     **/
     public function readEmployeeProjects( $emp ) {
     	$emp = (int) $emp;
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, employeeID, projectID, projectName ".
        			"FROM employeeProjectList ".
        			"WHERE employeeID = :employeeID ".
        			"ORDER BY projectName"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare employee query"));
        }
        
        $success = $stmt->execute(Array(
        		':employeeID' => $emp	
        ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for employee records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new EmployeeProjectList(
                    $row->id,
                    $row->employeeID,
                    $row->projectID,
                    $row->projectName
                );
        } // while

        return $rv;
	 }//readEmployeeProjects
	 
	 /**
     * Reads a projectID from the project database. 
     * This is a list of projects comprosed of id, name, timeEstimate, and status.
     *
     * @return  Int	$projectID	The id of the current project name.
     **/
     public function readProjectID($projectName) {
		static $stmt;
		if ($stmt == null) {
			$stmt = $this->dbh->prepare(
				"SELECT id FROM project WHERE name = :projectName"
			);
		
			if(!$stmt)
				throw new Exception($this->formatErrorMessage(null, "Unable to prepare readProjectsID query."));
		}
	
		$success = $stmt->execute(Array(
			':projectName' => $projectName
		));
		if ($success === false)
			throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for project id."));

		$rv = $stmt->fetchObject();
		if (!$rv)
			throw new Exception("No such project id");
        $rv = $rv->id;
		return $rv;
     }//readProjectID
     
     /**
     * Writes a projectTimeHistory row into database.
     *
     * @param	$employeeID 
     * @param	$projectID 
     * @param	$projectName 
     * @param	$time
     * 
     * @return
     **/
     public function addHours($employeeID, $projectID, $projectName, $time ) {
    	$id = 0;
    	static $stmtInsert;
        static $stmtUpdate;
        if ($stmtInsert == null) {
            $stmtInsert = $this->dbh->prepare(
                    "INSERT INTO projectTimeHistory ( ".
                            "employeeID, projectID, projectName, time".
                        ") VALUES ( ".
                            ":employeeID, :projectID, :projectName, :time ".
                        ")"
                );
            if (!$stmtInsert)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare add hours insert"));
        }
        
        $params = Array(
        		':employeeID' => $employeeID,
        		':projectID' => $projectID,
        		':projectName' => $projectName,
        		':time' => $time
            );

        if ($id == 0) {
            $stmt = $stmtInsert;
        } 
        $success = $stmt->execute($params);
        if ($success == false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to add hours to project in database"));

        $newId = $this->dbh->lastInsertId();
        
        return new ProjectTimeHistory(
			$newId,
			$employeeID,
			$projectID,
			$projectName,
			$time
		);
		
    }//addHours
    
    /**
     * Delete a employeeProjectList row into database.
     *
     * @param	$employeeID 
     * @param	$projectName
     * 
     * @return
     **/
     public function deleteProjectList($employeeID, $projectName ) {
    	static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                    "DELETE FROM employeeProjectList ".
                            "WHERE employeeID = :employeeID ".
                            "AND projectName = :projectName"
                );
                            
            if (!$stmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare delete employeeProjectList"));
        }
        $success = $stmt->execute(Array(
        		':projectName' => $projectName,
        		':employeeID' => $employeeID
        	));

        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to delete employeeProjectList row in database"));

        $row = $stmt->fetchObject();
        return ($row !== false);
    }//deleteProjectList
    
    /**
     * Select all projects from database that match a division.
     *
     * @param	$div 
     * 
     * @return
     **/
     public function readProjectsForDiv($div) {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT A.id, A.name, A.timeEstimate, A.status FROM project A ".
        			"INNER JOIN projectDivisionList B ".
        				"ON A.id = B.projectID ".
        				"AND B.divisionName = :div ".
        				"AND A.status = 'Active'"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readProjectsForDiv query"));
        }
        
        $success = $stmt->execute(Array(
        		':div' => $div	
        ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readProjectsForDiv records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Project(
                    $row->id,
                    $row->name,
                    $row->timeEstimate,
                    $row->status
                );
        } // while
        return $rv;
     }
     //readProjectsForDiv
     
     /**
     * Tests whether a specific project and employee
     * is in currently in employeeProjectList.
     *
     * @param   String  $username   The username to test for.
     *
     * @return  Boolean    True if the employee is assigned to an existing project, false if not.
     */
     public function isEmployeeProjectInUse($empId, $proId) {
     	static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                  "SELECT id ".
                    "FROM employeeProjectList ".
                    "WHERE employeeID = :employeeID AND projectID = :projectID"
                );
        }

        $success = $stmt->execute(Array(
                ':employeeID' => $empId,
                ':projectID' => $proId
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for isEmployeeProjectInUse"));

        $row = $stmt->fetchObject();
        return ($row !== false);
     }//isEmployeeProjectInUse
     
     /**
     * Insert employeeProjectList row to database.
     *
     * @param	int		$id 			new id
     * @param	int		$employeeID		employeeID that is being added to row
     * @param	int		$projectID		projectID that is being added to row
     * @param	string	$projectName	projectName that is being added to row
     * 
     * @return
     **/
    public function insertProjectList($id, $employeeID, $projectID, $projectName) {
    	static $insertStmt;
        
            $insertStmt = $this->dbh->prepare(
                    "INSERT INTO employeeProjectList ( ".
                            "employeeID, projectID, projectName ".
                        ") VALUES ( ".
                            ":employeeID, :projectID, :projectName ".
                        ")"
                );

            if (!$insertStmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare employeeProjectList insert"));
        
        $id = $this->dbh->lastInsertId();
       //
        $rv = new EmployeeProjectList($id, $employeeID, $projectID, $projectName);
        
        // Create the employeeProjectList record
        $success = $insertStmt->execute(Array(
                ':employeeID' => $employeeID,
                ':projectID' => $projectID,
                ':projectName' => $projectName
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($insertStmt, $id));

        return $rv;
    }//insertProjectList
    
    /**
     * Select all divisions from database. 
     * 
     * @return	all division ids and name
     **/
     public function readDivisions() {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, name FROM division"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readDivisions query"));
        }
        
        $success = $stmt->execute();
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readDivsions records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Division(
                    $row->id,
                    $row->name
                );
        } // while
        return $rv;
     }// readDivisions
     
     /**
     * Select Project from database with name
     * 
     * @return	Project
     **/
     public function readProject($name) {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, name, timeEstimate, status FROM project WHERE name = :name"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readProjects query"));
        }
        
        $success = $stmt->execute(Array(
                ':name' => $name
            ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readProjects records"));

        $row = $stmt->fetchObject();
        if ($row === false)
            throw new Exception("No such project: $name");    
        return new Project(
                    $row->id,
                    $row->name,
                    $row->timeEstimate,
                    $row->status
        );
     }//readProject
     
     /**
     * Select all projects from database. 
     * 
     * @return	all projects ids and name
     **/
     public function readProjects() {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, name, timeEstimate, status FROM project"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readProjects query"));
        }
        
        $success = $stmt->execute();
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readProjects records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Project(
                    $row->id,
                    $row->name,
                    $row->timeEstimate,
                    $row->status
                );
        } // while
        return $rv;
     }//readProjects
     
     /**
     * Tests whether a specific division and project
     * is in currently in divisionProjectList.
     *
     * @param   int			$projectId		The username to test.
     * @param	string		$divisionName	The divsion name to test.
     *
     * @return  Boolean		True if the division is assigned to an existing project, false if not.
     */
     public function isProjectDivisionInUse($projectID, $divisionName) {
     	static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                  "SELECT id ".
                    "FROM projectDivisionList ".
                    "WHERE projectID = :projectID AND divisionName = :divisionName"
                );
        }

        $success = $stmt->execute(Array(
                ':projectID' => $projectID,
                ':divisionName' => $divisionName
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for isProjectDivisionInUse"));

        $row = $stmt->fetchObject();
        return ($row !== false);
     }//isProjectDivisionInUse
     
     /**
     * Inserts a projectDivsionList row in database
     * is in currently in divisionProjectList.
     *
     * @param   int			$projectId		The username to test.
     * @param	string		$divisionName	The divsion name to test.
     *
     * @return  ProjectDivisionList		Returns a ProjectDivisionList.
     */
     public function writeProjectDivisionList($projectID, $divisionName) {
     	static $insertStmt;
        
            $insertStmt = $this->dbh->prepare(
                    "INSERT INTO projectDivisionList ( ".
                            "projectID, divisionName ".
                        ") VALUES ( ".
                            ":projectID, :divisionName ".
                        ")"
                );

            if (!$insertStmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare projectDivisionList insert"));
        
        $id = $this->dbh->lastInsertId();
       //
        $rv = new ProjectDivisionList($id, $projectID, $projectName);
        
        // Create the employeeProjectList record
        $success = $insertStmt->execute(Array(
                ':projectID' => $projectID,
                ':divisionName' => $divisionName
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($insertStmt, "Unable to query database for writeProjectDivisionList records"));

        return $rv;
     }//writeProjectDivisionList
     
     /**
     * Inserts a project row in database.
     *
     * @param   string	$name			Name of project.
     * @param	int		$timeEstimate	Estimate of time it will take to make.
     * @param	enum	$status			Active for new project
     *
     * @return  Project		returns a Project object
     */
     public function writeProject($name, $timeEstimate, $status) {
     	static $insertStmt;
        
            $insertStmt = $this->dbh->prepare(
                    "INSERT INTO project ( ".
                            "name, timeEstimate, status ".
                        ") VALUES ( ".
                            ":name, :timeEstimate, :status ".
                        ")"
                );

            if (!$insertStmt)
                throw new Exception($this->formatErrorMessage(null, "Unable to prepare project insert"));
        
        $id = $this->dbh->lastInsertId();
       //
        $rv = new Project($id, $name, $timeEstimate, $status);
        
        // Create the employeeProjectList record
        $success = $insertStmt->execute(Array(
                ':name' => $name,
                ':timeEstimate' => $timeEstimate,
                ':status' => $status
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($insertStmt, "Unable to query database for writeProject records"));

        return $rv;
     }//writeProject
     
     /**
     * Tests whether a specific name for project is in use.
     *
     * @param	string		$name	The project name to check.
     *
     * @return  Boolean		True if the project name exists, false if not.
     */
     public function isProjectNameInUse($name) {
     	static $stmt;
        if ($stmt == null) {
            $stmt = $this->dbh->prepare(
                  "SELECT id ".
                    "FROM project ".
                    "WHERE name = :name "
                );
        }

        $success = $stmt->execute(Array(
                ':name' => $name
            ));
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for isProjectNameInUse"));

        $row = $stmt->fetchObject();
        return ($row !== false);
     }//isProjectNameInUse
     
     /**
     * Selects all Projects with active status.
     *
     * @return  Array of Projects.
     */
     public function readActiveProjects() {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT id, name, timeEstimate, status FROM project WHERE status = 'Active'"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readActiveProjects query"));
        }
        
        $success = $stmt->execute();
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readActiveProjects records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new Project(
                    $row->id,
                    $row->name,
                    $row->timeEstimate,
                    $row->status
                );
        } // while
        return $rv;
     }//readActiveProjects
     
	 /**
	 * Updates Project with active status.
	 *
	 * @return  
	 */
	 public function completeProject($name) {
		static $stmt;
		if ($stmt == null) {
			$stmt = $this->dbh->prepare(
				"Update project SET status = 'Inactive' WHERE name = :name"
			);
		
			if(!$stmt)
				throw new Exception($this->formatErrorMessage(null, "Unable to prepare readActiveProjects query"));
		}
	
		$success = $stmt->execute(Array(
				':name' => $name
			));
	
		if ($success === false)
			throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readActiveProjects records"));

		$row = $stmt->fetchObject();
		return ($row !== false);
	}//completeProjects
     
	public function totalHours($projectName) {
     	static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT SUM(time) AS ActualTime FROM projectTimeHistory WHERE projectName = :projectName"
        	);
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare totalHours query"));
        }
        $success = $stmt->execute(Array(
                ':projectName' => $projectName
            ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for totalHours records"));

        $row = $stmt->fetchObject();
        return $row;
	}//totalHours
     
	public function totalProjectEmployeeHours($projectName) {
		static $stmt;
        if ($stmt == null) {
        	$stmt = $this->dbh->prepare(
        		"SELECT employeeID, SUM(time) AS hours FROM projectTimeHistory WHERE projectName = :projectName GROUP BY employeeID"
        	);
        	
        	if(!$stmt)
        		throw new Exception($this->formatErrorMessage(null, "Unable to prepare readProjects query"));
        }
        
        $success = $stmt->execute(Array(
                ':projectName' => $projectName
            ));
        
        if ($success === false)
            throw new Exception($this->formatErrorMessage($stmt, "Unable to query database for readProjects records"));

        $rv = Array();
        while ($row = $stmt->fetchObject()) {
            $rv[] = new CountHours(
                    $row->employeeID,
                    $row->hours
                );
        } // while
        return $rv;
	}
    	//totalProjectEmployeeHours
} // DBInterface