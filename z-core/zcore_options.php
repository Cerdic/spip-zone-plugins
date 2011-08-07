<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

// demander a SPIP de definir 'type-page' dans le contexte du premier squelette
define('_DEFINIR_CONTEXTE_TYPE_PAGE',true);
define('_ZPIP',true);
// differencier le cache,
// la verification de credibilite de var_zajax sera faite dans public_styliser_dist
// mais ici on s'assure que la variable ne permet pas de faire une inclusion arbitraire
// avec un . ou un /
if ($z = _request('var_zajax')
  AND !preg_match(",[^\w-],",$z)) {
	$GLOBALS['marqueur'] .= "$z:";
	$GLOBALS['flag_preserver'] = true;
}
else {
	// supprimer cette variable dangeureuse
	set_request('var_zajax','');
}

?>