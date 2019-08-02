<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// I
	'identifiants_nom' => 'Identifiants',
	'identifiants_slogan' => 'Ajouter des identifiants uniques aux objets.',
	'identifiants_description' => 'Ce plugin permet d\'attribuer des identifiants textes uniques aux objets.
	Ainsi, au lieu de faire <code><BOUCLE_rubrique(RUBRIQUES){id_rubrique=N}></code>, vous pourrez faire par exemple <code><BOUCLE_rubrique(RUBRIQUES){identifiant=ecureuil}></code>.
	Seuls les webmestres peuvent voir et manipuler les identifiants : ils sont (en principe) à utiliser avec parcimonie, et ne devraient pas changer une fois définis.
	Les objets auxquels ont peut ajouter des identifiants sont à définir sur la page de configuration du plugin.
	Seules les tables ne possédant pas déjà une colonne `identifiant` sont proposées.
	Les identifiants sont stockés dans la table de liens `spip_identifiants`',
);
