<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// 
///  Fichier produit par PlugOnet
// Module: paquet-apropos
// Langue: en
// Date: 17-10-2011 12:32:15
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
	'apropos_description' => 'Lists the active plugins and displays a brief description of them.

	It is used to display a page of type \"about the site\" with a summary of actives plugins, including the icon of these.

	<br />

	You can use the new balise #APROPOS into your templates. This balise has 4 parameters:<br />

	- if you wrote: <code>#APROPOS{liste}</code>, you get the list of the actives plugins,

	- if you wrote: <code>#APROPOS{nombre}</code>, you get the number of all the actives plugins AND extensions.

	- if you wrote: <code>#APROPOS{plugins}</code>, you get the number of the actives plugins.

	- if you wrote: <code>#APROPOS{extensions}</code>, you get the number of the actives extensions.

	

	To personalize the informations which are displayed before and after the list, modify the file modeles/apropos_liste.html after copying it into your folder squelettes/modeles. 

	<br />To view the list in an article, you must write <code><apropos|liste></code>. 

	To display the number of actives plugins AND extensions, write <code><apropos|nombre></code>.

    To display the number of actives plugins, write <code><apropos|plugins></code>.

    To display the number of actives extensions, write <code><apropos|extensions></code>.
    
	If you want to display the full description of a specific plugin, use this: <code><apropos|prefixe=the prefixe of the plugin></code>. For example, to display the description of this plugin, write: <code><apropos|prefixe=apropos></code>.

',
	'apropos_slogan' => 'Lists the active plugins and displays a brief description of them',
);
?>