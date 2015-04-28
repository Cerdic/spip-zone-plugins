<?php
/**
 * Plugin Rôles de documents
 * (c) 2015
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajout de contenu dans le bloc «actions» des documents
 * 
 * - Formulaire pour définir les rôles des documents
 *
 * @pipeline document_desc_actions
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_document_desc_actions($flux) {

	$texte       = "";
	$e           = trouver_objet_exec(_request('exec'));
	include_spip('inc/autoriser');

	if (
		$e !== false // page d'un objet éditorial
		AND $e['edition'] === false // pas en mode édition
		AND $id_document = intval($flux['args']['id_document'])
		AND ($media=sql_getfetsel('media','spip_documents',"id_document=".$id_document)=='image')
		AND $objet = $e['type'] // article
		AND $id_table_objet = $e['id_table_objet'] // id_article
		AND $id_objet = intval(_request($id_table_objet))
		AND autoriser('modifier','document',$id_document)
	) {
		// description des roles
		include_spip('inc/roles');
		$roles = roles_presents('document',$objet);
		// mini-formulaire
		$form = recuperer_fond('prive/squelettes/inclure/editer_roles_objet_lie',
			array(
				'objet_source' => "document",
				'id_objet_source' => $id_document,
				'objet' => $objet,
				'id_objet' => $id_objet
			)
		);
		$texte = $form;
	}

	if ($texte)
			$flux['data'] .= $texte;

	return $flux;
}


?>
