<?php
	
function sympatic_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	// pour pouvoir faires des BOUCLES(SYMPATIC_LISTES)
	$interface['table_des_tables']['sympatic_listes']='sympatic_listes';
	$interface['table_des_tables']['sympatic_abonnes']='sympatic_abonnes';

	$interface['tables_jointures']['spip_sympatic_listes'][] = 'sympatic_abonnes';
	$interface['tables_jointures']['spip_sympatic_abonnes'][] = 'sympatic_listes';
	$interface['tables_jointures']['spip_auteurs'][] = 'sympatic_abonnes';

	return $interface;
}

function sympatic_declarer_tables_principales($tables_principales){
	$sympatic_listes = array(
		"id_liste"		=> "bigint(21) NOT NULL",
		"titre"			=> "text NOT NULL DEFAULT ''",
		"descriptif"	=> "text NOT NULL DEFAULT ''",
		"email_liste"	=> "text NOT NULL DEFAULT ''",
		"email_robot"	=> "text NOT NULL DEFAULT ''"
	);
	
	$sympatic_listes_key = array(
		"PRIMARY KEY"	=> "id_liste"
	);
	
	$tables_principales['spip_sympatic_listes'] = array(
		'field' => &$sympatic_listes,
		'key' => &$sympatic_listes_key
	);

	return $tables_principales;
}

function sympatic_declarer_tables_auxiliaires($tables_auxiliaires){
	$sympatic_abonnes = array(
		"id_liste" 	=> "bigint(21) NOT NULL",
		"id_auteur" 	=> "bigint(21) NOT NULL");
	
	$sympatic_abonnes_key = array(
		"PRIMARY KEY" 	=> "id_liste,id_auteur",
		"KEY id_auteur" => "id_liste");
	
	$tables_auxiliaires['spip_sympatic_abonnes'] = array(
		'field' => &$sympatic_abonnes,
		'key' => &$sympatic_abonnes_key);
	
	return $tables_auxiliaires;
}

?>