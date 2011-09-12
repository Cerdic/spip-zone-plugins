<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// On trouve ici la fonction 'afficher_objets' appelees par 'evenement_tous'
// permet de lister tous les objets, avec certaines colonnes



include_spip('inc/simplecal_utils');

$GLOBALS['my_sites']=array();


//function inc_afficher_evenements($titre, $requete, $formater) {
//    // Appelé si elle existe. Sinon, celle du core est utilisée
//    // cf. inc/afficher_objets.php : inc_afficher_objets_dist
//}

// On la définit uniquement dans le but de pouvoir changer le chemin de l'icone !!
function inc_afficher_evenements($titre, $requete, $formater='') {
    // ---- code ajouté ----
    $type = "evenement";
    $force = false;
    $icone = _DIR_SIMPLECAL_IMG_PACK.'simplecal-24.png';
    
    
    // ------------------------------------------------------
    // Le reste ci-dessous est conforme au code dans
    // inc/afficher_objets.php : inc_afficher_objets_dist
    // excepté le $icone...
    //-------------------------------------------------------
    if (($GLOBALS['meta']['multi_rubriques'] == 'oui'
	     AND (!isset($GLOBALS['id_rubrique'])))
	OR $GLOBALS['meta']['multi_articles'] == 'oui') {
		$afficher_langue = true;

		if (isset($GLOBALS['langue_rubrique'])) $langue_defaut = $GLOBALS['langue_rubrique'];
		else $langue_defaut = $GLOBALS['meta']['langue_site'];
	} else $afficher_langue = $langue_defaut = '';

	$arg = array($afficher_langue, false, $langue_defaut, $formater, $type,id_table_objet($type));
	if (!function_exists($skel = "afficher_{$type}s_boucle")){
		$skel = "afficher_objet_boucle";
	}

	$presenter_liste = charger_fonction('presenter_liste', 'inc');
	$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
	$styles = array(array('arial11', 7), array('arial11'), array('arial1'), array('arial1'), array('arial1 centered', 100), array('arial1', 38));

	$tableau = array(); // ne sert pas ici
	return $presenter_liste($requete, $skel, $tableau, $arg, $force, $styles, $tmp_var, $titre, $icone);
}



// affichage de la puce
function evenement_puce_statut($statut) {
    global $lang_objet;
    
    $lang_dir = lang_dir($lang_objet);
    
    $puces = array(
        0 => 'puce-orange-breve.gif',
        1 => 'puce-verte-breve.gif',
        2 => 'puce-rouge-breve.gif',
        3 => 'puce-blanche-breve.gif',
        4 => 'puce-poubelle-breve.gif'
    );

    switch ($statut) {
        case 'prop':
            $puce = $puces[0];
            $title = _T('simplecal:titre_evenement_propose');
            break;
        case 'publie':
            $puce = $puces[1];
            $title = _T('simplecal:titre_evenement_publie');
            break;
        case 'refuse':
            $puce = $puces[2];
            $title = _T('simplecal:titre_evenement_refuse');
            break;
        case 'prepa':
            $puce = $puces[3];
            $title = _T('simplecal:titre_evenement_preparation');
            break;
        case 'poubelle':
            $puce = $puces[4];
            $title = _T('simplecal:titre_evenement_poubelle');
            break;
        default:
            $puce = $puces[3];
            $title = '';
    }

    $inser_puce = http_img_pack($puce, $title, "style='margin: 1px;'");

    
    return 	"<span dir='$lang_dir'>".$inser_puce."</span>";

}

//
// Fonction principale d'affichage
//

// Est executee en boucle autant de fois que d'objets listes
// Indispensable pour l'affichage de l'ID et de la puce dans cette liste.
// En cas de dysfonctionnement la fonction generique 'afficher_objet_boucle'
// du core prends le relais, de façon simplifiee toutefois.
//
//cf. dans inc/afficher_objets : 
//if (!function_exists($skel = "afficher_{$type}s_boucle"))
//    $skel = "afficher_objet_boucle";



function afficher_evenements_boucle($row, $own) {
    global $connect_statut, $spip_lang_right;
    static $chercher_logo = true;
    
    list($afficher_langue, $affrub, $langue_defaut, $formater, $type, $primary) = $own;
    $vals = array();
    
    // On force $primary. Semble poser probleme sinon : elle n'est pas definie.
    $primary = "id_evenement";	
    
    $id_evenement = $row[$primary]; 		
    if (autoriser('voir', 'evenement', $id_evenement)){
        
         // Statut
        $statut = isset($row['statut'])?$row['statut']:"";
        
        // Puce du statut (fonction definie au-dessus)
        $puce_statut = evenement_puce_statut($statut);
        
        // Dates de l'évènement
        $les_dates = simplecal_affiche_dates($row['date_debut'], $row['date_fin']);
        // Lien vers évènement
        $lien_evenement = "<a href='".generer_url_ecrire('evenement_voir',"id_evenement=$id_evenement")."'>$les_dates</a>";
        //Titre
        $titre = $row['titre'];
        
        // Lien vers objet spip
        $id_objet = isset($row['id_objet'])?$row['id_objet']:'';
        $type_obj = isset($row['type'])?$row['type']:'';
        // Lien objet Spip
        if ($id_objet){
            $lien_objet = "<br />&raquo; <a href='".simplecal_get_url_for_obj($type_obj, $id_objet)."'>$type_obj n&deg;$id_objet</a>";
        } else {
            $lien_objet = "";
        }
        
        // Auteur
        if ($row['nb_auteurs']>1){
            $nom_auteur = _T('simplecal:multiples_auteurs');
            $lien_auteur = $nom_auteur;
            $id_auteur = '';
        } else {
            $nom_auteur = isset($row['nom'])?$row['nom']:'';
            $nom_auteur .= (isset($row['nb_auteurs']) && $row['nb_auteurs']>1)?' ('.$row['nb_auteurs'].')':'';
            $id_auteur = isset($row['id_auteur'])?$row['id_auteur']:'';
            // Lien Auteur
            if ($id_auteur){
                $lien_auteur = "<a href='".generer_url_ecrire('auteur_infos',"id_auteur=$id_auteur")."'>$nom_auteur</a>";
            } else {
                $lien_auteur = _T('simplecal:inconnu');;
            }
        }
        
        
        // Lien Modifier
        $lien_modifier = "<a href='".generer_url_ecrire('evenements_edit',"id_evenement=$id_evenement")."'>modifier</a>";
        
        
        // Date de publication
        $date_publi = isset($row['date'])?$row['date']:(isset($row['date_heure'])?$row['date_heure']:"");
        if ($statut) {
            if ($statut == "publie") {
                $date_publi = simplecal_affiche_dates($date_publi);
            } else if ($statut == "prepa") {
                $date_publi = _T('simplecal:info_en_cours');
            } else if ($statut == "prop") {
                $date_publi = _T('simplecal:info_a_valider');
            } else {
                $date_publi = "/";
            }
        } else {
            $date_publi = "?";
        }
        
        // LOGO
        $flogo = '';
        $chercher_logo = charger_fonction('chercher_logo', 'inc');
        $logo = $chercher_logo($id_evenement, "evenement", 'on');
        if ($logo) {
            list($fid, $dir, $nom, $format) = $logo;
            include_spip('inc/filtres_images_mini');
            $logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
            if ($logo) {
                $flogo = "\n$logo";
            }
        }
        // ---
        
        
        
        
        // Stockage des différentes valeurs pour affichage
        $vals[] = "\n".$puce_statut;
        $vals[] = "\n".$lien_evenement;
        $vals[] = "\n".$titre.$lien_objet;
        $vals[] = "\n".$lien_auteur;
        //$vals[] = "\n".$lien_objet;
        $vals[] = "\n".$flogo;
        $vals[] = "\n".$date_publi;
        //$vals[] = "\n".$lien_modifier;
        $vals[] = afficher_numero_edit($id_evenement, $primary, $type, $row);
    }
    return $vals;

}

?>
