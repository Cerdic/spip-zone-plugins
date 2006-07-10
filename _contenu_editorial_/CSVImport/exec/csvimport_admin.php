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
include_spip("base/abstract_sql");
include_spip("base/serial");
include_spip("base/auxiliaires");
include_spip("public/interfaces"); // definition des jointures et autres

function csvimport_admin_action(){
	global $tables_principales;
	global $tables_auxiliaires;
	if (_request('modif')){
		$csvimport_tables_auth = array();
		$exportable = _request("exportable");
		if (count($exportable)){
			$titre = _request("titre");
			$statut=_request("statut");
			$operation=_request("operation");
			$field=_request("field");
			foreach(array_keys($exportable) as $table){
				$csvimport_tables_auth[$table]=array(
					'titre'=>$titre[$table],
					'statut'=>isset($statut[$table])?$statut[$table]:array(),
					'operations'=>isset($operation[$table])?$operation[$table]:array(),
					'field'=>isset($field[$table])?$field[$table]:array(),
					'dyn_declare_aux'=>(!isset($tables_principales[$table])&&!isset($tables_auxiliaires[$table]))
					);
			}
		}
		ecrire_meta('csvimport_tables_auth',serialize($csvimport_tables_auth));
		ecrire_metas();
	}
}

function ligne_table_import($table,$desc){
	static $csvimport_tables_auth=NULL;
	$liste_statuts = array('0minirezo'=>_T('item_administrateur_2'), '1comite'=>_T('intem_redacteur'));
	$liste_operations = array('add' => _L('Ajouter des donn&eacute;es'),'replaceall' =>_L('Tout remplacer'),'export' =>_L('Exporter'));

	if ($csvimport_tables_auth==NULL)
		$csvimport_tables_auth = csvimport_tables_auth();

	
	if (isset($csvimport_tables_auth[$table])){
		$exportable = true;
		$titre = $csvimport_tables_auth[$table]['titre'];
		$statuts = $csvimport_tables_auth[$table]['statut'];
		$operations = $csvimport_tables_auth[$table]['operations'];
		$fields = $csvimport_tables_auth[$table]['field'];
	}
	else{
		$exportable = false;
		$titre = "";
		$statuts = array('0minirezo');
		$operations = array('export');
		$fields = array();
	}

	$vals=array();
	
	// nom de la table dans mysql
	$s = "<input type='checkbox' name='exportable[$table]' value='1' id='exportable_$table'";
	$s .= ($exportable)?" checked='checked'":"";
	$s .= " /> <label for='exportable_$table'>";
	$s .= $table;
	$s .= "</label>";
	$vals[] = $s;

	// Libelle explicite
	$s = "<input type='text' name='titre[$table]' id='titre_$table' class='formo' value='".entites_html($titre)."' size='30' />";
	//$vals[] = $s;
	$s .= "<br />";
	
	// status autorises a manipuler la table
	//$s = "";
	foreach($liste_statuts as $stat=>$lib){
		$s .= "<input type='checkbox' name='statut[$table][]' value='$stat' id='statut_$table_$stat'";
		$s .= (in_array($stat,$statuts))?" checked='checked'":"";
		$s .= " />&nbsp;<label for='statut_$table_$stat'>";
		$s .= str_replace(" ","&nbsp;",$lib);
		$s .= "</label> ";
		//$s .= "<br />";
	}
	$s .= "<hr />";
	//$vals[] = $s;

	
	// operations autorises sur la table
	//$s = "";
	foreach($liste_operations as $op=>$lib){
		$s .= "<input type='checkbox' name='operation[$table][]' value='$op' id='statut_$table_$op'";
		$s .= (in_array($op,$operations))?" checked='checked'":"";
		$s .= " />&nbsp;<label for='statut_$table_$op'>";
		$s .= str_replace(" ","&nbsp;",$lib);
		$s .= "</label> ";
		//$s .= "<br />";
	}
	$s .= "<hr />";
	//$vals[] = $s;
	
	// champs de la table
	//$s = "";
	$s .= "<table>";
	$col=0;
	foreach($desc['field'] as $field=>$type){
		if ($col==0)
			$s .= "<tr>";
		$s.="<td>";
		$s .= "<input type='checkbox' name='field[$table][]' value='$field' id='statut_$table_$field'";
		$s .= (in_array($field,$fields))?" checked='checked'":"";
		$s .= " />&nbsp;<label for='statut_$table_$field'>";
		$s .= $field;
		$s .= "</label>";
		$s.="</td>";
		$col++;
		if ($col==4){
			$s .= "</tr>";
			$col = 0;
		}
		//$s .= "<br />";
	}
	if ($col!=0)
		$s .= "</tr>";
	$s.= "</table>";
	$vals[] = $s;
	

	return $vals;
}

function exec_csvimport_admin(){
	global $connect_statut;
	global $tables_jointures;	
	global $table_prefix;
	global $spip_lang_right;
	$tables_defendues = array('ajax_fonc','meta','ortho_cache','ortho_dico','caches','test');
	
	//
	// Afficher une liste de tables importables
	//
	
	debut_page(_L("Import CSV"), "csvimport", "csvimport");
	debut_gauche();
	
	debut_droite();

	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
	csvimport_admin_action();
	
	//csvimport_afficher_tables(_L("Tables declar&eacute;es pour import"));
	
	$retour = _request('retour');
	if (!$retour)
		$retour = generer_url_ecrire('csvimport_tous');
	$icone = "../"._DIR_PLUGIN_CSVIMPORT."/img_pack/csvimport-24.png";
		
	//
	// Icones retour
	//
	if ($retour) {
		echo "<br />\n";
		echo "<div align='$spip_lang_right'>";
		icone(_T('icone_retour'), $retour, $icone, "rien.gif");
		echo "</div>\n";
	}

	include_spip('base/serial');
	include_spip('base/auxiliaires');
	global $tables_principales;
	global $tables_auxiliaires;
	global $table_des_tables;
	global $tables_jointures;
	// on construit un index des tables de liens
	// pour les ajouter SI les deux tables qu'ils connectent sont sauvegardees
	$tables_for_link = array();
	foreach($tables_jointures as $table=>$liste_relations)
		if (is_array($liste_relations))
		{
			$nom = $table;
			if (!isset($tables_auxiliaires[$nom])&&!isset($tables_principales[$nom]))
				$nom = "spip_$table";
			if (isset($tables_auxiliaires[$nom])||isset($tables_principales[$nom])){
				foreach($liste_relations as $link_table){
					if (isset($tables_auxiliaires[$link_table])/*||isset($tables_principales[$link_table])*/){
						$tables_for_link[$link_table][] = $nom;
					}
					else if (isset($tables_auxiliaires["spip_$link_table"])/*||isset($tables_principales["spip_$link_table"])*/){
						$tables_for_link["spip_$link_table"][] = $nom;
					}
				}
			}
		}
	
	$res = spip_query("SHOW TABLES");
	$liste_des_tables_spip=array();
	$liste_des_tables_autres=array();
	while ($row=spip_fetch_array($res)){
		$table = $row[0];
		// on ne retient que les tables prefixees par spip_
		// evite les melanges sur une base avec plusieurs spip installes
		if (substr($table,0,strlen($table_prefix))==$table_prefix){
			$table_abr = substr($table,strlen($table_prefix)+1);
			if (!isset($tables_for_link["spip_$table_abr"]) && !in_array($table_abr,$tables_defendues)){
				$liste_des_tables_spip[]=$table;
			}
		}
		else {
			$liste_des_tables_autres[] = $table;
		}
	}
	
	echo "<div class='liste'>";
	echo bandeau_titre_boite2(_L("Tables pr&eacute;sentes dans la base"), $icone, $couleur_claire, "black",false);
	echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";

	echo generer_url_post_ecrire('csvimport_admin',"modif=1&retour=".urlencode($retour));
	$num_rows = count($liste_des_tables_spip)+count($liste_des_tables_autres);

	$ifond = 0;
	$premier = true;

	$compteur_liste = 0;
	$tableau = array();
	foreach($liste_des_tables_spip as $table) {
		$desc = spip_abstract_showtable($table);
		if (is_array($desc)){
			$ligne = ligne_table_import($table,$desc);
			$tableau[] = $ligne;
		}
	}
	foreach($liste_des_tables_autres as $table) {
		$desc = spip_abstract_showtable($table);
		if (is_array($desc)){
			$ligne = ligne_table_import($table,$desc);
			$tableau[] = $ligne;
		}
	}

	$largeurs = array('','','','','');
	$styles = array('arial11', 'arial1', 'arial1', 'arial1', 'arial1');
	echo afficher_liste($largeurs, $tableau, $styles);
	echo "</table>";
	echo "</div>\n";
	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Enregistrer' value='"._T('bouton_enregistrer')."' class='fondo'>";
	echo "</div>";
	
	fin_page();
}

?>