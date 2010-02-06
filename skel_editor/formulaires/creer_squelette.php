<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

include_spip('inc/skeleditor');

function formulaires_creer_squelette_charger_dist($path_base){
	$valeurs = array('filename'=>'');
	$valeurs['editable'] = autoriser('creerdans','squelette',$path_base);

	return $valeurs;
}

function formulaires_creer_squelette_verifier_dist($path_base){
	$erreurs = array();

	$filename = _request('filename');
	if ($e=skeleditor_verifie_nouveau_nom($path_base,$filename))
		$erreurs['filename'] = $e;

	return $erreurs;
}

function formulaires_creer_squelette_traiter_dist($path_base){
	$res = array();

	$filename = _request('filename');
	if (ecrire_fichier($path_base.$filename, ""))
		$res = array('message_ok'=>_T('ok'),'redirect'=>parametre_url(self(),'f',$path_base.$filename));
	else
		$res['message_erreur'] = _T('skeleditor:erreur_ecriture_fichier');

	return $res;
}

?>