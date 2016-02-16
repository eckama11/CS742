<?php

class Division
	extends GetterSetter
	implements JsonSerializable
{
	private $_id;
	private $_name;
	/**
     * Constructs a new Division object.
     *
     * @param   int     $id    The unique database ID assigned to the Division.
     * @param   string  $name  The name assigned to the Division.
     */
     public function __construct(
     		$id, $name
     	) 
     {
     	if (!is_numeric($id) || ($id < 0))
            throw new Exception('The $id parameter must be an integer');
        $id = (int) $id;

        $name = trim($name);
        if (empty($name))
            throw new Exception('The $name parameter must be a non-empty string');

        $this->_id = $id;
        $this->_name = $name;
     } // _construct
     
     public function jsonSerialize() {
        $rv = new StdClass();
        $rv->id = $this->id;
        $rv->name = $this->name;
        return $rv;
    } // jsonSerialize
    
    protected function getId() {
        return $this->_id;
    } // getId
    
    protected function getName() {
    	return $this->_name;
    }// getName
    
    protected function setName($newName) {
    	$newName = trim($newName);
    	if (empty($newName))
    		throw new Exception("Name cannot be empty string");
    	$this->_name = $newName;
    }//setName
    
    public function __toString() {
        return __CLASS__ ."(id=$this->id, name=$this->name)";
    } // __toString
} // class Division