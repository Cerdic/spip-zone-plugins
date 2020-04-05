<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'domlang_description' => 'Ce plugin convient pour un site qui utilise des secteurs de langues. En configuration, pour chaque secteur, vous pourrez définir une URL spécifique (un nom de domaine) qui correspond à cette langue.

Certaines balises d\'URL sont modifiées afin d\'utiliser les domaines configurés : 
-* `#URL_SITE_SPIP` retourne l\'URL du site pour la langue en cours d\'utilisation
-* `#URL_ARTICLE` ou `#URL_RUBRIQUE` retournent une URL relative si l\'article ou la rubrique est dans la langue en cours, sinon, retourne une URL absolue de l\'article ou la rubrique avec l\'URL correspondant à sa langue.

D\'autres balises ne sont pas modifiées, par exemple `#URL_ARTICLE` retourne toujours une URL relative. ',
	'domlang_nom' => 'Domaines par secteur de langue',
	'domlang_slogan' => 'Permet de définir 1 domaine pour chaque secteur de langue',
);
