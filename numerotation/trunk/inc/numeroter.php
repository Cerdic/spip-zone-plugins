<?php
/*
 * Plugin numero
 * aide a la numerotation/classement des objets dans l'espace prive
 *
 * Auteurs :
 * Cedric Morin, Nursit.com
 * (c) 2008-2014 - Distribue sous licence GNU/GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_NUMEROTE_STEP')) define('_NUMEROTE_STEP',10);

/**
 * Enlever le numero d'un titre
 * @param $titre
 * @return mixed
 */
function numero_denumerote_titre($titre){
	return preg_replace(',^([0-9]+[.]\s+),','',$titre);
}

/**
 * Informations concernant un objet
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
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
			'tri_date' => '',
		);
		if (isset($desc['date']) AND isset($desc['field'][$desc['date']])){
			$infos[$objet]['tri_date'] = $desc['date'];
		}
		elseif(isset($desc['field']['maj'])){
			$infos[$objet]['tri_date'] = 'maj';
		}
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

/**
 * Requeter le(s) titres d'un objet selon une serie de conditions fournies en argument
 * @param string $type
 * @param array $cond
 * @param bool $count
 * @return resource|int
 */
function numero_requeter_titre($type,$cond = array(), $count=false){
	$d = numero_info_objet($type);
	$select = array(
		$d['primary']." AS id",
		$d['titre']." AS titre",
	);
	if ($d['parent'])
		$select[] = $d['parent']." AS id_parent";
	else
		$select[] = '0 AS id_parent';

	$order = "0+titre,titre";
	if ($d['tri_date']){
		$order .= "," . $d['tri_date']." DESC";
	}
	if ($count)
		$res  = sql_countsel($d['table_sql'],$cond,'');
	else
		$res = sql_select($select,$d['table_sql'],$cond,'',$order);
	return $res;
}

/**
 * changer le titre d'un objet en base
 * @param string $type
 * @param int $id
 * @param string $titre
 */
function numero_titrer_objet($type,$id,$titre){
	$d = numero_info_objet($type);
	sql_updateq($d['table_sql'],array($d['titre']=>$titre),$d['primary']."=".intval($id));
}

/**
 * Numeroter/denumeroter les objets d'un parent
 * @param string $type
 * @param int $id_parent
 * @param bool $remove
 */
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

/**
 * Compat version anterieure
 * @param int $id_rubrique
 * @param string $type
 * @param bool $numerote
 */
function numero_numeroter_rubrique($id_rubrique,$type='rubrique',$numerote=true){
	return numero_numeroter_objets($type,$id_rubrique,!$numerote);
}


/**
 * Lister tous les objets freres d'un objet (avec le meme parent)
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function numero_lister_fratrie($objet,$id_objet){
	// recuperer le titre/parent de l'objet
	$d = numero_info_objet($objet,$id_objet);
	$cond = array($d['primary']."=".intval($id_objet));
	$res = numero_requeter_titre($objet,$cond);
	$row = sql_fetch($res);
	$cond = array();
	if ($d['parent'])
		$cond = array($d['parent']."=".$row['id_parent']);

	// si plus de 1000 on n'affiche plus rien
	$n = numero_requeter_titre($objet,$cond, true);
	if ($n>1000)
		return array();

	$res = numero_requeter_titre($objet,$cond);
	$fratrie = array();
	while($row = sql_fetch($res)){
		$fratrie[$row['id']] = $row['titre'];
	}
	return $fratrie;
}

/**
 * Trouver le precedent dans la liste numerotee (ou non)
 * @param int $id_objet
 * @param array $fratrie
 * @return int
 */
function numero_trouver_precedent($id_objet,$fratrie){
	$ids = array_keys($fratrie);
	$k = array_search($id_objet,$ids);
	if ($k==0)
		return 0;
	return $ids[$k-1];
}