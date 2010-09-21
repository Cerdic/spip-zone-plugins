<?php
/**
 * @name 		SPIP Popup
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	0.2 (06/2009)
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

# --------------------------------------------------------------
# Fichier de configuration pris en compte par config_outils.php 
# et specialement dedie a la configuration de ma lame perso
# --------------------------------------------------------------

// Ajout de l'outil 'popup'
function outils_popup_config_dist() {

	@define('POPUP_SKEL_DEFAUT', 'popup_defaut.html');
	@define('POPUP_TITRE_DEFAUT', 'popup');
	@define('POPUP_WIDTH_DEFAUT', '620');
	@define('POPUP_HEIGHT_DEFAUT', '640');
	$GLOBALS['spipopup_datas'] = array('popup_skel','popup_titre','popup_width','popup_height');

	// Ajout de l'outil 'popup'
	add_outil(array(
		'id' => 'popup',
        'nom' => _T('popup:nom'),
        'contrib' => 3573,
        'auteur' => 'Piero Wbmstr',
        'categorie' => 'spip',
		'description' => _T('popup:description'),
		'code:options' => "%%popup_skel%% define('POPUP_TITRE', %%popup_titre%%);\n define('POPUP_WIDTH', %%popup_width%%);\n define('POPUP_HEIGHT', %%popup_height%%);\n",
		'code:js' => "var popup_settings={default_popup_name:'%%popup_titre%%',default_popup_width:'%%popup_width%%',default_popup_height:'%%popup_height%%'};",
	));

	// Ajout des variables utilisees ci-dessus
	add_variables(
		array(
			'nom' => 'popup_skel',
			'format' => _format_CHAINE,
			'defaut' => '"'.POPUP_SKEL_DEFAUT.'"',
			'code' => "define('POPUP_SKEL', str_replace('.html', '', %s));\n",
			'label' => _T('popup:skel_label'),
		),
		array(
			'nom' => 'popup_titre',
			'format' => _format_CHAINE,
			'defaut' => '"'.POPUP_TITRE_DEFAUT.'"',
			'label' => _T('popup:titre_label'),
		),
		array(
			'nom' => 'popup_width',
			'format' => _format_NOMBRE,
			'defaut' => '"'.POPUP_WIDTH_DEFAUT.'"',
			'label' => _T('popup:width_label'),
		),
		array(
			'nom' => 'popup_height',
			'format' => _format_NOMBRE,
			'defaut' => '"'.POPUP_HEIGHT_DEFAUT.'"',
			'label' => _T('popup:height_label'),
		)
	);

}

?>