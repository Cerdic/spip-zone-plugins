<?php
function palette_insert_head($flux) {
  if ($GLOBALS['meta']['palette_public'])
	  $flux .= palette_header_common();
	return $flux;
}

function palette_header_prive($flux) {
	//if ($GLOBALS['meta']['palette_ecrire'])
	  $flux .= palette_header_common();
	return $flux;
}

/**
 * Retourne le code html head pour la palette
 * Cette fonction peut être surchargée (cf doc SPIP)
 *
 * @return string
 */
function palette_header_common() {
	$f = charger_fonction('palette_header', 'inc');
	if (is_callable($f))
		return $f();
	else
		return '';
}
?>