<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/simplecal_utils');



function exec_evenement_tous_dist(){
    global $spip_lang_right;
        
    $param_annee = _request("annee");
    $param_mois = _request("mois");
    $param_mode = _request("mode");
    $param_idrub = _request("id_rubrique");
    if (!empty($param_idrub)){
        $param_idrub = intval($param_idrub);
    } else {
        $param_idrub = 0;
    }

    // Autorisations de consultation ?
    $autorisation = autoriser('voir', 'evenement');
    // Autorisation pour creer dans la rubrique ? (Pour faire jouer le plugin Acces restreint)
    if ($autorisation && $param_idrub != 0){
        $autorisation = autoriser('voir', 'rubrique', $param_idrub);
    }
    
    if (!$autorisation) {
        include_spip('inc/minipres');
        echo minipres();
        exit;
    }
    
    
    // pipeline d'initialisation
    pipeline('exec_init', array('args'=>array('exec'=>'evenement_tous'),'data'=>'')); 	
    // entetes de la page
    $commencer_page = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T('simplecal:html_title'), "editer", "editer");			
    
    // ---
    
    echo debut_grand_cadre(true);
	echo afficher_hierarchie($param_idrub);
	echo fin_grand_cadre(true);
    
    // #####################
    // # Colonne de gauche #
    // #####################

    echo debut_gauche('', true);
    echo pipeline('affiche_gauche', array('args'=>array('exec'=>'evenement_tous'),'data'=>''));

    // Affichage du bloc d'information
    $boite = "<div class='logo-plugin'><img src='"._DIR_SIMPLECAL_IMG_PACK."simplecal-logo-96.png' alt='"._T('simplecal:alt_img_logo')."' /></div>";
    $boite .= "<p class='logo-plugin-desc'>"._T('simplecal:description_plugin')."</p>";
    echo debut_boite_info(true);
    echo $boite; 
    echo fin_boite_info(true);
    

    // Affichage du bloc des raccourcis
    if ($param_idrub != 0){
        $racc_rubrique = icone_horizontale(_T('simplecal:retour_rubrique'), generer_url_ecrire("naviguer","id_rubrique=$param_idrub"), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
        $racc_tous = icone_horizontale(_T('simplecal:raccourcis_tous_evenements'), generer_url_ecrire("evenement_tous", "mode=avenir"), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
        
        $raccourcis = "";
        $raccourcis .= $racc_rubrique;
        $raccourcis .= $racc_tous;
        echo bloc_des_raccourcis($raccourcis);
    }
    
    

    $sous_titre = "";
    $nom_mois = array(
        1 => ucfirst(_T('date_mois_1')), 
        2 => ucfirst(_T('date_mois_2')), 
        3 => ucfirst(_T('date_mois_3')), 
        4 => ucfirst(_T('date_mois_4')), 
        5 => ucfirst(_T('date_mois_5')), 
        6 => ucfirst(_T('date_mois_6')), 
        7 => ucfirst(_T('date_mois_7')), 
        8 => ucfirst(_T('date_mois_8')), 
        9 => ucfirst(_T('date_mois_9')), 
        10 => ucfirst(_T('date_mois_10')), 
        11 => ucfirst(_T('date_mois_11')), 
        12 => ucfirst(_T('date_mois_12'))
    );
    
    // Filtres
    $filtre = "";

    $liste_a = simplecal_get_liste_annees($param_idrub);
    if (count($liste_a)>0){
        $filtre .= '<ul id="simplecal-filtres">';
        
        // Restriction à la rubrique ?
        if ($param_idrub != 0){
            $param_rub = "id_rubrique=$param_idrub";
        } else {
            $param_rub = "";
        }
        
        // Lien Tous
        $filtre .= '<li>';
        $actif = (!$param_annee && !$param_mois && !$param_mode);
        if ($actif){
            $filtre .= '<span>'._T('simplecal:tous').'</span>';
        } else {
            $href_tous = generer_url_ecrire("evenement_tous", $param_rub);
            $filtre .= '<a href="'.$href_tous.'">'._T('simplecal:tous').'</a>';
        }
        $filtre .= '<small> ['.simplecal_get_nb_tous($param_idrub).']</small>';
        $filtre .= '</li>';
        
        // Lien A venir
        $filtre .= '<li class="marge-bas1">';
        $actif = (!$param_annee && !$param_mois && $param_mode);
        if ($actif){
            $filtre .= '<span>'._T('simplecal:a_venir').'</span>';
            $sous_titre = _T('simplecal:a_venir');
        } else {
            $tmp = "mode=avenir";
            if ($param_idrub != 0){
                $tmp .= "&".$param_rub;
            }
            $href_avenir = generer_url_ecrire("evenement_tous", $tmp);
            $filtre .= '<a href="'.$href_avenir.'">'._T('simplecal:a_venir').'</a>';
        }
        $filtre .= '<small> ['.simplecal_get_nb_avenir($param_idrub).']</small>';
        $filtre .= '</li>';
        
        // Pour chaque Annee
        foreach ($liste_a as $row){
            $annee = $row['annee'];
            $nb_a = $row['nb'];
            $actif = ($param_annee && $param_annee==$annee && !$param_mois);
            
            $filtre .= '<li>';
            if ($actif) {
                $filtre .= '<span>'.$annee.'</span>';
                $sous_titre = $annee;
            } else {
                $tmp = "annee=".$annee;
                if ($param_idrub != 0){
                    $tmp .= "&".$param_rub;
                }
                $href_a = generer_url_ecrire("evenement_tous",$tmp);
                $filtre .= '<a href="'.$href_a.'">'.$annee.'</a>';
            }
            $filtre .= '<small> ['.$nb_a.']</small>';
            
            //---
            $liste_m = simplecal_get_liste_mois($annee, $param_idrub);
            //---
            if (count($liste_m)>0){
                $filtre .= '<ul>';
                
                // Pour chaque Mois
                foreach ($liste_m as $row_m){
                    $mois = $row_m['mois'];
                    $nb_m = $row_m['nb'];
                    $actif = ($param_annee && $param_annee==$annee && $param_mois && $param_mois==$mois);
                    
                    $filtre .= '<li>';
                    if ($actif) {
                        $filtre .= '<span>'.$nom_mois[intval($mois)].'</span>';
                        $sous_titre = $nom_mois[intval($mois)]." ".$annee;
                    } else {
                        $tmp = "annee=".$annee."&mois=".$mois;
                        if ($param_idrub != 0){
                            $tmp .= "&".$param_rub;
                        }
                        $href_m = generer_url_ecrire("evenement_tous",$tmp);
                        $filtre .= '<a href="'.$href_m.'"'.$classe.'>'.$nom_mois[intval($mois)].'</a>';
                    }
                    $filtre .= '<small> ['.$nb_m.']</small>';
                    $filtre .= '</li>';
                }
                $filtre .= "</ul>";
            }
            //---            
            $filtre .= '</li>';
        }
        $filtre .= "</ul>";
    }
    
    
    
    if ($filtre){
        echo debut_cadre_forum('', true);
        echo '<strong>'.strtoupper(_T('simplecal:filtres')).' :</strong>';
        if ($param_idrub != 0){
            echo ' <small>('._T('simplecal:filtres_rubrique_concernee').')</small>';
        }
        echo $filtre;
        echo fin_cadre_forum(true);
    }
    
    // Lien vers la démo
    if (autoriser('demo', 'evenement')) {
        $lien = generer_url_ecrire("simplecal_demo", "var_mode=recalcul");
        $racc_demo = icone_horizontale(_T('simplecal:raccourcis_demo'), $lien, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
        echo debut_cadre_forum('', true);
        echo $racc_demo;
        echo fin_cadre_forum(true);
        
    }
    
    // #####################
    // # Contenu central   #
    // #####################

    
    echo debut_droite('', true);
    echo pipeline('affiche_milieu', array('args'=>array('exec'=>'evenement_tous'),'data'=>''));

    
    if ($param_idrub == 0){
        echo gros_titre(_T('simplecal:liste_des_evenements'), "", false);
    } else {
        echo gros_titre(_T('simplecal:liste_des_evenements_rubrique'), "", false);
    }
    if ($sous_titre) {
        echo '<strong>'.$sous_titre.'</strong><br />';
    }
    
    //----------------------------
    if ($param_annee && $param_mois) {
        $req_filtres = " AND (e.date_debut like '%".$param_annee."-".$param_mois."%'";
        $req_filtres .= " OR e.date_fin like '%".$param_annee."-".$param_mois."%')";
    } else if ($param_annee && !$param_mois) {
        $req_filtres = " AND (e.date_debut like '%".$param_annee."%'";
        $req_filtres .= " OR e.date_fin like '%".$param_annee."%')";
    } else if ($param_mode == 'avenir') {
        $req_filtres = " AND (e.date_debut >= DATE_FORMAT(NOW(),'%Y-%m-%d')";
        $req_filtres .= " OR e.date_fin >= DATE_FORMAT(NOW(),'%Y-%m-%d'))";
    } else {
        $req_filtres = "";
    }
    // ---
    if ($param_idrub != 0) {
        $req_filtres .= " AND e.id_rubrique = ".$param_idrub;
    }
    //----------------------------
    
       

    // Note : inc_afficher_objets_dist charge la fonction inc/afficher_|evenement|s
    // Note : dernier paramètre = pour que le bloc n'apparaisse pas si aucun item.
    
    $req_select = "e.*, a.id_auteur, a.nom, count(e.id_evenement) as nb_auteurs";
    $req_from = "spip_evenements AS e, spip_auteurs_evenements as lien, spip_auteurs as a";
    $req_where = "e.id_evenement=lien.id_evenement AND lien.id_auteur = a.id_auteur";
    $req_where .= $req_filtres;
    $req_groupby = "e.id_evenement";
    $req_orderby = "e.date_debut DESC, e.date_fin DESC";
    
    
    $req1 = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'prop'", "GROUP BY"=>$req_groupby, "ORDER BY"=>$req_orderby);
    $req2 = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'publie'", "GROUP BY"=>$req_groupby, "ORDER BY"=>$req_orderby);
    $req3 = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'prepa'", "GROUP BY"=>$req_groupby, "ORDER BY"=>$req_orderby);
    $req4 = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'refuse'", "GROUP BY"=>$req_groupby, "ORDER BY"=>$req_orderby);
    //$req5 = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'poubelle'", "GROUP BY"=>$req_groupby, "ORDER BY"=>$req_orderby);
             
    
    // Liste des evenement  'proposées à l'évaluation'
    echo afficher_objets('evenement',_T('simplecal:liste_evenements_prop'), $req1, '',false);
    
    // Liste des evenement 'publiées'
    echo afficher_objets('evenement',_T('simplecal:liste_evenements_publie'), $req2, '', false);
    
    // Liste des evenement 'en cours de rédaction'
    echo afficher_objets('evenement',_T('simplecal:liste_evenements_prepa'), $req3, '', false);
    
    // Liste des evenement 'supprimées'
    echo afficher_objets('evenement',_T('simplecal:liste_evenements_refuse'), $req4, '',false);
    
    // Liste des evenement 'à la poubelle'
    //echo afficher_objets('evenement',_T('simplecal:liste_evenements_poubelle'), $req5, '',false);
    // --------------

    
    if (autoriser('creer', 'evenement', null)){
        $param_creation='new=oui&retour=liste';
        if ($param_idrub != 0){
            $param_creation.="&id_rubrique=$param_idrub";
        }
        echo icone_inline(_T('simplecal:raccourcis_ecrire_evenement'), generer_url_ecrire("evenements_edit", $param_creation), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "creer.gif", $spip_lang_right);
    }
    
    
    echo fin_gauche(), fin_page();
}


function simplecal_get_liste_annees($id_rubrique){
    /*
    select DATE_FORMAT(date_debut,'%Y') as annee
    from spip_evenements 
    where date_debut not like '%0000%'
    union
    select DATE_FORMAT(date_fin,'%Y') as annee
    from spip_evenements 
    where date_fin not like '%0000%'
    */

    $from = "spip_evenements";
    $order_by = "annee desc";

    $select1 = "distinct DATE_FORMAT(date_debut,'%Y') as annee";
    $select2 = "distinct DATE_FORMAT(date_fin,'%Y') as annee";
    $where1 = "date_debut not like '%0000%'";
    $where2 = "date_fin not like '%0000%'";
    
    if ($id_rubrique!=0){
        $where_rub = " and id_rubrique = ".$id_rubrique;
    } else {
        $where_rub = "";
    }
    
    // ----------------------
    //  Acces restreint ?
    // ----------------------
    $where_rub_exclure = simplecal_get_where_rubrique_exclure();
    // ----------------------
        
    $liste_a1 = sql_allfetsel($select1, $from, $where1.$where_rub.$where_rub_exclure, "", $order_by, "");
    $liste_a2 = sql_allfetsel($select2, $from, $where2.$where_rub.$where_rub_exclure, "", $order_by, "");

    $annees = array();
    
    foreach ($liste_a1 as $row){
        $a = $row['annee'];
        if (!in_array($a, $annees)){
            $annees[] = $a;
        }
    }
    
    foreach ($liste_a2 as $row){
        $a = $row['annee'];
        if (!in_array($a, $annees)){
            $annees[] = $a;
        }
    }
    
    rsort($annees);
    
    $tab = array();
    foreach ($annees as $annee){
        $where = "(date_debut like '%".$annee."%' OR date_fin like '%".$annee."%')";
        $nb = sql_countsel($from, $where.$where_rub.$where_rub_exclure);
        $tab[] = array("annee"=>$annee, "nb"=>$nb);
    }
    
    return $tab;
}




function simplecal_get_liste_mois($annee, $id_rubrique){
    $from = "spip_evenements";
    $order_by = "mois desc";
    
    $select1 = "distinct DATE_FORMAT(date_debut,'%m') as mois";
    $select2 = "distinct DATE_FORMAT(date_fin,'%m') as mois";
    $where1 = "date_debut like '%".$annee."%'";
    $where2 = "date_fin like '%".$annee."%'";
    if ($id_rubrique!=0){
        $where_rub = " and id_rubrique = ".$id_rubrique;
    } else {
        $where_rub = "";
    }
    
    // ----------------------
    //  Acces restreint ?
    // ----------------------
    $where_rub_exclure = simplecal_get_where_rubrique_exclure();
    // ----------------------
    
    $liste_m1 = sql_allfetsel($select1, $from, $where1.$where_rub.$where_rub_exclure, "", $order_by, "");
    $liste_m2 = sql_allfetsel($select2, $from, $where2.$where_rub.$where_rub_exclure, "", $order_by, "");

    
    $tab_mois = array();
    
    foreach ($liste_m1 as $row){
        $m = $row['mois'];
        if (!in_array($m, $tab_mois)){
            $tab_mois[] = $m;
        }
    }
    
    foreach ($liste_m2 as $row){
        $m = $row['mois'];
        if (!in_array($m, $tab_mois)){
            $tab_mois[] = $m;
        }
    }
    
    rsort($tab_mois);
    
    $tab = array();
    foreach ($tab_mois as $mois){
        $where = "(date_debut like '%".$annee."-".$mois."%' OR date_fin like '%".$annee."-".$mois."%')";
        $nb = sql_countsel($from, $where.$where_rub.$where_rub_exclure);
        $tab[] = array("mois"=>$mois, "nb"=>$nb);
    }
    
    return $tab;
    
}

function simplecal_get_nb_tous($id_rubrique){
    $from = "spip_evenements as e";
    
    if ($id_rubrique != 0){
        $where = "id_rubrique=$id_rubrique";
    } else {
        $where = "";
    }
    
    // ----------------------
    //  Acces restreint ?
    // ----------------------
    $where_rub_exclure = simplecal_get_where_rubrique_exclure(!empty($where));
    // ----------------------
    
    $nb = sql_countsel($from, $where.$where_rub_exclure);
    
    return $nb;
}

function simplecal_get_nb_avenir($id_rubrique){
    $from = "spip_evenements as e";
    $where = " (e.date_debut >= DATE_FORMAT(NOW(),'%Y-%m-%d')";
    $where .= " OR e.date_fin >= DATE_FORMAT(NOW(),'%Y-%m-%d'))";
    
    if ($id_rubrique != 0){
        $where .= " AND id_rubrique=$id_rubrique";
    } 
    
    // ----------------------
    //  Acces restreint ?
    // ----------------------
    $where_rub_exclure = simplecal_get_where_rubrique_exclure();
    // ----------------------
    
    $nb = sql_countsel($from, $where.$where_rub_exclure);
    
    return $nb;
}



?>
