<?php
function formulaires_recherche_seances_charger_dist() {
  $valeurs = array(
  	'id_rubrique' => _request('id_rubrique'),
  	'id_article' => _request('id_article'),
  	'id_endroit' => _request('id_endroit'),
  	'date_seance' => _request('date_seance')
  );
  return $valeurs;
}


?>