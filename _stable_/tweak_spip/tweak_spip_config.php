<?php

global $tweaks, $tweaks_actifs;

// cette liste enumere les tweaks inc/???.php a installer.
// commenter les tweaks à ne pas activer...
// une page en admin sera la bienvenue pour eviter de configurer le plugin ici...
$tweaks_actifs = array(	
//	'desactiver_cache', 
	'supprimer_numero_options',
	'verstexte_fonctions',
	'orientation',
//	'desactiver_flash',
	'toutmulti',
	'bellespuces',
	'decoration',
);
	
//-----------------------------------------------------------------------------//
//                               options                                       //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'D&eacute;sactiver le cache',
	'description' 	=> 'Inhibition du cache de SPIP pour le d&eacute;veloppement du site.',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'desactiver_cache',
	'pipeline' 		=> 'options',
));

add_tweak( array(
	'nom'			=> 'Supprimer le num&eacute;ro',
	'description' 	=> "Applique la fonction spip supprimer_numero &agrave; l'ensemble des titres du site, sans qu'elle soit pr&eactute;sente dans les squelettes.",
	'auteur' 		=> 'collectif',
	'include' 		=> 'supprimer_numero_options',
	'pipeline' 		=> 'options',
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'Version texte',
	'description' 	=> "2 Filtres : 
_ version_texte {(extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires)}
_ version_plein_texte {(extrait le contenu texte d'une page html pour rendre du texte plein)}",
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'verstexte_fonctions',
	'pipeline' 		=> 'fonctions',
));

add_tweak( array(
	'nom'			=> 'Orientation des images',
	'description' 	=> "Le plugin orientation ajoute les crit&egrave;res <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code> pour le classement des photos. [->http://www.spip-contrib.net/Portrait-ou-Paysage]",
	'auteur' 		=> 'Pierre Andrews (Mortimer) &amp; IZO ',
	'include' 		=> 'orientation',
	'pipeline' 		=> 'fonctions',
));


//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction à revoir ?
add_tweak( array(
	'nom'			=> 'D&eacute;sactiver les objects flash',
	'description' 	=> 'Ce plugin supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.
_ N&eacute;cessite jQuery.',
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'desactiver_flash',
	'pipeline' 		=> 'affichage_final',
	'fonction' 		=> 'InhibeFlash_affichage_final',
));

//-----------------------------------------------------------------------------//
//                               TYPO                                          //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'Tout multi',
	'description' 	=> 'Introduit le raccourci {&lt;:texte:&gt;} pour introduire librement des blocs multi dans un texte.',
	'auteur' 		=> '',
	'include' 		=> 'toutmulti',
	'pipeline' 		=> 'pre_typo',
	'fonction' 		=> 'ToutMulti_pre_typo',
));

add_tweak( array(
	'nom'			=> 'Belles puces',
	'description' 	=> 'Remplace les puces - (tiret) des articles par des puces -* (&lt;li>...)',
	'auteur' 		=> '[J&eacute;r&ocirc;me Combaz->http://conseil-recherche-innovation.net/index.php/2000/07/08/72-jerome-combaz]',
	'include' 		=> 'bellespuces',
	'pipeline' 		=> 'pre_typo',
	'fonction' 		=> 'bellespuces_pre_typo',
));	

add_tweak( array(
	'nom'			=> 'D&eacute;coration',
	'description' 	=> "Ce filtre permet aux r&eacute;dacteurs d'un article d'appliquer les styles <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote> et <fluo>fluo</fluo> &agrave; un texte.
-* {&lt;sc&gt;}Lorem ipsum dolor sit amet{&lt;/sc&gt;}
-* {&lt;souligne&gt;}Lorem ipsum dolor sit amet{&lt;/souligne&gt;}
-* {&lt;barre&gt;}Lorem ipsum dolor sit amet{&lt;/barre&gt;}
-* {&lt;dessus&gt;}Lorem ipsum dolor sit amet{&lt;/dessus&gt;}
-* {&lt;clignote&gt;}Lorem ipsum dolor sit amet{&lt;/clignote&gt;}
-* {&lt;fluo&gt;}Lorem ipsum dolor sit amet{&lt;/fluo&gt;}",
	'auteur' 		=> '[izo@aucuneid.net->http://www.aucuneid.com/bones]',
	'include' 		=> 'decoration',
	'pipeline' 		=> 'pre_typo',
	'fonction' 		=> 'decoration_pre_typo',
));

//-----------------------------------------------------------------------------//
//                        activation des tweaks                                //
//-----------------------------------------------------------------------------//


foreach ($tweaks as $i=>$tweak)
	 $tweaks[$i]['actif'] = in_array($tweak['include'], $tweaks_actifs);

//print_r($tweaks_actifs);
//print_r($tweaks);

?>