<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// R
	'roles_documents_nom' => 'Rôles de documents',
	'roles_documents_slogan' => 'Typer des liaisons de documents',
	'roles_documents_description' => 'Ce plugin permet d\'attribuer un rôle aux documents liés aux contenus. Par exemple pour un squelette présentants des livres, on peut avoir besoin de distinguer les images de 1ère de couverture, de 4ème de couverture, les extraits, etc.

	Par défaut seuls 2 rôles génériques sont proposés : « logo » et « logo de survol ». C\'est aux squelettes de compléter cette liste au moyen du pipeline « lister_roles_documents » en fonction de leurs besoins.

	Bonus : les images avec le rôle « logo » seront utilisée automatiquement par la balise #LOGO_PATATE si aucun logo n\'a été choisi.',

);