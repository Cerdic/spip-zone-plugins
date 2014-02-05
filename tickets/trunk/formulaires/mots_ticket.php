<?php

/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 * 
 * Formulaire d'ajout de mots à un ticket
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_mots_ticket_charger($id_ticket='', $retour='', $config_fonc='ticket_mots_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);

	$mots = array();
	foreach (sql_allfetsel('id_mot', 'spip_mots_liens', "objet='ticket' AND id_objet=".$id_ticket) as $res) {
		if ($res['id_mot']) {
			$mots[] = $res['id_mot'];
		}
	}
	$valeurs['mots'] = $mots;

	return $valeurs;
}

/**
 * 
 * Fonction de vérification des valeurs
 * 
 * @return 
 * @param int $id_ticket[optional]
 * @param string $retour[optional] URL de retour
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_mots_ticket_verifier($id_ticket='', $retour='', $config_fonc='ticket_mots_config', $row=array(), $hidden=''){
	$erreurs = array();

	return $erreurs;
}

function ticket_mots_config(){
	return array();
}

/**
 * 
 * Fonction de traitement du formulaire
 * 
 * @return 
 * @param int $id_ticket[optional]
 * @param string $retour[optional]
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_mots_ticket_traiter($id_ticket='',$retour='', $config_fonc='ticket_mots_config', $row=array(), $hidden=''){
	$message = "";
	$mots = _request('mots');

	$result = sql_select('0+mot.titre AS num, mot.id_mot','spip_mots as mot LEFT JOIN spip_mots_liens as liens ON mot.id_mot=liens.id_mot','liens.objet="ticket" AND liens.id_objet='.intval($id_ticket),'','num, mot.titre');
	while ($row = sql_fetch($result)) {
		$mots_multiples[] = $row['id_mot'];
	}
	foreach($mots as $cle => $mot){
		/**
		 * Si le mot est déja dans les mots, on le supprime juste
		 * de l'array des mots originaux
		 */
		if(in_array($mot, $mots_multiples)){
			$mots_multiples = array_diff($mots_multiples,array($mot));
		}
		else{
			sql_insertq('spip_mots_liens', array('id_mot' =>$mot, 'id_objet' => $id_ticket, 'objet' => 'ticket'));
		}
	}

	/**
	 * S'il reste quelque chose dans les mots d'origine, on les délie de l'objet
	 */
	if(count($mots_multiples)>0){
		sql_delete('spip_mots_liens','objet="ticket" AND id_objet='.intval($id_ticket).' AND id_mot IN ('.implode(',',$mots_multiples).')');
	}

	$message['message_ok'] = _T('tickets:mots_modifies');
	if($retour)
		$message['redirect'] = $retour;
	
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('motsticket', $id_ticket,
			array('mots' => $mots)
		);
	}
	
	return $message;
}
?>
