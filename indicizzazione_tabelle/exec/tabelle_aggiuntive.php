<?php

/*
 * Indicizzazione tabelle
 * plug-in per l'indicizzazione di tabelle esterne 
 * 
 *
 * Autore : renatoformato@virgilio.it
 * © 2006-2007 - Distribuito sotto licenza GNU/GPL
 *
 */


include_spip('inc/presentation');
include_spip('inc/actions');

function exec_tabelle_aggiuntive_dist() {
	
	global $tables_principales,$tables_auxiliaires,$INDEX_tables_interdites,$INDEX_elements_objet;
	include_spip("base/serial");
	include_spip("base/auxiliaires");
	
	$tabelle_standard = $tables_principales;
	
	//carica definizioni da mes_fonctions
	include_spip('mes_fonctions');
	
	//carica definizioni tabelle da plugin
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){ 
	 		        include_once(_DIR_SESSIONS."charger_plugins_fonctions.php"); 
	} 
	
	$tabelle_definite = $tables_principales;
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	$ret = $commencer_page(_T('indicizzazione:indicizzazione_tabelle'), "administration", "");
	
	$ret .= debut_gauche('',true);
	
	$ret .= debut_boite_info(true);
	
	$ret .= _T('indicizzazione:info_indicizzazione');
	
	$ret .= fin_boite_info(true);
	
	$ret .= debut_droite('',true);
	
	$ret .= gros_titre(_T('indicizzazione:indicizzazione_tabelle'),'',false);
	
	
	$ret .= debut_cadre_trait_couleur('',true,'',_T("indicizzazione:tabelle"));
	
	//Enumera tutte le tabelle in db e non
	include_spip("inc/indexation");
	//Aggiorna tabella spip_index e meta index_table per indicizzazione
	update_index_tables();
	$lista_tabelle_indicizzate = liste_index_tables();
	
	$tabelle_db = spip_query("SHOW TABLES"); 
	$tabelle=array();
	
	$tabelle[]= array("<strong>"._T("indicizzazione:nome_tabella")."</strong>","<strong>"._T("indicizzazione:stato_tabella")."</strong>","<strong>"._T("indicizzazione:azioni")."</strong>");
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
					$idx = "<div style='color:red'>"._T("indicizzazione:tabella_non_definita")."</div>";
					//$azioni ="<a href='#'>definisci</a>";
					$azioni = "";
					break;
				case "vietata":
					$idx = "<div style='color:red'>"._T("indicizzazione:tabella_esclusa")."</div>";
					$azioni ="";
					break;
				case "noind":
					$idx = "<div style='color:red'>"._T("indicizzazione:tabella_non_indicizzata")."</div>";
					//if (!array_key_exists($tab[0],$tabelle_standard)) $azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0]))."'>indicizza</a>";
					$azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0])."&new=oui")."'>"._T("indicizzazione:indicizza")."</a>";
					break;
				case "nocampiind":
					$idx = _T("indicizzazione:nessun_campo_indicizzato");
					$azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0]))."'>"._T("indicizzazione:definisci_campi")."</a>";
					break;
				case "ind":
					$idx = _T("indicizzazione:tabella_indicizzata");
					//if (!array_key_exists($tab[0],$tabelle_standard)) $azioni ="<a href='#'>elimina indice</a>";
					$azioni ="<a href='".generer_url_ecrire("indicizza_tabella","tabella=".rawurlencode($tab[0]))."'>"._T("indicizzazione:modifica_campi")."</a><br />";
					$azioni .="<a href='".redirige_action_auteur("indicizza","-".rawurlencode($tab[0]),"tabelle_aggiuntive")."'>"._T("indicizzazione:elimina_indice")."</a>";
					break;
			}
			
			$tabelle[]= array($tab[0],$idx,$azioni);
		}
	}
	
	$ret .= afficher_liste_debut_tableau().afficher_liste(array('40%','40%','20%'),$tabelle).afficher_liste_fin_tableau();
	
	$ret .= fin_cadre_trait_couleur(true);
	
	$ret .= fin_gauche(); 
	
	$ret .= fin_page();
	
	echo $ret;
	
	}

?>
