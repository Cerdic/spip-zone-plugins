<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2015 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_images_versions_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		spip_log("action_images_versions_dist $arg pas compris","photospip");
	} else {
		action_images_versions_post($r);
	}
}

function action_images_versions_post($r){
	$action = _request('action_faire') ? _request('action_faire') : $r['action_faire'];
	//on récup l'id_document
	$arg = $r[1];

	$version = _request('version') ? _request('version') : $r['version'];
	$redirect = _request('redirect');
	if(is_numeric($version)){

		include_spip('inc/charsets');	# pour le nom de fichier
		include_spip('inc/documents'); 
		
		if($action == "revenir"){
			
			$row = sql_fetsel("*", "spip_documents_inters", "id_document=".intval($arg)." AND version=".intval($version));
			
			$src = get_spip_doc($row['fichier']);
			spip_log("la source est $src","photospip");
			
			// On cherche le fichier actuel
			$doc_actuel = sql_getfetsel("fichier","spip_documents","id_document=".intval($arg));
			// On supprimer le document actuel puisque l'on revient en arrière
			spip_unlink(get_spip_doc($doc_actuel));
			
			sql_updateq('spip_documents', array('fichier' => $row['fichier'], 'largeur' =>$row['largeur'], 'hauteur' =>$row['hauteur'],'taille' => $row['taille']), "id_document=$arg");
			spip_log("on update la table spip_documents et on met le fichier ".$row['fichier'],"photospip");
			$nextversion = $version - 1;
			$res2 = sql_select("version,fichier","spip_documents_inters","id_document=$arg AND version > $nextversion");
			$total_delete = sql_count($res2);
			spip_log("on recherche les versions supérieures à $nextversion: $total_delete","photospip");
			while($version_delete = sql_fetch($res2)){
				sql_delete("spip_documents_inters","id_document =".intval($arg)." AND version = ".$version_delete['version']);
				spip_log("Pour le doc $arg on delete la version ".$version_delete['version'],"photospip");
				if($version_delete['version'] > $version){
					spip_unlink(get_spip_doc($version_delete['fichier']));
					spip_log("On vire le fichier ".$version_delete['fichier'],"photospip");
				}
				else{
					spip_log("On ne vire pas le fichier ".$version_delete['fichier']." qui reste la source","photospip");
				}
			}
		}
		else if($action == "supprimer"){
			$fichier = sql_getfetsel("fichier", "spip_documents_inters", "id_document=$arg AND version=$version");
			spip_unlink(get_spip_doc($fichier));
			sql_delete("spip_documents_inters","id_document=".intval($arg)." AND version=$version");
			
			$res2 = sql_allfetsel("version","spip_documents_inters","id_document=".intval($arg)." AND version > $version");
			$total_delete = count($res2);
			spip_log("on recherche les versions supérieures à $version: $total_delete","photospip");
			foreach($res2 as $version_delete){
				$newversion = ($version_delete['version']-1);
				sql_updateq("spip_documents_inters",array('version'=> $newversion),"id_document =".intval($arg)." AND version = ".$version_delete['version']);
				spip_log("on descend de version pour l'ancienne version ".$version_delete['version']." qui devient $newversion","photospip");
			}
		}
		else{
			spip_log("script image_versions... pas d'action demandée","photospip");
		}
	}
		
	if($redirect){
		include_spip('inc/headers');
		redirige_par_entete(str_replace("&amp;","&",$redirect));
	}
		
	return true;
}
?>