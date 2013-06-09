<?php
include_spip('inc/meta');
function formulaires_cfg_rubrique_a_linscription_charger(){
	$meta = unserialize(lire_meta('rubrique_a_linscription'));
	$id_parent = $meta['id_parent'] ? $meta['id_parent'] : 0 ; 

	return array('id_parent'=>$id_parent,'mail_prive'=>$meta["mail_prive"],'mail_public'=>$meta["mail_public"],'espace_prive_voir'=>$meta["espace_prive_voir"],'espace_prive_creer'=>$meta["espace_prive_creer"],'groupe_mots'=>$meta["groupe_mots"],'statut'=>$meta['statut'],'argument_explicite'=>$meta['argument_explicite']);	
}
function formulaires_cfg_rubrique_a_linscription_verifier(){
	return array();
	
}
function formulaires_cfg_rubrique_a_linscription_traiter(){
	$meta 		= array(
		'id_parent' 		=> _request('id_parent'),
		'mail_public'		=> _request('mail_public'),
		'mail_prive'		=> _request('mail_prive'),
		'espace_prive_voir'	=> _request('espace_prive_voir'),
		'espace_prive_creer'	=> _request('espace_prive_creer'),
		'groupe_mots'		=> _request('groupe_mots'),
		'statut'			=> _request('statut'),
		'argument_explicite'			=> _request('argument_explicite')
		);
	spip_log('Mise à jour des réglages '.var_export($meta,true),'rubrique_a_linscription');
	$meta		= serialize($meta);
	ecrire_meta('rubrique_a_linscription',$meta);
	ecrire_metas();
	
	return array();
	
}

?>