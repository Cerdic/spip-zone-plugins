<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (defined('_DIR_PLUGIN_CRAYONS'))
	include_spip('crayons/forms_crayons');

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


function forms_generer_url_sondage($id_form) {
	return generer_url_public("sondage","id_form=$id_form",true);
}

function forms_definir_session($session){
	foreach($_COOKIE as $cookie=>$value){
		if (strpos($cookie,'cookie_form_')!==FALSE)
			$session .= "-$cookie:$value";
	}
	return $session;
}
?>