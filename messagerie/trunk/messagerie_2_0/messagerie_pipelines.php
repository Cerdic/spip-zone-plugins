<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Pipeline messagerie_signer_message
 * Ajout d'une signature en bas de mail
 *
 * @param unknown_type $texte
 * @return unknown
 */
function messagerie_messagerie_signer_message($texte){
	$texte .= _T('messagerie:texte_signature_email',array('nom_site'=>$GLOBALS['meta']['nom_site'],'url_site'=>$GLOBALS['meta']['adresse_site']));
	return $texte;
}

/**
 * Pipeline inserthead.
 * Ajout d'une css dans l'espace public
 *
 * @param unknown_type $texte
 * @return unknown
 */
function messagerie_insert_head($texte){
	$texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('habillage/messagerie.css').'" media="all" />'."\n";
	return $texte;
}

/**
 * Pipeline notification
 * fonction appelee a chaque fois qu'un plugin notifie une action
 *
 * @param array $flux
 * @return array
 */
function messagerie_notifications($flux){
	$quoi = $flux['args']['quoi'];
	$id = $flux['args']['id'];
	$options = $flux['args']['options'];

	if ($quoi == 'envoyermessage') {
		$qui = isset($options['id_auteur'])?intval($options['id_auteur']):intval($GLOBALS['visiteur_session']['id_auteur']);
		$destinataires = isset($options['destinataires'])?$options['destinataires']:array();
		$profil_decrire = charger_fonction('profil_decrire','inc');
		$qui = $profil_decrire($qui,true);
		$qui['url_site']=$GLOBALS['meta']['adresse_site'];
		$qui['nom_site']= typo($GLOBALS['meta']['nom_site']);
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		foreach($destinataires as $dest) {
			$to = $profil_decrire($dest,true);
			$sujet = _T("messagerie:mail_sujet_".$quoi,$qui);
			$texte = _T("messagerie:mail_texte_".$quoi,$qui);
			$envoyer_mail($to['email'],$sujet,$texte,"","Reply-To: ".$qui['email']."\n");
		}
	}

	return $flux;
}

?>
