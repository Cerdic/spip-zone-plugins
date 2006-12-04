<?php
/**
 * c_HTTP_Request minimal compat Class
 */
class c_HTTP_Request {
	var $_postData = array();
	function c_HTTP_Request($url = '', $params = array())
	{
	}
    function addPostData($name, $value, $preencoded = false)
    {
        if ($preencoded) {
            $this->_postData[$name] = $value;
        } else {
            $this->_postData[$name] = $this->_arrayMapRecursive('urlencode', $value);
        }
    }
    function _arrayMapRecursive($callback, $value)
    {
        if (!is_array($value)) {
            return call_user_func($callback, $value);
        } else {
            $map = array();
            foreach ($value as $k => $v) {
                $map[$k] = $this->_arrayMapRecursive($callback, $v);
            }
            return $map;
        }
    }
    function sendRequest($saveBody = true)
    {
    }
    function getResponseCode()
    {
        return isset($this->_responseCode) ? $this->_responseCode : false;
    }
    function getResponseBody()
    {
        return isset($this->_responseBody) ? $this->_responseBody : false;
    }
}
