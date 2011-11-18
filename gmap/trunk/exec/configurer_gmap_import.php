<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de param�trage du plugin
 *
 */

include_spip('inc/presentation');
include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('configuration/gmap_config_onglets');

if (!defined("_ECRIRE_INC_VERSION")) return;

// Bo�tes d'information gauche
function boite_info_help()
{
	$flux = '';
	
	// D�but de la bo�te d'information
	$flux .= debut_boite_info(true);
	
	// Info globale
	$flux .= propre(_T('gmap:info_configuration_gmap_import'));
	
	// Lien sur l'aide
	$url = generer_url_ecrire('configurer_gmap_html').'&page=doc/parametrage#paramImportExport';
	$flux .= propre('<a href="'.$url.'">'._T('gmap:info_configuration_help').'</a>');
	
	// Fin de la bo�te
	$flux .= fin_boite_info(true);
	
	return $flux;
}

// Page de configuration
function exec_configurer_gmap_import_dist($class = null)
{
	// v�rifier une nouvelle fois les autorisations
	if (!autoriser('webmestre'))
	{
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	// Pipeline pour customiser
	pipeline('exec_init',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	
	// affichages de SPIP
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('gmap:configuration_titre'), 'configurer_gmap', 'configurer_gmap_import');
	echo "<br /><br /><br />\n";
	$logo = '<img src="'.find_in_path('images/logo-config-title-big.png').'" alt="" style="vertical-align: center" />';
	echo gros_titre(_T('gmap:configuration_titre'), $logo, false);
	echo barre_onglets("configurer_gmap", "cg_import");
	echo debut_gauche('', true);
	
	// Informations sur la colonne gauche
	echo boite_info_help();
	
	// Suite des affichages SPIP
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'configurer_gmap_import'),'data'=>''));
	echo debut_droite("", true);
	
	// R�cup�rer une liste des fichiers de configuration
	// �a marche comme �a mais ce n'est pas tr�s ouvert : on va trouver tous
	// les fichiers php qui contiennent outil_ puis on va restreindre � ceux
	// qui sont dans gmap. Serait peut-�tre mieux d'utiliser un pipeline...
	$outils = find_all_in_path('configuration/','outil_\w+\.php$');
	foreach ($outils as $outil)
	{
		// find_all_in_path renvoie un path relatif depuis le dossier ecrire/ (dans la partie priv�e)
		// donc il faut revenir � un path relatif � la racine de gmap.
		$root = _DIR_PLUGIN_GMAP.'configuration/';
		if (!strncmp($outil, $root, strlen($root)))
		{
			$outil = substr($outil, strlen($root));
			$outil = substr($outil, 0, strrpos($outil, '.'));
			if (strncmp($outil, 'faire_', strlen('faire_')) != 0)
			{
				$outil_cmd = charger_fonction($outil, 'configuration');
				if (is_callable($outil_cmd))
					echo $outil_cmd();
			}
		}
	}
	
	// pied de page SPIP
	echo fin_gauche() . fin_page();
}

?>
