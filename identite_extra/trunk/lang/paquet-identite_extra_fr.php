<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'identite_extra_titre' => 'Identité extra',
	'identite_extra_description' => 'Déclarez vos champs supplémentaires dans <code>mes_options.php</code>. Une fois saisies dans <code>?exec=configurer_identite</code>, les valeurs sont accessibles dans vos squelettes via la balise <code>#IDENTITE_NOM_CHAMP</code>.',
	'identite_extra_slogan' => 'Étendre facilement le formulaire Identité du site',
);
