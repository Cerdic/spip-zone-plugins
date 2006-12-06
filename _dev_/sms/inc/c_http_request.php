<?php
/**
 * c_HTTP_Request minimal compat Class
 */
class c_HTTP_Request {
	var $_url = '';
// seules certaines options sont supportees par rapport à l'original
	var $_method = 'GET';
	var $_timeout = null;
	var $_allowRedirects = false;
	var $_postData = array();
    var $_responseCode = null;
    var $_responseBody = null;

	function c_HTTP_Request($url = '', $params = array())
	{
		$this->_url = $url;
        foreach ($params as $key => $value) {
            $this->{'_' . $key} = $value;
        }
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
    	$url = $this->_url;
    	if ($this->_method == 'GET') {
    		foreach ($this->_postData as $c => $v) {
    			$url = parametre_url($url, $c, $v);
    		}
    		$this->_postData = '';
    	}
    	include_spip('inc/distant');
    	$page = recuperer_page($url, false, false, 1048576, $this->_postData);
    	if ($page === false) {
    		$this->_responseCode = 0;
    		return;
    	}
		$this->_responseCode = 200;
		if ($saveBody) {
			$this->_responseBody = $page;
		}
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
