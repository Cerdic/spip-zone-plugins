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
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_html'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_html'),'data'=>''));
	echo debut_droite("", true);
	
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
	
	// Récupérer seulement le contenu du tag body
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
	if (preg_match('/<body>(.*)<\/body>/s', $contents, $matches) === 1)
		$contents = $matches[1];

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
	echo '<div id="help-contents">' . "\n";
	echo $contents;
	echo '</div>' . "\n";
	echo fin_cadre_trait_couleur(true);
	
	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
