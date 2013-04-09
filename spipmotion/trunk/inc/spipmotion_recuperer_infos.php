<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 * 
 * Récupération de metadonnés d'un fichier audio ou vidéo
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des informations techniques du document audio ou video
 * 
 * Si on a un id_document (en premier argument) on enregistre en base dans cette fonction
 * Si on a seulement un chemin de fichier (en second argument), on retourne un tableau des metas
 * 
 * @param int/false $id_document 
 *   id_document sur lequel récupérer les informations
 * @param string/null $fichier 
 *   chemin du fichier à analyser si pas de id_document
 * @param bool $logo
 *   si true, récupère une vignette du document
 * @param bool $only_return
 * 	si true, on n'insère rien en base depuis cette fonction (devra être fait après)
 * @return array $infos 
 * 	 Un tableau des informations récupérées
 */
function inc_spipmotion_recuperer_infos($id_document=false,$fichier=null,$logo=false,$only_return=false){
	if((!intval($id_document) && !isset($fichier)) OR ($GLOBALS['meta']['spipmotion_casse'] == 'oui')){
		spip_log('SPIPMOTION est cassé','spipmotion');
		return false;
	}
	
	$fichier_tmp = false;
	
	if(!isset($fichier)){
		spip_log("SPIPMOTION : recuperation des infos du document $id_document","spipmotion");
		include_spip('inc/documents');
		$document = sql_fetsel("*", "spip_documents","id_document=".intval($id_document));
		$fichier = get_spip_doc($document['fichier']);
		$extension = $document['extension'];
	}else{
		spip_log("SPIPMOTION : recuperation des infos du document $fichier","spipmotion");
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
		else if(isset($GLOBALS['spipmotion_metas']['spipmotion_flvtool2'])){
			$flvtool2 = unserialize($GLOBALS['spipmotion_metas']['spipmotion_flvtool2']);
		}
		if($flvtoolplus['flvtoolplus']){
			$fichier_tmp = $fichier.'_tmp';
			$soft = 'flvtoolplus';
			$metadatas_flv = "flvtool++ $fichier $fichier_tmp";
		}else if($flvtool2['flvtool2']){
			$soft = 'flvtool2';
			$metadatas_flv = "flvtool2 -xUP $fichier";
		}
		if($metadatas_flv){
			exec(escapeshellcmd($metadatas_flv),$retour,$retour_int);
		}
	}
	if(in_array($extension,array('mov','mp4','m4v')) && !$GLOBALS['meta']['spipmotion_qt-faststart_casse']){
		$fichier_tmp = $fichier.'_tmp';
		exec(escapeshellcmd("qt-faststart $fichier $fichier_tmp"),$retour,$retour_int);
	}
	
	if($fichier_tmp && file_exists($fichier_tmp))
		rename($fichier_tmp,$fichier);
	
	/**
	 * Récupération des métadonnées par mediainfo
	 * cf inc/spipmotion_mediainfo.php
	 */
	if(!$GLOBALS['meta']['spipmotion_mediainfo_casse']){
		$mediainfo = charger_fonction('spipmotion_mediainfo','inc');
		$infos = $mediainfo($fichier);
	}
	/**
	 * Récupération des métadonnées de ffprobe
	 * cf inc/spipmotion_ffprobe.php
	 */
	$infos_ffprobe = array();
	if(!$GLOBALS['meta']['spipmotion_ffprobe_casse']){
		$ffprobe = charger_fonction('spipmotion_ffprobe','inc');
		$infos_ffprobe = $ffprobe($fichier);
		foreach($infos_ffprobe as $info => $valeur){
			if(!isset($infos[$info]) OR !$infos[$info])
				$infos[$info] = $valeur;
		}
	}
	
	if(strlen($document['titre']) > 0)
		unset($infos['titre']);

	if(strlen($document['descriptif']) > 0)
		unset($infos['descriptif']);

	foreach($infos as $key => $val){
		if(!$val)
			unset($infos[$key]);
	}
	
	/**
	 * Si les champs sont vides, on ne les enregistre pas
	 * Par contre s'ils sont présents dans le $_POST ou $_GET,
	 * on les utilise (fin de conversion où on récupère le titre et autres infos du document original)
	 */
	foreach(array('titre','descriptif','credit') as $champ){
		if(!isset($infos[$champ]))
			$infos[$champ] = '';
		if(is_null($infos[$champ]) OR ($infos[$champ]=='')){
			if(_request($champ))
				$infos[$champ] = _request($champ);
			else
				unset($infos[$champ]);	
		}
	}
	
	/**
	 * La récupération de duree est importante
	 * pour les vignettes
	 * Si le logiciel de récupération de métadonnées ne sait pas la récupérer, 
	 * on utilise celle du document original
	 */
	if(!$infos['duree'] && ($document['mode'] == 'conversion')){
		$doc_orig = sql_getfetsel('lien.id_objet',
							'spip_documents as document LEFT JOIN spip_documents_liens as lien ON document.id_document=lien.id_document',
							'lien.objet="document" AND lien.id_document='.intval($id_document));
		$duree = sql_getfetsel('duree','spip_documents','id_document='.intval($doc_orig));
		if($duree > 0)
			$infos['duree'] = $duree;
	}
	
	/**
	 * Filesize tout seul est limité à 2Go
	 * cf http://php.net/manual/fr/function.filesize.php#refsect1-function.filesize-returnvalues
	 */
	$infos['taille'] = @intval(filesize($fichier));
	if($infos['taille'] == '2147483647')
		$infos['taille'] = sprintf("%u", filesize($fichier));
	
	if($logo){
		$recuperer_logo = charger_fonction("spipmotion_recuperer_logo","inc");
		$id_vignette = $recuperer_logo($id_document,1,$fichier,$infos,true);
		if(intval($id_vignette))
			$infos['id_vignette'] = $id_vignette;
	}

	/**
	 * Si on a gis et que les fonctions de récupération de metadonnés nous ont renvoyé :
	 * -* lat = la latitude;
	 * -* lon = la longitude;
	 * 
	 * Deux cas :
	 * -* Si on a un id_document numérique 
	 * -** On recherche si on a déjà un point lié au document et on le modifie
	 * -** Sinon on crée de nouvelles coordonnées
	 * -* Si on n'a pas d'id_document (cas des metadonnées récupérées par les fonctions metadatas/....php)
	 * -** On crée un point avec les coordonnées et on envoit dans le $_POST id_gis_meta 
	 * pour que le point soit lié dans le pipeline post_edition
	 */
	if(defined('_DIR_PLUGIN_GIS') && is_numeric($infos['lat']) && is_numeric($infos['lon'])){
		include_spip('inc/config');
		$zoom = lire_config('gis/zoom',4);
		$config = @unserialize($GLOBALS['meta']['gis']);
		$c = array(
			'titre' => $infos['titre'] ? $infos['titre'] : basename($fichier),
			'lat'=> $infos['lat'],
			'lon' => $infos['lon'],
			'zoom' => $zoom
		);

		if (defined('_DIR_PLUGIN_GISGEOM')) {
			$geojson = '{"type":"Point","coordinates":['.$infos['lon'].','.$infos['lat'].']}';
			set_request('geojson',$geojson);
		}
		
		include_spip('action/editer_gis');
		
		if(intval($id_document)){
			if($id_gis = sql_getfetsel("G.id_gis","spip_gis AS G LEFT  JOIN spip_gis_liens AS T ON T.id_gis=G.id_gis ","T.id_objet=" . intval($id_document) . " AND T.objet='document'")){
				/**
				 * Des coordonnées sont déjà définies pour ce document => on les update
				 */ 
				revisions_gis($id_gis,$c);
			}else{
				/**
				 * Aucune coordonnée n'est définie pour ce document  => on les crée
				 */ 
				$id_gis = insert_gis();
				revisions_gis($id_gis,$c);
				lier_gis($id_gis, 'document', $id_document);
			}
		}else{
			/**
			 * Aucune coordonnée n'est définie pour ce document  => on les crée
			 * On ajoute dans le $_POST id_gis_meta qui sera utilisable dans post_edition
			 */ 
			$id_gis = insert_gis();
			revisions_gis($id_gis,$c);
			set_request('id_gis_meta',$id_gis);
		}
	}
	/**
	 * Si on a $only_return à true, on souhaite juste retourner les metas, sinon on les enregistre en base
	 * Utile pour metadatas/video par exemple
	 */
	if(!$only_return && (intval($id_document) && (count($infos) > 0))){
		foreach($infos as $champ=>$val){
			if($document[$champ] == $val)
				unset($infos[$champ]);
		}
		if(count($infos) > 0){
			include_spip('action/editer_document');
			document_modifier($id_document, $infos);
		}
		
		return true;
	}
	return $infos;
}
?>