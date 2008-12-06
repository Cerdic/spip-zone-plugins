<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => ':&nbsp;no',
	'2pts_oui' => ':&nbsp;yes',

	// S
	'SPIP_liens:description' => '@puce@ By default, all links on the site open in the current window. But it can be useful to open external links in a new window, i.e. adding {target="_blank"} to all link tags bearing one of the SPIP classes {spip_out}, {spip_url} or {spip_glossaire}. It is sometimes necessary to add one of these classes to the links in the site\'s templates (html files) in order make this functionality wholly effective.[[%radio_target_blank3%]]

@puce@ SPIP provides the shortcut <code>[?word]</code> to link words to their definition. By default (or if you leave the box below empty), wikipedia.org is used as the external glossary. You may choose another address. <br />Test link: [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP and external links',

	// A
	'acces_admin' => 'Administrators\' access:',
	'action_rapide' => 'Rapid action, only if you know what you are doing!',
	'attente' => 'Waiting...',
	'auteur_forum:description' => 'Request all authors of public messages to fill in (with at least one letter!) the field "@_CS_FORUM_NOM@" in order to avoid completely anonymous messages.',
	'auteur_forum:nom' => 'No anonymous forums',
	'auteurs:description' => 'This tool configures the appearance of [the authors\' page->./?exec=auteurs], in the private area.

@puce@ Define here the maximum number of authors to display in the central frame of the author\'s page. Beyond this number page numbering will be triggered.[[%max_auteurs_page%]]

@puce@ Which kinds of authors should be listed on the spages?
[[%auteurs_tout_voir%[[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]]]',
	'auteurs:nom' => 'Authors page',

	// B
	'basique' => 'Basic',
	'blocs:aide' => 'Folding blocks: <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias: <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) and <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Allows you to create blocks which show/hide when you click on the title.

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
 #BLOC_RESUME    (optional)
 a summary of the following block
 #BLOC_DEBUT
 My collapsible block (which can be loaded by an AJAX URL, if needed)
 #BLOC_FIN</code></quote>

@puce@ If you tick "yes" below, opening one block will cause all other blocks on the page to close. i.e. only one block is open at a time.[[%bloc_unique%]]',
	'blocs:nom' => 'Folding Blocks',
	'boites_privees:description' => 'All the boxes described below appear in the editing area.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]][[->%bp_urls_propres%]]
- {{Swiss Knife updates}}: a box on this configuration page showing the last changes made to the code of the plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Articles in SPIP format}}: an extra folding box for your articles showing the source code used by their authors.
- {{Author stats}}: an extra box on [the authors\' page->./?exec=auteurs] showing the last 10 connected authors and unconfirmed registrations. Only administrators can view this information.
- {{Clean URLs}}: a box for each objet type (article, section, author, ...) showing the clean URL associated with it and any existing aliases. The tool &laquo;&nbsp;[.->type_urls]&nbsp;&raquo; makes possible a fine adjustment of the site\'s URLs.',
	'boites_privees:nom' => 'Private boxes',
	'bp_urls_propres' => 'See clean URLs',

	// C
	'cache_controle' => 'Cache control',
	'cache_nornal' => 'Normal usage',
	'cache_permanent' => 'Permanent cache',
	'cache_sans' => 'No cache',
	'categ:admin' => '1. Administration',
	'categ:divers' => '60. Miscellaneous',
	'categ:interface' => '10. Private interface',
	'categ:public' => '40. Public site',
	'categ:spip' => '50. Tags, filters, criteria',
	'categ:typo-corr' => '20. Text improvements',
	'categ:typo-racc' => '30. Typographical shortcuts',
	'certaines_couleurs' => 'Only the tags defined below @_CS_ASTER@:',
	'chatons:aide' => 'Smileys: @liste@',
	'chatons:description' => 'Replace <code>:name</code> with smiley images in the text.
_ This tool will replace the shortcuts by the images of the same name found in the directory plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Smileys',
	'class_spip:description1' => 'Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{SPIP shortcuts}}.

Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '<MODIF>

SPIP normally uses the &lt;h3&gt; tag for subtitles. Here you can choose a different tag: [[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '<MODIF>

SPIP normally uses &lt;i> for marking italics. But &lt;em> could also be used. You can choose: [[%racc_i1%]][[->%racc_i2%]]
N.B.: if you change the tag used for italics, style {{2.}} above will not be applied.

@puce@ {{SPIP styles}}. Up to version 1.92 of SPIP, typographical shortcuts produced HTML tags all marked with the class "spip". For exeample, <code><p class="spip"></code>. Here you can define the style of these tags to link them to your stylesheet. An empty box means that no particular style will be applied.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Tags &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; and the lists (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Tags &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; and &lt;blockquote&gt; :[[%style_h%]]

N.B.: by changing the second parameter you will lose any standard styles associated with these tags.</blockquote>',
	'class_spip:nom' => 'SPIP and its shortcuts...',
	'code_css' => 'CSS',
	'code_fonctions' => 'Functions',
	'code_jq' => 'jQuery',
	'code_js' => 'JavaScript',
	'code_options' => 'Options',
	'code_spip_options' => 'SPIP options',
	'contrib' => 'More information: @url@',
	'corbeille:description' => '<NEW>SPIP suprime autom&aacute;ticamente los objetos tirados a la basura en un plazo de 24 horas, en general hacia las 4 de la madrugada, gracias a una tarea &laquo;CRON&raquo; (lanzamiento peri&oacute;dico y/o autom&aacute;tico de procesos preprogramados). Aqu&iacute; puedes impedir ese proceso para gestionar mejor la papelera.[[%arret_optimisation%]]',
	'corbeille:nom' => 'Wastebin',
	'corbeille_objets' => '@nb@ object(s) in the wastebin.',
	'corbeille_objets_lies' => '@nb_lies@ connection(s) detected.',
	'corbeille_objets_vide' => 'No object in the wastebin',
	'corbeille_objets_vider' => 'Delete the selected objects',
	'corbeille_vider' => 'Empty the wastebin:',
	'couleurs:aide' => 'Text colouring: <b>[coul]text[/coul]</b>@fond@ with <b>coul</b> = @liste@',
	'couleurs:description' => 'Provide shortcuts to add colours in any text of the site (articles, news items, titles, forums, ...)

Here are two identical examples to change the colour of text:@_CS_EXEMPLE_COULEURS2@

In the same way, to change the font if the following option allows:@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@The format of this personalised tags have to be of existing colours or define pairs &laquo;tag=colour&raquo;, separated by comas. Examples : &laquo;grey, red&raquo;, &laquo;smooth=yellow, strong=red&raquo;, &laquo;low=#99CC11, high=brown&raquo; but also &laquo;grey=#DDDDCC, red=#EE3300&raquo;. For the first and last example, the allowed tags are: <code>[grey]</code> et <code>[red]</code> (<code>[fond grey]</code> et <code>[fond red]</code> if the backgrounds are allowed).',
	'couleurs:nom' => 'Coloured text',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',
	'cs_comportement:description' => '<NEW>@puce@ {{Logs.}} Obtener abundante informaci&oacute;n sobre el funcionamiento de la Navaja Suiza en los archivos {spip.log} que se pueden encontrar en el directorio: {@_CS_DIR_TMP@}[[%log_couteau_suisse%]]

@puce@ {{Opciones de SPIP.}} SPIP ordena los plugins en un orden determinado. Para asegurarse de que la Navaja Suiza sea el primero y gestione desde el principio ciertas opciones de SPIP, marca la opci&oacute;n siguiente. Si los permisos de tu servidor lo permiten, se modificar&aacute; autom&aacute;ticamente el archivo {@_CS_FILE_OPTIONS@} para incluir el archivo {@_CS_DIR_TMP@couteau-suisse/mes_spip_options.php}.
[[%spip_options_on%]]

@puce@ {{Consultas externas.}} La Navaja Suiza verifica regularmente la existencia de una versi&oacute;n m&aacute;s reciente de su c&oacute;digo e informa en su p&aacute;gina de configuraci&oacute;n de una actualizaci&oacute;n que est&eacute; disponible. Si las consultas externas de tu servidor causan problemas, marca la casilla siguiente.[[%distant_off%]]',
	'cs_comportement:nom' => 'Behaviour of the Swiss Knife',
	'cs_distant_off' => 'Checks of remote versions',
	'cs_log_couteau_suisse' => 'Detailed logs of the Swiss Knife',
	'cs_spip_options_on' => 'SPIP options in "@_CS_FILE_OPTIONS@"',

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
	'decoupe:description' => '@puce@ Divides the display of an article into pages using automatic page numbering. Simply place four consecutive + signes (<code>++++</code>) where you wish a page break to occur.

By default, the Swiss Knife inserts the pagination links at the top and bottom of the page. But you can place the links elsewhere in your template by using the #CS_DECOUPE tag, which you can activate here:
[[%balise_decoupe%]]

@puce@ If you use this separator between  &lt;onglets&gt; and &lt;/onglets&gt; tags, then you will receive a tabbed page instead.

In templates you can use the tags #ONGLETS_DEBUT, #ONGLETS_TITRE and #ONGLETS_FIN.

This tool may be combined with "[.->sommaire]".',
	'decoupe:nom' => 'Division in pages and tabs',
	'desactiver_flash:description' => 'Deletes the flash objects from your site and replaces them by the associated alternative content.',
	'desactiver_flash:nom' => 'Deactivate flash objects',
	'detail_balise_etoilee' => '{{N.B.}} : Check the use made in your templates of starred tags. This tool will not apply its treatment to the following tag(s): @bal@.',
	'detail_fichiers' => 'Files:',
	'detail_inline' => 'Inline code:',
	'detail_jquery1' => '{{N.B.}}: this tool requires the {jQuery} plugin in order to function with this version of SPIP.',
	'detail_jquery2' => 'This tool requires the {jQuery} library.',
	'detail_jquery3' => '{{N.B.}}: this tool requires the plugin [jQuery pour SPIP 1.92->http://files.spip.org/spip-zone/jquery_192.zip] in order to function correctly with this version of SPIP.',
	'detail_pipelines' => 'Pipelines:',
	'detail_traitements' => 'Treatment:',
	'dossier_squelettes:description' => 'Changes which template directory to use. For example: "squelettes/mytemplate". You can register several directories by separating them with a colon <html>":"</html>. If you leave the following box empty (or type "dist" in it), then the default "dist" template, supplied with SPIP, will be used.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Template directory',

	// E
	'effaces' => 'Deleted',
	'en_travaux:description' => 'Makes it possible to display a customised message on the public site during maintenance work.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site in maintenance mode',
	'erreur:bt' => '<span style=\\"color:red;\\">Warning:</span> the typographical bar appears to be an old version (@version@).<br />The Swiss Knife is compatible only with version @mini@ or newer.',
	'erreur:description' => 'missing id in the tool\'s definition!',
	'erreur:distant' => 'The distant server',
	'erreur:jquery' => '{{N.B.}} : {jQuery} does not appear to be active for this page. Please consult the paragraph about the plugin\'s required libraries [in this article->http://www.spip-contrib.net/?article2166] or reload this page.',
	'erreur:js' => 'A Javascript error appears to have occurred on this page, hindering its action. Please activate Javascript in your browser, or try deactivating some SPIP plugins which may be causing interference.',
	'erreur:nojs' => 'Javascript has been deactivated on this page.',
	'erreur:nom' => 'Error!',
	'erreur:probleme' => 'Problem with: @pb@',
	'erreur:traitements' => 'The Swiss Knife - Compilation error: forbidden mixing of \'typo\' and \'propre\'!',
	'erreur:version' => 'This tool is unavailable in this version of SPIP.',
	'etendu' => 'Expanded',

	// F
	'f_jQuery:description' => 'Prevents the installation of {jQuery} on the public site in order to economise some "machine resources". The jQuery library ([->http://jquery.com/]) is useful in Javascript programming and many plugins use it. SPIP uses it in the editing interface.

N.B: some Swiss Knife tools require {jQuery} to be installed. ',
	'f_jQuery:nom' => 'Deactivate jQuery',
	'filets_sep:aide' => 'Dividing lines: <b>__i__</b> or <b>i</b> is a number.<br />Other available lines: @liste@',
	'filets_sep:description' => 'Inserts separating lines for any SPIP texts which can be customised with a stylesheet.
_ The syntax is: "__code__", where "code" is either the identifying number (from 0 to 7) of the line to insert and which is linked to the corresponding style, or the name of an image in the plugins/couteau_suisse/img/filets directory.',
	'filets_sep:nom' => 'Dividing lines',
	'filtrer_javascript:description' => 'Three modes are available for controlling JavaScript inserted directly in the text of articles:
- <i>never</i>: JavaScript is prohibited everywhere
- <i>default</i>: the presence of Javascript is highlighted in red in the editing interface
- <i>always</i>: JavaScript is always accepted.

N.B.: in forums, petitions, RSS feeds, etc., JavaScript is <b>always</b> made secure.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'JavaScript management',
	'flock:description' => 'Deactivates the file-locking system which uses the PHP {flock()} function. Some web-hoting environments are unable to work with this function. Do not activate this tool if your site is functioning normally.',
	'flock:nom' => 'Files are not locked',
	'fonds' => 'Backgrounds:',
	'forcer_langue:description' => '<MODIF>Forces the language context for multiligual templates which have a language menu able to manage the language cookie.',
	'forcer_langue:nom' => 'Force language',
	'format_spip' => 'Articles in SPIP format',
	'forum_lgrmaxi:description' => '<MODIF>By default forum messages are not limited in size. If this tool is activated, an error message is shown each time someone tries to post a message larger than the size given, and the message is refused. An empty value (or 0) means that no limit will be imposed.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Size of forums',

	// G
	'glossaire:aide' => 'A text with no glossary: <b>@_CS_SANS_GLOSSAIRE@</b>',
	'glossaire:description' => '<MODIF>@puce@ Use one or several groups of keywords to manage an internal glossary. Enter the names of the groups here, separating them by  colons (:). If you leave the box empty (or enter "Glossaire"), it is the "Glossaire" group which will be used.[[%glossaire_groupes%]]@puce@ You can indicate the maximum number of links to create in a text for each word. A null or negative value will mean that all instances of the words will be treated. [[%glossaire_limite% par mot-cl&eacute;]]@puce@ There is a choice of two options for generating the small window which appears on the mouseover. [[%glossaire_js%]]',
	'glossaire:nom' => 'Internal glossary',
	'glossaire_css' => 'CSS solution',
	'glossaire_js' => 'JavaScript solution',
	'guillemets:description' => 'Automatically replaces straight inverted commas (") by curly ones, using the correct ones for the current language. The replacement does not change the text stored in the database, but only the display on the screen.',
	'guillemets:nom' => 'Curly inverted commas',

	// H
	'help' => '{{This page is only accessible to main site administrators.}} It gives access to the configuration of some additional functions of the {{Swiss Knife}}.',
	'help2' => 'Local version: @version@',
	'help3' => '<p>Documentation links:<br/>• [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>Resets :
_ • [Hidden tools|Return to the original appearance of this page->@hide@]
_ • [Whole plugin|Reset to the original state of the plugin->@reset@]@install@
</p>',

	// I
	'icone_visiter:description' => 'Replaces the standard "Visit" button (top right on this page) by the site logo, if it exists.

To set this logo, go to the page "Site configuration" by clicking the "Configuration" button.',
	'icone_visiter:nom' => '"Visit" button',
	'insert_head:description' => '<MODIF>Activate the tag [#INSERT_HEAD->http://www.spip.net/en_article2421.html] in all templates, whether or not this tag is present between &lt;head&gt; et &lt;/head&gt;. This option can be used to allow plugins to insert javascript code (.js) or stylesheets (.css).',
	'insert_head:nom' => '#INSERT_HEAD tag',
	'insertions:description' => 'N.B.: tool in development!! [[%insertions%]]',
	'insertions:nom' => 'Auto-correct',
	'introduction:description' => '<MODIF>This tag can be used in templates to generate short summaries of articles, new items, etc.</p>
<p>{{Beware}} : If you have another plugin defining the fonction {balise_INTRODUCTION()} or you have defined it in your templates, you will get a compilation error.</p>
@puce@ You can specify (as a percentage of the default value) the length of the text generated by the tag #INTRODUCTION. A null value, or a value equal to 100 will not modify anything and return the defaults: 500 characters for the articles, 300 for the news items and 600 for forums and sections.
[[%lgr_introduction%&nbsp;%]]
@puce@ By default, if the text is too long, #INTRODUCTION will end with 3 dots: <html>&laquo;&amp;nbsp;(…)&raquo;</html>. You can change this to a customized string which shows that there is more text available.
[[%suite_introduction%]]
@puce@ If the #INTRODUCTION tag is used to give a summary of an article, the Swiss Knife can generate a link to the article on the 3 dots or string marking that there is more text available. For example : &laquo;Read the rest of the article…&raquo;

[[%lien_introduction%]]
',
	'introduction:nom' => '#INTRODUCTION tag',

	// J
	'jcorner:description' => '"Pretty Corners" is a tool which makes it easy to change the appearance of the corners of {{coloured boxes}} on the public pages of your site. Almost anything is possible!
_ See this page for examples: [->http://www.malsup.com/jquery/corner/].

Make a list below of the elements in your templates which are to be rounded. Use CSS syntax (.class, #id, etc. ). Use the sign "&nbsp;=&nbsp;" to specify the jQuery command to apply, and a double slash ("&nbsp;//&nbsp;") for comments. If no equals sign is provided, rounded corners equivalent to  <code>.ma_classe = .corner()</code> will be applied.[[%jcorner_classes%]]

N.B. This tool requires the {Round Corners} jQuery plugin in order to function. The Swiss Knife can install it automatically if you check this box. [[%jcorner_plugin%]]',
	'jcorner:nom' => 'Pretty Corners',
	'jcorner_plugin' => '"&nbsp;Round Corners plugin&nbsp;"',
	'jq_localScroll' => 'jQuery.LocalScroll ([demo->http://demos.flesler.com/jquery/localScroll/])',
	'jq_scrollTo' => 'jQuery.ScrollTo ([demo->http://demos.flesler.com/jquery/scrollTo/])',
	'js_defaut' => 'Default',
	'js_jamais' => 'Never',
	'js_toujours' => 'Always',

	// L
	'label:admin_travaux' => 'Close the public site for:',
	'label:arret_optimisation' => 'Stop SPIP from emptying the wastebin automatically:',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Systematic creation of a summary:',
	'label:balise_decoupe' => 'Activate the #CS_DECOUPE tag:',
	'label:balise_sommaire' => 'Activate the tag #CS_SOMMAIRE :',
	'label:bloc_unique' => 'Only one block open on the page:',
	'label:couleurs_fonds' => 'Allow backgrounds:',
	'label:cs_rss' => 'Activate:',
	'label:debut_urls_libres' => '<:label:debut_urls_propres:>',
	'label:debut_urls_propres' => 'Beginning of the URLs:',
	'label:debut_urls_propres2' => '<:label:debut_urls_propres:>',
	'label:decoration_styles' => 'Your personalised style tags:',
	'label:derniere_modif_invalide' => 'Refresh immediately after a modification:',
	'label:distant_off' => 'Deactivate:',
	'label:dossier_squelettes' => 'Directory(ies) to use:',
	'label:duree_cache' => 'Duration of local cache:',
	'label:duree_cache_mutu' => 'Duration of mutualised cache:',
	'label:expo_bofbof' => 'Place in superscript: <html>St(e)(s), Bx, Bd(s) et Fb(s)</html>',
	'label:forum_lgrmaxi' => 'Value (in characters):',
	'label:glossaire_groupes' => 'Group(s) used:',
	'label:glossaire_js' => 'Technique used:',
	'label:glossaire_limite' => 'Maximum number of links created:',
	'label:insertions' => 'Auto-correct:',
	'label:jcorner_classes' => 'Improve the corners of elements with the following CSS selectors',
	'label:jcorner_plugin' => 'Install the following {jQuery} plugin:',
	'label:lgr_introduction' => 'Length of summary:',
	'label:lgr_sommaire' => 'Length of summary (9 to 99):',
	'label:lien_introduction' => 'Clickable follow-on dots:',
	'label:liens_interrogation' => 'Protect URLs:',
	'label:liens_orphelins' => 'Clickable links:',
	'label:log_couteau_suisse' => 'Activate:',
	'label:marqueurs_urls_propres' => 'Add markers to distinguish between objects (SPIP>=2.0) :<br/>(e.g.. : "&nbsp;-&nbsp;" for -My-section-, "&nbsp;@&nbsp;" for @My-site@) ',
	'label:marqueurs_urls_propres2' => '<:label:marqueurs_urls_propres:>',
	'label:marqueurs_urls_propres_qs' => '<:label:marqueurs_urls_propres:>',
	'label:max_auteurs_page' => 'Authors per page:',
	'label:message_travaux' => 'Your maintenance message:',
	'label:moderation_admin' => 'Automatically validate messages from:',
	'label:paragrapher' => 'Always insert paragraphs:',
	'label:puce' => 'Public bullet &laquo;<html>-</html>&raquo;:',
	'label:quota_cache' => 'Quota value',
	'label:racc_g1' => 'Beginning and end of "<html>{{bolded text}}</html>":',
	'label:racc_h1' => 'Beginning and end of a &laquo;<html>{{{subtitle}}}</html>&raquo;:',
	'label:racc_hr' => 'Horizontal line (<html>----</html>) :',
	'label:racc_i1' => 'Beginning and end of &laquo;<html>{italics}</html>&raquo;:',
	'label:radio_desactive_cache3' => 'Deactivate the cache',
	'label:radio_desactive_cache4' => 'Use of the cache',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'New window for external links:',
	'label:radio_type_urls3' => 'URL format:',
	'label:scrollTo' => 'Instal the following {jQuery} plugins:',
	'label:separateur_urls_page' => 'Separating character \'type-id\'<br/>(eg.: ?article-123):',
	'label:set_couleurs' => 'Set to be used ',
	'label:spam_mots' => 'Prohibited sequences:',
	'label:spip_options_on' => 'Include',
	'label:spip_script' => 'Calling script',
	'label:style_h' => 'Your style:',
	'label:style_p' => 'Your style:',
	'label:suite_introduction' => 'Follow-on dots',
	'label:terminaison_urls_arbo' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_libres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_page' => 'URL endings (e.g.: .html):',
	'label:terminaison_urls_propres' => '<:label:terminaison_urls_page:>',
	'label:terminaison_urls_propres_qs' => '<:label:terminaison_urls_page:>',
	'label:titre_travaux' => 'Message title:',
	'label:titres_etendus' => 'Activate the extended use of the tags #TITRE_XXX:',
	'label:tri_articles' => 'Your choice:',
	'label:url_arbo_minuscules' => 'Preserve the case of titles in URLs:',
	'label:url_arbo_sep_id' => 'Separation character \'title-id\', used in the event of homonyms:<br/>(do not use \'/\')',
	'label:url_glossaire_externe2' => 'Link to external glossary:',
	'label:urls_arbo_sans_type' => 'Show the type of SPIP object in URLs:',
	'label:webmestres' => 'List of the website managers:',
	'liens_en_clair:description' => 'Makes the filter: \'liens_en_clair\' available to you. Your text probably contains hyperlinks which are not visible when the page is printed. This filter adds the link code between square brackets for every clickabel link (external links and email addresses). N.B: in printing mode (when using the parameter \'cs=print\' or \'page=print\' in the URL), this treatment is automatically applied.',
	'liens_en_clair:nom' => 'Visible hyperlinks',
	'liens_orphelins:description' => 'This tool has two functions:

@puce@ {{Correct Links}}.

In French texts, SPIP follows the rules of French typography and inserts a space before question and exclamation marks. This tool prevents this from happening in URLs.[[%liens_interrogation%]]

@puce@ {{Orhan links}}.

Systematically replaces all URLs which authors have placed in texts (especially often in forums) and which are thus not clickable, by links in the SPIP format. For example, {<html>www.spip.net</html>} will be replaced by: [->www.spip.net].

You can choose the manner of replacement:
_ • {Basic}: links such as {<html>http://spip.net</html>} (whatever protocol) and {<html>www.spip.net</html>} are replaced.
_ • {Extended}: additionally links such as these are also replaced:  {<html>me@spip.net</html>}, {<html>mailto:myaddress</html>} ou {<html>news:mynews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'Fine URLs',

	// M
	'mailcrypt:description' => 'Hides all the email links in your textes and replaces them with a Javascript link which activates the visitor\'s email programme when the link is clicked. This antispam tool attempts to prevent web robots from collecting email addresses which have been placed in forums or in the text displayed by the tags in your templates.',
	'mailcrypt:nom' => 'MailCrypt',
	'message_perso' => 'oh!',
	'moderation_admins' => 'authenticated administrators',
	'moderation_message' => '<NEW>Este foro est&aacute; moderado a priori: tu contribuci&oacute;n no aparecer&aacute; hasta que haya sido validada por un administrador del sitio, salvo si te has identificado y est&aacute;s autorizado a escribir directamente.',
	'moderation_moderee:description' => '<NEW>Permite moderar la moderaci&oacute;n de los foros p&uacute;blicos <b>configurados a priori</b> por los usuarios inscritos.<br />Por ejemplo: Si soy el webmaster de mi sitio, y respondo al mensaje de un usuario, &iquest;por qu&eacute; debo validar mi propio mensaje? &iexcl;Moderaci&oacute;n moderada lo hace para m&iacute;!  [[%moderation_admin%]][[-->%moderation_redac%]][[-->%moderation_visit%]]',
	'moderation_moderee:nom' => '<NEW>Moderaci&oacute;n moderada',
	'moderation_redacs' => 'authenticated authors',
	'moderation_visits' => 'Visitors authenticated',
	'modifier_vars' => 'Change these @nb@ parameters',
	'modifier_vars_0' => 'Change these parameters',

	// N
	'no_IP:description' => 'Deactivates, in order to preserve confidentiality, the mechanism which records the IP addresses of visitors to your site. SPIP will thus no longer record any IP addresses, neither temporarily at the time of the visits (used for managing statistics or for spip.log), nor in the forums (source of posts).',
	'no_IP:nom' => 'No IP recording',
	'nouveaux' => 'New',

	// O
	'orientation:description' => '3 new criteria for your templates: <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Ideal for sorting photos according to their format (carre = square; paysage = landscape).',
	'orientation:nom' => 'Picture orientation',
	'outil_actif' => 'Activated tool',
	'outil_activer' => 'Activate',
	'outil_activer_le' => 'Activate the tool',
	'outil_cacher' => 'No longer show',
	'outil_desactiver' => 'Deactivate',
	'outil_desactiver_le' => 'Deactivate this tool',
	'outil_inactif' => 'Inactive tool',
	'outil_intro' => '<MODIF>This page lists the functionalties which the plugin makes available to you.<br /><br />By clicking on the names of the tools below, you choose the ones which you can then switch on/off using the central button: active tools will be disabled and <i>vice versa</i>. When you click, the tools description is shown above the list. The tool categories are collapsible to hide the tools they contain. A double-click allows you to directly switch a tool on/off.<br /><br />For first use, it is recommended to activate tools one by one, thus reavealing any incompatibilites with your templates, with SPIP or with other plugins.<br /><br />N.B.: simply loading this page recompiles all the Swiss Knife tools.',
	'outil_intro_old' => 'This is the old interface.<br /><br />If you have difficulties in using <a href=\\\'./?exec=admin_couteau_suisse\\\'>the new interface</a>, please let us know in the forum of <a href=\\\'http://www.spip-contrib.net/?article2166\\\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ tool',
	'outil_nbs' => '@pipe@ : @nb@ tools',
	'outil_permuter' => 'Switch the tool: &laquo; @text@ &raquo; ?',
	'outils_actifs' => 'Activated tools:',
	'outils_caches' => 'Hidden tools:',
	'outils_cliquez' => 'Click the names of the tools above to show their description.',
	'outils_inactifs' => 'Inactive tools:',
	'outils_liste' => 'List of tools of the Swiss Knife',
	'outils_permuter_gras1' => 'Switch the tools in bold type',
	'outils_permuter_gras2' => 'Switch the @nb@ tools in bold type?',
	'outils_resetselection' => 'Reset the selection',
	'outils_selectionactifs' => 'Select all the active tools',
	'outils_selectiontous' => 'ALL',

	// P
	'pack_actuel' => 'Pack @date@',
	'pack_actuel_avert' => '<NEW>Atenci&oacute;n, las sobrecargas para los define() o los globales no se especifican aqu&iacute;',
	'pack_actuel_titre' => 'UP-TO-DATE CONFIGURATION PACK OF THE SWISS KNIFE',
	'pack_alt' => 'See the current configuration parameters',
	'pack_descrip' => '<MODIF>Your "Current configuration pack" brings together all the parameters activated for the Swiss Knife plugin. It remembers both whether a tool is activated or not and, if so, what options have been chosen.

This PHP code may be placed in the /config/mes_options.php file. It will place a reset link on the page of the "pack {@pack@}". Of course, you can change its name below.

If you reset the plugin by clicking on a pack, the Swiss Knife will reconfigure itself according to the values defined in that pack.',
	'pack_du' => '• of the pack @pack@',
	'pack_installe' => 'Installation of a configuration pack',
	'pack_nb_plrs' => 'At the moment there are @nb@ "configuration packs" available.',
	'pack_nb_un' => 'A "configuration pack" is currently available.',
	'pack_nb_zero' => 'No "configuration pack" is currently available.',
	'pack_outils_defaut' => 'Installation of the default tools',
	'pack_sauver' => 'Save the current configuration',
	'pack_sauver_descrip' => '<NEW>El bot&oacute;n de m&aacute;s abajo permite insertar directamente en el archivo <b>@file@</b> los par&aacute;metros necesarios para a&ntilde;adir un &laquo;paquete de configuraci&oacute;n&nbsp;&raquo; en el men&uacute; de la izquierda. Esto te permitir&aacute; despu&eacute;s devolver con un clic la Navaja Suiza al estado de configuraci&oacute;n en que est&aacute; actualmente.',
	'pack_titre' => 'Current configuration',
	'pack_variables_defaut' => 'Installation of the default variables',
	'par_defaut' => 'By default',
	'paragrapher2:description' => 'The SPIP function <code>paragrapher()</code> inserts the tags &lt;p&gt; and &lt;/p&gt; around all texts which do not have paragraphs. In order to have a finer control over your styles and layout, you can give a uniform look to your texts throughout the site.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Insert paragraphs',
	'pipelines' => 'Entry points used:',
	'pucesli:description' => 'Replaces bullets &laquo;-&raquo; (simple dash) in articles with ordered lists &laquo;-*&raquo; (transformed into  &lt;ul>&lt;li>…&lt;/li>&lt;/ul> in HTML) whose style may be customised using CSS.',
	'pucesli:nom' => 'Beautiful bullets',

	// R
	'raccourcis' => 'Active Swiss Knife typographical shortcuts:',
	'raccourcis_barre' => 'The Swiss Knife\'s typographical shorcuts',
	'reserve_admin' => 'Access restricted to administrators',
	'rss_actualiser' => 'Update',
	'rss_attente' => 'Awaiting RSS...',
	'rss_desactiver' => 'Deactivate &laquo;Swiss Knife updates&raquo;',
	'rss_edition' => 'RSS feed updated:',
	'rss_source' => 'RSS source',
	'rss_titre' => 'Development of the &laquo;The Swiss Knife&raquo;:',
	'rss_var' => 'Swiss Knife updates',

	// S
	'sauf_admin' => 'All, except administrators',
	'set_options:description' => 'Preselects the type of interface (simplified or advanced) for all editors, both existing and future ones. At the same time the button offering the choice between the two interfaces is also removed.[[%radio_set_options4%]]',
	'set_options:nom' => 'Type of private interface',
	'sf_amont' => 'Upstream',
	'sf_tous' => 'All',
	'simpl_interface:description' => 'Deactivates the pop-up menu for changing article status which shows onmouseover on the coloured status bullets. This can be useful if you wish to have an editing interface which is as simple as possible for the users.',
	'simpl_interface:nom' => 'Simplification of the editing interface',
	'smileys:aide' => 'Smileys: @liste@',
	'smileys:description' => 'Inserts smileys in texts containing a shortcut in this form <acronym>:-)</acronym>. Ideal for forums.
_ A tag is available for displaying a table of smileys in templates: #SMILEYS.
_ Images : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Smileys',
	'soft_scroller:description' => 'Gives a slow scroll effect when a visitor clicks on a link with an anchor tag. This helps the visitor to know where they are in a long text.

N.B. In order to work, this tool needs to be used in &laquo;DOCTYPE XHTML&raquo; pages (not HTML!). It also requires two {jQuery} plugins: {ScrollTo} et {LocalScroll}. The Swiss Knife can install them itself if you check the following two boxes. [[%scrollTo%]][[->%LocalScroll%]]
@_CS_PLUGIN_JQUERY192@',
	'soft_scroller:nom' => 'Soft anchors',
	'sommaire:description' => 'Builds a summary of your articles in order to access the main headings quickly (HTML tags &lt;h3>A Subtitle&lt;/h3> or SPIP subtitle shortcuts in the form: <code>{{{My subtitle}}}</code>).

@puce@ You can define the maximum number of characters of the subtitles used to make the summary:[[%lgr_sommaire% characters]]

@puce@ You can also determine the way in which the plugin constructs the summary: 
_ • Systematically, for each article (a tag <code>@_CS_SANS_SOMMAIRE@</code> placed anywhere within the text of the article will make an exception to the rule).
_ • Only for articles containing the <code>@_CS_AVEC_SOMMAIRE@</code> tag.

[[%auto_sommaire%]]

@puce@ By default, the Swiss Knife inserts the summary at the top of the article. But you can place it elsewhere, if you wish, by using the #CS_SOMMAIRE tag, which you can activate here:
[[%balise_sommaire%]]

The summary can be used in conjunction with : {[.->decoupe]}.',
	'sommaire:nom' => '<MODIF>A summary for your articles',
	'sommaire_avec' => 'An article with summary: <b>@_CS_AVEC_SOMMAIRE@</b>',
	'sommaire_sans' => 'An article without summary: <b>@_CS_SANS_SOMMAIRE@</b>',
	'spam:description' => 'Attempts to fight against the sending of abusive and automatic messages through forms on the public site. Some words and the tags  &lt;a>&lt;/a> are prohibited.

List here the sequences you wish to prohibit@_CS_ASTER@ separating them with spaces. [[%spam_mots%]]
@_CS_ASTER@To specify a whole word, place it in brackets. Expressions containing spaces should be placed with inverted commas.',
	'spam:nom' => 'Fight against SPAM',
	'spip_cache:description' => '<MODIF>@puce@ By default, SPIP calculates all the public pages and caches them in order to accelerate their display. It can be useful, when developing the site to disable the cache temporarily, in order to see the effect of changes immediately.[[%radio_desactive_cache3%]]@puce@ The cache occupies disk space and SPIP can limit the amount of space taken up. Leaving empty or putting 0 means that no limit will be applied.[[%quota_cache% Mo]]@puce@ When the site\'s contents are changed, SPIP immediately invalidates the cache without waiting for the next periodic recalculation. If your site experiences performance problems because of the load of repeated recalculations, you can choose "no" for this option.[[%derniere_modif_invalide%]]@puce@ If the #CACHE tag is not found in a template then by default SPIP caches a page for 24 hours before recalculating it. You can modify this default here.[[%duree_cache% heures]]@puce@ If you are running several mutualised sites, you can specify here the default value for all the local sites (SPIP 1.93).[[%duree_cache_mutu% heures]]',
	'spip_cache:description1' => '<NEW>@puce@ Por omisi&oacute;n, SPIP calcula todas las p&aacute;ginas p&uacute;blicas y las sit&uacute;a en la cach&eacute; para acelerar la consulta. Desactivar temporalmente la cach&eacute; puede ayudar durante el desarrollo del sitio. @_CS_CACHE_EXTENSION@[[%radio_desactive_cache3%]]',
	'spip_cache:description2' => '<NEW>@puce@ Cuatro opciones para orientar el funcionamiento de la cach&eacute; de SPIP: <q1>
_ • {Uso normal}: SPIP calcula todas las p&aacute;ginas p&uacute;blicas y las pone en la cach&eacute; para acelerar la consulta. Tras un cierto intervalo, la cach&eacute; se recalcula y se guarda.
_ • {Cach&eacute; permanente}: los intervalos de invalidaci&oacute;n de la cach&eacute; se ignoran.
_ • {Sin cach&eacute;}: desactivar temporalmente la cach&eacute; puede ayudar mientras se desarrolla el sitio. Aqu&iacute;, no se guarda nada en el disco.
_ • {Control de la cach&eacute;}: opci&oacute;n id&eacute;ntica a la anterior, con escritura en el disco de todos los resultados para poder controlarlos si hace falta.</q1>[[%radio_desactive_cache4%]]',
	'spip_cache:nom' => 'SPIP and the cache',
	'stat_auteurs' => 'Authors in statistics',
	'statuts_spip' => 'Only the following SPIP status:',
	'statuts_tous' => 'Every status',
	'suivi_forums:description' => 'The author of an article is always informed when a message is posted in the article\'s public forum. It is also possible to inform others: either all the forum\'s participants, or  just all the authors of messages higher in the thread.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Overview of the public forums',
	'supprimer_cadre' => 'Delete this frame',
	'supprimer_numero:description' => '<MODIF>Applies the supprimer_numero() SPIP function to all {{titles}} et des {{names}} of the public site, without needing the filter to be present in the templates.<br />For a multilingual site, follow this syntax: <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Delete the number',

	// T
	'titre' => 'The Swiss Knife',
	'titre_parent:description' => '<NEW>En el interior de un bucle, es habitual que se quiera mostrar el t&iacute;tulo del padre del objeto en curso. Tradicionalmente, bastaba utilizar un segundo bucle, pero esta nueva baliza #TITRE_PARENT aligerar&aacute; la escritura de tus esqueletos. El resultado devuelto es: el t&iacute;tulo del grupo de una palabra-clave o el de la secci&oacute;n padre (si existe) de cualquier otro objeto (art&iacute;culo, secci&oacute;n, breve, etc.).

Nota: Para las palabras-clave, un alias de #TITRE_PARENT es #TITRE_GROUPE. El tratamiento por SPIP de estas balizas nuevas es similar al de #TITRE.

@puce@ Si utilizas SPIP 2.0, aqu&iacute; tienes disponible todo un conjunto de balizas #TITRE_XXX que podr&aacute;n devolver el t&iacute;tulo del objeto \'xxx\', a condici&oacute;n de que el campo \'id_xxx\' est&eacute; presente en la tabla en curso (#ID_XXX utilizable en el bucle en curso).

Por ejemplo, en un bucle sobre (ARTICLES), #TITRE_SECTEUR devolver&aacute; el t&iacute;tulo del sector en el que est&aacute; situado el art&iacute;culo en curso, pues el identificador #ID_SECTEUR (o el campo \'id_secteur\') est&aacute; disponible en ese caso.[[%titres_etendus%]]',
	'titre_parent:nom' => '<NEW>Baliza #TITRE_PARENT',
	'titre_tests' => 'The Swiss Knife - Test page',
	'tous' => 'All',
	'toutes_couleurs' => 'The 36 colours in CSS styles: @_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Multilingual blocks: <b><:trad:></b>',
	'toutmulti:description' => '<MODIF>Makes it possible to use the shortcut <code><:a_text:></code> in order to place multilingual blocks anywhere in the text, titles, etc. of an article.

The SPIP function used is: <code>_T(\'a_text\')</code>.

User variables can also be added to the shortcuts. For example, <code><:a_text{name=John, tel=2563}:></code> makes it possible to pass the values to the SPIP language file: <code>\'a_text\'=>\'Please contact @name@, the administrator, on @tel@.</code>.

In this case, the SPIP function used is: <code>_T(\'a_text\', array(\'var1\'=>\'first text\', \'var2\'=>\'second text\', ...))</code>.

Do not forget to check that the variable used <code>\'a_text\'</code> is defined in the language files. Consult [this article->http://www.spip.net/en_article2444.html] for documentation.',
	'toutmulti:nom' => 'Multilingual blocks',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'This site will be back online soon.
_ Thank you for your understanding.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => 'Choose the sort order to be used for displaying articles in the editing interface ([->./?exec=auteurs]), within the sections.

The options below use the SQL function \'ORDER BY\'. Only use the customised option if you know what you are doing (the available fields are: {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => 'Article sort order',
	'tri_modif' => 'Sort by last modified date (ORDER BY date_modif DESC)',
	'tri_perso' => 'Sort by customised SQL, ORDER BY:',
	'tri_publi' => 'Sort by publication date (ORDER BY date DESC)',
	'tri_titre' => 'Sort by title (ORDER BY 0+titre,titre)',
	'type_urls:description' => '<MODIF>@puce@ SPIP offers a choice between several types of URLs for your site:

<div style="font-size:90%; margin:0 2em;">
- {{page}} : the default type for SPIP since version 1.9: <code>/spip.php?article123</code>.
- {{html}} : URLs take the form of classic html pages: <code>/article123.html</code>.
- {{propre}} : URLs are constructed using the title of the object: <code>/Mon-titre-d-article</code>.
- {{propres2}} : the extension \'.html\' is added to the URLs generated: <code>/Mon-titre-d-article.html</code>.
- {{arborescentes}} (only from SPIP 2.0 on) URLs are built in a tree structure: <code>/sector/section1/section2/article</code>.
- {{standard}} : the URLs used by SPIP v1.8 and earlier: <code>article.php3?id_article=123</code>
- {{propres-qs}} : this system functions using a "Query-String", in other words, without using the .htaccess file. The URLs are of the form: <code>/?Mon-titre-d-article</code>.</div>

More information: [->http://www.spip.net/en_article3588.html]
[[%radio_type_urls3%]]
<p style=\'font-size:85%\'>@_CS_ASTER@to use the types {html}, {propre}, {propre2} ou {arborescentes}, copy the file "htaccess.txt" from the root directory of the SPIP site to a file (also at the root) named ".htaccess" (be careful not to overwrite any existing configuration if there already is a file of this name). If your site is in a subdirectory, you may need to edit the line "RewriteBase" in the file in order for the defined URLs to direct requests to the SPIP files.</p>

@puce@ {{Only if you are using the type  {page} described above}} can you choose the displayed calling script for SPIP. By default, SPIP shows {spip.php}, but {index.php} (format : <code>/index.php?article123</code>) or an empty value (format : <code>/?article123</code>) are also possible. To use any other value, you need to create the corresponding file at the root of your site with the same contents as are found in the file {index.php}.
[[%spip_script%]]

@puce@ {{Only if you are using the type {arborescentes} describes aboves}}, you can customise the format:</p>
[[%url_arbo_minuscules%]][[%urls_arbo_sans_type%]][[%url_arbo_sep_id%]][[%terminaison_urls_arbo%]]',
	'type_urls:nom' => 'Format of URLs',
	'typo_exposants:description' => '<MODIF>{{Text in French}}: improves the typographical rendering of common abbreviations by adding superscript where necessary (thus, {<acronym>Mme</acronym>} becomes {M<sup>me</sup>}). Common errors corrected:  ({<acronym>2&egrave;me</acronym>} and  {<acronym>2me</acronym>}, for example, become {2<sup>e</sup>}, the only correct abbreviation).

The rendered abbreviations correspond to those of the Imprimerie nationale given in the {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, Presses de l\'Imprimerie nationale, Paris, 2002).

The following expressions are also handled: <html>Dr, Pr, Mgr, St, Bx, m2, m3, Mn, Md, St&eacute;, &Eacute;ts, Vve, bd, Cie, 1o, 2o, etc.</html>

{{English text}}: the suffixes of ordinal numbers are placed in superscript: <html>1st, 2nd</html>, etc.',
	'typo_exposants:nom' => 'Superscript',

	// U
	'url_arbo' => 'arborescentes@_CS_ASTER@',
	'url_html' => 'html@_CS_ASTER@',
	'url_libres' => 'libres@_CS_ASTER@',
	'url_page' => 'page',
	'url_propres' => 'propres@_CS_ASTER@',
	'url_propres-qs' => 'propres-qs',
	'url_propres2' => 'propres2@_CS_ASTER@',
	'url_propres_qs' => 'propres_qs',
	'url_standard' => 'standard',
	'urls_base_total' => '<NEW>Actualmente hay @nb@ URL(s) en la base',
	'urls_base_vide' => '<NEW>La base de datos de URLs est&aacute; vac&iacute;a',
	'urls_choix_objet' => '<MODIF>Edici&oacute;n basada en la URL de un objeto espec&iacute;fico:',
	'urls_edit_erreur' => '<NEW>El formato actual de las URLs (&laquo;&nbsp;@type@&nbsp;&raquo;) no permite la edici&oacute;n.',
	'urls_enregistrer' => '<NEW>Grabar esta URL en la base',
	'urls_nouvelle' => '<MODIF>&Eacute;diter l\'URL &laquo;&nbsp;propres&nbsp;&raquo;&nbsp;:',
	'urls_num_objet' => 'Number:',
	'urls_purger' => '<MODIF>Erase all',
	'urls_purger_tables' => 'empty tables selected',
	'urls_purger_tout' => '<MODIF>Reset the URLs stored in the database:',
	'urls_rechercher' => 'Find this object in the database',
	'urls_titre_objet' => '<NEW>T&iacute;tulo grabado:',
	'urls_type_objet' => '<MODF>Order:',
	'urls_url_calculee' => 'URL PUBLIC  &laquo;&nbsp;@type@&nbsp;&raquo;:',
	'urls_url_objet' => '<MODIF>URL &laquo;&nbsp;propres&nbsp;&raquo; enregistr&eacute;e&nbsp;:',
	'urls_valeur_vide' => '<NEW>(Un valor vac&iacute;o implica la supresi&oacute;n de la URL)',

	// V
	'validez_page' => 'To access modifications:',
	'variable_vide' => '(Empty)',
	'vars_modifiees' => 'The data has been modified',
	'version_a_jour' => 'Your version is up to date.',
	'version_distante' => 'Distant version...',
	'version_distante_off' => '<NEW>Comprobaci&oacute;n externa desactivada',
	'version_nouvelle' => 'New version: @version@',
	'version_revision' => 'version: @revision@',
	'version_update' => 'Automatic update',
	'version_update_chargeur' => '<NEW>Descarga autom&aacute;tica',
	'version_update_chargeur_title' => '<NEW>Descargar la &uacute;ltima versi&oacute;n del plugin mediante el plugin &laquo;Descargador&raquo;',
	'version_update_title' => 'Downloads the latest version of the plugin and updates it automatically.',
	'verstexte:description' => '<MODIF>2 filters for your templates which make it possible to produce lighter pages.
_ version_texte : extracts the text content of an HTML page (includes only a few very basic tags).
_ version_plein_texte : extracts the text content from an html to render full text.',
	'verstexte:nom' => 'Text version',
	'visiteurs_connectes:description' => 'Creates an HTML fragment for your templates which displays on the public site the number of vistors logged in.

Simply add <code><INCLURE{fond=fonds/visiteurs_connectes}></code> in the template.',
	'visiteurs_connectes:nom' => 'Vistors logged in',
	'voir' => 'See: @voir@',
	'votre_choix' => 'Your choice:',

	// W
	'webmestres:description' => '<NEW>Un {{webmaster}} en el sentido de SPIP es un {{administrador}} que tiene acceso al espacio FTP. Por omisi&oacute;n, y a partir de SPIP 2.0, el administrador es el <code>id_auteur=1</code> del sitio. Los webmasters definidos aqu&iacute; tienen el privilegio de no estar obligados a pasar por el FTP para validar las operaciones delicadas del sitio, como la actualizaci&oacute;n de la base de datos o la restauraci&oacute;n de un volcado.

Webmaster(s) actual(es): {@_CS_LISTE_WEBMESTRES@}.
_ Administrador(es) elegible(s): {@_CS_LISTE_ADMINS@}.

Al ser webmaster tu mismo, aqu&iacute; tienes permisos para modificar esta lista de ids -- separadas por dos puntos &laquo;&nbsp;:&nbsp;&raquo; si son varias. Ejemplo: &laquo;1:5:6&raquo;.[[%webmestres%]]',
	'webmestres:nom' => 'list of webmasters',

	// X
	'xml:description' => 'Activates the XML validator for the public site, as described in the [documentation->http://www.spip.net/en_article3582.html]. An &laquo;&nbsp;Analyse XML&nbsp;&raquo; button is added to the other admin buttons.',
	'xml:nom' => 'XML validator'
);

?>
