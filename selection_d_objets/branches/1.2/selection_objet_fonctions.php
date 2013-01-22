<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

 //Applique des filtres sur des champs spéciciques
function filtrer_champ($data){
    include_spip('inc/texte');
    $exceptions=charger_fonction('exceptions','inc');
    $titres=$exceptions('titre');
    $titres=array_merge(array(0=>'titre'),$titres);
    $propres=array('descriptif','texte');
    $extraire_multi=array_merge($titres,array('descriptif','texte'));
    $filtres=array(
        'extraire_multi'=>  $extraire_multi,          
        'supprimer_numero'=>$titres,
        'propre'=>$propres,
       );
        
    foreach($filtres as $filtre => $champ){
        if(is_array($data)){
            if(is_array($champ)){
                foreach($champ as $c){
                    if($data[$c])$data[$c]=$filtre($data[$c]);
                    }
                }
            }
        else $data=$filtre($data);
        }
    return $data;   
    
}

/* Fournit les champs désirés d'un objet donné */
function info_objet($objet,$id_objet='',$champs='*',$where=array()){
	include_spip('inc/filtres');

    //Les tables non conforme
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions('objet');
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
          $table='spip_'.$objet;
    }
    else $table='spip_'.$objet.'s';

    
    if($id_objet){
        if(!$where)$where=array('id_'.$objet.'='.$id_objet);  
    	if($champs=='*')$data=sql_fetsel($champs,$table,$where);
        else $data=sql_getfetsel($champs,$table,$where);
        $data=filtrer_champ($data);
        }
    else{
        $data=array();
        $sql=sql_select($champs,$table,$where);
        while($d = sql_fetch($sql)){
            
            if($d)$data[$d['id_'.$objet]]=filtrer_champ($d);
            }
        }
    
	return $data;
    
}

/* Fonction qui fournit le lien d'un objet*/
function url_objet($id_objet,$objet,$titre='',$url=''){
    
    if(!$titre AND !$url){
        $objet_sel=sql_fetsel('titre,url','spip_selection_objets','id_objet='.$id_objet.' AND objet='.sql_quote($objet));
        $url=$objet_sel['url'];
        $titre=$objet_sel['titre'];
    }

	if(!$titre)$titre=info_objet($objet,$id_objet,'titre');
    if(!$url)$url=generer_url_entite($id_objet,$objet);
	
	$lien = '<a href="'.$url.'" title="'.$titre.'">'.$titre.'</a>';
	return $lien;
}


/*Fournit un tableau avec id_objet=>donnees_objet*/

function tableau_objet($objet,$id_objet='',$champs='*',$where=array(),$filtrer=array(),$array_donnes=true){
    $d=info_objet($objet,$id_objet,$champs,$where);
    //Les tables non conforme, faudrait inclure une pipeline
    $exceptions=charger_fonction('exceptions','inc');
    $exception_objet=$exceptions('objet');
    if($exception_objet[$objet]){
         $objet=$exception_objet[$objet];
    }
    $data=array();
    if(is_array($d)){
        foreach($d as $r){
            if(!$r['titre']){
                $r['titre']=$r['nom']?$r['nom']:($r['nom_site']?$r['nom_site']:'objet'.$r['id_'.$objet]);
                unset($r['nom']);
                unset($r['nom_site']);
            }
            if(!$filtrer) $data[$r['id_'.$objet]]=$r;
            elseif(is_array($filtrer)){
                $donnees=array();
                foreach($filtrer as $c){
                if($r[$c])$donnees[$c]=$r[$c];  
                }
             if($array_donnes) $data[$r['id_'.$objet]]=$donnees; 
             else $data[$r['id_'.$objet]]=implode(',',$donnees);
            }
        }
    }
    return $data;
}
/* Assemble les données entre un objet sélectioné et son objet d'origine pour injection dans un modele choisit*/
function generer_modele($id_objet,$objet='article',$fichier='modeles_selection_objet/defaut',$env=array(),$where=''){
    include_spip('inc/utils');
    
    if(!$where)$where='id_'.$objet.'='.$id_objet;
    
    if(!$contexte=sql_fetsel('*','spip_'.$objet.'s',$where))$contexte=array();

    if(is_array($env))$contexte= array_merge($contexte,$env);

    $contexte['objet']=$objet;
    $contexte['id_objet']=$id_objet; 
    if($contexte['nom'])$contexte['titre']=$contexte['nom'];
    $rest = substr($objet, 0,3);
    $extensions=array('png','jpg','gif');
    foreach($extensions as $extension){
        if($contexte['logo_objet']=find_in_path('IMG/'.$rest.'on'.$id_objet.'.'.$extension))break;
    }

    $fond=recuperer_fond($fichier,$contexte);
    
    return $fond;
}

//donnele nom du type de lien
function nom_type($type,$objet){
    include_spip('inc/config');
    if(!$types=lire_config('selection_objet/type_liens_'.$objet_dest_original,array())) $types=lire_config('selection_objet/type_liens',array());
    
    $nom=_T($types[$type]);
    
    return $nom;
    }
?>
