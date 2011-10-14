<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_db_utils');
include_spip('inc/gmap_config_utils');

// Inserer les scripts dans la partie privée
// Dans la partie privée, on le fait tout le temps, même si on n'est pas sur une page qui contient une carte
function gmap_insert_head_prive($flux)
{
	// Init du retour
	$flux .= "\n" . '<!-- Header GMAP -->' . "\n";
	
	// Inclure le style
	$css_prive = _DIR_PLUGIN_GMAP . 'style/gmap_private.css';
	$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$css_prive.'" />' . "\n";
	$css_balloon = _DIR_PLUGIN_GMAP . 'style/gmap-balloon.css';
	$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$css_balloon.'" />' . "\n";
	
	// Ajouter le style du picker de spip_bonux
	$css_picker = find_in_path('formulaires/selecteur/picker.css');
	if ($css_picker)
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$css_picker.'" />' . "\n";
	
	// On n'inclut pas les scripts si la clef Google Maps n'est pas définie
	if (!gmap_est_actif())
		return $flux;
		
	// Inclure les outils de base
	$js_utils = _DIR_PLUGIN_GMAP . 'javascript/gmap_js_utils.js';
	$flux .= '<script type="text/javascript" src="'.$js_utils.'"></script>' . "\n";
	
	// Inclure le script google
	$gmap_script_init = charger_fonction('gmap_script_init','inc');
	$flux .= $gmap_script_init();
	
	// Inclure les scripts supplémentaires et les styles pour la partie privée
	$js_prive = _DIR_PLUGIN_GMAP . 'javascript/gmap_private.js';
	$flux .= '<script type="text/javascript" src="'.$js_prive.'"></script>' . "\n";
	
	// Fin d'inclusion
	$flux .= '<!-- Fin header GMAP -->' . "\n";
	
	return $flux;
}

// Insérer les cartes dans la partie privée
function gmap_saisie_geo_info($flux)
{
	// Si la carte n'est pas complètement fonctionelle, inutile de faire quoi que ce soit : il faut d'abord paramétrer
	if (!gmap_est_actif())
		return $flux;
	
	// Edition d'une rubrique
	if ($flux['args']['exec'] === 'naviguer')
	{
		$id_rubrique = $flux['args']['id_rubrique'];
		if (gmap_est_geolocalisable('rubrique', $id_rubrique))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_rubrique, 'rubrique', $flux['args']['exec']);
		}
	}
	
	// Edition d'un article
	else if ($flux['args']['exec'] === 'articles')
	{
		$id_article = $flux['args']['id_article'];
		if (gmap_est_geolocalisable('article', $id_article))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_article, 'article', $flux['args']['exec']);
			if (gmap_lire_config('gmap_edit_params', 'hack_modalbox', 'oui') === "oui")
				$flux['data'] .= '
<script type="text/javascript">
//<![CDATA[
// CONTOURNEMENT : je n\'arrive pas à faire fonctionner le formulaire en ajax 
// dans modalbox qui est utilisé par le plugin médiathèque ! L\'évènement 
// document.ready est envoyé avant que la div ne soit ajoutée au document.
// Et même en contournant ça avec un ajaxComplete, la soumission du formulaire
// en ajax ne marche pas non plus (je n\'ai pas eu le courage de chercher
// pourquoi.
// ==> Solution de base, je désactive ModalBox sur les liens "modifier"...
jQuery(document).ready(function()
{
	jQuery("#portfolios").find("a.editbox").removeClass("editbox").removeAttr("target");
});
//]]>
</script>'."\n";
		}
	}

	// Edition d'un document
	// Avec le plugin médiathèque, deux éditions possibles :
	// - documents_edit : c'est la page à laquelle on accède depuis la médiathèque
	// - document_edit : c'est le popup qui s'affiche quand on fait "modifier" sur un doc depuis son article
	else if ($flux['args']['exec'] === 'documents_edit')
	{
		$id_document = $flux['args']['id_document'];
		if (gmap_est_geolocalisable('document', $id_document))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_document, 'document', $flux['args']['exec'], 1);
		}
	}
/*	Comme dit plus haut, je n'arrive pas à faire marcher l'initialisation de la carte
	et la soumission du formulaire dans une modalbox, donc inutile d'ajouter le code.
	Si quelqu'un a une idée...
	else if ($flux['args']['exec'] === 'document_edit')
	{
		$id_document = $flux['args']['id_document'];
		if (gmap_est_geolocalisable('document', $id_document))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_document, 'document', $flux['args']['exec']);
		}
	}*/

	// Edition d'une brève
	else if ($flux['args']['exec'] === 'breves_voir')
	{
		$id_breve = $flux['args']['id_breve'];
		if (gmap_est_geolocalisable('breve', $id_breve))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_breve, 'breve', $flux['args']['exec']);
		}
	}

	// Edition d'un mot-clef
	else if ($flux['args']['exec'] === 'mots_edit')
	{
		$id_mot = $flux['args']['id_mot'];
		if (gmap_est_geolocalisable('mot', $id_mot))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_mot, 'mot', $flux['args']['exec']);
		}
	}

	// Edition d'un auteur
	else if ($flux['args']['exec'] === 'auteur_infos')
	{
		$id_auteur = $flux['args']['id_auteur'];
		if (gmap_est_geolocalisable('auteur', $id_auteur))
		{
			include_spip('inc/gmap_saisie_privee');
			$flux['data'] .= gmap_saisie_privee($id_auteur, 'auteur', $flux['args']['exec']);
		}
	}

	return $flux;
}

// Insertion des styles et script dans le header
function gmap_insert_head($flux)
{
	// Si la carte n'est pas complètement fonctionelle, inutile de faire quoi que ce soit : il faut d'abord paramétrer
	if (!gmap_est_actif())
		return $flux;
		
	// Init du retour
	$flux .= "\n" . '<!-- Header GMAP -->' . "\n";
	
	// Inclure le style
	$css_public = _DIR_PLUGIN_GMAP . 'style/gmap_public.css';
	$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$css_public.'" />' . "\n";
	$css_balloon = _DIR_PLUGIN_GMAP . 'style/gmap-balloon.css';
	$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$css_balloon.'" />' . "\n";
	
	// Inclure les outils de base
	$js_utils = _DIR_PLUGIN_GMAP . 'javascript/gmap_js_utils.js';
	$flux .= '<script type="text/javascript" src="'.$js_utils.'"></script>' . "\n";
		
	// Inclure le script google
	$gmap_script_init = charger_fonction('gmap_script_init','inc');
	$flux .= $gmap_script_init();
	
	// Fin d'inclusion
	$flux .= '<!-- Fin header GMAP -->' . "\n";
	
	return $flux;
}

// Inserer les scripts dans la partie publique
// Ancienne fonction, avant l'ajout du pipeline insert_head
function gmap_affichage_final($flux)
{
	// Si la carte n'est pas complètement fonctionelle, inutile de faire quoi que ce soit : il faut d'abord paramétrer
	if (!gmap_est_actif())
		return $flux;
	
	// S'il y a une carte, insérer le script
    if (strpos($flux, '<div id="gmap_cont') !== FALSE)
	{
		$incHead = gmap_insert_head('');
		if (strlen($incHead) == 0)
			return $flux;
		else
			return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
    }
	else
		return $flux;
}

// Récupérer les informations de position sur un objet
function gmap_information_exif($info)
{
	$point = gmap_get_point($info['objet'], $info['id_objet']);
	$info['longitude'] = $point['longitude'];
	$info['latitude'] = $point['latitude'];
	return $info;
}

?>