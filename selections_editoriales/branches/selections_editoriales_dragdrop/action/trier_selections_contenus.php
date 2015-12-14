<?php
/**
 * Plugin selections_editoriales
 * 
 * fichier action/trier_selections_contenus
 *
 * 
 */


if (!defined("_ECRIRE_INC_VERSION")) return;


function action_trier_selections_contenus_dist(){
	$id_selection = _request('id_selection');
        
        // ordre de tri des items renvoye par sortable [item3,item2,item1] 
        $nouveau_tri = explode(',',_request('sort'));
     
        include_spip('inc/filtres');
        // Parcours et met a jour le nouvel ordre
        // en préfixant le titre 10. Titre
        foreach($nouveau_tri as $cle => $valeur){
            preg_match_all("#selection(\d)-contenu(\d)#i", $valeur, $items);
            $id_contenu = $items[2][0];
    
            // recuperer le titre
            $contenu_titre = sql_getfetsel('titre', 'spip_selections_contenus', 'id_selection=' . intval($id_selection).' AND id_selections_contenu='.intval($id_contenu));
            // supprimer_numero
            $contenu_titre=supprimer_numero($contenu_titre);
            sql_updateq("spip_selections_contenus", array("titre" => $cle."0. $contenu_titre" ), "id_selection = '$id_selection' AND id_selections_contenu = '$id_contenu'");

        }

        if ($redirect = _request('redirect')) {
            include_spip('inc/headers');
            redirige_par_entete($redirect);
        }

	include_spip('inc/invalideur');
	suivre_invalideur("id='selections_contenu/$id_contenu'");
}




?>
