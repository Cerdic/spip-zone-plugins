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

	$tabella = _request("tabella");
	$aggiungi_indice = _request("new"); 
	//Controlli di sicurezza, tabella non specificata
	if(!$tabella) {
		echo indicizza_tabelle_debut_page().
		     _T("indicizzazione:err_nessuna_tabella").
		     indicizza_tabelle_fin_page();
		die();
	}
	$tabella = corriger_caracteres($tabella);


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
			echo indicizza_tabelle_debut_page().
			     _T("indicizzazione:err_tabella_non_trovata",array('tabella' => interdire_scripts($tabella))).
			     indicizza_tabelle_fin_page();
			die();
		}
	}
	
	$res = indicizza_tabelle_debut_page();
	$tabelle = array();
	$tabelle[] = array(
		"<strong>"._T("indicizzazione:nome_campo")."</strong>",
		"<strong>"._T("indicizzazione:importanza")."</strong>",
		"<strong>"._T("indicizzazione:min_lungh")."</strong>",
		"<strong>"._T("indicizzazione:filtri")."</strong>",
	);
			
	//recupera descrizione tabella	
	$descr = $tables_principales[$tabella];	
	//Elimina chiave primaria e campo indice
	$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
	$chiavi[] = 'idx';
	$descr = array_diff(array_keys($descr["field"]),$chiavi);
	//recupera parametri di indicizzazione
	//$config_indice["nome_campo"] = array(importanza oppure array(importanza,lungh_minima),"filtri")
	$config_indice = array();
	if($INDEX_elements_objet[$tabella])
		foreach($INDEX_elements_objet[$tabella] as $nome => $val) {
			$params = explode('|',$nome,2);
			$config_indice[$params[0]] = array('valori' => $val,'filtri' => $params[1]); 
		}
	
	$valore_predefinito = $aggiungi_indice=='oui'?'5':'0';
	
	foreach($descr as $campo) {
		$config_campo = $config_indice[$campo];
		if(is_array($config_campo['valori'])) {
			$importanza = $config_campo['valori'][0];
			$lungh_minima = $config_campo['valori'][1];
		} else if($config_campo['valori']) {
			$importanza = $config_campo['valori'];
			$lungh_minima = 0;
		} else {
			$importanza = $valore_predefinito;
			$lungh_minima = 0;		
		}
		$filtri = $config_campo['filtri'];
		 
		$tabelle[] = array(
			$campo,
			"<input type='text' size='3' name='importanza[$campo]' value='$importanza' />",
			"<input type='text' size='3' name='lungh_min[$campo]' value='$lungh_minima' />",
			"<input type='text' name='filtri[$campo]' value='$filtri' />",
		);
	}
	
	$tabelle = afficher_liste_debut_tableau().afficher_liste(array('40%','20%','20%','20%'),$tabelle).afficher_liste_fin_tableau();

	$tabelle .= "<input type='submit' value='"._T("bouton_valider")."' name='invia' />";
	
	$res .= redirige_action_auteur("indicizza",($aggiungi_indice=='oui'?'+':'*').rawurlencode($tabella),'tabelle_aggiuntive','',$tabelle);
	
	$res .= indicizza_tabelle_fin_page();
			
	echo $res;
	
}

function indicizza_tabelle_debut_page() {
	global $tabella;
	
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	$ret = $commencer_page(_T('indicizzazione:indicizzazione_tabelle'), "administration", "");
	
	$ret .= debut_gauche('',true);
	
	$ret .= debut_boite_info(true);
	$ret .= _T('indicizzazione:info_configurazione');
	
	$ret .= fin_boite_info(true);
	
	$ret .= debut_droite('',true);
	
	$ret .= gros_titre(_T("indicizzazione:indicizzazione_tabelle"),'',false);
	
	$ret .= debut_cadre_trait_couleur('',true,'',_T("indicizzazione:configura_campi",array('tabella' => interdire_scripts($tabella))));
	
	return $ret;
}

function indicizza_tabelle_fin_page() {

	$ret = fin_cadre_trait_couleur(true);
	
	$ret .= fin_page();
	
	return $ret;

}
?>
