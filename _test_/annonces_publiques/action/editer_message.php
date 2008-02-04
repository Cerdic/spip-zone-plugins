<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include('ecrire/action/editer_message.php');

// http://doc.spip.org/@action_editer_message_dist
function action_editer_message() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	//tout pareil qu'en standard, sauf qu'on a un champ supplementaire, donc une fonction d'update differente
	if (preg_match(',^(\d+)$,', $arg, $r))
		action_editer_message_post_vieux_ap($arg); 
	elseif (preg_match(',^-(\d+)$,', $arg, $r))
		action_editer_message_post_supprimer($r[1]);
	elseif (preg_match(',^(\d+)\W$,', $arg, $r))
		action_editer_message_post_choisir($r[1]);	  
	elseif (preg_match(',^(\d+)\W@(\d+)$,', $arg, $r))
		action_editer_message_post_ajouter($r[1], $r[2]);	  
	elseif (preg_match(',^(\d+)\W:(\d+)$,', $arg, $r))
		action_editer_message_post_vu($r[1], $r[2]);	  
	elseif (preg_match(',^(\d+)\W-(\d+)$,', $arg, $r))
		action_editer_message_post_retirer($r[1], $r[2]);	  
	elseif (preg_match(',^(\d+)\W(\w+)$,', $arg, $r))
		action_editer_message_post_envoyer($r[1], $r[2]);	  
	elseif (preg_match(',^(\w+)$,', $arg, $r))
		action_editer_message_post_nouveau($arg);
	elseif (preg_match(',^(\w+)\W(\d+)$,', $arg, $r))
		action_editer_message_post_nouveau($r[1], $r[2]);
	elseif (preg_match(',^(\w+)\W(\d+-\d+-\d+)$,', $arg, $r))
		action_editer_message_post_nouveau($r[1], '', $r[2]);
	else 	spip_log("action_editer_message_dist $arg pas compris");
}

// idem que la focntion standard mais on update le champ lieu en plus.
function action_editer_message_post_vieux_ap($id_message)
{
		
	spip_query("UPDATE spip_messages SET titre=" . _q(_request('titre')) . ", texte=" . _q(_request('texte')) . ", lieu=" . _q(_request('lieu')) . " WHERE id_message='$id_message'");

	spip_query("UPDATE spip_messages SET rv=" . _q(_request('rv')) . " WHERE id_message='$id_message'");

	if (_request('jour'))
		change_date_message($id_message, _request('heures'),_request('minutes'),_request('mois'), _request('jour'), _request('annee'), _request('heures_fin'),_request('minutes_fin'),_request('mois_fin'), _request('jour_fin'), _request('annee_fin'));
}

?>
