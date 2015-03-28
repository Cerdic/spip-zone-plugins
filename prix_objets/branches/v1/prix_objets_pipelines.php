<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function prix_objets_affiche_milieu($flux){
	// affichage du formulaire d'activation désactivation projets
		
	include_spip('inc/config');
    $objets=lire_config('prix_objets/objets_prix',array());
    $e = trouver_objet_exec($flux['args']['exec']);
    $type = $e['type'];
    $id_table_objet = $e['id_table_objet'];   
    $id = intval($flux['args'][$id_table_objet]);
    if(in_array($type,$objets)){  
        if ($type=='article') {
        $id_article = $flux['args']['id_article'];
	   $rubriques_produits=rubrique_prix($id_article);
		if(($rubriques_produits AND $id_article) OR (!$rubriques_produits)){
			$contexte = array('id_objet'=>$id_article,'objet'=>'article');
			$contenu .= recuperer_fond('prive/objets/editer/prix', $contexte,array('ajax'=>true));
            if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
             $flux['data'] = substr_replace($flux['data'],$contenu,$p,0);
            else
                $flux['data'] .= $contenu;
			    }
	        } 
        elseif($id){
            $contexte = array('id_objet'=>$id,'objet'=>$type);
            $contenu .= recuperer_fond('prive/objets/editer/prix', $contexte,array('ajax'=>true));
            if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
             $flux['data'] = substr_replace($flux['data'],$contenu,$p,0);
            else
                $flux['data'] .= $contenu;
                }
            }
    return $flux;
}

// declare l'object pour le Plugin shop https://github.com/abelass/shop
function prix_objets_shop_objets($flux){
	
	$flux['data']['prix_objets']=array(
	            'action'=>'prix_objets',
	            'nom_action'=>_T('prix_objets:prix_objets_titre'),
	            'icone'=>'prix_objets-16.png',
	            'configurer'=>array(
	                'titre'=>_T('prix_objets:titre_prix_objets'),
	                'chemin'=>'prive/squelettes/contenu/configurer_prix_objets')
            );

    return $flux;
}
?>