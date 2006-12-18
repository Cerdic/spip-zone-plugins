<?php

#---------------------------------------------------#
#  Plugin  : Tweak SPIP                             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice�.!vanneufville�@!laposte�.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

global $tweaks, $tweaks_actifs, $tweaks_pipelines, $tweak_exclude;

// cette liste enumere les tweaks inc/???.php a inclure.
// commenter les tweaks � ne pas activer...
// quand la page d'admin sera pleinement fonctionnelle, les lignes suivantes ne seront plus n�cessaires...
// une option sera simplement � rajouter pour d�finir si le tweak est charg� par defaut ou non
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
	// a inserer dans les options
	'options'		=> 1
));

add_tweak( array(
	'nom'			=> 'Supprimer le num&eacute;ro',
	'description' 	=> "Applique la fonction SPIP supprimer_numero() &agrave; l'ensemble des titres du site, sans qu'elle soit pr&eactute;sente dans les squelettes.",
	'auteur' 		=> 'collectif',
	'include' 		=> 'supprimer_numero_options',
	// a inserer dans les options
	'options'		=> 1
));

//-----------------------------------------------------------------------------//
//                               fonctions                                     //
//-----------------------------------------------------------------------------//

add_tweak( array(
	'nom'			=> 'Version texte',
	'description' 	=> "2 Filtres pour vos squelettes. 
_ version_texte : extrait le contenu texte d'une page html &agrave; l'exclusion de quelques balises &eacute;l&eacute;mentaires
_ version_plein_texte : extrait le contenu texte d'une page html pour rendre du texte plein",
	'auteur' 		=> '[Cedric MORIN->mailto:cedric.morin@yterium.com]',
	'include' 		=> 'verstexte_fonctions',
	// a inserer dans les fonctions
	'fonctions'		=> 1
));

add_tweak( array(
	'nom'			=> 'Orientation des images',
	'description' 	=> "Le plugin orientation ajoute les crit&egrave;res <code>{portrait}</code>, <code>{carre}</code> et <code>{paysage}</code> dans vos squelettes pour le classement des photos.
_ [Plus d'infos->http://www.spip-contrib.net/Portrait-ou-Paysage]",
	'auteur' 		=> 'Pierre Andrews (Mortimer) &amp; IZO',
	'include' 		=> 'orientation',
	// a inserer dans les fonctions
	'fonctions'		=> 1
));


//-----------------------------------------------------------------------------//
//                               PUBLIC                                        //
//-----------------------------------------------------------------------------//

// TODO : gestion du jQuery dans la fonction � revoir ?
add_tweak( array(
	'nom'			=> 'D&eacute;sactiver les objects flash',
	'description' 	=> 'Ce plugin supprime les objets flash des pages de votre site et les remplace par le contenu alternatif associ&eacute;.
_ N&eacute;cessite le plugin {jQuery} ou une version de SPIP sup&eacute;rieure � 1.9.2.',
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
_ N'oubliez pas de v&eacute;rifier que 'un_texte' est bien d&eacute;fini dans les fichiers de langue",
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
	'description' 	=> "Ce filtre permet aux r&eacute;dacteurs d'un article d'appliquer les styles <sc>capitales</sc>, <souligne>soulign&eacute;</souligne>, <barre>barr&eacute;</barre>, <dessus>dessus</dessus>, <clignote>clignote</clignote> et <fluo>fluo</fluo> &agrave; un texte.
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

//-----------------------------------------------------------------------------//
//                        activation des tweaks                                //
//-----------------------------------------------------------------------------//

// exclure ce qui n'est pas un pipeline...
$tweak_exclude = array('nom', 'description', 'auteur', 'include', 'options', 'fonctions', 'actif');

foreach ($tweaks as $i=>$tweak) {
	// insersion des parametres de $tweaks_actifs dans $tweaks;
	$actif = $tweaks[$i]['actif'] = in_array($tweak['include'], $tweaks_actifs);
	// stockage de la liste des fonctions par pipeline, si le tweak est actif...
	if ($actif) {
		foreach ($tweak as $pipe=>$fonc) if(!in_array($pipe, $tweak_exclude)) {
			$tweaks_pipelines[$pipe][0][] = $tweak['include'];
			$tweaks_pipelines[$pipe][1][] = $fonc;
		}
		if ($tweak['options']) $tweaks_pipelines['inc_options'][] = $tweak['include'];
		if ($tweak['fonctions']) $tweaks_pipelines['inc_fonctions'][] = $tweak['include'];
	}
}

//print_r($tweaks_actifs);
//print_r($tweaks);
//print_r($tweaks_pipelines);

?>