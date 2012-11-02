<?php


/* Fonction qui traduit les champs */
function info_objet($id_objet,$objet,$champ='*'){
	include_spip('inc/filtres');
    
    $where=array(
            'id_'.$objet.'='.$id_objet,  
            );  
        

	if($champ=='*')$data=sql_fetsel($champ,'spip_'.$objet.'s',$where);
    else $data=sql_getfetsel($champ,'spip_'.$objet.'s',$where);
	
    //Appliquer des filtres sur des champs spéciciques
    $filtres=array(
        'supprimer_numero'=>array(
            'titre','nom'
            ),
        'typo'=>array(
            'titre','nom'
            ),
       );
        
    foreach($filtres as $filtre => $champ){
        if(is_array($data) AND $data[$champ]){
            if(is_array($champ)){
                foreach($champ as $c){
                    $data[$champ]=$filtre($data[$c]);
                    }
                }
            }
         
        else $data=$filtre($data);
        }
	return $data;

}

/* Fonction qui fournit le lien */
function url_objet($id_objet,$objet){

	$title=info_objet($id_objet,$objet,'titre');

	$string_objet=substr($objet,0,strlen($objet)-1);

	$url=generer_url_entite($id_objet,$string_objet);
	
	$lien = '<a href="'.$url.'" title="'.$title.'">'.$title.'</a>';
	return $lien;
}

function generer_modele($id_objet,$objet='article',$fichier='modeles_selection_objet/defaut',$env=array(),$where=''){
    include_spip('inc/utils');
    
    if(!$where)$where='id_'.$objet.'='.$id_objet;
    
    $contexte=sql_fetsel('*','spip_'.$objet.'s',$where);
    $cont=calculer_contexte();
    if(is_array($env))$contexte= array_merge($contexte,$env,$cont);
    
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
?>
