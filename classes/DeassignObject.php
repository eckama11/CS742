<?php

class deassignObject
	extends GetterSetter
    implements JsonSerializable
{
	private $_id;
	private $_projectName;
	
	/**
	* Constructs a new deassignObject object
	*
	* @param	int			$id				id of employee
	* @param	string		$projectName	name of project
	*/
	public function __construct(
			$id, $projectName
		)
	{
		if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;
        $this->projectName = $projectName;
	}// __construct
	
	public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->projectName = $this->projectName;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getProjectName() {
		return $this->_projectName;
	}// getProjectName
	
	protected function setProjectName($projectName) {
		if (empty($projectName))
            throw new Exception("Project Name cannot be empty string");
        $this->_projectName = $projectName;
	}// setProjectName
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, projectName=$this->projectName)";
    } // __toString
}// class deassignObject
?>