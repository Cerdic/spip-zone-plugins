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

    Dans votre squelette, tapez les code suivant : <br />
	
	<code><INCLURE{fond=modeles/apropos_lister_tout}></code> pour afficher la liste de tous plugins tiers et plugins verrouill&eacute;s actifs et inactifs ;<br />
	<code><INCLURE{fond=modeles/apropos_liste}></code> pour afficher la liste des plugins et plugins verrouill&eacute;s actifs ;<br />
	<code><INCLURE{fond=modeles/apropos_nombre></code> pour n\'afficher que le nombre de plugins ET de plugins verrouill&eacute;s actifs ;<br />
	<code><INCLURE{fond=modeles/apropos_plugins></code> pour n\'afficher que le nombre de plugins actifs ;<br />
	<code><INCLURE{fond=modeles/apropos_extensions></code> pour n\'afficher que le nombre de plugins verrouill&eacute;s actifs ; <br />
	<code><INCLURE{fond=modeles/apropos_adisposition></code> pour n\'afficher que le nombre de plugins dans le dossier plugins ;<br />
	<code><INCLURE{fond=modeles/apropos_disponible></code> pour n\'afficher que le nombre total de plugins verrouill&eacute;s et de plugins de votre configuration.<br />
	<code><INCLURE{fond=modeles/apropos}{prefixe=le préfixe du plugin}></code> si vous souhaitez afficher la description compl&egrave;te d\'un plugin sp&eacute;cifique. Ainsi, par exemple, pour afficher dans un article la description compl&egrave;te du plugin À propos des plugins, utilisez <code><INCLURE{fond=modeles/apropos}{prefixe=apropos}></code>.<hr />
	
	Dans un article, &eacute;crivez :<br>
	
	<code><apropos|lister_tout></code> pour afficher la liste de tous plugins tiers et plugins verrouill&eacute;s actifs et inactifs ;<br />
	<code><apropos|liste></code> pour afficher la liste des plugins et plugins verrouill&eacute;s actifs ;<br />
	<code><apropos|nombre></code> pour n\'afficher que le nombre de plugins ET de plugins verrouill&eacute;s actifs ;<br />
	<code><apropos|plugins></code> pour n\'afficher que le nombre de plugins actifs ;<br />
	<code><apropos|extensions></code> pour n\'afficher que le nombre de plugins verrouill&eacute;s actifs ; <br />
	<code><apropos|adisposition></code> pour n\'afficher que le nombre de plugins dans le dossier plugins ;<br />
	<code><apropos|disponible></code> pour n\'afficher que le nombre total de plugins verrouill&eacute;s et de plugins de votre configuration.<br />
	<code><apropos|prefixe=le prefixe du plugin></code> si vous souhaitez afficher la description compl&egrave;te d\'un plugin sp&eacute;cifique, &eacute;crivez <code><apropos|prefixe=le prefixe du plugin></code>. Ainsi, par exemple, pour afficher dans un article la description compl&egrave;te de ce plugin, utilisez <code><apropos|prefixe=apropos></code>.<hr />',
	'apropos_slogan' => 'Liste les plugins actifs et affiche une description sommaire de ceux-ci'
);
?>