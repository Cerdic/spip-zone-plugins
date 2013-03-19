<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function genie_tradlang_verifier_versions_dist($t) {
	$creer_versions = charger_fonction('tradlang_creer_premieres_revisions','inc');
	$creer_versions();
	return 0;
}
?>