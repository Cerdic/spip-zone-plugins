<?php
include_spip('base/create');
include_spip('base/abstract_sql');
include_spip('base/captcha');

creer_base();

$current_version = 0.0;
ecrire_meta('ftcaptcha/captcha_base_version',$current_version);
ecrire_meta('ftcaptcha/captcha_activation','oui');
?>