<?php
lire_metas();
$lire_habillages_squelettes = $GLOBALS['meta']['habillages_squelettes'];

if ($lire_habillages_squelettes == "dist") {
$GLOBALS['dossier_squelettes'] = 'dist';
}
?>