<?php

/*
 * Fonctions a peu pres identiques a l'API editer liens
 * mais pour des tables spip_xx_yy et non spip_xx_liens.
 *
 * Ce sont les memes fonctions suffixees de _simples
 *
**/

// parametre : $objet_source en plus.
// retour : simplement la table contrairement a la fonction objet_associable() qui retourne un tableau.
function objet_associable_simple($objet_lien, $objet_source) {
	$trouver_table = charger_fonction('trouver_table','base');
	$table = 'spip_' . table_objet($objet_lien) . '_' . table_objet($objet_source);
	return $trouver_table($table) ? $table : false;
}


// mutualisation de code
function objets_liens_simples_where($_id_objets, $ids, $_id_objetl, $idl, $qualif=null) {
	$where=array();
	
	// selection source
	if (is_array($ids)) {
		if (count($ids) == 2 AND $ids[0] == 'NOT') {
			$where[] = sql_in($_id_objets, $ids, 'NOT');
		} else {
			$where[] = sql_in($_id_objets, $ids);
		}
	} else {
		if ($ids != '*') {
			$where[] = "$_id_objets=".sql_quote($ids);
		}
	}
	
	// selection lien
	if (is_array($idl)) {
		if (count($idl) == 2 AND $idl[0] == 'NOT') {
			$where[] = sql_in($_id_objetl, $idl, 'NOT');
		} else {
			$where[] = sql_in($_id_objetl, $idl);
		}
	} else {
		if ($idl != '*') {
			$where[] = "$_id_objetl=".sql_quote($idl);
		}
	}

	if ($qualif) {
		$where = array_merge($where, $qualif);
	}
	return $where;
}

function objet_trouver_liens_simples($objets_source, $objets_lies, $qualif=null) {

	$res = array();
	
	foreach($objets_source as $objets => $ids) {
		foreach($objets_lies as $objetl => $idl) {
			$table_liaison = objet_associable_simple($objets, $objetl);
			if (!$table_liaison) continue;

			$_id_objets = id_table_objet($objets);
			$_id_objetl = id_table_objet($objetl);

			// selection
			$where = objets_liens_simples_where($_id_objets, $ids, $_id_objetl, $idl, $qualif);

			$liens = sql_allfetsel('*', $table_liaison,$where);
			// on ajoute 'organisation' => 12 / 'contact' => 3
			// comme dans l'api editer_liens.
			if ($liens) {
				foreach ($liens as $l) {
					$l[$objets] = $l[$_id_objets];
					$l[$objetl] = $l[$_id_objetl];
					$res[] = $l;
				}
			}
		}
	}
	return $res;
}



function objet_associer_simples($objets_source, $objets_lies, $qualif=null) {

	$ins = 0; // le nombre d'insertions faites
	$echec = false;
	include_spip('action/editer_liens'); // pour lien_propage_date_modif()
	
	foreach($objets_source as $objets => $ids) {
		foreach($objets_lies as $objetl => $idl) {
			$table_liaison = objet_associable_simple($objets, $objetl);
			if (!$table_liaison) continue;

			$_id_objets = id_table_objet($objets);
			$_id_objetl = id_table_objet($objetl);

			// insertions sources
			$inserts = array();

			if (is_array($ids)) {
				if (count($ids) == 2 AND $ids[0] == 'NOT') {
					// on ne peut rien faire de NOT
				} else {
					$inserts = $ids;
				}
			} else {
				// on ne peut rien faire de * 
				if ($ids != '*') {
					$inserts[] = $ids;
				}
			}
			
			// insertions liens
			$insertl = array();
			if (is_array($idl)) {
				if (count($idl) == 2 AND $idl[0] == 'NOT') {
					// on ne peut rien faire de NOT
				} else {
					$insertl = $idl;
				}
			} else {
				// on ne peut rien faire de * 
				if ($idl != '*') {
					$insertl[] = $idl;
				}
			}

			if ($inserts and $insertl) {
				foreach ($inserts as $is) {
					foreach ($insertl as $il) {
						// pipeline pre_edition_lien_simple ?
						if (!sql_getfetsel($_id_objets, $table_liaison, array("$_id_objets=".sql_quote($is), "$_id_objetl=".sql_quote($il)))) {
							$e = sql_insertq($table_liaison, array($_id_objets=>$is, $_id_objetl=>$il));
							if ($e!==false) {
								$ins++;
								lien_propage_date_modif($objets,$ids);
								lien_propage_date_modif($objetl,$idl);
								// pipeline post_edition_lien_simple ?
							} else {
								$echec = true;
							}
						}
					}
				}
			}

		}
	}

	if ($qualif)
		objet_qualifier_liens_simples($objets_source, $objets_lies, $qualif);

	return $echec?true:$ins;
}



function objet_dissocier_simples($objets_source, $objets_lies) {

	$dels = 0;
	$echec = false;
	include_spip('action/editer_liens'); // pour lien_propage_date_modif()

	foreach($objets_source as $objets => $ids) {
		foreach($objets_lies as $objetl => $idl) {
			$table_liaison = objet_associable_simple($objets, $objetl);
			if (!$table_liaison) continue;

			$_id_objets = id_table_objet($objets);
			$_id_objetl = id_table_objet($objetl);

			// selection
			$where = objets_liens_simples_where($_id_objets, $ids, $_id_objetl, $idl);

			$liens = sql_allfetsel("$_id_objets, $_id_objetl", $table_liaison, $where);
			if ($liens) {
				foreach ($liens as $l) {
					// pipeline pre_edition_lien_simple ?
					$e = sql_delete($table_liaison, array("$_id_objets=".$l[$_id_objets], "$_id_objetl=".$l[$_id_objetl]));
					if ($e!==false){
						$dels+=$e;
						lien_propage_date_modif($objets,$l[$_id_objets]);
						lien_propage_date_modif($objetl,$l[$_id_objetl]);
					} else {
						$echec = true;
					}
					// retire ?
					// pipeline post_edition_lien_simple ?
				}
			}
		}
	}
	// pipeline trig_supprimer_objets_lies_simple ?
	
	return $echec?false:$dels;
}



function objet_qualifier_liens_simples($objets_source, $objets_lies, $qualif) {

	$echec = false;
	$nb = 0;
	
	foreach($objets_source as $objets => $ids) {
		foreach($objets_lies as $objetl => $idl) {
			$table_liaison = objet_associable_simple($objets, $objetl);
			if (!$table_liaison) continue;

			$_id_objets = id_table_objet($objets);
			$_id_objetl = id_table_objet($objetl);

			// selection
			$where = objets_liens_simples_where($_id_objets, $ids, $_id_objetl, $idl);

			unset($qualif[$objets]);
			unset($qualif[$_id_objets]);
			unset($qualif[$objetl]);
			unset($qualif[$_id_objetl]);

			$e = sql_updateq($table_liaison, $qualif, $where);
			if ($e !== false) {
				$nb++;
			} else {
				$echec = true;
			}
		}
	}
	
	return $echec ? false : $nb;
}


?>
