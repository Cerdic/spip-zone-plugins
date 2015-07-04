<?php
/**
 * Plugin Shop Livraison
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function action_gestion_zones_dist($arg=null) { 
    if (is_null($arg)){
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $arg = $securiser_action();
    }
    
    include_spip('inc/session');
    include_spip('inc/config');

    //Les variables
    list($action,$id,$id_objet)= explode("-",$arg);    

    switch ($action) {
        case 'ajouter_pays_continent':
                sql_updateq('spip_pays',array('id_livraison_zone'=>$id),'id_continent='.$id_objet);
            break;
        case 'ajouter_pays':
                sql_updateq('spip_pays',array('id_livraison_zone'=>$id),'id_pays='.$id_objet);
            break;  
        case 'eliminer_zone':
                sql_delete('spip_livraison_zones','id_livraison_zone='.$id);
                sql_delete('spip_livraison_montants','id_livraison_zone='.$id);            
                sql_updateq('spip_pays',array('id_livraison_zone'=>'0'),'id_livraison_zone='.$id);
                    
            break;  
        case 'eliminer_montant':
                sql_delete('spip_livraison_montants','id_livraison_montant='.$id);
                    
            break;  
                                                             

    }


    return array('action_ok'=>true,'id_objet'=>$id_objet);
}

?>
