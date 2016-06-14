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
 * - Formulaire pour définir les rôles des documents de type image
 *
 * @pipeline document_desc_actions
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_document_desc_actions($flux) {
	include_spip('inc/autoriser');
	
	$texte               = "";
	$exec                = trouver_objet_exec(_request('exec'));
	$objet_exec          = $exec['type'];
	$id_table_objet_exec = $exec['id_table_objet'];
	$id_objet_exec       = intval(_request($id_table_objet_exec));
	// soit objet et id_objet sont passés en paramètre, soit on prend l'objet édité sur la page
	$objet               = !empty($flux['args']['objet']) ? $flux['args']['objet'] : $objet_exec;
	$id_objet            = !empty($flux['args']['id_objet']) ? $flux['args']['id_objet'] : $id_objet_exec;

	if (
		$exec !== false // page d'un objet éditorial
		and $exec['edition'] === false // pas en mode édition
		and $id_document = intval($flux['args']['id_document'])
		// AND ($media=sql_getfetsel('media','spip_documents',"id_document=".$id_document)=='image') // que pour les images
		and autoriser('modifier', 'document', $id_document)
		and $objet
		and $id_objet
	) {
		// description des roles
		include_spip('inc/roles');
		$roles = roles_presents('document', $objet);
		// bloc à recharger
		$ajaxreload = !empty($flux['args']['ajaxreload']) ? $flux['args']['ajaxreload'] : '#documents';
		// mini-formulaire
		$form = recuperer_fond('prive/squelettes/inclure/editer_roles_objet_lie',
			array(
				'objet_source'    => "document",
				'id_objet_source' => $id_document,
				'objet'           => $objet,
				'id_objet'        => $id_objet,
				'options' => array(
					'ajaxReload' => $ajaxreload,
				),
			)
		);
		$texte = $form;
	}

	if ($texte) {
		$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Synchroniser le champ "vu" lors de la création d'un nouveau lien de document portant un rôle.
 *
 * @pipeline post_edition_liens
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_post_edition_lien($flux) {
	if (
		$flux['args']['action'] == 'insert'             // il s'agit d'une création de lien
		and $flux['args']['objet_source'] == 'document' // on a affaire à un document
		and isset($flux['args']['role'])
		and $role = $flux['args']['role']
		and strlen($role)                               // et il y a un role donné
		and isset($flux['args']['colonne_role'])
		and $colonne_role = $flux['args']['colonne_role']
		and $id_document = intval($flux['args']['id_objet_source'])
		and $objet = $flux['args']['objet']
		and $id_objet = intval($flux['args']['id_objet'])
		and $vu = sql_getfetsel('vu', 'spip_documents_liens', 'id_document=' .$id_document .' AND objet='.sql_quote($objet) .' AND id_objet='.$id_objet .' AND '.$colonne_role.'='.sql_quote('document'))
	) {
		include_spip('action/editer_liens');
		objet_qualifier_liens(array('document'=>$id_document), array($objet=>$id_objet), array($colonne_role=>$role, 'vu'=>$vu));
	}

	return $flux;
}


/**
 * Après la modif d'un objet, synchroniser le vu de tous les document liés ayant un rôle
 * avec celui du lien de base (ayant le rôle par défaut)
 *
 * @pipeline post_edition
 *
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function roles_documents_post_edition($flux) {
	if (
		isset($flux['args']['action'])
		and $flux['args']['action'] == 'modifier'       // on modifie un objet
		and $flux['args']['table'] !== 'spip_documents'  // mais pas un document
		and $objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id_objet'])
	){
		include_spip('action/editer_liens');

		// on regarde s'il y a des documents liés à l'objet modifié
		if (count($liens = objet_trouver_liens(array('document'=>'*'),array($objet=>$id_objet)))) {
			foreach ($liens as $l) {
				// on récupère le champ "vu" du lien avec le rôle par défaut
				$vu = sql_getfetsel(
					'vu',
					'spip_documents_liens',
					'id_document=' .$l['id_document'] .' AND objet='.sql_quote($objet) .' AND id_objet='.$id_objet .' AND role='.sql_quote('document')
				);
				// on met à jour tous les autres liens avec rôle
				sql_updateq(
					'spip_documents_liens',
					array('vu'=>$vu),
					'id_document=' .$l['id_document'] .' AND objet='.sql_quote($objet) .' AND id_objet='.$id_objet .' AND role!='.sql_quote('document')
				);
			}
		}
	}

	return $flux;
}
