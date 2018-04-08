<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function balise_NOIZETIER_NOISETTE_PREVIEW_dist($p)
{
	$id_noisette = champ_sql('id_noisette', $p);
	$type_noisette = champ_sql('type_noisette', $p);
	$parametres = champ_sql('parametres', $p);

	$inclusion = "recuperer_fond(
		'noisette_preview',
		array_merge(unserialize($parametres), array('type_noisette' => $type_noisette))
	)";

	$p->code = "$inclusion";
	$p->interdire_scripts = false;

	return $p;
}
