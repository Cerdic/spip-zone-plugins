<?php

include_spip('base/abstract_sql');

global $kconf;
$kconf['i']['rubrique']['objet'] = 'rubrique';
$kconf['i']['rubrique']['autoriser'] = 'publierdans';
$kconf['i']['rubrique']['autoriser_c'] = 'publierdans';
$kconf['i']['rubrique']['conteneur'] = 'rubrique';
$kconf['i']['rubrique']['c_objet'] = 'rubrique';
$kconf['i']['rubrique']['o_parent'] = 'parent';
$kconf['i']['rubrique']['c_parent'] = 'parent';
$kconf['i']['rubrique']['exec'] = 'naviguer';
$kconf['i']['rubrique']['exec_c'] = 'naviguer';
$kconf['i']['rubrique']['o'] = array();
$kconf['i']['rubrique']['c'] = &$kconf['i']['rubrique']['o'];

$kconf['i']['article']['objet'] = 'article';
$kconf['i']['article']['autoriser'] = 'modifier';
$kconf['i']['article']['autoriser_c'] = 'publierdans';
$kconf['i']['article']['conteneur'] = 'rubrique';
$kconf['i']['article']['c_objet'] = 'article_rubrique';
$kconf['i']['article']['o_parent'] = 'rubrique';
$kconf['i']['article']['c_parent'] = 'parent';
$kconf['i']['article']['exec'] = 'articles';
$kconf['i']['article']['exec_c'] = 'naviguer';
$kconf['i']['article']['o'] = array();
$kconf['i']['article']['c'] = array();

function kconf_hierarchie($I,$id_objet,$O='o') {
	global $kconf;
	$o = $kconf['i'][$I]['objet'];
	$o_parent = $kconf['i'][$I]['o_parent'];
	$c = $kconf['i'][$I]['conteneur'];
	$c_parent = $kconf['i'][$I]['c_parent'];
	$k_c = $kconf['i'][$I]['c_objet'];
	
	if ($O=='o') {
		// l'objet
		if (isset($kconf['i'][$I]['o'][$id_objet]['parent'])) {
			$id_parent = $kconf['i'][$I]['o'][$id_objet]['parent'];
// 			spip_log("cache,$id_parent, $valeur,");
		} else {
			$select = $id_objet ? "id_$o_parent as id_parent, spip_kconf_${o}s.valeur as kconf" : "spip_kconf_${o}s.valeur as kconf";
			$from = $id_objet ? "spip_${o}s LEFT JOIN spip_kconf_${o}s on spip_${o}s.id_${o} = spip_kconf_${o}s.id_${o}" : "spip_kconf_${o}s";
			$where = $id_objet ? "spip_${o}s.id_${o}=$id_objet" : "spip_kconf_${o}s.id_${o}=0";
			$row = sql_fetsel($select, $from, $where);
			$id_parent = $row['id_parent'];
			// création du tableau des valeurs
			$kconf['i'][$I]['o'][$id_objet]['clefs'] = kconf_unserialize($row['kconf']);
			$kconf['i'][$I]['o'][$id_objet]['parent'] = intval($id_parent);
// 			spip_log("pas cache pour $id_objet: parent de l'objet: $id_parent et clefs: ".var_export($kconf['i'][$I]['o'][$id_objet]['clefs'],true));
		}
		if ($id_parent!==null) {
			$id_parent = intval($id_parent);
			$ret[] = $id_parent;
		} else if ($id_objet==0) unset($id_parent);
	} else {
		$id_parent = intval($id_objet);
	}
// 	spip_log("hierarchie $O : $id_objet => $id_parent");
	
	// le conteneur
	while (is_int($id_parent)) {
		if (isset($kconf['i'][$I]['c'][$id_parent]['parent'])) {
			$parent = $kconf['i'][$I]['c'][$id_parent]['parent'];
// 			spip_log("cache,$id_parent, $valeur,");
		} else {
			$select = $id_parent ? "id_$c_parent as id_parent, spip_kconf_${k_c}s.valeur as kconf" : "spip_kconf_${k_c}s.valeur as kconf";
			$from = $id_parent ? "spip_${c}s LEFT JOIN spip_kconf_${k_c}s on spip_${c}s.id_${c} = spip_kconf_${k_c}s.id_${c}" : "spip_kconf_${k_c}s";
			$where = $id_parent ? "spip_${c}s.id_${c}=$id_parent" : "spip_kconf_${k_c}s.id_${c}=0";
			$row = sql_fetsel($select, $from, $where);
			$parent = $row['id_parent'];
		// création du tableau des valeurs
			$kconf['i'][$I]['c'][$id_parent]['clefs'] = kconf_unserialize($row['kconf']);
			$kconf['i'][$I]['c'][$id_parent]['parent'] = intval($parent);
// 			spip_log("pas cache pour $id_parent: parent du conteneur $parent et clefs: ".var_export($kconf['i'][$I]['c'][$id_parent]['clefs'],true));
/*			if ($kconf['i'][$objet]['c'][$id_parent]['clefs'])
				spip_log(var_export($kconf['i'][$objet]['c'][$id_parent]['clefs'],true)." yyyyypi");*/
		}
		if ($parent!==null) {
			$ret[] = intval($parent);
		}
// 		spip_log("hierarchie $O : $id_parent => $parent");
		if ($id_parent==0) break;
		$id_parent = intval($parent);
	}
	
	return $ret;
}

function kconf_recevoir_valeur($I,$id_objet,$clef,$O='o') {
	global $kconf;
	if (!($id_parent = $kconf['i'][$I][$O][$id_objet]['parent'])) { // <>< risque d'appeler hierarchie pour 0 alors que pas besoin (pas très handicapant)
		kconf_hierarchie($I,$id_objet,$O);
		$id_parent = $kconf['i'][$I][$O][$id_objet]['parent'];
// 		spip_log("eu besoin de kconf_hierarchie: $id_objet $O: parent: $id_parent");
	} else {
// 		spip_log("pas besoin de kconf_hierarchie: $id_objet $O: parent: $id_parent");
	}

	if (isset($kconf['i'][$I][$O][$id_objet]['clefs'][$clef])) {
		$val = $kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['valeur'];
		$type = $kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['type'];
// 		spip_log("valeur de $clef trouvé dans l'objet: $id_objet, $val, $type");
		unset($id_parent);
	}

	$i=0;
	while (is_int($id_parent)) {
// 		spip_log("valeurs: $clef: $id_objet($id_parent), ".var_export($kconf['i'][$I]['c'][$id_parent]['clefs'][$clef],true));
		if (isset($kconf['i'][$I]['c'][$id_parent]['clefs'][$clef])) {
			$type = $kconf['i'][$I]['c'][$id_parent]['clefs'][$clef]['type'];
			if (($type=='protege' && ++$i==1) || $type=='public') {
				$val = $kconf['i'][$I]['c'][$id_parent]['clefs'][$clef]['valeur'];
				$cascade = $id_parent;
			} else {
				$error = "prive";
				$type = "";
			}
			break;
		}
		if ($id_parent==0) break;
		$id_parent = $kconf['i'][$I]['c'][$id_parent]['parent'];
// 		spip_log("encore un tour pour $id_parent");
	}
	
// 	spip_log("$clef demandé pour $I,$O,$id_objet ($id_parent): $val,$type,$cascade");
	return array($val,$type,$cascade);
}

function kconf_unserialize($k) {
	$k = unserialize($k);
	if (is_array($k)) {
		$types = array(1=>'prive',2=>'protege',3=>'public');
		$K = array();
		foreach($k as $clef => $t) {
			$K[$clef] = array(
				'valeur' => $t[0],
				'type' => $types[$t[1]],
				'defaut' => $t[2]
			);
		}
	}
	return $K;
}

function kconf_serialize($k) {
	$types = array('prive'=>1,'protege'=>2,'public'=>3);
	$K = array();
	foreach($k as $clef => $t) {
		$K[$clef] = array(
			$t['valeur'],
			$types[$t['type']],
			$t['defaut']
		);
	}
	$K = serialize($K);
	return $K;
}

?>