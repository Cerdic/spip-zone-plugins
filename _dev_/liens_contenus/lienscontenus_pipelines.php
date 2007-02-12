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

include_spip('inc/lienscontenus');

function lienscontenus_post_edition($flux)
{
	if (!isset($flux['args']['id_objet']) || !isset($flux['args']['table'])) {
		return $flux;
	}

	$id_objet = $flux['args']['id_objet'];
	$type_objet = ereg_replace("^spip_(.*[^s])s?$", "\\1", $flux['args']['table']);
	// Cas particulier des sites
	if ($type_objet == 'syndic') {
    	$type_objet = 'site';
	}

    // On recupere les donnees en base
    include_spip('base/abstract_sql');
    if ($type_objet == 'site') {
        $query = 'SELECT * FROM spip_syndic WHERE id_syndic='._q($id_objet);
    } else {
        $query = 'SELECT * FROM spip_'.$type_objet.'s WHERE id_'.$type_objet.'='._q($id_objet);
    }
    $res = spip_query($query);
    $row = spip_fetch_array($res);
    
	// Traitement des redirections
	if ($type_objet == 'article' && substr($row['chapo'], 0, 1) == '=') {
		$row['chapo'] = '[->'.substr($row['chapo'], 1).']';
	}
	$contenu = join(' ',$row);
	lienscontenus_referencer_liens($type_objet, $id_objet, $contenu);

	return $flux;
}

function lienscontenus_affiche_droite($flux)
{
	if (!isset($flux['args']['exec'])) {
		return $flux;
	}

    // On verifie si la table a ete creee
    lienscontenus_verifier_version_base();
    
    $exec = $flux['args']['exec'];
    $liste_pages = array(
        'naviguer' => array('rubrique', 'id_rubrique'),
        'articles' => array('article', 'id_article'),
        'breves_voir' => array('breve', 'id_breve'),
        'breves_edit' => array('breve', 'id_breve'),
        'sites' => array('site', 'id_syndic'),
        'mots_edit' => array('mot', 'id_mot'),
        'auteur_infos' => array('auteur', 'id_auteur'),
        'forms_edit' => array('form', 'id_form')
        // TODO : Ajouter les autres
        );
    if (isset($liste_pages[$exec])) {
        $type = $liste_pages[$exec];
        $flux['data'] .= lienscontenus_boite_liste($type[0], $flux['args'][$type[1]]);
    }
    return $flux;
}

function lienscontenus_header_prive($flux)
{
	// On ajoute une CSS pour le back-office
	$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_LIENS_CONTENUS.'/css/styles.css" />';
	return $flux;
}
?>