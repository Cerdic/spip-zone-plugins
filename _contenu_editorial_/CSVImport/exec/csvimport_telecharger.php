<?php
/*
 * csvimport
 * plug-in d'import csv dans les tables spip
 *
 * Auteur :
 * Cedric MORIN
 * notre-ville.net
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip("inc/csvimport");
include_spip("inc/presentation");

function exec_csvimport_telecharger(){
	global $spip_lang_right;
	$table = _request('table');	
	$retour = _request('retour');
	$delim = _request('delim');

	if (!$retour)
		$retour = generer_url_ecrire('csvimport_tous');
	
	$operations = array();
	
	$titre = "";
	$is_importable = csvimport_table_importable($table,$titre,$operations);
	if (in_array('export',$operations))
	  $csvimport_export_actif = true;
	
	
	if ((!$delim)&&($csvimport_export_actif)){
		$icone = "../"._DIR_PLUGIN_CSVIMPORT."/img_pack/csvimport-24.png";
	
		debut_page($titre, "documents", "csvimport");
		debut_gauche();
	
		echo "<br /><br />\n";
		debut_droite();
	
		debut_cadre_relief($icone);
		gros_titre($titre);
		echo "<br />\n";
		echo _L("Format du fichier téléchargé :");
		echo "<br />\n";
		// Extrait de la table en commençant par les dernieres maj
		echo generer_url_post_ecrire('csvimport_telecharger',"table=$table&retour=$retour");
		echo "<select name='delim'>\n";
		echo "<option value=','>"._L("Format CSV")."</option>\n";
		echo "<option value=';'>"._L("Format CSV pour Excel (séparateur ';')")."</option>\n";
		echo "</select>";
		echo "<br /><br />\n";
		echo "<input type='submit' name='ok' value='Telecharger' />\n";
	
		fin_cadre_relief();
	
	
		//
		// Icones retour
		//
		if ($retour) {
			echo "<br />\n";
			echo "<div align='$spip_lang_right'>";
			icone(_T('icone_retour'), $retour, $icone, "rien.gif");
			echo "</div>\n";
		}
		fin_page();
		exit;
	
	}
	
	$csvimport_tables_auth = csvimport_tables_auth();
	if ($csvimport_export_actif){
		if (isset($csvimport_tables_auth[$table]['field']))
			$tablefield=$csvimport_tables_auth[$table]['field'];
		else
			$tablefield=array_keys($tables_principales[$table]['field']);
	
		//
		// Telechargement du contenu de la table au format CSV
		//
	
		$output = csvimport_csv_ligne($tablefield,$delim);
		//$tablefield = array_flip($tablefield);
	
		$query="SELECT * FROM $table";
		$result = spip_query($query);
		while ($row=spip_fetch_array($result)){
			$ligne=array();
			foreach($tablefield as $key)
			  if (isset($row[$key]))
			    $ligne[]=$row[$key];
				else
				  $ligne[]="";
			$output .= csvimport_csv_ligne($ligne,$delim);
		}
	
		$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));
		$charset = lire_meta('charset');
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.csv");
		//Header("Content-Type: text/plain; charset=$charset");
		Header("Content-Length: ".strlen($output));
		echo $output;
		exit;
	}
	else {
		acces_interdit();
	}
}
?>