<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des informations techniques du document audio ou video
 * 
 * Si on a un id_document (en premier argument) on enregistre en base dans cette fonction
 * Si on a seulement un chemin de fichier (en second argument), on retourne un tableau des metas
 * 
 * @param int/false $id_document id_document sur lequel récupérer les informations
 * @param string/null $fichier chemin du fichier à analyser
 * @return array $infos Un tableau des informations récupérées
 */
function inc_spipmotion_recuperer_infos($id_document=false,$fichier=null){
	if((!intval($id_document) && !isset($fichier)) OR ($GLOBALS['meta']['spipmotion_casse'] == 'oui')){
		spip_log('SPIPMOTION est cassé','spipmotion');
		return false;
	}

	if(!isset($fichier)){
		spip_log("SPIPMOTION : recuperation des infos du document $id_document","spipmotion");
		include_spip('inc/documents');
		$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
		$chemin = $document['fichier'];
		$fichier = get_spip_doc($chemin);
		$extension = $document['extension'];
	}else{
		$extension = strtolower(array_pop(explode('.',basename($fichier))));
	}

	/**
	 * Si c'est un flv on lui applique les metadatas pour éviter les problèmes
	 * Si c'est un mov ou MP4 on applique qt-faststart
	 */
	if($extension == 'flv'){
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
			$fichier_tmp = $fichier.'_tmp';
			$metadatas_flv = "flvtool++ $fichier $fichier_tmp";
			
		}else if($flvtool2['flvtool2']){
			$metadatas_flv = "flvtool2 -xUP $fichier";
		}
		if($metadatas_flv){
			exec($metadatas_flv,$retour,$retour_int);
		}
	}
	if(in_array($extension,array('mov','mp4','m4v')) && !$GLOBALS['meta']['spipmotion_qt-faststart_casse']){
		exec("qt-faststart $fichier $fichier._temp",$retour,$retour_int);
	}
	
	if(file_exists($fichier.'._tmp')){
		rename($fichier.'._tmp',$fichier);
	}
	
	/**
	 * Récupération des métadonnées par mediainfo et le cas échéant par la class ffmpeg-pho
	 */
	if(!$GLOBALS['meta']['spipmotion_mediainfo_casse']){
		$mediainfo = charger_fonction('spipmotion_mediainfo','inc');
		$infos = $mediainfo($fichier);
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

	if(intval($id_document) && (count($infos) > 0)){
		include_spip('action/editer_document');
		document_modifier($id_document, $infos);
		return true;
	}
	return $infos;
}
?>