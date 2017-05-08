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
		"couleur_foncee" => "#578A51",
		"couleur_claire" => "#74B86C",
		"couleur_lien" => "#649F5D",
		"couleur_lien_off" => "#243921"
		),
// Rose vieux
2 => array (
		"couleur_foncee" => "#7C8A50",
		"couleur_claire" => "#A5B86B",
		"couleur_lien" => "#8E9F5C",
		"couleur_lien_off" => "#333921"
		),
// Orange
3 => array (
		"couleur_foncee" => "#8A7B55",
		"couleur_claire" => "#B8A471",
		"couleur_lien" => "#9F8E62",
		"couleur_lien_off" => "#393323"
		),
//  Bleu truquoise
4 => array (
		"couleur_foncee" => "#8A705F",
		"couleur_claire" => "#B8957F",
		"couleur_lien" => "#9F765E",
		"couleur_lien_off" => "#392A22"
		),
// Violet
5 => array (
		"couleur_foncee" => "#875A78",
		"couleur_claire" => "#B87BA4",
		"couleur_lien" => "#9F5B81",
		"couleur_lien_off" => "#39212E"
		),
//  Gris
6 => array (
		"couleur_foncee" => "#67618A",
		"couleur_claire" => "#8981B8",
		"couleur_lien" => "#6C629F",
		"couleur_lien_off" => "#272339"
		),
//  Gris
7 => array (
		"couleur_foncee" => "#5A778A",
		"couleur_claire" => "#789FB8",
		"couleur_lien" => "#68899F",
		"couleur_lien_off" => "#253139"
		),
//  Gris
8 => array (
		"couleur_foncee" => "#5A8A83",
		"couleur_claire" => "#78B8AF",
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
