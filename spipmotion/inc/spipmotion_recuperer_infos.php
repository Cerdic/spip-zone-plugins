<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Récupération des informations techniques du document audio ou video
 * @param int $id_document
 */
function inc_spipmotion_recuperer_infos($id_document){
	spip_log("SPIPMOTION : recuperation des infos du document $id_document","spipmotion");
	if(!intval($id_document) OR ($GLOBALS['meta']['spipmotion_casse'] == 'oui')){
		spip_log('SPIPMOTION est cassé','spipmotion');
		return false;
	}

	include_spip('inc/documents');
	$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
	$chemin = $document['fichier'];
	$fichier = get_spip_doc($chemin);
	
	/**
	 * Si c'est un flv on lui applique les metadatas pour éviter les problèmes
	 * Si c'est un mov ou MP4 on applique qt-faststart
	 */
	if($document['extension'] == 'flv'){
		/**
		 * Inscrire les metadatas dans la video finale
		 * On utilise soit :
		 * -* flvtool++
		 * -* flvtool2
		 */
		if(isset($GLOBALS['spipmotion_metas']['spipmotion_flvtoolplus'])){
			$flvtoolplus = unserialize($GLOBALS['spipmotion_metas']['spipmotion_flvtoolplus']);
		}
		if(isset($GLOBALS['spipmotion_metas']['spipmotion_flvtool2'])){
			$flvtool2 = unserialize($GLOBALS['spipmotion_metas']['spipmotion_flvtool2']);
		}
		if($flvtoolplus['flvtoolplus']){
			$movie_chemin_tmp = $movie_chemin.'_tmp';
			$metadatas_flv = "flvtool++ $movie_chemin $movie_chemin_tmp";
			
		}else if($flvtool2['flvtool2']){
			$metadatas_flv = "flvtool2 -xUP $fichier";
		}
		if($metadatas_flv){
			exec($metadatas_flv,$retour,$retour_int);
			if(file_exists($movie_chemin_tmp)){
				rename($movie_chemin_tmp,$fichier);
			}
		}
	}
	if(in_array($document['extension'],array('mov','mp4','m4v')) && !$GLOBALS['meta']['spipmotion_qt-faststart_casse']){
		exec("qt-faststart $fichier $fichier._temp",$retour,$retour_int);
		if(file_exists($fichier.'._temp')){
			rename($fichier.'._temp',$fichier);
		}
	}
	
	/**
	 * Récupération des métadonnées par mediainfo et le cas échéant par la class ffmpeg-pho
	 */
	if(!$GLOBALS['meta']['spipmotion_mediainfo_casse']){
		$mediainfo = charger_fonction('spipmotion_mediainfo','inc');
		$infos = $mediainfo($fichier,$id_document);
	}
	
	if(strlen($document['titre']) > 0){
		unset($infos['titre']);
	}
	if(strlen($document['descriptif']) > 0){
		unset($infos['descriptif']);
	}
	foreach($infos as $key => $val){
		if(!$val){
			unset($infos[$key]);
		}	
	}
	
	$infos['taille'] = @intval(filesize($fichier));

	// Filesize tout seul est limité à 2Go
	// cf http://php.net/manual/fr/function.filesize.php#refsect1-function.filesize-returnvalues
	if($infos['taille'] == '2147483647'){
		$infos['taille'] = sprintf("%u", filesize($fichier));
	}

	if(count($infos) > 0){
		include_spip('inc/modifier');
		revision_document($id_document, $infos);
	}
	return true;
}
?>