<?php

class EmployeeProjectList
	extends GetterSetter
    implements JsonSerializable
{
	private $_id;
	private $_employeeID;
	private $_projectID;
	private $_projectName;
	
	/**
     * Constructs a new Employee object.
     *
     * @param   int     $id
     * @param   int  	$employeeID
     * @param   int  	$projectID
     * @param   string	$projectName
     */
     
     public function __construct(
            $id, $employeeID, $projectID, $projectName
        )
    {
        if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;

        $this->employeeID = $employeeID;
        $this->projectID = $projectID;
        $this->projectName = $projectName;
    } // __construct

    public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->employeeID = $this->employeeID;
        $rv->projectID = $this->projectID;
        $rv->projectName = $this->projectName;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId

    protected function getEmployeeID() {
        return $this->_employeeID;
    } // getEmployeeID
    
    protected function setEmployeeID($newEmployeeID) {
        $newEmployeeID = trim($newEmployeeID);
        if (empty($newEmployeeID))
            throw new Exception("EmployeeID cannot be empty string");
        $this->_employeeID = $newEmployeeID;
    } // setEmployeeID

    protected function getProjectID() {
        return $this->_projectID;
    } // getProjectID
    
    protected function setProjectID($newProjectID) {
        if (empty($newProjectID))
            throw new Exception("ProjectID cannot be empty string");
        $this->_projectID = $newProjectID;
    } // setProjectID

	protected function getProjectName() {
		return $this->_projectName;
	}// getProjectName
	
	protected function setProjectName($newProjectName) {
		if (empty($newProjectName))
            throw new Exception("Project Name cannot be empty string");
        $this->_projectName = $newProjectName;
	}// setProjectName
	
    public function __toString() {
        return __CLASS__ ."(id=$this->id, employeeID=$this->employeeID, projectID=$this->projectID, projectName=$this->projectName)";
    } // __toString
} //class EmployeeProjectList