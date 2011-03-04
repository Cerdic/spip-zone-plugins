<?php

/*
 * abonne/désabonne un auteur d'une liste sympatic
 * 
 * name: sympatic_traiter_abonnement
 * @param int $id_liste id de la liste sympatic
 * @param int $id_auteur id de l'auteur à traiter
 * @param string $action abonner ou desabonner
 * @param string $email optionnel, le mail de l'auteur
 * @return boolean
 */
function sympatic_traiter_abonnement($id_liste,$id_auteur,$action,$email=''){
	$liste_data = sql_fetsel("*","spip_sympatic_listes","id_liste = $id_liste");
	if ($email != '')
		$email_auteur = $email;
	else
		$email_auteur = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($id_auteur));
	$sujet=null;
	
	spip_log("traiter abonnement liste : $id_liste | action : $action | id_auteur : $id_auteur","sympatic");
	
	if ($action=='abonner')
		$sujet = 'QUIET ADD ';
	if ($action=='desabonner')
		$sujet = 'QUIET DEL ';

	$sujet .= $liste_data['email_liste'].' ';
	$sujet .= $email_auteur;

	// envoi de mail via facteur et ajout/suppression dans la table sympatic_abonnes
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	if ($envoyer_mail($liste_data['email_robot'], $sujet, 'hop sympatic')){
		if ($action=='abonner'){
			sql_insertq('spip_sympatic_abonnes', array('id_liste' => intval($id_liste), 'id_auteur' => intval($id_auteur)));
		}
		if ($action=='desabonner'){
			sql_delete('spip_sympatic_abonnes','id_liste='.intval($id_liste).' AND id_auteur='.intval($id_auteur));
		}
		return true;
	}
	else
		return false;
}

?>
