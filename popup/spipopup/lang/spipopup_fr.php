<?php
/**
 * @name 		JavascriptPopup_lang_FR
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Description
	'nom' => 'Balise &#035;POPUP',

// CFG
	'description' => 'Gestion d\'une fen&ecirc;tre popup ({fen&ecirc;tre externe}) unique sous forme de squelette SPIP et aux dimensions r&eacute;glables pour diff&eacute;rents usages.

@puce@ {{Utilisation dans les squelettes :}}

En activant cet outil, vous pourrez utiliser la balise {{&#035;POPUP}} comme ceci :
<quote><code>
<a href="#POPUP{
	\'article1\' ou \'id_article=1\' (objet SPIP),
	squelette utiliseÕ (optionnel),
	width (en pixels - optionnel),
	height (en pixels - optionnel)
}">texte du lien</a>
</code></quote>
La balise renvoie le lien n&eacute;cessaire pour ouvrir la popup, sous forme de javascript :

<quote><code>
javascript:_popup_set(\'URL\',width,height);
</code></quote>

@puce@ {{R&eacute;glages par d&eacute;faut :}}

Choisissez ci-dessous le squelette SPIP par d&eacute;faut qui sera utilis&eacute; pour afficher le contenu de la fen&ecirc;tre.[[%popup_skel%]]

Choisissez ci-dessous le nom JavaScript qui sera donn&eacute; &agrave; la fen&ecirc;tre ({vous pourrez par la suite utiliser ce nom pour la d&eacute;signer}).[[%popup_titre%]]

Choisissez ci-dessous la largeur par d&eacute;faut de la fen&ecirc;tre, {{en pixels}}.[[%popup_width%]]

Choisissez ci-dessous la hauteur par d&eacute;faut de la fen&ecirc;tre, {{en pixels}}.[[%popup_height%]]',
	'skel_label' => 'Squelette de la popup :',
	'titre_label' => 'Nom de la fen&ecirc;tre :',
	'width_label' => 'Largeur de la fen&ecirc;tre :',
	'height_label' => 'Hauteur de la fen&ecirc;tre :',

	'titre_descr_cfg' => 'Configuration de l\'outil "Popup"',
	'descr_cfg' => 'Documentation du plugin pour plus d\'infos : [spip-contrib.net/?article3573->http://www.spip-contrib.net/?article3573]',
	'legend_cfg_balise' => 'Concernant la fen&ecirc;tre externe',
	'skel_defaut' => 'Squelette par d&eacute;faut utilis&eacute; pour afficher le contenu de la fen&ecirc;tre',
	'skel_defaut_comment' => 'Il s\'agit initialement du squelette "popup_defaut.html" pr&eacute;sent &agrave; la racine du plugin.',
	'width_and_height' => 'Taille de la fen&ecirc;te',
	'width' => 'Largeur (en pixels)',
	'height' => 'Hauteur (en pixels)',
	'titre_popup' => 'Nom JavaScript de la fen&ecirc;tre',
	'titre_popup_comment' => 'Vous pourrez par la suite utiliser ce nom pour la d&eacute;signer dans vos scripts ({"window.popup"}).',

// Popup
	'popup_titre' => 'Bo&#238;te de dialogue',
	'btn_imprimer' => 'Imprimer',
	'btn_imprimer_ttl' => 'Imprimer cette page',
	'btn_fermer_fenetre' => 'Fermer',
	'btn_fermer_fenetre_ttl' => 'Fermer cette fen&#234;tre',
	'btn_haut_page' => 'Haut de page',
	'btn_haut_page_ttl' => 'Retour en haut de page',
	'nouvelle_fenetre' => '[Nouvelle fen&#234;tre]',
	'retour_fenetre' => '[Retour en fen&#234;tre principale]',
);
?>