<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'titre' => 'Tweak SPIP',
	'help'	=> "{{Cette page est uniquement accessible aux responsables du site.}}<p>Elle donne acc&egrave;s aux diff&eacute;rentes  fonctions suppl&eacute;mentaires apport&eacute;es par le plugin 'Tweak&nbsp;SPIP'.</p>",
	'actif' => 'Tweak actif',
	'inactif' => 'Tweak inactif',
	'activer_tweak' => 'Activer le tweak',
	'tweak'	=> 'Tweak :',
	'tweaks_liste' => 'Liste des tweaks',
	'presente_tweaks' => "Cette page liste les tweaks disponibles.<br />Vous pouvez activer les tweaks n&eacute;cessaires en cochant la case correspondante.",
// erreurs
	'erreur:nom' => 'Erreur !',
	'erreur:description'	=> 'id manquant dans la d&eacute;finition du tweak !',
	'erreur:version'	=> 'indisponible dans cette version de Spip trop ancienne.',

// categories
	'admin' => "1. Administration",
	'typo' => "2. Raccourcis typographiques",
	'squel' => "3. Squelettes",
	'divers' => "4. Divers",
	
// Les tweaks
	'desactive_cache:nom' => 'D&eacute;sactive le cache',
	'desactive_cache:description'	=> 'Inhibition du cache de SPIP pour le d&eacute;veloppement du site.',

	'supprimer_numero:nom' => 'Supprime le num&eacute;ro des titres',
	'supprimer_numero:description'	=> "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des {{titres}} du site, sans que le filtre supprimer_numero soit pr&eacute;sent dans les squelettes.",

	'paragrapher:nom' => 'Paragrapher',
	'paragrapher:description'	=> "Applique la fonction SPIP paragrapher() aux textes qui sont d&eacute;pourvus de paragraphes en ins&eacute;rant des balises &lt;p&gt; et &lt;/p&gt;. Utile pour visualiser tous les textes sans style.",

	'forcer_langue:nom' => 'Forcer langue',
	'forcer_langue:description'	=> "Force le contexte de langue pour les jeux de squelettes multilingues disposant d'un formulaire ou d'un menu de langues sachant g&eacute;rer le cookie de langues.",

	'insert_head:nom' => 'Balise #INSERT_HEAD',
	'insert_head:description'	=> "Active #INSERT_HEAD sur tous les squelettes, qu'ils aient ou non cette balise entre &lt;head&gt; et &lt;/head&gt;. Gr&acirc;ce &agrave; cette option, les plugins pourront ins&eacute;rer du javascript (.js) ou des feuilles de style (.css).",

	'verstexte:nom' => 'Version texte',
	'verstexte:description'	=> "2 filtres pour vos squelettes. 
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein.",

	'orientation:nom' => 'Orientation des images',
	'orientation:description'	=> "3 nouveaux crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&eacute;al pour le classement des photos en fonction de leur forme.
_ Infos : [->http://www.spip-contrib.net/Portrait-ou-Paysage]",

	'desactiver_flash:nom' => 'D&eacute;sactive les objets flash',
	'desactiver_flash:description'	=> 'Supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.
_ N&eacute;cessite le plugin {jQuery} ou une version de SPIP sup&eacute;rieure &agrave; 1.9.2.',

	'toutmulti:nom' => 'Blocs multilingues',
	'toutmulti:description'	=> "Introduit le raccourci &lt;:un_texte:&gt; pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : _T('un_texte', \$flux).
_ N'oubliez pas de v&eacute;rifier que 'un_texte' est bien d&eacute;fini dans les fichiers de langue.",

	'pucesli:nom' => 'Belles puces',
	'pucesli:description'	=> 'Remplace les puces - (tiret) des articles par des puces -* (&lt;li>&lt;ul>...&lt;/li>&lt;/ul>)',

	'decoration:nom' => 'D&eacute;coration',
	'decoration:description'	=> "7 nouveaux styles dans vos articles : <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote>, <surfluo>fluo</surfluo> et <surgris>gris&eacute;</surgris>. Utilisation :
-* {&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
-* {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
-* {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
-* {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
-* {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
-* {&lt;surfluo&gt;}Lorem ipsum dolor sit amet{&lt;/surfluo&gt;}
-* {&lt;surgris&gt;}Lorem ipsum dolor sit amet{&lt;/surgris&gt;}

Infos : [->http://www.spip-contrib.net/?article1552]",

	'typo_exposants:nom' => 'Exposants typographiques',
	'typo_exposants:description'	=> "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).
_ Infos : [->http://www.spip-contrib.net/?article1564]",

	'filets_sep:nom' => 'Filets de S&eacute;paration',
	'filets_sep:description'	=>  "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de Spip.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d'une image plac&eacute;e dans le dossier img/filets.
_ Infos : [->http://www.spip-contrib.net/?article1563]",

	'smileys:nom' => 'Smileys',
	'smileys:description'	=> "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Infos : [->http://www.spip-contrib.net/?article1561]",

	'quota_cache:nom' => 'Quota du cache',
	'quota_cache:description'	=> "Modifie le quota r&eacute;serv&eacute; au cache. Une valeur vide ou &eacute;gale &agrave; 0 signifie qu'aucun quota ne s'applique.<br />Valeur (en Mo) : %quota_cache%",

	'dossier_squelettes:nom' => 'Dossier du squelette',
	'dossier_squelettes:description'	=> "Modifie le dossier du squelette utilis&eacute;. Par exemple : &quot;squelettes/monsquelette&quot;. En laissant vide la case qui suit, c'est le squelette original &quot;dist&quot; fourni par Spip qui sera utilis&eacute;.<br />Dossier(s) &agrave; utiliser : %dossier_squelettes%",

	'chatons:nom' => 'Chatons',
	'chatons:description'	=> 'Ins&egrave;re des images (ou chatons pour les {tchats}) dans tous les textes o&ugrave; appara&icirc;t une cha&icirc;ne du genre <acronym>:nom</acronym>.
_ Ce tweak remplace ces raccourcis par les images du m&ecirc;me nom qu\'il trouve dans le r&eacute;pertoire img/chatons.',

	'guillemets:nom' => 'Guillemets typographiques',
	'guillemets:description'	=> 'Remplace automatiquement les guillemets droits (") par les guillemets typographiques de la langue de composition. Le remplacement, transparent pour l\'utilisateur, ne modifie pas le texte mais seulement l\'affichage final.',

	'set_options:nom' => "Type d'interface priv&eacute;e",
	'set_options:description'	=> "S&eacute;lectionne d'office le type d&rsquo;interface priv&eacute;e (simplifi&eacute;e ou avanc&eacute;e) pour tous les r&eacute;dacteurs d&eacute;j&agrave; existant ou &agrave; venir et supprime le bouton correspondant du bandeau des petites ic&ocirc;nes.<br />Votre choix : %radio_set_options%",

	'log_tweaks:nom' => 'Log d&eacute;taill&eacute; de Tweak Spip',
	'log_tweaks:description'	=> "Inscrit de nombreux renseignements &agrave; propos du fonctionnement du plugin 'Tweak Spip' dans les fichiers spip.log que l'on peut trouver dans le r&eacute;pertoire : ".tweak_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP),

/*
	':nom' => '',
	':description'	=> '',
*/
);


?>