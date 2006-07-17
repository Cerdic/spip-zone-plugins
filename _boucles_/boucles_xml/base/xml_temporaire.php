<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
// Definition des tables temporaires pour permettre la squeletisation des formulaires
//

// Boucle XML
$xml_field = array(
 		#"`hash`"	=> "BIGINT UNSIGNED NOT NULL", // hash du nom xml
		"xml"	=> "text NOT NULL",
		"xpath" => "text NOT NULL",
		#"cle" => "bigint(21) NOT NULL",
		"noeud" => "varchar(100)",
		"texte" => "text NOT NULL",
		"attributs" => "text NOT NULL",
);
$xml_key = array(
	"PRIMARY KEY"	=> "id_form, cle"
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
		spip_create_table($nom, $champs, $cles, false, true);
		
	}
}

function xml_fill_table($xml_file){
	if (lire_fichier($xml_file,$contenu)!==false){
		include_spip('inc/plugin');
		$tree = parse_plugin_xml($contenu);
		xml_recurse_parse_to_table('/',$tree);
	}
}

function xml_recurse_parse_to_table($xpath,$subtree){
	foreach($subtree as $tag=>$tagoccur){
		$attrs = explode(' ',$tag);
		$noeud = array_shift($attrs);
		$attrs = trim(implode(' ',$attrs));
		if ((count($tagoccur)==1) AND !is_array($tagoccur[0])){
			// c'est une feuille
			// insertion sql
		}
		else{
			foreach ($tagoccur as $subsubtree) {
				// c'est un noeud
				// insertion sql
				xml_recurse_parse_to_table("$xpath$noeud/",$subsubtree);
			}
		}
	}
}
?>