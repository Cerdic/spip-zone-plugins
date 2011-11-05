<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(

// Description CS
	'nom' => 'Balise &#035;POPUP',
	'description' => 'Gestion d\'une fen&ecirc;tre popup ({fen&ecirc;tre externe}) unique sous forme de squelette SPIP et aux dimensions r&eacute;glables pour diff&eacute;rents usages. Pensez &agrave; ne pas abuser de ces liens ouvrants, par respect pour les internautes.

@puce@ {{Utilisation dans les squelettes}}

En activant cet outil, vous pourrez utiliser la balise {{&#035;POPUP}} comme ceci :
<quote><code>
<a href="#POPUP{
	objet SPIP,
	squelette de la popup (optionnel),
	largeur (en pixels - optionnel),
	hauteur (en pixels - optionnel)
}">texte cliquable</a>
</code></quote>

L\'objest SPIP est d&eacute;sign&eacute; sous la forme suivante : {{article1}}, {{breve1}}, {{id_article=1}}, etc.

La balise renvoie le lien n&eacute;cessaire pour ouvrir la popup, sous forme de javascript :

<quote><code>
javascript:_popup_set(\'URL\',width,height);
</code></quote>

@puce@ {{Utilisation dans les contenus SPIP (articles, br&egrave;ves, etc.)}}

Les deux mod&egrave;les disponibles {{popup}} et {{popup_img}} sont utilisables de cette façon :

<quote><code>
<popup
	|texte=texte cliquable ou URL
	|lien=objet SPIP
	|skel=squelette (option)
	|width=XX (option)
	|height=XX (option)
	|titre=mon titre (option)
>
<popup_img
	|doc=numero de l\'image cliquable
	|lien=objet SPIP
	|skel=squelette (option)
	|width=XX (option)
	|height=XX (option)
	|titre=mon titre (option)
>
</code></quote>

@puce@ {{R&eacute;glages utilis&eacute;s pour fabriquer la fen&ecirc;tre}}

[[%popup_width% x %popup_height% pixels.]][[%popup_skel%]][[%popup_titre%]]',

	'width_label' => 'Dimensions par d&eacute;faut (L x H) :',
	'skel_label' => 'Squelette par d&eacute;faut  :',
	'titre_label' => 'Nom JavaScript :',

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