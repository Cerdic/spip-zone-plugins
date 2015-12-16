<?php



class AccuseReceptionSMSClass
{
	//input parameters ---------------------
	var $username;                          //your username
	var $password;                          //your password
	var $ticket;                            //your ticket
	var $response;							//wainting response
	//--------------------------------------


	function AccuseReceptionSMS($username, $password, $ticket)
	{
		$this->username = $username;
		$this->password = $password;
		$this->ticket = $ticket;

		// (POST /dr) get recipient, url + dr
		$host = "https://api.smsfactor.com/dr";

		$this->prepareXMLdata();
		
		$this->response = $this->do_post_request($host,$this->request_data);
		return $this->response;
	}

	function prepareXMLdata()
	{
		$xmldata = "<deliveryreport><authentification><username>" . $this->username . "</username><password>" . $this->password . "</password></authentification><message><ticket gsmsmsid=\"\">" . $this->ticket . "</ticket></message></deliveryreport>";
		$this->request_data = 'XML=' . $xmldata;
	}

	function do_post_request($url, $postdata, $optional_headers = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}

?>