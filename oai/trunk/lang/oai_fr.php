<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	//D
	'depot_oai' => 'Dépôt OAI',
	
	// E
	'erreur_badArgument_interdit' => 'Argument interdit : «@arg@» n’existe pas pour le verbe «@verbe@».',
	'erreur_badArgument_obligatoire' => 'Argument manquant : «@arg@» est obligatoire pour le verbe «@verbe@».',
	'erreur_badArgument_resumptionToken_exclusif' => 'L’argument «resumptionToken» doit être le seul argument.',
	'erreur_badVerb_absent' => 'La requête doit comporter l’argument «verb».',
	'erreur_badVerb_inconnu' => 'Le verbe «@verbe@» ne fait pas parti des valeurs autorisées.',
	'erreur_cannotDisseminateFormat' => 'Le format de méta-données «@format@» n’existe pas.',
	'erreur_idDoesNotExist' => 'L’identifiant demandé ne correspond à aucun enregistrement.',
	'erreur_noMetadataFormats' => 'Aucun format de méta-données n’a été trouvé pour ce dépot.',
	'erreur_norecordsmatch' => 'Aucun enregistrement ne correspond à ces critères.',
	'erreur_noSetHierarchy' => 'Ce dépôt n’a aucune catégorie.',
);
