<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

// quelques chaines temporaires a traduire
$temp['jQuery1'] = "{{Attention}} : ce tweak n&eacute;cessite le plugin {jQuery} pour fonctionner ou une version de SPIP sup&eacute;rieure &agrave; 1.9.2.";
$temp['jQuery2'] = "Ce tweak utilise la librairie {jQuery}.";
$temp['reset'] = 'R&eacute;initialisation totale du plugin';

// un peu de code : ne pas toucher !
$temp['couleurs'] = '<span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert olive</span>, <span style="background-color:navy;">navy/bleu marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu violet</span>, <span style="background-color:chocolate;">chocolate/brun clair</span>, <span style="background-color:cornsilk;">cornsilk/rose clair</span>, <span style="background-color:darkgreen;">darkgreen/vert fonce</span>, <span style="background-color:darkorange;">darkorange/orange fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune paille</span>, <span style="background-color:yellow;">yellow/jaune</span>';
$temp['jQuery'] = "\n\n" . $GLOBALS['spip_version_code']<1.92?$temp['jQuery1']:$temp['jQuery2'];
$temp['reset'] = $GLOBALS['spip_version_code']<1.92?'<p>['. $temp['reset'] . '->' . parametre_url(self(),'reset','oui') . ']</p>':'';

// traductions habituelles
$GLOBALS[$GLOBALS['idx_lang']] = array(
	'titre' => 'Tweak SPIP',
	'help' => "{{Cette page est uniquement accessible aux responsables du site.}}"
		."<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin 'Tweak&nbsp;SPIP'.</p>"
		."<p>Documentation : [Tweak-SPIP->http://www.spip-contrib.net/Tweak-SPIP]</p>" . $temp['reset'],
	'raccourcis' => "Raccourcis typographiques actifs&nbsp;:",
	'pipelines' => "Pipelines utilis&eacute;s&nbsp;:",
	'nbtweak' => '@pipe@ : @nb@ tweak',
	'nbtweaks' => '@pipe@ : @nb@ tweaks',
	'titre_tests' => 'Tweak SPIP - Page de tests',
	'actif' => 'Tweak actif',
	'inactif' => 'Tweak inactif',
	'actifs' => 'Tweaks actifs :',
	'activer_tweak' => 'Activer le tweak',
	'validez_page' => 'Validez cette page pour acc&eacute;der aux modifications.',
	'modifier_vars' => 'Modifier ces @nb@ param&egrave;tres',
	'variable_vide' => '(Vide)',
	'tweak' => 'Tweak :',
	'tweaks_liste' => 'Liste des tweaks',
	'presente_tweaks' => "Cette page liste les tweaks disponibles.<br />Vous pouvez activer les tweaks n&eacute;cessaires en cochant la case correspondante puis en validant la page.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d'activer les tweaks un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette ou avec d'autres plugin.",
	'erreur:nom' => 'Erreur !',
	'erreur:description' => 'id manquant dans la d&eacute;finition du tweak !',
	'erreur:version' => 'indisponible dans cette version de Spip.',

// categories de tweaks
// --------------------

	'admin' => "1. Administration",
	'typo-corr' => "2. Am&eacute;liorations typographiques",
	'typo-racc' => "3. Raccourcis typographiques",
	'public' => "4. Affichage public",
	'spip' => "5. Balises, filtres, crit&egrave;res",
	'divers' => "6. Divers",
	
// Chaines de langue concernant tous les tweaks configures dans tweak_spip_config.php
// ----------------------------------------------------------------------------------

	'SPIP_cache:nom' => 'SPIP et le cache...',
	'SPIP_cache:description' => "#PUCE Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site.<br />{{D&eacute;sactiver le cache}} : %radio_desactive_cache%\n\n"
		. "#PUCE Le cache occupe un certain espace disque et SPIP peut en limiter l'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu'aucun quota ne s'applique au cache.<br />{{Valeur du quota}} : %quota_cache% Mo",

	'supprimer_numero:nom' => 'Supprime le num&eacute;ro',
	'supprimer_numero:description' => "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code><br />Attention, cette fonctionnalit&eacute; ne sera pas prise en compte si votre squelette utilise les balises &eacute;toil&eacute;es : <code>#TITRE*</code> ou <code>#NOM*</code>",

	'paragrapher:nom' => 'Paragrapher',
	'paragrapher:description' => "Applique la fonction SPIP paragrapher() aux textes qui sont d&eacute;pourvus de paragraphes en ins&eacute;rant des balises &lt;p&gt; et &lt;/p&gt;. Utile pour visualiser tous les textes sans style.",

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
	'pucesli:description' => 'Remplace les puces - (tiret) des articles par des puces -* (&lt;li>&lt;ul>...&lt;/li>&lt;/ul>)',

	'decoration:nom' => 'D&eacute;coration',
	'decoration:description' => "7 nouveaux styles dans vos articles : <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote>, <surfluo>fluo</surfluo> et <surgris>gris&eacute;</surgris>. Utilisation :
-* {&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
-* {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
-* {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
-* {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
-* {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
-* {&lt;surfluo&gt;}Lorem ipsum dolor sit amet{&lt;/surfluo&gt;}
-* {&lt;surgris&gt;}Lorem ipsum dolor sit amet{&lt;/surgris&gt;}

Plus d'infos : [->http://www.spip-contrib.net/?article1552]",
	'decoration:aide' => 'D&eacute;coration&nbsp;: <strong>&lt;balise&gt;test&lt;/balise&gt;</strong>, avec <strong>balise</strong> = @liste@',
	
	'couleurs:nom' => 'Tout en couleurs',
	'couleurs:description' => "Permet d'appliquer facilement des couleurs &agrave; tous les textes du site (articles, br&egrave;ves, titres, forum, ...) en utilisant des balises en raccourcis.
_ Deux  exemples identiques : 
-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>
-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.

34 couleurs sont disponibles (en fran&ccedil;ais ou en anglais) : " . $temp['couleurs'],
	'couleurs:aide' => 'Couleurs : <strong>[couleur]texte[/couleur]</strong>, avec <strong>couleur</strong> = @liste@',

	'typo_exposants:nom' => 'Exposants typographiques',
	'typo_exposants:description' => "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).
_ Plus d'infos : [->http://www.spip-contrib.net/?article1564]",

	'filets_sep:nom' => 'Filets de S&eacute;paration',
	'filets_sep:description' =>  "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de Spip.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d'une image plac&eacute;e dans le dossier plugins/tweak_spip/img/filets.
_ Plus d'infos : [->http://www.spip-contrib.net/?article1563]",
	'filets_sep:aide' => 'Filets de S&eacute;paration&nbsp;: <strong>__i__</strong> o&ugrave; <strong>i</strong> est un nombre.<br />Autres filets disponibles : @liste@',

	'smileys:nom' => 'Smileys',
	'smileys:description' => "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Plus d'infos : [->http://www.spip-contrib.net/?article1561]
_ Dessins : [Sylvain Michel->http://www.guaph.net/]",
	'smileys:aide' => 'Smileys : @liste@',

	'dossier_squelettes:nom' => 'Dossier du squelette',
	'dossier_squelettes:description' => "Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&#8220;:&#8221;</html>. En laissant vide la case qui suit, c'est le squelette original &quot;dist&quot; fourni par Spip qui sera utilis&eacute;.<br />Dossier(s) &agrave; utiliser : %dossier_squelettes%",

	'chatons:nom' => 'Chatons',
	'chatons:description' => 'Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <code>:nom</code>.
_ Ce tweak remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire plugins/tweak_spip/img/chatons.',
	'chatons:aide' => 'Chatons : @liste@',

	'guillemets:nom' => 'Guillemets typographiques',
	'guillemets:description' => 'Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',

	'set_options:nom' => "Type d'interface priv&eacute;e",
	'set_options:description' => "S&eacute;lectionne d'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.<br />Votre choix : %radio_set_options3%",

	'type_urls:nom' => "Format des URLs",
	'type_urls:description' => "Spip offre un choix sur plusieurs jeux d'URLs pour acc&eacute;der aux pages de votre site :
- {page} : la valeur par d&eacute;faut pour Spip v1.9 : <code>/spip.php?article123</code>.
- {html} : les liens ont la forme des pages html classiques : <code>/article123.html</code>.
- {propre} : les liens sont calcul&eacute;s gr&acirc;ce au titre: <code>/Mon-titre-d-article</code>.
- {propres2} : l'extension '.html' est ajout&eacute;e aux adresses g&eacute;n&eacute;r&eacute;es : <code>/Mon-titre-d-article.html</code>.
- {standard} : URLs utilis&eacute;es par Spip v1.8 et pr&eacute;c&eacute;dentes : <code>article.php3?id_article=123</code>
- {propres-qs} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont de la forme : <code>/?Mon-titre-d-article</code>.

{{Attention}} : pour utiliser les formats {html}, {propre} ou {propre2}, Recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site Spip sous
  le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier.
  Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de Spip.<br />Votre choix : %radio_type_urls3%",

	'log_tweaks:nom' => 'Log d&eacute;taill&eacute; de Tweak Spip',
	'log_tweaks:description' => "Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin 'Tweak Spip' dans les fichiers spip.log que l'on peut trouver dans le r&eacute;pertoire : ".tweak_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP),

	'cookie_prefix:nom' => 'Pr&eacute;fixe des cookies',
	'cookie_prefix:description' => 'Sp&eacute;cifie le pr&eacute;fixe &agrave; donner aux cookies de ce site. Utile pour installer des sites SPIP dans des sous-r&eacute;pertoires.<br />Votre choix : %cookie_prefix%',

	'filtrer_javascript:nom' => 'Gestion du javascript',
	'filtrer_javascript:description' => 'Pour g&eacute;rer le javascript dans les articles, trois modes sont disponibles :
- <i>jamais</i> : le javascript est refus&eacute; partout
- <i>d&eacute;faut</i> : le javascript est signal&eacute; en rouge dans l\'espace priv&eacute;
- <i>toujours</i> : le javascript est accept&eacute; partout.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <strong>toujours</strong> s&eacute;curis&eacute;e.<br />Votre choix : %radio_filtrer_javascript3%',
	'js_jamais' => 'Jamais',
	'js_defaut' => 'D&eacute;faut',
	'js_toujours' => 'Toujours',

	'suivi_forums:nom' => 'Suivi des forums',
	'suivi_forums:description' => 'Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.<br />Votre choix : %radio_suivi_forums3%',
	'sf_tous' => 'Tous',
	'sf_defaut' => 'Par d&eacute;faut',
	'sf_amont' => 'En amont',

	'xml:nom' =>'Validateur XML',
	'xml:description' =>"Active le validateur xml pour l'espace public tel qu'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html].",

	'f_jQuery:nom' => 'D&eacute;sactive jQuery',
	'f_jQuery:description' => "Emp&ecirc;che l'installation de jQuery dans la partie publique. Cette librairie ([->http://jquery.com/]) apporte de nombreuses commodit&eacute;s dans la programmation de Javascript et peut &ecirc;tre utilis&eacute;e par certains plugins. Spip l'utilise dans sa partie priv&eacute;e.",

	'SPIP_liens:nom' => 'SPIP et les liens...',
	'SPIP_liens:description' => "#PUCE Tous les liens du site s'ouvrent par d&eacute;faut dans la fen&ecirc;tre de navigation en cours. Mais il peut &ecirc;tre utile d'ouvrir liens externes au site dans une nouvelle fen&ecirc;tre ext&eacute;rieure -- cela revient &agrave; ajouter {target=&quot;_blank&quot;} &agrave; toutes les balises &lt;a&gt; dot&eacute;es par Spip des classes {spip_out}, {spip_url} ou {spip_glossaire}. Il est parfois n&eacute;cessaire d'ajouter l'une de ces classes aux liens du squelette du site (fichiers html) afin d'&eacute;tendre au maximum cette fonctionnalit&eacute;."
		. $temp['jQuery'] . /*"<br />Lien de test : [Google->http://www.google.com]".*/ "<br />{{Utiliser des liens externes}} : %radio_target_blank3%\n\n"
		. "#PUCE Spip permet de relier des mots &agrave; leur d&eacute;finition gr&acirc;ce au raccourci typographique <code>[?mot]</code>. Par d&eacute;faut (ou si vous laissez vide la case ci-dessous), le glossaire externe renvoie vers l&rsquo;encyclop&eacute;die libre wikipedia.org. &Agrave; vous de choisir l'adresse &agrave; utiliser. <br />Lien de test : [?SPIP]<br />{{Lien vers le glossaire}} : %url_glossaire_externe%",

	'forum_lgrmaxi:nom' => 'Taille des forums',
	'forum_lgrmaxi:description' => "Par d&eacute;faut les messages de forum ne sont pas limit&eacute;s en taille. Si ce tweak est activ&eacute;, un message d'erreur s'affichera lorsque quelqu'un voudra poster un message  d'une taille sup&eacute;rieure &agrave; la valeur sp&eacute;cifi&eacute;e, et le message sera refus&eacute;. Une valeur vide ou &eacute;gale &agrave; 0 signifie n&eacute;amoins qu'aucune limite ne s'applique.<br />Valeur (en caract&egrave;res) : %forum_lgrmaxi%",

	'suite_introduction:nom' => 'Points de suite pour #INTRODUCTION',
	'suite_introduction:description' => "Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION  sont : '&amp;nbsp;(...)'. Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin d'introduire les articles, les br&egrave;ves, etc.. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant &agrave; l'utilisateur que le texte tronqu&eacute; a une suite.<br />Valeur : %suite_introduction%",

	'class_spip:nom' => 'SPIP et les styles...',
	'class_spip:description' => "<p>Jusqu'&agrave; la version 1.92 de Spip, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style &quot;spip&quot;. Par exemple : <code><p class=\"spip\"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu'aucun style particulier ne sera appliqu&eacute;."
	."<br />#PUCE {{Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :}}<br />Votre style : %style_p%"
	."<br />#PUCE {{Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt; et &lt;blockquote&gt; :}}<br />Votre style : %style_h%"
	."<br />Attention : en modifiant ce deuxi&egrave;me param&egrave;tre, vous perdez alors les css standards associ&eacute;s &agrave; ces raccourcis.</p>"
	."<p>Pour afiner un peu, vous pouvez maintenant d&eacute;finir le style particulier des balises suivantes :</p>",

	'decoupe:nom' => 'D&eacute;coupe un article en pages',
	'decoupe:description' => "D&eacute;coupe l'affichage public d'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l'endroit qui doit recevoir la coupure.<br />Attention, cette pagination ne sera pas prise en compte si votre squelette utilise la balise #TEXTE &eacute;toil&eacute;e : <code>#TEXTE*</code>. Ce tweak peut &ecirc;tre coupl&eacute; avec {Sommaire en d&eacute;but d'article}",
	'decoupe:aide' => 'S&eacute;parateur de pages&nbsp;: @sep@',
	'page_suivante' => 'Page suivante',
	'page_precedente' => 'Page pr&eacute;c&eacute;dente',
	'page_debut' => 'Premi&egrave;re page',
	'page_fin' => 'Derni&egrave;re page',

	'sommaire:nom' => 'Sommaire en d&eacute;but d\'article',
	'sommaire:description' => "Construit syst&eacute;matiquement un sommaire en d&eacute;but d&rsquo;article afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML <code><h3>Un titre</h3></code> ou raccourci SPIP <code>{{{Un autre titre}}}</code>. Afin d'&eacute;viter l'insertion automatique du sommaire, il vous suffit de placerla balise <code>[!sommaire]</code> &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article (n&rsquo;importe o&ugrave;).<br />Attention, le sommaire ne sera pas construit si votre squelette utilise la balise #TEXTE &eacute;toil&eacute;e : <code>#TEXTE*</code>. Ce tweak peut &ecirc;tre coupl&eacute; avec {D&eacute;coupe un article en pages}",
	'sommaire:aide' => 'Un article sans sommaire&nbsp;: @interdit@',
	'sommaire' => 'Sommaire',

/*
	':nom' => '',
	':description' => '',
*/
);

unset($temp);
?>