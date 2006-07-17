<?php
/*
 * Boucle xml
 * 
 *
 * Auteur :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

// Definition des tables temporaires pour permettre la squeletisation des formulaires
//

// Boucle XML
$xml_field = array(
 		#"`hash`"	=> "BIGINT UNSIGNED NOT NULL", // hash du nom xml
		"id_xml" => "bigint(21) NOT NULL",
		"xml"	=> "varchar(255) default '' NOT NULL",
		"xpath" => "varchar(255) default '' NOT NULL",
		"noeud" => "varchar(100)",
		"texte" => "text NOT NULL",
		"attributs" => "text NOT NULL",
		"id_parent" => "bigint(21) NOT NULL",
		"statut" => "ENUM('', 'noeud', 'feuille') DEFAULT '' NOT NULL"
);
$xml_key = array(
	"PRIMARY KEY"	=> "id_xml",
	"KEY" =>"xml",
	"KEY" => "xpath",
	"KEY" => "noeud"
);

$GLOBALS['tables_principales']['spip_xml'] =
	array('field' => &$xml_field, 'key' => &$xml_key);
$GLOBALS['table_des_tables']['xml'] = 'xml';

function xml_creer_tables_temporaires(){
	static $ok=NULL;
	if ($ok==NULL){
		$ok=true;
		$nom = 'spip_xml';
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		spip_create_table($nom, $champs, $cles, true, true);
		
	}
}

function xml_fill_table($xml_file){
	static $file_done=array(''=>true);
	if (isset($file_done[$xml_file])) return;
	if (lire_fichier($f=find_in_path($xml_file),$contenu)!==false){
		include_spip('inc/plugin');
		$tree = parse_plugin_xml($contenu);
		spip_query("DELETE FROM spip_xml WHERE xml=".spip_abstract_quote($xml_file));
		xml_recurse_parse_to_table($xml_file,'/',0,$tree);
		$file_done[$xml_file]=true;
	}
}

function xml_recurse_parse_to_table(&$file,$xpath,$id_parent,&$subtree){
	if (!is_array($subtree)){
		die('erreur inatendue');
	}
	foreach($subtree as $tag=>$tagoccur){
		$attrs = explode(' ',$tag);
		$noeud = array_shift($attrs);
		$attrs = trim(implode(' ',$attrs));
		$texte = "";
		if ((count($tagoccur)==1) AND !is_array($tagoccur[0])){
			// c'est une feuille
			$texte = $tagoccur[0];
			#spip_query("REPLACE INTO spip_xml "
			#	."(xml,xpath,noeud,texte,attributs,statut) "
			#	."VALUES (".spip_abstract_quote($file).",".spip_abstract_quote($xpath).",".spip_abstract_quote($noeud).",".spip_abstract_quote($texte).",".spip_abstract_quote($attrs).",'feuille')");
			$id = spip_abstract_insert('spip_xml',
				"(xml,xpath,noeud,texte,attributs,id_parent,statut)",
				"(".spip_abstract_quote($file).",".spip_abstract_quote($xpath).",".spip_abstract_quote($noeud).",".spip_abstract_quote($texte).",".spip_abstract_quote($attrs).",$id_parent,'noeud')"
				);
		}
		else{
			// c'est un noeud
			$id = spip_abstract_insert('spip_xml',
				"(xml,xpath,noeud,texte,attributs,id_parent,statut)",
				"(".spip_abstract_quote($file).",".spip_abstract_quote($xpath).",".spip_abstract_quote($noeud).",".spip_abstract_quote($texte).",".spip_abstract_quote($attrs).",$id_parent,'noeud')"
				);
			#spip_query("REPLACE INTO spip_xml "
			#	."(xml,xpath,noeud,texte,attributs,statut) "
			#	."VALUES (".spip_abstract_quote($file).",".spip_abstract_quote($xpath).",".spip_abstract_quote($noeud).",".spip_abstract_quote($texte).",".spip_abstract_quote($attrs).",'noeud')");
			foreach ($tagoccur as $key=>$subsubtree) {
				xml_recurse_parse_to_table($file,"$xpath$noeud/",$id,$subsubtree);
			}
		}
	}
}
?>