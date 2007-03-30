<?php

chdir('../../../../ecrire/');

require 'inc_version.php';

include_spip('inc/mots');
include_spip('inc/texte');

$f = _request('field');
$q = _request('value');

$s = spip_query("SELECT nom FROM spip_auteurs WHERE nom LIKE "._q("%$q%")." ORDER BY nom LIMIT 10");
while ($t = spip_fetch_array($s)) {
	echo preg_replace(",[\n\r],", ' ', supprimer_tags(typo($t['nom'])))."\n";
}


?>