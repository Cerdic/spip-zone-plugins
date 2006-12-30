<?php

#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

global $tweaks, $tweaks_pipelines, $tweaks_css, $tweak_exclude;
$tweaks = $tweaks_pipelines = $tweaks_css = $tweak_exclude = array();

//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'id' 	=> 'desactive_cache',
	'code' 	=> '$_SERVER["REQUEST_METHOD"]="POST";',
	'auteur'		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'categorie'		=> 'administration',
	'options'		=> 1,
));

add_tweak( array(
	'id' 	=> 'supprimer_numero',
	'code' 	=> '$GLOBALS["table_des_traitements"]["TITRE"][]= "typo(supprimer_numero(%s))";',
	'categorie'	=> 'administration',
	'options'	=> 1
));

add_tweak( array(
	'id' 	=> 'paragrapher',
	'code'	=> '$GLOBALS["toujours_paragrapher"]=true;',
	'categorie'		=> 'administration',
	'options'		=> 1
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'id' 		=> 'verstexte',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'fonctions'		=> 1,
));

add_tweak( array(
	'nom'			=> 'Orientation des images',
	'description' 	=> "3 nouveauw crit&egrave;res pour vos squelettes : <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code>. Id&egrave;al pour le classement des photos en fonction de leur forme.
_ Infos : [->http://www.spip-contrib.net/Portrait-ou-Paysage]",
	'auteur' 		=> 'Pierre Andrews (Mortimer) &amp; IZO',
	'id' 		=> 'orientation',
	'fonctions'		=> 1,
));


//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction à revoir ?
add_tweak( array(
	'nom'			=> 'D&eacute;sactiver les objects flash',
	'description' 	=> 'Supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.
_ N&eacute;cessite le plugin {jQuery} ou une version de SPIP sup&eacute;rieure à 1.9.2.',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'id' 		=> 'desactiver_flash',
	'categorie'		=> 'administration',
	// pipeline => fonction
	'affichage_final' => 'InhibeFlash_affichage_final',
));

//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'Tout multi',
	'description' 	=> "Introduit le raccourci &lt;:un_texte:&gt; pour introduire librement des blocs multi-langues dans un article.
_ La fonction SPIP utilis&eacute;e est : _T('un_texte', \$flux).
_ N'oubliez pas de v&eacute;rifier que 'un_texte' est bien d&eacute;fini dans les fichiers de langue.",
	'auteur' 		=> '',
	'id' 		=> 'toutmulti',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'pre_typo'	=> 'ToutMulti_pre_typo',
));

add_tweak( array(
	'nom'			=> 'Belles puces',
	'description' 	=> 'Remplace les puces - (tiret) des articles par des puces -* (&lt;li>...)',
	'auteur' 		=> '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'id' 		=> 'bellespuces',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'pre_typo' => 'bellespuces_pre_typo',
));	

add_tweak( array(
	'nom'			=> 'D&eacute;coration',
	'description' 	=> "7 nouveaux styles dans vos articles : <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote>, <surfluo>fluo</surfluo> et <surgris>gris&eacute;</surgris>. Utilisation :
-* {&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
-* {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
-* {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
-* {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
-* {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
-* {&lt;surfluo&gt;}Lorem ipsum dolor sit amet{&lt;/surfluo&gt;}
-* {&lt;surgris&gt;}Lorem ipsum dolor sit amet{&lt;/surgris&gt;}

Infos : [->http://www.spip-contrib.net/?article1552]",
	'auteur' 		=> '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'id' 		=> 'decoration',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'pre_typo' => 'decoration_pre_typo',
));

// tweak specifiquement français. D'autres langues peuvent etre ajoutees dans inc/typo_exposants.php
add_tweak( array(
	'nom'			=> 'Mises en exposant',
	'description' 	=> "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {<acronym>Mme</acronym>} devient {M<sup>me</sup>}) et en corrigeant les erreurs courantes ({<acronym>2&egrave;me</acronym>} ou  {<acronym>2me</acronym>}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).
_ Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).
_ Infos : [->http://www.spip-contrib.net/?article1564]",
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'id' 		=> 'typo_exposants',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'post_typo'	=> 'typo_exposants',
));

add_tweak( array(
	'nom'			=> 'Filets de S&eacute;paration',
	'description' 	=> "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans tous les textes de Spip.
_ La syntaxe est : &quot;__code__&quot;, o&ugrave; &quot;code&quot; repr&eacute;sente soit le num&eacute;ro d&rsquo;identification (de 0 &agrave; 7) du filet &agrave; ins&eacute;rer en relation directe avec les styles correspondants, soit le nom d'une image plac&eacute;e dans le dossier img/filets.
_ Infos : [->http://www.spip-contrib.net/?article1563]",
	'auteur' 		=> 'FredoMkb',
	'id' 		=> 'filets_sep',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'pre_typo'	=> 'filets_sep',
));

add_tweak( array(
	'nom'			=> 'Smileys',
	'description' 	=> "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourci du genre <acronym>:-)</acronym>. Id&eacute;al pour les  forums.
_ Infos : [->http://www.spip-contrib.net/?article1561]",
	'auteur' 		=> 'Sylvain',
	'id' 		=> 'smileys',
	'categorie'		=> 'typographie',
	// pipeline => fonction
	'pre_typo'	=> 'tweak_smileys',
));

// Idées d'ajouts :
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles

//-----------------------------------------------------------------------------//
//                        activation des tweaks                                //
//-----------------------------------------------------------------------------//

// exclure ce qui n'est pas un pipeline...
$tweak_exclude = array('nom', 'description', 'auteur', 'categorie', 'id', 'code', 'options', 'fonctions', 'actif');

// lire les metas et initialiser : $tweaks_pipelines, $tweaks_css
tweak_initialisation();

?>