<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'identite_extra_titre' => 'Identité extra',	
	'identite_extra_description' => 'Déclarez vos champs supplémentaires dans <em>mes_options.php</em>. Une fois saisies dans <em>?exec=configurer_identite</em>, les valeurs sont accessibles dans vos squelettes via <code>#CONFIG{identite_extra/telephone}</code>.' ,
	'identite_extra_slogan' => 'Étendre facilement le formulaire Identité du site',
);
