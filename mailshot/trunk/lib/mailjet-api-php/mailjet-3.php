<?php

/**
 * Mailjet Public API
 *
 * @package    API v3
 * @author    Nursit
 * @link    http://api.mailjet.com/
 *
 */

class Mailjet {
	var $version = 3;
	var $apiVersion = 'v3/';

	# Connect thru https protocol
	var $secure = true;

	# Mode debug ? 0 none / 1 errors only / 2 all
	var $debug = 0;

	# Edit with your Mailjet Infos
	var $apiKey = '';
	var $secretKey = '';

	var $_method = '';
	var $_request = '';
	var $_data = '';
	var $_response = '';
	var $_error = '';

	// Constructor function
	public function __construct($apiKey = false, $secretKey = false){
		if ($apiKey){
			$this->apiKey = $apiKey;
		}
		if ($secretKey){
			$this->secretKey = $secretKey;
		}
		$this->apiUrl = (($this->secure) ? 'https' : 'http') . '://'
			. ($this->apiKey ? $this->apiKey . ':' . $this->secretKey . '@' : '')
			. 'api.mailjet.com/' . $this->apiVersion . '';
	}

	/*
	 * Legacy methods
	 */
	public function userSenderadd($params){
		$data = array('email' => $params['email']);
		return $this->sender(array('data' => $data));
	}

	public function __call($method, $args){
		# params
		$params = (sizeof($args)>0) ? $args[0] : array();

		$data = '';
		$request = 'GET';

		# request method
		if (isset($params["method"])){
			$request = $params["method"];
			unset($params["method"]);
		}

		# Make request
		$result = $this->sendRequest($method, $params, $request);

		# Return result
		$return = ($result===true) ? $this->_response : false;

		if ($this->_error){
			$url_log = "api.mailjet.com/" . $this->apiVersion . $this->_method;
			spip_log("$url_log : " . $this->_error, 'mailshot' . _LOG_ERREUR);
		} elseif (isset($this->_response['StatusCode'])) {
			$url_log = "api.mailjet.com/" . $this->apiVersion . $this->_method;
			spip_log("$url_log : status " . $this->_response['StatusCode'] . " - " . $this->_response['ErrorInfo'] . ", " . $this->_response['ErrorMessage'], 'mailshot' . _LOG_INFO_IMPORTANTE);
		}

		/*
		if ($this->debug==2 || ($this->debug==1 && $return==false)){
			$this->debug();
		}*/

		return $return;
	}


	/**
	 * @param bool $method
	 *   api call method
	 * @param array $params
	 *   path : subpath of the method (:id/validate for calling sender/:id/validate)
	 *   data : data to send as a POST
	 *   filters : args to add in query string to filter results
	 * @param string $request
	 *   GET/POST/PUT/...
	 * @return bool
	 */
	public function sendRequest($method = false, $params = array(), $request = "GET"){
		# Method
		$this->_method = $method;
		$this->_request = $request;
		$this->_data = '';
		$this->_response = '';
		$this->_error = '';

		// submethod path ?
		if (isset($params['path'])){
			$method .= "/" . ltrim($params['path'], "/");
			$this->_method = $method;
		}

		$headers = "";
		$data = "";
		// data is the json body of request
		if (isset($params['data'])){
			$data = $params['data'];
			if (!is_string($data)){
				include_spip('inc/json');
				$data = json_encode($data);
			}
			$headers .= "Content-Type: application/json\r\n";
			$this->_data = $headers . "\r\n" . $data;
			if ($request=='GET'){
				$this->_request = $request = 'POST';
			}
		}


		include_spip('inc/distant');
		if ($method!=='send'){
			$method = "REST/$method";
		}

		$url = $this->apiUrl . $method;
		$url_log = "api.mailjet.com/" . $this->apiVersion . $method;

		// filters is the query string part of request
		if (isset($params['filters'])){
			$qs = $params['filters'];
			if (!is_string($qs)){
				$qs = http_build_query($qs);
			}
			$url .= "?$qs";
			$url_log .= "?$qs";
		}


		try {
			if (function_exists('recuperer_url')){
				$options = array(
					'methode' => $this->_request,
				);
				if ($this->_data){
					$options['datas'] = $this->_data;
				}
				$res = recuperer_url($url, $options);
				$this->_response_code = $res['status'];
				$response = $res['page'];
			} elseif (function_exists('curl_init')) {
				$ch = curl_init();
				$headers = explode("\n", $headers);
				$headers = array_map('trim', $headers);
				$headers = array_filter($headers);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				if ($headers){
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				}
				if (in_array($this->_request, array("DELETE", "PUT"))){
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->_request);
				}
				if ($data){
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				}
				$response = curl_exec($ch);
				$this->_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
			} else {
				// en cas de DELETE on appelle directement recuperer_lapage
				// en esperant ne pas avoir de 301
				if (in_array($this->_request, array('DELETE'))){
					$response = recuperer_lapage($url, false, $this->_request, 1048576, $this->_data);
				} else {
					$response = recuperer_page($url, false, false, null, $this->_data);
				}
				if (!$response){
					$this->_error = 'erreur lors de recuperer_page sur API Mailjet ' . $this->apiVersion;
					return false;
				}
				$this->_response_code = 200; // on suppose car sinon renvoie false
			}

		} catch (Exception $e) {
			$this->_error = "sendRequest $url_log : " . $e->getMessage();
			spip_log($this->_error, "mailshot" . _LOG_ERREUR);
			return false;
		}

		spip_log("$request $url_log resultat: " . $response, "mailshot" . _LOG_DEBUG);
		if ($response){
			$this->_response = json_decode($response, true);
			if ($this->_response==null and intval($this->_response_code/100)!==2 and !$this->_error){
				if ($response){
					$this->_error = strip_tags($response);
				} else {
					$this->_error = "Unknown error";
				}
			}
		}
		return (intval($this->_response_code/100)==2) ? true : false;
	}


	public function debug(){
		echo '<style type="text/css">';
		echo '

		#debugger {width: 100%; font-family: arial;}
		#debugger table {padding: 0; margin: 0 0 20px; width: 100%; font-size: 11px; text-align: left;border-collapse: collapse;}
		#debugger th, #debugger td {padding: 2px 4px;}
		#debugger tr.h {background: #999; color: #fff;}
		#debugger tr.Success {background:#90c306; color: #fff;}
		#debugger tr.Error {background:#c30029 ; color: #fff;}
		#debugger tr.Not-modified {background:orange ; color: #fff;}
		#debugger th {width: 20%; vertical-align:top; padding-bottom: 8px;}

		';
		echo '</style>';

		echo '<div id="debugger">';

		if (isset($this->_response_code)) :

			if ($this->_response_code==200) :

				echo '<table>';
				echo '<tr class="Success"><th>Success</th><td></td></tr>';
				echo '<tr><th>Status code</th><td>' . $this->_response_code . '</td></tr>';

				if (isset($this->_response)) :
					echo '<tr><th>Response</th><td><pre>' . utf8_decode(print_r($this->_response, 1)) . '</pre></td></tr>';
				endif;

				echo '</table>';
			elseif ($this->_response_code==304) :

				echo '<table>';
				echo '<tr class="Not-modified"><th>Error</th><td></td></tr>';
				echo '<tr><th>Error no</th><td>' . $this->_response_code . '</td></tr>';
				echo '<tr><th>Message</th><td>Not Modified</td></tr>';
				echo '</table>';
			else :

				echo '<table>';
				echo '<tr class="Error"><th>Error</th><td></td></tr>';
				echo '<tr><th>Error no</th><td>' . $this->_response_code . '</td></tr>';
				if (isset($this->_response)) :
					if (is_array($this->_response) OR is_object($this->_response)):
						echo '<tr><th>Status</th><td><pre>' . print_r($this->_response, true) . '</pre></td></tr>';
					else:
						echo '<tr><th>Status</th><td><pre>' . $this->_response . '</pre></td></tr>';
					endif;
				endif;
				echo '</table>';

			endif;

		endif;

		$call_url = parse_url($this->call_url);

		echo '<table>';
		echo '<tr class="h"><th>API config</th><td></td></tr>';
		echo '<tr><th>Protocole</th><td>' . $call_url['scheme'] . '</td></tr>';
		echo '<tr><th>Host</th><td>' . $call_url['host'] . '</td></tr>';
		echo '<tr><th>Version</th><td>' . $this->version . '</td></tr>';
		echo '</table>';

		echo '<table>';
		echo '<tr class="h"><th>Call infos</th><td></td></tr>';
		echo '<tr><th>Method</th><td>' . $this->_method . '</td></tr>';
		echo '<tr><th>Request type</th><td>' . $this->_request . '</td></tr>';
		echo '<tr><th>Get Arguments</th><td>';

		$args = explode("&", $call_url['query']);
		foreach ($args as $arg){
			$arg = explode("=", $arg);
			echo '' . $arg[0] . ' = <span style="color:#ff6e56;">' . $arg[1] . '</span><br/>';
		}

		echo '</td></tr>';

		if ($this->_request_post){
			echo '<tr><th>Post Arguments</th><td>';

			foreach ($this->_request_post as $k => $v){
				echo $k . ' = <span style="color:#ff6e56;">' . $v . '</span><br/>';
			}

			echo '</td></tr>';
		}

		echo '<tr><th>Call url</th><td>' . $this->call_url . '</td></tr>';
		echo '</table>';

		echo '</div>';
	}
}