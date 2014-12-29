<?php
/**
 * Fonctions utiles au plugin Shop Livraisons
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function unite_mesure($id_livraison_zone,$mesure='',$brute=false){
    include_spip('inc/config');
    $config=lire_config('shop_livraison',array());
    
    $unite=sql_getfetsel('unite','spip_livraison_zones','id_livraison_zone='.$id_livraison_zone);
    if(!$brute){
        if($mesure) $unite=$mesure.' .'._T('livraison:label_unite_'.$unite);
        else $unite=_T('livraison:label_unite_'.$unite);
        }
    return $unite;
}

// Charge l'unité par défaut
function unite_defaut(){
    include_spip('inc/config');
    $unite_defaut=lire_config('shop_livraison/unite_defaut',''); 
    return $unite_defaut;
}

function unites_dispos(){
    $unites=charger_fonction('unites','inc');
    $unites=$unites();
    return $unites;
}

// Charge la mesure par défaut
function mesure_defaut(){
    $unite_defaut=unite_defaut(); 
    if($unite_defaut) return mesure_unite($unite_defaut) ;
    return ;
}


function mesure_unite($unite=""){
    $unites=unites_dispos();

    if($unite){
    if($unites){
      foreach($unites AS $mesure=>$u){
           if(array_key_exists($unite,$u))$mesure_unite=$mesure;
            }
        }
    }
    else mesure_defaut();
    return $mesure_unite;
}


if(!function_exists('devise_defaut_prix')){
	function devise_defaut_prix($prix='',$traduire=true){

    if($_COOKIE['spip_devise'])$devise_defaut=$_COOKIE['spip_devise'];
    else    $devise_defaut='€';
    if($prix)$devise_defaut= $prix.' '.$devise_defaut;

    return $devise_defaut;
}
	
	
}
?>
