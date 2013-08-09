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

	- if you wrote: <code><INCLURE{fond=modeles/apropos_lister_tout}></code>, you get the list of the actives and inactives plugins,

	- if you wrote: <code><INCLURE{fond=modeles/apropos_liste}></code>, you get the list of the actives plugins,

	- if you wrote: <code><INCLURE{fond=modeles/apropos_nombre}></code>, you get the number of all the actives plugins AND locked plugins.

	- if you wrote: <code><INCLURE{fond=modeles/apropos_plugins}></code>, you get the number of the actives plugins.

	- if you wrote: <code><INCLURE{fond=modeles/apropos_extensions}></code>, you get the number of the actives locked plugins.

	- if you wrote: <code><INCLURE{fond=modeles/apropos_adisposition}></code>, you get the number of plugins in the folder plugins.

	- if you wrote: <code><INCLURE{fond=modeles/apropos_disponible}></code>, you get the total number of locked plugins and plugins of your configuration.

	If you want to display the full description of a specific plugin, use this: <code><INCLURE{fond=modeles/apropos}{prefixe=the prefixe of the plugin></code>. For example, to display the description of this plugin, write: <code><INCLURE{fond=modeles/apropos}{prefixe=apropos></code>.
	

	To personalize the informations which are displayed before and after the list, modify the file modeles/apropos_liste.html after copying it into your folder squelettes/modeles. 

	<br />To view the list of all the activated and inactivated in an article, you must write <code><apropos|lister_tout></code>. 

	<br />To view the list in an article, you must write <code><apropos|liste></code>. 

	To display the number of actives plugins AND locked plugins, write <code><apropos|nombre></code>.

    To display the number of actives plugins, write <code><apropos|plugins></code>.

    To display the number of actives locked plugins, write <code><apropos|extensions></code>.<br />
	
	To display the number of plugins in the folder plugins, write <code><apropos|adisposition></code>.

	To display the total number of locked plugins and plugins of your configuration, write <code><apropos|disponible></code>.

	If you want to display the full description of a specific plugin, use this: <code><apropos|prefixe=the prefixe of the plugin></code>. For example, to display the description of this plugin, write: <code><apropos|prefixe=apropos></code>.

',
	'apropos_slogan' => 'Lists the active plugins and displays a brief description of them',
);
?>