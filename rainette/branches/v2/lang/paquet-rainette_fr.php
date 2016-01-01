<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-rainette
// Langue: fr
// Date: 05-08-2012 17:10:32
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// R
	'rainette_description' => 'Cette nouvelle version de Rainette permet de choisir son service météo parmi weather.com, Wunderground, World Weather Online, Open Weather Map ou Yahoo. Une configuration est disponible pour chaque service en particulier pour saisir une clé d\'enregistrement. Les affichages proposées par cette version sont incompatibles avec ceux de la branche v1.

Ce plugin permet d\'afficher les conditions et les prévisions météorologiques d\'une ville donnée à partir du flux xml fourni par un des services méteorologiques supportés.
Il ne stocke aucune information en base de données ni ne gère le choix des villes.

L\'affichage des données météorologiques se fait principalement via l\'utilisation de modèles dans les squelettes. Le plugin propose des 
modèles par défaut comme {{rainette_previsions}} et {{rainette_conditions}}. Il est possible aussi d\'afficher les informations sur la ville choisie soit via le modèle {{rainette_infos}},
soit via la balise {{RAINETTE_INFOS}}. Tous les affichages proposés par Rainette sont personnalisables (icônes, libellés, unités, présentation...).

Une page « Meteo » compatible avec les squelettes Z est disponible : elle propose les conditions et prévisions à 10 jours d\'une ville donnée.

Essayez la page de démo {demo/rainette.html} pour des exemples d\'utilisation.',
	'rainette_slogan' => 'La météo au quotidien',
);
?>