<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/*Fournit un tableau avec id_objet=>donnees_objet*/

function tableau_recherche_objet($objet,$id_objet='',$champs='*'){
    //Les tables non conforme, faudrait inclure une pipeline
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions();
    if(!$champ_titre=$exception_objet['titre'][$objet]) $champ_titre='titre';
    
        
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
         $table_dest='spip_'.$objet;
    }
    else $table_dest='spip_'.$objet.'s';
    
    $tables=lister_tables_objets_sql();


    $where_titre=$champ_titre.' LIKE '.sql_quote('%'._request('term').'%');
    
  if(isset($tables[$table_dest]['statut'][0]['publie']))$statut=$tables[$table_dest]['statut'][0]['publie'];

   if($statut AND $objet !='rubrique')  $where_statut=' AND statut='.sql_quote($statut);
   if($objet=='auteur') $where_statut=' AND statut !='.sql_quote('5poubelle');
    
    $d=info_objet($objet,'',$champ_titre.',id_'.$objet,$where_titre.$where_statut);
    
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
    }
    $data=array();
    if(is_array($d)){
        foreach($d as $r){
            if(!isset($r['titre'])){
                $r['titre']=$r['nom']?$r['nom']:($r['nom_site']?$r['nom_site']:'objet'.$r['id_'.$objet]);
                if($r['nom'])unset($r['nom']);
                if($r['nom_site'])unset($r['nom_site']);
            }
            $data[]=array('label'=>$r[titre].' ('.$objet.')','value'=>$r['id_'.$objet].'-'.$objet);
        }
    }
    return $data;
}

?>
