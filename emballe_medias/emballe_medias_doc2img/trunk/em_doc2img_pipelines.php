<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline em_post_upload_medias
 * Dans le cas d'un import de pdf, on essaie de récupérer le texte en descriptif
 * 
 * @param array $flux Le contexte
 * @return array $flux le contexte complété
 */
function em_doc2img_em_post_upload_medias($flux){
	if(defined('_DIR_PLUGIN_FULLTEXT')){
		$row = sql_fetsel('*','spip_documents','id_document='.intval($flux['args']['id_document']));
		if (include_spip('extract/'.$row['extension'])
			&& ($lire = charger_fonction($row['extension'],'extracteur'))) {
			include_spip('inc/distant');
			include_spip('inc/documents');
			if (!$fichier = copie_locale(get_spip_doc($row['fichier']))) {
				spip_log('Pas de copie locale de '.$row['fichier'], 'fulltext');
				return;
			}
			// par defaut, on pense que l'extracteur va retourner ce charset
			$charset = 'iso-8859-1';
			// lire le contenu
			$contenu = $lire(_DIR_RACINE.$fichier, $charset);
			if (!$contenu) {
				spip_log('Echec de l\'extraction de '.$fichier, 'extract');
				sql_updateq("spip_documents", array('contenu' => '', 'extrait' => 'err'), "id_document=".intval($row['id_document']));
			} else {
				// importer le charset
				include_spip('inc/charsets');
				//$contenu = importer_charset($contenu, $charset);
				sql_updateq("spip_documents", array('contenu' => $contenu, 'extrait' => 'oui'), "id_document=".intval($row['id_document']));
			}
		}
		if(!function_exists('lire_config'))
			include_spip('inc/config');
		if((lire_config('emballe_medias/fichiers/remplir_texte_article', 'on') != 'off') && $contenu && ($flux['args']['objet'] == 'article') && intval($flux['args']['id_objet'])){
			$texte = sql_getfetsel('texte','spip_articles','id_article='.intval($flux['args']['id_objet']));
			if(strlen(trim($texte)) == 0){
				spip_log('on va ajouter le contenu du coup','fulltext');
				include_spip('action/editer_article');
				article_modifier($flux['args']['id_objet'], array('texte'=>$contenu));
			}
		}
	}
	return $flux;
}
?>
