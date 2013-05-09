<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function formulaires_configurer_notation_general_charger_dist(){
	$valeurs['accepter_note'] = $GLOBALS['meta']['notations_publics'];
	return $valeurs;
}

function formulaires_configurer_notation_general_traiter_dist(){
	$accepter_note = _request('accepter_note') ? _request('accepter_note') : 'oui';
	ecrire_meta('notations_publics',$accepter_note);
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>