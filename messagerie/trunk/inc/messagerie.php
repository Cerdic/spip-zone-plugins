<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */


include_spip('inc/filtres');
include_spip('inc/messages');
include_spip('base/abstract_sql');

if (!defined('_EMAIL_GENERAL'))
	define('_EMAIL_GENERAL','general'); // permet aux admin d'envoyer un email a tout le monde

/**
 * Fonction generique de verification de la saisie
 * lors de l'envoi d'un message ou de recommander
 *
 * @param array $obligatoires
 * @return array
 */
function messagerie_verifier($obligatoires = array()){
	$erreurs = array();
	foreach($obligatoires as $obli)
		if (!_request($obli))
			$erreurs[$obli] = (isset($erreurs[$obli])?$erreurs[$obli]:'') . _T('formulaires:info_obligatoire_rappel');

	$dests = _request('destinataires');
	
	if (!count($dests)
	  AND !count(	$dests = pipeline('messagerie_destiner',$dest)))
			$erreurs[$obli='destinataire'] = (isset($erreurs[$obli])?$erreurs[$obli]:'') . _T('formulaires:info_obligatoire_rappel');
	
	return $erreurs;
}


/**
 * Envoyer un message par la messagerie interne
 *
 * @param string $objet
 * @param string $texte
 * @param array $auteurs_dest
 * @param bool $general
 * @return int
 */
function messagerie_messager($objet, $texte, $auteurs_dest=array(),$general = false){
	$out = false;
	if (count($auteurs_dest) OR $general){
		// envoyons le message
		$id_message = sql_insertq('spip_messages',array(
		'titre' => safehtml($objet),
		'texte' => safehtml($texte),
		'type' => $general?'genera':'normal',
		'date_heure' => 'NOW()',
		'date_fin' => 'NOW()',
		'rv' => 'non',
		'statut' => 'publie',
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		));
		
		if ($id_message) {
			$insert = array();
			if (!$general) {
				foreach($auteurs_dest as $id)
					$insert[] = array('objet'=>'message', 'id_objet'=>$id_message,'id_auteur'=>$id,'vu'=>'non');
			}
			else {
				$res = sql_select('id_auteur','spip_auteurs');
				while ($row = sql_fetch($res))
					$insert[] = array('objet'=>'message', 'id_objet'=>$id_message,'id_auteur'=>$row['id_auteur'],'vu'=>'non');
			}
			sql_insertq_multi('spip_auteurs_liens',$insert);

			$out = $id_message;			
		}
	}
	return $out;
}

/**
 * Envoyer un message par mail
 *
 * @param string $objet
 * @param string $texte
 * @param array $emails_dest
 * @return bool
 */
function messagerie_mailer($objet, $texte, $emails_dest=array()){
	if (count($emails_dest)) {
		$from = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		foreach($emails_dest as $email)
			$envoyer_mail($email,$objet,$texte,$from);
		return true;
	}
	return false;
}

/**
 * Effacer un message
 *
 * @param int $id_auteur
 * @param array $liste
 */
function messagerie_effacer($id_auteur,$liste){
	$liste = array_map('intval',$liste);
	sql_updateq('spip_auteurs_liens',array('vu'=>'poub'),array('id_auteur='.intval($id_auteur), 'objet="message"', 'id_objet IN ('.implode(',',$liste).')'));
	include_spip('inc/invalideur');
	suivre_invalideur("message/".implode(',',$liste));
}

?>
