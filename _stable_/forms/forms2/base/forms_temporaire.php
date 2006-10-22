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
		"titre" => "text",
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
		"choix" => "varchar(100) NOT NULL DEFAULT ''",
		"titre" => "text"
);
$formschampchoix_key = array(
	"PRIMARY KEY"	=> "id_form, cle, choix",
	"KEY" => "choix"
);

$GLOBALS['tables_principales']['spip_forms_champs_choix'] =
	array('field' => &$formschampchoix_field, 'key' => &$formschampchoix_key);
$GLOBALS['table_des_tables']['forms_champs_choix'] = 'forms_champs_choix';


function forms_structure2table($row,$clean=false){
	$id_form=$row[id_form];
	// netoyer la structure precedente en table
	if ($clean){
		spip_query("DELETE FROM spip_forms_champs WHERE id_form=".spip_abstract_quote($id_form));
		spip_query("DELETE FROM spip_forms_champs_choix WHERE id_form=".spip_abstract_quote($id_form));
	}
	
	$structure = unserialize($row['structure']);
	foreach($structure as $cle=>$val){
		$champ = $val['code'];
		$titre = $val['nom'];
		$type = $val['type'];
		$obligatoire = $val['obligatoire'];
		$type_ext = $val['type_ext'];
		$id_groupe= isset($type_ext['id_groupe']) ? $type_ext['id_groupe']:0;
		$obligatoire = $val['obligatoire'];
		spip_query("INSERT INTO spip_forms_champs (id_form,cle,champ,titre,type,obligatoire,id_groupe) 
			VALUES(".spip_abstract_quote($id_form).",".spip_abstract_quote($cle).",".spip_abstract_quote($champ).",".spip_abstract_quote($titre).",".spip_abstract_quote($type).",".spip_abstract_quote($obligatoire).",".spip_abstract_quote($id_groupe).")");
		if ($type=='select' OR $type=='multiple'){
			foreach($type_ext as $choix=>$titre){
				spip_query("INSERT INTO spip_forms_champs_choix (id_form,cle,choix,titre) 
					VALUES(".spip_abstract_quote($id_form).",".spip_abstract_quote($cle).",".spip_abstract_quote($choix).",".spip_abstract_quote($titre).")");
			}
		}
	}
}
function forms_allstructure2table($clean=false){
	$res = spip_query("SELECT * FROM spip_forms");
	while ($row=spip_fetch_array($res))
		forms_structure2table($row,$clean);
}

function forms_creer_tables_temporaires($temporaires=true){
	static $ok=NULL;
	if ($ok==NULL){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		$ok=true;
		$nom = 'spip_forms_champs';
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		spip_create_table($nom, $champs, $cles, false, $temporaires);
		
		$nom = 'spip_forms_champs_choix';
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		spip_create_table($nom, $champs, $cles, false, $temporaires);
		
		forms_allstructure2table($temporaires==false);
	}
}
?>
