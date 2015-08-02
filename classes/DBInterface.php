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
//throw new Exception($authenticatedEmployee);
        $rv = new LoginSession( $sessionId, $this->readEmployee($authenticatedEmployee) );
//throw new Exception("Here2");
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
} // DBInterface

