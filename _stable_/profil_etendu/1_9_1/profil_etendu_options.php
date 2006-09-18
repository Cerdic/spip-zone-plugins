<?php
//////////////////////////////////////////////////
//PARAMETRAGE
/////////////////////////////////////////////////

//definition des listes de valeurs
$GLOBALS['enum_conf']=array(
	"pays" => array(	
		 "CH" => _T('profil:suisse'),
		 "FR" => _T('profil:france'),
		 "DE" => _T('profil:allemagne') 
	),
	"age" => array(	
		"9_13" => "9-13 ans",
		"13_16" => "13-16 ans",
		"16" => "+ 16 ans"
   ),
   	"domaine" => array(	
		"x" => "X",
		"y" => "Y",
		"z" => "Z"
   )
   
  
);

//liste des tables d'extension du profil
$GLOBALS['champs_etendus']=array(
//Pour chaque table d'extension, liste des champs
'profil_etendu' => array (
"prenom"=> "ligne|brut|"._T('profil:prenom'),
"nom"=> "ligne|brut|"._T('profil:nom'),
"ville"=> "ligne|brut|"._T('profil:ville'),
"pays" => "select|brut|"._T('profil:pays')."|".join(array_values($GLOBALS['enum_conf']["pays"]),",")."|".join(array_keys($GLOBALS['enum_conf']["pays"]),","),

"enseignant" => "checkbox|brut|"._T('profil:enseignant'),
"enseignant_age" => "select|brut|"._T('profil:enseignant_age')."|".join(array_values($GLOBALS['enum_conf']["age"]),",")."|".join(array_keys($GLOBALS['enum_conf']["age"]),","),
"enseignant_ecole"=> "ligne|brut|"._T('profil:enseignant_ecole'),
"enseignant_ville"=> "ligne|brut|"._T('profil:enseignant_ville'),
"enseignant_pays" => "select|brut|"._T('profil:enseignant_pays')."|".join(array_values($GLOBALS['enum_conf']["pays"]),",")."|".join(array_keys($GLOBALS['enum_conf']["pays"]),","),

"eleve" => "checkbox|brut|"._T('profil:eleve'),
"eleve_nele"=> "ligne|brut|"._T('profil:eleve_nele'),
"eleve_ecole"=> "ligne|brut|"._T('profil:eleve_ecole'),
"eleve_ville"=> "ligne|brut|"._T('profil:eleve_ville'),
"eleve_pays" => "select|brut|"._T('profil:eleve_pays')."|".join(array_values($GLOBALS['enum_conf']["pays"]),",")."|".join(array_keys($GLOBALS['enum_conf']["pays"]),","),

"scientifique" => "checkbox|brut|"._T('profil:scientifique'),
"scientifique_domaine" => "select|brut|"._T('profil:scientifique_domaine')."|".join(array_values($GLOBALS['enum_conf']["domaine"]),",")."|".join(array_keys($GLOBALS['enum_conf']["domaine"]),","),
"scientifique_societe"=> "ligne|brut|"._T('profil:scientifique_societe'),
"scientifique_ville"=> "ligne|brut|"._T('profil:scientifique_ville'),
"scientifique_pays" => "select|brut|"._T('profil:scientifique_pays')."|".join(array_values($GLOBALS['enum_conf']["pays"]),",")."|".join(array_keys($GLOBALS['enum_conf']["pays"]),","),
"autre" => "checkbox|brut|"._T('profil:autre'),
"autre_detail"=> "ligne|brut|"._T('profil:autre_detail'),
"autre_ville"=> "ligne|brut|"._T('profil:autre_ville'),
"autre_pays" => "select|brut|"._T('profil:autre_pays')."|".join(array_values($GLOBALS['enum_conf']["pays"]),",")."|".join(array_keys($GLOBALS['enum_conf']["pays"]),",")
));

//////////////////////////////////////////////////
//FIN PARAMETRAGE
/////////////////////////////////////////////////
include_spip('inc/profil_etendu');

foreach($GLOBALS['champs_etendus'] as $type_profil){
	$leschamps=etendu_champs($type_profil);
	$spip_profil_etendu[$type_profil]=array();
	//TODO : gerer les valeurs par defaut

	foreach (array_keys($leschamps) as $lechamp){
		if ((($leschamps[$lechamp]=="radio")||($leschamps[$lechamp]=="select"))&&(is_array($GLOBALS['enum_conf'][$lechamp])))
			$spip_profil_etendu[$type_profil][$leschamps]="ENUM('".join(array_keys($GLOBALS['enum_conf'][$lechamp]),"','")."')";
		elseif ($leschamps[$lechamp]=="bloc")
			$spip_profil_etendu[$type_profil][$leschamps]="TEXT";
		elseif ($leschamps[$lechamp]=="checkbox")
			$spip_profil_etendu[$type_profil][$leschamps]="ENUM('oui','non') NOT NULL default 'non'";
		else 
			$spip_profil_etendu[$type_profil][$leschamps]="varchar(255) default NULL";
	}
	$spip_profil_etendu[$type_profil]['id_auteur']="int(11) NOT NULL default '0'";
	$spip_profil_etendu[$type_profil]['maj']="datetime default NULL";
  
	$spip_profil_etendu_key[$type_profil] = array("PRIMARY KEY" => "id_auteur");

global $tables_principales,$table_primary,$table_des_tables;

	$tables_principales['spip_'.$type_profil] = array('field' => $spip_profil_etendu[$type_profil], 
												'key' => $spip_profil_etendu_key[$type_profil]);
	$table_primary[$type_profil]="id_auteur";
	$table_des_tables[$type_profil]=$type_profil;

}

/*
//normalement plus necessaire
function boucle_PROFIL_ETENDU($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$boucle->from[] =  "spip_profil_etendu AS " . $boucle->type_requete;
	return calculer_boucle($id_boucle, $boucles); 
}
*/

?>