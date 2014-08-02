<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_sql_requete_tables_charger_dist()
{
    if (!autoriser('modifier','sqlrequete',$id_sql_requete)){
        $valeurs['editable'] = false;
    } else {
        $valeurs['tables'] = sql_alltable('%');
        $valeurs['table'] =_request('table');
    }
    return $valeurs;
}


function formulaires_editer_sql_requete_tables_verifier_dist() {
    $erreurs = array();
    return $erreurs;
}


function formulaires_editer_sql_requete_tables_traiter_dist() {
    $table = _request('table');
    $champs = sql_showtable($table);
    $res['message_ok'] = array_keys($champs['field']);
    $res['editable'] = true;
    return $res;
}

?>
