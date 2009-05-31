<?php
//////////////////////////////////////////////////
//PARAMETRAGE
/////////////////////////////////////////////////
//definition des listes de valeurs
$GLOBALS['enum_conf']=array(
    "profil_type" => array(	
		"_profil_entreprise" => '<:profil:profil_entreprise:>',
		"_profil_association" => '<:profil:profil_association:>',
		"_profil_particulier" => '<:profil:profil_particulier:>'
   )
);

//liste des tables d'extension du profil
$GLOBALS['champs_etendus']=array(
//Pour chaque table d'extension, liste des champs
'profil_etendu' => array (
"nom"=> "ligne|textebrut|"._T('profil:nom'),
"prenom"=> "ligne|textebrut|"._T('profil:prenom'),
"adresse"=> "ligne|textebrut|"._T('profil:adresse'),
"cp"=> "ligne|textebrut|"._T('profil:cp'),
"ville"=> "ligne|textebrut|"._T('profil:ville'),
"pays"=> "ligne|textebrut|"._T('profil:pays'),
"tel"=> "ligne|textebrut|"._T('profil:tel'),
"fax"=> "ligne|textebrut|"._T('profil:fax'),
'profil' => "hidden_form|textebrut|_profil",
'profil_type' => 'radio_form|profil_propremulti|<:profil:profil_type:>|'.join(array_values($GLOBALS['enum_conf']["profil_type"]),",").'|'.join(array_keys($GLOBALS['enum_conf']["profil_type"]),','),

));

//////////////////////////////////////////////////
//FIN PARAMETRAGE
/////////////////////////////////////////////////
include_spip('inc/profil_etendu');

global $tables_principales,$table_primary,$table_des_tables;

foreach($GLOBALS['champs_etendus'] as $type_profil){
	$leschamps=etendu_champs($type_profil);
	$spip_profil_etendu[$type_profil]=array();
	//TODO : gerer les valeurs par defaut

	foreach (array_keys($leschamps) as $lechamp){
		if ((($leschamps[$lechamp]=="radio")||($leschamps[$lechamp]=="select")||($leschamps[$lechamp]=="radio_form"))&&(is_array($GLOBALS['enum_conf'][$lechamp])))
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


	$tables_principales['spip_'.$type_profil] = array('field' => $spip_profil_etendu[$type_profil], 
												'key' => $spip_profil_etendu_key[$type_profil]);
	$table_primary[$type_profil]="id_auteur";
	$table_des_tables[$type_profil]=$type_profil;

}

function profil_multi($texte) {
	$regexp = "|<:([^>]*):>|";
	if (preg_match_all($regexp, $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs)
		$texte = str_replace($regs[0],
		_T($regs[1]), $texte);
	return $texte;
}
function profil_propremulti($texte) {
	return propre(profil_multi($texte));
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