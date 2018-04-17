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
		
		// Si l'objet a un champ id_rubrique, on pré-remplit le champ du parent
		if (isset($desc['field']['id_rubrique'])) {
			$parent = "id_rubrique";
		}
		
		// Suivant le type d'objet, on gère des cas particuliers
		switch($type) {
			case 'rubrique':
				$parent = "id_parent";
				break;
			case 'mot':
				$parent = "id_groupe";
				break;
			case 'groupe_mots':
				$parent = "";
				break;
			// Type non prevu ici (et pas rangé dans une rubrique) : on ne fait rien, on quitte
			default :
				if (!$parent) {
					return false;
				}
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
			'numerotable' => true,
		);
		if (isset($desc['date']) AND isset($desc['field'][$desc['date']])){
			$infos[$objet]['tri_date'] = $desc['date'];
		}
		elseif(isset($desc['field']['maj'])){
			$infos[$objet]['tri_date'] = 'maj';
		}
		
		// Extraire le champ titre SI on a "as titre" dedans
		// car sinon ça veut dire que c'est le champ titre tout court
		if (
			isset($desc['titre'])
			and stripos($desc['titre'], 'as titre') !== false
		){
			// On récupère ce qui est avant le "as titre"
			if (preg_match('/(^|,)([\w\s]+)\s+as titre/i', $desc['titre'], $trouve)) {
				$infos[$objet]['titre'] = trim($trouve[2]);
			}
			// On a pas trouvé, l'objet n'est donc pas numérotable
			else {
				$infos[$objet]['numerotable'] = false;
			}
		}
		elseif(!isset($desc['field']['titre'])) {
			$infos[$objet]['numerotable'] = false;
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
	$numero_numeroter_objets = charger_fonction('numero_numeroter_objets', 'inc');
	return $numero_numeroter_objets($type, $id_parent, $remove);
}

/**
 * Compat version anterieure
 * @param int $id_rubrique
 * @param string $type
 * @param bool $numerote
 */
function numero_numeroter_rubrique($id_rubrique,$type='rubrique',$numerote=true){
	$numero_numeroter_objets = charger_fonction('numero_numeroter_objets', 'inc');
	return $numero_numeroter_objets($type, $id_rubrique, !$numerote);
}


/**
 * Lister tous les objets freres d'un objet (avec le meme parent)
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function numero_lister_fratrie($objet,$id_objet){
	$fratrie = array();
	// recuperer le titre/parent de l'objet
	$d = numero_info_objet($objet,$id_objet);
	$cond = array($d['primary']."=".intval($id_objet));
	$res = numero_requeter_titre($objet,$cond);
	if ($row = sql_fetch($res)) {
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
