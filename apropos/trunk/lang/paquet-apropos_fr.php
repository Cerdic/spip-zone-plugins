<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// 
///  Fichier produit par PlugOnet
// Module: paquet-apropos
// Langue: fr
// Date: 17-10-2011 12:32:15
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
	'apropos_description' => 'Liste les plugins actifs et affiche une description sommaire de ceux-ci.

    
    Ce plugin sert à afficher sur une page de type « à propos du site » un récapitulatif des plugins actifs, y compris l\'icone de ceux-ci.
    <br />
	Vous pouvez utiliser la nouvelle balise #APROPOS dans vos squelettes. Cette balise a 4 paramètres : <br />

	
	- soit vous écrivez :  <code>#APROPOS{liste}</code> pour retourner la liste des plugins, <br />

	
	- soit vous écrivez :  <code>#APROPOS{nombre}</code> pour n\'afficher que le nombre de plugins ET d\'extensions actifs.<br />

	
	- soit vous écrivez :  <code>#APROPOS{plugins}</code> pour n\'afficher que le nombre de plugins actifs.<br />

	- soit vous écrivez :  <code>#APROPOS{extensions}</code> pour n\'afficher que le nombre d\'extensions actives.<br />

	Pour personnaliser ce qui est affiché avant et après la liste des plugins actifs, modifiez le fichier modeles/apropos_liste.html que vous aurez préalablement copié dans le dossier squelettes/modeles.

	<br />
	- Pour afficher la liste dans un article, il faut écrire dans l\'article <code><apropos|liste></code>.<br />

	- Pour n\'afficher que le nombre de plugins ET d\'extensions actifs, utilisez <code><apropos|nombre></code>.<br />

	- Pour n\'afficher que le nombre de plugins actifs, utilisez <code><apropos|plugins></code>.<br />

	- Pour n\'afficher que le nombre d\'extensions actives, utilisez <code><apropos|extensions></code>.<br />
	
	Si vous voulez afficher la description complète d\'un plugin dans un article, utilisez <code><apropos|prefixe=le préfixe du plugin></code>. Ainsi, par exemple, si vous mettez <code><apropos|prefixe=apropos></code>, vous afficherez la description complète de ce plugin.
	',
	
	'apropos_slogan' => 'Liste les plugins actifs et affiche une description sommaire de ceux-ci',
);
?>