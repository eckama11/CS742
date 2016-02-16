<?php

class CountHours
	extends GetterSetter
    implements JsonSerializable
{
	private $_id;
	private $_hours;
	
	/**
	* Constructs a new deassignObject object
	*
	* @param	int			$id			id of employee
	* @param	int			$hours		name of project
	*/
	public function __construct(
			$id, $hours
		)
	{
		if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;
        $this->hours = $hours;
	}// __construct
	
	public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->hours = $this->hours;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getHours() {
		return $this->_hours;
	}// getProjectName
	
	protected function setHours($hours) {
		if (!is_numeric($hours))
            throw new Exception("The \$hours parameter must be an integer");
        $this->_hours = $hours;
	}// setProjectName
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, hours=$this->hours)";
    } // __toString
}// class deassignObject
?>