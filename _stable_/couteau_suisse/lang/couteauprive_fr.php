<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_stable_/couteau_suisse/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// 2
	'2pts_non' => '&nbsp;:&nbsp;non',
	'2pts_oui' => '&nbsp;:&nbsp;oui',

	// S
	'SPIP_liens:description' => '@puce@ Tous les liens du site s\'ouvrent par d&eacute;faut dans la fen&ecirc;tre de navigation en cours. Mais il peut &ecirc;tre utile d\'ouvrir les liens externes au site dans une nouvelle fen&ecirc;tre ext&eacute;rieure -- cela revient &agrave; ajouter {target=&quot;_blank&quot;} &agrave; toutes les balises &lt;a&gt; dot&eacute;es par SPIP des classes {spip_out}, {spip_url} ou {spip_glossaire}. Il est parfois n&eacute;cessaire d\'ajouter l\'une de ces classes aux liens du squelette du site (fichiers html) afin d\'&eacute;tendre au maximum cette fonctionnalit&eacute;.[[%radio_target_blank3%]]

@puce@ SPIP permet de relier des mots &agrave; leur d&eacute;finition gr&acirc;ce au raccourci typographique <code>[?mot]</code>. Par d&eacute;faut (ou si vous laissez vide la case ci-dessous), le glossaire externe renvoie vers l&rsquo;encyclop&eacute;die libre wikipedia.org. &Agrave; vous de choisir l\'adresse &agrave; utiliser. <br />Lien de test : [?SPIP][[%url_glossaire_externe2%]]',
	'SPIP_liens:nom' => 'SPIP et les liens&hellip; externes',

	// A
	'acces_admin' => 'Acc&egrave;s administrateurs :',
	'auteur_forum:description' => 'Incite tous les auteurs de messages publics &agrave; remplir (d\'au moins d\'une lettre !) le champ &laquo;@_CS_FORUM_NOM@&raquo; afin d\'&eacute;viter les contributions totalement anonymes.',
	'auteur_forum:nom' => 'Pas de forums anonymes',
	'auteurs:description' => 'Cet outil configure l\'apparence de [la page des auteurs->./?exec=auteurs], en partie priv&eacute;e.

@puce@ D&eacute;finissez ici le nombre maximal d\'auteurs &agrave; afficher sur le cadre central de la page des auteurs. Au-del&agrave;, une pagination est mise en place.[[%max_auteurs_page%]]

@puce@ Quels statuts d\'auteurs peuvent &ecirc;tre list&eacute;s sur cette page ?
[[%auteurs_tout_voir%[[->%auteurs_0%]][[->%auteurs_1%]][[->%auteurs_5%]][[->%auteurs_6%]][[->%auteurs_n%]]]]',
	'auteurs:nom' => 'Page des auteurs',

	// B
	'basique' => 'Basique',
	'blocs:aide' => 'Blocs D&eacute;pliables : <b>&lt;bloc&gt;&lt;/bloc&gt;</b> (alias : <b>&lt;invisible&gt;&lt;/invisible&gt;</b>) et <b>&lt;visible&gt;&lt;/visible&gt;</b>',
	'blocs:description' => 'Vous permet  de cr&eacute;er des blocs dont le titre cliquable peut les rendre visibles ou invisibles.

@puce@ {{Dans les textes SPIP}} : les r&eacute;dacteurs ont &agrave; disposition les  nouvelles balises &lt;bloc&gt; (ou &lt;invisible&gt;) et &lt;visible&gt; &agrave; utiliser dans leurs textes comme ceci : 

<quote><code>
<bloc>
 Un titre qui deviendra cliquable
 
 Le texte a cacher/montrer, apres deux sauts de ligne...
 </bloc>
</code></quote>

@puce@ {{Dans les squelettes}} : vous avez &agrave; votre disposition les  nouvelles balises #BLOC_TITRE, #BLOC_DEBUT et #BLOC_FIN &agrave; utiliser comme ceci : 
<quote><code> #BLOC_TITRE ou #BLOC_TITRE{mon_URL}
 Mon titre
 #BLOC_RESUME    (facultatif)
 une version resumee du bloc suivant
 #BLOC_DEBUT
 Mon bloc depliable (qui contiendra l\'URL pointee si necessaire)
 #BLOC_FIN</code></quote>
 
@puce@ En cochant &laquo;oui&raquo; ci-dessous, l\'ouverture d\'un bloc provoquera la fermeture de tous les autres blocs de la page, afin d\'en avoir qu\'un seul ouvert &agrave; la fois.[[%bloc_unique%]]
',
	'label:bloc_unique' => 'Un seul bloc ouvert sur la page :',
	'blocs:nom' => 'Blocs D&eacute;pliables',
	'boites_privees:description' => 'Toutes les bo&icirc;tes d&eacute;crites ci-dessous apparaissent dans la partie priv&eacute;e.[[%cs_rss%]][[->%format_spip%]][[->%stat_auteurs%]]
- {{Les r&eacute;visions du Couteau Suisse}} : un cadre sur la pr&eacute;sente page de configuration, indiquant les derni&egrave;res modifications apport&eacute;es au code du plugin ([Source->@_CS_RSS_SOURCE@]).
- {{Les articles au format SPIP}} : un cadre repliable suppl&eacute;mentaire pour vos articles afin de conna&icirc;tre le code source utilis&eacute; par leurs auteurs.
- {{Les auteurs en stat}} : un cadre suppl&eacute;mentaires sur [la page des auteurs->./?exec=auteurs] indiquant les 10 derniers connect&eacute;s et les inscriptions non confirm&eacute;es. Seuls les administrateurs voient ces informations.',
	'boites_privees:nom' => 'Bo&icirc;tes priv&eacute;es',

	// C
	'categ:admin' => '1. Administration',
	'categ:divers' => '60. Divers',
	'categ:interface' => '10. Interface priv&eacute;e',
	'categ:public' => '40. Affichage public',
	'categ:spip' => '50. Balises, filtres, crit&egrave;res',
	'categ:typo-corr' => '20. Am&eacute;liorations des textes',
	'categ:typo-racc' => '30. Raccourcis typographiques',
	'certaines_couleurs' => 'Seules les balises d&eacute;finies ci-dessous@_CS_ASTER@ :',
	'chatons:aide' => 'Chatons : @liste@',
	'chatons:description' => 'Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <code>:nom</code>.
_ Cet outil remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire plugins/couteau_suisse/img/chatons.',
	'chatons:nom' => 'Chatons',
	'class_spip:description1' => 'Vous pouvez ici d&eacute;finir certains raccourcis de SPIP. Une valeur vide &eacute;quivaut &agrave; utiliser la valeur par d&eacute;faut.[[%racc_hr%]]',
	'class_spip:description2' => '@puce@ {{Les raccourcis de SPIP}}.

Vous pouvez ici d&eacute;finir certains raccourcis de SPIP. Une valeur vide &eacute;quivaut &agrave; utiliser la valeur par d&eacute;faut.[[%racc_hr%]][[%puce%]]',
	'class_spip:description3' => '

{Attention : si l\'outil &laquo;&nbsp;Belles puces&nbsp;&raquo; est activ&eacute;, le remplacement du tiret &laquo;&nbsp;-&nbsp;&raquo; ne sera plus effectu&eacute;&nbsp;; une liste &lt;ul>&lt;li> sera utilis&eacute;e &agrave; la place.}

SPIP utilise habituellement la balise &lt;h3&gt; pour les intertitres. Choisissez ici un autre remplacement :[[%racc_h1%]][[->%racc_h2%]]',
	'class_spip:description4' => '

SPIP a choisi d\'utiliser la balise &lt;strong> pour transcrire les gras. Mais &lt;b> aurait pu &eacute;galement convenir, avec ou sans style. &Agrave; vous de voir :[[%racc_g1%]][[->%racc_g2%]]

SPIP a choisi d\'utiliser la balise &lt;i> pour transcrire les italiques. Mais &lt;em> aurait pu &eacute;galement convenir, avec ou sans style. &Agrave; vous de voir :[[%racc_i1%]][[->%racc_i2%]]

@puce@ {{Les styles de SPIP par d&eacute;faut}}. Jusqu\'&agrave; la version 1.92 de SPIP, les raccourcis typographiques produisaient des balises syst&eacute;matiquement affubl&eacute;s du style &quot;spip&quot;. Par exemple : <code><p class="spip"></code>. Vous pouvez ici d&eacute;finir le style de ces balises en fonction de vos feuilles de style. Une case vide signifie qu\'aucun style particulier ne sera appliqu&eacute;.

{Attention : si certains raccourcis (ligne horizontale, intertitre, italique, gras) ont &eacute;t&eacute; modifi&eacute;s ci-dessus, alors les styles ci-dessous ne seront pas appliqu&eacute;s.}

<q1>
_ {{1.}} Balises &lt;p&gt;, &lt;i&gt;, &lt;strong&gt; :[[%style_p%]]
_ {{2.}} Balises &lt;tables&gt;, &lt;hr&gt;, &lt;h3&gt;, &lt;blockquote&gt; et les listes (&lt;ol&gt;, &lt;ul&gt;, etc.) :[[%style_h%]]

Notez bien : en modifiant ce deuxi&egrave;me style, vous perdez alors les styles standards de SPIP associ&eacute;s &agrave; ces balises.</q1>',
	'class_spip:nom' => 'SPIP et ses raccourcis&hellip;',
	'code_css' => 'CSS',
	'code_fonctions' => 'Fonctions',
	'code_jq' => 'jQuery',
	'code_js' => 'Javascript',
	'code_options' => 'Options',
	'code_spip_options' => 'Options SPIP',
	'contrib' => 'Plus d\'infos : @url@',
	'couleurs:aide' => 'Mise en couleurs : <b>[coul]texte[/coul]</b>@fond@ avec <b>coul</b> = @liste@',
	'couleurs:description' => 'Permet d\'appliquer facilement des couleurs &agrave; tous les textes du site (articles, br&egrave;ves, titres, forum, &hellip;) en utilisant des balises en raccourcis.

Deux exemples identiques pour changer la couleur du texte :@_CS_EXEMPLE_COULEURS2@

Idem pour changer le fond, si l\'option ci-dessous le permet :@_CS_EXEMPLE_COULEURS3@

[[%couleurs_fonds%]]
[[%set_couleurs%]][[->%couleurs_perso%]]
@_CS_ASTER@Le format de ces balises personnalis&eacute;es doit lister des couleurs existantes ou d&eacute;finir des couples &laquo;balise=couleur&raquo;, le tout s&eacute;par&eacute; par des virgules. Exemples : &laquo;gris, rouge&raquo;, &laquo;faible=jaune, fort=rouge&raquo;, &laquo;bas=#99CC11, haut=brown&raquo; ou encore &laquo;gris=#DDDDCC, rouge=#EE3300&raquo;. Pour le premier et le dernier exemple, les balises autoris&eacute;es sont : <code>[gris]</code> et <code>[rouge]</code> (<code>[fond gris]</code> et <code>[fond rouge]</code> si les fonds sont permis).',
	'couleurs:nom' => 'Tout en couleurs',
	'couleurs_fonds' => ', <b>[fond&nbsp;coul]texte[/coul]</b>, <b>[bg&nbsp;coul]texte[/coul]</b>',

	// D
	'decoration:aide' => 'D&eacute;coration&nbsp;: <b>&lt;balise&gt;test&lt;/balise&gt;</b>, avec <b>balise</b> = @liste@',
	'decoration:description' => 'De nouveaux styles param&eacute;trables dans vos textes et accessibles gr&acirc;ce &agrave; des balises &agrave; chevrons. Exemple : 
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
	'decoration:nom' => 'D&eacute;coration',
	'decoupe:aide' => 'Bloc d\'onglets : <b>&lt;onglets>&lt;/onglets></b><br/>S&eacute;parateur de pages ou d\'onglets&nbsp;: @sep@',
	'decoupe:aide2' => 'Alias&nbsp;:&nbsp;@sep@',
	'decoupe:description' => '@puce@ D&eacute;coupe l\'affichage public d\'un article en plusieurs pages gr&acirc;ce &agrave; une pagination automatique. Placez simplement dans votre article quatre signes plus cons&eacute;cutifs (<code>++++</code>) &agrave; l\'endroit qui doit recevoir la coupure.

Par d&eacute;faut, le Couteau Suisse ins&egrave;re la pagination en t&ecirc;te et en pied d\'article automatiquement. Mais vous avez la possibilt&eacute; de placer cette pagination ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_DECOUPE que vous pouvez activer ici :
[[%balise_decoupe%]]

@puce@ Si vous utilisez ce s&eacute;parateur &agrave; l\'int&eacute;rieur des balises &lt;onglets&gt; et &lt;/onglets&gt; alors vous obtiendrez un jeu d\'onglets.

Dans les squelettes : vous avez &agrave; votre disposition les nouvelles balises #ONGLETS_DEBUT, #ONGLETS_TITRE et #ONGLETS_FIN.

Cet outil peut &ecirc;tre coupl&eacute; avec {Un sommaire pour vos articles}.',
	'decoupe:nom' => 'D&eacute;coupe en pages et onglets',
	'desactiver_flash:description' => 'Supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.',
	'desactiver_flash:nom' => 'D&eacute;sactive les objets flash',
	'detail_balise_etoilee' => '{{Attention}} : V&eacute;rifiez bien l\'utilisation faite par vos squelettes des balises &eacute;toil&eacute;es. Les traitements de cet outil ne s\'appliqueront pas sur : @bal@.',
	'detail_fichiers' => 'Fichiers :',
	'detail_inline' => 'Code inline :',
	'detail_jquery1' => '{{Attention}} : cet outil n&eacute;cessite le plugin {jQuery} pour fonctionner avec cette version de SPIP.',
	'detail_jquery2' => 'Cet outil n&eacute;cessite la librairie {jQuery}.',
	'detail_jquery3' => '{{Attention}} : cet outil n&eacute;cessite le plugin [jQuery pour SPIP 1.92->http://files.spip.org/spip-zone/jquery_192.zip] pour fonctionner correctement avec cette version de SPIP.',
	'detail_pipelines' => 'Pipelines :',
	'detail_traitements' => 'Traitements :',
	'dossier_squelettes:description' => 'Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. Vous pouvez inscrire plusieurs dossiers en les s&eacute;parant par les deux points <html>&laquo;&nbsp;:&nbsp;&raquo;</html>. En laissant vide la case qui suit (ou en tapant &quot;dist&quot;), c\'est le squelette original &quot;dist&quot; fourni par SPIP qui sera utilis&eacute;.[[%dossier_squelettes%]]',
	'dossier_squelettes:nom' => 'Dossier du squelette',

	// E
	'effaces' => 'Effac&eacute;s',
	'en_travaux:description' => 'Permet d\'afficher un message personalisable pendant une phase de maintenance sur tout le site public.
[[%message_travaux%]][[%titre_travaux%]][[%admin_travaux%]]',
	'en_travaux:nom' => 'Site en travaux',
	'erreur:bt' => '<span style=\\"color:red;\\">Attention :</span> la barre typographique (version @version@) semble ancienne.<br />Le Couteau Suisse est compatible avec une version sup&eacute;rieure ou &eacute;gale &agrave; @mini@.',
	'erreur:description' => 'id manquant dans la d&eacute;finition de l\'outil !',
	'erreur:distant' => 'le serveur distant',
	'erreur:jquery' => '{{Note}} : la librairie {jQuery} semble inactive sur cette page. Veuillez consulter [ici->http://www.spip-contrib.net/?article2166] le paragraphe sur les d&eacute;pendances du plugin.',
	'erreur:js' => 'Une erreur JavaScript semble &ecirc;tre survenue sur cette page et emp&ecirc;che son bon fonctionnement. Veuillez activer JavaScript sur votre navigateur ou d&eacute;sactiver certains plugins SPIP de votre site.',
	'erreur:nojs' => 'Le JavaScript est d&eacute;sactiv&eacute; sur cette page.',
	'erreur:nom' => 'Erreur !',
	'erreur:probleme' => 'Probl&egrave;me sur : @pb@',
	'erreur:traitements' => 'Le Couteau Suisse - Erreur de compilation des traitements : m&eacute;lange \'typo\' et \'propre\' interdit !',
	'erreur:version' => 'Cet outil est indisponible dans cette version de SPIP.',
	'etendu' => '&Eacute;tendu',

	// F
	'f_jQuery:description' => 'Emp&ecirc;che l\'installation de {jQuery} dans la partie publique afin d\'&eacute;conmiser un peu de &laquo;temps machine&raquo;. Cette librairie ([->http://jquery.com/]) apporte de nombreuses commodit&eacute;s dans la programmation de Javascript et peut &ecirc;tre utilis&eacute;e par certains plugins. SPIP l\'utilise dans sa partie priv&eacute;e.

Attention : certains outils du Couteau Suisse n&eacute;cessitent les fonctions de {jQuery}. ',
	'f_jQuery:nom' => 'D&eacute;sactive jQuery',
	'filets_sep:aide' => 'Filets de S&eacute;paration&nbsp;: <b>__i__</b> o&ugrave; <b>i</b> est un nombre.<br />Autres filets disponibles : @liste@',
	'filets_sep:description' => 'Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de SPIP.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d\'une image plac&eacute;e dans le dossier plugins/couteau_suisse/img/filets.',
	'filets_sep:nom' => 'Filets de S&eacute;paration',
	'filtrer_javascript:description' => 'Pour g&eacute;rer l\'insertion de javascript dans les articles, trois modes sont disponibles :
- <i>jamais</i> : le javascript est refus&eacute; partout
- <i>d&eacute;faut</i> : le javascript est signal&eacute; en rouge dans l\'espace priv&eacute;
- <i>toujours</i> : le javascript est accept&eacute; partout.

Attention : dans les forums, p&eacute;titions, flux syndiqu&eacute;s, etc., la gestion du javascript est <b>toujours</b> s&eacute;curis&eacute;e.[[%radio_filtrer_javascript3%]]',
	'filtrer_javascript:nom' => 'Gestion du javascript',
	'flock:description' => 'D&eacute;sactive le syst&egrave;me de verrouillage de fichiers en neutralisant la fonction PHP {flock()}. Certains h&eacute;bergements posent en effet des probl&egrave;mes graves suite &agrave; un syst&egrave;me de fichiers inadapt&eacute; ou &agrave; un manque de synchronisation. N\'activez pas cet outil si votre site fonctionne normalement.',
	'flock:nom' => 'Pas de verrouillage de fichiers',
	'fonds' => 'Fonds :',
	'forcer_langue:description' => 'Force le contexte de langue pour les jeux de squelettes multilingues disposant d\'un formulaire ou d\'un menu de langues sachant g&eacute;rer le cookie de langues.',
	'forcer_langue:nom' => 'Forcer langue',
	'format_spip' => 'Les articles au format SPIP',
	'forum_lgrmaxi:description' => 'Par d&eacute;faut les messages de forum ne sont pas limit&eacute;s en taille. Si cet outil est activ&eacute;, un message d\'erreur s\'affichera lorsque quelqu\'un voudra poster un message  d\'une taille sup&eacute;rieure &agrave; la valeur sp&eacute;cifi&eacute;e, et le message sera refus&eacute;. Une valeur vide ou &eacute;gale &agrave; 0 signifie n&eacute;amoins qu\'aucune limite ne s\'applique.[[%forum_lgrmaxi%]]',
	'forum_lgrmaxi:nom' => 'Taille des forums',

	// G
	'glossaire:description' => '@puce@ Gestion d&rsquo;un glossaire interne li&eacute; &agrave; un ou plusieurs groupes de mots-cl&eacute;s. Inscrivez ici le nom des groupes en  les s&eacute;parant par les deux points &laquo;&nbsp;:&nbsp;&raquo;. En laissant vide la case qui  suit (ou en tapant &quot;Glossaire&quot;), c&rsquo;est le groupe &quot;Glossaire&quot; qui sera utilis&eacute;.[[%glossaire_groupes%]]

@puce@ Pour chaque mot, vous avez la possibilit&#233; de choisir le nombre maximal de liens cr&#233;&#233;s dans vos textes. Toute valeur nulle ou n&#233;gative implique que tous les mots reconnus seront trait&#233;s. [[%glossaire_limite% par mot-cl&#233;]]

@puce@ Deux solutions vous sont offertes pour g&#233;n&#233;rer la petite fen&ecirc;tre automatique qui apparait lors du survol de la souris. [[%glossaire_js%]]',
	'glossaire:nom' => 'Glossaire interne',
	'glossaire:aide' => 'Un texte sans glossaire : <b>@_CS_SANS_GLOSSAIRE@</b>',
	'glossaire_css' => 'Solution CSS',
	'glossaire_js' => 'Solution Javascript',
	'guillemets:description' => 'Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte original mais seulement l\'affichage final.',
	'guillemets:nom' => 'Guillemets typographiques',

	// H
	'help' => '{{Cette page est uniquement accessible aux responsables du site.}} Elle permet la configuration des diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.',
	'help0' => '{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin &laquo;{{Le&nbsp;Couteau&nbsp;Suisse}}&raquo;.</p><p>Lien de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]</p><p>R&eacute;initialisation :
_ &bull; [De tout le plugin->@reset@]
</p>',
	'help2' => 'Version locale : @version@',
	'help3' => '<p>Liens de documentation :<br/>&bull; [Le&nbsp;Couteau&nbsp;Suisse->http://www.spip-contrib.net/?article2166]@contribs@</p><p>R&eacute;initialisations :
_ &bull; [Des outils cach&eacute;s|Revenir &agrave; l\'apparence initiale de cette page->@hide@]
_ &bull; [De tout le plugin|Revenir &agrave; l\'&eacute;tat initial du plugin->@reset@]@install@
</p>',

	// I
	'icone_visiter:description' => 'Remplace l\'image du bouton standard &laquo;&nbsp;Visiter&nbsp;&raquo; (en haut &agrave; droite sur cette page)  par le logo du site, s\'il existe.

Pour d&eacute;finir ce logo, rendez-vous sur la page &laquo;&nbsp;Configuration du site&nbsp;&raquo; en cliquant sur le bouton &laquo;&nbsp;Configuration&nbsp;&raquo;.',
	'icone_visiter:nom' => 'Bouton &laquo;&nbsp;Visiter&nbsp;&raquo;',
	'insert_head:description' => 'Active automatiquement la balise [#INSERT_HEAD->http://www.spip.net/fr_article1902.html] sur tous les squelettes, qu\'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).',
	'insert_head:nom' => 'Balise #INSERT_HEAD',
	'insertions:description' => 'ATTENTION : outil en cours de d&eacute;veloppement !! [[%insertions%]]',
	'insertions:nom' => 'Corrections automatiques',
	'introduction:description' => 'Cette balise &agrave; placer dans les squelettes sert en g&eacute;n&eacute;ral &agrave; la une ou dans les rubriques afin de produire un r&eacute;sum&eacute; des articles, des br&egrave;ves, etc..</p>
<p>{{Attention}} : Avant d\'activer cette fonctionnalit&eacute;, v&eacute;rifiez bien qu\'aucune fonction {balise_INTRODUCTION()} n\'existe d&eacute;j&agrave; dans votre squelette ou vos plugins, la surcharge produirait alors une erreur de compilation.</p>
@puce@ Vous pouvez pr&eacute;ciser (en pourcentage par rapport &agrave; la valeur utilis&eacute;e par d&eacute;faut) la longueur du texte renvoy&eacute; par balise #INTRODUCTION. Une valeur nulle ou &eacute;gale &agrave; 100 ne modifie pas l\'aspect de l\'introduction et utilise donc les valeurs par d&eacute;faut suivantes : 500 caract&egrave;res pour les articles, 300 pour les br&egrave;ves et 600 pour les forums ou les rubriques.
[[%lgr_introduction%&nbsp;%]]
@puce@ Par d&eacute;faut, les points de suite ajout&eacute;s au r&eacute;sultat de la balise #INTRODUCTION si le texte est trop long sont : <html>&laquo;&amp;nbsp;(&hellip;)&raquo;</html>. Vous pouvez ici pr&eacute;ciser votre propre cha&icirc;ne de carat&egrave;re indiquant au lecteur que le texte tronqu&eacute; a bien une suite.
[[%suite_introduction%]]
@puce@ Si la balise #INTRODUCTION est utilis&eacute;e pour r&eacute;sumer un article, alors le Couteau Suisse peut fabriquer un lien hypertexte sur les points de suite d&eacute;finis ci-dessus afin de mener le lecteur vers le texte original. Par exemple : &laquo;Lire la suite de l\'article&hellip;&raquo;
[[%lien_introduction%]]
',
	'introduction:nom' => 'Balise #INTRODUCTION',

	// J
	'jcorner:description' => '&laquo;&nbsp;Jolis Coins&nbsp;&raquo; est un outil permettant de modifier facilement l\'aspect des coins de vos {{cadres color&eacute;s}} en partie publique de votre site. Tout est possible, ou presque !
_ Voyez le r&eacute;sultat sur cette page : [->http://www.malsup.com/jquery/corner/].

Listez ci-dessous les objets de votre squelette &agrave; arrondir en utilisant la syntaxe CSS (.class, #id, etc. ). Utilisez le le signe &laquo;&nbsp;=&nbsp;&raquo; pour sp&eacute;cifier la commande jQuery &agrave; utiliser et un double slash (&laquo;&nbsp;//&nbsp;&raquo;) pour les commentaires. En absence du signe &eacute;gal, des coins ronds seront appliqu&eacute;s (&eacute;quivalent &agrave; : <code>.ma_classe = .corner()</code>).[[%jcorner_classes%]]

Attention, cet outil a besoin pour fonctionner du plugin {jQuery} : {Round Corners}. Le Couteau Suisse peut l\'installer directement si vous cochez la case suivante. [[%jcorner_plugin%]]',
	'jcorner:nom' => 'Jolis Coins',
	'jcorner_plugin' => '&laquo;&nbsp;Round Corners plugin&nbsp;&raquo;',
	'js_defaut' => 'D&eacute;faut',
	'js_jamais' => 'Jamais',
	'js_toujours' => 'Toujours',

	// L
	'label:admin_travaux' => 'Fermer le site public pour :',
	'label:auteurs_tout_voir' => '@_CS_CHOIX@',
	'label:auto_sommaire' => 'Cr&eacute;ation syst&eacute;matique du sommaire :',
	'label:balise_decoupe' => 'Activer la balise #CS_DECOUPE :',
	'label:balise_sommaire' => 'Activer la balise #CS_SOMMAIRE :',
	'label:couleurs_fonds' => 'Permettre les fonds :',
	'label:cs_rss' => 'Activer :',
	'label:decoration_styles' => 'Vos balises de style personnalis&eacute; :',
	'label:derniere_modif_invalide' => 'Recalculer juste apr&egrave;s une modification :',
	'label:dossier_squelettes' => 'Dossier(s) &agrave; utiliser :',
	'label:duree_cache' => 'Dur&eacute;e du cache local :',
	'label:duree_cache_mutu' => 'Dur&eacute;e du cache en mutualisation :',
	'label:forum_lgrmaxi' => 'Valeur (en caract&egrave;res) :',
	'label:glossaire_groupes' => 'Groupe(s) utilis&eacute;(s) :',
	'label:glossaire_js' => 'Technique utilis&eacute;e :',
	'label:glossaire_limite' => 'Nombre maximal de liens cr&#233;&#233;s :',
	'label:insertions' => 'Corrections automatiques :',
	'label:jcorner_classes' => 'Am&eacute;liorer les coins des s&eacute;lecteurs suivantes :',
	'label:jcorner_plugin' => 'Installer le plugin {jQuery} suivant :',
	'label:lgr_introduction' => 'Longueur du r&eacute;sum&eacute; :',
	'label:lgr_sommaire' => 'Largeur du sommaire (9 &agrave; 99) :',
	'label:lien_introduction' => 'Points de suite cliquables :',
	'label:liens_interrogation' => 'Prot&eacute;ger les URLs :',
	'label:liens_orphelins' => 'Liens cliquables :',
	'label:max_auteurs_page' => 'Auteurs par page :',
	'label:message_travaux' => 'Votre message de maintenance :',
	'label:paragrapher' => 'Toujours paragrapher :',
	'label:puce' => 'Puce publique &laquo;<html>-</html>&raquo; :',
	'label:quota_cache' => 'Valeur du quota :',
	'label:racc_h1' => 'Entr&eacute;e et sortie d\'un &laquo;<html>{{{intertitre}}}</html>&raquo; :',
	'label:racc_hr' => 'Ligne horizontale &laquo;<html>----</html>&raquo; :',
	'label:racc_i1' => 'Entr&eacute;e et sortie de la mise en &laquo;<html>{italique}</html>&raquo; :',
	'label:racc_g1' => 'Entr&eacute;e et sortie de la mise en &laquo;<html>{{gras}}</html>&raquo; :',
	'label:radio_desactive_cache3' => 'Utilisation du cache :',
	'label:radio_desactive_cache4' => 'Utilisation du cache :',
	'label:radio_filtrer_javascript3' => '@_CS_CHOIX@',
	'label:radio_set_options4' => '@_CS_CHOIX@',
	'label:radio_suivi_forums3' => '@_CS_CHOIX@',
	'label:radio_target_blank3' => 'Nouvelle fen&ecirc;tre pour les liens externes :',
	'label:radio_type_urls3' => 'Format des URLs :',
	'label:scrollTo' => 'Installer les plugins {jQuery} suivants :',
	'label:set_couleurs' => 'Set &agrave; utiliser :',
	'label:spam_mots' => 'S&eacute;quences interdites :',
	'label:spip_script' => 'Script d\'appel :',
	'label:style_h' => 'Votre style :',
	'label:style_p' => 'Votre style :',
	'label:suite_introduction' => 'Points de suite :',
	'label:terminaison_urls_arbo' => 'Terminaison des URls (ex : .html) :',
	'label:terminaison_urls_page' => 'Terminaison des URls (ex : .html) :',
	'label:terminaison_urls_propres' => 'Terminaison des URls (ex : .html) :',
	'label:titre_travaux' => 'Titre du message :',
	'label:tri_articles' => 'Votre choix :',
	'label:url_arbo_minuscules' => 'Conserver la casse des titres dans les URLs :',
	'label:url_arbo_sep_id' => 'Caract&egrave;re de s&eacute;paration \'titre-id\' en cas de doublon :<br/>(ne pas utiliser \'/\')',
	'label:separateur_urls_page' => 'Caract&egrave;re de s&eacute;paration \'type-id\' (ex. : ?article-123) :',
	'label:url_glossaire_externe2' => 'Lien vers le glossaire externe :',
	'label:urls_arbo_sans_type' => 'Afficher le type d\'objet SPIP dans les URLs :',
	'liens_en_clair:description' => 'Met &agrave; votre disposition le filtre : \'liens_en_clair\'. Votre texte contient probablement des liens hypertexte qui ne sont pas visibles lors d\'une impression. Ce filtre ajoute entre crochets la destination de chaque lien cliquable (liens externes ou mails). Attention : en mode impression (parametre \'cs=print\' ou \'page=print\' dans l\'url de la page), cette fonctionnalit&eacute; est appliqu&eacute;e automatiquement.',
	'liens_en_clair:nom' => 'Liens en clair',
	'liens_orphelins:description' => 'Cet outil a deux fonctions :

@puce@ {{Liens corrects}}.

SPIP a pour habitude d\'ins&eacute;rer un espace avant les points d\'interrogation ou d\'exclamation, typo fran&ccedil;aise oblige. Voici un outil qui prot&egrave;ge le point d\'interrogation dans les URLs de vos textes.[[%liens_interrogation%]]

@puce@ {{Liens orphelins}}.

Remplace syst&eacute;matiquement toutes les URLs laiss&eacute;es en texte par les utilisateurs (notamment dans les forums) et qui ne sont donc pas cliquables, par des liens hypertextes au format SPIP. Par exemple : {<html>www.spip.net</html>} est remplac&eacute; par [->www.spip.net].

Vous pouvez choisir le type de remplacement :
_ &bull; {Basique} : sont remplac&eacute;s les liens du type {<html>http://spip.net</html>} (tout protocole) ou {<html>www.spip.net</html>}.
_ &bull; {&Eacute;tendu} : sont remplac&eacute;s en plus les liens du type {<html>moi@spip.net</html>}, {<html>mailto:monmail</html>} ou {<html>news:mesnews</html>}.
[[%liens_orphelins%]]',
	'liens_orphelins:nom' => 'Belles URLs',
	'cs_log_couteau_suisse' => 'Les logs d&eacute;taill&eacute;s du Couteau Suisse',
	'cs_comportement:description' => "@puce@ {{Logs.}} Obtenez de nombreux renseignements &agrave; propos du fonctionnement du Couteau Suisse dans les fichiers {spip.log} que l'on peut trouver dans le r&eacute;pertoire : {@_CS_DIR_TMP@}[[%log_couteau_suisse%]]

@puce@ {{Options SPIP.}} SPIP ordonne les plugins dans un ordre sp&eacute;cifique. Afin d'&ecirc;tre s&ucirc;r que le Couteau Suisse soit en t&ecirc;te et g&egrave;re en amont certaines options de SPIP, alors cochez l'option suivante. Si les droits de votre serveur le permettent, le fichier {@_CS_FILE_OPTIONS@} sera automatiquement modifi&eacute; pour inclure le fichier {@_CS_DIR_TMP@couteau-suisse/mes_spip_options.php}.
[[%spip_options_on%]]

@puce@ {{Requ&ecirc;tes externes.}} Le Couteau Suisse v&eacute;rifie r&eacute;guli&egrave;rement l'existence d'une version plus r&eacute;cente de son code et informe sur sa page de configuration d'une mise &agrave; jour &eacute;ventuellement disponible. Si les requ&ecirc;tes externes de votre serveur posent des probl&egrave;mes, alors cochez la case suivante.[[%distant_off%]]",
	'cs_comportement:nom' => 'Comportements du Couteau Suisse',
	'cs_distant_off' => 'Les v&eacute;rifications de versions distantes',
	'cs_spip_options_on' => 'Les options SPIP dans &laquo;@_CS_FILE_OPTIONS@&raquo;',
	'label:log_couteau_suisse' => 'Activer :',
	'label:spip_options_on' => 'Inclure :',
	'label:distant_off' => 'D&eacute;sactiver :',
	

	// M
	'mailcrypt:description' => 'Masque tous les liens de courriels pr&eacute;sents dans vos textes en les rempla&ccedil;ant par un lien Javascript permettant quand m&ecirc;me d\'activer la messagerie du lecteur. Cet outil antispam tente d\'emp&ecirc;cher les robots de collecter les adresses &eacute;lectroniques laiss&eacute;es en clair dans les forums ou dans les balises de vos squelettes.',
	'mailcrypt:nom' => 'MailCrypt',
	'modifier_vars' => 'Modifier ces @nb@ param&egrave;tres',
	'moderation_moderee:nom' => 'Mod&eacute;ration mod&eacute;r&eacute;e',
	'label:moderation_admin' => 'Valider automatiquement les messages des : ',
	'moderation_moderee:description' => 'Permet de mod&eacute;rer la mod&eacute;ration des forums pour les utilisateurs inscrits. [[%moderation_admin%]][[-->%moderation_redac%]][[-->%moderation_visit%]]',
	'moderation_admins' => 'administrateurs authentifi&eacute;s',
	'moderation_redacs' => 'r&eacute;dacteurs authentifi&eacute;s',
	'moderation_visits' => 'visiteurs authentifi&eacute;s',

	// N
	'no_IP:description' => 'D&eacute;sactive le m&eacute;canisme d\'enregistrement automatique des adresses IP des visiteurs de votre site par soucis de confidentialit&eacute; : SPIP ne conservera alors plus aucun num&eacute;ro IP, ni temporairement lors des visites (pour g&eacute;rer les statistiques ou alimenter spip.log), ni dans les forums (responsabilit&eacute;).',
	'no_IP:nom' => 'Pas de stockage IP',
	'nouveaux' => 'Nouveaux',

	// O
	'orientation:description' => '3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.',
	'orientation:nom' => 'Orientation des images',
	'outil_actif' => 'Outil actif',
	'outil_activer' => 'Activer',
	'outil_activer_le' => 'Activer l\'outil',
	'outil_cacher' => 'Ne plus afficher',
	'outil_desactiver' => 'D&eacute;sactiver',
	'outil_desactiver_le' => 'D&eacute;sactiver l\'outil',
	'outil_inactif' => 'Outil inactif',
	'outil_intro' => 'Cette page liste les fonctionnalit&eacute;s du plugin mises &agrave; votre disposition.<br /><br />En cliquant sur le nom des outils ci-dessous, vous s&eacute;lectionnez ceux dont vous pourrez permuter l\'&eacute;tat &agrave; l\'aide du bouton central : les outils activ&eacute;s seront d&eacute;sactiv&eacute;s et <i>vice versa</i>. &Agrave; chaque clic, la description apparait au-dessous des listes. Les cat&eacute;gories sont repliables et les outils peuvent &ecirc;tre cach&eacute;s. Le double-clic permet de permuter rapidement un outil.<br /><br />Pour une premi&egrave;re utilisation, il est recommand&eacute; d\'activer les outils un par un, au cas o&ugrave; apparaitraient certaines incompatibilit&eacute;s avec votre squelette, avec SPIP ou avec d\'autres plugins.<br /><br />Note : le simple chargement de cette page recompile l\'ensemble des outils du Couteau Suisse.',
	'outil_intro_old' => 'Cette interface est ancienne.<br /><br />Si vous rencontrez des probl&egrave;mes dans l\'utilisation de la <a href=\'./?exec=admin_couteau_suisse\'>nouvelle interface</a>, n\'h&eacute;sitez pas &agrave; nous en faire part sur le forum de <a href=\'http://www.spip-contrib.net/?article2166\'>Spip-Contrib</a>.',
	'outil_nb' => '@pipe@ : @nb@ outil',
	'outil_nbs' => '@pipe@ : @nb@ outils',
	'outil_permuter' => 'Permuter l\'outil : &laquo; @text@ &raquo; ?',
	'outils_actifs' => 'Outils actifs :',
	'outils_caches' => 'Outils cach&eacute;s :',
	'outils_cliquez' => 'Cliquez sur le nom des outils ci-dessus pour afficher ici leur description.',
	'outils_inactifs' => 'Outil inactifs :',
	'outils_liste' => 'Liste des outils du Couteau Suisse',
	'outils_permuter_gras1' => 'Permuter les outils en gras',
	'outils_permuter_gras2' => 'Permuter les @nb@ outils en gras ?',
	'outils_resetselection' => 'R&eacute;initialiser la s&eacute;lection',
	'outils_selectionactifs' => 'S&eacute;lectionner tous les outils actifs',
	'outils_selectiontous' => 'TOUS',

	// P
	'pack_alt' => 'Voir les param&egrave;tres de configuration en cours',
	'pack_descrip' => 'Votre "Pack de configuration actuelle" rassemble l\'ensemble des param&egrave;tres de configuration en cours concernant le Couteau Suisse : l\'activation des outils et la valeur de leurs &eacute;ventuelles variables.

Ce code PHP peut prendre place dans le fichier /config/mes_options.php et ajoutera un lien de r&eacute;initialisation sur cette page "du pack {Pack Actuel}". Bien s&ucirc;r il vous est possible de changer son nom ci-dessous.

Si vous r&eacute;initialisez le plugin en cliquant sur un pack, le Couteau Suisse se reconfigurera automatiquement en fonction des param&egrave;tres pr&eacute;d&eacute;finis dans le pack.',
	'pack_du' => '&bull; du pack @pack@',
	'pack_installe' => 'Mise en place d\'un pack de configuration',
	'pack_titre' => 'Configuration Actuelle',
	'par_defaut' => 'Par d&eacute;faut',
	'paragrapher2:description' => 'La fonction SPIP <code>paragrapher()</code> ins&egrave;re des balises &lt;p&gt; et &lt;/p&gt; dans tous les textes qui sont d&eacute;pourvus de paragraphes. Afin de g&eacute;rer plus finement vos styles et vos mises en page, vous avez la possibilit&eacute; d\'uniformiser l\'aspect des textes de votre site.[[%paragrapher%]]',
	'paragrapher2:nom' => 'Paragrapher',
	'pipelines' => 'Pipelines utilis&eacute;s&nbsp;:',
	'pucesli:description' => 'Remplace les puces &laquo;-&raquo; (tiret simple) des articles par des listes not&eacute;es &laquo;-*&raquo; (traduites en HTML par : &lt;ul>&lt;li>&hellip;&lt;/li>&lt;/ul>) et dont le style peut &ecirc;tre personnalis&eacute; par css.',
	'pucesli:nom' => 'Belles puces',

	// R
	'raccourcis' => 'Raccourcis typographiques actifs du Couteau Suisse&nbsp;:',
	'raccourcis_barre' => 'Les raccourcis typographiques du Couteau Suisse',
	'reserve_admin' => 'Acc&egrave;s r&eacute;serv&eacute; aux administrateurs.',
	'rss_attente' => 'Attente RSS...',
	'rss_desactiver' => 'D&eacute;sactiver les &laquo; R&eacute;visions du Couteau Suisse &raquo;',
	'rss_edition' => 'Flux RSS mis &agrave; jour le :',
	'rss_titre' => '&laquo;&nbsp;Le Couteau Suisse&nbsp;&raquo; en d&eacute;veloppement :',
	'rss_var' => 'Les r&eacute;visions du Couteau Suisse',
	'rss_actualiser' => 'Actualiser',
	'rss_source' => 'Source RSS',

	// S
	'sauf_admin' => 'Tous, sauf les administrateurs',
	'set_options:description' => 'S&eacute;lectionne d\'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.[[%radio_set_options4%]]',
	'set_options:nom' => 'Type d\'interface priv&eacute;e',
	'sf_amont' => 'En amont',
	'sf_tous' => 'Tous',
	'simpl_interface:description' => 'D&eacute;sactive le menu de changement rapide de statut d\'un article au survol de sa puce color&eacute;e. Cela est utile si vous cherchez &agrave; obtenir une interface priv&eacute;e la plus d&eacute;pouill&eacute;e possible afin d\'optimiser les performances client.',
	'simpl_interface:nom' => 'All&egrave;gement de l\'interface priv&eacute;e',
	'smileys:aide' => 'Smileys : @liste@',
	'smileys:description' => 'Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Une balise est disponible pour aficher un tableau de smileys dans vos squelettes : #SMILEYS.
_ Dessins : [Sylvain Michel->http://www.guaph.net/]',
	'smileys:nom' => 'Smileys',
	'soft_scroller:description' => 'Offre &agrave; votre site public un d&eacute;filement  adouci de la page lorsque le visiteur clique sur un lien pointant vers une ancre : tr&egrave;s utile pour &eacute;viter de se perdre dans une page complexe ou un texte tr&egrave;s long...

Attention, cet outil a besoin pour fonctionner de pages au &laquo;DOCTYPE XHTML&raquo; (non HTML !) et de deux plugins {jQuery} : {ScrollTo} et {LocalScroll}. Le Couteau Suisse peut les installer directement si vous cochez les cases suivantes. [[%scrollTo%]][[-->%LocalScroll%]]
@_CS_PLUGIN_JQUERY192@',
	'soft_scroller:nom' => 'Ancres douces',
	'jq_scrollTo' => 'jQuery.ScrollTo ([d&eacute;mo->http://demos.flesler.com/jquery/scrollTo/])',
	'jq_localScroll' => 'jQuery.LocalScroll ([d&eacute;mo->http://demos.flesler.com/jquery/localScroll/])',
	'sommaire:description' => 'Construit un sommaire pour le texte de vos articles et de vos rubriques afin d&rsquo;acc&eacute;der rapidement aux gros titres (balises HTML &lt;h3>Un intertitre&lt;/h3> ou raccourcis SPIP : intertitres de la forme :<code>{{{Un gros titre}}}</code>).

@puce@ Vous pouvez d&eacute;finir ici le nombre maximal de caract&egrave;res retenus des intertitres pour construire le sommaire :[[%lgr_sommaire% caract&egrave;res]]

@puce@ Vous pouvez aussi fixer le comportement du plugin concernant la cr&eacute;ation du sommaire: 
_ &bull; Syst&eacute;matique pour chaque article (une balise <code>@_CS_SANS_SOMMAIRE@</code> plac&eacute;e n&rsquo;importe o&ugrave; &agrave; l&rsquo;int&eacute;rieur du texte de l&rsquo;article cr&eacute;era une exception).
_ &bull; Uniquement pour les articles contenant la balise <code>@_CS_AVEC_SOMMAIRE@</code>.

[[%auto_sommaire%]]

@puce@ Par d&eacute;faut, le Couteau Suisse ins&egrave;re le sommaire en t&ecirc;te d\'article automatiquement. Mais vous avez la possibilt&eacute; de placer ce sommaire ailleurs dans votre squelette gr&acirc;ce &agrave; une balise #CS_SOMMAIRE que vous pouvez activer ici :
[[%balise_sommaire%]]

Ce sommaire peut &ecirc;tre coupl&eacute; avec : {D&eacute;coupe en pages et onglets}.',
	'sommaire:nom' => 'Un sommaire pour vos articles',
	'sommaire_avec' => 'Un texte avec sommaire&nbsp;: <b>@_CS_AVEC_SOMMAIRE@</b>',
	'sommaire_sans' => 'Un texte sans sommaire&nbsp;: <b>@_CS_SANS_SOMMAIRE@</b>',
	'spam:description' => 'Tente de lutter contre les envois de messages automatiques et malveillants en partie publique. Certains mots et les balises &lt;a>&lt;/a> sont interdits.

Listez ici les s&eacute;quences interdites@_CS_ASTER@ en les s&eacute;parant par des espaces. [[%spam_mots%]]
@_CS_ASTER@Pour sp&eacute;cifier un mot entier, mettez-le entre paranth&egrave;ses. Pour une expression avec des espaces, placez-la entre guillemets.',
	'spam:nom' => 'Lutte contre le SPAM',
	'spip_cache:description1' => '@puce@ Par d&eacute;faut, SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. D&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. @_CS_CACHE_EXTENSION@[[%radio_desactive_cache3%]]',
	'spip_cache:description2' => '@puce@ Quatre options pour vous aider &agrave; g&eacute;rer le cache de SPIP : <q1>
_ &bull; {Usage normal} : SPIP calcule toutes les pages publiques et les place dans le cache afin d\'en acc&eacute;l&eacute;rer la consultation. Apr&egrave;s un certain d&eacute;lai, le cache est recalcul&eacute; et stock&eacute;.
_ &bull; {Cache permanent} : les d&eacute;lais d\'invalidation du cache sont ignor&eacute;s.
_ &bull; {Pas de cache} : d&eacute;sactiver temporairement le cache peut aider au d&eacute;veloppement du site. Ici, rien n\'est stock&eacute; sur le disque.
_ &bull; {Contr&ocirc;le du cache} : option identique &agrave; la pr&eacute;c&eacute;dente, avec une &eacute;criture sur le disque de tous les r&eacute;sultats afin de pouvoir &eacute;ventuellement les contr&ocirc;ler.</q1>[[%radio_desactive_cache4%]]',
	'spip_cache:description' => '@puce@ Le cache occupe un certain espace disque et SPIP peut en limiter l\'importance. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu\'aucun quota ne s\'applique.[[%quota_cache% Mo]]

@puce@ Lorsqu\'une modification du contenu du site est faite, SPIP invalide imm&eacute;diatement le cache sans attendre le calcul p&eacute;riodique suivant. Si votre site a des probl&egrave;mes de performance face &agrave; une charge tr&egrave;s &eacute;lev&eacute;e, vous pouvez cocher &laquo;&nbsp;non&nbsp;&raquo; &agrave; cette option.[[%derniere_modif_invalide%]]

@puce@ Si la balise #CACHE n\'est pas trouv&eacute;e dans vos squelettes locaux, SPIP consid&egrave;re par d&eacute;faut que le cache d\'une page a une dur&eacute;e de vie de 24 heures avant de la recalculer. Afin de mieux g&eacute;rer la charge de votre serveur, vous pouvez ici modifier cette valeur.[[%duree_cache% heures]]

@puce@ Si vous avez plusieurs sites en mutualisation, vous pouvez sp&eacute;cifier ici la valeur par d&eacute;faut prise en compte par tous les sites locaux (SPIP 2.0 mini).[[%duree_cache_mutu% heures]]',
	'spip_cache:nom' => 'SPIP et le cache&hellip;',
	'stat_auteurs' => 'Les auteurs en stat',
	'statuts_spip' => 'Uniquement les statuts SPIP suivants :',
	'statuts_tous' => 'Tous les statuts',
	'suivi_forums:description' => 'Un auteur d\'article est toujours inform&eacute; lorsqu\'un message est publi&eacute; dans le forum public associ&eacute;. Mais il est aussi possible d\'avertir en plus : tous les participants au forum ou seulement les auteurs de messages en amont.[[%radio_suivi_forums3%]]',
	'suivi_forums:nom' => 'Suivi des forums publics',
	'supprimer_cadre' => 'Supprimer ce cadre',
	'supprimer_numero:description' => 'Applique la fonction SPIP supprimer_numero() &agrave; l\'ensemble des {{titres}} et des {{noms}} du site public, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.<br />Voici la syntaxe &agrave; utiliser dans le cadre d\'un site multilingue : <code>1. <multi>My Title[fr]Mon Titre[de]Mein Titel</multi></code>',
	'supprimer_numero:nom' => 'Supprime le num&eacute;ro',

	// T
	'titre' => 'Le Couteau Suisse',
	'titre_tests' => 'Le Couteau Suisse - Page de tests&hellip;',
	'tous' => 'Tous',
	'toutes_couleurs' => 'Les 36 couleurs des styles css :@_CS_EXEMPLE_COULEURS@',
	'toutmulti:aide' => 'Blocs multilingues&nbsp;: <b><:trad:></b>',
	'toutmulti:description' => '&Agrave; l\'instar de ce vous pouvez d&eacute;j&agrave; faire dans vos squelettes, cet outil vous permet d\'utiliser librement les cha&icirc;nes de langues (de SPIP ou de vos squelettes) dans tous les contenus de votre site (articles, titres, messages, etc.) &agrave; l\'aide du raccourci <code><:chaine:></code>.
	
Consultez [ici ->http://www.spip.net/fr_article2128.html] la documentation de SPIP &agrave; ce sujet.

Cet outil accepte &eacute;galement les arguments introduits par SPIP 2.0. Par exemple, le raccourci <code><:ma_chaine{nom=Charles Martin, age=37}:></code> permet de passer deux param&egrave;tres &agrave; la cha&icirc;ne suivante : <code>\'ma_chaine\'=>"Bonjour, je suis @nom@ et j\'ai @age@ ans\"</code>.

La fonction SPIP utilis&eacute;e en PHP est <code>_T(\'chaine\')</code> sans argument, et  <code>_T(\'chaine\', array(\'arg1\'=>\'un texte\', \'arg2\'=>\'un autre texte\'))</code> avec arguments.

 N\'oubliez donc pas de v&eacute;rifier que la clef <code>\'chaine\'</code> est bien d&eacute;finie dans les fichiers de langues.',
	'toutmulti:nom' => 'Blocs multilingues',
	'travaux_nom_site' => '@_CS_NOM_SITE@',
	'travaux_prochainement' => 'Ce site sera r&eacute;tabli tr&egrave;s prochainement.
_ Merci de votre compr&eacute;hension.',
	'travaux_titre' => '@_CS_TRAVAUX_TITRE@',
	'tri_articles:description' => 'En naviguant sur le site en partie priv&eacute;e ([->./?exec=auteurs]), choisissez ici le tri &agrave; utiliser pour afficher vos articles &agrave; l\'int&eacute;rieur de vos rubriques.

Les propositions ci-dessous sont bas&eacute;es sur la fonctionnalit&eacute; SQL \'ORDER BY\' : n\'utilisez le tri personnalis&eacute; que si vous savez ce que vous faites (champs disponibles : {id_article, id_rubrique, titre, soustitre, surtitre, statut, date_redac, date_modif, lang, etc.})
[[%tri_articles%]][[->%tri_perso%]]',
	'tri_articles:nom' => 'Tri des articles',
	'tri_modif' => 'Tri sur la date de modification (ORDER BY date_modif DESC)',
	'tri_perso' => 'Tri SQL personnalis&eacute;, ORDER BY suivi de :',
	'tri_publi' => 'Tri sur la date de publication (ORDER BY date DESC)',
	'tri_titre' => 'Tri sur le titre (ORDER BY 0+titre,titre)',
	'type_urls:description' => '@puce@ SPIP offre un choix sur plusieurs jeux d\'URLs pour fabriquer les liens d\'acc&egrave;s aux pages de votre site.

Plus d\'infos : [->http://www.spip.net/fr_article765.html]
[[%radio_type_urls3%]]
<q3>@_CS_ASTER@pour utiliser les formats {html}, {propre}, {propre2}, {libres} ou {arborescentes}, recopiez le fichier &quot;htaccess.txt&quot; du r&eacute;pertoire de base du site SPIP sous le sous le nom &quot;.htaccess&quot; (attention &agrave; ne pas &eacute;craser d\'autres r&eacute;glages que vous pourriez avoir mis dans ce fichier) ; si votre site est en &quot;sous-r&eacute;pertoire&quot;, vous devrez aussi &eacute;diter la ligne &quot;RewriteBase&quot; ce fichier. Les URLs d&eacute;finies seront alors redirig&eacute;es vers les fichiers de SPIP.</q3>

<radio_type_urls3 valeur="page">@puce@ {{URLs &laquo;page&raquo;}} : ce sont les liens par d&eacute;faut, utilis&eacute;s par SPIP depuis sa version 1.9x.
_ Exemple : <code>/spip.php?article123</code>[[%terminaison_urls_page%]][[%separateur_urls_page%]]</radio_type_urls3>
<radio_type_urls3 valeur="html">@puce@ {{URLs &laquo;html&raquo;}} : les liens ont la forme des pages html classiques.
_ Exemple : <code>/article123.html</code></radio_type_urls3>
<radio_type_urls3 valeur="propres">@puce@ {{URLs &laquo;propres&raquo;}} : les liens sont calcul&eacute;s gr&acirc;ce au titre des objets demand&eacute;s. Des marqueurs (_, -, +, etc.) encadrent les titres en fonction du type d\'objet.
_ Exemples : <code>/Mon-titre-d-article</code> ou <code>/-Ma-rubrique-</code> ou <code>/@Mon-site@</code></radio_type_urls3>
<radio_type_urls3 valeur="propres2">@puce@ {{URLs &laquo;propres2&raquo;}} : l\'extension \'.html\' est ajout&eacute;e aux liens {&laquo;propres&raquo;}.
_ Exemple : <code>/Mon-titre-d-article.html</code> ou <code>/-Ma-rubrique-.html</code></radio_type_urls3>
<radio_type_urls3 valeur="libres">@puce@ {{URLs &laquo;libres&raquo;}} : les liens sont {&laquo;propres&raquo;}, mais sans marqueurs (_, -, +, etc.).
_ Exemple : <code>/Mon-titre-d-article</code> ou <code>/Ma-rubrique</code></radio_type_urls3>
<radio_type_urls3 valeur="arbo">@puce@ {{URLs &laquo;arborescentes&raquo;}} : les liens sont {&laquo;propres&raquo;}, mais de type arborescent.
_ Exemple : <code>/secteur/rubrique1/rubrique2/Mon-titre-d-article</code>[[%url_arbo_minuscules%]][[%urls_arbo_sans_type%]][[%url_arbo_sep_id%]][[%terminaison_urls_arbo%]]</radio_type_urls3>
<radio_type_urls3 valeur="propres-qs">@puce@ {{URLs &laquo;propres-qs&raquo;}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont {&laquo;propres&raquo;}.
_ Exemple : <code>/?Mon-titre-d-article</code></radio_type_urls3>
<radio_type_urls3 valeur="propres_qs">@puce@ {{URLs &laquo;propres_qs&raquo;}} : ce syst&egrave;me fonctionne en &quot;Query-String&quot;, c\'est-&agrave;-dire sans utilisation de .htaccess ; les liens sont {&laquo;propres&raquo;}.
_ Exemple : <code>/?Mon-titre-d-article</code></radio_type_urls3>
<radio_type_urls3 valeur="standard">@puce@ {{URLs &laquo;standard&raquo;}} : ces liens d&eacute;sormais obsol&egrave;tes &eacute;taient utilis&eacute;s par SPIP jusqu\'&agrave; sa version 1.8.
_ Exemple : <code>article.php3?id_article=123</code></radio_type_urls3>

@puce@ Si vous utilisez le format {page} ci-dessus ou si l\'objet demand&eacute; n\'est pas reconnu, alors il vous est possible de choisir {{le script d\'appel}} &agrave; SPIP. Par d&eacute;faut, SPIP choisit {spip.php}, mais {index.php} (exemple de format : <code>/index.php?article123</code>) ou une valeur vide (format : <code>/?article123</code>) fonctionnent aussi. Pour tout autre valeur, il vous faut absolument cr&eacute;er le fichier correspondant dans la racine de SPIP, &agrave; l\'image de celui qui existe d&eacute;j&agrave; : {index.php}.
[[%spip_script%]]',
	'type_urls:nom' => 'Format des URLs',
	'typo_exposants:description' => '{{Textes fran&ccedil;ais}} : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).

Les abr&eacute;viations obtenues sont conformes &agrave; celles de l\'Imprimerie nationale telles qu\'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l\'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l\'Imprimerie nationale, Paris, 2002).

Sont aussi trait&eacute;es les expressions suivantes : <html>Dr, Pr, Mgr, m2, m3, Mn, Md, St&eacute;, &Eacute;ts, Vve, Cie, 1o, 2o, etc.</html> 

Choisissez ici de mettre en exposant certains raccourcis suppl&eacute;mentaires, malgr&eacute; un avis d&eacute;favorable de l\'Imprimerie nationale :[[%expo_bofbof%]]

{{Textes anglais}} : mise en exposant des nombres ordinaux : <html>1st, 2nd</html>, etc.',
	'label:expo_bofbof' => 'Mise en exposants pour : <html>St(e)(s), Bx, Bd(s) et Fb(s)</html>',
	'typo_exposants:nom' => 'Exposants typographiques',

	// U
	'url_arbo' => 'arborescentes@_CS_ASTER@',
	'url_html' => 'html@_CS_ASTER@',
	'url_page' => 'page',
	'url_libres' => 'libres@_CS_ASTER@',
	'url_propres' => 'propres@_CS_ASTER@',
	'url_propres-qs' => 'propres-qs',
	'url_propres2' => 'propres2@_CS_ASTER@',
	'url_standard' => 'standard',

	// V
	'validez_page' => 'Pour acc&eacute;der aux modifications :',
	'variable_vide' => '(Vide)',
	'vars_modifiees' => 'Les donn&eacute;es ont bien &eacute;t&eacute; modifi&eacute;es',
	'version_a_jour' => 'Votre version est &agrave; jour.',
	'version_distante' => 'Version distante...',
	'version_distante_off' => 'V&eacute;rification distante d&eacute;sactiv&eacute;e',
	'version_nouvelle' => 'Nouvelle version : @version@',
	'version_revision' => 'R&eacute;vision : @revision@',
	'version_update' => 'Mise &agrave; jour automatique',
	'version_update_title' => 'T&eacute;l&eacute;charge la derni&egrave;re version du plugin et lance sa mise &agrave; jour automatique',
	'version_update_chargeur' => 'T&eacute;l&eacute;chargement automatique',
	'version_update_chargeur_title' => 'T&eacute;l&eacute;charge la derni&egrave;re version du plugin gr&acirc;ce au plugin &laquo;T&eacute;l&eacute;chargeur&raquo;',
	'verstexte:description' => '2 filtres pour vos squelettes, permettant de produire des pages plus l&eacute;g&egrave;res.
_ version_texte : extrait le contenu texte d\'une page html &agrave; l\'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d\'une page html pour rendre du texte plein.',
	'verstexte:nom' => 'Version texte',
	'visiteurs_connectes:description' => 'Offre une noisette pour votre squelette qui affiche le nombre de visiteurs connect&eacute;s sur le site public.

Ajoutez simplement <code><INCLURE{fond=fonds/visiteurs_connectes}></code> dans vos pages.',
	'visiteurs_connectes:nom' => 'Visiteurs connect&eacute;s',
	'voir' => 'Voir : @voir@',
	'votre_choix' => 'Votre choix :',

	// X
	'xml:description' => 'Active le validateur xml pour l\'espace public tel qu\'il est d&eacute;crit dans la [documentation->http://www.spip.net/fr_article3541.html]. Un bouton intitul&eacute; &laquo;&nbsp;Analyse XML&nbsp;&raquo; est ajout&eacute; aux autres boutons d\'administration.',
	'xml:nom' => 'Validateur XML',
	'webmestres:description' => "Un {{webmestre}} au sens SPIP est un {{administrateur}} ayant acc&egrave;s &agrave; l'espace FTP. Par d&eacute;faut et &agrave; partir de SPIP 2.0, il est l’administrateur <code>id_auteur=1</code> du site. Les webmestres ici d&eacute;finis ont le privil&egrave;ge de ne plus &ecirc;tre oblig&eacute;s de passer par FTP pour valider les op&eacute;rations sensibles du site, comme la mise &agrave; jour de la base de donn&eacute;es ou la restauration d&rsquo;un dump.

Webmestre(s) actuel(s) : {@_CS_LISTE_WEBMESTRES@}.
_ Administrateur(s) &eacute;ligible(s) : {@_CS_LISTE_ADMINS@}.

En tant que webmestre vous-m&ecirc;me, vous avez ici les droits de modifier cette liste d'ids -- s&eacute;par&eacute;s par les deux points &laquo;&nbsp;:&nbsp;&raquo; s'ils sont plusieurs. Exemple : &laquo;1:5:6&raquo;.[[%webmestres%]]",
	'webmestres:nom' => 'Liste des webmestres',
	'label:webmestres' => 'Liste des webmestres du site :',
	'cache_nornal' => 'Usage normal',
	'cache_sans' => 'Pas de cache',
	'cache_permanent' => 'Cache permanent',
	'cache_controle' => 'Contr&ocirc;le du cache',
	'message_perso' => 'Un grand merci aux traducteurs qui passeraient par ici. Pat ;-)',

);

?>