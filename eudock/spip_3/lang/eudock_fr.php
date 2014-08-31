<?php

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// Original
	'titre_original' => 'EuDock, plugin pour SPIP 2.0+',
	'licence_originale' => 'Script javascript original "{{EuDock}}" version 2.0 - {{copyright &#169; 2006 [Parodi (Pier...) Eugenio->http://eudock.jules.it] distribu&eacute; sous licence [LGPL->http://eudock.jules.it/LICENSE.txt]}} ({cf. fichier "LICENSE.txt" dans le r&eacute;pertoire du plugin}).',
	'licence_actuelle' => 'Plugin pour SPIP 2.0+ : {{"EuDock" - copyright &#169; 2009 [Piero Wbmstr->http://contrib.spip.net/PieroWbmstr] sous licence &eacute;tendue [GPL->http://www.opensource.org/licenses/gpl-3.0.html] }}.',
	// Documentation
	'doc_titre_page' => 'Page de documentation du plugin EuDock',	
	'doc_titre_court' => 'Documentation EuDock',	
	'doc_chapo' => 'Le plugin EuDock pour SPIP 2.0 ({et plus}) est une adaptation de l\'applet JavaScript "EuDock" de <u>Parodi (Pier...) Eugenio</u> ({[plus d\'infos->#bloc_licence]}) ; il permet de g&eacute;n&eacute;rer une pr&eacute;sentation d\'images avec un effet loupe au passage de la souris, sur le mod&egrave;le notamment du {Dock} propos&eacute; par Apple.',
	'exemple' => '{{{Exemple}}}

Le bloc ci-dessous vous pr&eacute;sente un exemple en utilisant les logos des articles ou rubriques de votre site ({cela n&eacute;cessite que des logos soient pr&eacute;sents})[[Vous pouvez personnaliser ce test en &eacute;ditant le fichier "{{eudock_documentation.html}}" &agrave; la racine du plugin en bas de page ({diff&eacute;rents sets d\'exemple sont propos&eacute;s}).]].

@lien_bordure@',
	'voir_logos_rubrique' => 'Voir les logos de rubriques',
	'voir_logos_articles' => 'Voir les logos d\'articles',
	'voir_documents_articles' => 'Voir les documents d\'articles',
	'limiter_4_images' => 'Limiter le nombre d\'images &agrave; 4',
	'image_si_vide' => 'Image de remplacement si logo absent',
	'cacher_bordure' => 'Cacher la bordure du bloc',
	'montrer_bordure' => 'Montrer la bordure du bloc',
	'exemple_squelette' => 'Squelette de cette page',
	'documentation' => '{{{Utilisation du mod&egrave;le}}}

Le dock s\'inclus dans la page en appelant le mod&egrave;le ci-dessous {{apr&egrave;s le tag &lt;/html&gt; de la page}} ({c\'est &agrave; dire tout en bas du fichier HTML}).

<cadre class=\'spip\'>
[(&#035;MODELE{noisette_euDock} ... options ... })]
</cadre>

Il n&eacute;cessite pour fonctionner que vos squelettes d&eacute;finissent, {{en dehors de l\'inclusion du mod&egrave;le}}, un objet du DOM portant l\'ID pr&eacute;cis&eacute; dans la valeur de l\'option "div_id", qui vaut "eudock" par d&eacute;faut[[Vous pouvez d\'ailleurs d&eacute;finir des styles CSS pour le bloc concern&eacute; dans vos personnalisations, soit par d&eacute;faut "#eudock".]].

<cadre class=\'spip\'>
<div id="eudock"> </div>
</cadre>

Voici la liste des principales options du mod&egrave;le ({les options de s&eacute;lection des objets sont d&eacute;velopp&eacute;es dans le paragraphe suivant}) :
-* "{{div_id=...}}" ({valeur par d&eacute;faut "eudock"}) : le DOM-ID du bloc o&ugrave; sera pr&eacute;sent&eacute; le dock ({<u>le bloc doit &ecirc;tre inclus dans vos squelettes</u> - cf. cadre ci-dessus})
-* "{{max_image=...}}" ({10 par d&eacute;faut}) : le nombre maximum d\'images affich&eacute;es
-* "{{style=...}}" ({pas de valeur par d&eacute;faut}) : les styles CSS qui seront appliqu&eacute;s aux titres des images
-* "{{offset=...}}" ({par d&eacute;faut 0}) : une valeur de d&eacute;centrage qui sera appliqu&eacute; au dock[[La valeur de l\'{{offset}} peut permettre de r&eacute;gler certains probl&egrave;mes d\'affichages des images, notamment lors d\'h&eacute;ritage de styles entre blocs.]]
-* "{{image_si_vide= oui / non}}" ({non par d&eacute;faut}) : doit-on pr&eacute;senter une image de remplacement si l\'objet ne poss&egrave;de pas de logo
-* "{{image_defaut=...}}" ({par d&eacute;faut : "img/ecureuil_transparent.png" - l\'&eacute;cureuil SPIP}) : chemin vers l\'image qui sera utilis&eacute;e pour les fichiers absents si l\'option ci-dessus est activ&eacute;e
-* "{{alpha}}" ({par d&eacute;faut 40}) : la couche alpha qui sera appliqu&eacute;e sur les vignettes
-* "{{taille}}" ({par d&eacute;faut 200x200px maximum}) : la taille de retaille des images en version large (lorsque la souris est dessus)
-* "{{taille_vignette}}" ({par d&eacute;faut 100x100px maximum}) : la taille de retaille des vignettes (images &agrave; l\'affichage).

{{{S&eacute;lection des images pr&eacute;sent&eacute;es}}}

Le plugin peut g&eacute;n&eacute;rer un aper&ccedil;u de tous les logos de SPIP ainsi que de tous les documents portant une extension image ({png, gif ou jpg}). La m&eacute;canique de s&eacute;lection peut para&icirc;tre complexe mais elle est en fait \'SPIP-intuitive\' ... Voici son fonctionnement :

{{L\'option "type_objet"}}

C\'est cette option qui choisie le type d\'objets qui sera affich&eacute;. Elle peut prendre les valeurs suivantes :
- \'{{logos_articles}}\' ({sa valeur par d&eacute;faut}),
- \'{{logos_rubriques}}\',
- \'{{logos_breves}}\',
- \'{{logos_sites}}\',
- \'{{logos_auteurs}}\',
- \'{{logos_mots}}\',
- \'{{documents_articles}}\',
- \'{{documents_rubriques}}\'.

Ces valeurs correspondent, intuitivement, aux logos des diff&eacute;rents objets &eacute;ditoriaux de SPIP ou aux documents des articles ou rubriques, &agrave; condition que ceux-ci soient des images ({typiquement les documents pr&eacute;sents dans le portfolio des objets concern&eacute;s}).

{{Le "top-level"}}

Par d&eacute;faut, le plugin bouclera sur toutes les rubriques depuis la racine de SPIP ({cela &eacute;quivaut &agrave; une boucle sur tous les secteurs}). Vous pouvez cependant, c\'est m&ecirc;me conseill&eacute; pour limiter les tours de boucle, sp&eacute;cifier une liste d\'identifiants pour chaque type d\'objet ...

Cette liste doit &ecirc;tre d&eacute;clar&eacute;e comme un tableau PHP dans SPIP, en utilisant {{la balise "&#035;ARRAY"}}. Pour rappel, cette balise n&eacute;cessite de d&eacute;finir {{les cl&eacute;s ET les valeurs}} du tableau : &#035;ARRAY&#123; cle1 , valeur1 , cle2 , valeur2 , ... &#125;. 

Pour boucler sur les rubriques 1, 2 et 5 par exemple, nous devons &eacute;crire :
<cadre class=\'spip\'>
&#035;ARRAY{0,1,1,2,3,5}

// équivalent PHP :

array( 0=>1, 1=>2, 3=>5 );

// option à passer au modèle \'noisette_euDock\' :

{rubriques=&#035;ARRAY{0,1,1,2,3,5}}
</cadre>

De la m&ecirc;me fa&ccedil;on, il est possible de passer au mod&egrave;le des listes d\'identifiants pour tous les objets SPIP ...

{{{Exemple de squelette}}}

Le lien ci-dessous vous montre le squelette de la page courante, pr&eacute;sentant tout en bas le bloc d\'inclusion du mod&egrave;le "noisette_euDock".

@lien_skel@

{{{Habillage}}}

Le script intial pr&eacute;sente le dock dans un bloc "habill&eacute;" dont les images sont personnalisables dans le r&eacute;pertoire \'barImages/\' du plugin.

@lien_habillage@',
	'exemple_site' => '{{{Exemple pouss&eacute;}}}

L\'exemple de <a href="@lien_gadgets@">cette page</a> pr&eacute;sente quelques gadgets du plugin : le logo du site et un petit gadget sp&eacute;cial SPIP ...
	',
	'montrer_bar' => 'Montrer l\'habillage',
	'cacher_bar' => 'Cacher l\'habillage',
	// Infos squelette de doc
	'sep' => '----',
	'info_doc' => 'Si vous rencontrez des probl&#232;mes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l&#039;affichage de cette page',
	'info_skel_doc' => 'Cette page de documentation est con&#231;ue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du r&#233;pertoire &#034;squelettes-dist/&#034;}). Si vous ne parvenez pas &#224; visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de g&#233;rer son affichage :

-* [Mode &#034;texte simple&#034;->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode &#034;squelette Zpip&#034;->@mode_zpip@] ({squelette Z compatible})
-* [Mode &#034;squelette SPIP&#034;->@mode_spip@] ({compatible distribution})',
	'info_skel_contrib' => 'Page de documentation compl&egrave;te en ligne sur contrib.spip : [->http://contrib.spip.net/?article3567].',
);
?>