<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

// Fichier produit par PlugOnet
// Module: paquet-languepreferee
// Langue: en
// Date: 07-06-2017 07:09:46
// Items: 1

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// L
	'languepreferee_description' => 'This plugin checks the visitor browser prefered languages and redirects automagically to this language within a multi language website having one section per language. It requiers not to have a specific sommaire page, the home pages will be localized to each sectors.

Use only the <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION</code> tag by putting the following code in <code>sommaire.html</code>, but any other code, allowing each visitors to be redirected to the most meaningful sector: [(#LANGUE_PREFEREE_SECTEUR_REDIRECTION|sinon{Activate the langue_preferee plugin})]

If not any website language suits the visitor\'s preferred language, the website default language is selected. By the way, if this language is not used by any sector (yes you can !), the first avalaible sector is selected. It\'s also possible to exclude some sectors from the possible redirections. In this case, just add as parameter a comma seperated list of unwanted sectors to <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION</code>, for example: <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION{"3,12"}</code>, the tag can not redirect to sector 3 or 12.

It\'s possible to let the visitor choose it\'s own preferred language, that may be different from its browser, by adding (for example selecting english) <code>/?lang=en</code> calling sommaire page. Therefore this choice is stored in a cookie for further use and will take precedence to the browser preference. The <code>#LANGUE_PREFEREE_LIEN_EFFACE_COOKIE</code> tag allows you to add a link allowing to remove this cookie. Using a parameter like: <code>#LANGUE_PREFEREE_LIEN_EFFACE_COOKIE{my own message}</code> prompts your "own message" instead of the default one.',
	'languepreferee_slogan' => 'Redirect the user on its browser languages preferences',
);
