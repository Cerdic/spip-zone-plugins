<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-scrut_prop
// Langue: fr
// Date: 23-05-2012 22:05:43
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// S
	'scrut_prop_description' => 'Vous pouvez insérer dans un article un formulaire de la manière suivante.
	<code><formulaire|scrut_prop|liste=A;B;C;...></code>, ou A;B;C,... sont des noms de liste.
	
	Un formulaire apparait. Il propose :
-* de remplir les voix pour chaque liste.
-* de remplir le nombre de blanc ou nul.
-* de remplir le nombre d\'inscrits.
-* de fournir le nombre de sièges à pourvoir.
-* de préciser si le scrutin se fait au plus fort reste ou à la plus forte moyenne.
-* de fixer un seuil de voix (en pourcentage) pour obtenir des sièges (le nombre de voix est arrondi à la valeur infèrieure).
-* de proposer une prime majoritaire (depuis la version 1.1)

	Une fois rempli, le formulaire teste si les valeurs sont cohérentes (pas plus de votants que d\'inscrits par exemple !). Puis il calcule la répartition de sièges.',
	'scrut_prop_slogan' => 'Répartissez les sièges',
);
?>