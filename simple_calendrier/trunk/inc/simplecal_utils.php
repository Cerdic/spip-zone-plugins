<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
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

// Converti une date saisie a la main en date SQL
// Retourne une date a zero si conversion impossible.
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


// Plugin Acces restreint : 
// retourne la liste des rubriques interdites pour l'auteur connecte
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
?>