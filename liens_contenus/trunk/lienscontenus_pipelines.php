<?php
include_spip('inc/lienscontenus');

function lienscontenus_pre_edition($flux)
{
	if (!isset($flux['args']['id_objet']) || !isset($flux['args']['type']) || $flux['args']['action'] != 'modifier') {
		return $flux;
	}

	$id_objet = $flux['args']['id_objet'];
    $type_objet = $flux['args']['type'];
    $data = $flux['data'];

    // Traitement des redirections
	if ($type_objet == 'article' && substr($data['chapo'], 0, 1) == '=') {
		$data['chapo'] = '[->'.substr($data['chapo'], 1).']';
	}

    // On fait le traitement tout de suite et non pas avec le génie pour que l'utilisateur ait l'interface à jour
	lienscontenus_referencer_liens($type_objet, $id_objet, implode(' ',$data));

	return $flux;
}

function lienscontenus_affiche_droite($flux)
{
	if (!isset($flux['args']['exec'])) {
		return $flux;
	}

	$exec = $flux['args']['exec'];

 	// TODO : Ajouter les autres
	$liste_pages_unitaires = array(
    'naviguer' => array('rubrique', 'id_rubrique'),
    'articles' => array('article', 'id_article'),
    'breves_voir' => array('breve', 'id_breve'),
    'breves_edit' => array('breve', 'id_breve'),
    'sites' => array('syndic', 'id_syndic'),
    'mots_edit' => array('mot', 'id_mot'),
    'auteur_infos' => array('auteur', 'id_auteur'),
    'forms_edit' => array('form', 'id_form')
	);
	if (isset($liste_pages_unitaires[$exec])) {
		$type = $liste_pages_unitaires[$exec];
		$flux['data'] .= lienscontenus_boite_liste($type[0], $flux['args'][$type[1]]);
	}
	$fonction = 'lienscontenus_verification_'.$exec;
	if (function_exists($fonction)) {
		$flux['data'] .= $fonction();
	}
	return $flux;
}

function lienscontenus_header_prive($flux)
{
	// On ajoute une CSS pour le back-office
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_LIENSCONTENUS.'/css/styles.css" />';
	return $flux;
}

function lienscontenus_taches_generales_cron($taches_generales) {
	$taches_generales['lienscontenus_queue_process'] = 60; // toutes les minutes
    return $taches_generales;
}
?>