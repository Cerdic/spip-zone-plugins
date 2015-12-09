<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

/* compat SPIP 1.9 */
if (!function_exists('redirige_action_post')) {
function redirige_action_post($action, $arg, $ret, $gra, $corps, $att='') {
	$r = _DIR_RESTREINT . generer_url_ecrire($ret, $gra, false, true);
	return generer_action_auteur($action, $arg, $r, $corps, $att . " method='post'");
}
}

// http://doc.spip.org/@calculer_taille_dossier
function calculer_taille_dossier ($dir) {
	$handle = @opendir($dir);
	if (!$handle) return;
	$taille = 0;
	while (($fichier = @readdir($handle)) !== false) {
		// Eviter ".", "..", ".htaccess", etc.
		if ($fichier[0] == '.') continue;
		if (is_file($d = "$dir/$fichier")) {
			$taille += filesize($d);
		}
		else if (is_dir($d))
			$taille += calculer_taille_dossier($d);
	}
	closedir($handle);
	return $taille;
}



// http://doc.spip.org/@afficher_taille_cache_vignettes
function afficher_taille_cache_vignettes() {
	$taille = calculer_taille_dossier(_DIR_VAR);
	return _T('ecrire:taille_cache_image',
		array(
			'dir' => joli_repertoire(_DIR_VAR),
			'taille' => "<b>".taille_en_octets($taille)."</b>"
			)
		);
}

// http://doc.spip.org/@exec_admin_vider_dist
function exec_admin_vider_dist()
{
	global $spip_lang;

	// autorisation a affiner 
	if (function_exists('autoriser') AND !autoriser('configurer', 'admin_vider')){
		include_spip('inc/minipres');
		echo minipres();
	} else {
		if ($commencer_page = charger_fonction('commencer_page', 'inc', true))
		echo $commencer_page(_T('onglet_vider_cache'), "configuration", "cache");

		echo "<br /><br /><br />";
		echo gros_titre(_T('titre_admin_vider'),'', false);
// barre_onglets("administration", "vider");

		echo debut_gauche("",true);

		echo debut_boite_info(true);

		echo _T('info_gauche_admin_vider');

		echo fin_boite_info(true);

		echo debut_droite("",true);

		echo debut_cadre_trait_couleur("cache-24.gif", true, "", _T('texte_vider_cache'));

		echo "\n<p style='text-align: justify;'>"._T('texte_suppression_fichiers')."</p>",
		  "<p style='text-align: justify;'>"._T('texte_recalcul_page')."</p>";

		echo "\n<div>&nbsp;</div>";

	//
	// taille du cache
	//

		$m = &$GLOBALS['Memoization'];
		$info = _T('memoization:methodes_cache', array('type' => $m->methode));
		if (is_int($n = $m->size())) {
			if ($n > 0*250*1024)
				$info2 = _T('taille_cache_octets', array('octets' => taille_en_octets($n)));
			else
				$info2 = _T('taille_cache_vide');
		}
		else
			$info2 = _T('memoization:taille_tototale_indisponible');
		$bouton = _T('memoization:invalider_cache');

		echo debut_cadre_relief("", true, "", _T('taille_repertoire_cache'));

		echo "<p style='text-align: justify;'><b>$info</b></p>\n";

		if ($info2) {
			echo "\n<p style='text-align: justify;'>";
			echo $info2;
			echo "</p>";
		}

		echo
			redirige_action_post('purger', 'cache', "admin_vider", '',
				 "\n<div style='text-align: right'><input  type='submit' value=\"" .
		 str_replace('"', '&quot;', $bouton) .
				 "\" /></div>");
		echo fin_cadre_relief(true);

		echo debut_cadre_relief("image-24.gif", true, "", _T('info_images_auto'));

		echo afficher_taille_cache_vignettes();

		echo redirige_action_post('purger', 'vignettes', "admin_vider",'',
					    "\n<div style='text-align: right'><input  type='submit' value=\"" .
					    str_replace('"', '&quot;', _T('bouton_vider_cache')) .
					    "\" /></div>");

		echo fin_cadre_relief(true);

		echo fin_cadre_trait_couleur(true);
		echo "<br />";
		echo fin_gauche(), fin_page();
	}
}
?>
