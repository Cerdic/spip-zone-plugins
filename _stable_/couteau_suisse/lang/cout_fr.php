<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

// quelques chaines temporaires a traduire
$temp['jQuery1'] = "{{Attention}} : cette fonctionnalit&eacute; n&eacute;cessite le plugin {jQuery} pour fonctionner avec cette version de SPIP.";
$temp['jQuery2'] = "Cette fonctionnalit&eacute;  utilise la librairie {jQuery}.";
$temp['reset'] = 'R&eacute;initialisation des outils';
$temp['type_urls'] = "#PUCE {{page}} : la valeur par d&eacute;faut pour SPIP v1.9 : <code>/spip.php?article123</code>.
_ #PUCE {{html}} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
_ #PUCE {{propre}} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
_ #PUCE {{propres2}} : l'extension '.html' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
_ #PUCE {{standard}} : URLs utilis&eacute;es par SPIP v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>
_ #PUCE {{propres-qs}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.";

// un peu de code : ne pas toucher !
$temp['couleurs'] = '<br /><span style="font-weight:normal; font-size:85%;"><span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert&nbsp;olive</span>, <span style="background-color:navy; color:white;">navy/bleu&nbsp;marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert&nbsp;clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu&nbsp;clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu&nbsp;azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu&nbsp;violet</span>, <span style="background-color:chocolate;">chocolate/brun&nbsp;clair</span>, <span style="background-color:cornsilk;">cornsilk/rose&nbsp;clair</span>, <span style="background-color:darkgreen;">darkgreen/vert&nbsp;fonce</span>, <span style="background-color:darkorange;">darkorange/orange&nbsp;fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve&nbsp;fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu&nbsp;ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune&nbsp;paille</span>, <span style="background-color:yellow;">yellow/jaune</span></span><span style="font-size:50%;"><br />&nbsp;</span>';
$temp['jQuery'] = "<p>" . ($GLOBALS['spip_version_code']<1.92?$temp['jQuery1']:$temp['jQuery2']) . "</p>";
//$temp['reset'] = $GLOBALS['spip_version_code']<1.92?'<p>['. $temp['reset'] . '->' . parametre_url(self(),'reset','oui') . ']</p>':'';
$temp['reset'] = '<p>['. $temp['reset'] . '->' . parametre_url(self(),'reset','oui') . ']</p>';
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
	'help' => "{{Cette page est uniquement accessible aux responsables du site.}}"
		."<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin 'Le&nbsp;Couteau&nbsp;Suisse'.</p>"
		."<p>Documentation : [Tweak-SPIP->http://www.spip-contrib.net/Tweak-SPIP]</p>" . $temp['reset'],
	'raccourcis' => "Raccourcis typographiques actifs&nbsp;:",
	'pipelines' => "Pipelines utilis&eacute;s&nbsp;:",
	'nb_outil' => '@pipe@ : @nb@ outil',
	'nb_outils' => '@pipe@ : @nb@ outils',
	'titre_tests' => 'Le Couteau Suisse - Page de tests&hellip;',
	'actif' => 'Outil actif',
	'inactif' => 'Outil inactif',
	'actifs' => 'Outils actifs :',
	'activer_outil' => "Activer l'outil",
	'validez_page' => 'Pour acc&eacute;der aux modifications :',
	'modifier_vars' => 'Modifier ces @nb@ param&egrave;tres',
	'vars_modifiees' => 'Merci, les donn&eacute;es ont &eacute;t&eacute; modifi&eacute;es',
	'variable_vide' => '(Vide)',
	'detail_outil' => 'En jeu :',
	'liste_outils' => 'Liste des outils du Couteau Suisse',
	'presente_outils' => "Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition. Cliquez sur le petit triangle pour acc&eacute;der &agrave; leur description.<br />Vous pouvez activer les fonctionnalit&eacute;s n&eacute;cessaires en cochant la case correspondante puis en validant la page.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d'autres plugins.",
	'par_defaut' => 'Par d&eacute;faut',
	'erreur:nom' => 'Erreur !',
	'erreur:description' => "id manquant dans la d&eacute;finition de l'outil !",
	'erreur:version' => 'indisponible dans cette version de SPIP.',

// categories d'outils
// --------------------

	'admin' => "1. Administration",
	'typo-corr' => "2. Am&eacute;liorations typographiques",
	'typo-racc' => "3. Raccourcis typographiques",
	'public' => "4. Affichage public",
	'spip' => "5. Balises, filtres, crit&egrave;res",
	'divers' => "6. Divers",

// Chaines de langue concernant de tous les outils configures dans config_outils.php
// ----------------------------------------------------------------------------------

	'SPIP_cache:nom' => 'SPIP et le cache&hellip;',
	'SPIP_cache:description' => "#PUCE Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.[[D&eacute;sactiver le cache :->%radio_desactive_cache3%]]"
		. "#PUCE Le cache occupe un certain espace disque et SPIP peut en limiter l'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu'aucun quota ne s'applique.[[Valeur du quota :->%quota_cache% Mo]]",

	'supprimer_numero:nom' => 'Supprime le num&eacute;ro',
	'supprimer_numero:description' => "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code><br />Attention, cette fonctionnalit&eacute; ne sera pas prise en compte si votre squelette utilise les balises &eacute;toil&eacute;es : <code>#TITRE*</code> ou <code>#NOM*</code>",

	'paragrapher2:nom' => 'Paragrapher',
	'paragrapher2:description' => "La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d'uniformiser l'aspect des textes de votre site.[[Toujours paragrapher :->%paragrapher%]]",

	'forcer_langue:nom' => 'Forcer langue',
	'forcer_langue:description' => "Force le contexte de langue pour les jeux de squelettes multilingues disposant d'un formulaire ou d'un menu de langues sachant g&eacute;rer le cookie de langues.",

	'insert_head:nom' => 'Balise #INSERT_HEAD',
	'insert_head:description' => "Active automatiquement la balise [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] sur tous les squelettes, qu'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).",

	'verstexte:nom' => 'Version texte',
	'verstexte:description' => "2 filtres pour vos squelettes.
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein.",

	'orientation:nom' => 'Orientation des images',
	'orientation:description' => "3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.
_ Plus d'infos : [->http://www.spip-contrib.net/Portrait-ou-Paysage]",

	'desactiver_flash:nom' => 'D&eacute;sactive les objets flash',
	'desactiver_flash:description' => 'Supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.'
		. $temp['jQuery'],

	'toutmulti:nom' => 'Blocs multilingues',
	'toutmulti:description' => "Introduit le raccourci <code><:un_texte:></code> pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : <code>_T('un_texte', \$flux)</code>.
_ N'oubliez pas de v&eacute;rifier que 'un_texte' est bien d&eacute;fini dans les fichiers de langue.",
	'toutmulti:aide' => 'Blocs multilingues&nbsp;: <strong><:trad:></strong>',

	'pucesli:nom' => 'Belles puces',
	'pucesli:description' => 'Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',

	'decoration:nom' => 'D&eacute;coration',
	'decoration:description' => "7 nouveaux styles dans vos articles : <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote>, <surfluo>fluo</surfluo> et <surgris>gris&eacute;</surgris>. Utilisation :{$temp['decoration']}
_ Plus d'infos : [->http://www.spip-contrib.net/?article1552]",
	'decoration:aide' => 'D&eacute;coration&nbsp;: <strong>&lt;balise&gt;test&lt;/balise&gt;</strong>, avec <strong>balise</strong> = @liste@',

// ---------------------------------------------------------------------------
	'couleurs:nom' => 'Tout en couleurs',
	'couleurs:description' => "Permet d'appliquer facilement des couleurs &agrave; tous les textes du site (articles, br&egrave;ves, titres, forum, &hellip;) en utilisant des balises en raccourcis.

Deux exemples identiques pour changer la couleur du texte :
-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>
-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.

Idem pour changer le fond, si l'option ci-dessous le permet :
-* <code>Lorem ipsum [fond rouge]dolor[/fond rouge] sit amet</code>
-* <code>Lorem ipsum [bg red]dolor[/bg red] sit amet</code>.

Quelque soit la couleur, la balise fermante peut aussi &ecirc;tre : <code>[/couleur]</code> ou <code>[/color]</code>, et <code>[/fond]</code> ou <code>[/bg]</code>.
_ Un exemple de balises imbriqu&eacute;es : <code>[fond jaune]Lorem ipsum [rouge]dolor[/couleur] sit amet[/fond]</code>.

[[Permettre les fonds :->%couleurs_fonds%]]
[[Set &agrave; utiliser :->%set_couleurs%]][[->%couleurs_perso%]]
{$temp['note']}Le format de ces balises personnalis&eacute;es doit lister des couleurs existantes ou d&eacute;finir des couples &laquo;balise=couleur&raquo;, le tout s&eacute;par&eacute; par des virgules. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; ou encore &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Pour le premier et le dernier exemple, les balises autoris&eacute;es sont : <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> et <code>[fond rouge]</code> si les fonds sont permis).",
	'couleurs:aide' => 'Mise en couleurs : <strong>[couleur]texte[/couleur]</strong>@fond@ avec <strong>couleur</strong> = @liste@',
	'couleurs_fonds' => ', <strong>[fond&nbsp;couleur]texte[/couleur]</strong>, <strong>[bg&nbsp;couleur]texte[/couleur]</strong>',
	'toutes_couleurs' => "Les 36 couleurs des styles css :" . $temp['couleurs'],
	'certaines_couleurs' => "Seules les balises d&eacute;finies ci-dessous{$temp['note']} :",
	'ok_fonds' => "",

// ---------------------------------------------------------------------------
	'typo_exposants:nom' => 'Exposants typographiques',
	'typo_exposants:description' => "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).
_ Plus d'infos : [->http://www.spip-contrib.net/?article1564]",

	'filets_sep:nom' => 'Filets de S&eacute;paration',
	'filets_sep:description' =>  "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de Spip.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.
_ Plus d'infos : [->http://www.spip-contrib.net/?article1563]",
	'filets_sep:aide' => 'Filets de S&eacute;paration&nbsp;: <strong>__i__</strong> o&ugrave; <strong>i</strong> est un nombre.<br />Autres filets disponibles : @liste@',

	'smileys:nom' => 'Smileys',
	'smileys:description' => "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Plus d'infos : [->http://www.spip-contrib.net/?article1561]
_ Dessins : [Sylvain Michel->http://www.guaph.net/]",
	'smileys:aide' => 'Smileys : @liste@',
	'smileys_dispos' => 'Frimousses disponibles :',

	'dossier_squelettes:nom' => 'Dossier du squelette',
	'dossier_squelettes:description' => "Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. En laissant vide la case qui suit, c'est le squelette original &quot;dist&quot; fourni par Spip qui sera utilis&eacute;.[[Dossier(s) &agrave; utiliser :->%dossier_squelettes%]]",

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

#PUCE {{Uniquement si vous utilisez le format {page} ci-dessus}}, alors il vous est possible de choisir le script d'appel &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de spip, &agrave; l'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[Script d'appel :->%spip_script%]]",
	'page' => 'page', 'html' => 'html'.$temp['note'],
	'propres' => 'propres'.$temp['note'], 'propres2' => 'propres2'.$temp['note'],
	'standard' => 'standard', 'propres-qs' => 'propres-qs',

// ---------------------------------------------------------------------------
	'log_couteau_suisse:nom' => 'Log d&eacute;taill&eacute; du Couteau Suisse',
	'log_couteau_suisse:description' => "Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin 'Couteau Suisse' dans les fichiers spip.log que l'on peut trouver dans le r&eacute;pertoire : ".cs_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP),

	'cookie_prefix:nom' => 'Pr&eacute;fixe des cookies',
	'cookie_prefix:description' => 'Sp&eacute;cifie le pr&eacute;fixe &agrave; donner aux cookies de ce site. Utile pour installer des sites SPIP dans des sous-r&eacute;pertoires.[[Votre choix :->%cookie_prefix%]]',

	'filtrer_javascript:nom' => 'Gestion du javascript',
	'filtrer_javascript:description' => 'Pour g&eacute;rer le javascript dans les articles, trois modes sont disponibles :
- <i>jamais</i> : le javascript est refus&eacute; partout
- <i>d&eacute;faut</i> : le javascript est signal&eacute; en rouge dans l\'espace priv&eacute;
- <i>toujours</i> : le javascript est accept&eacute; partout.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <strong>toujours</strong> s&eacute;curis&eacute;e.[[Votre choix :->%radio_filtrer_javascript3%]]',
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

	'SPIP_liens:nom' => 'SPIP et les liens&hellip;',
	'SPIP_liens:description' => "#PUCE Tous les liens du site s'ouvrent par d&eacute;faut dans la fen&ecirc;tre de navigation en cours. Mais il peut &ecirc;tre utile d'ouvrir liens externes au site dans une nouvelle fen&ecirc;tre ext&eacute;rieure -- cela revient &agrave; ajouter {target=&quot;_blank&quot;} &agrave; toutes les balises &lt;a&gt; dot&eacute;es par SPIP des classes {spip_out}, {spip_url} ou {spip_glossaire}. Il est parfois n&eacute;cessaire d'ajouter l'une de ces classes aux liens du squelette du site (fichiers html) afin d'&eacute;tendre au maximum cette fonctionnalit&eacute;."
		. $temp['jQuery'] . /*"<br />Lien de test : [Google->http://www.google.com]".*/ "[[Utiliser des liens externes :->%radio_target_blank3%]]"
		. "#PUCE SPIP permet de relier des mots &agrave; leur d&eacute;finition gr&acirc;ce au raccourci typographique <code>[?mot]</code>. Par d&eacute;faut (ou si vous laissez vide la case ci-dessous), le glossaire externe renvoie vers l&rsquo;encyclop&eacute;die libre wikipedia.org. &Agrave; vous de choisir l'adresse &agrave; utiliser. <br />Lien de test : [?SPIP][[Lien vers le glossaire :->%url_glossaire_externe%]]",

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
	'class_spip:nom' => 'SPIP et les styles&hellip;',
	'class_spip:description' => "<p>Jusqu'&agrave; la version 1.92 de SPIP, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style &quot;spip&quot;. Par exemple : <code><p class=\"spip\"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu'aucun style particulier ne sera appliqu&eacute;.</p>
<p>#PUCE Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[Votre style :->%style_p%]]
#PUCE Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :[[Votre style :->%style_h%]]</p>
<p>Attention : en modifiant ce deuxi&egrave;me param&egrave;tre, vous perdez alors les styles standards associ&eacute;s &agrave; ces raccourcis.</p>"
,//	."<p>Pour afiner un peu, vous pouvez maintenant d&eacute;finir le style particulier des balises suivantes :</p>",

	'decoupe:nom' => 'D&eacute;coupe un texte en pages',
	'decoupe:description' => "D&eacute;coupe l'affichage public d'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l'endroit qui doit recevoir la coupure.
_ Cet outil peut &ecirc;tre coupl&eacute; avec {Sommaire en d&eacute;but d'article}.
_ Plus d'infos : [->http://www.spip-contrib.net/?article2135]",
	'decoupe:aide' => 'S&eacute;parateur de pages&nbsp;: @sep@',
	'page_suivante' => 'Page suivante',
	'page_precedente' => 'Page pr&eacute;c&eacute;dente',
	'page_debut' => 'Premi&egrave;re page',
	'page_fin' => 'Derni&egrave;re page',

// ---------------------------------------------------------------------------
	'sommaire:nom' => 'Sommaire en d&eacute;but d\'article',
	'sommaire:description' => "Construit syst&eacute;matiquement un sommaire en d&eacute;but d&rsquo;article afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML <code><h3>Un titre</h3></code> ou raccourci SPIP (intertitres de la forme :<code>{{{Un autre titre}}}</code>). Afin d'&eacute;viter l'insertion automatique du sommaire, il vous suffit de placerla balise <code>[!sommaire]</code> &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article (n&rsquo;importe o&ugrave;).<br />Attention, le sommaire ne sera pas construit si votre squelette utilise la balise #TEXTE &eacute;toil&eacute;e : <code>#TEXTE*</code>. Cet outil peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe un article en pages}.",
	'sommaire:aide' => 'Un article sans sommaire&nbsp;: @interdit@',
	'sommaire' => 'Sommaire',

// ---------------------------------------------------------------------------
	'liens_orphelins:nom' => 'Liens orphelins',
	'liens_orphelins:description' => 'Remplace syst&eacute;matiquement tous les liens laiss&eacute;s en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
- {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
- {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[Votre choix :->%liens_orphelins%]]',
	'basique' => 'Basique',
	'etendu' => '&Eacute;tendu',

// ---------------------------------------------------------------------------
	'auteur_forum:nom' => "Pas de forums anonymes",
	'auteur_forum:description' => "Incite tous les auteurs de messages publics &agrave; remplir (d'au moins d'une lettre !) le champ &laquo;"._T('forum_votre_nom')."&raquo; afin d'&eacute;viter les contributions totalement anonymes." . $temp['jQuery'],
	'nom_forum' => 'Merci de sp&eacute;cifier votre nom !',

// ---------------------------------------------------------------------------
	'en_travaux:nom' => 'Site en travaux',
	'en_travaux:description' => "Permet d'afficher un message personalisable pendant une phase de maintenance sur tout le site public.
[[Votre message de maintenance :->%message_travaux%]][[Fermer le site public pour :->%admin_travaux%]]",
	'prochainement' => "Ce site sera r&eacute;tabli tr&egrave;s prochainement.
_ Merci de votre compr&eacute;hension.",
	'tous' => 'Tous',
	'sauf_admin' => 'Tous, sauf les administrateurs',
	
// ---------------------------------------------------------------------------
	'glossaire:nom' => 'Glossaire interne',
	'glossaire:description' => "Gestion d'un glossaire interne li&#233; &#224; un groupe de mots-cl&#233;s nomm&#233; &quot;Glossaire&quot;.
_ Plus d'infos : [->http://www.spip-contrib.net/?article2206]",

/*
	':nom' => '',
	':description' => '',
*/
);

unset($temp);
?>