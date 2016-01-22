<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// I
	'identifiants_nom' => 'Identifiants',
	'identifiants_slogan' => 'Ajouter des identifiants uniques à n\'importe quel objet.',
	'identifiants_description' => 'On a parfois besoin de différencier certains objets : un article sur les mentions légales, une rubrique contenant des vidéos, etc.
	Le but du plugin est de permettre d\'attribuer des identifiants uniques à certains objets, et donc de les sélectionner en fonction de ceux-ci.
	Ex. : <BOUCLE_rubrique(RUBRIQUES){identifiant=un_identifiant}>. Les identifiants sont stockés dans la table `spip_identifiants`.
	L\'ajout d\'identifiants n\'est proposé que pour les tables ne possédant pas déjà une colonne `identifiant`, donc elles ne seront pas concernées par l\'utilisation de ce plugin.',
);

?>
