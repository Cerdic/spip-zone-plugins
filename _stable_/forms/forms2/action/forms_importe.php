<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('inc/forms');

function action_forms_importe(){
	global $auteur_session;
	$id_form = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	include_spip("inc/actions");
	if (verifier_action_auteur("forms_importe-$id_form",$hash,$id_auteur)==TRUE) {
		if (($val = $_FILES['fichier_xml']) AND (isset($val['tmp_name']))) {
			$source = $val['tmp_name'];
			// $id_form='form' : import d'un formulaire complet , creation
			// $id_form numerique, import d'un formulaire comme sous partie d'un autre : formette
			if ($id_form=='form' OR ($id_form=intval($id_form))){
				Forms_importe_form($id_form,$source);
			}
			@unlink($source);
		}
	}
	redirige_par_entete($redirect);
}

?>