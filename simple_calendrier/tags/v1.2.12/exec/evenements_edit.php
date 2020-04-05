<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

//-------------------------------------------------------------------------
// Fonction generale d'édition des évènements.
// Elle est appelée de plusieurs façons :
//
// - creation depuis la liste complete (evenement_tous)
//   => ?exec=evenements_edit&new=oui&retour=liste
//
// - creation depuis la liste complete restreinte à une rubrique
//   => ?exec=evenements_edit&new=oui&retour=liste&id_rubrique=17
//
// - création depuis une rubrique
//   => ?exec=evenements_edit&new=oui&retour=rubrique&id_rubrique=17
//
// - création depuis un objet (article/breve)
//   => ?exec=evenements_edit&new=oui&retour=objet&refobj=breve12
//
// - modification depuis evenement_voir
//   => ?exec=evenements_edit&id_evenement=17
//-------------------------------------------------------------------------
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/barre');
include_spip('inc/simplecal_utils');
include_spip('inc/documents'); // gestion de l'ajout d'images/documents

function exec_evenements_edit() {
    
	// parametres de la requetes
	$param_new    = _request('new');
    $param_idevt  = _request('id_evenement');
    $param_retour = _request('retour');
    $param_idrub  = _request('id_rubrique');
    $param_refobj = _request('refobj');
    
    // Mode création
	if ($param_new == "oui") {
		// Autorisation pour creer ?
        $autorisation = autoriser('creer', 'evenement', null);

        // Autorisation pour creer dans la rubrique ? (Pour faire jouer le plugin Acces restreint)
        if ($autorisation && $param_idrub){
            $autorisation = autoriser('voir', 'rubrique', $param_idrub);
        }
        
		if ($autorisation) {
			evenements_edit_ok(null, $param_new, $param_retour, $param_idrub, $param_refobj);
		} 
		else{
			include_spip('inc/minipres');
			echo minipres();
		}		
	}
    
	// Mode modification
	else {
		// Autorisation pour modifier ?
		$autorisation = autoriser('modifier', 'evenement', $param_idevt);

        // Autorisation pour voir ? (Pour faire jouer le plugin Acces restreint)
        if ($autorisation){
            $autorisation = autoriser('voir', 'evenement', $param_idevt);
        }

		if ($autorisation) {
			evenements_edit_ok($param_idevt, $param_new, $param_retour, $param_idrub, $param_refobj);
		} else {
			// message d'erreur
			include_spip('inc/minipres');
			echo minipres();
		}
	}
}



// EDITION DE L'OBJET
function evenements_edit_ok($param_idevt, $param_new, $param_retour, $param_idrub, $param_refobj) {
	global $connect_statut, $spip_lang_right;

	
    //------------------
	// 1 - Le contexte
	//------------------
		
	// Mode création
	if ($param_new == 'oui') {
        $row = array();
        // ---
        $titre_evenement = _T('simplecal:titre_nouvel_evenement');
        $texte = "";
        // ---
        
        // on initialise l'objet avec type et id_objet
        if (simplecal_is_ref_ok($param_refobj)){
            $tab = simplecal_get_tuple_from_ref($param_refobj);
            $row['type'] = $tab['type'];
            $row['id_objet'] = $tab['id_objet'];
        }
        
        // Création dans une rubrique ?
        if (empty($param_idrub)){
            $row['id_rubrique'] = 0;
        } else {
            $row['id_rubrique'] = $param_idrub;
        }
        
        // ---------------
        // Url de retour
        // ---------------
        if ($param_retour == "liste"){
            // Retour à la liste complete des évènements
            if (empty($param_idrub)){
                $redirection = generer_url_ecrire("evenement_tous", "mode=avenir");
            } 
            // Retour à la liste des évènements d'une rubrique
            else {
                $redirection = generer_url_ecrire("evenement_tous", "id_rubrique=$param_idrub");
            }            
        } 
        // Retour à une rubrique
        else if ($param_retour == "rubrique"){
            $redirection = generer_url_ecrire("naviguer", "id_rubrique=$param_idrub");
        } 
        // Retour à un objet
        else if ($param_retour == "objet"){
            $redirection = simplecal_get_url_for_ref($param_refobj);
        } 
        // Nouveau cas, non prévu par le developpeur !
        else {
            $redirection = generer_url_ecrire("evenement_tous", "mode=avenir");
        }
	}
    
	// Mode modification
	else {
        $req_select = "e.*";
        $req_from = "spip_evenements as e";
        $req_where = "e.id_evenement=".$param_idevt;
        $row = sql_fetsel($req_select, $req_from, $req_where);
        // ---
        $titre_evenement = $row['titre'];
        $texte = $row['texte'];
        // ---
        $redirection = generer_url_ecrire("evenement_voir","id_evenement=".$param_idevt);
	} 

    
    
	//---------------------------------
	// 2 - Preparation de l'affichage
	//---------------------------------
	
	// On initialise la page et les entetes
	pipeline('exec_init',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$param_idevt),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('simplecal:titre_page_evenements_edit', array('titre' => $titre_evenement)), "naviguer", 'evenements');

	
	//---------------------------------
	// 3 - Le contenu
	//---------------------------------

    echo debut_grand_cadre(true);
	echo afficher_hierarchie($row['id_rubrique']);
	echo fin_grand_cadre(true);
    
	// # Colonne de gauche #
	// #####################
	
	// Colonne gauche 
	echo debut_gauche('', true);
    
    // Affichage du bloc d'information
    $boite = "<div class='logo-plugin'><img src='"._DIR_SIMPLECAL_IMG_PACK."simplecal-logo-96.png' alt='"._T('simplecal:alt_img_logo')."' /></div>";
    $boite .= "<p class='logo-plugin-desc'>"._T('simplecal:description_plugin')."</p>";
    echo debut_boite_info(true);
    echo $boite; 
    echo fin_boite_info(true);
    // ---
    
    // Affichage du portlet d'ajout d'images et de documents.
    // sur le modèle de breves_edit.php (évite d'avoir à gérer un 'GROS HACK' comme dans edit_article.php)
    if ($param_new != 'oui') {
	    traiter_modeles("$titre_evenement$texte", true);
		echo afficher_documents_colonne($param_idevt, "evenement");
	}
    // mode creation : on affiche un petit message sympa
    // (pas de 'gros hack' mais on prend soin du redacteur...)
    else if ($param_new == 'oui') {
        $bloc = "";
        $bloc .= debut_cadre_relief("doc-24.gif", $return=true, $fonction='', $titre=_T('bouton_ajouter_image_document').aide("ins_doc"));
        $bloc .= _T('simplecal:enregistrer_dabord_une_fois');
        $bloc .= fin_cadre_relief(true);
        echo $bloc;
    }
	
    // Pour pouvoir afficher des choses ici grace au pipeline affiche_gauche.
    echo pipeline('affiche_gauche',array('args'=>array('exec'=>'evenements_edit','id_evenement'=>$param_idevt),'data'=>''));
    
    
	
	// # Contenu central #
	// ###################
	
	// On cree une colonne centrale
	echo debut_droite('', true);


    $config_reference = $GLOBALS['meta']['simplecal_refobj'];
    $config_rubrique = $GLOBALS['meta']['simplecal_rubrique'];
    $config_descriptif = $GLOBALS['meta']['simplecal_descriptif'];
    $config_texte = $GLOBALS['meta']['simplecal_texte'];
    $config_lieu = $GLOBALS['meta']['simplecal_lieu'];
    $config_lien = $GLOBALS['meta']['simplecal_lien'];
    
    $config = array(
        "simplecal_rubrique" => $config_rubrique,
        "simplecal_reference" => $config_reference,
        "simplecal_descriptif" => $config_descriptif,
        "simplecal_texte" => $config_texte,
        "simplecal_lieu" => $config_lieu,
        "simplecal_lien" => $config_lien
    );
    
	// Contexte qui sera transmis au formulaire
	$contexte = array(
		'icone_retour'=>icone_inline(_T('icone_retour'), $redirection, _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "rien.gif",$GLOBALS['spip_lang_left']),
		'redirect'=>$redirection,
		'titre'=>$titre_evenement,
        'id_evenement'=>$param_idevt,
        'row'=>$row,
        'new'=>$param_new,
        'config'=>$config
	);

    // On recupere et affiche le fond (transmission du contexte)
	echo recuperer_fond("prive/editer/evenement", $contexte);


	// Fin du layout
	echo fin_gauche(), fin_page();

}

?>
