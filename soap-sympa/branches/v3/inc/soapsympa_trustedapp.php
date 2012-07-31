<?php
//(c) 2012 Thomas Weiss
//(c) 2007-2008 Sébastien Santoro aka Dereckson - www.dereckson.be
//Released under BSD license

class SympaTrustedApp {
	public function __construct($wsdl = false, $username = '', $password = ''){
		if ($wsdl!==false){
			$this->wsdl = $wsdl;
			$this->InitializeSOAP();
		}
		$this->username = $username;
		$this->password = $password;
	}

	public function InitializeSOAP(){

		$this->client = null;
		$local_wsdl = sous_repertoire(_DIR_CACHE,"wsdl").md5($this->wsdl);
		include_spip("inc/distant");
		recuperer_page($this->wsdl,$local_wsdl);
		try {
			if ($local_wsdl AND file_exists($local_wsdl))
				$this->client = new SoapClient($local_wsdl,array('exceptions'=>true));
    }
		catch (Exception $e) {
			$this->client = null;
			spip_log($e->getMessage(),"sympa"._LOG_ERREUR);
    }

		if (!$this->client){
			$this->wsdl = false;
		}
	}

	#SOAP calls

	//Adds a user to a list
	//if $quiet, doesn't send welcome file
	public function add($list, $mail, $quiet){
		if (!$this->client) return false;
		return $this->client->authenticateRemoteAppAndRun(
			$this->username, $this->password, "USER_EMAIL=$this->USER_EMAIL",
			'add',
			array($list, $mail, true)
		);
	}

	public function subscribe($list, $name){
		if (!$this->client) return false;
		return $this->client->authenticateRemoteAppAndRun(
			$this->username, $this->password, "USER_EMAIL=$this->USER_EMAIL",
			'subscribe',
			array($list, $name)
		);
	}


	//Deletes a user from a list
	//if $quiet, doesn't send quit notification
	public function del($list, $mail, $quiet){
		if (!$this->client) return false;
		return $this->client->authenticateRemoteAppAndRun(
			$this->username, $this->password, "USER_EMAIL=$this->USER_EMAIL",
			'del',
			array($list, $mail, $quiet)
		);
	}

	public function signoff($list, $email){
		if (!$this->client) return false;
		return $this->client->authenticateRemoteAppAndRun(
			$this->username, $this->password, "USER_EMAIL=$this->USER_EMAIL",
			'signoff',
			array($list, $email)
		);
	}

	public function which($mail = false){
		if (!$this->client) return false;
		if ($mail===false) $mail = $this->USER_EMAIL;
		$SoapAnswer = $this->client->authenticateRemoteAppAndRun($this->username, $this->password, "USER_EMAIL=$mail", 'which', null);
		$i = 0;
		foreach ($SoapAnswer as $listString){
			$listArray = explode(';', $listString);
			foreach ($listArray as $listItem){
				$listInfo = explode('=', $listItem, 2);
				$lists[$i][$listInfo[0]] = $listInfo[1];
			}
			$i++;
		}
		return $lists;
	}

	public function complexlists($mail){
		if (!$this->client) return false;
		try {
			$SoapAnswer = $this->client->authenticateRemoteAppAndRun($this->username, $this->password, "USER_EMAIL=$mail", 'complexLists', null);

			return $SoapAnswer;
		} catch (SoapFault $ex) {
			return false;
		}
	}


	public function review($list, $mail = false){
		if (!$this->client) return false;
		if ($mail===false) $mail = $this->USER_EMAIL;

		$SoapAnswer = $this->client->authenticateRemoteAppAndRun($this->username, $this->password, "USER_EMAIL=$mail", 'review', array($list));

		return $SoapAnswer;
	}


	public function info($list){
		if (!$this->client) return false;
		try {
			$SoapAnswer = $this->client->authenticateRemoteAppAndRun($this->username, $this->password, "USER_EMAIL=$this->USER_EMAIL", 'info', array($list));
			return $SoapAnswer;
		} catch (SoapFault $ex) {
			return false;
		}
	}

	public function ami($list, $function, $mail){
		if (!$this->client) return false;
		return $this->client->authenticateRemoteAppAndRun($this->username, $this->password, "USER_EMAIL=$mail", 'amI', array($list, $function, $mail));
	}


	//SOAP
	private $client;
	public $wsdl;

	//AuthenticateAndRun
	public $username;
	public $password;

	//Proxy variables
	public $USER_EMAIL;
	public $remote_host;
	public $SYMPA_ROBOT;

}

?>