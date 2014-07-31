<?php
/**
 * Affichage de la fiche complete des objets de la base AMENGEES
 *
 * @param array $flux
 * @return array
 */
function requeteursql_afficher_fiche_objet($flux){
    spip_log($flux);
    if ($flux['args']['type']=='sql_requete'){

        $id_sql_requete = _request('id_sql_requete');
        $result = sql_select('requetesql','spip_sql_requetes',"id_sql_requete = $id_sql_requete");
        spip_log($sql);
        if($res = sql_fetch($result)) {
            $sql = $res['requetesql'];
            if($res = sql_query("$sql LIMIT 0,100")) {
                $aRes = sql_fetch_all($res);
                spip_log($aRes);
                $flux['data'] .= recuperer_fond('prive/squelettes/fiche_objet/sql_requete',array('res'=>$aRes),array('ajax'));
            }
        }
        else{
            echo(sql_error());
        }
    }
    return $flux;
}
?>
