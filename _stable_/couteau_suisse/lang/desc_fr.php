<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier reserve a la partie PRIVEE (CONFIGURATION) du Couteau Suisse

// quelques chaines temporaires a traduire
$temp['type_urls'] = "#PUCE {{page}} : la valeur par d&eacute;faut pour SPIP v1.9 : <code>/spip.php?article123</code>.
_ #PUCE {{html}} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
_ #PUCE {{propre}} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
_ #PUCE {{propres2}} : l'extension '.html' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
_ #PUCE {{standard}} : URLs utilis&eacute;es par SPIP v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>
_ #PUCE {{propres-qs}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.";

// un peu de code : ne pas toucher !
$temp['couleurs'] = '<br /><span style="font-weight:normal; font-size:85%;"><span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert&nbsp;olive</span>, <span style="background-color:navy; color:white;">navy/bleu&nbsp;marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert&nbsp;clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu&nbsp;clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu&nbsp;azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu&nbsp;violet</span>, <span style="background-color:chocolate;">chocolate/brun&nbsp;clair</span>, <span style="background-color:cornsilk;">cornsilk/rose&nbsp;clair</span>, <span style="background-color:darkgreen;">darkgreen/vert&nbsp;fonce</span>, <span style="background-color:darkorange;">darkorange/orange&nbsp;fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve&nbsp;fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu&nbsp;ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune&nbsp;paille</span>, <span style="background-color:yellow;">yellow/jaune</span></span><span style="font-size:50%;"><br />&nbsp;</span>';
$temp['decoration'] = "{&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
_ {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
_ {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
_ {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
_ {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
_ {&lt;surfluo&gt;}Lorem ipsum dolor sit amet{&lt;/surfluo&gt;}
_ {&lt;surgris&gt;}Lorem ipsum dolor sit amet{&lt;/surgris&gt;}";
$temp['decoration'] = "<blockquote style=\"font-size:90%; margin:0 2em 0 2em;\">{$temp['decoration']}</blockquote>";
$temp['type_urls'] = "<blockquote style=\"font-size:90%; margin:0 2em 0 2em;\">{$temp['type_urls']}</blockquote>";
$temp['note'] = "<sup>(*)</sup>";

// fin du code

// traductions habituelles
$GLOBALS[$GLOBALS['idx_lang']] = array(
	'titre' => 'Le Couteau Suisse',
	'help0' => "{{Cette page est uniquement accessible aux responsables du site.}}"
		."<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p>"
		."<p>Lien de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p>"
		."<p>R&eacute;initialisation :
_ &bull; [De tout le plugin->@reset@]
</p>",
	'help' => "{{Cette page est uniquement accessible aux responsables du site.}}"
		."<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p>"
		."<p>Version locale : @version@@distant@<br/>@pack@</p>"
		."<p>Liens de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p>"
		."<p>R&eacute;initialisations :
_ &bull; [Des outils cach&eacute;s->@hide@]
_ &bull; [De tout le plugin->@reset@]@install@
</p>",
	'pour' => '&bull; du pack @pack@',
	'pack' => 'Configuration Actuelle',
	'descrip_pack' => "Votre \"Pack de configuration actuelle\" rassemble l'ensemble des param&egrave;tres de configuration en cours concernant le Couteau Suisse : l'activation des outils et la valeur de leurs &eacute;ventuelles variables.\n\nCe code PHP peut prendre place dans le fichier /config/mes_options.php et ajoutera un lien de r&eacute;initialisation sur cette page \"du pack {Pack Actuel}\". Bien s&ucirc;r il vous est possible de changer son nom ci-dessous.\n\nSi vous r&eacute;initialisez le plugin en cliquant sur un pack, le Couteau Suisse se reconfigurera automatiquement en fonction des param&egrave;tres pr&eacute;d&eacute;finis dans le pack.",
	'distant' => "<br/>Nouvelle version : [@version@->http://files.spip.org/spip-zone/couteau_suisse.zip]",
	'a_jour' => "<br/>Votre version est &agrave; jour.",

	'raccourcis' => "Raccourcis typographiques actifs du Couteau Suisse&nbsp;:",
	'raccourcis_barre' => "Les raccourcis typographiques du Couteau Suisse",
	'pipelines' => "Pipelines utilis&eacute;s&nbsp;:",
	'nb_outil' => '@pipe@ : @nb@ outil',
	'nb_outils' => '@pipe@ : @nb@ outils',
	'titre_tests' => 'Le Couteau Suisse - Page de tests&hellip;',
	'actif' => 'Outil actif',
	'actifs' => 'Outils actifs :',
	'inactif' => 'Outil inactif',
	'inactifs' => 'Outil inactifs :',
	'caches' => 'Outils cach&eacute;s :',
	'activer' => "Activer",
	'desactiver' => "D&eacute;sactiver",
	'activer_outil' => "Activer l'outil",
	'desactiver_outil' => "D&eacute;sactiver l'outil",
	'permuter' => "Permuter les outils en gras", // Javascript
	'permuter_outil' => "Permuter l'outil : \u00ab @text@ \u00bb ?", // Javascript : \u00ab et \u00bb sont les guillemets
	'permuter_outils' => "Permuter les @nb@ outils en gras ?",
	'resetselection' => "R&eacute;initialiser la s&eacute;lection",
	'neplusafficher' => "Ne plus afficher",
	'validez_page' => 'Pour acc&eacute;der aux modifications :',
	'modifier_vars' => 'Modifier ces @nb@ param&egrave;tres',
	'vars_modifiees' => 'Les donn&eacute;es ont bien &eacute;t&eacute; modifi&eacute;es',
	'variable_vide' => '(Vide)',
	'detail_inline' => 'Code inline :',
	'detail_fichiers' => 'Fichiers :',
	'detail_pipelines' => 'Pipelines :',
	'detail_traitements' => 'Traitements :',
	'code_options' => 'Options', 
	'code_fonctions' => 'Fonctions', 
	'code_js' => 'Javascript', 
	'code_jq' => 'jQuery', 
	'code_css' => 'CSS',
	'contrib' => "Plus d'infos : [->http://www.spip-contrib.net/?article@id@]",
	'liste_outils' => 'Liste des outils du Couteau Suisse',
	'presente_outils' => "Cette interface est ancienne.<br /><br />Si rencontrez des probl&egrave;mes dans l'utilisation de la <a href='./?exec=admin_couteau_suisse'>nouvelle interface</a>, n'h&eacute;sitez pas &agrave; nous en faire part sur le forum de <a href='http://www.spip-contrib.net/?article2166'>Spip-Contrib</a>.",
	'presente_outils2' => "Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition.<br /><br />En cliquant sur le nom des outils ci-dessous, vous s&eacute;lectionnez ceux dont vous pourrez permuter l'&eacute;tat &agrave; l'aide du bouton central : les outils activ&eacute;s seront d&eacute;sactiv&eacute;s et <i>vice versa</i>. &Agrave; chaque clic, la description apparait au-dessous des listes. Les cat&eacute;gories sont repliables et les outils peuvent &ecirc;tre cach&eacute;s. Le double-clic permet de permuter rapidement un outil.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d'autres plugins.<br /><br />Note : le simple chargement de cette page recompile l'ensemble des outils du Couteau Suisse.",
	'cliquezlesoutils' => "Cliquez sur le nom des outils ci-dessus pour afficher ici leur description.",
	'selectiontous' => "S&eacute;lectionner tous les outils actifs",
	'maj_tous' => 'TOUS',
	'par_defaut' => 'Par d&eacute;faut',
	'jquery1' => "{{Attention}} : cet outil n&eacute;cessite le plugin {jQuery} pour fonctionner avec cette version de SPIP.",
	'jquery2' => "Cet outil  utilise la librairie {jQuery}.",
	'balise_etoilee' => '{{Attention}} : V&eacute;rifiez bien l\'utilisation faite par vos squelettes des balises &eacute;toil&eacute;es. Les traitements de cet outil ne s\'appliqueront pas sur : @bal@.',
	'2pts_oui' => '&nbsp;:&nbsp;oui',
	'2pts_non' => '&nbsp;:&nbsp;non',
	'erreur:nom' => 'Erreur !',
	'erreur:description' => "id manquant dans la d&eacute;finition de l'outil !",
	'erreur:version' => 'Cet outil est indisponible dans cette version de SPIP.',
	'erreur:traitements' => "Le Couteau Suisse - Erreur de compilation des traitements : m&eacute;lange 'typo' et 'propre' interdit !",
	'erreur:probleme' => 'Probl&egrave;me sur : @pb@',
	'erreur:distant' => 'le serveur distant',
	'erreur:js' => "Une erreur JavaScript semble &ecirc;tre survenue sur cette page et emp&ecirc;che son bon fonctionnement. Veuillez activer JavaScript sur votre navigateur ou d&eacute;sactiver certains plugins SPIP de votre site.",
	'erreur:nojs' => "Le JavaScript est d&eacute;sactiv&eacute; sur cette page.",

// categories d'outils
// --------------------

	'admin' => "1. Administration",
	'typo-corr' => "2. Am&eacute;liorations des textes",
	'typo-racc' => "3. Raccourcis typographiques",
	'public' => "4. Affichage public",
	'spip' => "5. Balises, filtres, crit&egrave;res",
	'divers' => "6. Divers",

// Chaines de langue concernant de tous les outils configures dans config_outils.php
// ----------------------------------------------------------------------------------

	'spip_cache:nom' => 'SPIP et le cache&hellip;',
	'spip_cache:description' => "#PUCE Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[D&eacute;sactiver le cache :->%radio_desactive_cache3%]]"
		. "#PUCE Le cache occupe un certain espace disque et SPIP peut en limiter l'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu'aucun quota ne s'applique.[[Valeur du quota :->%quota_cache% Mo]]"
		. "#PUCE Si la balise #CACHE n'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[Dur&eacute;e du cache local :->%duree_cache% heures]]"
		. "#PUCE Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 1.93).[[Dur&eacute;e du cache en mutualisation :->%duree_cache_mutu% heures]]"
,/*		. "#PUCE &Agrave; un article particulier, une rubrique particuli&egrave;re, il est possible d'attribuer une valeur de cache. Listez ci-dessous (en les s&eacute;parant par une virgule) les identifiants et la dur&eacute;e de vie (en secondes) du cache correspondant.<br />Exemple : &laquo; rubrique12=7*24*3600, article34=60*60, article56=0 &raquo;.[[Liste d'exceptions :->%exceptions_cache%]]",*/

	'supprimer_numero:nom' => 'Supprime le num&eacute;ro',
	'supprimer_numero:description' => "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>",

	'paragrapher2:nom' => 'Paragrapher',
	'paragrapher2:description' => "La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d'uniformiser l'aspect des textes de votre site.[[Toujours paragrapher :->%paragrapher%]]",

	'forcer_langue:nom' => 'Forcer langue',
	'forcer_langue:description' => "Force le contexte de langue pour les jeux de squelettes multilingues disposant d'un formulaire ou d'un menu de langues sachant g&eacute;rer le cookie de langues.",

	'insert_head:nom' => 'Balise #INSERT_HEAD',
	'insert_head:description' => "Active automatiquement la balise [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] sur tous les squelettes, qu'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).",

	'verstexte:nom' => 'Version texte',
	'verstexte:description' => "2 filtres pour vos squelettes, permettant de produire des pages plus l&eacute;g&egrave;res.
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein.",

	'orientation:nom' => 'Orientation des images',
	'orientation:description' => "3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.",

	'desactiver_flash:nom' => 'D&eacute;sactive les objets flash',
	'desactiver_flash:description' => 'Supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.',

	'toutmulti:nom' => 'Blocs multilingues',
	'toutmulti:description' => "Introduit le raccourci <code><:un_texte:></code> pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : <code>_T('un_texte', \$flux)</code>.
_ N'oubliez pas de v&eacute;rifier que 'un_texte' est bien d&eacute;fini dans les fichiers de langue.",
	'toutmulti:aide' => 'Blocs multilingues&nbsp;: <b><:trad:></b>',

	'pucesli:nom' => 'Belles puces',
	'pucesli:description' => 'Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',

	'decoration:nom' => 'D&eacute;coration',
	'decoration:description' => "De nouveaux styles param&eacute;trables dans vos textes et accessibles gr&acirc;ce &agrave; des balises &agrave; chevrons. Exemple : 
&lt;mabalise&gt;texte&lt;/mabalise&gt; ou : &lt;mabalise/&gt;.<br />D&eacute;finissez ci-dessous les styles CSS dont vous avez besoin, une balise par ligne, selon les syntaxes suivantes :
- {type.mabalise = mon style CSS}
- {type.mabalise.class = ma classe CSS}
- {type.mabalise.lang = ma langue (ex : fr)}
- {unalias = mabalise}

Le param&egrave;tre {type} ci-dessus peut prendre trois valeurs :
- {span} : balise &agrave; l'int&eacute;rieur d'un paragraphe (type Inline)
- {div} : balise cr&eacute;ant un nouveau paragraphe (type Block)
- {auto} : balise d&eacute;termin&eacute;e automatiquement par le plugin

[[Vos balises de style personnalis&eacute; :->%decoration_styles%]]",
	'decoration:aide' => 'D&eacute;coration&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b>, avec <b>balise</b> = @liste@',

// ---------------------------------------------------------------------------
	'couleurs:nom' => 'Tout en couleurs',
	'couleurs:description' => "Permet d'appliquer facilement des couleurs &agrave; tous les textes du site (articles, br&egrave;ves, titres, forum, &hellip;) en utilisant des balises en raccourcis.

Deux exemples identiques pour changer la couleur du texte :
-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>
-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.

Idem pour changer le fond, si l'option ci-dessous le permet :
-* <code>Lorem ipsum [fond rouge]dolor[/fond rouge] sit amet</code>
-* <code>Lorem ipsum [bg red]dolor[/bg red] sit amet</code>.

[[Permettre les fonds :->%couleurs_fonds%]]
[[Set &agrave; utiliser :->%set_couleurs%]][[->%couleurs_perso%]]
{$temp['note']}Le format de ces balises personnalis&eacute;es doit lister des couleurs existantes ou d&eacute;finir des couples &laquo;balise=couleur&raquo;, le tout s&eacute;par&eacute; par des virgules. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; ou encore &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Pour le premier et le dernier exemple, les balises autoris&eacute;es sont : <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> et <code>[fond rouge]</code> si les fonds sont permis).",
	'couleurs:aide' => 'Mise en couleurs : <b>[coul]texte[/coul]</b>@fond@ avec <b>coul</b> = @liste@',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]texte[/coul]</b>, <b>[bg&nbsp;coul]texte[/coul]</b>',
	'toutes_couleurs' => "Les 36 couleurs des styles css :" . $temp['couleurs'],
	'certaines_couleurs' => "Seules les balises d&eacute;finies ci-dessous{$temp['note']} :",
	'fonds' => 'Fonds :',

// ---------------------------------------------------------------------------
	'typo_exposants:nom' => 'Exposants typographiques',
	'typo_exposants:description' => "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).",

	'filets_sep:nom' => 'Filets de S&eacute;paration',
	'filets_sep:description' =>  "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de SPIP.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.",
	'filets_sep:aide' => 'Filets de S&eacute;paration&nbsp;: <b>__i__</b> o&ugrave; <b>i</b> est un nombre.<br />Autres filets disponibles : @liste@',

	'smileys:nom' => 'Smileys',
	'smileys:description' => "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]",
	'smileys:aide' => 'Smileys : @liste@',

	'dossier_squelettes:nom' => 'Dossier du squelette',
	'dossier_squelettes:description' => "Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. En laissant vide la case qui suit (ou en tapant &quot;dist&quot;), c'est le squelette original &quot;dist&quot; fourni par SPIP qui sera utilis&eacute;.[[Dossier(s) &agrave; utiliser :->%dossier_squelettes%]]",

	'chatons:nom' => 'Chatons',
	'chatons:description' => 'Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <code>:nom</code>.
_ Cet outil remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire plugins/couteau_suisse/img/chatons.',
	'chatons:aide' => 'Chatons : @liste@',

	'guillemets:nom' => 'Guillemets typographiques',
	'guillemets:description' => 'Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',

	'set_options:nom' => "Type d'interface priv&eacute;e",
	'set_options:description' => "S&eacute;lectionne d'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[Votre choix :->%radio_set_options4%]]",

	'simpl_interface:nom' => "All&egrave;gement de l'interface priv&eacute;e",
	'simpl_interface:description' => "D&eacute;sactive le menu de changement rapide de statut d'un article au survol de sa puce color&eacute;e. Cela est utile si vous cherchez &agrave; obtenir une interface priv&eacute;e la plus d&eacute;pouill&eacute;e possible afin d'optimiser les performances client.",


// ---------------------------------------------------------------------------
	'type_urls:nom' => "Format des URLs",
	'type_urls:description' => "#PUCE SPIP offre un choix sur plusieurs jeux d'URLs pour fabriquer les liens d'acc&egrave;s aux pages de votre site :{$temp['type_urls']}

_ Plus d'infos : [->http://www.spip.net/fr_article765.html]
[[Format des URLs :->%radio_type_urls3%]]
<p style='font-size:80%'>{$temp['note']} pour utiliser les formats {html}, {propre} ou {propre2}, Recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site SPIP sous le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</p>

#PUCE {{Uniquement si vous utilisez le format {page} ci-dessus}}, alors il vous est possible de choisir le script d'appel &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[Script d'appel :->%spip_script%]]",
	'page' => 'page', 'html' => 'html'.$temp['note'],
	'propres' => 'propres'.$temp['note'], 'propres2' => 'propres2'.$temp['note'],
	'standard' => 'standard', 'propres-qs' => 'propres-qs',

// ---------------------------------------------------------------------------
	'log_couteau_suisse:nom' => 'Log d&eacute;taill&eacute; du Couteau Suisse',
	'log_couteau_suisse:description' => "Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin 'Le Couteau Suisse' dans les fichiers spip.log que l'on peut trouver dans le r&eacute;pertoire : ".cs_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP),

	'filtrer_javascript:nom' => 'Gestion du javascript',
	'filtrer_javascript:description' => 'Pour g&eacute;rer le javascript dans les articles, trois modes sont disponibles :
- <i>jamais</i> : le javascript est refus&eacute; partout
- <i>d&eacute;faut</i> : le javascript est signal&eacute; en rouge dans l\'espace priv&eacute;
- <i>toujours</i> : le javascript est accept&eacute; partout.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <b>toujours</b> s&eacute;curis&eacute;e.[[Votre choix :->%radio_filtrer_javascript3%]]',
	'js_jamais' => 'Jamais',
	'js_defaut' => 'D&eacute;faut',
	'js_toujours' => 'Toujours',

	'suivi_forums:nom' => 'Suivi des forums publics',
	'suivi_forums:description' => 'Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[Votre choix :->%radio_suivi_forums3%]]',
	'sf_tous' => 'Tous',
	'sf_amont' => 'En amont',

	'xml:nom' =>'Validateur XML',
	'xml:description' =>"Active le validateur xml pour l'espace public tel qu'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html]. Un bouton intitul&eacute; &laquo;&nbsp;"._T('analyse_xml')."&nbsp;&raquo; est ajout&eacute; aux autres boutons d'administration.",

	'f_jQuery:nom' => 'D&eacute;sactive jQuery',
	'f_jQuery:description' => "Emp&ecirc;che l'installation de {jQuery} dans la partie publique afin d'&eacute;conmiser un peu de &laquo;temps machine&raquo;. Cette librairie ([->http://jquery.com/]) apporte de nombreuses commodit&eacute;s dans la programmation de Javascript et peut &ecirc;tre utilis&eacute;e par certains plugins. SPIP l'utilise dans sa partie priv&eacute;e.

Attention : certains outils du Couteau Suisse n&eacute;cessitent les fonctions de {jQuery}. ",

	'SPIP_liens:nom' => 'SPIP et les liens&hellip; externes',
	'SPIP_liens:description' => "#PUCE Tous les liens du site s'ouvrent par d&eacute;faut dans la fen&ecirc;tre de navigation en cours. Mais il peut &ecirc;tre utile d'ouvrir les liens externes au site dans une nouvelle fen&ecirc;tre ext&eacute;rieure -- cela revient &agrave; ajouter {target=&quot;_blank&quot;} &agrave; toutes les balises &lt;a&gt; dot&eacute;es par SPIP des classes {spip_out}, {spip_url} ou {spip_glossaire}. Il est parfois n&eacute;cessaire d'ajouter l'une de ces classes aux liens du squelette du site (fichiers html) afin d'&eacute;tendre au maximum cette fonctionnalit&eacute;."
		. "[[Nouvelle fen&ecirc;tre pour les liens externes :->%radio_target_blank3%]]"
		. "#PUCE SPIP permet de relier des mots &agrave; leur d&eacute;finition gr&acirc;ce au raccourci typographique <code>[?mot]</code>. Par d&eacute;faut (ou si vous laissez vide la case ci-dessous), le glossaire externe renvoie vers l&rsquo;encyclop&eacute;die libre wikipedia.org. &Agrave; vous de choisir l'adresse &agrave; utiliser. <br />Lien de test : [?SPIP][[Lien vers le glossaire externe :->%url_glossaire_externe2%]]",

	'forum_lgrmaxi:nom' => 'Taille des forums',
	'forum_lgrmaxi:description' => "Par d&eacute;faut les messages de forum ne sont pas limit&eacute;s en taille. Si cet outil est activ&eacute;, un message d'erreur s'affichera lorsque quelqu'un voudra poster un message  d'une taille sup&eacute;rieure &agrave; la valeur sp&eacute;cifi&eacute;e, et le message sera refus&eacute;. Une valeur vide ou &eacute;gale &agrave; 0 signifie n&eacute;amoins qu'aucune limite ne s'applique.[[Valeur (en caract&egrave;res) :->%forum_lgrmaxi%]]",

// ---------------------------------------------------------------------------
	'introduction:nom' => "Balise #INTRODUCTION",
	'introduction:description' => "<p>Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Attention}} : Avant d'activer cette fonctionnalit&eacute;, v&eacute;rifiez bien qu'aucune fonction {balise_INTRODUCTION()} n'existe d&eacute;j&agrave; dans votre squelette ou vos plugins, la surcharge produirait alors une erreur de compilation.</p>
#PUCE Vous pouvez pr&eacute;ciser (en pourcentage par rapport &agrave; la valeur utilis&eacute;e par d&eacute;faut) la longueur du texte renvoy&eacute; par balise #INTRODUCTION. Une valeur nulle ou &eacute;gale &agrave; 100 ne modifie pas l'aspect de l'introduction et utilise donc les valeurs par d&eacute;faut suivantes : 500 caract&egrave;res pour les articles, 300 pour les br&egrave;ves et 600 pour les forums ou les rubriques.
[[Longueur du r&eacute;sum&eacute; :->%lgr_introduction%&nbsp;%]]
#PUCE Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION si le texte est trop long sont : <html>&laquo;&amp;nbsp;(&hellip;)&raquo;</html>. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant au lecteur que le texte tronqu&eacute; a bien une suite.
[[Points de suite :->%suite_introduction%]]
#PUCE Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l'article&hellip;&raquo;
[[Points de suite cliquables :->%lien_introduction%]]
",

// ---------------------------------------------------------------------------
// TODO : a partir de 1.93 il faut changer (ul ol pour style_h et simplement li pour style_p)
	'class_spip:nom' => 'SPIP et ses raccourcis&hellip;',
	'class_spip:description' => "".
	// avant SPIP 1.93 : <hr/> seulement
(!defined('_SPIP19300')?"Vous pouvez ici d&eacute;finir certains raccourcis de SPIP. Une valeur vide &eacute;quivaut &agrave; utiliser la valeur par d&eacute;faut.
[[Ligne horizontale &laquo;<html>----</html>&raquo; :->%racc_hr%]]"
	// des SPIP 1.93 : <hr/> + puce
:"#PUCE {{Les raccourcis de SPIP}}.\n\nVous pouvez ici d&eacute;finir certains raccourcis de SPIP. Une valeur vide &eacute;quivaut &agrave; utiliser la valeur par d&eacute;faut.
[[Ligne horizontale &laquo;<html>----</html>&raquo; :->%racc_hr%]]
[[Puce publique &laquo;<html>-</html>&raquo; :->%puce%]]").
	// des SPIP 1.91 : les intertitres
"\n\nSPIP utilise habituellement la balise &lt;h3&gt; pour les intertitres. Choisissez ici un autre remplacement :
[[Entr&eacute;e et sortie d'un &laquo;<html>{{{intertitre}}}</html>&raquo; :->%racc_h1%]][[->%racc_h2%]]".
	// des SPIP 1.93 : les italiques + les styles
(!defined('_SPIP19300')?"":"\n\nSPIP a choisi d'utiliser la balise &lt;i> pour transcrire les italiques. Mais &lt;em> aurait pu &eacute;galement convenir. &Agrave; vous de voir :
[[Entr&eacute;e et sortie d'un &laquo;<html>{italique}</html>&raquo; :->%racc_i1%]][[->%racc_i2%]]
Attention : en modifiant le remplacement des raccourcis d'italiques, le style {{2.}} sp&eacute;cifi&eacute; plus haut ne sera pas appliqu&eacute;.

#PUCE {{Les styles de SPIP}}. Jusqu'&agrave; la version 1.92 de SPIP, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style &quot;spip&quot;. Par exemple : <code><p class=\"spip\"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu'aucun style particulier ne sera appliqu&eacute;.<blockquote style='margin:0 2em;'>
_ {{1.}} Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[Votre style :->%style_p%]]
_ {{2.}} Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[Votre style :->%style_h%]]

Attention : en modifiant ce deuxi&egrave;me param&egrave;tre, vous perdez alors les styles standards associ&eacute;s &agrave; ces balises.</blockquote>"),

	'decoupe:nom' => 'D&eacute;coupe en pages et onglets',
	'decoupe:description' => "D&eacute;coupe l'affichage public d'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l'endroit qui doit recevoir la coupure.
_ Si vous utilisez ce s&eacute;parateur &agrave; l'int&eacute;rieur des balises &lt;onglets&gt; et &lt;/onglets&gt; alors vous obtiendrez un jeu d'onglets.
_ Dans les squelettes : vous avez &agrave; votre disposition les nouvelles balises #ONGLETS_DEBUT, #ONGLETS_TITRE et #ONGLETS_FIN.
_ Cet outil peut &ecirc;tre coupl&eacute; avec {Un sommaire pour vos articles}.",
	'decoupe:aide' => 'Bloc d\'onglets : <b>&lt;onglets>&lt;/onglets></b><br/>S&eacute;parateur de pages ou d\'onglets&nbsp;: @sep@',
	'decoupe:aide2' => '<br/>Alias&nbsp;:&nbsp;@sep@',

// ---------------------------------------------------------------------------
	'sommaire:nom' => 'Un sommaire pour vos articles',
	'sommaire:description' => "Construit un sommaire pour vos articles afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

#PUCE Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[Largeur du sommaire (9 &agrave; 99) :->%lgr_sommaire% caract&egrave;res]]

#PUCE Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ &bull; Syst&eacute;matique pour chaque article (une balise <code>[!sommaire]</code> plac&eacute;e n&rsquo;importe o&ugrave; &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article cr&eacute;era une exception).
_ &bull; Uniquement pour les articles contenant la balise <code>[sommaire]</code>.

[[Cr&eacute;ation syst&eacute;matique du sommaire :->%auto_sommaire%]]

#PUCE Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d'article automatiquement. Mais vous avez la possibilt&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[Activer la balise #CS_SOMMAIRE :->%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe en pages et onglets}.",
	'sommaire:aide' => defined('_sommaire_AUTOMATIQUE')?'Un article sans sommaire&nbsp;: @racc@':'Un article avec sommaire&nbsp;: @racc@',

// ---------------------------------------------------------------------------
	'liens_orphelins:nom' => 'Belles URLs',
	'liens_orphelins:description' => 'Cet outil a deux fonctions :

#PUCE {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[Prot&eacute;ger les URLs :->%liens_interrogation%]]

#PUCE {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[Liens cliquables :->%liens_orphelins%]]',
	'basique' => 'Basique',
	'etendu' => '&Eacute;tendu',

// ---------------------------------------------------------------------------
	'auteur_forum:nom' => "Pas de forums anonymes",
	'auteur_forum:description' => "Incite tous les auteurs de messages publics &agrave; remplir (d'au moins d'une lettre !) le champ &laquo;"._T('forum_votre_nom')."&raquo; afin d'&eacute;viter les contributions totalement anonymes.",

// ---------------------------------------------------------------------------
	'en_travaux:nom' => 'Site en travaux',
	'en_travaux:description' => "Permet d'afficher un message personalisable pendant une phase de maintenance sur tout le site public.
[[Votre message de maintenance :->%message_travaux%]][[Titre du message :->%titre_travaux%]][[Fermer le site public pour :->%admin_travaux%]]",
	'prochainement' => "Ce site sera r&eacute;tabli tr&egrave;s prochainement.
_ Merci de votre compr&eacute;hension.",
	'tous' => 'Tous',
	'sauf_admin' => 'Tous, sauf les administrateurs',
	'acces_admin' => 'Acc&egrave;s administrateurs :',
	'reserve_admin' => 'Acc&egrave;s r&eacute;serv&eacute; aux administrateurs.',
	'travaux_titre' => '<i>'._T('info_travaux_titre').'</i>',
	'travaux_nom_site' => '<i>'.$GLOBALS['meta']['nom_site'].'</i>',
	
// ---------------------------------------------------------------------------
	'glossaire:nom' => 'Glossaire interne',
	'glossaire:description' => "#PUCE Gestion d&rsquo;un glossaire interne li&eacute; &agrave; un ou plusieurs groupes de mots-cl&eacute;s. Inscrivez ici le nom des groupes en  les s&eacute;parant par les deux points &laquo;&nbsp;:&nbsp;&raquo;. En laissant vide la case qui  suit (ou en tapant &quot;Glossaire&quot;), c&rsquo;est le groupe &quot;Glossaire&quot; qui sera utilis&eacute;.[[Groupe(s) utilis&eacute;(s) :->%glossaire_groupes%]]"
		. "#PUCE Pour chaque mot, vous avez la possibilit&#233; de choisir le nombre maximal de liens cr&#233;&#233;s dans vos textes. Toute valeur nulle ou n&#233;gative implique que tous les mots reconnus seront trait&#233;s. [[Nombre maximal de liens cr&#233;&#233;s :->%glossaire_limite% par mot-cl&#233;]]"
		. "#PUCE Deux solutions vous sont offertes pour g&#233;n&#233;rer la petite fen&ecirc;tre automatique qui apparait lors du survol de la souris. [[Technique utilis&#233;e:->%glossaire_js%]]",
	'glossaire_css' => 'Solution CSS',
	'glossaire_js' => 'Solution Javascript',

// ---------------------------------------------------------------------------
	'mailcrypt:nom' => 'MailCrypt',
	'mailcrypt:description' => "Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d'activer la messagerie du lecteur. Cet outil antispam tente d'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.",

// ---------------------------------------------------------------------------
	'no_IP:nom' => "Pas de stockage IP",
	'no_IP:description' => "D&eacute;sactive le m&eacute;canisme d'enregistrement automatique des adresses IP des visiteurs de votre site par soucis de confidentialit&eacute; : SPIP ne conservera alors plus aucun num&eacute;ro IP, ni temporairement lors des visites (pour g&eacute;rer les statistiques ou alimenter spip.log), ni dans les forums (responsabilit&eacute;).",

// ---------------------------------------------------------------------------
	'flock:nom' => "Pas de verrouillage de fichiers",
	'flock:description' => "D&eacute;sactive le syst&egrave;me de verrouillage de fichiers en neutralisant la fonction PHP {flock()}. Certains h&eacute;bergements posent en effet des probl&egrave;mes graves suite &agrave; un syst&egrave;me de fichiers inadapt&eacute; ou &agrave; un manque de synchronisation. N'activez pas cet outil si votre site fonctionne normalement.",

// ---------------------------------------------------------------------------
	'spam:nom' => 'Lutte contre le SPAM',
	'spam:description' => 'Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.',

// ---------------------------------------------------------------------------
	'liens_en_clair:nom' => 'Liens en clair',
	'liens_en_clair:description' => "Met &agrave; votre disposition le filtre : 'liens_en_clair'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre 'cs=print' ou 'page=print' dans l'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.",

// ---------------------------------------------------------------------------
	'boites_privees:nom' => 'Bo&icirc;tes priv&eacute;es',
	'boites_privees:description' => "Toutes les bo&icirc;tes d&eacute;crites ci-dessous apparaissent dans la partie priv&eacute;e.[[Activer :->%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
	
#PUCE {{Les r&eacute;visions du Couteau Suisse}} : un cadre sur la pr&eacute;sente page de configuration, indiquant les derni&egrave;res modifications apport&eacute;es au code du plugin ([Source->"._CS_RSS_SOURCE."]).

#PUCE {{Les articles au format SPIP}} : un cadre repliable suppl&eacute;mentaire pour vos articles afin de conna&icirc;tre le code source utilis&eacute; par leurs auteurs.

#PUCE {{Les auteurs en stat}} : un cadre suppl&eacute;mentaires sur [la page des auteurs->./?exec=auteurs] indiquant les 10 derniers connect&eacute;s et les inscriptions non confirm&eacute;es. Seuls les administrateurs voient ces informations.",
	'cs_rss' => 'Les r&eacute;visions du Couteau Suisse',
	'format_spip' => 'Les articles au format SPIP',
	'stat_auteurs' => 'Les auteurs en stat',
	'rss_titre' => '&laquo;&nbsp;Le Couteau Suisse&nbsp;&raquo; en d&eacute;veloppement :',
	'edition' => 'Flux RSS mis &agrave; jour le :',
	'supprimer_cadre' => 'Supprimer ce cadre',
	'desactiver_rss' => 'D&eacute;sactiver les &laquo; R&eacute;visions du Couteau Suisse &raquo;',

// ---------------------------------------------------------------------------
	'auteurs:nom' => 'Page des auteurs',
	'auteurs:description' => "Cet outil configure l'apparence de [la page des auteurs->./?exec=auteurs], en partie priv&eacute;e.

#PUCE D&eacute;finissez ici le nombre maximal d'auteurs &agrave; afficher sur le cadre central de la page des auteurs. Au-del&agrave;, une pagination est mise en place.
[[Auteurs par page :->%max_auteurs_page%]]

#PUCE Quels statuts d'auteurs peuvent &ecirc;tre list&eacute;s sur cette page ?
[[Votre choix :->%auteurs_tout_voir%]][[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]",
	'statuts_tous' => 'Tous les statuts',
	'statuts_spip' => 'Uniquement les statuts SPIP suivants :',
	'nouveaux' => 'Nouveaux',
	'effaces' => 'Effac&eacute;s',
	
// ---------------------------------------------------------------------------
	'blocs:nom' => 'Blocs D&eacute;pliables',
	'blocs:description' => "Vous permet  de cr&eacute;er des blocs dont le titre cliquable peut les rendre visibles ou invisibles.\n\n#PUCE {{Dans les textes SPIP}} : les r&eacute;dacteurs ont &agrave; disposition les  nouvelles balises &lt;bloc&gt; (ou &lt;invisible&gt;) et &lt;visible&gt; &agrave; utiliser dans leurs textes comme ceci : 

<quote><code>
<bloc>
 Un titre qui deviendra cliquable
 
 Le texte a cacher/montrer, apres deux sauts de ligne...
 </bloc>
</code></quote>

#PUCE {{Dans les squelettes}} : vous avez &agrave; votre disposition les  nouvelles balises #BLOC_TITRE, #BLOC_DEBUT et #BLOC_FIN &agrave; utiliser comme ceci : 
<quote><code> #BLOC_TITRE
 Mon titre
 #BLOC_DEBUT
 Mon bloc depliable
 #BLOC_FIN</code></quote>
",
	'blocs:aide' => 'Blocs D&eacute;pliables : <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) et <b>&lt;visible&gt;&lt;/visible&gt;</b>',

// ---------------------------------------------------------------------------
	'insertions:nom' => 'Corrections automatiques',
	'insertions:description' => 'ATTENTION : outil en cours de d&eacute;veloppement !! [[Corrections automatiques :->%insertions%]]',
/*
// ---------------------------------------------------------------------------
	':nom' => '',
	':description' => '',
*/
);

unset($temp);
?>