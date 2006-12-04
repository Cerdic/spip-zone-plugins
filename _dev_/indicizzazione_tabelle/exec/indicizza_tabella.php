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
	
	global $tables_principales,$INDEX_tables_interdites,$INDEX_elements_objet;
	include_spip("base/serial");
	
	//carica definizioni da mes_fonctions
	include_spip('mes_fonctions');
	
	//carica definizioni tabelle da plugin
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){ 
	 		        include_once(_DIR_SESSIONS."charger_plugins_fonctions.php"); 
	} 

	include_spip("inc/texte");

	$tabella = interdire_scripts(_request("tabella"));
	//Controlli di sicurezza, tabella non specificata
	if(!$tabella) {
		indicizza_tabelle_debut_page();
		echo _L("Errore: tabella non specificata. ");
		indicizza_tabelle_fin_page();
		die();
	}

	//Verifiche esistenza definizione tabella e tabelle vietate
	include_spip("inc/indexation");
	if((!array_key_exists($tabella,$tables_principales) || in_array($tabella,$INDEX_tables_interdites)) ) {
		//Verifica esistenza tabella non definita da SPIP ma in db
		$tabelle_in_db = spip_query("SHOW TABLES");
		$trovata = false;
		while($tab=spip_fetch_array($tabelle_in_db,SPIP_BOTH)) {
			if($tab[0] == $tabella) {
				$trovata = true;
				break;
			}
		}
		if(!$trovata) {
			indicizza_tabelle_debut_page();
			echo _L("Tabella $tabella non trovata");
			indicizza_tabelle_fin_page();
			die();
		}
	}
	
	indicizza_tabelle_debut_page();
	$tabelle = array();
	$tabelle[] = array("<strong>"._L("Nome Campo")."</strong>","<strong>"._L("Importanza")."</strong>");
			
	//recupera descrizione tabella	
	$descr = $tables_principales[$tabella];	
	//Elimina chiave primaria
	$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
	$descr = array_diff(array_keys($descr["field"]),$chiavi);

	foreach($descr as $campo) {
		$tabelle[] = array($campo,"<input type='text' name='campo[$campo]' value='5' />");
	}
	
	$tabelle = afficher_liste_debut_tableau().afficher_liste(array('60%','40%'),$tabelle).afficher_liste_fin_tableau();

	$tabelle .= "<input type='submit' value='Invia' name='invia' />";
	
	echo redirige_action_auteur("indicizza",$tabella,'tabelle_aggiuntive','',$tabelle);
	
	indicizza_tabelle_fin_page();
			
}

function indicizza_tabelle_debut_page() {
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('Indicizzazione tabelle esterne'), "administration", "");
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Questa pagina permette di indicizzare una tabella.'));
	
	fin_boite_info();
	
	debut_droite();
	
	gros_titre(_L("Indicizzazione tabelle esterne"));
	
	
	debut_cadre_trait_couleur('','','',_L("Indicizza tabella"));

}

function indicizza_tabelle_fin_page() {

	fin_cadre_trait_couleur();
	
	echo fin_page();

}
?>
