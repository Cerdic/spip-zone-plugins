<?php
// inc/c_pear PEAR compat classes just for the errors

/**
 * c_PEAR minimal compat Class
 */
class c_PEAR {
	function isError($data)
	{
        if (is_a($data, 'c_Error')) {
            return true;
        }
        return false;
	}
	function &raiseError($message = null)
	{
		$a = &new c_Error($message);
		return $a;
	}
}
class c_Error {
	var $message;
	function c_Error($message) {
		$this->message = $message;
	}
}
