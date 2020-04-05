<?php
/**
 * Utilisations de pipelines par Shop Livraisons
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
    
function shop_livraisons_post_insertion($flux){
	include_spip('inc/config');
    // Après insertion d'une commande "encours" et s'il y a un panier en cours
    if (
        $flux['args']['table'] == 'spip_commandes'
        and ($id_commande = intval($flux['args']['id_objet'])) > 0
        and $flux['data']['statut'] == 'encours'
        and include_spip('inc/filtres')
    ){
        $pays=_request('pays');
        $row=sql_fetsel('id_livraison_zone,unite','spip_pays LEFT JOIN spip_livraison_zones USING(id_livraison_zone)','code='.sql_quote($pays));
        
        $sql=sql_select('quantite,objet,id_objet','spip_commandes_details','id_commande='.$id_commande);
        include_spip('inc/pipelines_ecrire');
        $quantite=array();
        $mesure=array();
        $id_objet=array() ;
        
        while($data=sql_fetch($sql)){
            $prix_unitaire_ht='';
            $montant='';
            
            //On regarde si on une unité s'applique
            if(isset($row['unite'])){
            	//Si le plugin prix_objets est activé
            	if(test_plugin_actif('prix_objets')){
            		//On chercher l'objet attaché au prix
                	$objet_prix=sql_fetsel('objet,id_objet','spip_prix_objets','id_prix_objet='.$data['id_objet']);
					$objet=$objet_prix['objet'];
					$id_objet=$objet_prix['id_objet'];
            	}
				//Sinon on prend les donnés de l'objet depuis le détail de la commande
				else{
					$objet=$data['objet'];
					$id_objet=$data['id_objet'];
				}
                
                 //On constitue les données de cet objet
                $e = trouver_objet_exec($objet);
                $table=table_objet_sql($objet);
                $id_table_objet=$e['id_table_objet']; 
				 
                 //On récupère la mesure pour l'objet       
                $mesure[]=sql_getfetsel('mesure',$table,$id_table_objet.'='.$id_objet)*$data['quantite'];
            }
        }
        
        $valeurs= array(
                        'id_commande' => $id_commande,
                        'objet' => 'livraison_montant',
                        'descriptif' => _T('livraison_montant:titre_livraison_montant'),
                        'quantite' => 1,
                        'taxe'=>0
                        );
        
        
        //On regarde si on a une unité qui s'applique
        if(count($mesure)==0){
            $montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$row['id_livraison_zone']);  
            }//Sinon on vérifie si une tranche de mesure s'applique pour la mesure en question pour l'enregistrer
        else {
            $mesure=array_sum($mesure);
            
            if(!$montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$row['id_livraison_zone'].' AND mesure_min <='.$mesure.' AND mesure_max >='.$mesure)){
                //Sinon on divise le tout, et on enregistre par tranche
                mesure_par_tranche($mesure,$valeurs,$quantite,$row);
                return $flux;
                }
            }
		
		//Le prix unitaire         
        $prix_unitaire_ht=isset($montant['montant'])?$montant['montant']:lire_config('shop_livraison/montant_defaut');

            
        $valeurs['id_objet']=isset($montant['id_livraison_montant'])?$montant['id_livraison_montant']:0;
        $valeurs['prix_unitaire_ht']=$prix_unitaire_ht;    
        sql_insertq('spip_commandes_details', $valeurs);
    }

    return $flux;
}

//Établir les tranche de frais de livraison par rapport à la mesure maximale de la zone de livraison
function mesure_par_tranche($mesure,$valeurs,$quantite,$livraison_zone){
    //La mesure maximale de la zone de livraison 
    $mesure_max=sql_getfetsel('mesure_max','spip_livraison_montants','id_livraison_zone='.$livraison_zone['id_livraison_zone'],'','mesure_max DESC');
    //Si on trouve  un montant pour la mesure max, on enregistre une commande_detail
    if($montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$livraison_zone['id_livraison_zone'].' AND mesure_min <='.$mesure_max.' AND mesure_max >='.$mesure_max)){ 
        $valeurs['id_objet']=$montant['id_livraison_montant'];
        $valeurs['prix_unitaire_ht']= $montant['montant']; 
        $valeurs['descriptif'] =_T('livraison_montant:titre_livraison_montant').' '._T('livraison_montant:explication_tranche_frais_livraison',array('mesure_max'=>$mesure_max,'unite'=>$livraison_zone['unite']));
        sql_insertq('spip_commandes_details', $valeurs);
        $mesure_restante=$mesure-$mesure_max; 
        /*Puis on regarde si la mesure restante entre dans une tranche de mesures de la zone, sin oui on l'enregistre et on termine*/
        if($montant=sql_fetsel('montant,id_livraison_montant','spip_livraison_montants','id_livraison_zone='.$livraison_zone['id_livraison_zone'].' AND mesure_min <='.$mesure_restante.' AND mesure_max >='.$mesure_restante)){
            $total=$montant['montant'];    
            $valeurs['id_objet']=$montant['id_livraison_montant'];
            $valeurs['prix_unitaire_ht']= $montant['montant']; 
            $valeurs['descriptif'] =_T('livraison_montant:titre_livraison_montant'); 
            sql_insertq('spip_commandes_details', $valeurs);
            return;
            }
        //Sinon on relance le toute avec le montat restant
        elseif($mesure_restante>0)mesure_par_tranche($mesure_restante,$valeurs,$quantite,$livraison_zone);
    }
    
    
}

function shop_livraisons_formulaire_traiter($flux){
    
    // Installer des champs extras après la configuration prix
    if ($flux['args']['form'] == 'configurer_shop_livraison' ) {

    /*Installation de champs via le plugin champs extras*/
    include_spip('inc/cextras');
    include_spip('base/shop_livraisons');
    $maj_item = array();
    foreach(shop_livraisons_declarer_champs_extras() as $table=>$champs) {
        champs_extras_creer($table, $champs);
        } 
    }
 
    return $flux;
}

?>