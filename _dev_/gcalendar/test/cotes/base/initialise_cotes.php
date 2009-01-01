<?php

function initialise_cotes() {
	
	// on vérifie si la table est présente
$requests = Array(
  'DESCRIBE spip_cotes_classes',
  'DESCRIBE spip_cotes_cotes',
  'DESCRIBE spip_cotes_etudiants',
  'DESCRIBE spip_cotes_exercices',
  'DESCRIBE spip_cotes_mails',
);

$charger = false;
foreach($requests as $request) {
  $result = spip_query($request);
  if (!$result) {
    $charger = true;
  }
}
if ($charger == true) {
  include_spip('base/create');
  include_spip('base/abstract_sql');
  // création de la structure de la bdd
  creer_base();
	}
	
}

?>