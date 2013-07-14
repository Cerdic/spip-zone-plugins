<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-bible
// Langue: fr
// Date: 23-05-2012 15:02:15
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// B
	'bible_description' => 'Le plugin Spip-Bible permet de citer rapidement des extraits de la Bible dans le texte d’un article. Sous SPIP 2.0 avec SPIP-Bonux, propose un "presse-papier" biblique à côté des formulaires d\'éditions. Vous pouvez aussi utilisez un modéle, mais cette méthode est désuéte et déconseillée.

Ex : <code><bible|passage=Gn1,2-2,1></code> citera la Genèse du chapitre 1, verset 2 au chapitre 2, verset 1 inclu.

On peut passer des options au modéle. La valeur des options est "non" ou "oui", sauf pour traduction.

Ces options sont :
-* numeros : Pour afficher les numéros de versets et de chapitre dans le corps de texte.
-* retour : Pour faire des retours chariots entre les versets
-* ref : Pour afficher les références à la suite du passage
-* traduction : Pour choisir la traduction.

La liste des traductions disponibles est sur [Spip-Contrib->http://www.spip-contrib.net/Spip-Bible-traductions-disponibles]',
	'bible_slogan' => 'Pour faire rapidement des citations de la Bible',
);
?>