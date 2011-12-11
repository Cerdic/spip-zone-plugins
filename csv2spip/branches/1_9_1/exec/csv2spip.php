<?  
/* csv2spip est un plugin pour créer/modifier les visiteurs, rédacteurs et administrateurs restreints d'un SPIP à partir de fichiers CSV
*	 					VERSION : 3.0 => plugin pour spip 1.9
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/

//include_spip('base/db_mysql');
//include_spip('base/abstract_sql');


function csv2spip_crypt_md5($input) {			
					$hash = md5($input);
					$md5_pass = $hash;		
          return ($md5_pass);
}


function exec_csv2spip() {
	 			 include_spip("inc/presentation");
						 
			// définir comme constante le chemin du répertoire du plugin
         $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
         $pp = explode("/", end($p));
         define('_DIR_PLUGIN_CSV2SPIP',(_DIR_PLUGINS.$pp[0]));

      // vérifier les droits
         global $connect_statut;
      	 global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
      		  debut_page(_T('titre'), "csv2spip", "plugin");
      		  echo _T('avis_non_acces_page');
      		  fin_page();
      		  exit;
      	 }
			// trouver la version en cours à partir de plugin.xml
				 $Tlecture_fich_plugin = file(_DIR_PLUGIN_CSV2SPIP.'/plugin.xml');
				 $stop_prochain = 0;
				 foreach ($Tlecture_fich_plugin as $ligne) {
								 if ($stop_prochain == 1) {
									  $version_script = $ligne;
									  break;
								 }
								 if (substr_count($ligne, '<version>') > 0) {
									  $stop_prochain = 1;
								 }
				 }
				 
			// le plugin acces_groupes est il installé/activé ?
					 $plugin_accesgroupes = 0;
					 $sql11 = spip_query("SELECT valeur FROM spip_meta WHERE nom = 'plugin' LIMIT 1");
    			 $result11 = spip_fetch_array($sql11);
    			 $ch_meta = $result11['valeur'];
    			 $Tch_meta = explode(',', $ch_meta);
					 if (in_array('acces_groupes', $Tch_meta)) {			
/*
    		// version compatible >= 1.9.2... nettement plus sure : on teste la présence de la constante chemin_du_plugin 
				// et non pas le nom du dossier de plugin stocké dans spip_meta
					 if (defined(_DIR_PLUGIN_ACCESGROUPES)) {					 		 
*/
							 $plugin_accesgroupes = 1;
					 }			
				 
			// début affichage
         debut_page(_T('csvspip:csv2spip'));
         echo "\r\n<style type=\"text/css\">				 
        			\r\n.Cerreur {
        					background-color: #f33;
        					display: block;
        					padding: 10px;
        			}
        			\r\n.Cok {
        			    width: 47%;
        					background-color: #ddd;
        					display: block;
        					padding: 10px;
        			}
					    \r\n</style>";
         echo "<br />";
         gros_titre(_T('csvspip:titre_page'));
         debut_gauche();
         debut_boite_info();
         echo "<strong>"._T('csvspip:titre_info')."</strong><br /><br />";
				 echo "\r\n"._T('csvspip:help_info');
				 echo "<br /><br /><strong>"._T('csvspip:version')."</strong>".$version_script;
         fin_boite_info();


				 
				 
// TRAITEMENT DES DONNEES ENVOYEES PAR LE FORMULAIRE DE SAISIE

// Etape 0 : définition des noms de tables SPIP
		$Trubriques = 'spip_rubriques';
		$Tauteurs = 'spip_auteurs';
		$Tauteurs_rubriques = 'spip_auteurs_rubriques';
		$Tarticles =  'spip_articles';
		$Tauteurs_articles = 'spip_auteurs_articles';
		$Taccesgroupes_groupes = 'spip_accesgroupes_groupes';
		$Taccesgroupes_auteurs = 'spip_accesgroupes_auteurs';
		
		$err_total = 0;

// étape 1 : téléchargement du fichier sur le serveur		
    if ($_FILES['userfile']['name'] != '') {  
				debut_raccourcis();			 
			 	echo "<a href=\"".$PHP_SELF."?exec=csv2spip\"><img src=\"img_pack/cal-today.gif\"> "._T('csvspip:retour_saisie')."</a>";
				fin_raccourcis();
		}				 
		debut_droite();
    if ($_FILES['userfile']['name'] != '') {  
//        debut_cadre_trait_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:resultat_fichier').$_FILES['userfile']['name']);       
								
 		 		debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape1'));
//				echo "<h2>"._T('csvspip:titre_etape1')."</h2>";
				if ($_FILES['userfile']['error'] != 0) { 
				 		echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape1.1_debut').$_FILES['userfile']['tmp_name']._T('csvspip:err_etape1.1_fin').$_FILES['userfile']['error']."</span>";				 							 
  	 				fin_cadre_couleur();
						exit();
			 	} 
     		$nom_fich = "data/tmp_fich.csv";	
    	 	if (!move_uploaded_file($_FILES['userfile']['tmp_name'], "$nom_fich")) {  
					 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape1.2_debut').$_FILES['userfile']['tmp_name']._T('csvspip:err_etape1.2_fin').$nom_fich."</span>";
		    	 exit();
		   	}
    	 	$tmp_csv_slh = addslashes($nom_fich);	
				echo "<br>"._T('csvspip:ok_etape1').$_FILES['userfile']['name']."<br>";
				fin_cadre_couleur();
				

// étape 2 : passage des données du fichier dans la base temporaire			
    		debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape2'));
//				echo "<h2>"._T('csvspip:titre_etape2')."</h2>";
				spip_query("DROP TABLE IF EXISTS tmp_auteurs");
    		if (!spip_query("CREATE TABLE tmp_auteurs (
					 											id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
																nom TEXT NOT NULL, 
																prenom TEXT NOT NULL, 
																groupe TEXT NOT NULL, 
																ss_groupe TEXT NOT NULL, 
																mdp TEXT NOT NULL, 
																pseudo_spip TEXT NOT NULL, 
																mel TEXT NOT NULL, 
																id_spip INT(11) NOT NULL)"
												) ) {  
					 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape2.1')."</span>";
					 fin_cadre_couleur();
	 	    	 exit();
				}
    		else {
    			 		echo "<br>"._T('csvspip:ok_etape2.1')."<br>";
    		}
				
  			$ok = 0;
  			$Tlignes = file($nom_fich);
//print '<br>Tlignes départ = ';
//print_r($Tlignes);				
  			$Terr_sql_temp = array();
				$ligne1 = 0;
  			foreach ($Tlignes as $l) {
								 $l = str_replace('"', '', $l);
  					 		 $Tuser_ec = explode(';', $l);
							// traiter la première ligne pour récupérer les noms des champs dc la position des colonnes
								 if ($ligne1 == 0) {
								 		$Tchamps = array();
										$Tref_champs = array('login', 'prenom', 'pass', 'groupe', 'ss_groupe', 'pseudo_spip', 'email');
										foreach ($Tuser_ec as $champ_ec) {
														$champ_ec = strtolower(trim($champ_ec));
														if (in_array($champ_ec, $Tref_champs)) {
															 $Tchamps[] = $champ_ec;
														}
										}
//echo '<br><br>$Tchamps = ';
//print_r($Tchamps);					
										if (count($Tchamps) < 7) {
											 $Tvides = array();
											 foreach ($Tref_champs as $cref) {
											 				 if (!in_array($cref, $Tchamps)) {
															 		$Tvides[] = $cref;
															 }
											 }  
											 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape2.1');
									     foreach ($Tvides as $cec) {
														   echo " $cec ";
										   } 
											 echo "</span>";
										   exit;
										}
								 }								 
								 for ($i = 0; $i < 7; $i ++) {
								 		 $var = $Tchamps[$i].'_ec';
										 $$var = trim($Tuser_ec[$i]);
//echo "<br>$$Tchamps[$i].'_ec' = $Tuser_ec[$i]";
								 } 
								 if ($pass_ec == '') {
								 		$pass_ec = $login_ec;
								 }
							// ne pas intégrer la première ligne comme un utilisateur
								 if ($ligne1 == 0) {
								 		$ligne1 = 1;
								 }
								 else {
						 // passage des lignes du fichier dans la table tmp_auteurs
								 spip_query("INSERT INTO tmp_auteurs (id, nom, prenom, groupe, ss_groupe, mdp, mel, pseudo_spip) 
								 							VALUES ('', '$login_ec', '$prenom_ec', '$groupe_ec', '$ss_groupe_ec', '$pass_ec', '$email_ec', '$pseudo_spip_ec')");
								 }
      	 				 if (mysql_error() != '') {
      					 		$Terr_sql_temp[] = array('nom' => $nom_ec, 'erreur' => mysql_error());
      					 }
  			}
  			if (count($Terr_sql_tmp) > 0) { 
					 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape2.2');
					 foreach ($Terr_sql_temp as $e) {
										echo "<br>"._T('csvspip:utilisateur').$e['nom']._T('csvspip:erreur').$e['erreur'];
					 }  
					 echo "</span>";
		 			 $err_total ++;
			 	}			
			 	else {
			 			 echo "<br>"._T('csvspip:ok_etape2.2')."<br>";
			 	}
				fin_cadre_couleur();
			
// étape 3 : si nécessaire création des rubriques pour les admins restreints et des groupes pour accesgroupes
				$_POST['groupe_admins'] != '' ? $groupe_admins = strtolower($_POST['groupe_admins']) : $groupe_admins = '-1';
  			$_POST['groupe_visits'] != '' ? $groupe_visits = strtolower($_POST['groupe_visits']) : $groupe_visits = '-1';
				$_POST['groupe_redacs'] != '' ? $groupe_redacs = strtolower($_POST['groupe_redacs']) : $groupe_redacs = '-1';
				debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape3'));
				
	 // étape 3.1 : création des rubriques pour les admins restreints
	 		 	if ($_POST['rub_prof'] == 1 AND $groupe_admins != '-1') {
					 $Terr_rub = array();
					 $Tres_rub = array();
					 $date_rub_ec = date("Y-m-j H:i:s");
					 $Tch_rub = explode(',', $_POST['rub_parent']);
					 $rubrique_parent = $Tch_rub[0];
					 $secteur = $Tch_rub[1];
					 $sql8 = spip_query("SELECT ss_groupe FROM tmp_auteurs WHERE LOWER(groupe) = '$groupe_admins' AND ss_groupe != '' GROUP BY ss_groupe");
					 if (isset($sql8)) {
    					 while ($data8 = spip_fetch_array($sql8)) {
    					 			 $rubrique_ec = $data8['ss_groupe']; 
//										 if (trim($rubrique_ec) != '') {
        								 $sql7 = spip_query("SELECT COUNT(*) AS rub_existe FROM $Trubriques WHERE titre = '$rubrique_ec' LIMIT 1");
        								 $data7 = spip_fetch_array($sql7);
        								 if ($data7['rub_existe'] > 0) {
//print '<br>etape3 : rubrique '.$rubrique_ec.' existe';
        								 		continue;
        								 }
        								 spip_query("INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$rubrique_parent', '$rubrique_ec', '$secteur', 'publie', '$date_rub_ec')" );
          			 				 if (mysql_error() != '') {
          							 		$Terr_rub[] = array('ss_groupe' => $rubrique_ec, 'erreur' => mysql_error());
          							 }
    										 else {
    										 			$Tres_rub[] = $rubrique_ec;
    										 }
//										 }
    					 }
					 }		
  				 if (count($Terr_rub) > 0) {  
  				 		echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3.1');
	  				  foreach ($Terr_rub as $er) {
  										echo "<br>"._T('csvspip:rubrique').$er['ss_groupe']._T('csvspip:erreur').$er['erreur'];
  						}
  						echo "</span>";
	  	        $err_total ++;
					 }			
  			 	 else {
  			 				echo "<br>"._T('csvspip:ok_etape3.1_debut').count($Tres_rub)._T('csvspip:ok_etape3.1_fin')."<br>";
  			   }
				}
		// gestion de la rubrique par défaut des admins restreints
				if ($groupe_admins != '-1') {
					// faut-il créer la rubrique par défaut?
					  $cree_rub_adm_defaut = 0;
						if ($_POST['rub_prof'] == 0) {
							 $sql20 = spip_query("SELECT COUNT(*) AS nb_admins FROM tmp_auteurs WHERE LOWER(groupe) = '$groupe_admins'");
							 $rows20 = spip_fetch_array($sql20);
							 if ($rows20['nb_admins'] > 0) {
							 		$cree_rub_adm_defaut = 1;
							 }							 
						}
						else {
    						$sql19 = spip_query("SELECT COUNT(*) AS nb_sans_ssgrpe FROM tmp_auteurs WHERE LOWER(groupe) = '$groupe_admins' AND ss_groupe = ''");
    						$rows19 = spip_fetch_array($sql19);
    						if ($rows19['nb_sans_ssgrpe'] > 0) {
    							 $cree_rub_adm_defaut = 1;
    						}
						}
					// création de la rubrique par défaut
						if ($cree_rub_adm_defaut == 1) {
    					 $date_rub_defaut = date("Y-m-j H:i:s");
    					 $Tch_rub_defaut = explode(',', $_POST['rub_parent_admin_defaut']);
    					 $rubrique_parent_defaut = $Tch_rub_defaut[0];
    					 $secteur_defaut = $Tch_rub_defaut[1];
  			 			 $rubrique_defaut = ($_POST['rub_admin_defaut'] != '' ? $_POST['rub_admin_defaut'] : _T('csvspip:nom_rub_admin_defaut') ); 
  						 $sq21 = spip_query("SELECT COUNT(*) AS rub_existe FROM $Trubriques WHERE titre = '$rubrique_defaut' LIMIT 1");
  						 $rows21 = spip_fetch_array($sq21);
  						 if ($rows21['rub_existe'] < 1) {
  						 		spip_query("INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) 
														  VALUES ('', '$rubrique_parent_defaut', '$rubrique_defaut', '$secteur_defaut', 'prive', '$date_rub_defaut')" );
  			 				  if (mysql_error() != '') {
  							 		 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_cree_rub_defaut').mysql_error()."</span>";
										 $err_total ++;
  							  }
        				  else {
        				 			 echo "<br>"._T('csvspip:ok_cree_rub_defaut').$rubrique_defaut."<br />";
											 $id_rub_admin_defaut = mysql_insert_id();
        				  }
							 }
							 
						}
				}

	 // étape 3.2 : création des groupes pour le plugin acces_groupes				
				$_POST['ss_groupes_redac'] == 1 ? $ss_groupes_redac = 1 : $ss_groupes_redac = 0;
				$_POST['ss_groupes_admin'] == 1 ? $ss_groupes_admin = 1 : $ss_groupes_admin = 0;
				$_POST['ss_groupes_visit'] == 1 ? $ss_groupes_visit = 1 : $ss_groupes_visit = 0;
				if ($ss_groupes_redac == 1 OR $ss_groupes_admin == 1 OR $ss_groupes_visit == 1) {
     		// si le plugin acces_groupes est activé
					 if ($plugin_accesgroupes == 1) {					 		 
							 $Terr_acces_groupes = array();
							 $Tres_acces_groupes = array();
							 $Tgroupes_accesgroupes = array();
							 $Tres_vider_aceesgroupes = array();
							 $Terr_vider_aceesgroupes = array();
    					 $date_grpe_ec = date("Y-m-j H:i:s");							 
							 $sql_sup = '';
							 $sql_liaison = " WHERE ";
							 $ss_groupes_admin != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_admins'" : $sql_sup .= "";
							 $sql_sup != '' ? $sql_liaison = " AND " : $sql_liaison = " WHERE ";
							 $ss_groupes_visit != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_visits'" : $sql_sup .= "";
							 $sql_sup != '' ? $sql_liaison = " AND " : $sql_liaison = " WHERE ";
							 $ss_groupes_redac != 1 ? $sql_sup .= $sql_liaison." LOWER(groupe) != '$groupe_redacs'" : $sql_sup .= "";
//echo '<br>$ch_sql = '."SELECT ss_groupe FROM tmp_auteurs ".$sql_sup." GROUP BY ss_groupe";							 
    					 $sql18= spip_query("SELECT ss_groupe FROM tmp_auteurs ".$sql_sup." GROUP BY ss_groupe");
//echo '<br>mysql_error $sql18 = '.mysql_error();							 
    					 while ($data18 = spip_fetch_array($sql18)) {
    					 		// créer les sous-groupes
										 if ($data18['ss_groupe'] != '') {
    										 $grpe_ec = $data18['ss_groupe']; 				
//echo '<br>$grpe_ec = _'.$grpe_ec.'_';
        								 $sql17 = spip_query("SELECT id_grpacces FROM $Taccesgroupes_groupes WHERE nom = '$grpe_ec' LIMIT 1");
    //echo '<br>mysql_error $sql17 = '.mysql_error();											 
        							// le groupe existe déja
    										 if (spip_num_rows($sql17) > 0) {
        								 	// stocker l'id_grpacces du groupe dans $Tgrpes_accesgroupes[$nom_ss-grpe]
    												$data17 = spip_fetch_array($sql17);
    												$Tgroupes_accesgroupes[$grpe_ec] = $data17['id_grpacces'];
    										 // si nécessaire vider le groupe de ses utilisateurs
    										    if ($_POST['ss_grpes_reinitialiser'] == 1) {
    													 $id_grpacces_asupr = $data17['id_grpacces'];
    													 spip_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_grpacces = $id_grpacces_asupr");
    													 if (mysql_error() != '') {
    													 		$Terr_vider_accesgroupes[] = array('ss_groupe' => $grpe_ec, 'erreur' => mysql_error());
    													 }
    													 else {
    													 			$Tres_vider_accesgroupes[] = $id_grpacces_asupr;
    													 }
    												}
    												continue;
        								 }
    										 $desc_grpe_csv2spip = _T('csvspip:grpe_csv2spip');
        								 spip_query("INSERT INTO $Taccesgroupes_groupes (id_grpacces, nom, description, actif, proprio, demande_acces) 
    										 						 VALUES ('', '$grpe_ec', '$desc_grpe_csv2spip', 1, 0, 0)" );
          			 				 $id_grpacces_new = mysql_insert_id();
    										 if (mysql_error() != '') {
          							 		$Terr_acces_groupes[] = array('ss_groupe' => $grpe_ec, 'erreur' => mysql_error());
    												$err_total ++;
          							 }
    										 else {
    										 	 // stocker l'id_grpacces du groupe dans $Tgrpes_accesgroupes[$nom_ss-grpe]
    													$Tgroupes_accesgroupes[$grpe_ec] = $id_grpacces_new;
    													$Tres_acces_groupes[] = $grpe_ec;
    										 }
										 }
    					 }
							 echo "<br />"._T('csvspip:etape3.2')."<br />";
							 if (count($Terr_vider_accesgroupes) > 0 OR count($Terr_acces_groupes) > 0) {
      				 		echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3.2');
							 }
      				 if (count($Terr_vider_accesgroupes) > 0) {
    	  				  foreach ($Terr_vider_accesgroupes as $Ver) {
													echo "<br />"._T('csvspip:err_vider_accesgroupes').$Ver['ss_groupe']._T('csvspip:erreur').$Ver['erreur'];
									}
							 }
							 else {
							 			echo "<br />"._T('csvspip:ok_vider_accesgroupes').count($Tres_vider_accesgroupes)._T('csvspip:groupe')."<br />";
							 }
							 if (count($Terr_acces_groupes) > 0) {  
									echo "<br />";
									foreach ($Terr_acces_groupes as $Ger) {
      										echo "<br />"._T('csvspip:groupe_').$Ger['ss_groupe']._T('csvspip:erreur').$Ger['erreur'];
      						}
      						echo "</span>";
    	  	        $err_total ++;
    					 }			
      			 	 else {
      			 				echo "<br />"._T('csvspip:ok_etape3.2_debut').count($Tres_acces_groupes)._T('csvspip:ok_etape3.2_fin')."<br>";
      			   }
							 if (count($Terr_vider_accesgroupes) > 0 OR count($Terr_acces_groupes) > 0) {
      				 		echo "</span>";
							 }
					 }
					 else {   // plugin acces_groupes inactif et $_POST['acces_groupes'] == 1 (en principe pas possible...)
					 		echo "<br /><span class=\"Cerreur\">"._T('csvspip:abs_acces_groupes')."</span><br />"; 
							$err_total ++;
					 }
				}
				fin_cadre_couleur();
				
// étape 4 : intégration des rédacteurs, des visiteurs et des administrateurs							
		// redacteurs
				$Tres_nvx = array();
  			$Terr_nvx = array();
  			$Tres_maj = array();
  			$Terr_maj = array();
  			$Tres_eff = array();
  			$Terr_eff = array();
				$Tres_poub = array();
				$Terr_poub = array();
				$TresR_ss_grpe = array();
				$TerrR_ss_grpe = array();
				$TerrR_eff_accesgroupes = array();
				
		// admins
				$TresA_nvx = array();
  			$TerrA_nvx = array();
  			$TresA_maj = array();
  			$TerrA_maj = array();
  			$TresA_eff = array();
  			$TerrA_eff = array();
				$TresA_ss_grpe = array();
				$TerrA_ss_grpe = array();
				$TerrA_eff_accesgroupes = array();
				$TerrA_eff_rub_admins = array();
				
		// visiteurs
				$TresV_nvx = array();
  			$TerrV_nvx = array();
  			$TresV_maj = array();
  			$TerrV_maj = array();
  			$TresV_eff = array();
  			$TerrV_eff = array();
				$TresV_ss_grpe = array();
				$TerrV_ss_grpe = array();
				$TerrV_eff_accesgroupes = array();
				
		// communs
			  $Tres_maj_grpacces = array();
				$Terr_maj_grpacces = array();
				$Tres_maj_rub_admin = array();
				$Terr_maj_rub_admin = array();
				
  	// LA boucle : gère 1 à 1 les utilisateurs de tmp_auteurs en fonction des options => TOUS !
				$sql157 = spip_query("SELECT * FROM tmp_auteurs");
  			while ($data157 = spip_fetch_array($sql157)) {
  			 			 if ($data157['pseudo_spip'] != '') {
							 		$nom = ucwords($data157['pseudo_spip']);
//print '<br>pseudo_spip existe : $data157[pseudo_spip] = '.$data157['pseudo_spip'].' $nom = '.$nom;									
							 }
							 else {
							 			$nom = strtoupper($data157['nom']).' '.ucfirst($data157['prenom']);
							 }
//print '<br>$nom = '.$nom.' $data157[nom] = '.$data157['nom'].' $data157[pseudo_spip] = _'.$data157['pseudo_spip'].'_';							 
							 $groupe = strtolower($data157['groupe']);
							 $ss_groupe = $data157['ss_groupe'];
  						 $pass = $data157['mdp'];
							 $mel = $data157['mel'];
  						 $login = $data157['nom'];
							 $login_minuscules = strtolower($login);
					 		 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $statut = '1comite' : $statut = '6forum') : $statut = '0minirezo';
							 
  						 $sql423 = spip_query("SELECT COUNT(*) AS nb_user FROM $Tauteurs WHERE LOWER(login) = '$login_minuscules' LIMIT 1");
  						 $data423 = spip_fetch_array($sql423);							 
  						 $nb_user = $data423['nb_user'];	
// 4.1 : l'utilisateur n'est pas inscrit dans la base spip_auteurs
							 if ($nb_user < 1) {
  								 		$pass = csv2spip_crypt_md5($pass);
  										spip_query("INSERT INTO $Tauteurs (id_auteur, nom, email, login, pass, statut) VALUES ('', '$nom', '$mel', '$login', '$pass', '$statut')");
  										$id_spip = mysql_insert_id();
  										if (mysql_error() == '') {
										 		 include_spip("inc/indexation");
												 marquer_indexer('spip_auteurs', $id_auteur);
                  	// Mettre a jour les fichiers .htpasswd et .htpasswd-admin
                  	     ecrire_acces();
										// insertion de l'id_spip dans la base tmp
												 spip_query("UPDATE tmp_auteurs SET id_spip = '$id_spip' WHERE LOWER(nom) = '$login_minuscules' LIMIT 1");
												 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_nvx[] = $login: $TresV_nvx[] = $login) : $TresA_nvx[] = $login;
  										}
  										else {
													 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_nvx[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_nvx[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrA_nvx[] = array('login' => $login, 'erreur' => mysql_error());
											}
							 }
							 else {
// 4.2 : l'utilisateur est déja inscrit dans la base spip_auteurs
								// trouver l'id_auteur spip
										$sql44 = spip_query("SELECT id_auteur FROM $Tauteurs WHERE LOWER(login) = '$login_minuscules' LIMIT 1");
									  if (spip_num_rows($sql44) > 0) {
									 		 $result44 = spip_fetch_array($sql44);
											 $id_spip = $result44['id_auteur'];
  										 spip_query("UPDATE tmp_auteurs SET id_spip = '$id_spip' WHERE LOWER(nom) = '$login_minuscules' LIMIT 1");											 
									  } 
								// faut il faire la maj des existants ?
  									if ($_POST['maj_gene'] == 1) {
    								// 4.2.1 faire la maj des infos perso si nécessaire
												if ($_POST['maj_mdp'] == 1) {
      										 $pass = csv2spip_crypt_md5($pass);
      										 spip_query("UPDATE $Tauteurs SET nom = '$nom', email = '$mel', statut = '$statut', pass = '$pass', alea_actuel = '' WHERE id_auteur = $id_spip LIMIT 1");
      										 if (mysql_error() == '') {
        											 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_maj[] = $login : $TresV_maj[] = $login) : $TresA_maj[] = $login;
          								 }
          								 else {
          											 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_maj[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_maj[] = array('login' => $login, 'erreur' => mysql_error())) : $TerrA_maj[] = array('login' => $login, 'erreur' => mysql_error());
          								 }
      									}
										// 4.2.2 réinitialisation des groupes acces_groupes si nécessaire
											  if ( ($_POST['maj_grpes_redac'] == 1 AND $statut == '1comite') 
													 	  OR ($_POST['maj_grpes_admin'] == 1 AND $statut == '0minirezo')
															OR ($_POST['maj_grpes_visit'] == 1 AND $statut == '6forum')
														) {
														spip_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_spip");
														if (mysql_error() == '') {
        											 $Tres_maj_grpacces[] = $login;
          								  }
          								  else {
          											 $Terr_maj_grpacces[] = array('login' => $login, 'erreur' => mysql_error());
          								  }
												}
										// 4.2.3 suppression des droits sur les rubriques administrées si nécessaire
											  if ($_POST['maj_rub_adm'] == 1 AND $statut == '0minirezo') {
													 spip_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_spip");
													 if (mysql_error() == '') {
        											 $Tres_maj_rub_admin[] = $login;
          								  }
          								  else {
          											 $Terr_maj_rub_admin[] = array('login' => $login, 'erreur' => mysql_error());
          								  }
												}
										}
							 }

												 
// 4.3 : intégrer l'auteur dans son ss-groupe acces_groupes si nécessaire 
  						 if (($ss_groupes_redac == 1 AND $statut == '1comite') OR ($ss_groupes_admin == 1 AND $statut == '0minirezo') OR ($ss_groupes_visit == 1 AND $statut == '6forum')) {
  								if ($id_grpacces_ec = $Tgroupes_accesgroupes[$ss_groupe]) {
  									  $sql55 = spip_query("SELECT COUNT(*) AS existe_auteur FROM $Taccesgroupes_auteurs WHERE id_grpacces = $id_grpacces_ec AND id_auteur = $id_spip LIMIT 1");
										  $result55 = spip_fetch_array($sql55);
										// l'utilisateur n'existe pas dans la table _accesgroupes_auteurs
											if ($result55['existe_auteur'] == 0) {
													spip_query("INSERT INTO $Taccesgroupes_auteurs (id_grpacces, id_auteur, dde_acces, proprio)
  																		VALUES ($id_grpacces_ec, $id_spip, 0, 0)");
  												if (mysql_error() == '') {
  													 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $TresR_ss_grpe[] = $login : $TresV_ss_grpe[] = $login) : $TresA_ss_grpe[] = $login;
  												}
  												else {
  														 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $TerrR_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrA_ss_grpe[] = array('login' => $login, 'erreur' => mysql_error());
  												}
										  }
  								}
  								else {
  										 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe) : $TerrV_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe) ) :  $TerrA_ss_grpe[] = array('login' => $login, 'erreur' => _T('csvspip:err_integ_accesgroupes').$ss_groupe);
  								}
  						 }
				} 

// 4.4 : gestion des suppressions
				
// VERSION 2.3 de effacer les absents
				$ch_maj = 0;
				$eff_absv = 0;
				$eff_absr = 0;
				$eff_absa = 0;
	 			if ($_POST['eff_visit'] == 1) {
//					 $ch_maj = 1;
					 $eff_absv = 1;
				}
				if ($_POST['eff_redac'] == 1) {
					 $ch_maj = 1;
					 $eff_absr = 1;
				}
				if ($_POST['eff_admin'] == 1) {
					 $ch_maj = 1;
					 $eff_absa = 1;
				}

    // paramétrage auteur et dossier d'archive
	 			if ($ch_maj !== 0) {
    			// si auteurs supprimés (pas de poubelle), récupérer l'id du rédacteur affecté aux archives + si nécessaire, créer cet auteur (groupe = poubelle)
      			if ($_POST['auteurs_poubelle'] != 1) {
      			 		$nom_auteur_archives = $_POST['nom_auteur_archives'];
      					$sql615 = spip_query("SELECT id_auteur FROM $Tauteurs WHERE login = '$nom_auteur_archives' LIMIT 1");
      					if (spip_num_rows($sql615) > 0) {
      						 $data615 = spip_fetch_array($sql615);
      						 $id_auteur_archives = $data615['id_auteur'];
      					}
      					else {
      							 spip_query("INSERT INTO $Tauteurs (id_auteur, nom, login, pass, statut) VALUES ('', '$nom_auteur_archives', '$nom_auteur_archives', '$nom_auteur_archives', '5poubelle')");
      						   $id_auteur_archives = mysql_insert_id();
      					}
      					$nom_rub_archivesR = $nom_auteur_archives;
      					$id_rub_parent_archivesA = $nom_auteur_archives;
      					$id_rub_parent_archivesR = $id_auteur_archives;
      					$nom_rub_archivesA = $nom_auteur_archives;
      					$id_auteur_archivesA = $id_auteur_archives;
      					$nom_auteur_archivesR = $nom_auteur_archives;
      					$id_auteur_archivesR = $id_auteur_archives;
    				
    		// si archivage, récup de l'id de la rubrique archive + si nécessaire, créer la rubrique				 		
    						if ($_POST['supprimer_articles'] != 1 AND $_POST['archivage'] != 0) {
    							 $supprimer_articlesr = 1;
    							 $supprimer_articlesa = 1;
    							 $archivager =1;
    							 $archivagea = 1;
    							 
    							 $nom_rub_archives = $_POST['rub_archivage'];
								// $_POST['rub_parent_archivage'] de la forme : "id_rubrique,id_secteur"
    							 $Tids_parent_rub_archives = explode(',', $_POST['rub_parent_archivage']);
    							 $id_rub_parent_archives = $Tids_parent_rub_archives[0];
    							 $id_sect_parent_archives = $Tids_parent_rub_archives[1];
    							 $date_rub_archives = date("Y-m-j H:i:s");
    							 $sql613 = spip_query("SELECT id_rubrique, id_secteur FROM $Trubriques WHERE titre = '$nom_rub_archives' AND id_parent = '$id_rub_parent_archives' LIMIT 1");
    							 if (spip_num_rows($sql613) > 0) {
    									 $data613 = spip_fetch_array($sql613);
    									 $id_rub_archives = $data613['id_rubrique'];
    							 }
    							 else {
    										 spip_query("INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archives', '$nom_rub_archives', '$id_sect_parent_archives', 'publie', '$date_rub_archives')" );
    										 $id_rub_archives = mysql_insert_id();
    							 }
      			    }
      			}
				}
							        						
      // 4.4.1 : traitement des visiteurs actuels de la base spip_auteurs => si effacer les absV = OK
        if ($eff_absv == 1) {
		  			  $sql1471 = spip_query("SELECT COUNT(*) AS nb_redacsV FROM $Tauteurs WHERE statut = '6forum'");
        			$data1471 = spip_fetch_array($sql1471);
        			if ($data1471['nb_redacsV'] > 0) {
      				// pas de poubelle pour les visiteurs => suppression puisque pas d'articles
        			 		$sql1591 = spip_query("SELECT id_auteur, login FROM $Tauteurs WHERE statut = '6forum'");
      						while ($data1591 = spip_fetch_array($sql1591)) {
        							   $login_sp = strtolower($data1591['login']);
      									 $id_auteur_ec = $data1591['id_auteur'];
        							   $sql4561 = spip_query("SELECT COUNT(*) AS nb FROM tmp_auteurs WHERE LOWER(nom) = '$login_sp' LIMIT 1");
        							   $data4561 = spip_fetch_array($sql4561);
             // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
          							 if ($data4561['nb'] == 0) {
      							// traitement des visiteurs à effacer												
      									  		spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '6forum' LIMIT 1");
      												if (mysql_error() == 0) {
          											  $TresV_eff[] = $login;
																// effacer toutes les références à ce visiteur dans acces_groupes
																  spip_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
																	if (mysql_error() != '') {
																		 $TerrV_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
																	}
            									}
            									else {
            												 $TerrV_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
            									}
      									 }
      						}
      // optimisation de la table après les effacements
      						spip_query("OPTIMIZE TABLE $Tauteurs, $Taccesgroupes_auteurs");

/*      						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
      							 spip_query("OPTIMIZE TABLE $textra");
      						}
*/									
      				}	
				}
        
      // 4.4.2 : traitement des rédacteurs actuels de la base spip_auteurs => si effacer les absents redac = OK
        if ($eff_absr == 1) {
				  			$sql147 = spip_query("SELECT COUNT(*) AS nb_redacsR FROM $Tauteurs WHERE statut = '1comite'");
          			$data147 = spip_fetch_array($sql147);
          			if ($data147['nb_redacsR'] > 0) {
        		// si archivage, récup de l'id de la rubrique archive + si nécessaire, créer la rubrique				 		
        						if ($supprimer_articlesr != 1 AND $archivager != 0) {
        							 $nom_rub_archivesR = $rub_archivager;
        							 $sql613 = spip_query("SELECT id_rubrique, id_secteur FROM $Trubriques WHERE titre = '$nom_rub_archivesR' AND id_parent = '$id_rub_parent_archivesR' LIMIT 1");
        							 if (spip_num_rows($sql613) > 0) {
        									 $data613 = spip_fetch_array($sql613);
        									 $id_rub_archivesR = $data613['id_rubrique'];
        							 }
        							 else {
        										 spip_query("INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archivesR', '$nom_rub_archivesR', '$id_sect_parent_archivesR', 'publie', '$date_rub_archivesR')" );
														 $id_rub_archivesR = mysql_insert_id();
        							 }
        						}
          			 		$sql159 = spip_query("SELECT id_auteur, login FROM $Tauteurs WHERE statut = '1comite' AND bio != 'archive'");
          					$cteur_articles_deplacesR = 0;
        						$cteur_articles_supprimesR = 0;
        						$cteur_articles_modif_auteurR = 0;
        						while ($data159 = spip_fetch_array($sql159)) {
          							   $login_sp = strtolower($data159['login']);
        									 $id_auteur_ec = $data159['id_auteur'];
          							   $sql456 = spip_query("SELECT COUNT(*) AS nb FROM tmp_auteurs WHERE nom = '$login_sp' LIMIT 1");
          							   $data456 = spip_fetch_array($sql456);
          // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
            							 if ($data456['nb'] == 0) {
        						// traitement éventuel des articles de l'auteur à supprimer
        										 		$sql757 = spip_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        												$data757 = spip_fetch_array($sql757);
        												if ($data757['nb_articles_auteur'] > 0) {
            												if ($supprimer_articlesr != 1) {
                												if ($archivager != 0) {
        																	 $sql612 = spip_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
                													 if (spip_num_rows($sql612) > 0) {
                													 		while ($data612 = spip_fetch_array($sql612)) {
                																		$id_article_ec = $data612['id_article'];
        																						spip_query("UPDATE $Tarticles SET id_rubrique = '$id_rub_archivesR', id_secteur = '$id_sect_parent_archivesR' WHERE id_article = '$id_article_ec' LIMIT 1");
        																						$cteur_articles_deplacesR ++;
                															}
                													 } 
                   												 if ($auteurs_poubeller != 1) {
                  														 spip_query("UPDATE $Tauteurs_articles SET id_auteur = '$id_auteur_archivesR' WHERE id_auteur = '$id_auteur_ec'");
                  												 }	   														
                												}
            												}
            												else {
            														 $sql756 = spip_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
            														 while ($data756 = spip_fetch_array($sql756)) {
            														 			 $id_article_a_effac = $data756['id_article'];
            																	 spip_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
        																			 $cteur_articles_supprimesR ++;
            														 }
            														 spip_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
            												}
        												}
        							// traitement des auteurs à effacer												
        												if ($auteurs_poubeller != 1) {
        													  spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '1comite' LIMIT 1");
            												if (mysql_error() == 0) {
                											 $TresR_eff[] = $login;
    																// effacer toutes les références à ce visiteur dans acces_groupes
    																   spip_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
    																	 if (mysql_error() != '') {
    																		 $TerrR_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
    																	 }
                  									}
    																else {
                  											 $TerrR_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                  									}
        												}
        												else {
        														  spip_query("UPDATE $Tauteurs SET statut = '5poubelle' WHERE id_auteur = '$id_auteur_ec' LIMIT 1");
              												if (mysql_error() == 0) {
                  											 $TresR_poub[] = $id_auteur_ec;
                    									 }
                    									 else {
                    												 $TerrR_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                    									 }
        												}
        									 }
        						}
        // optimisation de la table après les effacements
        						spip_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
/*										
        						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
        							 spip_query("OPTIMIZE TABLE $textra");
        						}
        						if ($extra_supCSV == 1) {
        							 spip_query("OPTIMIZE TABLE $textracsv");
        						}						
*/										
        				}		
        }
      // 4.4.3 : traitement des administrateurs restreints actuels de la base spip_auteurs => si effacer les absA = OK
        if ($eff_absa == 1) {
						$sql1473 = spip_query("SELECT COUNT(*) AS nb_redacsA FROM $Tauteurs
										 	 						 LEFT JOIN $Tauteurs_rubriques
																	 ON $Tauteurs_rubriques.id_auteur = $Tauteurs.id_auteur
																	 WHERE statut = '0minirezo'");
//echo '<br>mysql_error 1473 = '.mysql_error();
      			$data1473 = spip_fetch_array($sql1473);
      			if ($data1473['nb_redacsA'] > 0) {
      			 		$sql1593 = spip_query("SELECT Tauteurs.id_auteur, Tauteurs.login FROM $Tauteurs AS Tauteurs, $Tauteurs_rubriques AS Tauteurs_rubriques WHERE statut = '0minirezo' AND Tauteurs.id_auteur = Tauteurs_rubriques.id_auteur");
      					$cteur_articles_deplacesA = 0;
    						$cteur_articles_supprimesA = 0;
    						$cteur_articles_modif_auteurA = 0;
    						while ($data1593 = spip_fetch_array($sql1593)) {
      							   $login_sp = strtolower($data1593['login']);
    									 $id_auteur_ec = $data1593['id_auteur'];
      							   $sql4563 = spip_query("SELECT COUNT(*) AS nbA FROM tmp_auteurs WHERE nom = '$login_sp' LIMIT 1");
      							   $data4563 = spip_fetch_array($sql4563);
      // l'utilisateur n'est pas dans le fichier CSV importé => le supprimer
        							 if ($data4563['nbA'] == 0) {
    						// traitement éventuel des articles de l'admin à supprimer
    										 		$sql7573 = spip_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
    												$data7573 = spip_fetch_array($sql7573);
    												if ($data7573['nb_articles_auteur'] > 0) {
        												if ($supprimer_articlesa != 1) {
            												if ($archivagea != 0) {
    																	 $sql6123 = spip_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
            													 if (spip_num_rows($sql6123) > 0) {
            													 		while ($data6123 = spip_fetch_array($sql6123)) {
            																		$id_article_ec = $data6123['id_article'];
    																						spip_query("UPDATE $Tarticles SET id_rubrique = '$id_rub_archivesA', id_secteur = '$id_sect_parent_archivesA' WHERE id_article = '$id_article_ec' LIMIT 1");
    																						$cteur_articles_deplacesA ++;
            															}
            													 } 
               												 if ($auteurs_poubellea != 1) {
              														 spip_query("UPDATE $Tauteurs_articles SET id_auteur = '$id_auteur_archivesA' WHERE id_auteur = '$id_auteur_ec'");
              												 }	   														
            												}
        												}
        												else {
        														 $sql7563 = spip_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        														 while ($data7563 = spip_fetch_array($sql7563)) {
        														 			 $id_article_a_effac = $data7563['id_article'];
        																	 spip_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
    																			 $cteur_articles_supprimesA ++;
        														 }
        														 spip_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
        												}
    												}
    							// traitement des admins à effacer												
    												if ($auteurs_poubellea != 1) {
    													  spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '0minirezo' LIMIT 1");
        												if (mysql_error() == 0) {
            											 $TresA_eff[] = $login;
																// effacer toutes les références à ce visiteur dans acces_groupes
																   spip_query("DELETE FROM $Taccesgroupes_auteurs WHERE id_auteur = $id_auteur_ec");
																	 if (mysql_error() != '') {
																		 $TerrA_eff_accesgroupes[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
																	 }
              								 // virer l'administation de toutes les rubriques pour cet admin
																	 spip_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_auteur_ec");
																	 if (mysql_error() != '') {
																		 $TerrA_eff_rub_admins[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
																	 }
              									 }
              									 else {
																		  $TerrA_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
              									 }
    												}
    												else {
    														  spip_query("UPDATE $Tauteurs SET statut = '5poubelle' WHERE id_auteur = '$id_auteur_ec' LIMIT 1");
          												if (mysql_error() == 0) {
              											 $TresA_poub[] = $id_auteur_ec;
                									 }
                									 else {
                												 $TerrA_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
                									 }
    												}
    									 }
    						}
    // optimisation de la table après les effacements
    						spip_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
/*								
    						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
    							 spip_query("OPTIMIZE TABLE $textra");
    						}
    						if ($extra_supCSV == 1) {
    							 spip_query("OPTIMIZE TABLE $textracsv");
    						}
*/														
    				}
				}   
//				}
    //   fin effacer les abs (4.4)  V 2.3	

		// résultats étape 4
				debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape4'));
			  echo "<br>"._T('csvspip:etape4.1')."<br>";
			  if (count($TerrV_nvx) > 0) {		
			 		echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
					foreach ($TerrV_nvx as $Ven) { 
 									echo _T('csvspip:utilisateur').$Ven['login']._T('csvspip: erreur').$Ven['erreur']."<br>";
					}
					echo "</span>";
			 	  $err_total ++;
			  }
			  else {
			 			echo "<br>"._T('csvspip:creation').count($TresV_nvx)._T('csvspip:comptes_visit_ok')."<br>";					 			
			  }
				if (count($Terr_nvx) > 0) {		
					 echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
					 foreach ($Terr_nvx as $en) { 
					 					echo _T('csvspip:utilisateur').$en['login']._T('csvspip:erreur').$en['erreur']."<br>";
					 }					
					 echo "</span>";
				   $err_total ++;
			  }
			  else {
			 			echo "<br>"._T('csvspip:creation').count($Tres_nvx)._T('csvspip:comptes_redac_ok')."<br>";					 
			  }

			  if (count($TerrA_nvx) > 0) {		
			 		echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
					foreach ($TerrA_nvx as $Pen) { 
 									echo _T('csvspip:utilisateur').$Pen['login']._T('csvspip: erreur').$Pen['erreur']."<br>";
					}
					echo "</span>";
			 	  $err_total ++;
			  }
			  else {
			 			echo "<br>"._T('csvspip:creation').count($TresA_nvx)._T('csvspip:comptes_admin_ok')."<br>";					 			
			  }

		// 4.2 résultats maj des existants
				if ($_POST['maj_gene'] == 1) {
    			  echo "<br>"._T('csvspip:etape4.2')."<br>";
						if ($_POST['maj_mdp'] == 1) { 					
          		echo "<br>"._T('csvspip:etape4.2.1')."<br>";
							if (count($TerrV_maj) > 0) {
        					echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
          			  foreach ($TerrV_maj as $Vem) { 
        	 		 						echo _T('csvspip:visit').$Vem['login']._T('csvspip: erreur').$Vem['erreur']."<br>";
        				  }		
        					echo "</span>";
        		 		  $err_total ++;
        			}
        			else {
        						 echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_visit_ok')."<br>";
        			}  					
    		 			if (count($Terr_maj) > 0) {		
    						 echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
    						 foreach ($Terr_maj as $em) { 
    					 		 		 	 echo _T('csvspip:redac').$em['login']._T('csvspip: erreur').$em['erreur']."<br>";
    						 }		 
    						 echo "</span>";
    				 		 $err_total ++;
        			}
        			else {
        					 echo "<br>"._T('csvspip:ok_etape4.2.1').count($Tres_maj)._T('csvspip:comptes_redac_ok')."<br>";							
        			} 
          		if (count($TerrA_maj) > 0) {
        					echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
          			  foreach ($TerrA_maj as $Pem) { 
        	 		 						echo _T('csvspip:admin').$Pem['login']._T('csvspip: erreur').$Pem['erreur']."<br>";
        				  }		
        					echo "</span>";
        		 		  $err_total ++;
        			}
        			else {
        						 echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_admin_ok')."<br>";
        			}  					
    			  }
						if ($_POST['maj_grpes_redac'] == 1 OR $_POST['maj_grpes_admin'] == 1 OR $_POST['maj_grpes_visit'] == 1) {
							 echo "<br>"._T('csvspip:etape4.2.2')."<br>";
							 if (count($Terr_maj_grpacces) > 0) {
							 		echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_grpacces');
          			  foreach ($Terr_maj_grpacces as $Peg) { 
        	 		 						echo _T('csvspip:utilisateur').$Peg['login']._T('csvspip: erreur').$Peg['erreur']."<br>";
        				  }		
        					echo "</span>";
        		 		  $err_total ++;
							 }
							 else {
							 			echo "<br />"._T('csvspip:ok_maj_grpacces').count($Tres_maj_grpacces)._T('csvspip:utilisateurs')."<br>";
							 }
						}
						if ($_POST['maj_rub_adm'] == 1) {
							 echo "<br>"._T('csvspip:etape4.2.3')."<br>";
							 if (count($Terr_maj_rub_admin) > 0) {
							 		echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_rub_adm');
          			  foreach ($Terr_maj_rub_admin as $Pera) { 
        	 		 						echo _T('csvspip:utilisateur').$Pera['login']._T('csvspip: erreur').$Pera['erreur']."<br>";
        				  }		
        					echo "</span>";
        		 		  $err_total ++;
							 }
							 else {
							 			echo "<br />"._T('csvspip:ok_maj_rub_adm').count($Tres_maj_rub_admin)._T('csvspip:utilisateurs')."<br>";
							 }
						}
				}
				
		// 4.3 résultats intégration des utilisateurs dans les groupes acces_groupes
		    if ($_POST['ss_groupes_redac'] == 1 OR $_POST['ss_groupes_admin'] == 1 OR $_POST['ss_groupes_visit'] == 1) {
					 echo "<br />"._T('csvspip:etape4.3')."<br>";
					 
				}
				
		// 4.4 résultats effacer les absents
		    if ($eff_absv == 1 OR $eff_absr == 1 OR $eff_absa == 1) {
					 echo "<br />"._T('csvspip:etape4.4')."<br>";
				}
		// résultats effacer les visiteurs
				if ($eff_absv == 1) {  					
					 echo "<br />"._T('csvspip:etape4.4.1')."<br>";
			 		 if (count($TerrV_eff) > 0 OR count($TerrV_poub) > 0) {	
					 		echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
							foreach ($TerrV_eff as $Vee) {
											echo _T('csvspip:visit').$Vee['login']._T('csvspip: erreur').$Vee['erreur'];
							}	
							$err_total ++;
					 }
					 else { 
					 			echo "<br />"._T('csvspip:suppression_debut').count($TresV_eff)._T('csvspip:comptes_visit_ok')."<br>";
					 }
/*
							if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $extrav != 0) {  ?>					
			<h3>Etape 4.3.1.1 : suppression des références aux visiteurs supprimés dans la table supplémentaire :</h3>
<?				 			 if (count($TerrV_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des références dans la table supplémentaire : visiteurs en erreur :</h4>
<?							    foreach ($TerrV_eff_extra as $Vefx) { ?>
<?					 		 				 print 'rédacteur = '.$Vefx['login'].' => erreur = '.$Vefx['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des références dans la table supplémentaire pour <? print count($TresV_eff_extra); ?> visiteurs = OK<br>
<?							 }
							}  					
*/
				}  					
		
		// résultats effacer les redacteurs
				if ($eff_absr == 1) { 
					 echo "<br />"._T('csvspip:etape4.4.2')."<br>";
				 	 if (count($TerrR_eff) > 0 OR count($TerrR_poub) >0) {
					 		echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
							foreach ($TerrR_eff as $ee) { 
											echo _T('csvspip:redac').$ee['login']._T('csvspip: erreur').$ee['erreur'];
							}
							echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
							foreach ($TerrR_poub as $ep) { 
											echo _T('csvspip:redac').$ep['login']._T('csvspip: erreur').$ep['erreur'];
							}
				 		  $err_total ++;
				   }
					 else { 
					 			echo "<br />"._T('csvspip:suppression_debut').count($TresR_eff)._T('csvspip:comptes_redac_ok')."<br>";
								echo "<br />"._T('csvspip:poubelle_debut').count($TresR_poub)._T('csvspip:comptes_redac_ok')."<br>";
					 }
					 if ($archivager != 0) {
					 		echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesR._T('csvspip:archivage_fin').$nom_rub_archivesR;
					 }  
					 if ($supprimer_articlesr == 1) {
					 		echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesR._T('csvspip:suppression_fin')."<br>";
					 }
/*
					 if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $extrar != 0) {  ?>					
			<h3>Etape 4.3.2.1 : suppression des références aux rédacteurs supprimés dans la table supplémentaire :</h3>
<?				 			 if (count($TerrR_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des références dans la table supplémentaire : rédacteurs en erreur :</h4>
<?							    foreach ($TerrR_eff_extra as $Refx) { ?>
<?					 		 				 print 'rédacteur = '.$Refx['login'].' => erreur = '.$Rex['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des références dans la table supplémentaire pour <? print count($TresR_eff_extra); ?> rédacteurs = OK<br>
<?							 }
							}  					
*/
				}  					

		// résultats effacer les admins
				if ($eff_absa == 1) { 			
					 echo "<br />"._T('csvspip:etape4.4.3')."<br>";
				 	 if (count($TerrA_eff) > 0 OR count($TerrA_poub) >0) {
					 		echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
							foreach ($TerrA_eff as $Aee) { 
											echo _T('csvspip:admin').$Aee['login']._T('csvspip: erreur').$Aee['erreur'];
							}
							echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
							foreach ($TerrA_poub as $Aep) { 
											echo _T('csvspip:admin').$Aep['login']._T('csvspip: erreur').$Aep['erreur'];
							}
				 		  $err_total ++;
				   }
					 else { 
					 			echo "<br />"._T('csvspip:suppression_debut').count($TresA_eff)._T('csvspip:comptes_admin_ok')."<br>";
								echo "<br />"._T('csvspip:poubelle_debut').count($TresA_poub)._T('csvspip:comptes_admin_ok')."<br>";
					 }
					 if ($archivagea != 0) {
					 		echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesA._T('csvspip:archivage_fin').$nom_rub_archivesA;
					 }  
					 if ($supprimer_articlesa == 1) {
					 		echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesA._T('csvspip:suppression_fin')."<br>";
					 }
					 if (count ($TerrA_eff_accesgroupes) > 0) {
					 		echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
							foreach ($TerrA_eff_accesgroupes as $Aec) { 
											echo _T('csvspip:err_eff_adm_accesgroupes').$Aec['login']._T('csvspip: erreur').$Aec['erreur'];
							}
							echo "</span>";
							$err_total ++;
					 }
					 if (count ($TerrA_eff_rub_admins) > 0) {
					 		echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
							foreach ($TerrA_eff_rub_admins as $Aer) { 
											echo _T('csvspip:err_eff_adm_rub').$Aer['login']._T('csvspip: erreur').$Aer['erreur'];
							}
							echo "</span>";
							$err_total ++;					 		
					 }
/*
							if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $extraa != 0) {  ?>					
			<h3>Etape 4.3.3.1 : suppression des références aux administrateurs supprimés dans la table supplémentaire :</h3>
<?				 			 if (count($TerrA_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des références dans la table supplémentaire : administrateurs en erreur :</h4>
<?							    foreach ($TerrA_eff_extra as $Aefx) { ?>
<?					 		 				 print 'rédacteur = '.$Aefx['login'].' => erreur = '.$Aex['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des références dans la table supplémentaire pour <? print count($TresA_eff_extra); ?> administrateurs = OK<br>
<?							 }
							}  					
*/
				}
				
// fin effacer les absents V 2.3
				fin_cadre_couleur();			
		
// étape 5 : si nécessaire intégration des admins comme administrateurs restreints de la rubrique de leur sous-groupe
//$id_rub_admin_defaut
	 			if ($groupe_admins != '-1') {
  					 $Terr_adm_rub = array();
  					 $Tres_adm_rub = array();
						 $sql54 = spip_query("SELECT ss_groupe, nom, id_spip FROM tmp_auteurs WHERE LOWER(groupe) = '$groupe_admins'");
						 while ($data54 = spip_fetch_array($sql54)) {
						 			 $login_adm_ec = strtolower($data54['nom']);
									 $id_adm_ec = $data54['id_spip'];
									 if ($_POST['rub_prof'] == 1 AND $data54['ss_groupe'] != '') {
//    									 if ($data54['ss_groupe'] != '') {
											 		$ss_grpe_ec = $data54['ss_groupe'];
      									  $sql55 = spip_query("SELECT id_rubrique FROM $Trubriques WHERE titre = '$ss_grpe_ec' LIMIT 1");
      									  $data55 = spip_fetch_array($sql55);
      									  $id_rubrique_adm_ec = $data55['id_rubrique'];									 		
//											 }
//											 else {
//											 			$id_rubrique_adm_ec = $id_rub_admin_defaut;
//											 }
									 }
									 else {
									 			$id_rubrique_adm_ec = $id_rub_admin_defaut;
									 }
									 $sql57 = spip_query("SELECT COUNT(*) AS existe_adm_rub FROM $Tauteurs_rubriques WHERE id_auteur = '$id_adm_ec' AND id_rubrique = '$id_rubrique_adm_ec' LIMIT 1");
									 $data57 = spip_fetch_array($sql57);
									 if ($data57['existe_adm_rub'] == 0) {
//print '<br>rubrique $ss_grpe_ec = '.$ss_grpe_ec.' $id_rubrique_adm_ec = '.$id_rubrique_adm_ec.'$id_adm_ec = '.$id_adm_ec;								 
									 		spip_query("INSERT INTO $Tauteurs_rubriques (id_auteur, id_rubrique) VALUES ('$id_adm_ec', '$id_rubrique_adm_ec')");
									 		if (mysql_error() != '') {
  											 $Terr_adm_rub[] = array('login' => $login_adm_ec, 'rubrique' => $ss_grpe_ec, 'erreur' => mysql_error());
  										}
  										else {
  												 $Tres_adm_rub[] = $login_adm_ec;
											}
									 }
						 }
						 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape5'));
//						 echo "<h2>"._T('csvspip:titre_etape5')."</h2>";
					   if (count($Terr_adm_rub) > 0) {
						 		echo "<span class=\"Cerreur\">"._T('csvspip:err_admin_rubrique');
							  foreach ($Terr_adm_rub as $ear) { 
					 		 					echo _T('csvspip:admin').$ear['login']._T('csvspip:rubrique_').$ear['rubrique']._T('csvspip: erreur').$ear['erreur']."<br>";
								}	
								echo "</span>";
					 		  $err_total ++;
							}
							else {
									 print 'Attribution d\'une sous-rubrique pour '.count($Tres_adm_rub).' administrateurs restreints = OK<br>';
							}
							fin_cadre_couleur();
	 			}
				 	
// Etape 6 : si nécessaire création d'un article par rubrique 					
					if ($_POST['art_rub'] == 1 AND $_POST['rub_prof'] == 1) {
						 $Terr_art_rub = array();
						 $Tres_art_rub = array();
						 $sql57 = spip_query("SELECT ss_groupe, nom FROM tmp_auteurs WHERE groupe = '$groupe_admins' AND ss_groupe != '' GROUP BY ss_groupe");
						 while ($data57 = spip_fetch_array($sql57)) {
						 			 $titre_rub_ec = $data57['ss_groupe'];
									 $sql58 = spip_query("SELECT id_rubrique, id_parent, id_secteur FROM $Trubriques WHERE titre = '$titre_rub_ec' AND id_parent = '$rubrique_parent' LIMIT 1");
									 $data58 = spip_fetch_array($sql58);
									 $id_rub_ec = $data58['id_rubrique'];
									 $id_parent_ec = $data58['id_parent'];
									 $id_sect_ec = $data58['id_secteur'];
									 $date_ec = date("Y-m-d H:i:s");
									 $titre_ec = 'Bienvenue dans la rubrique '.$titre_rub_ec;
									 $sql432 = spip_query("SELECT id_article FROM $Tarticles WHERE id_rubrique = '$id_rub_ec' AND titre = '$titre_ec' LIMIT 1");
									 if (spip_num_rows($sql432) < 1) {
									 		 $data432 = spip_fetch_array($sql432);
									 		 spip_query("INSERT INTO $Tarticles (id_article, id_rubrique, id_secteur, titre, date, statut ) VALUES ('', '$id_rub_ec', '$id_sect_ec', '$titre_ec', '$date_ec', 'publie')");
    									 if (mysql_error() != '') {
    											 $Terr_art_rub[] = array('rubrique' => $titre_rub_ec, 'erreur' => mysql_error());
    									 }
    									 else {
    												 $Tres_art_rub[] = $titre_rub_ec;
    									 }
									 }
						 }
						 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape6'));
//						 echo "<h3>"._T('csvspip:titre_etape6')."</h3>";
 					   if (count($Terr_art_rub) > 0) {
						 		echo "<span class=\"Cerreur\">"._T('csvspip:err_article');
							  foreach ($Terr_art_rub as $eart) { 
					 		 				  echo _T('csvspip:rubrique_').$eart['rubrique']._T('csvspip:erreur').$eart['erreur']."<br>";
							  }	
								echo "</span>";
					 		  $err_total ++;
							}
							else {
									 echo _T('csvspip:ok_etape6_debut').count($Tres_art_rub)._T('csvspip:ok_etape6_fin')."<br>";
							}
							fin_cadre_couleur();
  				}
					
					
					
					
// suppression de la table temporaire
			 		if ($err_total == 0) { 
//						 spip_query("DROP TABLE tmp_auteurs");
					}
					
//					fin_cadre_trait_couleur();
    }
// FIN TRAITEMENT DES DONNEES


// Formulaire de saisie du fichier CSV et des options de config		
		else {
echo "<script language=\"JavaScript\"> ";
echo "				function aff_masq(id_elem, vis) { ";
echo "								 vis == 0 ? s_vis = 'none' : s_vis = 'block'; ";
echo "								 document.getElementById(id_elem).style.display = s_vis; ";
//echo "								 document.getElementById(id_elem).style.display = s_vis; ";
echo "								 this.checked = 'checked'; ";			 
echo "				}";
echo "</script>";

//         debut_cadre_formulaire();
      	 echo "\r\n<form name=\"csv2spip\" enctype=\"multipart/form-data\" action=\"".$PHP_SELF."?exec=csv2spip\" method=\"post\" onsubmit=\"return (verifSaisie());\">";
    		 debut_cadre_couleur("cal-today.gif", false, "", _T('csvspip:titre_choix_fichier'));
         echo "<strong>"._T('csvspip:choix_fichier')."</strong><input name=\"userfile\" type=\"file\">";
			 	 echo "<br><br /><strong>"._T('csvspip:nom_groupe_redac')."</strong><input type=\"text\" name=\"groupe_redacs\" value=\"REDACTEURS\">";
				 echo "<br><br /><strong>"._T('csvspip:nom_groupe_admin')."</strong><input type=\"text\" name=\"groupe_admins\" value=\"ADMINS\">";
				 echo "<br><br /><strong>"._T('csvspip:nom_groupe_visit')."</strong><input type=\"text\" name=\"groupe_visits\" value=\"VISITEURS\">";
       	 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_nom_groupe_admin')."</span>";		
				 
				 fin_cadre_couleur();
				 debut_cadre_couleur("mot-cle-24.gif", false, "", _T('csvspip:options_maj'));
				 echo "<strong>"._T('csvspip:maj_utils')."</strong>";
    		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_gene\" value=\"1\"  checked=\"checked\" onClick=\"aff_masq('maj_avance', 1);\">"; 
    		 echo "<input type=\"radio\" name=\"maj_gene\" value=\"0\" onClick=\"aff_masq('maj_avance', 0);\">"._T('csvspip:non');
         echo "<div id=\"maj_avance\" class=\"cadre\">";
    		 echo "<br /><strong>"._T('csvspip:maj_mdp')."</strong>"; 
    		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_mdp\" value=\"1\"  checked=\"checked\">"; 
    		 echo "<input type=\"radio\" name=\"maj_mdp\" value=\"0\">"._T('csvspip:non');
				 echo "<br /><br /><strong>"._T('csvspip:maj_grpes')."</strong>";
				 echo "<ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
				 echo "<li style=\"list-style-image: url('img_pack/redac-12.gif');\"><strong>"._T('csvspip:redacs').":</strong> ";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_redac\" value=\"1\" checked=\"checked\">";
				 echo "<input type=\"radio\" name=\"maj_grpes_redac\" value=\"0\">"._T('csvspip:non');
				 echo "</li>";
				 echo "<li style=\"list-style-image: url('img_pack/admin-12.gif');\"><strong>"._T('csvspip:admins').":</strong> ";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_admin\" value=\"1\" checked=\"checked\">";
				 echo "<input type=\"radio\" name=\"maj_grpes_admin\" value=\"0\">"._T('csvspip:non'); 
				 echo "</li>";
				 echo "<li style=\"list-style-image: url('img_pack/visit-12.gif');\"><strong>"._T('csvspip:visits').":</strong> ";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_grpes_visit\" value=\"1\" checked=\"checked\" >";
				 echo "<input type=\"radio\" name=\"maj_grpes_visit\" value=\"0\">"._T('csvspip:non');
				 echo "</li>";				 
    		 echo "</ul>";
				 echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_maj_grpes')."</span><br>"; 
				 echo "<br /><img src=\"img_pack/admin-12.gif\" alt=\"admins uniquement\"> <strong>"._T('csvspip:maj_rub_adm')."</strong>";
       	 echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"1\" checked=\"checked\">"._T('csvspip:oui');   
         echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"0\">"._T('csvspip:non'); 
				 echo "<br /><span style=\"font-size: 10px;\">"._T('csvspip:help_maj_rub_adm')."</span><br>"; 
				 echo "</div>";				 
				 
				 fin_cadre_couleur();
				 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/supprimer_utilisateurs-24.gif", false, "", _T('csvspip:suppr_absents'));
    		 echo "<strong>"._T('csvspip:suppr_utilis')."</strong><ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
				 echo "<li style=\"list-style-image: url('img_pack/redac-12.gif');\">"._T('csvspip:suppr_redac')."";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_redac\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				 echo "<input type=\"radio\" name=\"eff_redac\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_admin[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non');
				 echo "</li>";
				 echo "<li style=\"list-style-image: url('img_pack/admin-12.gif');\">"._T('csvspip:suppr_admin');
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_admin\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				 echo "<input type=\"radio\" name=\"eff_admin\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_redac[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non'); 
				 echo "</li>";
				 echo "<li style=\"list-style-image: url('img_pack/visit-12.gif');\">"._T('csvspip:suppr_visit')."";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_visit\" value=\"1\" >";
				 echo "<input type=\"radio\" name=\"eff_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non');
				 echo "</li>";				 
    		 echo "</ul><span style=\"font-size: 10px;\">"._T('csvspip:help_suppr_redac')."</span><br>"; 
    		 echo "<div style=\"display: none\" id=\"archi\" class=\"cadre\"><br /><strong>"._T('csvspip:suprr_articles')."</strong>";
         echo _T('csvspip:oui')."<input type=\"radio\" name=\"supprimer_articles\" value=\"1\" onClick=\"aff_masq('transfert', 0);\">";   
         echo "<input type=\"radio\" name=\"supprimer_articles\" value=\"0\" checked=\"checked\" onClick=\"aff_masq('transfert', 1);\">"._T('csvspip:non'); 
         echo "<div id=\"transfert\" class=\"cadre\"><br><strong>"._T('csvspip:transfert_archive')."</strong>";
       	 echo "<input type=\"radio\" name=\"archivage\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_transfert', 1);\">"._T('csvspip:oui');   
         echo "<input type=\"radio\" name=\"archivage\" value=\"0\" onClick=\"aff_masq('rub_transfert', 0);\">"._T('csvspip:non'); 
         echo "<div id=\"rub_transfert\" class=\"cadre\"><br>";
         $sql9 = spip_query("SELECT COUNT(*) AS nb_rubriques FROM $Trubriques");
				 $data9 = spip_fetch_array($sql9);
				 $nb_rubriques = $data9['nb_rubriques'];
				 $annee = date("Y"); 
		     echo "<strong>"._T('csvspip:nom_rubrique_archives')."</strong>";
				 echo "<input type=\"text\" name=\"rub_archivage\" value=\"Archives année ".($annee - 1).'-'.$annee."\" style=\"width: 200px;\">";
		  	 echo "";
			   if ($nb_rubriques > 0) {   		
		  	    echo"<br><br><strong>"._T('csvspip:choix_parent_archive')."</strong>"; 
				    $sql10 = spip_query("SELECT id_rubrique, titre, id_secteur FROM $Trubriques ORDER BY titre");
        		echo "<select name=\"rub_parent_archivage\">";
        		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
						
				 		while ($data10 = spip_fetch_array($sql10)) { 
				 			     echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
			 		  }				 						
		     	  echo "</select><br>";
			 }
			 else { 
		        echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
			 }  		
			 echo "</div></div>";
    	 echo "<br><br><strong>"._T('csvspip:traitement_supprimes')."</strong><br>";
    	 echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"1\">"._T('csvspip:auteurs_poubelle')."  <br>"; 
    	 echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"0\" checked=\"checked\">"._T('csvspip:attribuer_articles'); 
    	 echo "<input type=\"text\" name=\"nom_auteur_archives\" value=\"archives".($annee - 1)."-".$annee."\">"._T('csvspip:passe_egale_login');
			 echo "</div>";
			 fin_cadre_couleur();
			 debut_cadre_couleur("rubrique-24.gif", false, "", _T('csvspip:creation_rubriques'));
//			 echo "<h3>"._T('csvspip:creation_rubriques')."</h3>";
			 echo "<strong>"._T('csvspip:rubrique_ss_groupes')."</strong>"; 
			 echo _T('csvspip:oui')."<input type=\"radio\" name=\"rub_prof\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_adm', 1);\">";   
			 echo "<input type=\"radio\" name=\"rub_prof\" value=\"0\" onClick=\"aff_masq('rub_adm', 0);\">"._T('csvspip:non');
			 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:profs_admins')."</span>";
			 echo "<br /><div id=\"rub_adm\" class=\"cadre\">";
 			 if ($nb_rubriques > 0) {   		
				  echo "<br /><strong>"._T('csvspip:choix_parent_rubriques')."</strong>"; 
      		echo "<select name=\"rub_parent\">";
      		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
				  $sql10 = spip_query("SELECT id_rubrique, titre, id_secteur FROM $Trubriques ORDER BY titre");
					while ($data10 = spip_fetch_array($sql10)) { 
				 			  echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
			 		}  	
		      echo "</select>";
			 }
		 	 else {  
				  	echo "<br>"._T('csvspip:pas_de_rubriques');
			 } 		
			 echo "<br /><br /><strong>"._T('csvspip:article_rubrique')."</strong>"; 
       echo _T('csvspip:oui')."<input type=\"radio\" name=\"art_rub\" value=\"1\">";   
       echo "<input type=\"radio\" name=\"art_rub\" value=\"0\" checked=\"checked\">"._T('csvspip:non');
			 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_articles')."</span>";
			 echo "<br /></div>";
			 echo "<br /><div id=\"rub_adm_defaut\">";
			 echo "<strong>"._T('csvspip:choix_rub_admin_defaut')."</strong>";
			 echo "<input type=\"text\" name=\"rub_admin_defaut\" value=\""._T('csvspip:nom_rub_admin_defaut')."\" style=\"width: 200px;\">";
			 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_rub_admin_defaut')."</span>";
 			 if ($nb_rubriques > 0) {   		
				  echo "<br/><br/><strong>"._T('csvspip:choix_parent_rub_admin_defaut')."</strong>"; 
      		echo "<select name=\"rub_parent_admin_defaut\">";
      		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
				  $sql108 = spip_query("SELECT id_rubrique, titre, id_secteur FROM $Trubriques ORDER BY titre");
					while ($data108 = spip_fetch_array($sql108)) { 
				 			  echo "<option value=\"".$data108['id_rubrique'].",".$data108['id_secteur']."\">".$data108['titre']."</option>";
			 		}  	
		      echo "</select><br />";
			 }
		 	 else {  
				  	echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
			 } 		
			 echo "</div>"; 
			 fin_cadre_couleur();
  		 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/groupe-24.png", false, "", _T('csvspip:acces_groupes'));
    	 echo "<strong>"._T('csvspip:option_acces_groupes')."</strong>"; 
/*
			 $sql11 = spip_query("SELECT valeur FROM spip_meta WHERE nom = 'plugin' LIMIT 1");
			 $result11 = spip_fetch_array($sql11);
			 $ch_meta = $result11['valeur'];
			 $Tch_meta = explode(',', $ch_meta);
		// si le plugin acces_groupes est activé
			 if (in_array('acces_groupes', $Tch_meta)) {
*/			 
			 if ($plugin_accesgroupes == 1) {
      		 echo "<ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
  				 echo "<li style=\"list-style-image: url('img_pack/redac-12.gif');\">"._T('csvspip:ss_groupes_redac')." ";
  				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_redac\" value=\"1\">";
  				 echo "<input type=\"radio\" name=\"ss_groupes_redac\" value=\"0\" checked=\"checked\">"._T('csvspip:non')."</li>";
  				 echo "<li style=\"list-style-image: url('img_pack/admin-12.gif');\">"._T('csvspip:ss_groupes_admin')." ";
  				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_admin\" value=\"1\">";
  				 echo "<input type=\"radio\" name=\"ss_groupes_admin\" value=\"0\" checked=\"checked\">"._T('csvspip:non')."</li>"; 
  				 echo "<li style=\"list-style-image: url('img_pack/visit-12.gif');\">"._T('csvspip:ss_groupes_visit')." ";
  				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_groupes_visit\" value=\"1\" >";
  				 echo "<input type=\"radio\" name=\"ss_groupes_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non')."</li>";
      		 echo "</ul>";
					 echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_acces_groupes')."</span>";
					 echo "<br /><br /><strong>"._T('csvspip:ss_grpes_reinitialiser')."</strong>";
  				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"ss_grpes_reinitialiser\" value=\"1\" checked=\"checked\">";
  				 echo "<input type=\"radio\" name=\"ss_grpes_reinitialiser\" value=\"0\">"._T('csvspip:non')."<br />";
					  echo "<span style=\"font-size: 10px;\">"._T('csvspip:help_reinitialiser')."</span>";
/*			 
        	 echo _T('csvspip:oui')."<input type=\"radio\" name=\"acces_groupes\" value=\"1\"  checked=\"checked\">"; 
        	 echo "<input type=\"radio\" name=\"acces_groupes\" value=\"0\">"._T('csvspip:non');
*/					 
			 }
			 else {
			 			echo "<br /><span class=\"Cerreur\">"._T('csvspip:abs_acces_groupes')."</span><br />";
			 }
  		 fin_cadre_couleur();
			 
    	 echo "<input type=\"submit\" value=\""._T('csvspip:lancer')."\" style=\"background-color: #FF8000; font-weight: bold; font-size: 14px;\">";
  		 echo "</form><br><br />";

//			 fin_cadre_trait_couleur();
			 
			 debut_cadre_trait_couleur("fiche-perso-24.gif", false, "", _T('csvspip:titre_help')); 
		// inclure le fichier help de la langue
			 include(_DIR_PLUGIN_CSV2SPIP.'/lang/csvspip_help_'.$GLOBALS['langue_site'].'.php');
			 echo "<a href=\""._DIR_PLUGIN_CSV2SPIP."/csv2spip_modele.csv\">csv2spip_modele.csv</a>";
//			 print '<br>globals renvoie :<br>';
//			 print $GLOBALS['langue_site'];
			 fin_cadre_trait_couleur();

		} 
		
//		fin_cadre_formulaire();
				
		echo fin_page();
}
		 
		 

?>
