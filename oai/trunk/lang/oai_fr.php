<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	// C
	'champ_admins_label' => 'Emails des administrateurs des entrepôts',
	'champ_admins_explication' => 'Un ou plusieurs emails séparés par des virgules. À défaut, c\'est celui du webmaster du site qui est utilisé. Cette information est utilisée dans l\'identification des entrepôts.',
	'cfg_titre_parametrages' => 'Configuration',
	
	//D
	'depot_oai' => 'Dépôt OAI',
	
	// E
	'erreur_badargument_interdit' => 'Argument interdit : «@arg@» n’existe pas pour le verbe «@verbe@».',
	'erreur_badargument_obligatoire' => 'Argument manquant : «@arg@» est obligatoire pour le verbe «@verbe@».',
	'erreur_badargument_resumptiontoken_exclusif' => 'L’argument «resumptionToken» doit être le seul argument.',
	'erreur_badverb_absent' => 'La requête doit comporter l’argument «verb».',
	'erreur_badverb_inconnu' => 'Le verbe «@verbe@» ne fait pas parti des valeurs autorisées.',
	'erreur_cannotdisseminateformat' => 'Le format de méta-données «@format@» n’existe pas.',
	'erreur_iddoesnotexist' => 'L’identifiant demandé ne correspond à aucun enregistrement.',
	'erreur_nometadataformats' => 'Aucun format de méta-données n’a été trouvé pour ce dépot.',
	'erreur_norecordsmatch' => 'Aucun enregistrement ne correspond à ces critères.',
	'erreur_nosethierarchy' => 'Ce dépôt n’a aucune catégorie.',
	
	// T
	'titre_page_configurer_oai' => 'Configurer OAI',
);