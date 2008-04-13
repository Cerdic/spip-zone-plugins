<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => ':&nbsp;no',
	'2pts_oui' => ':&nbsp;yes',

	// A
	'acces_admin' => 'Administrators\' access :',
	'actif' => 'Activated tool',
	'actifs' => 'Activated tools:',
	'activer' => 'Activate',
	'activer_outil' => 'Activate the tool',
	'alt_pack' => 'See the current configuration parameters',
	'auteurs:description' => 'This tool configures the appearance of [the authors\' page->./?exec=auteurs], in the private area.

@puce@ Define here the maximum number of authors to display in the central frame of the author\'s page. Beyond this number page numbering will be triggered.[[%max_auteurs_page%]]

@puce@ Which kinds of authors should be listed on the spages?
[[%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]',
	'auteurs:nom' => 'Authors page',
	'auteur_forum:description' => 'Request all authors of public messages to fill in (with at least one letter!) the field "@_CS_FORUM_NOM@" in order to avoid completely anonymous messages.',
	'auteur_forum:nom' => 'No anonymous forums',
	'a_jour' => 'Your version is up to date.',

	// B
	'balise_etoilee' => '{{N.B.}} : Check the use made in your templates of starred tags. This tool will not apply its treatment to the following tag(s): @bal@.',
	'basique' => 'Basic',
	'blocs:aide' => 'Folding blocks: <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias: <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) and <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Allows you to create blocks where clicking on the title makes the contents visible or invisible.

@puce@ {{In SPIP texts}}: authors can use the tags &lt;bloc&gt; (or &lt;invisible&gt;) and &lt;visible&gt; in this way: 

<quote><code>
<bloc>
 Clickable title
 
 The text which be shown/hidden, after two new lines.
 </bloc>
</code></quote>

@puce@ {{In templates}}: you can use the tags #BLOC_TITRE, #BLOC_DEBUT and #BLOC_FIN in this way: 
<quote><code> #BLOC_TITRE
 My title
 #BLOC_DEBUT
 My folding block
 #BLOC_FIN</code></quote>
',
	'blocs:nom' => 'Folding Blocks',
	'boites_privees:description' => 'All the boxes described below appear in the editing area.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{Swiss Knife updates}}: a box on this configuration page showing the last changes made to the code of the plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Articles in SPIP format}}: an extra folding box for your articles showing the source code used by their authors.
- {{Author stats}}: an extra box on [the authors\' page->./?exec=auteurs] showing the last 10 connected authors and unconfirmed registrations. Only administrators can view this information.',
	'boites_privees:nom' => 'Private boxes',

	// C
	'caches' => 'Hidden tools:',
	'categ:admin' => '1. Administration',
	'categ:divers' => '6. Miscellaneous',
	'categ:public' => '4. Public site',
	'categ:spip' => '5. Tags, filters, criteria',
	'categ:typo-corr' => '2. Text improvements',
	'categ:typo-racc' => '3. Typographical shortcuts',
	'certaines_couleurs' => 'Only the tags defined below @_CS_ASTER@:',
	'chatons:aide' => 'Smileys: @liste@',
	'chatons:description' => 'Replace <code>:name</code> with smiley images in the text.
_ This tool will replace the shortcuts by the images of the same name found in the directory plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Smileys',
	'class_spip:description1' => 'Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{SPIP shortcuts}}.

Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

SPIP normally uses the &lt;h3&gt; tag for subtitles. Here you can choose a different tag: [[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP normally uses &lt;i> for marking italics. But &lt;em> could also be used. You can choose: [[%racc_i1%]][[->%racc_i2%]]
N.B.: if you change the tag used for italics, style {{2.}} above will not be applied.

@puce@ {{SPIP styles}}. Up to version 1.92 of SPIP, typographical shortcuts produced HTML tags all marked with the class "spip". For exeample, <code><p class="spip"></code>. Here you can define the style of these tags to link them to your stylesheet. An empty box means that no particular style will be applied.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Tags &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; and the lists (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Tags &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; and &lt;blockquote&gt; :[[%style_h%]]

N.B.: by changing the second parameter you will lose any standard styles associated with these tags.</blockquote>',
	'class_spip:nom' => 'SPIP and its shortcuts...',
	'cliquezlesoutils' => 'Click on the names of the tools above in order to show their description.',
	'code_css' => 'CSS',
	'code_fonctions' => 'Functions',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Options',
	'contrib' => 'More information: [->http://www.spip-contrib.net/?article@id@]',
	'couleurs:aide' => 'Text colouring: <b>[coul]text[/coul]</b>@fond@ with <b>coul</b> = @liste@',
	'couleurs:description' => 'Provide shortcuts to add colours in any text of the site (articles, news items, titles, forums, ...)

Here are two identical examples to change the colour of text:@_CS_EXEMPLE_COULEURS2@

In the same way, to change the font if the following option allows:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@The format of this personalised tags have to be of existing colours or define pairs &laquo;tag=colour&raquo;, separated by comas. Examples : &laquo;grey, red&raquo;, &laquo;smooth=yellow, strong=red&raquo;, &laquo;low=#99CC11, high=brown&raquo; but also &laquo;grey=#DDDDCC, red=#EE3300&raquo;. For the first and last example, the allowed tags are: <code>[grey]</code> et <code>[red]</code> (<code>[fond grey]</code> et <code>[fond red]</code> if the backgrounds are allowed).',
	'couleurs:nom' => 'Coloured text',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',
	'cs_rss' => 'Swiss Knife updates',

	// D
	'decoration:aide' => 'D&eacute;coration: <b>&lt;tag&gt;test&lt;/tag&gt;</b>, with<b>tag</b> = @liste@',
	'decoration:description' => 'New, configurable styles in your text using angle brackets and tags. Example: 
&lt;mytag&gt;texte&lt;/mytag&gt; ou : &lt;mytag/&gt;.<br />Define below the CSS styles you need. Put each tag on a separate lign, using the following syntaxes:
- {type.mytag = mon style CSS}
- {type.mytag.class = ma classe CSS}
- {type.mytag.lang = ma langue (ex : en)}
- {unalias = mytag}

The parameter {type} above can be one of three values:
- {span} : inline tag 
- {div} : block element tag
- {auto} : tag chosen automtically by the plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Decoration',
	'decoupe:aide' => 'Tabbed block: <b>&lt;onglets>&lt;/onglets></b><br/>Page or tab separator: @sep@',
	'decoupe:aide2' => 'Alias:&nbsp;@sep@',
	'decoupe:description' => 'Divides the display of an article using automatic page numbering. Simply place four consecutive + signes (<code>++++</code>) where you wish a page break to occur.
_ If you use this separator between  &lt;onglets&gt; and &lt;/onglets&gt; tags, then you will receive a tabbed page instead.
_ In templates you can use the tags #ONGLETS_DEBUT, #ONGLETS_TITRE and #ONGLETS_FIN.
_ This tool may be combined with {A summary for your articles}.',
	'decoupe:nom' => 'Division in pages and tabs',
	'desactiver' => 'Deactivate',
	'desactiver_flash:description' => 'Deletes the flash objects from your site and replaces them by the associated alternative content.',
	'desactiver_flash:nom' => 'Deactivate flash objects',
	'desactiver_outil' => 'Deactivate this tool',
	'desactiver_rss' => 'Deactivate &laquo;Swiss Knife updates&raquo;',
	'descrip_pack' => 'Your "Current configuration pack" brings together all the parameters activated for the Swiss Knife plugin. It remembers both whether a tool is activated or not and, if so, what options have been chosen.

This PHP code may be placed in the /config/mes_options.php file. It will place a reset link on the page of the "pack {Current Pack}". Of course, you can change its name below.

If you reset the plugin by clicking on a pack, the Swiss Knife will reconfigure itself according to the values defined in that pack.',
	'detail_fichiers' => 'Files:',
	'detail_inline' => 'Inline code:',
	'detail_pipelines' => 'Pipelines:',
	'detail_traitements' => 'Treatment:',
	'distant' => 'New version: [@version@->http://files.spip.org/spip-zone/couteau_suisse.zip]',
	'dossier_squelettes:description' => 'Changes which template directory to use. For example: "squelettes/mytemplate". You can register several directories by separating them with a colon <html>":"</html>. If you leave the following box empty (or type "dist" in it), then the default "dist" template, supplied with SPIP, will be used.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Template directory',
	'du_pack' => ' of the pack @pack@',

	// E
	'edition' => 'RSS feed updated:',
	'effaces' => 'Deleted',
	'en_travaux:description' => 'Makes it possible to display a customised message on the public site during maintenance work.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site in maintenance mode',
	'erreur:description' => 'missing id in the tool\'s definition!',
	'erreur:distant' => 'The distant server',
	'erreur:js' => 'A Javascript error appears to have occurred on this page, hindering its action. Please activate Javascript in your browser, or try deactivating some SPIP plugins which may be causing interference.',
	'erreur:nojs' => 'Javascript has been deactivated on this page.',
	'erreur:nom' => 'Error!',
	'erreur:probleme' => 'Problem with: @pb@',
	'erreur:traitements' => 'The Swiss Knife - Compilation error: forbidden mixing of \'typo\' and \'propre\'!',
	'erreur:version' => 'This tool is unavailable in this version of SPIP.',
	'etendu' => 'Expanded',

	// F
	'filets_sep:aide' => 'Dividing lines: <b>__i__</b> or <b>i</b> is a number.<br />Other available lines: @liste@',
	'filets_sep:description' => 'Inserts separating lines for any SPIP texts which can be customised with a stylesheet.
_ The syntax is: "__code__", where "code" is either the identifying number (from 0 to 7) of the line to insert and which is linked to the corresponding style, or the name of an image in the plugins/couteau_suisse/img/filets directory.',
	'filets_sep:nom' => 'Dividing lines',
	'filtrer_javascript:description' => 'Three modes are available for controlling Javascript inserted directly in the text of articles:
- <i>never</i>: Javascript is prohibited everywhere
- <i>default</i>: the presence of Javascript is highlighted in red in the editing interface
- <i>always</i>: Javascript is always accepted.

N.B.: in forums, petitions, RSS feeds, etc., Javascript is <b>always</b> made secure.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Javascript management',
	'flock:description' => 'Deactivates the file-locking system which uses the PHP {flock()} function. Some web-hoting environments are unable to work with this function. Do not activate this tool if your site is functioning normally.',
	'flock:nom' => 'Files are not locked',
	'fonds' => 'Backgrounds:',
	'forcer_langue:description' => 'Forces the language context for multiligual templates which have a language menu able to manage the language cookie.',
	'forcer_langue:nom' => 'Force language (
forcer_langue)',
	'format_spip' => 'Articles in SPIP format',
	'forum_lgrmaxi:description' => 'By default forum messages are not limited in size. If this tool is activated, an error message is shown each time someone tries to post a message larger than the size given, and the message is refused. An empty value (or 0) means that no limit will be imposed.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Size of forums',
	'f_jQuery:description' => 'Prevents the installation of {jQuery} on th epublic site in order to economise some "machine resources". The jQuery library ([->http://jquery.com/]) is useful in Javascript programming and many plugins use it. SPIP uses it in the editing interface.

N.B: some Swiss Knife tools require {jQuery} to be installed. ',
	'f_jQuery:nom' => 'Deactivate jQuery',

	// G
	'glossaire:description' => '@puce@ Use one or several groups of keywords to manage an internal glossary. Enter the names of the groups here, separating them by  colons (:). If you leave the box empty (or enter "Glossaire"), it is the "Glossaire" group which will be used.[[%glossaire_groupes%]]@puce@ You can indicate the maximum number of links to create in a text for each word. A null or negative value will mean that all instances of the words will be treated. [[%glossaire_limite% par mot-cl&eacute;]]@puce@ There is a choice of two options for generating the small window which appears on the mouseover. [[%glossaire_js%]]',
	'glossaire:nom' => 'Internal glossary',
	'glossaire_css' => 'CSS solution',
	'glossaire_js' => 'Javascript solution',
	'guillemets:description' => '<NEW>Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',
	'guillemets:nom' => 'Curly inverted commas',

	// H
	'help' => '<NEW>{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Version locale : @version@@distant@<br/>@pack@</p><p>Liens de documentation :<br/> [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>R&eacute;initialisations :
_  [Des outils cach&eacute;s|Revenir &agrave; l\'apparence initiale de cette page->@hide@]
_  [De tout le plugin|Revenir &agrave; l\'&eacute;tat initial du plugin->@reset@]@install@
</p>',
	'help0' => '<NEW>{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Lien de documentation :<br/> [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>R&eacute;initialisation :
_  [De tout le plugin->@reset@]
</p>',
	'html' => 'html@_CS_ASTER@',

	// I
	'inactif' => 'Inactive tool',
	'inactifs' => 'Inactive tools:',
	'insertions:description' => 'N.B.: tool in development!! [[%insertions%]]',
	'insertions:nom' => 'Auto-correct',
	'insert_head:description' => 'Activate the tag [#INSERT_HEAD->http://www.spip.net/en_article2421.html] in all templates, whether or not this tag is present between &lt;head&gt; et &lt;/head&gt;. This option can be used to allow plugins to insert javascript code (.js) or stylesheets (.css).',
	'insert_head:nom' => '#INSERT_HEAD tag',
	'installe_pack' => 'Installation of a configuration pack',
	'introduction:description' => '<NEW>Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Attention}} : Avant d\'activer cette fonctionnalit&eacute;, v&eacute;rifiez bien qu\'aucune fonction {balise_INTRODUCTION()} n\'existe d&eacute;j&agrave; dans votre squelette ou vos plugins, la surcharge produirait alors une erreur de compilation.</p>
@puce@ Vous pouvez pr&eacute;ciser (en pourcentage par rapport &agrave; la valeur utilis&eacute;e par d&eacute;faut) la longueur du texte renvoy&eacute; par balise #INTRODUCTION. Une valeur nulle ou &eacute;gale &agrave; 100 ne modifie pas l\'aspect de l\'introduction et utilise donc les valeurs par d&eacute;faut suivantes : 500 caract&egrave;res pour les articles, 300 pour les br&egrave;ves et 600 pour les forums ou les rubriques.
[[%lgr_introduction%&nbsp;%]]
@puce@ Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION si le texte est trop long sont : <html>&laquo;&amp;nbsp;()&raquo;</html>. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant au lecteur que le texte tronqu&eacute; a bien une suite.
[[%suite_introduction%]]
@puce@ Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l\'article&raquo;
[[%lien_inctroduction%]]
',
	'introduction:nom' => '#INTRODUCTION tag',

	// J
	'jquery1' => '{{N.B.}}: this tool requires the {jQuery} plugin in order to function with this version of SPIP.',
	'jquery2' => 'This tool uses the {jQuery} library.',
	'js_defaut' => 'Default',
	'js_jamais' => 'Never',
	'js_toujours' => 'Always',

	// L
	'label:admin_travaux' => 'Close the public site for:',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Systematic creation of a summary:',
	'label:balise_sommaire' => 'Activate the tag #CS_SOMMAIRE :',
	'label:couleurs_fonds' => 'Allow backgrounds:',
	'label:cs_rss' => 'Activate:',
	'label:decoration_styles' => 'Your personalised style tags:',
	'label:dossier_squelettes' => 'Directory(ies) to use:',
	'label:duree_cache' => 'Duration of local cache:',
	'label:duree_cache_mutu' => 'Duration of mutualised cache:',
	'label:forum_lgrmaxi' => 'Value (in characters):',
	'label:glossaire_groupes' => 'Group(s) used:',
	'label:glossaire_js' => 'Technique used:',
	'label:glossaire_limite' => 'Maximum number of links created:',
	'label:insertions' => 'Auto-correct:',
	'label:lgr_introduction' => 'Length of summary:',
	'label:lgr_sommaire' => 'Length of summary (9 to 99):',
	'label:liens_interrogation' => 'Protect URLs:',
	'label:liens_orphelins' => 'Clickable links:',
	'label:lien_inctroduction' => 'Clickable follow-on dots:',
	'label:max_auteurs_page' => 'Authors per page:',
	'label:message_travaux' => 'Your maintenance message:',
	'label:paragrapher' => 'Always insert paragraphs:',
	'label:puce' => 'Public bullet &laquo;<html>-</html>&raquo;:',
	'label:quota_cache' => 'Quota value',
	'label:racc_h1' => 'Beginning and end of a &laquo;<html>{{{subtitle}}}</html>&raquo;:',
	'label:racc_hr' => 'Horizontal line (<html>----</html>) :',
	'label:racc_i1' => 'Beginning and end of &laquo;<html>{italics}</html>&raquo;:',
	'label:radio_desactive_cache3' => 'Deactivate the cache',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'New window for external links:',
	'label:radio_type_urls3' => 'URL format:',
	'label:set_couleurs' => 'Set to be used ',
	'label:spam_mots' => 'Prohibited sequences:',
	'label:spip_script' => 'Calling script',
	'label:style_h' => 'Your style:',
	'label:style_p' => 'Your style:',
	'label:suite_introduction' => 'Follow-on dots',
	'label:titre_travaux' => 'Message title:',
	'label:url_glossaire_externe2' => 'Link to external glossary:',
	'liens_en_clair:description' => '<NEW>Met &agrave; votre disposition le filtre : \'liens_en_clair\'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d\'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre \'cs=print\' ou \'page=print\' dans l\'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.',
	'liens_en_clair:nom' => 'Visible hyperlinks',
	'liens_orphelins:description' => '<NEW>Cet outil a deux fonctions :

@puce@ {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[%liens_interrogation%]]

@puce@ {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_  {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_  {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'Fine URLs',
	'liste_outils' => 'List of tools of the Swiss Knife',
	'log_couteau_suisse:description' => '<NEW>Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin \'Le Couteau Suisse\' dans les fichiers spip.log que l\'on peut trouver dans le r&eacute;pertoire : @_CS_DIR_TMP@',
	'log_couteau_suisse:nom' => 'Detailed log for the Swiss Knife',

	// M
	'mailcrypt:description' => '<NEW>Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d\'activer la messagerie du lecteur. Cet outil antispam tente d\'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.',
	'mailcrypt:nom' => 'MailCrypt',
	'maj_tous' => 'ALL',
	'modifier_vars' => 'Change these @nb@ parameters',

	// N
	'nb_outil' => '@pipe@ : @nb@ tool',
	'nb_outils' => '@pipe@ : @nb@ tools',
	'neplusafficher' => 'No longer show',
	'nouveaux' => 'New',
	'no_IP:description' => '<NEW>D&eacute;sactive le m&eacute;canisme d\'enregistrement automatique des adresses IP des visiteurs de votre site par soucis de confidentialit&eacute; : SPIP ne conservera alors plus aucun num&eacute;ro IP, ni temporairement lors des visites (pour g&eacute;rer les statistiques ou alimenter spip.log), ni dans les forums (responsabilit&eacute;).',
	'no_IP:nom' => 'No IP recording',

	// O
	'orientation:description' => '3 new criteria for your templates: <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Ideal for sorting photos according to their format (carre = square; paysage = landscape).',
	'orientation:nom' => 'Picture orientation',

	// P
	'pack' => 'Current configuration',
	'page' => 'page',
	'paragrapher2:description' => '<NEW>La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Insert paragraphs',
	'par_defaut' => 'By default',
	'permuter' => 'Switch the tools in bold type',
	'permuter_outil' => 'Switch the tool : \\u00ab @text@ \\u00bb ?',
	'permuter_outils' => 'Switch the @nb@ tools in bold type?',
	'pipelines' => 'Entry points used:',
	'presente_outils' => 'This is the old interface.<br /><br />If you have difficulties in using <a href=\'./?exec=admin_couteau_suisse\'>the new interface</a>, please let us know in the forum of <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'presente_outils2' => '<NEW>Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition.<br /><br />En cliquant sur le nom des outils ci-dessous, vous s&eacute;lectionnez ceux dont vous pourrez permuter l\'&eacute;tat &agrave; l\'aide du bouton central : les outils activ&eacute;s seront d&eacute;sactiv&eacute;s et <i>vice versa</i>. &Agrave; chaque clic, la description apparait au-dessous des listes. Les cat&eacute;gories sont repliables et les outils peuvent &ecirc;tre cach&eacute;s. Le double-clic permet de permuter rapidement un outil.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d\'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d\'autres plugins.<br /><br />Note : le simple chargement de cette page recompile l\'ensemble des outils du Couteau Suisse.',
	'prochainement' => 'This site will be back online soon.
_ Thank you for your understanding.',
	'propres' => 'propres@_CS_ASTER@',
	'propres-qs' => 'propres-qs',
	'propres2' => 'propres2@_CS_ASTER@',
	'pucesli:description' => '<NEW>Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',
	'pucesli:nom' => 'Beautiful bullets',

	// R
	'raccourcis' => 'Active Swiss Knife typographical shortcuts:',
	'raccourcis_barre' => 'The Swiss Knife\'s typographical shorcuts',
	'reserve_admin' => 'Access restricted to administrators',
	'resetselection' => 'Reset the selection',
	'rss_titre' => 'Development of the &laquo;The Swiss Knife&raquo;:',

	// S
	'sauf_admin' => 'All, except administrators',
	'selectiontous' => 'Select all the active tools',
	'set_options:description' => '<NEW>S&eacute;lectionne d\'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[%radio_set_options4%]]',
	'set_options:nom' => 'Type of private interface',
	'sf_amont' => 'Upstream',
	'sf_tous' => 'All',
	'simpl_interface:description' => '<NEW>D&eacute;sactive le menu de changement rapide de statut d\'un article au survol de sa puce color&eacute;e. Cela est utile si vous cherchez &agrave; obtenir une interface priv&eacute;e la plus d&eacute;pouill&eacute;e possible afin d\'optimiser les performances client.',
	'simpl_interface:nom' => 'Simplification of the editing interface',
	'smileys:aide' => 'Smileys: @liste@',
	'smileys:description' => '<NEW>Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Smileys',
	'sommaire:description' => '<NEW>Construit un sommaire pour vos articles afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

@puce@ Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ &bull; Syst&eacute;matique pour chaque article (une balise <code>[!sommaire]</code> plac&eacute;e n&rsquo;importe o&ugrave; &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article cr&eacute;era une exception).
_ &bull; Uniquement pour les articles contenant la balise <code>[sommaire]</code>.

[[%auto_sommaire%]]

@puce@ Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d\'article automatiquement. Mais vous avez la possibilt&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe en pages et onglets}.',
	'sommaire:nom' => 'A summary for your articles',
	'sommaire_avec' => 'An article with summary: <b>@racc@</b>',
	'sommaire_sans' => 'An article without summary: <b>@racc@</b>',
	'spam:description' => '<NEW>Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.

Listez ici les s&eacute;quences interdites@_CS_ASTER@ en les s&eacute;parant par des espaces. [[%spam_mots%]]
@_CS_ASTER@Pour sp&eacute;cifier un mot entier, mettez-le entre paranth&egrave;ses. Pour une expression avec des espaces, placez-la entre guillemets.',
	'spam:nom' => 'Fight against SPAM',
	'spip_cache:description' => '<NEW>@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[%radio_desactive_cache3%]]@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => 'SPIP and the cache',
	'SPIP_liens:description' => '@puce@ By default, all links on the site open in the current window. But it can be useful to open external links in a new window, i.e. adding {target="_blank"} to all link tags bearing one of the SPIP classes {spip_out}, {spip_url} or {spip_glossaire}. It is sometimes necessary to add one of these classes to the links in the site\'s templates (html files) in order make this functionality wholly effective.[[%radio_target_blank3%]]

@puce@ SPIP provides the shortcut <code>[?word]</code> to link words to their definition. By default (or if you leave the box below empty), wikipedia.org is used as the external glossary. You may choose another address. <br />Test link: [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP and external links',
	'standard' => 'standard',
	'statuts_spip' => 'Only the following SPIP status:',
	'statuts_tous' => 'Every status',
	'stat_auteurs' => 'Authors in statistics',
	'suivi_forums:description' => '<NEW>Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Overview of the public forums',
	'supprimer_cadre' => 'Delete this frame',
	'supprimer_numero:description' => '<NEW>Applique la fonction SPIP supprimer_numero() &agrave; l\'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d\'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Delete the number',

	// T
	'titre' => 'The Swiss Knife',
	'titre_tests' => 'The Swiss Knife - Test page',
	'tous' => 'All',
	'toutes_couleurs' => 'The 36 colours in CSS styles: @_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Multilingual blocks: <b><:trad:></b>',
	'toutmulti:description' => 'Makes it possible to use the shortcut <code><:a_text:></code> in order to place multilingual blocks anywhere in the text of an article.
_ The SPIP function used is: <code>_T(\'a_text\', 
flux)</code>.
_ Do not forget to check that the variable used (here, "a_text") is defined in the language files.',
	'toutmulti:nom' => 'Multilingual blocks',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'type_urls:description' => '<NEW>@puce@ SPIP offre un choix sur plusieurs jeux d\'URLs pour fabriquer les liens d\'acc&egrave;s aux pages de votre site :
<div style="font-size:90%; margin:0 2em;">
- {{page}} : la valeur par d&eacute;faut pour SPIP v1.9x : <code>/spip.php?article123</code>.
- {{html}} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
- {{propre}} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
- {{propres2}} : l\'extension \'.html\' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
- {{standard}} : URLs utilis&eacute;es par SPIP v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>
- {{propres-qs}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.</div>

Plus d\'infos : [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@pour utiliser les formats {html}, {propre} ou {propre2}, Recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site SPIP sous le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d\'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</p>

@puce@ {{Uniquement si vous utilisez le format {page} ci-dessus}}, alors il vous est possible de choisir le script d\'appel &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l\'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => 'Format of URLs',
	'typo_exposants:description' => '<NEW>Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).',
	'typo_exposants:nom' => 'Superscript',

	// V
	'validez_page' => 'To access modifications:',
	'variable_vide' => '(Empty)',
	'vars_modifiees' => 'The data has been modified',
	'verstexte:description' => '<NEW>2 filtres pour vos squelettes, permettant de produire des pages plus l&eacute;g&egrave;res.
_ version_texte : extrait le contenu texte d\'une page html &agrave; l\'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d\'une page html pour rendre du texte plein.',
	'verstexte:nom' => 'Text version',
	'votre_choix' => 'Your choice:',

	// X
	'xml:description' => '<NEW>Active le validateur xml pour l\'espace public tel qu\'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html]. Un bouton intitul&eacute; &laquo;&nbsp;Analyse XML&nbsp;&raquo; est ajout&eacute; aux autres boutons d\'administration.',
	'xml:nom' => 'XML validator'
);

?>
