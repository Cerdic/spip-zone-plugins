<?  
/* csv2spip est un plugin pour cr�er/modifier les r�dacteurs et administrateurs restreints d'un SPIP � partir de fichiers CSV
*	 					VERSION : 2.3 => plugin pour spip 1.9
*
* Auteur : cy_altern (cy_altern@yahoo.fr)
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
				 include_ecrire ("inc_index.php3");
						 
			// d�finir comme constante le chemin du r�pertoire du plugin
         $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
         $pp = explode("/", end($p));
         define('_DIR_PLUGIN_CSV2SPIP',(_DIR_PLUGINS.$pp[0]));

      // v�rifier les droits
         global $connect_statut;
      	 global $connect_toutes_rubriques;
         if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
      		  debut_page(_T('titre'), "csv2spip", "plugin");
      		  echo _T('avis_non_acces_page');
      		  fin_page();
      		  exit;
      	 }
			// trouver la version en cours � partir de plugin.xml
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
				 
			// d�but affichage
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

// Etape 0 : v�rification que le pr�fixe des tables SPIP est OK et d�finition des noms de tables SPIP
/*		if ($prefix_tables_SPIP == '') { 
			 <p style="background-color: red;">Attention le syst�me n'a pu d�terminer le nom de la table des utilisateurs de SPIP, veuillez �diter le fichier <strong>csv2spip.php</strong> pour v�rification des �l�ments configur�s</p>
 	     exit();
		}
*/
		$Trubriques = 'spip_rubriques';
		$Tauteurs = 'spip_auteurs';
		$Tauteurs_rubriques = 'spip_auteurs_rubriques';
		$Tarticles =  'spip_articles';
		$Tauteurs_articles = 'spip_auteurs_articles';
		
		$err_total = 0;

// �tape 1 : t�l�chargement du fichier sur le serveur		
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
				

// �tape 2 : passage des donn�es du fichier dans la base temporaire			
    		debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape2'));
//				echo "<h2>"._T('csvspip:titre_etape2')."</h2>";
				spip_query("DROP TABLE IF EXISTS tmp_auteurs");
    		if (!spip_query("CREATE TABLE tmp_auteurs (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, nom TEXT NOT NULL, prenom TEXT NOT NULL, groupe TEXT NOT NULL, ss_groupe TEXT NOT NULL, mdp TEXT NOT NULL, pseudo_spip TEXT NOT NULL, mel TEXT NOT NULL, id_spip INT(11) NOT NULL)") ) {  
					 echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape2.1')."</span>";
					 fin_cadre_couleur();
	 	    	 exit();
				}
    		else {
    			 		echo "<br>"._T('csvspip:ok_etape2.1')."<br>";
    		}
				
  			$ok = 0;
  			$Tlignes = file($nom_fich);
//print '<br>Tlignes d�part = ';
//print_r($Tlignes);				
  			$Terr_sql_temp = array();
				$ligne1 = 0;
  			foreach ($Tlignes as $l) {
								 $l = str_replace('"', '', $l);
  					 		 $Tuser_ec = explode(';', $l);
							// traiter la premi�re ligne pour r�cup�rer les noms des champs dc la position des colonnes
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
										if (count($Tchamps) != 7) {
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
							// ne pas int�grer la premi�re ligne comme un utilisateur
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
			
// �tape 3 : si n�cessaire cr�ation des rubriques de disciplines
				$_POST['groupe_admins'] != '' ? $groupe_admins = strtolower($_POST['groupe_admins']) : $groupe_admins = '-1';
	 		 	if ($_POST['rub_prof'] == 1 AND $groupe_admins != '-1') {
					 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape3'));
//					 echo "<h2>"._T('csvspip:titre_etape3')."</h2>";
					 $Terr_rub = array();
					 $date_rub_ec = date("Y-m-j H:i:s");
					 $Tch_rub = explode(',', $_POST['rub_parent']);
					 $rubrique_parent = $Tch_rub[0];
					 $secteur = $Tch_rub[1];
					 $sql8 = spip_query("SELECT ss_groupe FROM tmp_auteurs WHERE groupe = '$groupe_admins' GROUP BY ss_groupe");
					 if (isset($sql8)) {
    					 while ($data8 = spip_fetch_array($sql8)) {
    					 			 $rubrique_ec = $data8['ss_groupe']; 
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
    					 }
					 }		
  				 if (count($Terr_rub) > 0) {  
  				 		echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3');
	  				  foreach ($Terr_rub as $er) {
  										echo "<br>"._T('csvspip:rubrique').$er['ss_groupe']._T('csvspip:erreur').$er['erreur'];
  						}
  						echo "</span>";
	  	        $err_total ++;
					 }			
  			 	 else {
  			 				echo "<br>"._T('csvspip:ok_etape3')."<br>";
  			   }
					 fin_cadre_couleur();
				}

// �tape 4 : int�gration des r�dacteurs, des visiteurs et des administrateurs			
  			$_POST['groupe_visits'] != '' ? $groupe_visits = strtolower($_POST['groupe_visits']) : $groupe_visits = '-1';
				
		// redacteurs
				$Tres_nvx = array();
  			$Terr_nvx = array();
  			$Tres_maj = array();
  			$Terr_maj = array();
  			$Tres_eff = array();
  			$Terr_eff = array();
				$Tres_poub = array();
				$Terr_poub = array();
				
		// admins
				$TresP_nvx = array();
  			$TerrP_nvx = array();
  			$TresP_maj = array();
  			$TerrP_maj = array();
  			$TresP_eff = array();
  			$TerrP_eff = array();
				
		// visiteurs
				$TresV_nvx = array();
  			$TerrV_nvx = array();
  			$TresV_maj = array();
  			$TerrV_maj = array();
  			$TresV_eff = array();
  			$TerrV_eff = array();
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
//	if (lire_meta('activer_moteur') == 'oui') {
//		include_ecrire ("inc_index.php3");
										 		 marquer_indexer('auteur', $id_auteur);
//	}

                  	// Mettre a jour les fichiers .htpasswd et .htpasswd-admin
                  	     ecrire_acces();
												 
												 spip_query("UPDATE tmp_auteurs SET id_spip = '$id_spip' WHERE LOWER(nom) = '$login_minuscules' LIMIT 1");
												 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_nvx[] = $login: $TresV_nvx[] = $login) : $TresP_nvx[] = $login;
  										}
  										else {
  												 $id_auteur = 
													 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_nvx[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_nvx[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrP_nvx[] = array('login' => $login, 'erreur' => mysql_error());
											}
							 }
							 else {
// 4.2 : l'utilisateur est d�ja inscrit dans la base spip_auteurs => maj du mot de passe = OK
  									if ($_POST['maj_mdp'] == 1) {
  										 $pass = csv2spip_crypt_md5($pass);
  										 spip_query("UPDATE $Tauteurs SET nom = '$nom', email = '$mel', statut = '$statut', pass = '$pass', alea_actuel = '' WHERE LOWER(login) = '$login_minuscules' LIMIT 1");
  										 if (mysql_error() == 0) {
    											 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_maj[] = $login : $TresV_maj[] = $login) : $TresP_maj[] = $login;
      								 }
      								 else {
      											 $groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_maj[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_maj[] = array('login' => $login, 'erreur' => mysql_error())) : $TerrP_maj[] = array('login' => $login, 'erreur' => mysql_error());
      								 }
  									}
							 }
				} 

// 4.3 : gestion des suppressions
				
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


    // 4.3 effacer les absents => param�trage auteur et dossier d'archive
	 			if ($ch_maj !== 0) {
    			// si auteurs supprim�s (pas de poubelle), r�cup�rer l'id du r�dacteur affect� aux archives + si n�cessaire, cr�er cet auteur (groupe = poubelle)
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
    				
    		// si archivage, r�cup de l'id de la rubrique archive + si n�cessaire, cr�er la rubrique				 		
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
							        						
      // 4.3.1 : traitement des visiteurs actuels de la base spip_auteurs => si effacer les absV = OK
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
             // l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
          							 if ($data4561['nb'] == 0) {
      							// traitement des visiteurs � effacer												
      									  		spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '6forum' LIMIT 1");
      												if (mysql_error() == 0) {
          											 $TresV_eff[] = $login;
            									 }
            									 else {
            												 $TerrV_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
            									 }
      									 }
      						}
      						
      // optimisation de la table apr�s les effacements
      						spip_query("OPTIMIZE TABLE $Tauteurs");

/*      						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
      							 spip_query("OPTIMIZE TABLE $textra");
      						}
*/									
      				}	
				}
        
      // 4.3.2 : traitement des r�dacteurs actuels de la base spip_auteurs => si effacer les absents visit | redac |admin = OK
        if ($eff_absr == 1) {
				  			$sql147 = spip_query("SELECT COUNT(*) AS nb_redacsR FROM $Tauteurs WHERE statut = '1comite'");
          			$data147 = spip_fetch_array($sql147);
          			if ($data147['nb_redacsR'] > 0) {
        		// si archivage, r�cup de l'id de la rubrique archive + si n�cessaire, cr�er la rubrique				 		
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
          // l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
            							 if ($data456['nb'] == 0) {
        						// traitement �ventuel des articles de l'auteur � supprimer
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
        							// traitement des auteurs � effacer												
        												if ($auteurs_poubeller != 1) {
        													  spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '1comite' LIMIT 1");
            												if (mysql_error() == 0) {
                											 $TresR_eff[] = $login;
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
        // optimisation de la table apr�s les effacements
        						spip_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles");
        						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
        							 spip_query("OPTIMIZE TABLE $textra");
        						}
/*										
        						if ($extra_supCSV == 1) {
        							 spip_query("OPTIMIZE TABLE $textracsv");
        						}						
*/										
        				}		
        }
      // 4.3.3 : traitement des administrateurs restreints actuels de la base spip_auteurs => si effacer les absA = OK
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
      // l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
        							 if ($data4563['nbA'] == 0) {
    						// traitement �ventuel des articles de l'admin � supprimer
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
    							// traitement des admins � effacer												
    												if ($auteurs_poubellea != 1) {
    													  spip_query("DELETE FROM $Tauteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '0minirezo' LIMIT 1");
        												if (mysql_error() == 0) {
            											 $TresA_eff[] = $login;
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
    // optimisation de la table apr�s les effacements
    						spip_query("OPTIMIZE TABLE $Tauteurs, $Tarticles, $Tauteurs_articles");
    						if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $intitule_ss_grpe !== 0) {
    							 spip_query("OPTIMIZE TABLE $textra");
    						}
/*								
    						if ($extra_supCSV == 1) {
    							 spip_query("OPTIMIZE TABLE $textracsv");
    						}
*/														
    				}
				}   
//				}
    //   fin effacer les abs (4.3)  V 2.3	

				 
				debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif", false, "", _T('csvspip:titre_etape4'));
//				echo "<h2>"._T('csvspip:titre_etape4')."</h2>";
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

			  if (count($TerrP_nvx) > 0) {		
			 		echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
					foreach ($TerrP_nvx as $Pen) { 
 									echo _T('csvspip:utilisateur').$Pen['login']._T('csvspip: erreur').$Pen['erreur']."<br>";
					}
					echo "</span>";
			 	  $err_total ++;
			  }
			  else {
			 			echo "<br>"._T('csvspip:creation').count($TresP_nvx)._T('csvspip:comptes_admin_ok')."<br>";					 			
			  }

			  if ($_POST['maj_mdp'] == 1) { 					
			 		echo "<br>"._T('csvspip:etape4.2')."<br>";
      		if (count($TerrV_maj) > 0) {
    					echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
      			  foreach ($TerrV_maj as $Vem) { 
    	 		 						echo _T('csvspip:visit').$Vem['login']._T('csvspip: erreur').$Vem['erreur']."<br>";
    				  }		
    					echo "</span>";
    		 		  $err_total ++;
    			}
    			else {
    						 echo "<br />"._T('csvspip:maj_mdp').count($TresP_maj)._T('csvspip:comptes_visit_ok')."<br>";
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
    					 echo "<br>"._T('csvspip:maj_mdp').count($Tres_maj)._T('csvspip:comptes_redac_ok')."<br>";							
    			} 
      		if (count($TerrP_maj) > 0) {
    					echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
      			  foreach ($TerrP_maj as $Pem) { 
    	 		 						echo _T('csvspip:admin').$Pem['login']._T('csvspip: erreur').$Pem['erreur']."<br>";
    				  }		
    					echo "</span>";
    		 		  $err_total ++;
    			}
    			else {
    						 echo "<br />"._T('csvspip:maj_mdp').count($TresP_maj)._T('csvspip:comptes_admin_ok')."<br>";
    			}  					
			  }
				
//			  if ($_POST['eff_abs'] == 1) {  					

// ajout VERSION 2.3 : effacer les absents visits/redacs/admins
		// effacer les visiteurs
				if ($eff_absv == 1) {  					
					 echo "<br />"._T('csvspip:etape4.3.1')."<br>";
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
			<h3>Etape 4.3.1.1 : suppression des r�f�rences aux visiteurs supprim�s dans la table suppl�mentaire :</h3>
<?				 			 if (count($TerrV_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des r�f�rences dans la table suppl�mentaire : visiteurs en erreur :</h4>
<?							    foreach ($TerrV_eff_extra as $Vefx) { ?>
<?					 		 				 print 'r�dacteur = '.$Vefx['login'].' => erreur = '.$Vefx['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des r�f�rences dans la table suppl�mentaire pour <? print count($TresV_eff_extra); ?> visiteurs = OK<br>
<?							 }
							}  					
*/
				}  					
		
		// effacer les redacteurs
				if ($eff_absr == 1) { 
					 echo "<br />"._T('csvspip:etape4.3.2')."<br>";
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
			<h3>Etape 4.3.2.1 : suppression des r�f�rences aux r�dacteurs supprim�s dans la table suppl�mentaire :</h3>
<?				 			 if (count($TerrR_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des r�f�rences dans la table suppl�mentaire : r�dacteurs en erreur :</h4>
<?							    foreach ($TerrR_eff_extra as $Refx) { ?>
<?					 		 				 print 'r�dacteur = '.$Refx['login'].' => erreur = '.$Rex['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des r�f�rences dans la table suppl�mentaire pour <? print count($TresR_eff_extra); ?> r�dacteurs = OK<br>
<?							 }
							}  					
*/
				}  					

		// effacer les admins
				if ($eff_absa == 1) { 			
					 echo "<br />"._T('csvspip:etape4.3.3')."<br>";
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
/*
							if ($ch_ssgrpe == 3 AND $extra_sup != 0 AND $extraa != 0) {  ?>					
			<h3>Etape 4.3.3.1 : suppression des r�f�rences aux administrateurs supprim�s dans la table suppl�mentaire :</h3>
<?				 			 if (count($TerrA_eff_extra) >0) {		?>
			<span class="Cerreur">
					<h4>suppressions des r�f�rences dans la table suppl�mentaire : administrateurs en erreur :</h4>
<?							    foreach ($TerrA_eff_extra as $Aefx) { ?>
<?					 		 				 print 'r�dacteur = '.$Aefx['login'].' => erreur = '.$Aex['erreur']; ?><br>
<?							    }		 ?>
			</span>
<?					 		    $err_total ++;
								 }
								 else {  ?>
									 <br>Suppression des r�f�rences dans la table suppl�mentaire pour <? print count($TresA_eff_extra); ?> administrateurs = OK<br>
<?							 }
							}  					
*/
				}
				
// fin effacer les absents V 2.3
/*	OLD : effacer les absents V 2.2
				  if ($_POST['archivage'] != 0) {  
						 echo "<br>"._T('csvspip:archivage_debut').$cteur_articles_deplaces._T('csvspip:archivage_fin').$nom_rub_archives."<br>";
				  }  
				  if ($_POST['supprimer_articles'] == 1) { 
						 echo "<br>"._T('csvspip:suppression_debut').$cteur_articles_supprimes._T('csvspip: suppression_fin')."<br>";
				  }
//			  }  	
*/	
				fin_cadre_couleur();			
		
// �tape 5 : si n�cessaire int�gration des admins comme administrateurs restreints � la rubrique de leur sous-groupe
	 			if ($_POST['rub_prof'] == 1 AND $groupe_admins != '-1') {
  					 $Terr_adm_rub = array();
  					 $Tres_adm_rub = array();
						 $sql54 = spip_query("SELECT ss_groupe, nom, id_spip FROM tmp_auteurs WHERE groupe = '$groupe_admins' AND ss_groupe != '' ORDER BY ss_groupe");
						 while ($data54 = spip_fetch_array($sql54)) {
						 			 $login_adm_ec = strtolower($data54['nom']);
									 $id_adm_ec = $data54['id_spip'];
									 $ss_grpe_ec = $data54['ss_groupe'];
									 $sql55 = spip_query("SELECT id_rubrique FROM $Trubriques WHERE titre = '$ss_grpe_ec' LIMIT 1");
									 $data55 = spip_fetch_array($sql55);
									 $id_rubrique_adm_ec = $data55['id_rubrique'];
//									 $sql56 = spip_query("SELECT id_auteur FROM $Tauteurs WHERE login = '$login_adm_ec' AND statut = '0minirezo' LIMIT 1");
//									 $data56 = spip_fetch_array($sql56);
//									 $id_adm_ec = $data56['id_auteur'];

									 $sql57 = spip_query("SELECT COUNT(*) AS existe_adm_rub FROM $Tauteurs_rubriques WHERE id_auteur = '$id_adm_ec' AND id_rubrique = '$id_rubrique_adm_ec' LIMIT 1");
									 $data57 = spip_fetch_array($sql57);
									 $nb57  = $data57['existe_adm_rub']; 
									 if ($nb57 == 0) {
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
				 	
// Etape 6 : si n�cessaire cr�ation d'un article par rubrique 					
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
				 $_SESSION['csv2spip_err'] = ''; 
echo "<script language=\"JavaScript\"> ";
echo "				function aff_masq(id_elem, vis) { ";
echo "								 vis == 0 ? s_vis = 'none' : s_vis = 'block'; ";
echo "								 document.getElementById(id_elem).style.display = s_vis; ";
echo "								 document.getElementById(id_elem).style.display = s_vis; ";
echo "								 this.checked = 'checked'; ";			 
echo "				}";
echo "</script>";

//         debut_cadre_formulaire();
      	 echo "\r\n<form name=\"csv2spip\" enctype=\"multipart/form-data\" action=\"".$PHP_SELF."?exec=csv2spip\" method=\"post\" onsubmit=\"return (verifSaisie());\">";
    		 debut_cadre_couleur("cal-today.gif", false, "", _T('csvspip:titre_choix_fichier'));
//    		 echo "<h3>"._T('csvspip:titre_choix_fichier')."</h3>";
         echo "<strong>"._T('csvspip:choix_fichier')."</strong><input name=\"userfile\" type=\"file\">";
			 	 echo "<br><br /><strong>"._T('csvspip:nom_groupe_admin')."</strong><input type=\"text\" name=\"groupe_admins\" value=\"ADMINS\">";
				 echo "<br><br /><strong>"._T('csvspip:nom_groupe_visit')."</strong><input type=\"text\" name=\"groupe_visits\" value=\"VISITEURS\">";
       	 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_nom_groupe_admin')."</span>";		
				 
				 fin_cadre_couleur();
				 debut_cadre_couleur("mot-cle-24.gif", false, "", _T('csvspip:options_maj'));
//         echo "<h3>"._T('csvspip:options_maj')."</h3>";
    		 echo "<strong>"._T('csvspip:maj_mdp')."</strong>"; 
    		 echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_mdp\" value=\"1\"  checked=\"checked\">"; 
    		 echo "<input type=\"radio\" name=\"maj_mdp\" value=\"0\">"._T('csvspip:non');
				 fin_cadre_couleur();
				 debut_cadre_couleur("../"._DIR_PLUGIN_CSV2SPIP."/img_pack/supprimer_utilisateurs-24.gif", false, "", _T('csvspip:suppr_absents'));
//    		 echo "<h3>"._T('csvspip:suppr_redac')."</h3>";
    		 echo "<strong>"._T('csvspip:suppr_utilis')."</strong><ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
				 echo "<li style=\"list-style-image: url('img_pack/redac-12.gif');\">"._T('csvspip:suppr_redac')."";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_redac\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				 echo "<input type=\"radio\" name=\"eff_redac\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_admin[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non');
				 echo "</li><li style=\"list-style-image: url('img_pack/admin-12.gif');\">"._T('csvspip:suppr_admin');
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_admin\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
				 echo "<input type=\"radio\" name=\"eff_admin\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_redac[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non'); 
				 echo "</li>";
				 echo "<li style=\"list-style-image: url('img_pack/visit-12.gif');\">"._T('csvspip:suppr_visit')."";
				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_visit\" value=\"1\" >";
				 echo "<input type=\"radio\" name=\"eff_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non');
				 echo "</li>";				 

//				 echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_abs\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
//    		 echo "<input type=\"radio\" name=\"eff_abs\" value=\"0\" checked=\"checked\" onClick=\"aff_masq('archi', 0);\">"._T('csvspip:non'); 

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
				 echo "<input type=\"text\" name=\"rub_archivage\" value=\"Archives ann�e ".($annee - 1).'-'.$annee."\" style=\"width: 200px;\">";
		  	 echo "";
			   if ($nb_rubriques > 0) {   		
		  	    echo"<br><br><strong>"._T('csvspip:choix_parent_archive')."</strong>"; 
        		echo "<select name=\"rub_parent_archivage\">";
        		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
				    $sql10 = spip_query("SELECT id_rubrique, titre, id_secteur FROM $Trubriques ORDER BY id_rubrique");
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
			 echo "<br /><br /><div id=\"rub_adm\" class=\"cadre\"><strong>"._T('csvspip:article_rubrique')."</strong>"; 
       echo _T('csvspip:oui')."<input type=\"radio\" name=\"art_rub\" value=\"1\">";   
       echo "<input type=\"radio\" name=\"art_rub\" value=\"0\" checked=\"checked\">"._T('csvspip:non');
			 echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_articles')."</span>";
 			 if ($nb_rubriques > 0) {   		
				  echo "<br><br /><strong>"._T('csvspip:choix_parent_rubriques')."</strong>"; 
      		echo "<select name=\"rub_parent\">";
      		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
				  $sql10 = spip_query("SELECT id_rubrique, titre, id_secteur FROM $Trubriques ORDER BY id_rubrique");
					while ($data10 = spip_fetch_array($sql10)) { 
				 			  echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
			 		}  	
		      echo "</select><br>";
			 }
		 	 else {  
				  	echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
			 } 		
			 echo "</div>";
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
				
		fin_page();
}
		 
		 

?>
