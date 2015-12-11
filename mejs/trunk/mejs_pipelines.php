<?php
/**
 * Utilisations de pipelines par MediaElementPlayer.
 *
 * @plugin     MediaElementPlayer
 * @copyright  2014-2015
 * @author     John Dyer
 * @licence    MIT
 * @package    SPIP\Mejs\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * inserer systematiquement le CSS dans la page.
 *
 * @param string $flux
 *
 * @return string
 */
function mejs_insert_head_css($flux) {
	$css = find_in_path('mejs/mediaelementplayer.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
	$css = find_in_path('mejs/mejs-skins.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $flux;
}

/**
 * inserer systematiquement le JS dans la page
 *  on utilise uniquement la fonction pour l'affichage prive
 *  pour l'affichage publique on utilise  plutot le pipeline affichage_final comme sur video accessible.
 *
 * @param string $flux
 *
 * @return string
 */
function mejs_insert_head($flux) {
	$js = find_in_path('mejs/mediaelement-and-player.min.js');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	// $flux .= "<script>$('video,audio').mediaelementplayer();</script>\n";     // l'appel se fait à la volee
	return $flux;
}

/**
 * Insertion dynamique du js en pied de page,
 * uniquement en presence de video sur la page
 * et sur les pages html ! (pas dans les flux rss ou autre).
 *
 *(pas l'instant pas utilise)
 *
 * @param string $flux
 *
 * @return string
 */
function mejs_affichage_final($flux) {
	if ($GLOBALS['html']
		and stripos($flux, 'mejs-')) {
		$script = find_in_path('mejs/mediaelement-and-player.min.js');
		lire_fichier($script, $js);
		$js = '<script type="text/javascript">/*<![CDATA[*/'.$js.'/*]]>*/</script>';
		if ($p = stripos($flux, '</body>')) {
			$flux = substr_replace($flux, $js, $p, 0);
		} else {
			$flux .= $js;
		}
	}

	return $flux;
}

/**
 * inserer systematiquement le JS dans la page.
 *
 * @param string $flux
 *
 * @return string
 */
function mejs_header_prive($flux) {
	$flux = mejs_insert_head($flux);
	$flux .= "<script type='text/javascript'>jQuery(function(){ $('video,audio').mediaelementplayer();});</script>\n"; // dans le prive, on a appel le script via le head
	$flux = mejs_insert_head_css($flux);

	return $flux;
}
