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
 * @param array $emails_dest
 * @param bool $general
 * @return int
 */
function messagerie_messager($objet, $texte, $auteurs_dest=array(),$emails_dest=array(),$general = false){
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
		'statut' => 'prepa',
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		));
		
		if ($id_message) {
			// diffuser le message en interne
			messagerie_diffuser_message($id_message, $auteurs_dest);
			// diffuser le message en externe
			messagerie_mailer_message($id_message, $emails_dest);

			include_spip('action/editer_objet');
			objet_modifier('message',$id_message,array('statut'=>'publie'));

			$out = $id_message;
		}
	}
	return $out;
}

?>
