<?php
include_spip('inc/meta');
function formulaires_cfg_rubrique_a_linscription_charger(){
	$meta = unserialize(lire_meta('rubrique_a_linscription'));
	$id_parent = $meta['id_parent'] ? $meta['id_parent'] : 0 ; 

	return array('id_parent'=>$id_parent,'mail_prive'=>$meta["mail_prive"],'mail_public'=>$meta["mail_public"]);	
}
function formulaires_cfg_rubrique_a_linscription_verifier(){
	return array();
	
}
function formulaires_cfg_rubrique_a_linscription_traiter(){
	$meta 		= array(
		'id_parent' => _request('id_parent'),
		'mail_public'=>_request('mail_public'),
		'mail_prive'=> _request('mail_prive')
		);
	$meta		= serialize($meta);
	ecrire_meta('rubrique_a_linscription',$meta);
	ecrire_metas();
	
	return array();
	
}

?>