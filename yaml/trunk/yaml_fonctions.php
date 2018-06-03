<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Filtre permettant d'utiliser yaml_decode_file() dans un squelette
function decoder_yaml($fichier, $options = array()) {
	include_spip('inc/yaml');
	return yaml_decode_file($fichier, $options);
}

function inc_yaml_to_array($u) {
	include_spip('inc/yaml');
	return yaml_decode($u);
}
