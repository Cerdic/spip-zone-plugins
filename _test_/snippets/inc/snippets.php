<?php
/*
 * snippets
 * Gestion d'import/export XML de contenu
 *
 * Auteurs :
 * Cedric Morin
 * � 2006 - Distribue sous licence GNU/GPL
 *
 */

function snippets_fonction_importer($table){
	if (substr($table,0,5)=="spip_") $table = substr($table,5);
	return ($f = charger_fonction("importer","snippets/$table",true));
}
function snippets_fond_exporter($table,$find = true){
	if (substr($table,0,5)=="spip_") $table = substr($table,5);
	$f = "snippets/$table/exporter";
	if ($find)
		$f = find_in_path("$f.html");
	return $f;
}

function snippets_liste_imports($table){
	$pattern = $table;
	if (substr($table,0,5)=="spip_") $table = substr($table,5);
	
	$pattern = ".*[.]xml$";
	$snippets = find_all_in_path("snippets/$table/",$pattern);
	return $snippets;
}
function snippets_type_de_table($table){
	$type = $table;
	if (substr($type,-1)=="s") $type = substr($type,0,strlen($type)-1);
	if (substr($type,0,5)=="spip_") $type = substr($type,5);
	return $type;
}

function boite_snippets($titre,$icone,$table,$id,$contexte="",$retour = ""){
	include_spip('inc/autoriser');
	if (!strlen($retour))
		$retour = self();
	$out = "";
	
	// verifier les droits
	$auth = false;
	$type = snippets_type_de_table($table);
	if (intval($id)==$id) {
		$auth = autoriser('modifier',$type,$id);
	}
	else {
		$auth = true;
		if ( (count($t = explode('=',$contexte))==2) AND ($id_contexte=intval($t[1])) ) {
			$type_contexte = $t[0];
			if (substr($type_contexte,0,3)=="id_") $type_contexte = substr($type_contexte,3);
			$auth = autoriser('modifier',$type_contexte,$id_contexte);
		}
		$auth &= autoriser('creer',$type,$id);
	}
	if (!$auth) return "";
	
	// verifier le support de l'objet pour l'import/export
	$export_possible = (intval($id) AND $f = snippets_fond_exporter($table));
	$import_possible = ($f=snippets_fonction_importer($table));
	$import_creation = ($id !== intval($id));
	if (!$import_possible && !$export_possible) return "";

	$idbox="snippet_$table_$id";
	$out .= icone_horizontale($titre, "#", $icone, _DIR_PLUGIN_SNIPPETS."images/import".($export_possible?"_export":"").".gif", false, "onclick='$(\"#$idbox\").slideToggle(\"fast\");'");
	$out .= "<div id='$idbox' style='display:none;' >\n";
	$out .= debut_cadre_relief('',true);

	// icone d'export
	if (intval($id) AND $f = snippets_fond_exporter($table)){
		$action = generer_action_auteur('snippet_exporte',"$table:$id",$retour);
		$out .= icone_horizontale(_T('snippets:exporter'), $action, $icone, _DIR_PLUGIN_SNIPPETS."images/export.gif", false);
		$out .= "<hr/>";
	}

	// liste des snippets disponibles pour import
	$liste = snippets_liste_imports($table);
	foreach($liste as $snippet){
		if (!_DIR_RESTREINT) $snippet = substr($snippet,strlen(_DIR_RACINE));
		$action = generer_action_auteur('snippet_importe',"$table:$id:$contexte:$snippet",$retour);
		$out .= icone_horizontale(basename($snippet,".xml"), $action, $icone, $import_creation?"creer.gif":_DIR_PLUGIN_SNIPPETS."images/import.gif", false);
	}
	
	// formulaire d'upload d'un snippet
	$action = generer_action_auteur('snippet_importe',"$table:$id:$contexte",$retour);
	$out .= "<form action='$action' method='POST' enctype='multipart/form-data'>";
	$out .= form_hidden($action);
	$out .= "<strong><label for='file_name'>"._T("snippets:importer_fichier")."</label></strong> ";
	$out .= "<br />";
	$out .= "<input type='file' name='snippet_xml' id='file_name' class='formo'>";
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";
	

	$out .= fin_cadre_relief(true)."</div>";
	return $out;
}

function snippets_liste_raccourcis($table){
	$type = snippets_type_de_table($table);
	$l = array();
	$rac_historiques = array(
		'document' => '|doc|im|img|image|emb', 
		'article' => '|art', 
		'rubrique' => '|rub',
		'auteur' => '|aut',
		'breve' => '|br..?ve' # accents :(
		);

	// les modeles
	$rac = $type;
	if (isset($rac_historiques[$type]))
		$rac .= $rac_historiques[$type];
	
	$l[",([^a-z])($type)%d([^0-9]),"] = "$1$2%d$3" ;
	
	return $l;
}

// $translations est un array de ($table, $id_objet_ancien, $id_objet_nouveau)
function snippets_translate_raccourcis_modeles($translations){
	$trans = array();
	$liste = array();
	if (!is_array($translations)) return;
	foreach($translations as $translatation) {
		$t = $translatation[0];
		// recuperer la liste des raccourcis a changer pour ce type
		// si necessaire
		if (!isset($liste[$t])) $liste[$t] = snippets_liste_raccourcis($table);
		// ajouter la liste des translations a faire pour ce type
		foreach($liste as $f=>$r)
			$trans[str_replace('%d',$translatation[1],$f)] = str_replace('%d',$translatation[2],$r);
	}
	$a_type = array();
	$revision = array();
	foreach($translations as $translatation){
		$table = $translatation[0];
		if (!isset($a_type[$table])){
			$a_type[$table] = snippets_type_de_table($table);
			$rev = $table;
			if (substr($rev,0,5)=="spip_") $rev = substr($type,5);
			$rev = "revision_$rev";
			if (include_spip("action/editer_$type") && function_exists($rev))
				$revision[$table] = $rev;
		}
		$type = $a_type[$table];
		$id = $translatation[1];
		$prim = "id_$type";
		$row = spip_fetch_array(spip_query("SELECT * FROM $table WHERE $prim="._q($id)));
		if ($row) {
			foreach($row as $key => $val){
				$val = preg_replace(array_keys($trans),$trans,$val);
				if ($row[$key]!=$val)
					$row[$key]=$val;
				else
					unset($row[$key]);
			}
			if (count($row)){
				if (isset($revision[$table]))
					$revision[$table]($id,$row);
				else {
					$set = "";
					foreach($row as $key=>$val){
						$set .= "$key="._q($val).", ";
					}
					$set = substr($set,0,strlen($set)-2);
					spip_query("UPDATE $table SET $set WHERE $prim="._q($id));
				}
			}
		}
	}
}



// d'apres spip2spip par erationnal
// recupere id d'un auteur selon son nom ou le creer
function get_id_auteur($name) {
    if (trim($name)=="") return false;    
    $sql = "SELECT id_auteur FROM spip_auteurs WHERE nom='".addslashes(filtrer_entites($name))."'";
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_auteur'];
    }
    // auteur inconnu, on le cree ...
    spip_log("creation auteur".$name,"snippets");
    return sql_insertq('spip_auteurs',array('nom'=>$name,'statut'=>'1comite')) ;
}

// recupere un id_mot selon le type|titre ou le creer

function get_id_mot($name) {
  
   if (trim($name)=="") return false; 
    list($type,$titre) = explode('|',$name) ;
    $sql = "SELECT id_mot FROM spip_mots WHERE titre='".addslashes(filtrer_entites($titre))."'";
    $result = spip_query($sql);
    while ($row = spip_fetch_array($result)) {
       return $row['id_mot'];
    }

    // creer le groupe ?
    $sql = "SELECT id_groupe FROM spip_groupes_mots WHERE titre='".addslashes(filtrer_entites($type))."'";
    $result2 = spip_query($sql);
    $nb = sql_count($result2);
    if ($nb == 0) {
       $id_groupe = sql_insertq('spip_groupes_mots',array('titre'=>$type)) ;
    }
    while ($row = spip_fetch_array($result2)) {
       $id_groupe = $row['id_groupe'];
    }
    
    // mot inconnu, on le cree ...
     spip_log("creation mot : ".$titre."groupe ".$id_groupe,"snippets");
    return  sql_insertq('spip_mots',array('type'=>$type, 'titre'=>$titre , 'id_groupe' => $id_groupe)) ;

}

?>