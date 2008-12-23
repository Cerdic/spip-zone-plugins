<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

if (!defined('_DIR_PLUGIN_FORMS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p))."/");
}
if (defined('_DIR_PLUGIN_CRAYONS'))
	include_spip('forms_crayons');

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/forms');
$GLOBALS['forms_actif_exec'][] = 'donnees_edit';
$GLOBALS['forms_saisie_km_exec'][] = 'donnees_edit';
// pipelines d'ajout et surcharge des champs
foreach(array(
	#edition du formulaire
	'forms_types_champs',
	'forms_bloc_edition_champ',
	'forms_update_edition_champ',
	#visualisation du formulaire
	'forms_label_details',
	'forms_input_champs',
	'forms_ajoute_styles',
	#pre remplissage du formulaire
	'forms_pre_remplit_formulaire',
	#modification des donnees apres saisie du formulaire
	'forms_pre_edition_donnee',
	'forms_post_edition_donnee',
	'forms_valide_conformite_champ',
	'forms_message_complement_post_saisie',
	#affichage des donnees
	'forms_calcule_valeur_en_clair',
	# CVT pre 2.0
	'formulaire_charger',
	'formulaire_verifier',
	'formulaire_traiter',
	) as $pipe)
	if (!isset($GLOBALS['spip_pipeline'][$pipe])) $GLOBALS['spip_pipeline'][$pipe] = '';


if (version_compare($GLOBALS['spip_version_code'],'1.9200','<')){
	function inc_safehtml($t) {
		include_spip('inc/forms_safehtml_191');
		if (function_exists('inc_safehtml_dist'))
			return inc_safehtml_dist($t);
		return $t;
	}
}

function Forms_generer_url_sondage($id_form) {
	return generer_url_public("sondage","id_form=$id_form",true);
}

function Forms_definir_session($session){
	foreach($_COOKIE as $cookie=>$value){
		if (strpos($cookie,'cookie_form_')!==FALSE)
			$session .= "-$cookie:$value";
	}
	return $session;
}
?>