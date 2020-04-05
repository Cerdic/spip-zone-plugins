<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/config');
function palette_insert_head($flux) {
  $cfg = lire_config('palette');
  if ($cfg['palette_public'] == 'on')
	  $flux .= palette_header_common('public');
	return $flux;
}

function palette_header_prive($flux) {
  $cfg = lire_config('palette');
	if ($cfg['palette_ecrire'] == 'on')
	  $flux .= palette_header_common('prive');
	return $flux;
}

/**
 * Retourne le code html head pour la palette
 * Cette fonction peut être surchargée (cf doc SPIP)
 *
 * @return string
 */
function palette_header_common($type) {
	# spip_log("type = $type");
	$f = charger_fonction('palette_header', 'inc');
	if (is_callable($f))
		return $f($type);
	else
		return '';
}
?>
