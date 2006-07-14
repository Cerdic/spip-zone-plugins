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

// Boucle FORMS_CHAMPS
$formschamp_field = array(
		"id_form"	=> "bigint(21) NOT NULL",
		"cle" => "bigint(21) NOT NULL",
		"champ" => "varchar(100)",
		"titre" => "varchar(100)",
		"type" => "varchar(100)",
		"obligatoire" => "varchar(3)",
		"id_groupe" => "bigint(21) NOT NULL",
);
$formschamp_key = array(
	"PRIMARY KEY"	=> "id_form, cle"
);

$GLOBALS['tables_principales']['spip_forms_champs'] =
	array('field' => &$formschamp_field, 'key' => &$formschamp_key);
$GLOBALS['table_des_tables']['forms_champs'] = 'forms_champs';

// Boucle FORMS_CHAMPS_CHOIX
$formschampchoix_field = array(
		"id_form"	=> "bigint(21) NOT NULL",
		"cle" => "bigint(21) NOT NULL",
		"choix" => "varchar(100)",
		"titre" => "varchar(100)"
);
$formschampchoix_key = array(
	"PRIMARY KEY"	=> "id_form, cle, choix",
	"KEY" => "choix"
);

$GLOBALS['tables_principales']['spip_forms_champs_choix'] =
	array('field' => &$formschampchoix_field, 'key' => &$formschampchoix_key);
$GLOBALS['table_des_tables']['forms_champs_choix'] = 'forms_champs_choix';


function forms_creer_tables_temporaires(){
	static $ok=NULL;
	if ($ok==NULL){
		$ok=true;
		$nom = 'spip_forms_champs';
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		/*$query = ''; $keys = ''; $s = ''; $p='';
	
		foreach($cles as $k => $v) {
			$keys .= "$s\n\t\t$k ($v)";
			if ($k == "PRIMARY KEY")
				$p = $v;
			$s = ",";
		}
		$s = '';
		foreach($champs as $k => $v) {
			$query .= "$s\n\t\t$k $v";
			$s = ",";
		}
		spip_query_db("CREATE TEMPORARY TABLE IF NOT EXISTS $nom ($query" . ($keys ? ",$keys" : '') . ")\n");*/
		spip_create_table($nom, $champs, $cles, false, true);
		
		$nom = 'spip_forms_champs_choix';
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		spip_create_table($nom, $champs, $cles, false, true);
		/*$query = ''; $keys = ''; $s = ''; $p='';
	
		foreach($cles as $k => $v) {
			$keys .= "$s\n\t\t$k ($v)";
			if ($k == "PRIMARY KEY")
				$p = $v;
			$s = ",";
		}
		$s = '';
		foreach($champs as $k => $v) {
			$query .= "$s\n\t\t$k $v";
			$s = ",";
		}
		spip_query_db("CREATE TEMPORARY TABLE IF NOT EXISTS $nom ($query" . ($keys ? ",$keys" : '') . ")\n");*/
		
		$res = spip_query("SELECT * FROM spip_forms");
		while ($row=spip_fetch_array($res)){
			$structure = unserialize($row['structure']);
			$id_form=$row[id_form];
			foreach($structure as $cle=>$val){
				$champ = $val['code'];
				$titre = $val['nom'];
				$type = $val['type'];
				$obligatoire = $val['obligatoire'];
				$type_ext = $val['type_ext'];
				$id_groupe= isset($type_ext['id_groupe']) ? $type_ext['id_groupe']:0;
				$obligatoire = $val['obligatoire'];
				spip_query("INSERT INTO spip_forms_champs (id_form,cle,champ,titre,type,obligatoire,id_groupe) VALUES($id_form,$cle,'$champ','$titre','$type','$obligatoire',$id_groupe)");
				if ($type=='select' OR $type=='multiple'){
					foreach($type_ext as $choix=>$titre){
						spip_query("INSERT INTO spip_forms_champs_choix (id_form,cle,choix,titre) VALUES($id_form,$cle,'$choix','$titre')");
					}
				}
			}
		}
	}
}
?>