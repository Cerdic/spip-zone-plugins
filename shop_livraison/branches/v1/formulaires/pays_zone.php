<?php
/**
 * Gestion du formulaire de d'édition de livraison_zone
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_pays_zone_charger_dist(){
    
    $_id_livraison_zone=_request('_id_livraison_zone')?_request('_id_livraison_zone'):array();
    $id_livraison_zone=_request('id_livraison_zone');  
    $id_continent=_request('id_continent');       
    
    $valeurs=array('_id_livraison_zone'=>_request('_id_livraison_zone'),'id_livraison_zone'=>$id_livraison_zone,'id_continent'=>$id_continent,'effacer'=>'');
    
    $valeurs['_hidden'].='<input type="hidden" name="id_livraison_zone" value="'.$id_livraison_zone.'"/>';
    $valeurs['_hidden'].='<input type="hidden" name="id_continent" value="'.$id_continent.'"/>';
	return $valeurs;
}



function formulaires_pays_zone_traiter_dist(){
    $valeurs=array();
    
    $_id_livraison_zone=_request('_id_livraison_zone');
    
    
    $v=array();
    
    foreach($_id_livraison_zone AS $id_pays=>$id_livraison_zone){
        if(_request('effacer'))$id_livraison_zone='';
        sql_updateq('spip_pays',array('id_livraison_zone'=>$id_livraison_zone),'id_pays='.$id_pays);
    }

    
	return $valeurs;
}


?>