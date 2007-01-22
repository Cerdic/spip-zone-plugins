<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/liens_contenus');

function liens_contenus_post_edition($flux)
{
	if (!isset($flux['args']['id_objet']) || !isset($flux['args']['table'])) {
		return $flux;
	}

	$id_objet = $flux['args']['id_objet'];
	$type_objet = ereg_replace("^spip_(.*[^s])s?$", "\\1", $flux['args']['table']);
	// Cas particulier des sites
	if ($type_objet == 'syndic') {
	   spip_log('un site !!!');
    	$type_objet = 'site';
	}
	// Traitement des redirections
	if ($type_objet == 'article' && substr($flux['data']['chapo'], 0, 1) == '=') {
		$flux['data']['chapo'] = '[->'.substr($flux['data']['chapo'], 1).']';
	}
	$contenu = join(' ',$flux['data']);
	liens_contenus_referencer_liens($type_objet, $id_objet, $contenu);

	return $flux;
}

function liens_contenus_affiche_droite($flux)
{
	if (!isset($flux['args']['exec'])) {
		return $flux;
	}

    // On vérifie si la table a été créée
    $liens_contenus_version_base_active = isset($GLOBALS['meta']['liens_contenus_version_base']) ? $GLOBALS['meta']['liens_contenus_version_base'] : 0;
    if ($liens_contenus_version_base_active == 0) {
        liens_contenus_verifier_version_base();
    }
    
    $exec = $flux['args']['exec'];
    $liste_pages = array(
        'naviguer' => array('rubrique', 'id_rubrique'),
        'articles' => array('article', 'id_article'),
        'breves_voir' => array('breve', 'id_breve'),
        'sites' => array('site', 'id_syndic'),
        'mots_edit' => array('mot', 'id_mot'),
        'auteur_infos' => array('auteur', 'id_auteur')
        // TODO : Ajouter les autres
        );
    if (isset($liste_pages[$exec])) {
        $type = $liste_pages[$exec];
        $flux['data'] .= liens_contenus_boite_liste($type[0], $flux['args'][$type[1]]);
    }
    return $flux;
}

function liens_contenus_header_prive($flux)
{
	// On ajoute une CSS pour le back-office
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_LIENS_CONTENUS.'/css/styles.css" />';
	return $flux;
}
?>