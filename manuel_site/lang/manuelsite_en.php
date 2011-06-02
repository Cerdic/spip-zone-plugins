<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$idart = lire_config('manuelsite/id_article') ;
$texte = ($idart > 0) ? '<a href="?exec=articles&amp;id_article='.$idart.'" title="Editor manual">the article '.$idart.'</a> of your website.' : 'an article of the website.' ;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'cfg_boite_manuelsite' => 'Website Editor Manual plugin configuration.<br /><br />This plugin installs an help icon making it possible to post since any page of private space the Website Editor Manual. This manual is '.$texte.' The purpose of it is to explain to the editors the architecture of the site, in which heading to arrange what, how code and install a video… So, all that you want and which is specific to your website.',
	'cfg_titre_manuelsite' => 'Website Editor Manual',

	// E
	'erreur_article' => 'The article of the manuel defined in the plugin\'s configuration is untraceable : @idart@',
	'erreur_pas_darticle' => 'The article of the manuel is not defined in the plugin\'s configuration',
	'explication_afficher_bord_gauche' => 'Display the manual\'s icon in top on the left (if not the manual will be displayes in column)',
	'explication_background_color' => 'Type in the background color of the manual display area',
	'explication_cacher_public' => 'Hide this article in the public space, even in rss flow',
	'explication_email' => 'Contact email for editors',
	'explication_id_article' => 'Type in the number of the article wich contain the manual',
	'explication_intro' => 'Introduction text of the manual (will be placed before the introduction of the article)',
	'explication_largeur' => 'Type in the manual display area width',

	// F
	'fermer_le_manuel' => 'Close the manual',
	
	// H
	'help' => 'Help : ' ,
	
	// I
	'intro' => 'The purpose of this document is to help the editors with the use of the site. It comes in complement from the headed document “[How to use SPIP 2 as an author->http://www.spip-contrib.net/How-to-use-SPIP-as-an-author,3399]” which is a total help with the use of SPIP. You will find there a description of the architecture of the site, of the technical assistance on particular points…',
	// L
	'label_afficher_bord_gauche' => 'Display',
	'label_background_color' => 'Background color',
	'label_cacher_public' => 'Hide',
	'label_email' => 'Email',
	'label_id_article' => 'Article number',
	'label_intro' => 'Introduction',
	'label_largeur' => 'Width',
	'legende_apparence' => 'Appearance' ,
	'legende_contenu' => 'Contents' ,

	// T
	'titre_manuel' => 'Website Editor Manual'
);
?>
