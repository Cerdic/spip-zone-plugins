<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// Appelee sans argument, cette fonction retourne un menu de couleurs
// Avec un argument numerique, elle retourne les parametres d'URL 
// pour les feuilles de style calculees (cf commencer_page et svg)
// Avec un argument de type tableau, elle remplace le tableau par defaut
// par celui donne en argument

// http://code.spip.net/@inc_couleurs_dist
function inc_couleurs_dist($choix=NULL)
{
	static $couleurs_spip = array(
// Vert de gris
1 => array (
		"couleur_foncee" => "#fda069",
		"couleur_claire" => "#ffdbc6",
		"couleur_lien" => "#649F5D",
		"couleur_lien_off" => "#243921"
		),
// Rose vieux
2 => array (
		"couleur_foncee" => "#fd83c0",
		"couleur_claire" => "#fdcfe6",
		"couleur_lien" => "#8E9F5C",
		"couleur_lien_off" => "#333921"
		),
// Orange
3 => array (
		"couleur_foncee" => "#b67cfc",
		"couleur_claire" => "#e0c7fe",
		"couleur_lien" => "#9F8E62",
		"couleur_lien_off" => "#393323"
		),
// Violet
4 => array (
		"couleur_foncee" => "#72a9fe",
		"couleur_claire" => "#b4d1fd",
		"couleur_lien" => "#9F5B81",
		"couleur_lien_off" => "#39212E"
		),
//  Gris
5 => array (
		"couleur_foncee" => "#6ec8c3",
		"couleur_claire" => "#c1fcf9",
		"couleur_lien" => "#6C629F",
		"couleur_lien_off" => "#272339"
		),
//  Gris
6 => array (
		"couleur_foncee" => "#72d08a",
		"couleur_claire" => "#c1fed1",
		"couleur_lien" => "#68899F",
		"couleur_lien_off" => "#253139"
		),
//  Gris
7 => array (
		"couleur_foncee" => "#a2c85d",
		"couleur_claire" => "#e6fdbc",
		"couleur_lien" => "#689F97",
		"couleur_lien_off" => "#253936"
		),
//  Bleu truquoise
8 => array (
		"couleur_foncee" => "#c2c17b",
		"couleur_claire" => "#fffda2",
		"couleur_lien" => "#9F765E",
		"couleur_lien_off" => "#392A22"
		)
);

	if (is_numeric($choix)) {
		// Compatibilite ascendante (plug-ins notamment)
		$GLOBALS["couleur_claire"] = $couleurs_spip[$choix]['couleur_claire'];
		$GLOBALS["couleur_foncee"] = $couleurs_spip[$choix]['couleur_foncee'];
		$GLOBALS["couleur_lien"] = $couleurs_spip[$choix]['couleur_lien'];
		$GLOBALS["couleur_lien_off"] = $couleurs_spip[$choix]['couleur_lien_off'];
		
	  return
	    "couleur_claire=" .
	    substr($couleurs_spip[$choix]['couleur_claire'],1).
	    '&couleur_foncee=' .
	    substr($couleurs_spip[$choix]['couleur_foncee'],1);
	} else {
		if (is_array($choix)) return $couleurs_spip = $choix;

		$evt = '
onmouseover="changestyle(\'bandeauinterface\');"
onfocus="changestyle(\'bandeauinterface\');"
onblur="changestyle(\'bandeauinterface\');"';

		$bloc = '';
		$ret = self('&');
		foreach ($couleurs_spip as $key => $val) {
			$bloc .=
			'<a href="'
			  . generer_action_auteur('preferer',"couleur:$key",$ret)
				. '"'
			. ' rel="'.generer_url_public('style_prive','ltr='
				. $GLOBALS['spip_lang_left'] . '&'
				. inc_couleurs_dist($key)).'"'
			  . $evt
			.'>'
			. http_img_pack("rien.gif",
					_T('choix_couleur_interface') . $key,
					"width='8' height='8' style='margin: 1px; background-color: "	. $val['couleur_claire'] . ";'")
			. "</a>";
		}

		// Ce js permet de changer de couleur sans recharger la page

		return  '<span id="selecteur_couleur">'
		.  $bloc
		. "</span>\n"
		. '<script type="text/javascript"><!--' . "
			$('#selecteur_couleur a')
			.click(function(){
				$('head>link#cssprivee')
				.clone()
				.removeAttr('id')
				.attr('href', $(this).attr('rel'))
				.appendTo($('head'));

				$.get($(this).attr('href'));
				return false;
			});
		// --></script>\n";


	}
}

?>
