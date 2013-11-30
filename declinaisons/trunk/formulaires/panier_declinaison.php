<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_panier_declinaison_charger_dist($id_objet_produit,$objet_produit='article'){
    
   if(is_array($id_objet_produit))$id_objet_produit=implode(',',$id_objet_produit);
   if($id_objet_produit)$sql=sql_select('*','spip_prix_objets','id_objet IN ('.$id_objet_produit.') AND objet='.sql_quote($objet_produit));
   
   $declinaisons=array();
   
    $id_panier = session_get('id_panier');
    // S'il n'y a pas de panier, on le crée
    if (!$id_panier){
        include_spip('inc/paniers');
        $id_panier = paniers_creer_panier();
    }
   
   while($data=sql_fetch($sql)){
       if($data['prix_ht']!=0.00){
        $data['prix'] = $data['prix_ht'];          
        $data['taxe'] = _T('shop:prix_ht');
       }
       else{
         $data['prix'] = $data['prix']; 
         $data['taxe'] = _T('prix_objets:prix_ttc');      
       }
       $declinaisons[]=$data;
       
        }

   $valeurs=array(
    'objet_produit'=>$objet_produit,
    'id_objet_produit'=>$id_objet_produit,    
    'objet'=>'prix',
    'id_objet'=>'',
    'declinaisons'=>$declinaisons,
    'id_prix_objet'=>'',
    'retour'=>'');

	return $valeurs;			
}

function formulaires_panier_declinaison_traiter_dist($id_objet,$objet='article'){
        
    $remplir_panier=charger_fonction('remplir_panier','action/');
  
    $remplir_panier('prix_objet-'._request('id_prix_objet'));
    
    include_spip('inc/invalideur');
    suivre_invalideur("id='id_panier/$id_panier'");
    
    $valeurs['message_ok']=true;
  
    return $valeurs;
}

?>