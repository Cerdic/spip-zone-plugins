<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_session_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// La cible de notre operation de connexion
	$redirect = _request('redirect');
	$redirect = isset($redirect) ? $redirect : _DIR_RESTREINT_ABS;

	list($session, $action, $var, $val) = split('-', $arg);

  session_name($session);
	session_start();
	switch($action) {
	  case 'affecter':
			$_SESSION[$var] = $val;
			break;
	  case 'vider':
			unset($_SESSION[$var]);
			break;
		default:
		  break;
 	}

	// Redirection finale
	redirige_par_entete($redirect, true);
}

?>
