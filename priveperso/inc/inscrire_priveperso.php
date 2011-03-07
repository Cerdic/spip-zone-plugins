<?php

function priveperso_ecrire_db($priveperso,$table){

include_spip('base/abstract_sql');


	$rub_id = $priveperso['rub_id'];
	$u = sql_select("rub_id",$table,"rub_id='$rub_id'");
	$b = sql_fetch($u);
	                                
	if ($b['rub_id']==$rub_id){
			$res = sql_updateq($table, $priveperso, "rub_id='$rub_id'");
	}
	else {
			$res = sql_insertq($table, $priveperso);                              
	}
			
	return $res;


}

function priveperso_recuperer_valeurs($rub_id) {

      $trouver_table = charger_fonction('trouver_table', 'base');
      $desc = $trouver_table('priveperso');

		foreach ($desc['field'] as $key => $val){
			$u = sql_select("$key","spip_priveperso","rub_id='$rub_id'");
			$b = sql_fetch($u);
			$priveperso[$key] = $b[$key];
			
		}
		return $priveperso;
}

function priveperso_texte_recuperer_valeurs($rub_id) {

      $trouver_table = charger_fonction('trouver_table', 'base');
      $desc = $trouver_table('priveperso_texte');

		foreach ($desc['field'] as $key => $val){
			$u = sql_select("$key","spip_priveperso_texte","rub_id='$rub_id'");
			$b = sql_fetch($u);
			$priveperso[$key] = $b[$key];
		}
	
		return $priveperso;
}

function priveperso_rubrique_deja_perso($rub_id) {

	$u = sql_select("rub_id, activer_perso","spip_priveperso","rub_id='$rub_id'");
	$b = sql_fetch($u);

	if ($b['activer_perso']==='oui') {$res = true;}
	else {$res = false;}
	
		return $res;
}

function priveperso_trouver_rubrique_parent_perso($rub_id) {


	$u = sql_select("id_parent, id_secteur","spip_rubriques",'id_rubrique='.intval($rub_id));
	$b = sql_fetch($u);
	$id_parent = $b['id_parent'];
	$id_secteur = $b['id_secteur'];
	$v = sql_select("rub_id,sousrub, activer_perso","spip_priveperso",'rub_id='.intval($id_parent));
	$c = sql_fetch($v);
	if ( ($c) && ($c['sousrub']==='oui') && ($c['activer_perso']==='oui') ){
		return $id_parent;}
	else{
		if ($id_parent==='0'){
			return '0';			
			}
			else{
				return priveperso_trouver_rubrique_parent_perso($id_parent);
				}
		}

}

function priveperso_recupere_id_rubrique(){
	// on récupère le type d'objet sur lequel se fait la navigation
$id_rub = $_GET['id_rubrique'];
$id_art = $_GET['id_article'];
$id_bre = $_GET['id_breve'];
$id_syn = $_GET['id_syndic'];
$id_par = $_GET['id_parent'];

if ($id_par) return $id_par;

if ($id_rub | $id_art | $id_bre | $id_syn){

// Construction de la requete sql pour aller récupérer la rubrique
	$select = "id_rubrique";
if ($id_rub) {
	$from = "spip_rubriques";
	$where = "id_rubrique=".sql_quote($id_rub);
	}
if ($id_art) {
	$from = "spip_articles";
	$where = "id_article=".sql_quote($id_art);
	}
if ($id_bre) {
	$from = "spip_breves";
	$where = "id_breve=".sql_quote($id_bre);
	}
if ($id_syn) {
	$from = "spip_syndic";
	$where = "id_syndic=".sql_quote($id_syn);
	}


		$resultats = sql_select($select, $from, $where);
		$row = sql_fetch($resultats);
    	$id_rubrique = $row['id_rubrique'];

	return $id_rubrique;

	}
}


?>
