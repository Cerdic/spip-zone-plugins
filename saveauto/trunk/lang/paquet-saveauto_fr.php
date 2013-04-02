<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saveauto/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'saveauto_description' => 'Permet de réaliser une sauvegarde MySQL de toute la base de données utilisée par SPIP.
			Le fichier .zip (ou .sql) obtenu est stocké dans un répertoire (par défaut /tmp/dump, configurable)
			et peut être envoyé par mail.

			Les sauvegardes stockées considérées comme obsolètes (en fonction du paramètre de config correspondant)
			sont automatiquement détruites.

			Une interface permet de déclencher manuellement les sauvegardes et de gérer les fichiers générés',
	'saveauto_nom' => 'Sauvegarde automatique',
	'saveauto_slogan' => 'Sauvegarde MySQL automatique de la base de données de SPIP'
);

?>
