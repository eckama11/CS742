<?php

class ProjectDivisionList
	extends GetterSetter
	implements JsonSerializable
{
	private $_id;
	private $_projectID;
	private $_divisionName;
	/**
     * Constructs a new Division object.
     *
     * @param   int     $id    The unique database ID assigned to the Division.
     * @param   string  $name  The name assigned to the Division.
     */
     public function __construct(
     		$id, $projectID, $divisionName
     	) 
     {
     	if (!is_numeric($id) || ($id < 0))
            throw new Exception('The $id parameter must be an integer');
        $id = (int) $id;

        $this->_id = $id;
        $this->_projectID = $projectID;
        $this->_divisionName = $divisionName;
     } // _construct
     
     public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->projectID = $this->projectID;
        $rv->divisionName = $this->divisionName;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getProjectID() {
    	return $this->_projectID;
    }// getProjectID
    
    protected function setProjectID($newProjectID) {
    	if (!is_numeric($newProjectID))
            throw new Exception("The \$newProjectID parameter must be an integer");
    	$this->_projectID = $newProjectID;
    }//setDivisionName
    
    protected function getDivisionName() {
    	return $this->_divisionName;
    }// getDivisionName
    
    protected function setDivisionName($newDivisionName) {
    	$newDivisionName = trim($newDivisionName);
    	if (empty($newDivisionName))
    		throw new Exception("Division Name cannot be empty string");
    	$this->_divisionName = $newDivisionName;
    }//setDivisionName
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, projectID=$this->projectID, divisionName=$this->divisionName)";
    } // __toString
} // class Division