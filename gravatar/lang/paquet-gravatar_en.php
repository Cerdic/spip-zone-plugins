<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-gravatar
// Langue: en
// Date: 01-04-2013 13:21:03
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// G
	'gravatar_description' => 'Enables to use a cache system to store the gravatars.
_ To use it in a loop in this manner : <code>#GRAVATAR{email, size, URL of the default image}</code>
_ Example : <code>#GRAVATAR{#EMAIL,80,#URL_SITE_SPIP/defaut-gravatar.gif}</code>

Also extend the #LOGO_AUTEUR tag in order to take into account the gravatar of an author if it exists, including in forums and petitions.
_ Configures a default image, and image size.',
	'gravatar_slogan' => 'Display Gravatar for author or forum poster',
);
?>