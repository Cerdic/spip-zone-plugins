<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Utiliser le dossier de cache de SPIP pour stocker les caches de fonts
include_spip('inc/flock');
$dompdf_dir = sous_repertoire(_DIR_CACHE, 'fonts');
define('DOMPDF_FONT_DIR', $dompdf_dir);
