<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

//-------------------------------------------------------------------------
// Fonction generale d'affichage d'un évènement dans l'espace prive.
// Elle est appelée de la façon suivante :
// - ?exec=evenement_voir&id_evenement=17
//-------------------------------------------------------------------------

include_spip('inc/presentation');
include_spip('inc/actions');
include_spip('inc/simplecal_utils');


function exec_evenement_voir(){
    $id_evenement = intval(_request('id_evenement'));
    
    //--------------------------
    // 1/4 - Les autorisations
    //--------------------------
    // Autorisation de voir cette page ?
    if (!autoriser('voir', 'evenement', $id_evenement)) {
        // Message d'erreur
        include_spip('inc/minipres');
        echo minipres();
        exit;
    }
    
    
    
    
    // Droit de modification (admin ou proprio)
    $flag_editable = autoriser('modifier', "evenement", $id_evenement);
    
    
    //--------------------------
    // 2/4 - Le contexte
    //--------------------------
    
    // Récupération des infos de l'objet en base
    $row = sql_fetsel("*", "spip_evenements", "id_evenement=".$id_evenement);
    $titre  = $row['titre'];
    $date_debut = $row['date_debut'];
    $date_fin = $row['date_fin'];
    $statut = $row['statut'];
    $date = $row['date']; 
    $id_rubrique = $row['id_rubrique'];
    
    
    //------------------------------------
    // 3/4 - Preparation de l'affichage
    //------------------------------------
    
    // On initialise la page et les entetes
    pipeline('exec_init', array('args'=>array('exec'=>'evenement_voir','id_evenement'=>$id_evenement),'data'=>'')); 

    // Puis charge tout le debut du HTML, les entetes...
    $commencer_page = charger_fonction('commencer_page', 'inc'); 
    echo $commencer_page("&laquo; $titre &raquo;", "naviguer", "evenement_tous");	
    
    
    //---------------------------------
    // 3 - Le contenu
    //---------------------------------

    echo debut_grand_cadre(true);
    echo afficher_hierarchie($id_rubrique);
    echo fin_grand_cadre(true);
    
    // #####################
    // # Colonne de gauche #
    // #####################

    // On cree une colonne a gauche
    echo debut_gauche('', true);
    
    echo debut_boite_info(true);
    // recuperer le squelette dans  '/prive/infos' et le calculer avec les parametres indiques
    echo pipeline('boite_infos', array('data' => '', 'args' => array('type'=>'evenement', 'id_evenement'=>$id_evenement, 'row'=>$row)));
    echo fin_boite_info(true);

    // Logo
    // Note : la global provoque tout de même un petit pb d'affichage suite à l'upload/suppression (le titre disparait)
    // La solution consiste à ne pas utiliser la globale et customiser directement inc/iconifier.php
    // Mais bon, ça change d'une version de spip à l'autre... => le faire directement dans le core en prod...
    // Pour ne pas gérer de logo, mettre les 4 lignes en commentaire.
    global $logo_libelles;
    $logo_libelles['id_evenement'] = _T('simplecal:logo_evenement');
    $iconifier = charger_fonction('iconifier', 'inc');
    echo $iconifier('id_evenement', $id_evenement, 'evenement_voir', false, $flag_editable);
    // Fin Logo
    
    
    // Formulaire gestion des auteurs
    $contexte = array('id_evenement'=>$id_evenement);
    $fond_auteurs = recuperer_fond("prive/contenu/choisir_auteurs_simplecal", $contexte);
    echo debut_cadre_relief("redacteurs-24.gif", false, 'edit.gif', _T('simplecal:auteurs_titre'));
    echo $fond_auteurs;
    echo fin_cadre_relief();
    

    // ##################################
    // # Colonne de droite              #
    // # (à gauche en mode petit ecran) #
    // ##################################
    
    echo creer_colonne_droite('', true);
    
    // Raccourcis
    $raccourcis = "";
    
    // Lien vers l'objet lié (article/breve)
    $id_objet = intval($row['id_objet']);
    if ($id_objet != 0){
        $lien = simplecal_get_url_refobj($row['type'], $id_objet);
        $libelle = ucfirst($row['type'])." n°".$id_objet;
        $racc_objet = icone_horizontale($libelle, $lien, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
        $raccourcis .= $racc_objet;
    }
        
    // Lien vers la liste des évènements de la même rubrique
    if ($id_rubrique != 0){
        $lien = generer_url_ecrire("evenement_tous", "id_rubrique=$id_rubrique");
        $racc_meme_rubrique = icone_horizontale(_T('simplecal:raccourcis_tous_evenements_rubrique'), $lien, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
        $raccourcis .= $racc_meme_rubrique;
    }
    
    // Lien vers la liste de tous les évènements
    $lien = generer_url_ecrire("evenement_tous", "mode=avenir");
    $racc_tous = icone_horizontale(_T('simplecal:raccourcis_tous_evenements'), $lien, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
    $raccourcis .= $racc_tous;
    echo bloc_des_raccourcis($raccourcis);

    
    
    
    
    // ###################
    // # Contenu central #
    // ###################
    

    // Contexte pour le calcul du fond
    $contexte = array('id_evenement'=>$id_evenement);

    // On recupere et affiche le fond (transmission du contexte)
    $fond = recuperer_fond("prive/contenu/evenement",$contexte);
    $onglet_contenu = "<div id='wysiwyg'>".$fond."</div>";

    // --> "Onglet" des proprietes
    // Bloc de date de publication
    $dater = charger_fonction('dater', 'inc');
    
    // Bloc des mots clés
    $editer_mots = charger_fonction('editer_mots', 'inc');
    
    // Bloc des auteurs (non générique...)
    //$editer_auteurs = charger_fonction('editer_auteurs', 'inc');
    //$ids = "458,1";
    
    // "Onglet" des propriétés
    $onglet_proprietes = ''
        . ($dater?          $dater($id_evenement, $flag_editable, $statut, 'evenement', 'evenement_voir', $date): '')
        . ($editer_mots?    $editer_mots('evenement', $id_evenement, $cherche_mot, $select_groupe, $flag_editable, true, 'evenement_voir'): '')
        //. ($editer_auteurs? $editer_auteurs('evenement', $id_evenement, $flag_editable, true, $ids): '')
        ;

    // Todo : Il faudrait verifier que personne n'a ouvert l'objet en modification.
    // Pour l'instant, on considère que non !
    $modif = array();        
    

    // bouton de modification de l'objet
    $actions = $flag_editable?icone_inline(
        !$modif?_T('simplecal:icone_modifier_evenement'):"",
        generer_url_ecrire("evenements_edit","id_evenement=".$id_evenement),
        !$modif?_DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png":"warning-24.gif",
        !$modif?"edit.gif":'',
        $GLOBALS['spip_lang_right']
        ):"";	

    
    $titre_date = simplecal_affiche_dates($date_debut, $date_fin, true);
    $haut = "<div class='bandeau_actions'>".$actions."</div>";
    $haut .= _INTERFACE_ONGLETS;
    $haut .= gros_titre($titre_date, '' , false);
    $haut .= "<span class='arial1 spip_x-small'><b>".$titre."</b></span>\n";
    
    
    // Affichage
    echo debut_droite('', true);
    echo "<div class='fiche_objet'>";
    echo $haut;
    echo _INTERFACE_ONGLETS; // sous bloc des onglets
    echo afficher_onglets_pages(
        array('voir'=>_T('onglet_contenu'), 'props'=>_T('onglet_proprietes')), 
        array('props'=>$onglet_proprietes, 'voir'=>$onglet_contenu)
    );
    echo "</div>";
    

   
    
    // Fin du layout
    echo fin_gauche(), fin_page();
}

?>
