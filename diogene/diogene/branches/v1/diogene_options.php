<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Options spécifiques à Diogene (chargé à chaque hit)
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Déclaration des pipelines du plugin
 */

// #PIPELINE
$GLOBALS['spip_pipeline']['diogene_avant_formulaire']="";
$GLOBALS['spip_pipeline']['diogene_champs_sup']="";
$GLOBALS['spip_pipeline']['diogene_champs_texte']="";

// dans CVT
$GLOBALS['spip_pipeline']['diogene_ajouter_saisies']="";
$GLOBALS['spip_pipeline']['diogene_charger']="";
$GLOBALS['spip_pipeline']['diogene_verifier']="";
$GLOBALS['spip_pipeline']['diogene_traiter']="";
$GLOBALS['spip_pipeline']['diogene_champs_pre_edition']="";

/**
 * Le libellé du bloc de logo des diogènes dans le privé
 */
$GLOBALS['logo_libelles']['id_diogene'] = _T('diogene:libelle_logo_diogene');

/**
 * Fonction de révision d'un diogène
 * 
 * @param int $id_diogene Identifiant numérique du diogene
 * @param array $champs un tableau des champs à modifier en base
 */
function revision_diogene($id_diogene,$champs=false){

	modifier_contenu('diogene', $id_diogene,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')) 
		),
		$champs);

	return '';
}

?>