<?php

function formulaires_ajouter_explication_charger($pipe,$exec) {

  include_spip("inc/autoriser");
  $args = array(
    'pipeline' => $pipe,
    'arg_exec' => $exec,
    'texte_explication' => _request("texte_explication")
  );
  
  $args['editable'] = autoriser("webmestre");
  
  return $args;

}

function formulaires_ajouter_explication_traiter($pipe,$exec) {

  include_spip("inc/autoriser");
  if(!autoriser("webmestre"))
    return array();  
  
  sql_insertq('spip_explications', array(
    'pipeline'=> $pipe,
    'exec' => $exec,
    'texte' => _request('texte_explication')
  ));
  
  return array();

}