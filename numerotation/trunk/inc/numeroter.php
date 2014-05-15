<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_NUMEROTE_STEP')) define('_NUMEROTE_STEP',10);

function numero_denumerote_titre($titre){
	return preg_replace(',^([0-9]+[.]\s+),','',$titre);
}

function numero_info_objet($objet,$id_objet=0){
	static $infos = array();
	include_spip("base/abstract_sql");
	if (!isset($infos[$objet])){
		$type = objet_type($objet);
		$table = table_objet($type);
		$table_sql = table_objet_sql($type);
		$key = id_table_objet($type);
		$trouver_table = charger_fonction("trouver_table","base");
		$desc = $trouver_table($table);

		// champ parent
		$parent = "";
		if (isset($desc['field']['id_rubrique']))
			$parent = "id_rubrique";
		switch($type){
			case 'rubrique': $parent = "id_parent";break;
			case 'mot': $parent = "id_groupe";break;
			case 'groupe_mots': $parent = "";break;
			// type non prevu ici (et pas range dans une rubrique) : on ne fait rien
			default :
				if (!$parent) false;
				break;
		}

		$infos[$objet] = array(
			'type' => $type,
			'table' => $table,
			'table_sql' => $table_sql,
			'primary' => $key,
			'desc' => $desc,
			'parent' => $parent,
			'titre' => 'titre',
		);
		// extraire le champ titre
		if (isset($desc['titre'])){
			$infos[$objet]['titre'] = explode(',',$desc['titre']);
			$infos[$objet]['titre'] = explode(' ',reset($infos[$objet]['titre']));
			$infos[$objet]['titre'] = reset($infos[$objet]['titre']);
		}
	}
	$res = $infos[$objet];

	if ($id_objet AND $res['parent']){
		$res['id_parent'] = sql_getfetsel($res['parent'],$res['table_sql'],$res['primary']."=".intval($id_objet));
	}

	return $res;
}

function numero_requeter_titre($type,$cond = array()){
	$d = numero_info_objet($type);
	$select = array(
		$d['primary']." AS id",
		$d['titre']." AS titre",
	);
	if ($d['parent'])
		$select[] = $d['parent']." AS id_parent";
	else
		$select[] = '0 AS id_parent';
	$res = sql_select($select,$d['table_sql'],$cond,'',"0+titre,titre,maj DESC");
	return $res;
}

function numero_titrer_objet($type,$id,$titre){
	$d = numero_info_objet($type);
	sql_updateq($d['table_sql'],array($d['titre']=>$titre),$d['primary']."=".intval($id));
}

function numero_numeroter_objets($type='rubrique',$id_parent,$remove=false){
	$d = numero_info_objet($type);
	if (!$d)
		return;

	$type = $d['type'];
	$table = $d['table'];
	$table_sql = $d['table_sql'];
	$key = $d['primary'];
	$desc = $d['desc'];
	$parent = $d['parent'];
	$titre = $d['titre'];

	$cond = array();
	$zero = true;
	if (!$remove AND
		$type=='article'){
		$row = false;
		if (defined('_NUMERO_MOT_ARTICLE_ACCUEIL')) {
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_mots_liens as J ON (J.id_objet=a.id_article AND J.objet='article')",
				"a.id_rubrique=".sql_quote($id_parent)."
			 AND J.id_mot=".sql_quote(_NUMERO_MOT_ARTICLE_ACCUEIL),'',"0+a.titre, a.maj DESC","0,1");
		}
		if (defined('_DIR_PLUGIN_ARTICLE_ACCUEIL')){
			// numeroter 0. l'article d'accueil de la rubrique
			$row = sql_fetsel("a.id_article,a.titre",
				"spip_articles AS a INNER JOIN spip_rubriques as J ON J.id_article_accueil=a.id_article",
				"a.id_rubrique=".sql_quote($id_parent),'',"0+a.titre, a.maj DESC","0,1");
		}
		if ($row){
			$titre = "0. " . numero_denumerote_titre($row['titre']);
			if ($titre!==$row['titre'])
				sql_updateq($table_sql,array('titre'=>$titre),"$key=".sql_quote($row[$key]));
			$zero = false;
			$cond[] = "id_article<>".sql_quote($row[$key]);
		}
	}
	if ($type=='article') {
		$cond[] = "statut!=".sql_quote('poubelle');
	}

	if ($parent){
		$cond[] = "$parent=".sql_quote($id_parent);
	}

	$res = numero_requeter_titre($type,$cond);
	$cpt = 1;
	while($row = sql_fetch($res)) {
		// conserver la numerotation depuis zero si deja presente
		if ($zero && ($cpt==1) && preg_match(',^0+[.]\s,',$row['titre'])) {
			$zero = false;
			$cpt = 0;
		}
		$t = (!$remove?($cpt*_NUMEROTE_STEP) . ". ":"") . numero_denumerote_titre($row['titre']);
		if ($t!==$row['titre']){
			numero_titrer_objet($type,$row['id'],$t);
		}
		$cpt++;
	}
	return;
}


function numero_lister_fratrie($objet,$id_objet){
	// recuperer le titre/parent de l'objet
	$d = numero_info_objet($objet,$id_objet);
	$cond = array($d['primary']."=".intval($id_objet));
	$res = numero_requeter_titre($objet,$cond);
	$row = sql_fetch($res);
	$cond = array();
	if ($d['parent'])
		$cond = array($d['parent']."=".$row['id_parent']);
	$res = numero_requeter_titre($objet,$cond);
	$fratrie = array();
	while($row = sql_fetch($res))
		$fratrie[$row['id']] = $row['titre'];

	return $fratrie;
}
function numero_trouver_precedent($id_objet,$fratrie){
	$ids = array_keys($fratrie);
	$k = array_search($id_objet,$ids);
	if ($k==0)
		return 0;
	return $ids[$k-1];
}