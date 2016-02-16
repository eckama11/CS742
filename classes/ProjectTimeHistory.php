<?php

class ProjectTimeHistory
	extends GetterSetter
    implements JsonSerializable
{
	private $_id;
	private $_employeeID;
	private $_projectID;
	private $_projectName;
	private $_time;
	
	/**
	* Constructs a new ProjectTimeHistory object
	*
	* @param	int			$id
	* @param	int			$employeeID
	* @param	int			$projectID
	* @param	string		$projectName
	* @param	int			$time
	*/
	public function __construct(
			$id, $employeeID, $projectID, $projectName, $time
		)
	{
		if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;
        
        $this->employeeID = $employeeID; 
        $this->projectID = $projectID; 
        $this->projectName = $projectName; 
        $this->time = $time;
	}// __construct
	
	public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->employeeID = $this->employeeID;
        $rv->projectID = $this->projectID;
        $rv->projectName = $this->projectName;
        $rv->time = $this->time;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getEmployeeID() {
        return $this->_employeeID;
    }
    
    protected function setEmployeeID($newEmployeeID) {
    	if (!is_numeric($newEmployeeID))
            throw new Exception("The \$newEmployeeID parameter must be an integer");
    	$this->_employeeID = $newEmployeeID;
    }
    
     protected function getProjectID() {
        return $this->_projectID;
    }
    
    protected function setProjectID($newProjectID) {
    	if (!is_numeric($newProjectID))
            throw new Exception("The \$newProjectID parameter must be an integer");
    	$this->_projectID = $newProjectID;
    }
    
     protected function getProjectName() {
        return $this->_projectName;
    }
    
    protected function setProjectName($newProjectName) {
    	$newProjectName = trim($newProjectName);
        if (empty($newProjectName))
            throw new Exception("Project name cannot be empty string");
        $this->_projectName = $newProjectName;
    }
    
     protected function getTime() {
        return $this->_time;
    }
    
    protected function setTime($newTime) {
    	if (!is_numeric($newTime))
            throw new Exception("The \$newTime parameter must be an integer");
    	$this->_time = $newTime;
    }
    
     public function __toString() {
        return __CLASS__ ."(id=$this->id, employeeID=$this->employeeID, projectID=$this->projectID, projectName=$this->projectName, time=$this->time)";
    } // __toString
} // class ProjectTimeHistory