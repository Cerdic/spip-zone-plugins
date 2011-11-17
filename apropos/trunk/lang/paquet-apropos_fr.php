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

    

    Il sert &agrave; afficher sur une page de type &laquo;&nbsp;&agrave; propos du site&nbsp;&raquo; un r&eacute;capitulatif des plugins actifs, y compris l\'icone de ceux-ci.

    <br />

	Vous pouvez utiliser la nouvelle balise #APROPOS dans vos squelettes. Cette balise a 4 param&egrave;tres : <br />

	

	- soit vous &eacute;crivez :  <code>#APROPOS{liste}</code> pour retourner la liste des plugins, <br />

	

	- soit vous &eacute;crivez :  <code>#APROPOS{nombre}</code> pour n\'afficher que le nombre de plugins ET d\'extensions actifs.<br />

	

	- soit vous &eacute;crivez :  <code>#APROPOS{plugins}</code> pour n\'afficher que le nombre de plugins actifs.<br />

	- soit vous &eacute;crivez :  <code>#APROPOS{extensions}</code> pour n\'afficher que le nombre d\'extensions actives.<br />

	Pour personnaliser ce qui est affich&eacute; avant et apr&egrave;s la liste des plugins actifs, modifiez le fichier modeles/apropos_liste.html que vous aurez pr&eacute;alablement copi&eacute; dans le dossier squelettes/modeles.

	<br />

	Pour afficher la liste dans un article, il faut &eacute;crire dans l\'article <code><apropos|liste></code>.<br />

	Pour n\'afficher que le nombre de plugins ET d\'extensions actifs, utilisez <code><apropos|nombre></code>.<br />

	Pour n\'afficher que le nombre de plugins actifs, utilisez <code><apropos|plugins></code>.<br />

	Pour n\'afficher que le nombre d\'extensions actives, utilisez <code><apropos|extensions></code><br />
	
	Si vous souhaitez afficher la description compl&egrave;te d\'un plugin sp&eacute;cifique, &eacute;crivez <code><apropos|prefixe=le prefixe du plugin></code>. Ainsi, par exemple, pour afficher dans un article la description compl&egrave;te de ce plugin, utilisez <code><apropos|prefixe=apropos></code>.',
	'apropos_slogan' => 'Liste les plugins actifs et affiche une description sommaire de ceux-ci',
);
?>