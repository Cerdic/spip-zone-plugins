<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & FranÃ§ois de Montlivault
* http://www.plugandspip.com 
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*  
**/

// Boucles SPIP-listes
global $tables_principales;

//Ensuite, donner le format des tables ajoutées. Par exemple :
$tables_principales['spip_asso_adherents']= array(
'field' => array(
 						"id_adherent" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"nom" => "TEXT NOT NULL ",
						"prenom" => "TEXT NOT NULL ",
						"sexe" => "TINYTEXT NOT NULL",
						"fonction" => "TEXT",
						"email" => "TINYTEXT NOT NULL",
						"validite" => "DATE NOT NULL DEFAULT '0000-00-00' ",
						"numero" => "TEXT NOT NULL",
						"rue" => "TEXT NOT NULL ",
						"cp" => "TEXT NOT NULL ",
						"ville" => "TEXT NOT NULL ",
						"telephone" => "TINYTEXT",
						"portable" => "TINYTEXT",
						"montant" => "TEXT NOT NULL",
						"date" => "DATE NOT NULL DEFAULT '0000-00-00'",
						"divers" => "TEXT",
						"remarques" => "TEXT",
						"statut" => "TINYTEXT",
						"vignette" => "TINYTEXT",
						"relance" => "tinyint(4) NOT NULL default '0' ",
						"id_auteur" => "int(11) default NULL",
						"id_asso" => "text NOT NULL",
						"categorie" => "text NOT NULL",
						"naissance" => "date NOT NULL default '0000-00-00'",
						"profession" => "text NOT NULL",
						"societe" => "text NOT NULL",
						"identifiant" => "text NOT NULL",
						"passe" => "text NOT NULL",
						"creation" => "date NOT NULL default '0000-00-00'",
						"maj" => "timestamp(14) NOT NULL",
						"utilisateur1" => "text NOT NULL",
						"utilisateur2" => "text NOT NULL",
						"utilisateur3" => "text NOT NULL",
						"utilisateur4" => "text NOT NULL",
						"secteur"	=> "text NOT NULL",
						"publication"	=> "text NOT NULL",
						),
'key' => array("PRIMARY KEY" => "id_adherent")
);

$tables_principales['spip_asso_dons']= array(
 'field' => array(
						"id_don" 			=> "bigint(21) NOT NULL auto_increment",
						"date_don" 		=> "date NOT NULL default '0000-00-00'",
						"bienfaiteur" 		=> "text NOT NULL",
						"id_adherent" 	=> "int(11) NOT NULL default '0'",
						"argent" 			=> "tinytext",
						"colis" 			=> "text",
						"valeur" 			=> "text NOT NULL",
						"contrepartie" 	=> "tinytext",
						"commentaire" 	=> " text",
						"maj" 				=> "timestamp(14) NOT NULL",
						),
'key' => array("PRIMARY KEY" => "id_don")
);

$tables_principales['spip_asso_ventes']= array(
 'field' => array(
						"id_vente" 		=> "BIGINT(21) AUTO_INCREMENT",
						"article"			=> "TINYTEXT NOT NULL",
						"code"			=> "TEXT NOT NULL",
						"acheteur" 		=> "TINYTEXT NOT NULL",
						"quantite" 		=> "TINYTEXT NOT NULL",
						"date_vente"		 => "DATE NOT NULL DEFAULT '0000-00-00'",
						"date_envoi" 		=> "DATE DEFAULT '0000-00-00'",
						"don" 				=> "TINYTEXT",
						"prix_vente" 		=> "TINYTEXT",
						"frais_envoi" 		=> "float NOT NULL default '0'",
						"commentaire" 	=> "TEXT",
						"maj" 				=> "timestamp(14) NOT NULL",
						),
					
'key' => array("PRIMARY KEY" => "id_vente")
);

$tables_principales['spip_asso_comptes']= array(
'field' => array(
						"id_compte" 	=> "bigint(21) NOT NULL auto_increment",
						"date" 		=> "date default NULL",
						"recette" 		=> "float NOT NULL default '0'",
						"depense"	=> "float NOT NULL default '0'",
						"justification" => "text",
						"imputation" 	=> "text",
						"journal" 		=> "tinytext",
						"id_journal" 	=> "int(11) NOT NULL default '0'",
						"valide"		=> "text NOT NULL", 
						),
					
'key' => array("PRIMARY KEY" => "id_compte")
);

$tables_principales['spip_asso_categories']= array(
 'field' => array(
						"id_categorie" 	=> "int(10) unsigned NOT NULL auto_increment",
						"valeur" 			=> "varchar(30) NOT NULL default",
						"libelle" 			=> "text NOT NULL",
						"duree" 			=> "text NOT NULL",
						"cotisation" 		=> "float NOT NULL default '0'",
						"commentaires" 	=> " text NOT NULL",					
						),
					
'key' => array("PRIMARY KEY" => "id_categorie")	
);
								
$tables_principales['spip_asso_banques']= array(
 'field' => array(
						"id_banque" 	=> "int(11) NOT NULL auto_increment",
						"valeur" 			=> "text NOT NULL",
						"intitule" 			=> "text NOT NULL",
						"reference" 		=> "text NOT NULL",
						"solde" 			=> "float NOT NULL default '0'",
						"date"				=> "date NOT NULL",
						"commentaire" 	=> "text NOT NULL",
						),
					
'key' => array("PRIMARY KEY" => "id_banque")
);	

$tables_principales['spip_asso_activites']= array(
	'field' => array(
					"id_activite"		=> "bigint(20) NOT NULL auto_increment",
					"id_evenement"	=> "bigint(20) NOT NULL",
					"nom"				=> "text NOT NULL",
					"id_adherent"		=> "bigint(20) NOT NULL",
					"membres"		=> "text NOT NULL",
					"non_membres"	=> "text NOT NULL",
					"inscrits"			=> "int(11) NOT NULL default '0'",
					"date"				=> "date NOT NULL default '0000-00-00'",
					"telephone"		=> "text NOT NULL",
					"adresse"			=> "text NOT NULL",
					"email"			=> "text NOT NULL",
					"commentaire"	=> "text NOT NULL",
					"montant"			=> "float NOT NULL default '0'",
					"date_paiement"	=> "date NOT NULL default '0000-00-00'",
					"statut"			=> "text NOT NULL",
					),
					
	'key' => array("PRIMARY KEY" => "id_activite")
);	

//
// <BOUCLE(ADHERENTS)>
//
function boucle_ASSO_ADHERENTS($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_asso_adherents";  
	        return calculer_boucle($id_boucle, $boucles); 
}
//
// <BOUCLE(BIENFAITEURS)>
//
function boucle_ASSO_DONS($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_dons";  
        return calculer_boucle($id_boucle, $boucles);
}
//
// <BOUCLE(VENTES)>
//
function boucle_ASSO_VENTES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_ventes";  
        return calculer_boucle($id_boucle, $boucles);
}
//
// <BOUCLE(BANQUE)>
//
function boucle_ASSO_COMPTES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_comptes";  
        return calculer_boucle($id_boucle, $boucles);
}
//
// <BOUCLE(BANQUES)>
//
function boucle_ASSO_banqueS($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_banques";  
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(CATEGORIES)>
//
function boucle_ASSO_CATEGORIES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_categories";  
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(ACTIVITES)>
//
function boucle_ASSO_ACTIVITES($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_asso_activites";  
        return calculer_boucle($id_boucle, $boucles);
}

function association_auteur_infos($id='', $nom='') {
	if ($id) $query = "SELECT * FROM spip_asso_adherents WHERE id_auteur=$id";
	if ($nom) $query = "SELECT * FROM spip_asso_adherents WHERE nom='$nom'";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$row=serialize($row);
	}
	return $row;
}
	// Page blanche //include_spip('base/association');
	//include_spip('inc/plugin');
function association_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('association.css')).'" />';
		return $flux;
}
function association_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}
	
//Conversion de date
function association_datefr($date) { 
	$split = split('-',$date); 
	$annee = $split[0]; 
	$mois = $split[1]; 
	$jour = $split[2]; 
return $jour.'/'.$mois.'/'.$annee; 
} 

//Affichage du message indiquant la date 
function association_date_du_jour($heure=false) {
	return '<p>'.($heure ? _T('asso:date_du_jour_heure') : _T('asso:date_du_jour')).'</p>';
}

//Creation d'un login
function association_cree_login($nom) {
     $login = strtolower($login);
     $login = ereg_replace("[^a-zA-Z0-9]", "", $login);     
		
     for ($i = 0; ; $i++) {
     	if ($i) $login = $login.$i;
     	else $login = $login;
     	$query = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login'");
     	if (!spip_num_rows($query)) break;
	     }		
	return $login;
  }
?>
