<?php
//
// spip-lettres
//
// API d'envoi des lettres à des mails issus de requêtes SQL 
// (sous-sélection d'abonnés ou tables externes)
//
// Auteur : JLuc
//

include_spip('inc/lettres_fonctions');
include_spip('classes/lettre');
include_spip('base/abstract_sql');
@define('_LETTRES_MAX_TRY_SEND',5);

//
// Programme l'envoi de la lettre aux destinataires issus d'une requête sql
// En complément de $id_lettre, les paramètres sont ceux de sql_select
// La requête reçue doit produire un 'email', éventuellement un 'code' et d'autres champs
// dont les valeurs seront substituées aux %%CHAMPS%% présents dans le mail
// Si 'code' est fourni, %%URL_VALIDATION_DESABONNEMENTS%% et %%URL_VALIDATION_DESABONNEMENTS_PERSO%% seront substitués
// avec les bons paramètres email et code pour la page de désabonnement par défaut; 
// à savoir 'validation_desabonnements' (ou 'validation_desabonnements_perso' qu'il faut se construire sur mesure)
//
function lettres_sql_programmer_envois ($id_lettre, $select = array(), $from = "spip_abonnes", $where = array(), $groupby = array(), $orderby = array(), $limit = '', $having = array(), $serveur='', $option=true) {

	$lettre = new lettre($id_lettre,false); 
	
	// changer le statut sans envoyer aux abonnés
	$lettre->enregistrer_statut('envoi_en_cours',false);

	$res = sql_select($select, $from, $where, $groupby, $orderby, $limit, $having, $serveur, $option);
	$count=mysql_num_rows($res);
	
	while ($a = sql_fetch($res))
		lettres_programmer_envoi_email ($id_lettre, $a['email'], $a);
	
	// Dans cette API non interactive, on considère que la lettre est envoyée 
	// dés que les envois sont programmés.
	$lettre->enregistrer_statut('envoyee');
	
	return $count ? $count : '';
}

// $champs est un tableau associatif de (raccourcis à substituer, valeur)
function lettres_programmer_envoi_email ($id_lettre, $email, $champs=array()) {
	spip_log ("lettres_programmer_envoi_email ($id_lettre, $email,".print_r($champs,true).")", 'lettre_mail_req');

	$id_job = job_queue_add(
				'lettres_envoyer_une_lettre_email',
				"envoyer lettre $id_lettre à $email",
				array($id_lettre,$email,$champs,1),
				'inc/delivrer_hors_abo',true);

	// serait utile, avec lettres_envois_restants, pour un suivi des envois réels
	// queue_link_job($id_job,array('objet'=>'lettre','id_objet'=>$id_lettre));

	if (!$id_job)
		spip_log ("Echec lettres_programmer_envoi_email ($id_lettre, $email)",
			'lettres_delivrer_fail');
	return $id_job ? $id_job : '';
}

function lettres_envoyer_une_lettre_email ($id_lettre, $email, $champs=array(), $try=1) {
	$lettre = new lettre($id_lettre);

	$objet = $lettre->titre;

	$message_html	= $lettre->message_html;
	$message_texte	= $lettre->message_texte;

	// Si un code est fournit par la requête, alors on peut aussi traiter le désabonnement
	if (isset ($champs['code'])) {
		$parametres = 'lang='.$lettre->lang.'&rubriques[]=-1&code='.$champs['code'].'&email='.$email;
		$url_action_validation_desabonnements = url_absolue(generer_url_action('validation_desabonnements', $parametres, true));
		$message_html	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_html);
		$message_texte	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_texte);
		$url_action_validation_desabonnements_perso = url_absolue(generer_url_action('validation_desabonnements_perso', $parametres, true));
		$message_html	= str_replace("%%URL_VALIDATION_DESABONNEMENTS_PERSO%%", $url_action_validation_desabonnements_perso, $message_html);
		$message_texte	= str_replace("%%URL_VALIDATION_DESABONNEMENTS_PERSO%%", $url_action_validation_desabonnements_perso, $message_texte);
	};
	
	foreach ($champs as $c => $v) {
		$objet = lettres_remplacer_raccourci($c, $v, $objet);
		$message_html = lettres_remplacer_raccourci($c, $v, $message_html);
		$message_texte = lettres_remplacer_raccourci($c, $v, $message_texte);
	};

	// ici on pourrait gérer l'indication d'un éventuel $champ['format']
	$corps = array('html' => $message_html, 'texte' => $message_texte);
	
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	if ($envoyer_mail($email, $objet, $corps)) {
		spip_log("OK Envoi lettre email $id_lettre -> $email",
				'lettres_delivrer_ok');
		$lettre->enregistrer_envoi_hors_abo('envoye');			
		if (lettres_envois_restants($id_lettre)<=1)
			$lettre->enregistrer_statut('envoyee');
	}
	else
	// Echec de l'envoi, programmer une nouvelle tentative
	if (++$try<=_LETTRES_MAX_TRY_SEND
		and job_queue_add(
			'lettres_envoyer_une_lettre_email',
			"envoyer lettre $id_lettre à $email",
			array($id_lettre,$email,$champs,$try),
			'inc/delivrer_hors_abo',true))
		 // si reprogrammé, enregistrer l'echec
		spip_log("RETRY#$try Envoi lettre email $id_lettre -> $email",
				'lettres_delivrer_fail');
	else { // sinon, abandon
		spip_log("FAIL Envoi lettre email $id_lettre à $email",'lettres_delivrer_fail');
		$lettre->enregistrer_envoi_hors_abo('echec');
	};
}

?>