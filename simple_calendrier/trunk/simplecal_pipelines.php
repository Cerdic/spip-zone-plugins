<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/simplecal_utils');



// Pipeline. Entete des pages de l'espace privé
function simplecal_header_prive($flux){
    $flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_SIMPLECAL_PRIVE.'simplecal_style_prive.css" />';
    return $flux;
}

// Pipeline. Entete des pages de l'espace public
function simplecal_insert_head($flux) {
    //  CSS DatePicker : voir dans 'prive/css/datepicker/' - thèmes basé sur : http://jqueryui.com/themeroller/
    $theme_public = $GLOBALS['meta']['simplecal_themepublic'];
    // ---
    $rc = "\n";
    $flux .= $rc.'<link rel="stylesheet" type="text/css" href="'._DIR_SIMPLECAL_PRIVE.'css/datepicker/'.$theme_public.'.css" />';
    // ---    
    
    return $flux;
}


// Pipeline : éléments 'en cours' de la page d'accueil
function simplecal_accueil_encours($flux) {
    
    $req_select = "e.*, a.id_auteur, a.nom";
    $req_from = "spip_evenements AS e, spip_auteurs_evenements as lien, spip_auteurs as a";
    $req_where = "e.id_evenement=lien.id_evenement AND lien.id_auteur = a.id_auteur";
    $req_orderby = "e.date_debut DESC, e.date_fin DESC";
      
    $req = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where." AND e.statut = 'prop'", "ORDER BY"=>$req_orderby);
        
    // Liste des evenements  'proposées à l'évaluation'
    $flux .= afficher_objets('evenement',_T('simplecal:liste_evenements_a_valider'), $req, '', false);

    return $flux;
}

// Pipeline : synthèse des éléments 'publiés' de la page d'accueil
function simplecal_accueil_informations($flux) {
    
    $q = sql_select("COUNT(*) AS cnt, statut", 'spip_evenements', '', 'statut', '','', "COUNT(*)<>0");
  
    $cpt = array();
    while($row = sql_fetch($q)) {
        $cpt[$row['statut']] = $row['cnt'];
    }
    if ($cpt) {
        $res .= afficher_plus(generer_url_ecrire("evenement_tous",""))."<b>"._T('simplecal:titre_evenements')."</b>";
        $res .= "<ul style='margin:0px; padding-left: 20px; margin-bottom: 5px;'>";
        if (isset($cpt['prepa'])) $res .= "<li>"._T("texte_statut_en_cours_redaction").": " . $cpt['prepa'] .'</li>';
        if (isset($cpt['prop'])) $res .= "<li>"._T("texte_statut_attente_validation").": " . $cpt['prop'] . '</li>';
        if (isset($cpt['publie'])) $res .= "<li><b>"._T("texte_statut_publies").": " . $cpt['publie'] ."</b>" . '</li>';
        $res .= "</ul>";
        
        // ---
        $flux .= '<div class="verdana1">';
        $flux .= $res;
        $flux .= '</div>';
    }
    
    return $flux;
}

// Zone de contenu
function simplecal_affiche_milieu($flux) {

    global $spip_lang_right;
    $exec =  $flux['args']['exec'];
    
    // On se trouve sur une rubrique
    if ($exec == 'naviguer') {
        $id_rubrique = intval($flux['args']['id_rubrique']);
    }
    
    // On se trouve sur la page d'accueil
    if ($exec == 'accueil'){
        // zone au dessous de la liste des rubriques
    }
    
    // On se trouve sur un article
    if ($exec == 'articles'){
        
    }
    
    // On se trouve sur la page d'un mot clé
    // => liste des evenements liés à ce mot clé.
    if ($exec == 'mots_edit'){
        $id_mot = $flux['args']['id_mot'];
        
        $req_select = "e.*, a.id_auteur, a.nom";
        $req_from = "spip_evenements AS e";
        $req_from .= " LEFT JOIN spip_mots_evenements AS lien_m ON lien_m.id_evenement=e.id_evenement";
        $req_from .= " LEFT JOIN spip_auteurs_evenements AS lien_a ON lien_a.id_evenement=e.id_evenement";
        $req_from .= " LEFT JOIN spip_auteurs as a ON lien_a.id_auteur = a.id_auteur";
        $req_where = "lien_m.id_mot=".$id_mot;
        $req_orderby = "e.date_debut DESC, e.date_fin DESC";
        $req = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where, "ORDER BY"=>$req_orderby);
         
        $flux['data'] .= afficher_objets('evenement','<b>' . _T('simplecal:info_evenements_liees_mot') . '</b>', $req);
   }
    
    // On se trouve sur un auteur dans la section 'intervention' 
    // (pipeline ajouté à la main dans auteur_infos.php (fichier customisé du core))
    if ($exec == 'auteur_infos_interventions'){
        $id_auteur = intval($flux['args']['id_auteur']);
        
        $req_select = "e.*, a.id_auteur, a.nom";
        $req_from = "spip_evenements AS e, spip_auteurs_evenements as lien, spip_auteurs as a";
        $req_where = "e.id_evenement=lien.id_evenement AND lien.id_auteur = a.id_auteur AND lien.id_auteur=".$id_auteur;
        $req_orderby = "e.date_debut DESC, e.date_fin DESC";
        
        $req = array("SELECT"=>$req_select, "FROM"=>$req_from, "WHERE"=>$req_where, "ORDER BY"=>$req_orderby);
        
        // Liste des évènements
        $flux['data'] .= afficher_objets('evenement',_T('simplecal:liste_evenements_auteur'), $req, '',false);
        $flux['data'] .= '<br class="nettoyeur" />';
    }
    
    // Page de configuration 
    if ($exec == 'config_fonctions'){
        $s='<br />';
        $s.=debut_cadre_trait_couleur(_DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", true, "", _T('simplecal:config_titre'));
        $s.=recuperer_fond('prive/configurer/simplecal', array());
        $s.=fin_cadre_trait_couleur(true);
        
        $flux['data'] .= $s;
    }
    
    return $flux;
}


function simplecal_configurer_liste_metas($metas) {
    $metas['simplecal_autorisation_redac'] = 'non'; // [oui, non]
    $metas['simplecal_rubrique'] = 'non'; // [non, secteur, partout]
    $metas['simplecal_refobj'] = 'non';   // [oui, non]
    $metas['simplecal_themepublic'] = 'base';
    return $metas;
}

function simplecal_affiche_gauche($flux) {
    $exec =  $flux['args']['exec'];
    
    // On se trouve sur une rubrique
    if ($exec == 'naviguer') {
        $config_rubrique = $GLOBALS['meta']['simplecal_rubrique'];
        // affichage du portlet si config = 'partout' ou 'secteur'
        if ($config_rubrique != 'non'){
            $id_rubrique = intval($flux['args']['id_rubrique']);
            // Pas à la racine
            if ($id_rubrique != 0){
                $affiche = true;
                // si config = 'secteur', on verifie que la rubrique est un secteur
                if ($config_rubrique == 'secteur'){
                    $row_tmp = sql_fetsel("id_parent", "spip_rubriques", "id_rubrique=$id_rubrique");
                    $id_parent = intval($row_tmp['id_parent']);
                    if ($id_parent == 0){
                        $affiche = true; // car secteur
                    } else {
                        $affiche = false; // car pas un secteur
                    }                
                }
                
                if ($affiche){
                    $bloc = simplecal_get_portlet_rubrique($id_rubrique);
                    $flux['data'] .= $bloc;
                }
            }
        }
    }
    
    // On se trouve sur un article
    if ($exec == 'articles') {
        if ($GLOBALS['meta']['simplecal_refobj'] == 'oui'){
            $id_article = intval($flux['args']['id_article']);
            $bloc = simplecal_get_portlet_ajout('article', $id_article);
            $flux['data'] .= $bloc;
        }
    }
    
    // On se trouve sur une brève
    if ($exec == 'breves_voir'){ 
        if ($GLOBALS['meta']['simplecal_refobj'] == 'oui'){
            $id_breve = intval($flux['args']['id_breve']);
            $bloc = simplecal_get_portlet_ajout('breve', $id_breve);
            $flux['data'] .= $bloc;
        }
    }

       
    return $flux;
}


// Liste des contributions d'un auteur (depuis spip 2.1.0)
// http://programmer.spip.org/compter_contributions_auteur
function simplecal_compter_contributions_auteur($flux){
    $id_auteur = intval($flux['args']['id_auteur']);
    $nb = sql_countsel("spip_auteurs_evenements as lien", "lien.id_auteur = ".$id_auteur);
    
    if ($nb == 1){
        $phrase = $nb." "._T('simplecal:terme_evenement');
    } else if ($nb > 1){
        $phrase = $nb." "._T('simplecal:terme_evenements');
    } else {
        $phrase = "";
    }
    
    if ($nb>0){
        $flux['data'][] = $phrase;
    }
    
    return $flux;
}

// Pour ajouter du contenu aux formulaires CVT du core.
function simplecal_editer_contenu_objet($flux){
    // Pour le formulaire CVT 'editer_groupe_mot', on fait apparaitre les nouveaux objets
    if ($flux['args']['type']=='groupe_mot') {
        $fond = recuperer_fond('formulaires/inc-groupe-mot-evenement', $flux['args']['contexte']);
        // que l'on insere ensuite a l'endroit approprie, a savoir avant le texte <!--choix_tables--> du formulaire
        $flux['data'] = preg_replace('%(<!--choix_tables-->)%is', $fond."\n".'$1', $flux['data']);
    }
    return $flux;
}


// Page listant tous les groupes de mots (exec/mots_tous),
// Pour l'affichage de '> Evenements'
function simplecal_libelle_association_mots($flux){
    $flux['evenements'] = 'simplecal:info_evenement_libelle';
    return $flux;
}

// Listing des nombres d'objet par mot clé (exec/mot_tous -> inc/grouper_mots)
function simplecal_afficher_nombre_objets_associes_a($flux){
    $objet = $flux['args']['objet'];
    $id_mot = $flux['args']['id_objet'];
    
    if ($objet == 'mot'){
        $nb = sql_countsel("spip_mots_evenements AS lien", "lien.id_mot=$id_mot");
        
        if ($nb == 1) {
            $texte_lie = _T('simplecal:info_1_evenement');
        } else if ($nb > 1) {
            $texte_lie = $nb."&nbsp;"._T('simplecal:info_n_evenements');
        }
    }
    
    if (isset($texte_lie)){
        $flux['data'][] = $texte_lie;
    }
    
    
    return $flux;
    
}

// Définir le squelette evenement.html pour les urls de type spip.php?evenement123
// http://programmer.spip.org/declarer_url_objets
function simplecal_declarer_url_objets($array){
    $array[] = 'evenement';
    return $array;
}


// cf. urls/propres.php
function simplecal_propres_creer_chaine_url($flux){
    /*
    $flux = Array ( 
        [data] => evenement2 
        [objet] => Array ( 
            [url] => evenement2 
            [date] => 2010-07-25 22:53:04 
            [date_debut] => 2010-05-09 00:00:00 
            [date_fin] => 2010-05-10 00:00:00 
            [lieu] => 
            {titre] =>
            [lang] => 
            [type] => evenement 
            [id_objet] => 2 ) 
    ) 
    */
    
    $type = $flux['objet']['type'];
    if ($type == 'evenement'){
        $date_debut = $flux['objet']['date_debut'];
        $titre = substr($date_debut, 8, 2)."-".substr($date_debut, 5, 2)."-".substr($date_debut, 0, 4);
        $titre = "evenement-du-".$titre;
    }
    
    $flux['objet']['data'] = $titre;
    return $flux;
}


// CRON : Appel de genie/simplecal_nettoyer_base.php
function simplecal_taches_generales_cron($taches_generales){
    $taches_generales['simplecal_nettoyer_base'] = 2*24*3600; // tous les 2 jours
    return $taches_generales;
}

// pipeline : permettre la recherche dans les évènements
function simplecal_rechercher_liste_des_champs($tables){
    // Prendre en compte certains champs
    $tables['evenements']['titre'] = 3;
    $tables['evenements']['lieu'] = 3;
    $tables['evenements']['descriptif'] = 3;
    $tables['evenements']['texte'] = 3;

    return $tables;
}


// pipeline : pour que le plugin spip-jqueryui rappatrie jquery-ui
function simplecal_jqueryui_forcer($scripts){
    $scripts[] = "jquery.ui.datepicker";
    return $scripts;
}


?>
