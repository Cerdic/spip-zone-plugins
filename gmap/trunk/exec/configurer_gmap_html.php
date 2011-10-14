<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

include_spip('inc/presentation');
include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('configuration/gmap_config_onglets');

if (!defined("_ECRIRE_INC_VERSION")) return;

// Boîtes d'information gauche
function boite_info_plan($root)
{
	$flux = '';
	
	// Rechercher la page de plan
	$file = $root.'doc/plan.html';
	$contents = "";
	if (@file_exists($file))
		$contents = @file_get_contents($file);
	if (strlen($contents) == 0)
		return '';
		
	// Prendre le contenu du body
	if (preg_match('/<body(.*)>(.*)<\/body>/sU', $contents, $matches) === 1)
	{
		$attrs = $matches[1];
		$contents = $matches[2];
	}
	if (strlen($contents) == 0)
		return '';
		
	// Transformer tous les liens qui ne commencent pas par 'http:' ou '..\' en se servant du folder courant
	$contents = preg_replace('/href="(?!(http:|\.\.))(.*)\.html"/U', 'href="'.generer_url_ecrire('configurer_gmap_html').'&page=doc/$2"', $contents);
	// Transformer tous les liens commençant par .. sans tenir compte du folder (ATTENTION : ça ne supporte pas plusieurs niveau de remontée...)
	$contents = preg_replace('/href="\.\.\/(.*)\.html"/U', 'href="'.generer_url_ecrire('configurer_gmap_html').'&page=$1"', $contents);
	
	// Début de la boîte d'information
	$flux .= debut_boite_info(true);
	
	// Titre
	$flux .= propre('<div id="help-plan"'.$attrs.'>');
	$flux .= propre('<h1>'._T('gmap:info_configuration_html_plan').'</h1>');
	$flux .= propre($contents);
	$flux .= propre('</div>');
	
	// Script pour mettre à jour la page active
	$script = '
var curPage = "'.generer_url_ecrire('configurer_gmap_html').'&page='._request('page').'";
jQuery(document).ready(function()
{
	jQuery("#help-plan a").each(function()
	{
		if (jQuery(this).attr("href") === curPage)
			jQuery(this).addClass("active");
	});
});
';
	$flux .= http_script($script);
	
	// Fin de la boîte
	$flux .= fin_boite_info(true);
	
	return $flux;
}

// Page de configuration
function exec_configurer_gmap_html_dist($class = null)
{
	// vérifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Décoder le paramètre
	$page = _request('page');
	if (!$page)
		$page = "doc/index";
	$page_parts = explode("/", $page);
	$page = array_pop($page_parts);
	$folder = implode("/", $page_parts);
	$lang = $GLOBALS['spip_lang'];
	
	// Recherche de la racine (la langue par défaut est le français...)
	$root = _DIR_PLUGIN_GMAP.'html/'.$lang.'/';
	if (!@is_dir($root))
		$root = _DIR_PLUGIN_GMAP.'html/fr/';
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap_html'),'data'=>''));
	
	// affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap_html');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_help");
	echo debut_gauche('', true);
	
	// Informations sur la colonne gauche
	echo boite_info_plan($root);
	
	// Suite des affichages SPIP
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_html'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_html'),'data'=>''));
	echo debut_droite("", true);
	
	// Lire le contenu
	$file = $root.$folder.'/'.$page.'.html';
	$contents = "";
	$error = "";
	if (@file_exists($file))
		$contents = @file_get_contents($file);
	if (strlen($contents) == 0)
	{
		$error = _T('gmap:erreur_aide_html_debut').$page._T('gmap:erreur_aide_html_fin');
		$file = $root.$folder.'/index.html';
		$contents = @file_get_contents($file);
	}
	
	// Récupérer les titres et les feuilles de style
	if (preg_match('/<title>(.*)<\/title>/s', $contents, $matches) === 1)
		$title = $matches[1];
	else
		$title = _T('gmap:html_titre_defaut');
	if (preg_match_all('/<link (.*)href="([[:alnum:]-_]*).css"(.*)\/>/U', $contents, $matches, PREG_PATTERN_ORDER) !== FALSE)
	{
		foreach ($matches[2] as $cssFile)
		{
			echo "<style>\n<!--\n";
			readfile($root.$folder.'/'.$cssFile.'.css');
			echo "-->\n</style>\n";
		}
	}
	
	// Récupérer seulement le contenu du tag body
	$attrs = '';
	if (preg_match('/<body(.*)>(.*)<\/body>/sU', $contents, $matches) === 1)
	{
		$attrs = $matches[1];
		$contents = $matches[2];
	}
	else
		$contents = "";

	// Ajouter target="_blank" à tous les liens externes
	$contents = preg_replace('/href="http:\/\/(.*)"/U', 'href="http://$1" target="_blank"', $contents);
	
	// Transformer tous les liens qui ne commencent pas par 'http:' ou '..\' en se servant du folder courant
	$contents = preg_replace('/href="(?!(http:|\.\.))(.*)\.html"/U', 'href="'.generer_url_ecrire('configurer_gmap_html').'&page='.$folder.'/$2"', $contents);
	// Transformer tous les liens commençant par .. sans tenir compte du folder (ATTENTION : ça ne supporte pas plusieurs niveau de remontée...)
	$contents = preg_replace('/href="\.\.\/(.*)\.html"/U', 'href="'.generer_url_ecrire('configurer_gmap_html').'&page=$1"', $contents);
	
	// Transformer les images (ATTENTION : ça ne supportera pas de remontée)
	$contents = preg_replace('/src="(?!http:)(.*)"/U', 'src="'.$root.$folder.'/$1"', $contents);
	
	// Afficher le contenu du fichier
	if (strpos($folder, "doc-dev") !== FALSE)
		$logo = find_in_path('images/logo-config-doc-dev.png');
	else if (strpos($folder, "doc") !== FALSE)
		$logo = find_in_path('images/logo-config-doc.png');
	else if (strpos($folder, "credits") !== FALSE)
		$logo = find_in_path('images/logo-config-credits.png');
	echo debut_cadre_trait_couleur($logo, true, '', $title);
	if (strlen($error) > 0)
		echo '<div class="help-error"><p>'.$error.'</p></div>' . "\n";
	echo '<div id="help-contents"'.$attrs.'>' . "\n";
	echo $contents;
	echo '</div>' . "\n";
	echo fin_cadre_trait_couleur(true);
	
	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
