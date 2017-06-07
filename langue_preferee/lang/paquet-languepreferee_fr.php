<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

// Fichier produit par PlugOnet
// Module: paquet-languepreferee
// Langue: fr
// Date: 07-06-2017 07:09:46
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// L
	'languepreferee_description' => 'Ce plugin permet d\'identifier les langues préférées configurées par l\'internaute dans son navigateur et de lui proposer automatiquement la langue la plus pertinente dans un site multilingue ayant un secteur par langue.
Ce mécanisme suppose qu\'il n\'y a pas de page sommaire particulière, les pages d\'accueil localisées étant des pages de secteurs.

Utilisez la balise <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION</code> en insérant le code suivant dans <code>sommaire.html</code>, à l\'exclusion de tout autre code, pour que l\'internaute soit redirigé automatiquement vers le secteur qu\'il est le plus susceptible de comprendre, selon la configuration de langue de son navigateur : <code>[(#LANGUE_PREFEREE_SECTEUR_REDIRECTION|sinon{Activer le plugin langue_preferee})]</code>

Si aucune langue disponible n\'est compatible avec les choix configurés dans le navigateur, la langue par défaut du site est choisie. Si cette langue n\'est utilisée par aucun secteur (!!!), le premier secteur trouvé est choisi.
Il est possible de filtrer le ou les secteurs pour ne par rediriger vers un secteur non souhaité. Pour cela mettez la liste des secteurs non souhaités séparés par des virgules en paramètre de <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION</code>, par exemple : <code>#LANGUE_PREFEREE_SECTEUR_REDIRECTION{"3,12"}</code>, la balise ne pourra pas rediriger ni vers le secteur 3, ni vers le 12.

Il est possible de laisser l\'internaute choisir sa langue préférée, différente de celle configurée dans son navigateur, en la précisant en paramètre d\'appel du sommaire, avec <code>/?lang=fr</code> par exemple pour le français. Ce choix est alors stocké dans un cookie pour utilisation ultérieure prioritaire sur la configuration du navigateur. La balise <code>#LANGUE_PREFEREE_LIEN_EFFACE_COOKIE</code> permet de proposer un lien de suppression de ce cookie.
<code>#LANGUE_PREFEREE_LIEN_EFFACE_COOKIE{mon message personnalise}</code> permet de remplacer le message proposé par défaut.',
	'languepreferee_slogan' => 'Diriger l\'internaute vers le secteur de sa langue',
);
