<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/import_ics/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'archive' => 'archive',
	'archiver' => 'archivé',

	// C
	'cfg_attention' => 'Attention : les modifications effectuées sur ce formulaire n’affecteront pas les almanachs déjà créés et les évènements associés.',
	'cfg_configurer' => 'Configurer l’import de fichiers ICS',

	// D
	'depublier_anciens_evts' => 'Dépublier les anciens évènements',
	'depublier_anciens_evts_explication' => 'Cocher cette case si vous souhaitez que les évènements qui ne sont plus présents dans un flux distant soient automatiquement basculés à en « archivés »',

	// G
	'groupe_mots' => 'Groupe de mots',
	'groupe_mots_explication' => 'Lors de la création d’un almanach, forcer à choisir un mot clef dans le groupe.',

	// I
	'import_ics_titre' => 'Import_ics',

	// M
	'mot' => 'Mot',
	'mot_explication' => 'Vous pouvez aussi, alternativement, forcer l’association à un mot clef particulier.',
	'mot_facultatif' => 'Permettre de pas choisir de mot-clef',
	'mot_facultatif_explication' => 'Par défaut, un mot-clef doit doit être associé à un almanach et aux évènement liés. Cette option permet de rendre cela facultatif.',
	
	//T
	'titre_configuration' => 'Configurer les almanachs',
	
	// V
	'v_php' => 'Réglage verrouillé en PHP.'
);
