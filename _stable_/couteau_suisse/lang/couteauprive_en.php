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
	'balise_etoilee' => '<NEW>{{Attention}} : V&eacute;rifiez bien l\'utilisation faite par vos squelettes des balises &eacute;toil&eacute;es. Les traitements de cet outil ne s\'appliqueront pas sur : @bal@.',
	'basique' => 'Basic',
	'blocs:aide' => 'Folding blocks: <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias: <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) and <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => '<MODIF>Allows you to create blocks where clicking on the title makes the contents visible or invisible.

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
	'boites_privees:description' => '<NEW>Toutes les bo&icirc;tes d&eacute;crites ci-dessous apparaissent dans la partie priv&eacute;e.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{Les r&eacute;visions du Couteau Suisse}} : un cadre sur la pr&eacute;sente page de configuration, indiquant les derni&egrave;res modifications apport&eacute;es au code du plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Les articles au format SPIP}} : un cadre repliable suppl&eacute;mentaire pour vos articles afin de conna&icirc;tre le code source utilis&eacute; par leurs auteurs.
- {{Les auteurs en stat}} : un cadre suppl&eacute;mentaires sur [la page des auteurs->./?exec=auteurs] indiquant les 10 derniers connect&eacute;s et les inscriptions non confirm&eacute;es. Seuls les administrateurs voient ces informations.',
	'boites_privees:nom' => 'Private boxes',

	// C
	'caches' => 'Hidden tools:',
	'certaines_couleurs' => 'Only the tags defined below @_CS_ASTER@:',
	'chatons:aide' => 'Smileys: @liste@',
	'chatons:description' => '<NEW>Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <code>:nom</code>.
_ Cet outil remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Smileys',
	'class_spip:description1' => 'Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]]',
	'class_spip:description2' => '<MODIF>@puce@ {{SPIP shortcuts}}.

Here you can define some SPIP shortcuts. An empty value is equivalent to using the default.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '<MODIF>

SPIP utilise habituellement la balise &lt;h3&gt; pour les intertitres. Choisissez ici un autre remplacement :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '<MODIF>

SPIP a choisi d\'utiliser la balise &lt;i> pour transcrire les italiques. Mais &lt;em> aurait pu &eacute;galement convenir. &Agrave; vous de voir :[[%racc_i1%]][[->%racc_i2%]]
Notez : en modifiant le remplacement des raccourcis d\'italiques, le style {{2.}} sp&eacute;cifi&eacute; plus haut ne sera pas appliqu&eacute;.

@puce@ {{Les styles de SPIP}}. Jusqu\'&agrave; la version 1.92 de SPIP, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style &quot;spip&quot;. Par exemple : <code><p class="spip"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu\'aucun style particulier ne sera appliqu&eacute;.<blockquote style=\'margin:0 2em;\'>
_ {{1.}} Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_p%]]
_ {{2.}} Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[%style_h%]]

Notez : en modifiant ce deuxi&egrave;me param&egrave;tre, vous perdez alors les styles standards associ&eacute;s &agrave; ces balises.</blockquote>',
	'class_spip:nom' => 'SPIP and its shortcuts...',
	'cliquezlesoutils' => 'Click on the names of the tools above in order to show their description.',
	'code_css' => 'CSS',
	'code_fonctions' => 'Functions',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Options',
	'contrib' => 'More information: [->http://www.spip-contrib.net/?article@id@]',
	'couleurs:aide' => 'Text colouring: <b>[coul]text[/coul]</b>@fond@ with <b>coul</b> = @liste@',
	'couleurs:description' => '<NEW>Permet d\'appliquer facilement des couleurs &agrave; tous les textes du site (articles, br&egrave;ves, titres, forum, &hellip;) en utilisant des balises en raccourcis.

Deux exemples identiques pour changer la couleur du texte :@_CS_EXEMPLE_COULEURS2@

Idem pour changer le fond, si l\'option ci-dessous le permet :@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@Le format de ces balises personnalis&eacute;es doit lister des couleurs existantes ou d&eacute;finir des couples &laquo;balise=couleur&raquo;, le tout s&eacute;par&eacute; par des virgules. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; ou encore &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Pour le premier et le dernier exemple, les balises autoris&eacute;es sont : <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> et <code>[fond rouge]</code> si les fonds sont permis).',
	'couleurs:nom' => 'Coloured text',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]text[/coul]</b>, <b>[bg&nbsp;coul]text[/coul]</b>',
	'cs_rss' => 'Swiss Knife updates',

	// D
	'decoration:aide' => 'D&eacute;coration: <b>&lt;tag&gt;test&lt;/tag&gt;</b>, with<b>tag</b> = @liste@',
	'decoration:description' => '<NEW>De nouveaux styles param&eacute;trables dans vos textes et accessibles gr&acirc;ce &agrave; des balises &agrave; chevrons. Exemple : 
&lt;mabalise&gt;texte&lt;/mabalise&gt; ou : &lt;mabalise/&gt;.<br />D&eacute;finissez ci-dessous les styles CSS dont vous avez besoin, une balise par ligne, selon les syntaxes suivantes :
- {type.mabalise = mon style CSS}
- {type.mabalise.class = ma classe CSS}
- {type.mabalise.lang = ma langue (ex : fr)}
- {unalias = mabalise}

Le param&egrave;tre {type} ci-dessus peut prendre trois valeurs :
- {span} : balise &agrave; l\'int&eacute;rieur d\'un paragraphe (type Inline)
- {div} : balise cr&eacute;ant un nouveau paragraphe (type Block)
- {auto} : balise d&eacute;termin&eacute;e automatiquement par le plugin

[[%decoration_styles%]]',
	'decoration:nom' => 'Decoration',
	'decoupe:aide' => '<NEW>Bloc d\'onglets : <b>&lt;onglets>&lt;/onglets></b><br/>S&eacute;parateur de pages ou d\'onglets&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias:&nbsp;@sep@',
	'decoupe:description' => '<NEW>D&eacute;coupe l\'affichage public d\'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l\'endroit qui doit recevoir la coupure.
_ Si vous utilisez ce s&eacute;parateur &agrave; l\'int&eacute;rieur des balises &lt;onglets&gt; et &lt;/onglets&gt; alors vous obtiendrez un jeu d\'onglets.
_ Dans les squelettes : vous avez &agrave; votre disposition les nouvelles balises #ONGLETS_DEBUT, #ONGLETS_TITRE et #ONGLETS_FIN.
_ Cet outil peut &ecirc;tre coupl&eacute; avec {Un sommaire pour vos articles}.',
	'decoupe:nom' => 'Division in pages and tabs',
	'desactiver' => 'Deactivate',
	'desactiver_flash:description' => 'Deletes the flash objects from your site and replaces them by the associated alternative content.',
	'desactiver_flash:nom' => 'Deactivate flash objects',
	'desactiver_outil' => 'Deactivate this tool',
	'desactiver_rss' => 'Deactivate &laquo;Swiss Knife updates&raquo;',
	'descrip_pack' => '<MODIF>Votre "Pack de configuration actuelle" rassemble l\'ensemble des param&egrave;tres de configuration en cours concernant le Couteau Suisse : l\'activation des outils et la valeur de leurs &eacute;ventuelles variables.

Ce code PHP peut prendre place dans le fichier /config/mes_options.php et ajoutera un lien de r&eacute;initialisation sur cette page "du pack {Pack Actuel}". Bien s&ucirc;r il vous est possible de changer son nom ci-dessous.

Si vous r&eacute;initialisez le plugin en cliquant sur un pack, le Couteau Suisse se reconfigurera automatiquement en fonction des param&egrave;tres pr&eacute;d&eacute;finis dans le pack.',
	'detail_fichiers' => 'Files:',
	'detail_inline' => 'Inline code:',
	'detail_pipelines' => 'Pipelines:',
	'detail_traitements' => 'Treatment:',
	'distant' => 'New version: [@version@->http://files.spip.org/spip-zone/couteau_suisse.zip]',
	'dossier_squelettes:description' => '<NEW>Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. En laissant vide la case qui suit (ou en tapant &quot;dist&quot;), c\'est le squelette original &quot;dist&quot; fourni par SPIP qui sera utilis&eacute;.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Template directory',
	'du_pack' => '• of the pack @pack@',

	// E
	'edition' => 'RSS feed updated:',
	'effaces' => 'Deleted',
	'en_travaux:description' => '<NEW>Permet d\'afficher un message personalisable pendant une phase de maintenance sur tout le site public.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site in maintenance mode',
	'erreur:description' => '<NEW>id manquant dans la d&eacute;finition de l\'outil !',
	'erreur:distant' => 'The distant server',
	'erreur:js' => '<NEW>Une erreur JavaScript semble &ecirc;tre survenue sur cette page et emp&ecirc;che son bon fonctionnement. Veuillez activer JavaScript sur votre navigateur ou d&eacute;sactiver certains plugins SPIP de votre site.',
	'erreur:nojs' => 'Javascript has been deactivated on this page.',
	'erreur:nom' => 'Error!',
	'erreur:probleme' => 'Problem with: @pb@',
	'erreur:traitements' => 'The Swiss Knife - Compilation error: forbidden mixing of \'typo\' and \'propre\'!',
	'erreur:version' => 'This tool is unavailable in this version of SPIP.',
	'etendu' => 'Expanded',

	// F
	'filets_sep:aide' => 'Dividing lines: <b>__i__</b> or <b>i</b> is a number.<br />Other available lines: @liste@',
	'filets_sep:description' => '<NEW>Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de SPIP.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d\'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Dividing lines',
	'filtrer_javascript:description' => '<NEW>Pour g&eacute;rer le javascript dans les articles, trois modes sont disponibles :
- <i>jamais</i> : le javascript est refus&eacute; partout
- <i>d&eacute;faut</i> : le javascript est signal&eacute; en rouge dans l\'espace priv&eacute;
- <i>toujours</i> : le javascript est accept&eacute; partout.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <b>toujours</b> s&eacute;curis&eacute;e.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Javascript management',
	'flock:description' => '<NEW>D&eacute;sactive le syst&egrave;me de verrouillage de fichiers en neutralisant la fonction PHP {flock()}. Certains h&eacute;bergements posent en effet des probl&egrave;mes graves suite &agrave; un syst&egrave;me de fichiers inadapt&eacute; ou &agrave; un manque de synchronisation. N\'activez pas cet outil si votre site fonctionne normalement.',
	'flock:nom' => 'Files are not locked',
	'fonds' => 'Backgrounds:',
	'forcer_langue:description' => '<NEW>Force le contexte de langue pour les jeux de squelettes multilingues disposant d\'un formulaire ou d\'un menu de langues sachant g&eacute;rer le cookie de langues.',
	'forcer_langue:nom' => 'Force language (
forcer_langue)',
	'format_spip' => 'Articles in SPIP format',
	'forum_lgrmaxi:description' => '<NEW>Par d&eacute;faut les messages de forum ne sont pas limit&eacute;s en taille. Si cet outil est activ&eacute;, un message d\'erreur s\'affichera lorsque quelqu\'un voudra poster un message  d\'une taille sup&eacute;rieure &agrave; la valeur sp&eacute;cifi&eacute;e, et le message sera refus&eacute;. Une valeur vide ou &eacute;gale &agrave; 0 signifie n&eacute;amoins qu\'aucune limite ne s\'applique.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Size of forums',
	'f_jQuery:description' => '<NEW>Emp&ecirc;che l\'installation de {jQuery} dans la partie publique afin d\'&eacute;conmiser un peu de &laquo;temps machine&raquo;. Cette librairie ([->http://jquery.com/]) apporte de nombreuses commodit&eacute;s dans la programmation de Javascript et peut &ecirc;tre utilis&eacute;e par certains plugins. SPIP l\'utilise dans sa partie priv&eacute;e.

Attention : certains outils du Couteau Suisse n&eacute;cessitent les fonctions de {jQuery}. ',
	'f_jQuery:nom' => 'Deactivate jQuery',

	// G
	'glossaire:description' => '<NEW>@puce@ Gestion d&rsquo;un glossaire interne li&eacute; &agrave; un ou plusieurs groupes de mots-cl&eacute;s. Inscrivez ici le nom des groupes en  les s&eacute;parant par les deux points &laquo;&nbsp;:&nbsp;&raquo;. En laissant vide la case qui  suit (ou en tapant &quot;Glossaire&quot;), c&rsquo;est le groupe &quot;Glossaire&quot; qui sera utilis&eacute;.[[%glossaire_groupes%]]@puce@ Pour chaque mot, vous avez la possibilit&#233; de choisir le nombre maximal de liens cr&#233;&#233;s dans vos textes. Toute valeur nulle ou n&#233;gative implique que tous les mots reconnus seront trait&#233;s. [[%glossaire_limite% par mot-cl&#233;]]@puce@ Deux solutions vous sont offertes pour g&#233;n&#233;rer la petite fen&ecirc;tre automatique qui apparait lors du survol de la souris. [[%glossaire_js%]]',
	'glossaire:nom' => 'Internal glossary',
	'glossaire_css' => 'CSS solution',
	'glossaire_js' => 'Javascript solution',
	'guillemets:description' => '<NEW>Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',
	'guillemets:nom' => 'Curly inverted commas',

	// H
	'help' => '<NEW>{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Version locale : @version@@distant@<br/>@pack@</p><p>Liens de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>R&eacute;initialisations :
_ &bull; [Des outils cach&eacute;s|Revenir &agrave; l\'apparence initiale de cette page->@hide@]
_ &bull; [De tout le plugin|Revenir &agrave; l\'&eacute;tat initial du plugin->@reset@]@install@
</p>',
	'help0' => '<NEW>{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Lien de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>R&eacute;initialisation :
_ &bull; [De tout le plugin->@reset@]
</p>',
	'html' => 'html@_CS_ASTER@',

	// I
	'inactif' => 'Inactive tool',
	'inactifs' => 'Inactive tools:',
	'insertions:description' => 'N.B.: tool in development!! [[%insertions%]]',
	'insertions:nom' => 'Auto-correct',
	'insert_head:description' => '<NEW>Active automatiquement la balise [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] sur tous les squelettes, qu\'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).',
	'insert_head:nom' => '#INSERT_HEAD tag',
	'installe_pack' => 'Installation of a configuration pack',
	'introduction:description' => '<NEW>Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Attention}} : Avant d\'activer cette fonctionnalit&eacute;, v&eacute;rifiez bien qu\'aucune fonction {balise_INTRODUCTION()} n\'existe d&eacute;j&agrave; dans votre squelette ou vos plugins, la surcharge produirait alors une erreur de compilation.</p>
@puce@ Vous pouvez pr&eacute;ciser (en pourcentage par rapport &agrave; la valeur utilis&eacute;e par d&eacute;faut) la longueur du texte renvoy&eacute; par balise #INTRODUCTION. Une valeur nulle ou &eacute;gale &agrave; 100 ne modifie pas l\'aspect de l\'introduction et utilise donc les valeurs par d&eacute;faut suivantes : 500 caract&egrave;res pour les articles, 300 pour les br&egrave;ves et 600 pour les forums ou les rubriques.
[[%lgr_introduction%&nbsp;%]]
@puce@ Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION si le texte est trop long sont : <html>&laquo;&amp;nbsp;(&hellip;)&raquo;</html>. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant au lecteur que le texte tronqu&eacute; a bien une suite.
[[%suite_introduction%]]
@puce@ Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l\'article&hellip;&raquo;
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
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
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
	'orientation:description' => '<NEW>3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.',
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
	'presente_outils' => '<NEW>Cette interface est ancienne.<br /><br />Si rencontrez des probl&egrave;mes dans l\'utilisation de la <a href=\'./?exec=admin_couteau_suisse\'>nouvelle interface</a>, n\'h&eacute;sitez pas &agrave; nous en faire part sur le forum de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
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
	'raccourcis_barre' => '<NEW>Les raccourcis typographiques du Couteau Suisse',
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
	'supprimer_numero:nom' => '<NEW>Supprime le num&eacute;ro',

	// T
	'titre' => 'The Swiss Knife',
	'titre_tests' => 'The Swiss Knife - Test page',
	'tous' => 'All',
	'toutes_couleurs' => '<NEW>Les 36 couleurs des styles css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => '<NEW>Blocs multilingues&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => '<NEW>Introduit le raccourci <code><:un_texte:></code> pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : <code>_T(\'un_texte\', $flux)</code>.
_ N\'oubliez pas de v&eacute;rifier que \'un_texte\' est bien d&eacute;fini dans les fichiers de langue.',
	'toutmulti:nom' => '<NEW>Blocs multilingues',
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
