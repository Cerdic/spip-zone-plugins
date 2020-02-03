<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/metadonnees_photo/trunk/lang
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// P
	'photo_infos_description' => 'Ce plugin permet d’afficher les informations EXIF, GPS et IPTC d’un fichier JPEG.

Les informations EXIF sont manipulées, sans recours à l’extension EXIF de PHP, grâce au script de Vinay Yadav (sous licence LGPL).

Les informations IPTC nécessitent la fonction "iptcparse" de PHP et utilisent la classe "class_iptc" d’Alex Arica.',
	'photo_infos_nom' => 'Metadonnées photo',
	'photo_infos_slogan' => 'Afficher les infos EXIF, GPS et IPTC d’un fichier JPEG'
);
