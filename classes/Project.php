<?php

class Project
	extends GetterSetter
    implements JsonSerializable
{
    private $_id;
    private $_name;
    private $_timeEstimate;
    private $_status;
    
    /**
     * Constructs a new Project object.
     *
     * @param   int     $id
	 * @param   string  $name
     * @param   int		$timeEstimate
     * @param   string	$status
	*/

	public function __construct(
            $id, $name, $timeEstimate,$status
        )
    {
        if (!is_numeric($id))
            throw new Exception("The \$id parameter must be an integer");
        $this->_id = (int) $id;
        $this->name = $name;
        $this->timeEstimate = (int) $timeEstimate;
        $this->status = $status;
    } // __construct
    
    public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->name = $this->name;
        $rv->timeEstimate = $this->timeEstimate;
        $rv->status = $this->status;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getName() {
        return $this->_name;
    } // getName
    
    protected function setName($newName) {
        $newName = trim($newName);
        if (empty($newName))
            throw new Exception("Name cannot be empty string");
        $this->_name = $newName;
    } // setName
    
    protected function getTimeEstimate() {
        return $this->_timeEstimate;
    } // getName
    
    protected function setTimeEstimate($newTimeEstimate) {
        if (!is_numeric($newTimeEstimate))
            throw new Exception("The \$newTimeEstimate parameter must be an integer");
        $this->_timeEstimate = $newTimeEstimate;
    } // setTimeEstimate
    
    protected function getStatus() {
        return $this->_status;
    } // getStatus
    
    protected function setStatus($newStatus) {
        $newStatus = trim($newStatus);
        if (empty($newStatus))
            throw new Exception("Status cannot be empty string");
        $this->_status = $newStatus;
    } // setStatus
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, name=$this->name, timeEstimate=$this->timeEstimate, status=$this->status)";
    } // __toString
} // class Project