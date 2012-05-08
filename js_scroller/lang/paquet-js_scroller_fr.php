<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-js_scroller
// Langue: fr
// Date: 08-05-2012 09:30:14
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// J
	'js_scroller_description' => '{{Javascript Scroller :: un scroller javascript de données XML}}

Ce plugin ajoute un widget javascript sous forme de bannière défilante présentant une liste d\'éléments SPIP du site.

Le widget s\'appelle en utilisant la balise : {{#JS_SCROLLER{width,height,type,maximum,coupe,direction,titre}}} avec :
- {{width et height}} les dimensions ({par défaut 600 x 20 pixels}),
- {{type}} le type d\'éléments SPIP présenté ({par défaut les articles}),
- {{maximum}} le nombre d\'entrées présentées ({par défaut 50}),
- {{coupe}} le nombre de caractères du texte présenté pour chaque entrée ({par défaut 40}),
- {{direction}} la direction du texte ({par défaut \'ltr\' : gauche->droite}),
- {{titre}} le titre du bandeau ({valeur par défaut selon le type - mettre \'non\' pour un titre vide}).

Le code javascript du {scroller} est tiré de [->http://javascripts.vbarsan.com/].

Une documentation interne est disponible lorsque le plugin est actif sur la page publique [js_scroller_documentation->../?page=js_scroller_documentation].',
	'js_scroller_slogan' => 'Un scroller javascript de données XML',
	'js_scroller_nom' => 'Javascript Scroller',
);
?>