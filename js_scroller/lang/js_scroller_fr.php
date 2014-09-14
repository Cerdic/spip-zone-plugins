<?php
/**
 * @name 		Langue france
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @copyright 	CreaDesign 2009 {@link http://creadesignweb.free.fr/}
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	1.0 (10/2009)
 * @package		Javascript_Scroller
 */
$GLOBALS[$GLOBALS['idx_lang']] = array(
	// Titres du rendu en pages publiques
// -------------   => TITRES PAR DEFAUT DU BANDEAU -----------------------

	'titre_bandeau_articles' => 'Les @counter@ derniers articles parus&nbsp;&nbsp;:',
	'titre_bandeau_breves' => 'Les @counter@ derni&egrave;res br&egrave;ves parues&nbsp;&nbsp;:',
	'titre_bandeau_sites' => 'Les @counter@ derniers sites r&eacute;f&eacute;renc&eacute;s&nbsp;&nbsp;:',
	'titre_bandeau_rubriques' => 'Rubriques propos&eacute;es&nbsp;&nbsp;:',
	'titre_bandeau_auteurs' => 'Les @counter@ derniers auteurs inscrits&nbsp;&nbsp;:',
	'titre_bandeau_mots' => 'Les @counter@ derniers mots-cl&eacute;s&nbsp;&nbsp;:',
	'titre_bandeau_documents' => ' ', // pas de titre
	'title_link' => 'Lire la suite', // attribut 'title' des liens du bandeau

// -------------   => FIN TITRES PAR DEFAUT -----------------------
	// General
	'js_scroller' => 'Javascript Scroller',
	'titre_original' => 'Javascript Scroller, plugin pour SPIP',
	'licence_actuelle' => 'Copyright &#169; 2009 [Piero Wbmstr->http://contrib.spip.net/PieroWbmstr] distribu&eacute; sous licence [GNU GPL v3->http://www.opensource.org/licenses/gpl-3.0.html].',
	'licence_originale' => 'Le code javascript est tir&#233; de [->http://javascripts.vbarsan.com/] (licence libre).',

	// Documentation
	'doc_titre_court' => 'Documentation Javascript Scroller',
	'doc_titre_page' => 'Documentation du plugin "Javascript Scroller"',
	'doc_chapo' => 'Le plugin "Javascript Scroller" propose un widget en javascript permettant d\'afficher un bandeau d&eacute;filant ({comme ci-dessous}) pr&eacute;sentant une liste d\'&eacute;l&eacute;ments de votre site SPIP.',
	'documentation' => 'Chaque entr&eacute;e de la liste, selon le type d\'&eacute;l&eacute;ment choisi, pr&eacute;sente le titre de l\'&eacute;l&eacute;ment en question, un lien vers sa page ainsi que sa pr&eacute;sentation.

Ce widget s\'inclue dans vos squelettes en utilisant la balise suivante :

<cadre class=\'spip\'>
&#035;JS_SCROLLER{ width , height , type , maximum , coupe , direction , titre }
</cadre>

dont toutes les options sont facultatives ({une valeur vide[[Pour rappel, chez SPIP une valeur vide dans une balise s\'&eacute;crit : \'\'.]] vaudra la valeur par d&eacute;faut}) et correspondent &agrave; :
- {{width et height :}} les dimensions du bandeau ({par d&#233;faut 600 x 20 pixels, hauteur de 100 pixels pour les documents}),
- {{type :}} le type d\'&#233;l&#233;ments SPIP pr&#233;sent&#233; : {{\'articles\', \'breves\', \'sites\', \'rubriques\' ou \'documents\'}} ({par d&#233;faut les articles}),
- {{maximum :}} le nombre d\'entr&#233;es pr&#233;sent&#233;es ({par d&#233;faut 50}),
- {{coupe :}} le nombre de caract&egrave;res du texte pr&#233;sent&#233; pour chaque entr&eacute;e ({par d&#233;faut 40}),
- {{direction :}} la direction du texte sous forme de code \'{{ltr}}\' ou \'{{rtl}}\' ({par d&#233;faut \'ltr\' : de gauche &agrave; droite}),
- {{titre :}} le titre du bandeau ; sa valeur par d&eacute;faut est \'{{defaut}}\', le titre ajout&eacute; sera alors construit depuis les cha&icirc;nes de langues du plugin ({cha&icirc;ne du type "Les 20 derniers articles parus : "}) ; si vous ne voulez pas de titre, indiquez ici \'{{non}}\' ; vous pouvez &eacute;galement pr&eacute;ciser une cha&icirc;ne qui sera utilis&eacute;e comme titre[[Pour le titre, si vous pr&eacute;cisez une cha&icirc;ne de caract&egrave;res ({un titre personnel}), celle-ci sera pass&eacute;e si c\'est possible par la fonction de traduction de SPIP. Si vous souhaitez utiliser par exemple "<:mon_plugin:ma_chaine:>", indiquez simplement "mon_plugin:ma_chaine".]].

{{{Personnalisation}}}

{{Styles CSS du bandeau}}

Les styles du bandeau sont d&eacute;finits dans le fichier "{{js_scroller.css}}" &agrave; la racine du plugin. Vous pouvez le modifier pour styler le bandeau selon vos besoins.

{{Vos propres boucles}}

Le plugin charge l\'un des fichiers XML de son r&eacute;pertoire "xml/". Vous pouvez y ajouter votre propre boucle XML en cr&eacute;ant {{un nouveau fichier dont le nom doit commencer par "{scroller_items_}" suivi de l\'argument que vous passerez dans l\'attribut "type" de la balise}}.

Vos boucles doivent d&eacute;finir un "{{item}}" par objet comprenant les entr&eacute;es XML suivantes :
- "{{titre}}" : le titre de l\'entr&eacute;e ({obligatoire}),
- "{{lien}}" : l\'URL du lien cr&eacute;&eacute; sur ce titre ({obligatoire}),
- "{{description}}" : le texte de description ajout&eacute; apr&egrave;s le titre,
- "{{url_doc}}" : l\'URL du document ({cas des images - doit &ecirc;tre relative}),
- "{{width}}" et "{{height}}" : la taille du document ({cas des images}),
- "{{typedoc}}" : le type MIME du document ({cas des images}).

Il est conseill&eacute; d\'indiquer des URLs relatives.

Vous pouvez tester vos diff&eacute;rentes options en &eacute;ditant le fichier \'{{contenu/doc_js_scroller.html}}\' dans le r&eacute;pertoire du plugin ({squelette de la pr&eacute;sente page}) puis en recalculant cette page.

{{{Documentation & Suivi des bugs}}}

Une documentation compl&egrave;te et un forum sont disponibles en ligne : [->http://contrib.spip.net/?article3570].',

	// Infos squelette de documentation
	'docskel_sep' => '----',
	'info_doc' => 'Si vous rencontrez des probl&#232;mes pour afficher cette page, [cliquez-ici->@link@].',
	'info_doc_titre' => 'Note concernant l&#039;affichage de cette page',
	'info_skel_doc' => 'Cette page de documentation est con&#231;ue sous forme de squelette SPIP fonctionnant avec la distribution standard ({fichiers du r&#233;pertoire &#034;squelettes-dist/&#034;}). Si vous ne parvenez pas &#224; visualiser la page, ou que votre site utilise ses propres squelettes, les liens ci-dessous vous permettent de g&#233;rer son affichage :

-* [Mode &#034;texte simple&#034;->@mode_brut@] ({html simple + balise INSERT_HEAD})
-* [Mode &#034;squelette Zpip&#034;->@mode_zpip@] ({squelette Z compatible})
-* [Mode &#034;squelette SPIP&#034;->@mode_spip@] ({compatible distribution})',
	'info_skel_contrib' => 'Page de documentation compl&egrave;te en ligne sur spip-contrib : [->http://contrib.spip.net/?article3570].',
);
?>