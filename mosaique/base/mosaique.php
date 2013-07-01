<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
 
function mosaique_declarer_champs_extras($champs = array()) {
  $champs['spip_articles']['mosaique'] = array(
      'saisie' => 'mosaique',//Type du champ (voir plugin Saisies)
      'options' => array(
            'nom' => 'mosaique',
            'label' => '',
            'sql' => "varchar(1000) NOT NULL DEFAULT ''",
            'defaut' => '',// Valeur par defaut
            'env' => 'oui',
      ),
  );
  return $champs;      
}
?>