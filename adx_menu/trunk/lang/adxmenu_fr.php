<?php
$GLOBALS[$GLOBALS['idx_lang']] = array(
	'adxmenu' => 'ADX Menu',
	'hbt' => 'Ouverture verticale de bas en haut',
	'htb' => 'Ouverture verticale de haut en bas (d&eacute;faut)',
	'vlr' => 'Ouverture horizontale de gauche &agrave; droite',
	'vrl' => 'Ouverture horizontale de droite &agrave; gauche',

	// CFG
	'page_de_configuration' => 'Page de configuration',
	'titre_descr_cfg' => 'Configuration du plugin ADX Menu',
	'descr_cfg' => 'Le plugin ajoute la balise {{<code>#ADXMENU</code>}} et le mod&egrave;le \'{{adxmenu.html}}\' pour vos squelettes, qui fait appara&icirc;tre un menu &eacute;l&eacute;gant type \'{fly-out}\'.
	

Le plugin est bas&eacute; sur le script \'ADXmenu\', version 4.21, de [Aleksandar Vacic->http://aplus.rs/adxmenu/].',
	'legend_cfg' => 'S&eacute;lectionnez ci-dessous les options choisies',
	'cfgform_titre_adx' => 'Sens de d&eacute;ploiement du menu (au survol de la souris)',
	'cfgform_titre_liste_rubriques' => 'Liste des rubriques &agrave; inclure dans le menu',
	'cfgform_comment_liste_rubriques' => 'Vous pouvez ici indiquer :

-* \'secteurs\' pour pr&eacute;senter tous vos secteurs en t&ecirc;te de menu ({valeur par d&eacute;faut}),
-* \'tout\' pour pr&eacute;senter toutes vos rubriques en t&ecirc;te de menu,
-* une liste d\'ID de rubriques, s&eacute;par&eacute;s par deux-points (ex "1:2").

Pour exclure une ou plusieurs rubriques, listez les identifiants &agrave; exclure, s&eacute;par&eacute;s par deux-points et pr&eacute;c&eacute;d&eacute;s par un point d\'exclamation ("tout!3:5").',
	'bouton_reset' => 'R&eacute;initialiser',
	'enregistrer_les_modifications' => 'Enregsitrer les modifications',
	'effacer_les_modifications' => 'Effacer les modifications',
	'effacer_config_courante' => 'Effacer votre configuration',
	'bouton_reset' => 'R&#233;initialiser',
	'bouton_effacer' => 'Effacer',

	// Documentation
	'doc_titre_court' => 'Documentation ADX Menu',	
	'doc_titre_page' => 'Page de documentation du plugin ADX Menu',	
	'doc_chapo' => 'Le plugin ADX Menu pour SPIP 2.0 ({et plus}) est une adaptation du menu CSS/JavaScript "ADXmenu" de <u>Aleksandar Vacic</u> ({[plus d\'infos->#bloc_licence]}) ; il permet de mettre en place un menu ouvrant horizontalement ou verticalement en CSS ({et Javascript pour IE<7}).',
	'exemple' => '{{{Exemple}}}

Le menu ci-dessous vous pr&eacute;sente un exemple en utilisant les 3 premiers secteurs de votre site SPIP.',
	'pas_cfg_installe' => 'Le plugin CFG ne semble pas install&eacute; sur votre site.',
	'documentation' => '{{{Utilisation & configuration}}}

Le plugin peut s\'utiliser de deux fa&ccedil;ons d&eacute;taill&eacute;es ci-dessous. Il est pr&eacute;vu pour proposer une page de configuration gr&acirc;ce au plugin {{[CFG : moteur de configuration->http://www.spip-contrib.net/?rubrique575]}} mais celui-ci n\'est pas obligatoire[[Cela ne concerne que les versions de SPIP inférieures à 3.0 ; au-delà, la configuration est proposée en interne.]].

La page de configuration permet de d&eacute;finir notamment le sens d\'ouverture du menu ; celui-ci peut &ecirc;tre {{horizontal}}, de {{bas en haut}} ou de {{haut en bas}}, mais aussi {{vertical}}, de {{gauche &agrave; droite}} ou de {{droite &agrave; gauche}}[[Cette option n\'est accessible que si le plugin {{CFG}} est install&eacute; sur votre site. &Agrave; d&eacute;faut, vous pouvez &eacute;diter directement le fichier "adxmenu_options.php" &agrave; la racine du plugin.]].

@bloc_cfg@

{{{La balise &#035;ADXMENU}}}

Cette balise s\'utilise seule par d&eacute;faut, mais peut prendre les trois arguments suivants :
- {{liste des rubriques}} ({par d&eacute;faut tous les secteurs}) :
<br />Liste des ID de rubriques &agrave; inclure dans le menu, s&eacute;par&eacute;s par deux-points;
- {{longueur avant de couper les titres}} ({par d&eacute;faut 30}) :
<br />Nombre de caract&egrave;res au-del&agrave; desquels les titres seront tronqu&eacute;s;
- {{caract&egrave;re(s) de coupe}} ({par d&eacute;faut "."}) 
:<br />Le ou les caract&egrave;res qui seront indiqu&eacute;s pour un titre tronqu&eacute;.

Exemple pour un menu contenant les rubriques 1, 3 et 12, des titres coup&eacute;s &agrave; 50 caract&egrave;res et auxquels on ajoute "..." :

<cadre class=\'spip\'>
&#035;ADXMENU{1:3:12,50,...}
</cadre>

{{{La classe "adxm admenu"}}}

L\'effet menu ouvrant s\'applique en ajoutant simplement la classe "adxm adxmenu" &agrave; n\'importe quel menu ({liste imbriqu&eacute;e de &lt;ul&gt;&lt;li&gt;})[[Cette m&eacute;thode permet notamment d\'utiliser le plugin sur des menus d&eacute;finis gr&acirc;ce au plugin {{[Menus->http://www.spip-contrib.net/Plugin-Menus]}}, en ajoutant au menu concern&eacute; la classe "adxm adxmenu".]].

Exemple :

<cadre class=\'spip\'>
<ul classe="adxm adxmenu"> <li>un item de menu</li> </ul>
</cadre>

{{{Personnalisation}}}

Les styles CSS des diff&eacute;rents liens ou items du menu sont personnalisables dans le fichier CSS pr&eacute;sent &agrave; la racine du plugin :
- {{"adxmenu_css_styles.css.html"}}

A noter &eacute;galement, si vous avez besoin de modifier l\'ensemble de l\'apparence du menu, que les styles d&eacute;finissant la disposition des blocs, leur apparence au passage de la souris et toute la m&eacute;canique du menu sont modifiables dans les fichiers CSS ({complexes}) :
- {{"adxmenu_css.css.html"}}
- {{"adxmenu_css_ie.css.html"}}

Il est conseill&eacute; de bien tester vos personnalisations pour v&eacute;rifier que le menu est toujours pr&eacute;sentable (!). &Agrave; noter ici qu\'il est possible de coloriser la zone de s&eacute;curit&eacute; des items du menu en d&eacute;commentant la ligne 91 du fichier "adxmenu_css.css.html".

{{{Compatibilit&eacute;}}}

Les CSS utilis&eacute;s pour g&eacute;n&eacute;rer le menu sont compatibles avec la distribution ({bien-s&ucirc;r}) mais &eacute;galement avec les squelettes Z compatibles. Vous pouvez notamment, si vous utilisez un squelette [Zpip->http://www.spip-contrib.net/Zpip], pr&eacute;ciser la classe "adxm adxmenu" au menu g&eacute;n&eacute;ral utilis&eacute; par le squelette, l\'effet ouvrant s\'ajoutera automatiquement, en accord avec le sens d\'ouverture de vos r&eacute;glages.

{{{Conditions d\'utilisation}}}

La version originale du script du menu est propos&eacute;e par son auteur sous licence [Creative Commons Attribution->http://creativecommons.org/licenses/by/3.0/]. Il est donc demand&eacute; d\'indiquer sur la page de cr&eacute;dits de votre site l\'information :

<quote>&#171; Ce site web utilise [ADxMenu->http://aplus.rs/adxmenu/], par studio.aplus. &#187;</quote>

Pour plus d\'informations, reportez-vous &agrave; la page d&eacute;di&eacute;e par l\'auteur : [->http://aplus.rs/adxmenu/buy/].',

	'doc_en_ligne' => 'Documentation',
	'page_test' => 'Page de test (locale)',
	'page_test_in_new_window' => 'Page de test en nouvelle fen&#234;tre',
	'titre_original' => 'ADX Menu, plugin pour SPIP 2.0+',
	'licence_originale' => 'Script CSS/javascript original : {{"ADXmenu" version 4.21 de [Aleksandar Vacic->http://aplus.rs/] distribu&eacute; sous licence {[Creative Commons BY 3.0 Attribution->http://creativecommons.org/licenses/by/3.0/]} }}.',
	'licence_actuelle' => 'Plugin pour SPIP 2.0+ : {{"ADX Menu" - copyright &#169; 2009 [Piero Wbmstr->http://www.spip-contrib.net/PieroWbmstr] sous la licence originale ({[Creative Commons BY 3.0 Attribution->http://creativecommons.org/licenses/by/3.0/]}) }}.',

	// Infos squelette de doc
	'docskel_sep' => '----',
	'info_doc' => 'Si vous rencontrez des probl&#232;mes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l&#039;affichage de cette page',
	'info_skel_doc' => 'Cette page de documentation est con&#231;ue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du r&#233;pertoire &#034;squelettes-dist/&#034;}). Si vous ne parvenez pas &#224; visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de g&#233;rer son affichage :

-* [Mode &#034;texte simple&#034;->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode &#034;squelette Zpip&#034;->@mode_zpip@] ({squelette Z compatible})
-* [Mode &#034;squelette SPIP&#034;->@mode_spip@] ({compatible distribution})',
	'info_skel_contrib' => 'Page de documentation compl&egrave;te en ligne sur spip-contrib : [->http://www.spip-contrib.fr/?article3566].',
	'new_window' => 'Nouvelle fenêtre',
);
?>