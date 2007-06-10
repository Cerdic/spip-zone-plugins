<?php
// patch ACCESGROUPES pour passer de la version 0.61 ou 0.7 à la version 1.0 

// le nécessaire pour récupérer les messages dans le fichier de langue fr
//$GLOBALS = array();
$GLOBALS['idx_lang'] = 'fr';
include('lang/accesgroupes_fr.php');
$Tchaines = $GLOBALS['fr'];

// fct pour mimer la fct de traduction _T() de spip
function _T($ch) {
				 global $Tchaines;
				 if (array_key_exists($ch, $Tchaines)) {
				 		$retour = $Tchaines[$ch];
				 }
				 else {
				 			$retour = str_replace('_', ' ', $ch);
				 }
				 return $retour;
}

$msg_text = '';
$alert = 0;
// debut page 
echo '<html><head><title>'._T('module_titre').'</title><style type="text/css">body {font-family: Arial, Helvetica, sans-serif;} a {color: #000; font-weight: bold;} a:hover {background-color: #ccc;}</style></head><body>';
echo '<div style="width: 100%; text-align: center;"><h2>'._T('titre_patch').'</h2></div>';
echo '<p>'._T('info_patch').'</p><br>';
echo '<div style="width: 50%; margin-left: 20%; background-color: #eee; border: solid 1px #ccc; padding: 20px;">';

// lancement du patch si paramètres connexion MySQL + préfixe tables spip envoyés par le formulaire d'initialisation 
if (isset($_POST['lance_maj']) AND $_POST['host'] != '' AND $_POST['user'] != '' AND isset($_POST['pass']) AND $_POST['base'] != '' AND $_POST['prefixe'] != '') {
  // connexion MySQL
    $host = $_POST['host'];
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$base = $_POST['base']; 						
		$prefixe = $_POST['prefixe'];
		$db = mysql_connect($host, $user, $pass); 
		mysql_select_db($base,$db);
		
	// les noms des tables de la v0.61
		$prefix_tables_SPIP = $prefixe;	
		$prefix_tables_jpk = $prefixe."_jpk";
		$Tjpk_groupes = $prefix_tables_jpk."_groupes";
		$Tjpk_groupes_auteurs = $prefix_tables_jpk."_groupes_auteurs";
		$Tjpk_groupes_acces = $prefix_tables_jpk."_groupes_acces";
		
	// les noms des tables de la v1.0
		$Taccesgroupes_groupes = $prefixe.'_accesgroupes_groupes';
		$Taccesgroupes_acces = $prefixe.'_accesgroupes_acces';
		$Taccesgroupes_auteurs = $prefixe.'_accesgroupes_auteurs';
		
// existe t'il des tables version 0.6 ?
		$sql6880 = "SHOW TABLES LIKE '".$prefix_tables_SPIP."_jpk_groupes'";
		@$result6880 = mysql_query($sql6880);

  // si pas de tables v0.6
		if (! @$data6880 =  mysql_num_rows($result6880) AND mysql_num_rows($result6880) < 1) {
			  $sql6881 = "SHOW TABLES LIKE '".$Taccesgroupes_auteurs."'";
				@$result6881 = mysql_query($sql6881);
			// existe t'il des tables version 0.7 ?
				if (@$data6881 =  mysql_num_rows($result6881) AND mysql_num_rows($result6881) > 0) {
    		// patch pour passer de la v0.7 à la v1.0 : une seule modif dans la table _accesgroupes_auteurs
    			  mysql_query ("ALTER TABLE ".$Taccesgroupes_auteurs."  
    										  CHANGE dde_acces dde_acces BIGINT( 21 ) DEFAULT '0' NOT NULL");
    				if (mysql_error() != '') {
    					 $alerte = 1;
    					 $msg_text .= '<br>'._T('erreur_patch0.7_1.0');
    				}
    				else {
    						 $msg_text .= '<br>'._T('OK_patch1.0');
    				}
				}  // fin patch pour v0.7
		}   
		else {  // si il existe des tables v0.6, lancement du patch pour passer de v0.61 à v1.0
    // étape 1 renommer les tables prefixspip_jpk_xxx en prefixspip_accesgroupe_groupes, prefixspip_accesgroupe_acces, prefixspip_accesgroupe_auteurs  	       
    		 mysql_query("ALTER TABLE ".$prefix_tables_SPIP."_jpk_groupes RENAME ".$Taccesgroupes_groupes);
    		 mysql_query("ALTER TABLE ".$prefix_tables_SPIP."_jpk_groupes_acces RENAME ".$Taccesgroupes_acces);
    		 mysql_query("ALTER TABLE ".$prefix_tables_SPIP."_jpk_groupes_auteurs RENAME ".$Taccesgroupes_auteurs);		
    		 if (mysql_error() != '') {
    		 		$alerte = 1;
    				$msg_text .= '<br>'._T('erreur_patch0.7_etape1');
    		 }
    
    // étape 2 : ajout des champs supplémentaires
      	$sql701 = "SHOW COLUMNS FROM $Taccesgroupes_acces";
    		$result701 = mysql_query($sql701);
        $col_names = array();
      	if ($sql701) {
         		while ($row701 = mysql_fetch_array($result701)) {
         					$col_names[]=$row701[0];
         		}
      	}
        if (!in_array('prive_public', $col_names)) {
      		  mysql_query("ALTER TABLE $Taccesgroupes_acces ADD prive_public smallint(6) NOT NULL");
      		  if (mysql_error() != '') {  
      				 $alerte = 1;
    					 $msg_text .= '<br>'._T('erreur_patch0.7_etape2');
      	 		}
      	}
    		
      	$sql702 = "SHOW COLUMNS FROM $Taccesgroupes_groupes";
    		$result702 = mysql_query($sql702);
        $col_names = array();
      	if ($sql702) {
         		while ($row702 = mysql_fetch_array($result702)) {
         					$col_names[]=$row702[0];
         		}
      	}
        if (!in_array('demande_acces', $col_names)) {
      		  mysql_query("ALTER TABLE $Taccesgroupes_groupes ADD demande_acces tinyint(4) NOT NULL default '0'");
      		  if (mysql_error() != '') {  
      				 $alerte = 1;
    					 $msg_text .= '<br>'._T('erreur_patch0.7_etape2');
      	 		}
      	}
    		
    // étape 3 transformation des champs intitulés id_groupe en id_grpacces et du champ dde_acces de _accesgroupes_auteurs
     	  if (!in_array('id_grpacces', $col_names)) {
     	      mysql_query("ALTER TABLE $Taccesgroupes_groupes CHANGE id_groupe id_grpacces BIGINT( 20 ) NOT NULL AUTO_INCREMENT ");
     	    	mysql_query("ALTER TABLE $Taccesgroupes_acces CHANGE id_groupe id_grpacces BIGINT( 21 ) DEFAULT '0' NOT NULL ");
     	    	mysql_query("ALTER TABLE $Taccesgroupes_auteurs CHANGE id_groupe id_grpacces BIGINT( 21 ) DEFAULT '0' NOT NULL ");
						mysql_query ("ALTER TABLE ".$prefixe."_accesgroupes_auteurs CHANGE dde_acces dde_acces BIGINT( 21 ) DEFAULT '0' NOT NULL");   
     	    	if (mysql_error() != '') {
     	         $alerte = 1;
     	         $msg_text .= '<br>'._T('erreur_patch0.7_etape3');
    					 $msg_text .= mysql_error();
     	    	}
     	  }
				if ($msg_text == '') {
					 $msg_text .= '<br>'._T('OK_patch1.0');
				}
		}  // fin patch pour v0.6
		$couleur = ($alert == 1 ? '#f00' : '#393'); 		
		echo '<p style="color: '.$couleur.'; font-weight: bold;">';
		echo $msg_text;
		echo '</p>';

}  
// fin patch modifiant les tables
// si pas de données envoyées par formulaire : afficher le formulaire
else {  
		 echo "<form action=\"".$_SERVER['PHP_SELF']."\" name=\"form_init\" method=\"post\">";
		 echo "<h3 style=\"margin: 0px 50px;\" >"._T('titre_formulaire_patch')."</h3><br>";
		 echo "\r\n"._T('serveur_SQL')." : <input type=\"text\" name=\"host\" value=\"localhost\"><br>";
		 echo "\r\n"._T('user_SQL')." : <input type=\"text\" name=\"user\" value=\"\"><br>";
		 echo "\r\n"._T('pass_SQL')." : <input type=\"password\" name=\"pass\" value=\"localhost\"><br>";
		 echo "\r\n"._T('base_SQL')." : <input type=\"text\" name=\"base\" value=\"\"><br>";
		 echo "\r\n"._T('prefixe_tables')." : <input type=\"text\" name=\"prefixe\" value=\"spip\"><br>";
		 
		 			 
		 echo "\r\n<br><input type=\"submit\" name=\"lance_maj\" value=\""._T('lancer_patch')."\">";
		 echo "</form>";
}

// fin page
echo '</div></body></html>';


// OLD V 0.7
/*
// existe t'il des tables version 0.6 ?
		$sql6880 = "SHOW TABLES LIKE '".$prefix_tables_SPIP."_jpk_groupes'";
		@$result6880 = mysql_query($sql6880);
// si pas de tables v0.6, créer les tables

		if (! @$data6880 =  spip_num_rows($result6880) AND spip_num_rows($result6880) < 1) {
    		$sql6881 = "SHOW TABLES LIKE '$Tjpk_groupes'";
    		@$result6881 = mysql_query($sql6881);
        if (! @$data6881 =  spip_num_rows($result6881) AND spip_num_rows($result6881) < 1) {
    // création de la table si elle n'existe pas
    			 $sql_create1 = "CREATE TABLE $Tjpk_groupes (
                                    id_grpacces bigint(20) NOT NULL auto_increment,
                                    nom varchar(30) NOT NULL default '',
                                    description varchar(250) default NULL,
                                    actif smallint(1) NOT NULL default '0',
                                    proprio bigint(21) NOT NULL default '0',
    																demande_acces tinyint(4) NOT NULL default '0',
                            PRIMARY KEY  (id_grpacces),
                            UNIQUE KEY nom (nom) )";
    			 @mysql_query($sql_create1);
    			 if (mysql_error() != '') {
    			 			$msg_text .= "<br />".mysql_error()." "._T('creation_table')." ".$Tjpk_groupes."\r\n";
    						$alerte = 1;
    			 }
    			 else {
    					 $msg_text .= "<br />"._T('creation_table')." ".$Tjpk_groupes."\r\n";
    			 }
    		}
    		
    		$sql6882 = "SHOW TABLES LIKE '$Tjpk_groupes_auteurs'";
    		$result6882 = mysql_query($sql6882);
        if (! @$data6882 =  spip_num_rows($result6882) AND spip_num_rows($result6882) < 1) {
    // création de la table si elle n'existe pas
    			 $sql_create2 = "CREATE TABLE $Tjpk_groupes_auteurs (
                                id_grpacces bigint(21) NOT NULL default '0',
                                id_auteur bigint(21) NOT NULL default '0',
                                id_ss_groupe bigint(21) NOT NULL default '0',
                                sp_statut varchar(255) NOT NULL default '',
                                dde_acces smallint(1) NOT NULL default '1',
                                proprio bigint(21) NOT NULL default '0',
                             UNIQUE KEY id_grp (id_grpacces,id_auteur,id_ss_groupe,sp_statut) )";
    			 @mysql_query($sql_create2);
    			 if (mysql_error() != '') {
    					 $msg_text .= "<br />".mysql_error()."  "._T('creation_table')." ".$Tjpk_groupes_auteurs."\r\n";
    					 $alerte = 1;
    			 }
    			 else {
    					 $msg_text .= "<br />"._T('creation_table')." ".$Tjpk_groupes_auteurs."\r\n";
    			 }
    		}
    		
    		
    		$sql6883 = "SHOW TABLES LIKE '$Tjpk_groupes_acces'";
    		$result6883 = mysql_query($sql6883);
        if (! @$data6883 =  spip_num_rows($result6883) AND spip_num_rows($result6883) < 1) {
    // création de la table si elle n'existe pas
    			 $sql_create3 ="CREATE TABLE $Tjpk_groupes_acces (
                                id_grpacces bigint(21) NOT NULL default '0',
                                id_rubrique bigint(21) NOT NULL default '0',
                                id_article bigint(21) default NULL,
                                dtdb date default NULL,
                                dtfn date default NULL,
                                proprio bigint(21) NOT NULL default '0',
    														prive_public SMALLINT(6) NOT NULL default '0',
    														
                              KEY id_grpacces (id_grpacces),
                              KEY id_rubrique (id_rubrique),
                              KEY id_article (id_article) )";
    			@mysql_query($sql_create3);
    			if (mysql_error() != '' ) {
    			 			$msg_text .= "<br />".mysql_error()." "._T('creation_table')." ".$Tjpk_groupes_acces."\r\n";
    						$alerte = 1;
    			 }
    			 else {
    					 $msg_text .= "<br />"._T('creation_table')." ".$Tjpk_groupes_acces."\r\n";
    			 }
    		}
		}
		else {
		
	  }	  
*/
?>