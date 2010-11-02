<?php

function explications_affiche_gauche($flux) {
  //affiche_gauche explications pour exec
  include_spip("inc/explications");
  $flux["data"] .= explications_par_pipeline("affiche_gauche",$flux["args"]);
  
  return $flux;
}

function explications_affiche_milieu($flux) {
  //affiche_gauche explications pour exec
  include_spip("inc/explications");
  $flux["data"] .= explications_par_pipeline("affiche_milieu",$flux["args"]);
  
  return $flux;
}

function explications_affiche_droite($flux) {
  //affiche_gauche explications pour exec
  include_spip("inc/explications");
  $flux["data"] .= explications_par_pipeline("affiche_droite",$flux["args"]);
  
  return $flux;
}


function explications_declarer_tables_principales($tables) {
  $explication_field = array(
    "id_explication"  => "bigint(21) NOT NULL",
    "pipeline"        => "varchar(100) DEFAULT '' NOT NULL",
    "exec"            => "varchar(100) DEFAULT '' NOT NULL",
    "texte"            => "text DEFAULT '' NOT NULL",
    "maj"   => "TIMESTAMP"  
  );
  $explication_key = array(
    "PRIMARY KEY"   => "id_explication",
    "KEY args"    => "pipeline,exec"
  );

  $tables["spip_explications"] = array(
        'field' => &$explication_field,
        'key' => &$explication_key  
  );
  return $tables;
}