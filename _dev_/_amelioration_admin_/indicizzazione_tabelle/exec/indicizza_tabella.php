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

function exec_indicizza_tabella_dist() {
	
global $tables_principales,$INDEX_tables_interdites,$INDEX_elements_objet;
include_spip("base/serial");

$tabelle_standard = $tables_principales;
//carica definizioni da mes_fonctions
include_spip('mes_fonctions');

//carica definizioni tabelle da plugin
if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){ 
 		        include_once(_DIR_SESSIONS."charger_plugins_fonctions.php"); 
} 
	
debut_page(_L('Indicizzazione tabelle esterne'), "administration", "");

debut_gauche();

debut_boite_info();
echo propre(_L('Questa pagina permette di indicizzare una tabella.'));

fin_boite_info();

debut_droite();

gros_titre("Indicizzazione tabelle esterne");


debut_cadre_trait_couleur('','','',"Indicizza tabella");

include_spip("inc_texte");
$ok = true;
$tabella = interdire_scripts(_request("tabella"));
//Controlli di sicurezza
if(!$tabella) {echo "Errore";$ok=false;}
else include_spip("inc/indexation");
if($ok===true && (!array_key_exists($tabella,$tables_principales) || in_array($tabella,$INDEX_tables_interdites) ||
		in_array($tabella,liste_index_tables()) ) ) {
	echo "Impossibile indicizzare tabella $tabella";$ok=false;
}


if($ok===true) {
	
	include_spip("base/abstract_sql");
	$descr = spip_abstract_showtable($tabella);
	//Elimina chiave primaria
	$chiavi = explode(",",$descr["key"]["PRIMARY KEY"]);
	$campi = array_keys($descr["field"]);
	$descr = array_diff($campi,$chiavi);

	if(_request("invia")) {
		//Verifica sicurezza
		$ok = true;
		$campi = _request("campo");
		if(!is_array($campi)) {
				$ok=false;
				echo "Errore";			
		} else
		foreach($campi as $nome => $val) {
			if(!in_array($nome,$descr)) {
				$ok=false;
				echo "Campo ".interdire_scripts($nome)." inesistente";
				break;
			} else {
				$campi[$nome] = intval($val);
				if(!$campi[$nome]) {
					$ok=false;
					echo "Errore";
					break;				
				}
			}
		}
		
		if($ok===true) {
			spip_query("ALTER TABLE $tabella ADD COLUMN idx enum('', '1', 'non', 'oui', 'idx') NOT NULL default '';");
			
			include_spip('inc/meta');
			$INDEX_elements_objet["$tabella"] = $campi;
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			ecrire_metas();			
			
			//Aggiorna tabella spip_index e meta index_table per indicizzazione
			update_index_tables();
			redirige_par_entete(generer_url_ecrire("tabelle_aggiuntive"));
		}

	} else {
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
	}
		
}

fin_cadre_trait_couleur();

fin_page();

}

?>
