<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_getid3_appliquer_cover_defaut(){
	if(!autoriser('webmestre'))
		return false;

	if(!strlen($cover_defaut = lire_config('getid3/cover_defaut','')) > 1)
		return false;
		
	$id_document = _request('arg');
	$nb_modifs = 0;
	
	if(is_numeric($id_document)){
		if(sql_getfetsel('id_vignette','spip_documents','id_document='.intval($id_document)) == 0)
			$documents_modifs[] = $id_document;
	}else{
		$sons = array('mp3');
		$documents = sql_select('id_document','spip_documents','id_vignette=0 AND '.sql_in('extension', $sons));
		while($document = sql_fetch($documents)){
			$documents_modifs[] = $document['id_document'];
		}
	}
	
	if(count($documents_modifs) > 0){
		include_spip('inc/documents');
		include_spip('inc/distant');
		$cover_defaut = find_in_path(copie_locale($cover_defaut));
		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');

		list($extension,$arg) = fixer_extension_document($cover_defaut);
		
		foreach($documents_modifs as $document_modif){
			$x = $ajouter_documents($cover_defaut, $cover_defaut,
				$type, $id, 'vignette', $document_modif, $actifs);
			if(is_numeric($x) && ($x > 0))
				$nb_modifs++;
		}
	}
	
	if($redirect = _request('redirect')){
		$redirect = parametre_url(urldecode($redirect),
			'modifs', $nb_modifs, '&');

		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}else
		return $nb_modifs;
}

?>