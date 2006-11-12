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

function exec_indicizza_tabella_dist() {
	
	global $tables_principales,$tables_auxiliaires,$INDEX_tables_interdites,$INDEX_elements_objet;
	include_spip("base/serial");
	include_spip("base/auxiliaires");
	
	//carica definizioni da mes_fonctions
	include_spip('mes_fonctions');
	
	//carica definizioni tabelle da plugin
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){ 
	 		        include_once(_DIR_SESSIONS."charger_plugins_fonctions.php"); 
	} 

	$tabelle_standard = array_merge($tables_principales,$tables_auxiliaires);

		
	include_spip("inc/texte");
	$ok = true;
	$tabella = interdire_scripts(_request("tabella"));
	//Controlli di sicurezza
	if(!$tabella) {
		indicizza_tabelle_debut_page();
		echo "Errore: tabella non specificata. ";
		indicizza_tabelle_fin_page();
		die();
	}
	$azione = interdire_scripts(_request("action"));
	//Controlli di sicurezza
	if($azione!="indicizza" && $azione!="non_indicizza") {
		indicizza_tabelle_debut_page();
		echo "Errore: azione non specificata. ";
		indicizza_tabelle_fin_page();
		die();
	}	
	
	//Verifiche esistenza definizione tabella e tabelle vietate
	include_spip("inc/indexation");
	if((!array_key_exists($tabella,$tabelle_standard) || in_array($tabella,$INDEX_tables_interdites)) ) {
		indicizza_tabelle_debut_page();
		echo "Impossibile eseguire azione $azione sulla tabella $tabella";
		indicizza_tabelle_fin_page();
		die();
	}
	
	
	if($ok===true) {
		//Verifiche di congruenza azioni
		if($azione=="indicizza" && in_array($tabella,liste_index_tables())) {
			indicizza_tabelle_debut_page();
			echo "La tabella $tabella &egrave; gi&agrave; indicizzata";
			indicizza_tabelle_fin_page();	
			die();
		}
		if($azione=="non_indicizza") {
			if(!in_array($tabella,liste_index_tables())) {
				indicizza_tabelle_debut_page();
				echo "La tabella $tabella non &egrave; indicizzata";
				indicizza_tabelle_fin_page();	
				die();
			} else {
				indicizza_tabelle_remove_idx_field($tabella);
				include_spip('inc/headers'); 
				redirige_par_entete(generer_url_ecrire("tabelle_aggiuntive"));									
			}
		}
		
		
		
		include_spip("base/abstract_sql");
		$descr = spip_abstract_showtable($tabella);
		//Elimina chiave primaria
		$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
		$campi = array_keys($descr["field"]);
		$descr = array_diff($campi,$chiavi);
	
		if(_request("invia") && $azione=="indicizza") {
			switch($azione) {
				case "indicizza":
					if(!indicizza_tabelle_verify_fields($tabella,$campi,$descr)) {
						indicizza_tabelle_debut_page();
						echo $campi;
						indicizza_tabelle_fin_page();
					} else {
						indicizza_tabelle_add_idx_field($tabella);
						indicizza_tabelle_set_points_fields($tabella,$campi);
						include_spip('inc/headers'); 
						redirige_par_entete(generer_url_ecrire("tabelle_aggiuntive"));					
					}
					break;
			}
			
		} else {
			indicizza_tabelle_debut_page();
			$tabelle = array();
			$tabelle[] = array("<strong>Nome Campo</strong>","<strong>Importanza</strong>");
			echo "<form method='POST' action=''><div>";	
			foreach($descr as $campo) {
				$tabelle[] = array($campo,"<input type='text' name='campo[$campo]' value='5' />");
			}
			
			if($tabelle)
				echo afficher_liste_debut_tableau().afficher_liste(array('60%','40%'),$tabelle).afficher_liste_fin_tableau();
		
			echo "<input type='submit' value='Invia' name='invia' />";
			echo "</div></form>";
			indicizza_tabelle_fin_page();
		}
			
	}

}

function indicizza_tabelle_debut_page() {
	include_spip('inc/presentation');

	debut_page(_L('Indicizzazione tabelle esterne'), "administration", "");
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Questa pagina permette di indicizzare una tabella.'));
	
	fin_boite_info();
	
	debut_droite();
	
	gros_titre("Indicizzazione tabelle esterne");
	
	
	debut_cadre_trait_couleur('','','',"Indicizza tabella");

}

function indicizza_tabelle_fin_page() {

	fin_cadre_trait_couleur();
	
	fin_page();

}

function indicizza_tabelle_add_idx_field($tabella) {
		spip_query("ALTER TABLE $tabella ADD COLUMN idx enum('', '1', 'non', 'oui', 'idx') NOT NULL default '';");
		update_index_tables();		
}

function indicizza_tabelle_remove_idx_field($tabella) {
		spip_query("ALTER TABLE $tabella DROP COLUMN idx;");
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

function indicizza_tabelle_verify_fields($tabella,$campi,$descr) {
		//Verifica sicurezza
		$ok = true;
		$campi = _request("campo");
		if(!is_array($campi)) {
				$ok=false;
				$campi = "Errore";			
		} else
		foreach($campi as $nome => $val) {
			if(!in_array($nome,$descr)) {
				$ok=false;
				$campi =  "Campo ".interdire_scripts($nome)." inesistente";
				break;
			} else {
				$campi[$nome] = intval($val);
				if(!$campi[$nome]) {
					$ok=false;
					$campi = "Errore";
					break;				
				}
			}
		}
		return $ok; 		
}
?>
