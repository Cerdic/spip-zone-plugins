<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function reservations_detail_modifier($id_reservations_detail, $set=null) {

    $table_sql = table_objet_sql('reservations_detail');
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field'])) {
        spip_log("Objet $objet inconnu dans objet_modifier",_LOG_ERREUR);
        return _L("Erreur objet $objet inconnu");
    }
    include_spip('inc/modifier');

    $champ_date = '';
    if (isset($desc['date']) AND $desc['date'])
        $champ_date = $desc['date'];
    elseif (isset($desc['field']['date']))
        $champ_date = 'date';

    $white = array_keys($desc['field']);
    // on ne traite pas la cle primaire par defaut, notamment car
    // sur une creation, id_x vaut 'oui', et serait enregistre en id_x=0 dans la base
    $white = array_diff($white, array($desc['key']['PRIMARY KEY']));

    if (isset($desc['champs_editables']) AND is_array($desc['champs_editables'])) {
        $white = $desc['champs_editables'];
    }
    $c = collecter_requests(
        // white list
        $white,
        // black list
        array($champ_date,'statut','id_parent','id_secteur'),
        // donnees eventuellement fournies
        $set
    );

    $donnees_reservations_details=charger_fonction('donnees_reservations_details','inc');

    $c = array_merge($c,$donnees_reservations_details($id_reservations_detail,$c));

    // Si l'objet est publie, invalider les caches et demander sa reindexation
    if (objet_test_si_publie($objet,$id)){
        $invalideur = "id='reservations_detail/$id_reservations_detail'";
        $indexation = true;
    }
    else {
        $invalideur = "";
        $indexation = false;
    }

    if ($err = objet_modifier_champs('reservations_detail',$id_reservations_detail,
        array(
            'nonvide' => '',
            'invalideur' => $invalideur,
            'indexation' => $indexation,
             // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
            'date_modif' => (isset($desc['field']['date_modif'])?'date_modif':'')
        ),
        $c))
        return $err;

    // Modification de statut, changement de rubrique ?
    // FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
    //         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop. 
    $c = collecter_requests(array($champ_date, 'statut', 'id_parent'),array(),$set);
    $err = reservations_detail_instituer($id_reservations_detail, $c);

    return $err;
}


function reservations_detail_inserer( $id_parent=null, $set=null) {
    $objet='reservations_detail';

    $table_sql = table_objet_sql($objet);
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field']))
        return 0;

    $champs = array();



    if (isset($desc['field']['statut'])){
        if (isset($desc['statut_textes_instituer'])){
            $cles_statut = array_keys($desc['statut_textes_instituer']); 
            $champs['statut'] = reset($cles_statut);
        }
        else
            $champs['statut'] = 'attente';
    }


    if ((isset($desc['date']) AND $d=$desc['date']) OR isset($desc['field'][$d='date']))
        $champs[$d] = date('Y-m-d H:i:s');

    if ($set)
        $champs = array_merge($champs, $set);

    // Envoyer aux plugins
    $champs = pipeline('pre_insertion',
        array(
            'args' => array(
                'table' => $table_sql,
            ),
            'data' => $champs
        )
    );

    $id = sql_insertq($table_sql, $champs);

    if ($id){
        pipeline('post_insertion',
            array(
                'args' => array(
                    'table' => $table_sql,
                    'id_objet' => $id,
                ),
                'data' => $champs
            )
        );
    }

    return $id;
}

function reservations_detail_instituer($id_reservations_detail, $c, $calcul_rub=true) {

    include_spip('inc/autoriser');
    include_spip('inc/rubriques');
    include_spip('inc/modifier');
    
    $row = sql_fetsel('*','spip_reservations_details','id_reservations_detail='.intval($id_reservations_detail));
    
    
    if(!$places=$c[places]){
        $places=sql_getfetsel('places','spip_evenements','id_evenement='.$row['id_evenement']);
        }
    $statut_ancien = $statut = $row['statut'];

    $s = isset($c['statut']) ? $c['statut'] : $statut;

    $champs['statut']= $s ;
    
    // compter les réservations
    if ($s != $statut and $s=='accepte') {
        if(intval($places) AND $places>0){
            $sql=sql_select('quantite','spip_reservations_details','id_evenement='.$c['id_evenement'].' AND statut ='.sql_quote('accepte'));
            
            $reservations=array();
            while($data=sql_fetch($sql)){
                $reservations[]=$data['quantite'];
            }
            if(array_sum($reservations)>=$places)$champs['statut']='attente';
            
        }

    }


    // Envoyer aux plugins
    $champs = pipeline('pre_edition',
        array(
            'args' => array(
                'table' => 'spip_reservations_details',
                'id_reservation' => $id,
                'action'=>'instituer',
                'statut_ancien' => $statut_ancien,
                'date_ancienne' => $date_ancienne,
            ),
            'data' => $champs
        )
    );

    if (!count($champs)) return '';
    // Envoyer les modifs.
    objet_editer_heritage('reservations_detail', $id_reservations_detail,'', $statut_ancien, $champs);

    // Invalider les caches
    include_spip('inc/invalideur');
    suivre_invalideur("id='reservations_detail/$id_reservations_detail'");


    // Pipeline
    pipeline('post_edition',
        array(
            'args' => array(
                'table' => 'spip_reservations_details',
                'id_reservation' => $id_reservations_detail,
                'action'=>'instituer',
                'statut_ancien' => $statut_ancien,
                'date_ancienne' => $date_ancienne,
                'id_parent_ancien' => $id_rubrique,
            ),
            'data' => $champs
        )
    );

    return ''; // pas d'erreur
}
