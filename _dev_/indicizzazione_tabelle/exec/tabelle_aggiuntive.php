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


include_spip('inc/presentation');
include_spip('inc/actions');

function exec_tabelle_aggiuntive_dist() {
	
	global $tables_principales,$tables_auxiliaires,$INDEX_tables_interdites,$INDEX_elements_objet;
	include_spip("base/serial");
	include_spip("base/auxiliaires");
	
	$tabelle_standard = $tables_principales;//array_merge($tables_principales,$tables_auxiliaires);
	
	//carica definizioni da mes_fonctions
	include_spip('mes_fonctions');
	
	//carica definizioni tabelle da plugin
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){ 
	 		        include_once(_DIR_SESSIONS."charger_plugins_fonctions.php"); 
	} 
	
	$tabelle_definite = $tables_principales;//array_merge($tables_principales,$tables_auxiliaires);
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$ret = $commencer_page(_L('Indicizzazione tabelle esterne'), "administration", "");
	
	$ret .= debut_gauche('',true);
	
	$ret .= debut_boite_info(true);
	$ret .= propre(_L('Questa pagina permette di selezionare le tabelle da indicizzare.'));
	$ret .= _L("<h3>Legenda Stato Tabella</h3>\n".
	     "<ul style='margin:0;padding:10px;'>\n".
	     "<li><strong>Tabella non definita in SPIP</strong>: la tabella non &egrave; utilizzabile in un ciclo e quindi &egrave; inutile indicizzarla</li>\n".
	     "<li><strong>Tabella esclusa dall'indicizzazione</strong>: la tabella non &egrave; indicizzabile</li>\n".
	     "<li><strong>Tabella non indicizzata</strong>: la tabella non &egrave; indicizzata ma &egrave; possibile indicizzarla</li>\n".
	     "<li><strong>Nessun campo indicizzato</strong>: la tabella &egrave; indicizzata ma non &egrave; configurato alcun campo per l'indicizzazione</li>\n".
	     "<li><strong>Tabella indicizzata</strong>: la tabella &egrave; indicizzata regolarmente</li></ul>\n").
	     "<h3>Legenda Azioni</h3>".
	//echo "<ul style='margin:0;padding:10px;'><li><strong>Definisci</strong>: rende la tabella utilizzabile in un ciclo</li>";
	     "<ul style='margin:0;padding:10px;'><li><strong>Indicizza</strong>: Indicizza la tabella</li>".
	     "<li><strong>Modifica campi</strong>: definisce i campi da incidizzare per la tabella</li>".
	     "<li><strong>Elimina indice</strong>: Non indicizza la tabella e ne elimina i dati di indicizzazione</li></ul>";
	
	
	$ret .= fin_boite_info(true);
	
	$ret .= debut_droite('',true);
	
	$ret .= gros_titre(_L("Indicizzazione tabelle esterne"),'',false);
	
	
	$ret .= debut_cadre_trait_couleur('',true,'',_L("Tabelle aggiuntive"));
	
	//Enumera tutte le tabelle in db e non
	include_spip("inc/indexation");
	//Aggiorna tabella spip_index e meta index_table per indicizzazione
	update_index_tables();
	$lista_tabelle_indicizzate = liste_index_tables();
	
	$tabelle_db = spip_query("SHOW TABLES"); 
	$tabelle=array();
	
	$tabelle[]= array("<strong>"._L("Nome tabella")."</strong>","<strong>"._L("Stato tabella")."</strong>","<strong>"._L("Azioni")."</strong>");
	if($tabelle_db) {
		while($tab=spip_fetch_array($tabelle_db,SPIP_BOTH)) {
				$stato = "";
				$idx = "";
				$azioni ="";
				//Non enumerare le tabelle ausiliarie
				if(array_key_exists($tab[0],$tables_auxiliaires)) continue;
				if(!array_key_exists($tab[0],$tabelle_definite)) {
					$stato = "nodef";
				} else
				if(in_array($tab[0],$INDEX_tables_interdites)) {
					$stato = "vietata";
				} else
				if(in_array($tab[0],$lista_tabelle_indicizzate)) {
					if(array_key_exists($tab[0],$INDEX_elements_objet)) {
						$stato ="ind";
					} else {
						$stato = "nocampiind";
					}
				} else {
					$stato = "noind";
				}
	
				switch($stato) {
				case "nodef": 
					$idx = "<div style='color:red'>"._L("tabella non definita in spip")."</div>";
					//$azioni ="<a href='#'>definisci</a>";
					$azioni = "";
					break;
				case "vietata":
					$idx = "<div style='color:red'>"._L("tabella esclusa dall'indicizzazione")."</div>";
					$azioni ="";
					break;
				case "noind":
					$idx = "<div style='color:red'>"._L("tabella non indicizzata")."</div>";
					//if (!array_key_exists($tab[0],$tabelle_standard)) $azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0]))."'>indicizza</a>";
					$azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0]))."'>"._L("indicizza")."</a>";
					break;
				case "nocampiind":
					$idx = _L("nessun campo indicizzato");
					$azioni ="<a href='#'>"._L("aggiungi campi")."</a>";
					break;
				case "ind":
					$idx = _L("tabella indicizzata");
					//if (!array_key_exists($tab[0],$tabelle_standard)) $azioni ="<a href='#'>elimina indice</a>";
					$azioni ="<a href='".redirige_action_auteur("indicizza","-".rawurlencode($tab[0]),"tabelle_aggiuntive")."'>"._L("elimina indice")."</a>";
					break;
			}
			
			$tabelle[]= array($tab[0],$idx,$azioni);
		}
	}
	
	$ret .= afficher_liste_debut_tableau().afficher_liste(array('40%','40%','20%'),$tabelle).afficher_liste_fin_tableau();
	
	$ret .= fin_cadre_trait_couleur(true);
	
	$ret .= fin_page();
	
	echo $ret;
	
	}

?>
