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

	if (!preg_match(",^([-+*])(.*)$,", $arg, $r))
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
	if($mode!="-") {
		if($mode=="+" && in_array($tabella,liste_index_tables())) {
			include_spip('inc/minipres');
			minipres(_L("La tabella ".interdire_scripts($tabella)." &egrave; gi&agrave; indicizzata"));
		} else {
			//recupera descrizione tabella	
			$descr = $tables_principales[$tabella];
			//Elimina chiave primaria
			$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
			$descr = array_diff(array_keys($descr["field"]),$chiavi);
			$campi = array('importanza' => _request("importanza"),'lungh_min' => _request("lungh_min"),'filtri' => _request("filtri"));
			//verifica che tutti i campi siano validi e restituisce 
			if(!indicizza_tabelle_verify_fields($campi,$descr)) {
				include_spip('inc/minipres');
				minipres(_L("Errore nella definizione dei campi"));
			} else {
				if($mode=="+") indicizza_tabelle_add_idx_field($tabella);
				indicizza_tabelle_set_points_fields($tabella,$campi);
			}		
		}
	} else
	if($mode=="-") {
		if(!in_array($tabella,liste_index_tables())) {
			include_spip('inc/minipres');
			minipres(_L("La tabella ".interdire_scripts($tabella)." non &egrave; indicizzata"));
		} else {
			indicizza_tabelle_remove_idx_field($tabella);
			indicizza_tabelle_remove_points_fields($tabella);
		}
	}
}

function indicizza_tabelle_remove_idx_field($tabella) {
		spip_query("ALTER TABLE $tabella DROP COLUMN idx;");
		update_index_tables();		
}

function indicizza_tabelle_verify_fields(&$conf,$descr) {
		$ok = true;
		foreach($conf as $campi) {
			//verifica che $campi sia un array
			if(!is_array($campi)) {
					$ok = false;
					$campi = "Errore";			
			} else
			//Verifica che ogni campo sia effettivamente nella tabella di origine
			foreach($campi as $nome => $val) {
				$nome = corriger_caracteres($nome);
				if(!in_array($nome,$descr)) {
					$ok = false;
					$campi =  "Campo ".interdire_scripts($nome)." inesistente";
					break;
				} else {
					$campi[$nome] = $val;
				}
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
		//preparo la configurazione da salvare 
		$conf = array();
		foreach($campi['importanza'] as $nome => $val) { 
			//elimino campi da non indicizzare
			if(!$val) { 
				unset($campi[$nome]);
			}	else {
				if($campi['lungh_min'][$nome]) $val = array($val,$campi['lungh_min'][$nome]);
				if($campi['filtri'][$nome]) $nome .= "|".$campi['filtri'][$nome];
				$conf[$nome] = $val;
			}
		}
		//memorizzo parametri indicizzazione
		$INDEX_elements_objet[$tabella] = $conf;
		ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
		ecrire_metas();			
		
		//Aggiorna tabella spip_index e meta index_table per indicizzazione
		update_index_tables();
}

function indicizza_tabelle_remove_points_fields($tabella) {
		global $INDEX_elements_objet;

		include_spip('inc/meta');
		unset($INDEX_elements_objet["$tabella"]);
		ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
		ecrire_metas();			
		
		//Aggiorna tabella spip_index e meta index_table per indicizzazione
		update_index_tables();	
}
?>
