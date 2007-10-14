<?php
// Original From SPIP-Listes-V :: Id: spiplistes_naviguer_paniers.php paladin@quesaco.org

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


/*
	Affiche gauche
	Menu de navigation entre les paniers de courriers ou listes
*/

function spiplistes_naviguer_paniers ($titre, $element, $les_statuts, $return = false) {

	$result = "";
	
	$current_statut = _request('statut');
	
	switch($element) {
		case 'courriers':
			$sql_from = "spip_courriers";
			$script_exec = _SPIPLISTES_EXEC_COURRIERS_LISTE;
			break;
		case 'listes':
			$sql_from = "spip_listes";
			$script_exec = _SPIPLISTES_EXEC_LISTES_LISTE;
			break;
	}
	$sql_query = "SELECT statut,COUNT(id_liste) AS n FROM $sql_from GROUP BY statut";
	$sql_result = spip_query($sql_query);
	if(spip_num_rows($sql_result)) {
		$les_statuts = array_fill_keys(explode(";", $les_statuts), 0);
		while($row = spip_fetch_array($sql_result)) {
			$key = $row['statut'];
			if(array_key_exists($key, $les_statuts)) {
				$les_statuts[$key] = $row['n'];
			}
		}
		foreach($les_statuts as $statut=>$value) {
			if($value && ($current_statut != $statut)) {
				$result .= ""
					. "<li>"
					. icone_horizontale(
						spiplistes_items_get_item('nav_t', $statut).($value ? " <em>($value)</em>" : "")
						, generer_url_ecrire($script_exec, "statut=$statut")
						, spiplistes_items_get_item('icon', $statut)
						,""
						,false
						)
					. "</li>"
					;
			}
		}
	}
	if(!empty($result)) {
		if(!empty($titre)) {
			$titre .= ":";
		}
		$result = ""
			. spiplistes_debut_raccourcis($titre, false, true)
			. "<ul class='verdana2' style='list-style: none;padding:1ex;margin:0;'>"
			. $result
			. "</ul>"
			. spiplistes_fin_raccourcis(true)
			;
	}

	if($return) return($result);
	else echo($result);
}

function spiplistes_naviguer_paniers_listes ($titre = '', $return = false) {

	$result = spiplistes_naviguer_paniers ($titre, 'listes', _SPIPLISTES_LISTES_STATUTS, true);

	if($return) return($result);
	else echo($result);
}

function spiplistes_naviguer_paniers_courriers ($titre = '', $return = false) {
	
	$result = spiplistes_naviguer_paniers ($titre, 'courriers', _SPIPLISTES_COURRIERS_STATUTS, true);
	
	if($return) return($result);
	else echo($result);
}

?>