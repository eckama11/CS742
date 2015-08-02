<?php

class Employee
	extends GetterSetter
    implements JsonSerializable
{
    private $_id;
    private $_username;
    private $_password;
    private $_employeeType;
    private $_name;
    private $_status;
    private $_division;

	/**
     * Constructs a new Employee object.
     *
     * @param   int     $id
     * @param   string  $username
     * @param   string  $password       TODO: Password should probably not be required... Not the most secure.
     * @param   string	$employeeType
	 * @param   string  $name
     * @param   string	$status
     * @param   string	$division
     */
     
     public function __construct(
            $id, $username, $password, $employeeType, $name, $status, $division
        )
    {
        if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;

        $this->username = $username;
        $this->password = $password;
        $this->employeeType = $employeeType;
        $this->name = $name;
        $this->status = $status;
        $this->division = $division;
    } // __construct

    public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->name = $this->name;
        $rv->username = $this->username;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId

    protected function getUsername() {
        return $this->_username;
    } // getUsername
    
    protected function setUsername($newUsername) {
        $newUsername = trim($newUsername);
        if (empty($newUsername))
            throw new Exception("Username cannot be empty string");
        $this->_username = $newUsername;
    } // setUsername

    protected function getPassword() {
        return $this->_password;
    } // getPassword
    
    protected function setPassword($newPassword) {
        if (empty($newPassword))
            throw new Exception("Password cannot be empty string");
        $this->_password = $newPassword;
    } // setPassword

	protected function getEmployeeType() {
		return $this->_employeeType;
	}// getEmployeeType
	
	protected function setEmployeeType($employeeType) {
		if (empty($employeeType))
            throw new Exception("Employee Type cannot be empty string");
        $this->_employeeType = $employeeType;
	}// setEmployeeType
	
    protected function getName() {
        return $this->_name;
    } // getName
    
    protected function setName($newName) {
        $newName = trim($newName);
        if (empty($newName))
            throw new Exception("Name cannot be empty string");
        $this->_name = $newName;
    } // setName
    
    protected function getStatus() {
    	return $this->_status;
    }// getStatus
    
    protected function setStatus($newStatus) {
    	$newStatus = trim($newStatus);
        if (empty($newStatus))
            throw new Exception("Status cannot be empty string");
        $this->_status = $newStatus;
    }// setStatus
    
    protected function getDivision() {
    	return $this->_getDivision;
    }// getDivision
    
    protected function setDivision($newDivision) {
    	$newDivision = trim($newDivision);
        //if (empty($newDivision))
            //throw new Exception("Division cannot be empty string");
        $this->_division = $newDivision;
    }// setDivision
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, username=$this->username, password=$this->password, employeeType=$this->employeeType, name=$this->name, status=$this->status, division=$this->division)";
    } // __toString
} // class Employee