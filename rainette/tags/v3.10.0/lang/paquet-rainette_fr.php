<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// R
	'rainette_description' => 'Cette nouvelle version de Rainette permet de choisir son service météo parmi Weather.com, Wunderground, World Weather Online ou Open Weather Map. Une configuration est disponible pour chaque service en particulier pour saisir une clé d\'enregistrement. Les affichages proposées par cette version sont incompatibles avec ceux des branches v1 et v2.

Ce plugin permet d\'afficher les conditions et les prévisions météorologiques d\'une ville donnée à partir du flux fourni par un des services méteorologiques supportés.
Il ne stocke aucune information en base de données ni ne gère le choix des villes.

L\'affichage des données météorologiques se fait principalement via l\'utilisation de modèles dans les squelettes. Le plugin propose des 
modèles par défaut comme {{rainette_previsions}} et {{rainette_conditions}}. Il est possible aussi d\'afficher les informations sur la ville choisie soit via le modèle {{rainette_infos}},
soit via la balise <code>#RAINETTE_INFOS</code>. Tous les affichages proposés par Rainette sont personnalisables (icônes, libellés, unités, présentation...).

Une page « Meteo » compatible avec les squelettes Z est disponible : elle propose les conditions et prévisions d\'une ville donnée.',
	'rainette_slogan'      => 'La météo au quotidien',
);
