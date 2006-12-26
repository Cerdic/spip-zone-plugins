<?php

#---------------------------------------------------#
#  Plugin  : Tweak SPIP                             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

global $tweaks, $tweaks_pipelines, $tweaks_css, $tweak_exclude;
$tweaks = $tweaks_pipelines = $tweaks_css = $tweak_exclude = array();

//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'D&eacute;sactiver le cache',
	'description' 	=> 'Inhibition du cache de SPIP pour le d&eacute;veloppement du site.',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'desactiver_cache',
	// tweak a inserer dans les options
	'options'		=> 1,
));

add_tweak( array(
	'nom'			=> 'Supprimer le num&eacute;ro',
	'description' 	=> "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des titres du site, sans qu'elle soit pr&eacute;sente dans les squelettes.",
	'include' 		=> 'supprimer_numero',
	// tweak a inserer dans les options
	'options'		=> 1
));

add_tweak( array(
	'nom'			=> 'Paragrapher',
	'description' 	=> "Applique la fonction SPIP paragrapher() aux textes qui sont d&eacute;pourvus de paragraphes en insérant des balises &lt;p&gt;.",
	'include' 		=> 'paragrapher',
	// tweak a inserer dans les options
	'options'		=> 1
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'Version texte',
	'description' 	=> "2 filtres pour vos squelettes. 
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires.
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein.",
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'verstexte_fonctions',
	// tweak a inserer dans les fonctions
	'fonctions'		=> 1,
));

add_tweak( array(
	'nom'			=> 'Orientation des images',
	'description' 	=> "Ajoute les crit&egrave;res <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code> dans vos squelettes pour le classement des photos.
_ [Plus d'infos->http://www.spip-contrib.net/Portrait-ou-Paysage]",
	'auteur' 		=> 'Pierre Andrews (Mortimer) &amp; IZO',
	'include' 		=> 'orientation',
	// tweak a inserer dans les fonctions
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
	'include' 		=> 'desactiver_flash',
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
	'include' 		=> 'toutmulti',
	// pipeline => fonction
	'pre_typo'	=> 'ToutMulti_pre_typo',
));

add_tweak( array(
	'nom'			=> 'Belles puces',
	'description' 	=> 'Remplace les puces - (tiret) des articles par des puces -* (&lt;li>...)',
	'auteur' 		=> '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'include' 		=> 'bellespuces',
	// pipeline => fonction
	'pre_typo' => 'bellespuces_pre_typo',
));	

add_tweak( array(
	'nom'			=> 'D&eacute;coration',
	'description' 	=> "Permet aux r&eacute;dacteurs d'un article d'appliquer les styles <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote> et <fluo>fluo</fluo> &agrave; un texte.
-* {&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
-* {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
-* {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
-* {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
-* {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
-* {&lt;fluo&gt;}Lorem ipsum dolor sit amet{&lt;/fluo&gt;}",
	'auteur' 		=> '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'include' 		=> 'decoration',
	// pipeline => fonction
	'pre_typo' => 'decoration_pre_typo',
));

add_tweak( array(
	'nom'			=> 'Mises en exposant',
	'description' 	=> "Textes fran&ccedil;ais : am&eacute;liore le rendu typographique des abr&eacute;viations courantes, en mettant en exposant les &eacute;l&eacute;ments n&eacute;cessaires (ainsi, {M{m}e} devient {Mme} et en corrigeant les erreurs courantes {2{&egrave;}me} ou  {2{m}e}, par exemple, deviennent {2<sup>e</sup>}, seule abr&eacute;viation correcte).

Les abr&eacute;viations obtenues sont conformes &agrave; celles de l'Imprimerie nationale telles qu'indiqu&eacute;es dans le {Lexique des r&egrave;gles typographiques en usage &agrave; l'Imprimerie nationale} (article &laquo;&nbsp;Abr&eacute;viations&nbsp;&raquo;, presses de l'Imprimerie nationale, Paris, 2002).",
	'auteur' 		=> 'Vincent Ramos [contact->mailto:www-lansargues@kailaasa.net]',
	'include' 		=> 'typo_exposants',
	// pipeline => fonction
	'post_typo'	=> 'typo_exposants',
));

add_tweak( array(
	'nom'			=> 'Filets de S&eacute;paration',
	'description' 	=> "Ins&egrave;re des filets de s&eacute;paration, personnalisables par des feuilles de style, dans le corps des textes, aussi bien pour les articles que pour les br&egrave;ves.
_ La syntaxe est : &quot;__N__&quot;, o&ugrave; &quot;N&quot; repr&eacute;sente le num&eacute;ro d&rsquo;identification (de 0 &agrave; 9) du filet &agrave; ins&eacute;rer, en relation directe avec les styles correspondants.
_ Attention : chaque balise doit &ecirc;tre plac&eacute;e seule et sur une ligne unique.",
	'auteur' 		=> 'FredoMkb',
	'include' 		=> 'filets_sep',
	// pipeline => fonction
	'pre_typo'	=> 'filets_sep',
));

add_tweak( array(
	'nom'			=> 'Smileys',
	'description' 	=> "Ins&egrave;re des smileys dans tous les textes o&ugrave; apparait un raccourcis du genre :{-}). Id&eacute;al pour les  forums.
_ Infos : [->http://www.spip-contrib.net/?article1561]",
	'auteur' 		=> 'Sylvain',
	'include' 		=> 'smileys',
	// pipeline => fonction
	'post_typo'	=> 'tweak_smileys',
));

// Idées d'ajouts :
// http://www.spip-contrib.net/Citations
// http://www.spip-contrib.net/la-balise-LESMOTS
// http://www.spip-contrib.net/Ajouter-une-lettrine-aux-articles

//-----------------------------------------------------------------------------//
//                        activation des tweaks                                //
//-----------------------------------------------------------------------------//

// exclure ce qui n'est pas un pipeline...
$tweak_exclude = array('nom', 'description', 'auteur', 'include', 'options', 'fonctions', 'actif');

// lire les metas et initialiser : $tweaks_pipelines, $tweaks_css
tweak_lire_metas();

//print_r($tweaks);
//print_r($tweaks_pipelines);

?>