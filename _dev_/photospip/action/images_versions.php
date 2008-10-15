<?php

/*
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteurs :
 * Quentin Drouet
 *
 * © 2008 - Distribue sous licence GNU/GPL
 *
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_images_versions_dist() {
	include_spip('inc/distant'); # pour copie_locale
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		spip_log("action_images_versions_dist $arg pas compris","photospip");
	} else {
		action_images_versions_post($r);
	}
}

function action_images_versions_post($r){
	global $visiteur_session;
	$id_auteur = $visiteur_session['id_auteur'];
	$action = _request('action_faire');
	spip_log("action_faire: $action","photospip");
	spip_log($r,'photospip');
	//on récup l'id_document
	$arg = $r[1];
	spip_log("on travail sur l'id_document $arg","photospip");

	$version = _request('version');

	include_spip('inc/charsets');	# pour le nom de fichier
	include_spip('inc/documents'); 
			
	if (_SPIP_AJAX === 1 ){
		$redirect = _request('redirect_ajax');
	}
	else{
		$redirect = _request('redirect');
	}
	if($action == "revenir"){
		spip_log("script image_version... on repart vers l'arrière","photospip");
		spip_log("revenir à la version $version","photospip");
		
		$row = sql_fetsel("*", "spip_documents_inters", "id_document=$arg AND version=$version");
		
		$src = _DIR_RACINE . copie_locale(_NOM_PERMANENTS_ACCESSIBLES.$row['fichier']);
		spip_log("la source est $src","photospip");
		if (preg_match(',^(.*)-photospip(\d+).([^.]+)$,', $src, $match)) {
			//On est dans une image déjà modifiee
			$version_voulue = $match[2];
			$src = $match[1].'.'.$match[3];
		}
		
		sql_updateq('spip_documents', array('fichier' => $row['fichier'], 'largeur' =>$row['largeur'], 'hauteur' =>$row['hauteur']), "id_document=$arg");
		spip_log("on update la table spip_documents et on mets le fichier ".$row['fichier'],"photospip");
		$nextversion = $version - 1;
		$res2 = sql_select("*","spip_documents_inters","id_document=$arg AND version > $nextversion");
		$total_delete = sql_count($res2);
		spip_log("on recherche les versions supérieures à $nextversion: $total_delete","photospip");
		while($version_delete = sql_fetch($res2)){
				sql_delete("spip_documents_inters","id_document =$arg AND version = ".$version_delete['version']);
				spip_log("Pour le doc $arg on delete la version ".$version_delete['version'],"photospip");
				if(($version_delete['version'] > 1) && ($total_delete > 1)){
					unlink(_NOM_PERMANENTS_ACCESSIBLES.$version_delete['fichier']);
					spip_log("On vire le fichier ".$version_delete['fichier'],"photospip");
				}
				else{
					spip_log("On ne vire pas le fichier ".$version_delete['fichier']." qui reste la source","photospip");
				}
		}
	}
	else if($action == "supprimer"){
		spip_log("script image_version... on supprimer une version","photospip");
		$row = sql_fetsel("*", "spip_documents_inters", "id_document=$arg AND version=$version");
		spip_log("On vire le fichier ".$row['fichier'],"photospip");
		unlink(_NOM_PERMANENTS_ACCESSIBLES.$row['fichier']);
		sql_delete("spip_documents_inters","id_document=$arg AND version=$version");
		spip_log("supprimer la version $version","photospip");
		
		$res2 = sql_select("*","spip_documents_inters","id_document=$arg AND version > $version");
		$total_delete = sql_count($res2);
		spip_log("on recherche les versions supérieures à $version: $total_delete","photospip");
		while($version_delete = sql_fetch($res2)){
			$newversion = ($version_delete['version']-1);
			sql_updateq("spip_documents_inters",array('version'=> $newversion),"id_document =$arg AND version = ".$version_delete['version']);
			spip_log("on descend de version pour l'ancienne version ".$version_delete['version']." qui devient $newversion","photospip");
		}
	}
	else{
		spip_log("script image_version... pas d'action demandée","photospip");
	}
	
	redirige_par_entete(str_replace("&amp;","&",$redirect));
}
?>