<?php
/**
 * Plugin xmlrpc
 * 
 * Auteurs : kent1 (http://www.kent1.info)
 * © 2011 - GNU/GPL v3
 * 
 * Fonction cliente xml-rpc
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction cliente XML-RPC
 *
 *
 * @return unknown_type
 */
function inc_xmlrpc_dist(){
	include_spip(_DIR_IXR.'ixr_library');

	$args = func_get_args();
	$url = array_shift($args);
	$rpc_call = new IXR_Client($url,'','',10);
	//$rpc_call->debug = true;
	xmlrpc_erreur('','',true);

	if (is_array($args[0])) {
		$method = 'system.multicall';
		$multicall_args = array();
		foreach ($args[0] as $call) {
			$multicall_args[] = array('methodName' => array_shift($call), 'params' => $call);
		}	
		$args = array($multicall_args);
	}
	else {
		$method = array_shift($args);
	}
	$args = array_shift($args);

	$rpc_call->useragent = 'SPIP XML-RPC';
	$rpc_call->query($method,$args);

	/**
	 * La connexion au serveur renvoie une erreur
	 */
	if ($rpc_call->isError()) {
		$erreur = xmlrpc_erreur($rpc_call->getErrorCode(),$rpc_call->getErrorMessage());
		return $erreur;
	}

	//Now parse what we've got back
	//if (!xmlrpc_message_parse($message)) {
		// XML error
	//	xmlrpc_error(-32700, t('Parse error. Not well formed'));
	//	return false;
	//}
	/**
	 * Le serveur répond une erreur à notre requête
	 */
	if ($rpc_call->messagetype == 'fault') {
		xmlrpc_erreur($message[0], $message[1]);
		return false;
	}
	return $rpc_call->message->params[0];
}

function xmlrpc_erreur($code=null, $message=null, $reset = false){
	static $xmlrpc_erreur;
	if (isset($code)) {
		$xmlrpc_erreur = new stdClass();
		$xmlrpc_erreur->is_error = true;
		$xmlrpc_erreur->code = $code;
		$xmlrpc_erreur->message = $message;
	}
	elseif ($reset) {
		$xmlrpc_erreur = null;
	}

	return $xmlrpc_erreur;
}
?>