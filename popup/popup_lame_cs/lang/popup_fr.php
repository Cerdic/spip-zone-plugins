<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// Description CS
	'nom' => 'Balise &#035;POPUP',
	'description' => 'Gestion d\'une fen&ecirc;tre popup ({fen&ecirc;tre externe}) unique sous forme de squelette SPIP et aux dimensions r&eacute;glables pour diff&eacute;rents usages.

@puce@ {{Utilisation dans les squelettes :}}

En activant cet outil, vous pourrez utiliser la balise {{&#035;POPUP}} comme ceci :
<quote><code>
<a href="#POPUP{
	\'article1\' ou \'id_article=1\' (objet SPIP),
	squelette utilise’ (optionnel),
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

// Popup
	'popup_titre' => 'Bo&#238;te de dialogue',
	'btn_imprimer' => 'Imprimer',
	'btn_imprimer_ttl' => 'Imprimer cette page',
	'btn_fermer_fenetre' => 'Fermer',
	'btn_fermer_fenetre_ttl' => 'Fermer cette fen&#234;tre',
	'btn_haut_page' => 'Haut de page',
	'btn_haut_page_ttl' => 'Retour en haut de page',
	'nouvelle_fenetre' => '[Nouvelle fen&#234;tre]',
);
?>