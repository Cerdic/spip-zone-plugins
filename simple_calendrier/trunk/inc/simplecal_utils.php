<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// Affiche une date SQL sous la forme jj/mm/aaaa
function date_sql2affichage($texte) {
	// texte = 2010-05-12 07:55:00
	$date = "";
    
    //(Note : regex ne matche pas le 0000-00-00 00:00:00)
    if (preg_match("#([1-2][0-9]{3})\-(.*)\-(.*) .*#i", $texte, $matches)){
		$date = $matches[3]."/".$matches[2]."/".$matches[1];
    }
    
    return $date;
}

// Converti une date saisie à la main en date SQL
// Retourne une date à zéro si conversion impossible.
function date_saisie2sql($texte) {
	// texte = jj/mm/aaaa ou separateur = [/, -, .]
	$texte = trim($texte);
    $date = "0000-00-00 00:00:00";
    
    if (preg_match("#^([0-9]{2})[\-,\., \/]([0-9]{2})[\-,\., \/]([1-2][0-9]{3})$#i", $texte, $matches)){
		$date = $matches[3]."-".$matches[2]."-".$matches[1]." 00:00:00";
    }
    
    return $date;
}


function simplecal_affiche_dates($date_debut=null, $date_fin=null, $with_prefixe=false){
    $s = '';
    if (isset($date_debut) && $date_debut != '0000-00-00 00:00:00'){
        if (isset($date_fin) && $date_fin != '0000-00-00 00:00:00'){
            if ($with_prefixe){ 
                $s.='Dates : '; 
            }
            $s .= _T('simplecal:date_du_au', array('date_debut'=>affdate_jourcourt($date_debut), 'date_fin'=>affdate_jourcourt($date_fin)));
        } else {
            if ($with_prefixe){ 
                $s.='Date : '; 
            }
            $s .= _T('simplecal:date_le', array('date'=>affdate_jourcourt($date_debut)));
        }
    } else {
        if (isset($date_fin) && $date_fin != '0000-00-00 00:00:00'){
            if ($with_prefixe){ 
                $s.='Date : '; 
            }
            $s .= _T('simplecal:date_jusque', array('date'=>affdate_jourcourt($date_fin)));
        }
    }
    
    return $s;
}

function simplecal_is_ref_ok($ref){
    $b = false;
    if (preg_match("/^(article|breve)([0-9]*)$/i", $ref, $matches)){
        $b = true;
    }
    return $b;
}

// 'breve17' => ['type'=>'breve', 'id_objet'=>'17']
function simplecal_get_tuple_from_ref($ref){
    $tab = array();
    if (preg_match("/^(article|breve)([0-9]*)$/i", $ref, $matches)){
		$tab['type'] = $matches[1];
        $tab['id_objet'] = $matches[2];        
    }
    
    return $tab;
}

// 'breve', '17' => 'breve17'
function simplecal_get_ref_from_obj($type, $id_objet){
    $le_type = '';
    $id = '';
    
    if (preg_match("/^(article|breve)$/i", $type, $matches)){
        $le_type = $matches[0];
    }
    
    if (preg_match("/^([0-9]*)$/i", $id_objet, $matches)){
        $id = $matches[0];
    }    
    
    $ref = '';
    if ($le_type && $id){
        $ref = $le_type.$id;
    }
    
    return $ref;
}

// 'breve', '17' => 'Le titre de la breve n°17'
function simplecal_get_titre_from_obj($type, $id_objet){
    $le_type = '';
    $id = '';
    
    if (preg_match("/^(article|breve)$/i", $type, $matches)){
        $le_type = $matches[0];
    }
    
    if (preg_match("/^([0-9]*)$/i", $id_objet, $matches)){
        $id = $matches[0];
    }    
    
    $titre = '';
    if ($le_type && $id){
        $row = sql_fetsel("o.titre", "spip_".$le_type."s as o", "o.id_".$le_type."=".$id);
        $titre = $row['titre'];
    }
    
    return $titre;
}


function simplecal_get_url_for_obj($type, $id_objet){
    $url='';
    
    if ($type=='article'){
        $url = generer_url_ecrire($type."s","id_$type=$id_objet");
    } else {
        $url = generer_url_ecrire($type."s_voir","id_$type=$id_objet");
    }
    
    return $url;
}

function simplecal_get_url_for_ref($ref){
    $tab = simplecal_get_tuple_from_ref($ref);
    $type = $tab['type'];
    $id_objet = $tab['id_objet'];
    
    $url = simplecal_get_url_refobj($type, $id_objet);
    return $url;
}

function simplecal_get_url_refobj($type, $id_objet){
    if ($type == 'article'){
        $url = generer_url_ecrire($type."s","id_$type=$id_objet");
    } else {
        $url = generer_url_ecrire($type."s_voir","id_$type=$id_objet");
    }
    
    return $url;
}

// Portlet d'ajout d'évènement (fiche article/breve)
function simplecal_get_portlet_ajout($type, $id_objet){
    $ref = $type.$id_objet;
    
    $bloc = "";
    $bloc .= debut_cadre_enfonce(_DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", $return=true, $fonction='', $titre=_T('simplecal:titre_boite_refobj'));
    
    $ul = "";
    $rows = sql_allfetsel("e.*", "spip_evenements as e", "e.type='".$type."' and e.id_objet=".$id_objet);
    foreach ($rows as $row){
        $id_evt = $row['id_evenement'];
        $ul .= '<li><a href="'.generer_url_ecrire("evenement_voir", "id_evenement=$id_evt").'">'.simplecal_affiche_dates($row['date_debut'], $row['date_fin']).'</a></li>';
    }
    if ($ul!=""){
        $ul = '<ul>'.$ul.'</ul>';
        $bloc .= $ul;
    }
    
    if (autoriser('creer', 'evenement', null)){
        $bloc .= icone_horizontale(_T('simplecal:raccourcis_ajouter_date'), generer_url_ecrire("evenements_edit", "new=oui&retour=objet&refobj=$ref"), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "creer.gif", false);
    }
    $bloc .= fin_cadre_enfonce(true);
    
    return $bloc;
}

// Portlet de gestion des evenements de la rubrique
function simplecal_get_portlet_rubrique($id_rubrique){
    
    $bloc = "";
    $bloc .= debut_cadre_enfonce(_DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", $return=true, $fonction='', $titre=_T('simplecal:titre_boite_rubrique'));
    
    
    $nb = sql_countsel("spip_evenements as e", "e.id_rubrique=".$id_rubrique);
    if ($nb == 1){
        $phrase = '<strong>'.$nb.'</strong> '._T('simplecal:terme_evenement');
    } else if ($nb > 1){
        $phrase = '<strong>'.$nb.'</strong> '._T('simplecal:terme_evenements');
    } else {
        $phrase = '<strong>aucun</strong> '._T('simplecal:terme_evenement');
    }
    
    $phrase = '<div class="simplecal-nbinrub">'.$phrase.'</div>';
    
    $bloc.=$phrase;
    
    $bloc .= icone_horizontale(_T('simplecal:raccourcis_liste_evenements_rubrique'), generer_url_ecrire("evenement_tous", "id_rubrique=$id_rubrique"), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "", false);
    if (autoriser('creer', 'evenement', null)){
        $bloc .= icone_horizontale(_T('simplecal:raccourcis_ecrire_evenement'), generer_url_ecrire("evenements_edit", "new=oui&retour=rubrique&id_rubrique=$id_rubrique"), _DIR_SIMPLECAL_IMG_PACK."simplecal-logo-24.png", "creer.gif", false);
    }
    $bloc .= fin_cadre_enfonce(true);
    
    return $bloc;
}


// Plugin Acces restreint : 
// retourne la liste des rubriques interdites pour l'auteur connecté
function simplecal_get_ids_rubriques_exclues(){
    $ids = "";
    if (defined('_DIR_PLUGIN_ACCESRESTREINT')){
        include_spip('inc/acces_restreint');
        $id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
        $rub_exclues = accesrestreint_liste_rubriques_exclues(false, $id_auteur);

        if (count($rub_exclues)>0){
            $ids = join(',', $rub_exclues);
        }
    }
    return $ids;
}

// Plugin Acces restreint : 
// retourne un "and id_rubrique not in ($ids)"
function simplecal_get_where_rubrique_exclure($avec_and=true){
    $condition = "";
    if (defined('_DIR_PLUGIN_ACCESRESTREINT')){
        $ids = simplecal_get_ids_rubriques_exclues();
        if ($ids){
            $and = $avec_and ? " and " : "";
            $condition = $and."id_rubrique not in ($ids)";
        }
    }
    
    return $condition;
}


function simplecal_liste_themes($select_name, $choix){
    // Version Php5 : ne fonctionne pas facilement sous OVH
    //$dir_theme = _DIR_SIMPLECAL_PRIVE.'css/datepicker/';
    //$dirs = scandir($dir_theme, 0);
    //$dirs = array_slice ($dirs, 2); 

    // Version Php4                    
    $dir_theme = _DIR_SIMPLECAL_PRIVE.'css/datepicker/';
    $dh  = opendir($dir_theme);
    while (false !== ($filename = readdir($dh))) {
        $dirs[] = $filename;
    }
    sort($dirs);
    $dirs = array_slice ($dirs, 2); // retire les 2 premiers dossiers (. et ..)

    // -----
    
    $s="";
    $s.="\n<select name=\"$select_name\">";
    
    foreach ($dirs as $dir){
        if ($dir == $choix){
            $s.="\n\t<option name=\"$dir\" selected=\"selected\">$dir</option>";
        } else {
            $s.="\n\t<option name=\"$dir\">$dir</option>";
        }
    }
    
    
    $s.="\n</select>";
    
    return $s;
}
?>