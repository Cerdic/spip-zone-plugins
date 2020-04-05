<?php

include_spip('inc/config');
include_spip('medias_dereferencer_fonctions');

$config_md = lire_config('medias_dereferencer');

if (isset($config_md['htaccess'])) {
	if ($config_md['htaccess'] === 'oui') {
		md_creation_htaccess_img();
	} elseif ($config_md['htaccess'] === 'non') {
		md_suppression_htaccess_img();
	}
}
