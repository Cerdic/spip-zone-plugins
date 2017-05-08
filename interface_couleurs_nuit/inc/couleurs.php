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

// https://code.spip.net/@inc_couleurs_dist
function inc_couleurs_dist($choix=NULL)
{
	static $couleurs_spip = array(
// Vert de gris
1 => array (
		"couleur_foncee" => "#6d8a50",
		"couleur_claire" => "#7e9e5c",
		"couleur_lien" => "#649F5D",
		"couleur_lien_off" => "#243921"
		),
// Rose vieux
2 => array (
		"couleur_foncee" => "#c3902f",
		"couleur_claire" => "#dfa535",
		"couleur_lien" => "#8E9F5C",
		"couleur_lien_off" => "#333921"
		),
// Orange
3 => array (
		"couleur_foncee" => "#ce6f35",
		"couleur_claire" => "#e37a39",
		"couleur_lien" => "#9F8E62",
		"couleur_lien_off" => "#393323"
		),
//  Bleu truquoise
4 => array (
		"couleur_foncee" => "#b6382a",
		"couleur_claire" => "#c74a3c",
		"couleur_lien" => "#9F765E",
		"couleur_lien_off" => "#392A22"
		),
// Violet
5 => array (
		"couleur_foncee" => "#9a586c",
		"couleur_claire" => "#b3677e",
		"couleur_lien" => "#9F5B81",
		"couleur_lien_off" => "#39212E"
		),
//  Gris
6 => array (
		"couleur_foncee" => "#6c5d7e",
		"couleur_claire" => "#8a799e",
		"couleur_lien" => "#6C629F",
		"couleur_lien_off" => "#272339"
		),
//  Gris
7 => array (
		"couleur_foncee" => "#4a5a71",
		"couleur_claire" => "#6d7f99",
		"couleur_lien" => "#68899F",
		"couleur_lien_off" => "#253139"
		),
//  Gris
8 => array (
		"couleur_foncee" => "#266ca3",
		"couleur_claire" => "#4481b0",
		"couleur_lien" => "#689F97",
		"couleur_lien_off" => "#253936"
		),
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
