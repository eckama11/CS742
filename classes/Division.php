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
     public function _construct($id, $name) {
     	if (!is_numeric($id) || ($id < 0))
            throw new Exception('The $id parameter must be an integer');
        $id = (int) $id;

        $name = trim($name);
        if (empty($name))
            throw new Exception('The $name parameter must be a non-empty string');

        $this->_id = $id;
        $this->_name = $name;
     } // _construct
} // class Division