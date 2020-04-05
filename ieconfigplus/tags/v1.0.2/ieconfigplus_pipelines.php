<?php
/**
 * Utilisations de pipelines par Importeur/Exporteur de configuration PLUS
 *
 * @plugin     Importeur/Exporteur de configuration PLUS
 * @copyright  2018
 * @author     Arnaud B. (Mist. GraphX)
 * @licence    GNU/GPL
 * @package    SPIP\Ieconfigplus\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
// Import-export
//
// il est possible d’indiquer, via le pipeline ieconfig_metas,
// un préfixe de méta en le faisant suivre d’une astérisque (*).
//
// par exemple :
//
// function prefixe_plugin_ieconfig_metas($table){
// 		$table['prefixe_plugin']['titre'] = nom_du_plugin;
// 		$table['prefixe_plugin']['icone'] = 'chemin/image.png';
// 		$table['prefixe_plugin']['metas_serialize'] = 'titi,toto_*';
// 		return $table;
// 	}

function ieconfigplus_ieconfig_metas($table){

	// Plugins

	// SEO
	if(test_plugin_actif('seo')){
		$table['seo']['titre'] = _T('paquet-seo:seo_slogan');
		$table['seo']['icone'] = 'prive/themes/spip/images/seo-16.png';
		$table['seo']['metas_serialize'] = 'seo';
	}

  // Export de config des plugins <utilise>
  // http://zone.spip.org/trac/spip-zone/browser/_plugins_/ieconfig/trunk/ieconfig_pipelines.php

  // Gis
  if(test_plugin_actif('gis')){
      $table['gis']['titre'] = _T('gis:cfg_titre_gis');
      $table['gis']['icone'] = 'prive/themes/spip/images/gis-16.png';
      $table['gis']['metas_serialize'] = 'gis';
  }

  // Facebook Modèle : fb_modeles
  if(test_plugin_actif('fb_modeles')){
      $table['fb_modeles']['titre'] = _T('fbmodeles:cfg_descr_titre');
      $table['fb_modeles']['icone'] = 'prive/themes/spip/images/fb-btn-16.png';
      $table['fb_modeles']['metas_serialize'] = 'fb_modeles';
  }

	// Autorite
  if(test_plugin_actif('autorite')){
      $table['autorite']['titre'] = _T('paquet-autorite:autorite_nom');
      $table['autorite']['icone'] = 'prive/themes/spip/images/illuminati-16.png';
      $table['autorite']['metas_serialize'] = 'autorite';
  }

	// Paniers
  if(test_plugin_actif('paniers')){
      $table['paniers']['titre'] = _T('paniers:titre_panier');
      $table['paniers']['icone'] = 'prive/themes/spip/images/paniers-16.png';
      $table['paniers']['metas_serialize'] = 'paniers,paniers_*';
  }

	// Bank
  if(test_plugin_actif('bank')){
      $table['bank']['titre'] = _T('bank:titre_menu_configurer');
      $table['bank']['icone'] = 'prive/themes/spip/images/credit-card-16.png';
      $table['bank']['metas_serialize'] = 'bank,bank_*';
  }

  if(test_plugin_actif('mailsubscribers')){
      $table['mailsubscribers']['titre'] = 'Mailsubscribers';
      $table['mailsubscribers']['icone'] = 'prive/themes/spip/images/mailsubscriber-16.png';
      $table['mailsubscribers']['metas_serialize'] = 'mailsubscribers,mailsubscribers_*';
  }

  return $table;
}

/**
 * Pipeline ieconfig pour l'import/export de configuration
 *
 *
 * @see http://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation
 * @todo gérer lors de l'import si on a des identifiants identiques, voir ou en est le plugin IDENTIFIANTS
 * @todo annoncer les erreurs d'import si un champ sql n'est pas présent (exemple composition et pages), lors de l'export d'un skel x vers un skel y
 *
 * @param array $flux
 * @return array
 */
function ieconfigplus_ieconfig($flux){
  include_spip('inc/texte');
	$action = $flux['args']['action'];

  // Export des groupes et mots-clefs
  include_spip('inc/ieconfig_mots_fonctions');
  $mots = ieconfig_mots($flux,$action);

  // Pages
  if(test_plugin_actif('pages')){
    include_spip('inc/ieconfig_pages_fonctions');
    $pages = ieconfig_pages($flux,$action);
  }

  // Selections Editoriales
  if(test_plugin_actif('selections_editoriales')){
    include_spip('inc/ieconfig_selections_editoriales_fonctions');
    $selections = ieconfig_selections_editoriales($flux,$action);
  }

  // Formidable
  if(test_plugin_actif('formidable')){
      include_spip('inc/ieconfig_formidable_fonctions');
      $formidables = ieconfig_formidable($flux, $action);
  }


  return $flux;
}
