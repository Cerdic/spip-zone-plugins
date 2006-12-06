<?php

/*
 * Indicizzazione tabelle
 * plug-in per l'indicizzazione di tabelle esterne 
 * 
 *
 * Autore : renatoformato@virgilio.it
 * © 2006 - Distribuito sotto licenza GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_indicizza() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$arg = _request('arg');

	if (!preg_match(",^(-?)(.*)$,", $arg, $r))
		spip_log("action_indicizza $arg non corretta");
	else indicizza_tabella($r[2],$r[1]);
	
}

function indicizza_tabella($tabella,$mode='') {
	global $tables_principales;
	
	spip_log("azione indicizza_tabella $tabella $mode");
	include_spip("inc/indexation");
	include_spip("inc/texte");

	$tabella = corriger_caracteres($tabella);
	//Verifiche di congruenza azioni
	if(!$mode) {
		if(in_array($tabella,liste_index_tables())) {
			include_spip('inc/minipres');
			minipres(_L("La tabella $tabella &egrave; gi&agrave; indicizzata"));
		} else {
			//recupera descrizione tabella	
			$descr = $tables_principales[$tabella];
			//Elimina chiave primaria
			$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
			$descr = array_diff(array_keys($descr["field"]),$chiavi);
			$campi = _request("campo");
			//verifica che tutti i campi siano validi e restituisce 
			if(!indicizza_tabelle_verify_fields($tabella,$campi,$descr)) {
				include_spip('inc/minipres');
				minipres(_L("$campi"));
			} else {
				indicizza_tabelle_add_idx_field($tabella);
				indicizza_tabelle_set_points_fields($tabella,$campi);
			}		
		}
	} else
	if($mode=="-") {
		if(!in_array($tabella,liste_index_tables())) {
			include_spip('inc/minipres');
			minipres(_L("La tabella $tabella non &egrave; indicizzata"));
		} else {
			indicizza_tabelle_remove_idx_field($tabella);
		}
	}
}

function indicizza_tabelle_remove_idx_field($tabella) {
		spip_query("ALTER TABLE $tabella DROP COLUMN idx;");
		update_index_tables();		
}

function indicizza_tabelle_verify_fields($tabella,&$campi,$descr) {
		$ok = true;
		//verifica che $campi sia un array
		if(!is_array($campi)) {
				$ok = false;
				$campi = "Errore";			
		} else
		//Verifica che ogni campo sia effettivamente nella tabella di origine
		foreach($campi as $nome => $val) {
			nome = corriger_caracteres($nome);
			if(!in_array($nome,$descr)) {
				$ok = false;
				$campi =  "Campo ".interdire_scripts($nome)." inesistente";
				break;
			} else {
				//evita sql injections
				$campi[$nome] = intval($val);
			}
		}
		return $ok; 		
}

function indicizza_tabelle_add_idx_field($tabella) {
		spip_query("ALTER TABLE $tabella ADD COLUMN idx enum('', '1', 'non', 'oui', 'idx') NOT NULL default '';");
		update_index_tables();		
}

function indicizza_tabelle_set_points_fields($tabella,$campi) {
		global $INDEX_elements_objet;

		include_spip('inc/meta');
		$INDEX_elements_objet["$tabella"] = $campi;
		ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
		ecrire_metas();			
		
		//Aggiorna tabella spip_index e meta index_table per indicizzazione
		update_index_tables();
}
?>
