<?

//ce fichier est apellé pour traitement du formulaire de postage d'un nouveau fichier plugin.xml
// pas de retour html, true | $msg d'erreur pour pouvoir faire une réponse ahah par ex




// fct pour extraction des fichiers de config xml directement sous forme d'un tableau PHP
//     simule la fct correspondante de php5 ?		
//     $xml_tete = extraction brute du fichier xml ss forme d'une string
//		 $xml_balise = nom de la balise conteneur des items à extraire
//     $xml_style = style de xml utilisé : attr | bal
//		 		 style attr : <balise><balise:attribut1>valeur 1</balise:attribut1><balise:attribut2>valeur 2</balise:attribut2></balise>
//				 style bal : (celui utilisé dans plugin.xml) : <balise>valeur</balise><balise1><ss_baliseA>valeurA</ss_baliseA><ss_baliseB>valeur B</ss_baliseB></balise1>

//     retourne $Tretour = array('attribut1' => 'valeur 1', 'attribut2' => 'valeur 2'...)
//     ou       $Tretour[$cle_elems] = array('attribut1' => 'valeur 1', 'attribut2' => 'valeur 2'...)
		function mag_maj_domxml_xmltree($xml_tete, $xml_style = "bal", $xml_balise = "", $cle_elems = "nom") {
						 $Tretour = array();
						 
// ça c'est le cas ou on utilise $xml_style = "attr"
//		 fichier xml ultra-simplifié : de la forme : <balise><balise:attribut1>valeur 1</balise:attribut1><balise:attribut2>valeur 2</balise:attribut2></balise>
			 			 if ($xml_style == 'attr') {
						 		 $reg_exp1 = "|<$xml_balise>.*</$xml_balise>|sxiU";
						 		 $reg_exp2 = "|<$xml_balise:([-a-zA-Z0-9_]*)>([^<>]*)</$xml_balise:[-a-zA-Z0-9_]*>|sxiU";
    						 preg_match_all($reg_exp1, $xml_tete, $Ttmp1);
    						 for ($m = 0; $m < count($Ttmp1[0]); $m++) {
    								// attention ici il y a une embrouille pour que le tableau généré soit en plus généré de la forme : $Tretour[$cle_elems] = $Tretour
    						 		 	 $Tretour_ec = array();
    									 $val_cle_ec = '';
    						 			 preg_match_all($reg_exp2, $Ttmp1[0][$m], $Ttmp2);
    									 for ($n = 0; $n < count($Ttmp2[0]); $n++) {
    									 				 if ($cle_elems != '' AND $Ttmp2[1][$n] == $cle_elems) {
    													 		$val_cle_ec = mag_maj_html_accents(mag_maj_iso_accents($Ttmp2[2][$n]));
    													 }
    												// méchant hack pour les problèmes de jeux de caractères 
    													 $Tretour_ec[$Ttmp2[1][$n]] = mag_maj_html_accents(mag_maj_iso_accents($Ttmp2[2][$n]));
    									 }
    						 			 if ($val_cle_ec != '') {
    									 		$Tretour[$val_cle_ec]= $Tretour_ec;
    									 }
    									 else {
    									 			$Tretour[] = $Tretour_ec;
    									 }									 
    						 }
						 }
// ça c'est le cas ou on utilise $xml_style = "bal"
//	style bal : (celui utilisé dans plugin.xml) : <balise>valeur</balise><balise1><ss_baliseA>valeurA</ss_baliseA><ss_baliseB>valeur B</ss_baliseB></balise1>			 
						 else {
//echo '<br>$xml_tete = '.$xml_tete;
// !!! TO DO incomplet => ça gère que les balises simples, pas les imbriquées
						 			$Texp = explode('<plugin>', $xml_tete);
									$xml_tete = $Texp[1];
									$reg_exp1 = "|<([a-z0-9_]*)>(.*)</|sxiU";
									preg_match_all($reg_exp1, $xml_tete, $Ttmp1);
							// le tableau généré de la forme : $Tretour[$nom_balise] = $Tvaleur
									for ($m = 0; $m < count($Ttmp1[1]); $m++) {
											$Tretour[$Ttmp1[1][$m]] = addslashes(trim(strip_tags($Ttmp1[2][$m])));
									}
//echo '<br>$Ttmp1 retourne : ';
//print_r($Ttmp1);
						 }
//print '<br>$Tretour = ';
//print_r($Tretour);
						 return $Tretour;						 
		}

// !!! TO DO fonction de remplacement des caractères accentués pour sortie HTML des chaînes (il en manque plein ! => A SPIPER !!!
	  function mag_maj_iso_accents($chaine) {
						$Trech_iso = array("/Ã©/", "/Ã¨/", "/Ãª/", "/Ã«/", "/Ã¢/", "/Ã¹/", "/Ã¯/", "/Ã®/", "/Ã§/", "/Ã/");
						$Tremp_iso = array("é", "è", "ê", "ë", "â", "ù", "ï", "î", "ç", "à");
						$chaine = preg_replace($Trech_iso, $Tremp_iso, $chaine);
						return $chaine;
		}  
		
		function mag_maj_html_accents($chaine) {
            $Trech_accents = array("/é/", "/è/", "/ê/", "/ë/", "/à/", "/â/", "/ù/", "/ï/", "/î/", "/ç/");
            $Tremp_accents = array("&eacute;", "&egrave;", "&ecirc;", "&euml;", "&aacute;", "&acirc;", "&ugrave;", "&iuml;", "&icirc;", "&ccedil;");
						$chaine = preg_replace($Trech_accents, $Tremp_accents, $chaine);
						return $chaine; 
		}						 
						 

function editer_mag_maj() {
  // PARAMETRES A RENSEIGNER 
	  $num_secteur = 41;    // le numéro du secteur contenant les rubriques de _test_ et _stable_ 
		$num_rub_test = 42;  // le numéro de la rubrique _test_
		$num_rub_stable = 43;  // le numéro de la rubrique _stable_
		
		$rep_data = 'tmp/data';   // compatibilié 1.9.2 (reps déplacés)
//    $rep_data = 'ecrire/data';    // compatibilité 1.9.1 (reps "standards")		
				
 // FIN PARAMETRES A RENSEIGNER		
 
// 	include_spip("ecrire/inc/presentation");
  // vérifier les droits
 		global $auteur_session;
		$connect_statut = $auteur_session['statut'];
		$id_utilisateur = $auteur_session['id_auteur'];	
	  if ($connect_statut != '1comite' AND $connect_statut != '0minirezo') {    
		$msg = _T('avis_non_acces_page');
		return $msg;
	}

 // config des noms de tables SPIP
	$Tarticles = "spip_articles";
	$Tauteurs_articles = "spip_auteurs_articles";
	$Trubriques = "spip_rubriques";


// traitement des données envoyées par le formulaire
	 $msg = "";
  // étape 1 : téléchargement du fichier sur le serveur		
   if ($_FILES['userfile']['name'] != '') {  
				if ($_FILES['userfile']['error'] != 0) { 
				 		$msg .= "<br><span class=\"Cerreur\"><:mag_maj:err_chargement_fichier_debut:>".$_FILES['userfile']['tmp_name']."<:mag_maj:err_chargement_fichier_fin:>".$_FILES['userfile']['error']."</span>";				 							 
						exit();
			 	} 
     		$nom_fich = $rep_data."/tmp_plugin.xml";	
    	 	if (!move_uploaded_file($_FILES['userfile']['tmp_name'], "$nom_fich")) {  
					  $msg .= "<br><span class=\"Cerreur\">"._T('mag_maj:err_chargement_fichier_debut').$_FILES['userfile']['tmp_name']."<:mag_maj:err_chargement_fichier_fin:>".$nom_fich."</span>";
		    	 exit();
		   	}
    	 	$tmp_csv_slh = addslashes($nom_fich);	

			  $Terr_rub = array();
			  $Terr_art = array();
			 
			 
     // extraction des données du fichier tmp_plugin.xml	
  			$xml_conf_ec = file_get_contents($nom_fich);
     // passage des données xml du fichier de config en cours dans le tableau $Tconf_ec[] = array('attribut1_balise' => 'valeur 1', 'attribut2_balise' => 'valeur 2' ...)
  			$Tfichiers_config = mag_maj_domxml_xmltree($xml_conf_ec, 'bal');
//echo '<br>$Tfichiers_config = ';
//print_r($Tfichiers_config);
		 		
		 // on trouve l'id_rubrique du plugin s'il existe déja sinon on crée la rubrique
				if ($Tfichiers_config['etat'] == 'dev') {
					 $msg .= "<br><strong>les plugins dev ne sont pas g&eacute;r&eacute;s : utilisez svn://zone.spip.org/spip-zone/_plugins_/_dev_</strong>";
					 exit;
				}
				$Tfichiers_config['etat'] == 'stable' ? $id_parent_rub = $num_rub_stable : $id_parent_rub = $num_rub_test;
				$prefix = $Tfichiers_config['prefix'];
				
				$sql = "SELECT id_rubrique from $Trubriques WHERE titre = '$prefix' LIMIT 1";
				$result = spip_query($sql);
		 // la rubrique existe on récup son id
//echo '<br><br>$sql trouve rubrique existante = '.$sql;				
				if (spip_num_rows($result) > 0) {
					 $row = spip_fetch_array($result);
					 $id_rubrique = $row['id_rubrique'];
				}
		// elle n'existe pas : on la crée et on récup l'id 
				else {
						 $sql_rub = "INSERT INTO $Trubriques (id_rubrique, id_parent, titre, id_secteur) 
						 					   VALUES('', $id_parent_rub, '".$prefix."', $num_secteur)";
//echo '<br><br>$sql_rub = '.$sql_rub;
						 spip_query($sql_rub);
						 										
						 if (mysql_error() != '') {
						 		$msg .= '<br><br>$sql_rub = '.$sql_rub;
								$msg .= "<br><br>mysql_error création rubrique ".$nom_plugin." retourne : ".mysql_error();
						 }
						 else {
						 			$id_rubrique = mysql_insert_id();
						 }
				}
//echo '<br><br>$id_rubrique = '.$id_rubrique;				
		 // correspondance balise plugins / champs table articles 
				$Tfichiers_config['urls'] = ($Tfichiers_config['documentation_url'] ? 'doc|'.$Tfichiers_config['documentation_url'] : '');
				$Tfichiers_config['urls'] .= ($Tfichiers_config['telechargement_url'] ? '::zip|'.$Tfichiers_config['telechargement_url'] : '');
				$Tfichiers_config['urls'].= ($Tfichiers_config['svn_url'] ? '::svn|'.$Tfichiers_config['svn_url'] : '');
				
				$Tcorres = array('surtitre' => 'version',
								 	 			 'titre' => 'nom',
												 'soustitre' => 'prefix',
												 'descriptif' => 'description',
												 'chapo' => 'auteur',
												 'texte' => 'pipeline',
												 'ps' => 'etat',
												 'url_site' => 'urls',
												 'nom_site' => 'options',
												 'extra' => 'fonctions'
												 );
				$date_maj = date("Y-m-d H:i:s   ");
				foreach ($Tcorres as $s => $pxml) {
//echo '<br>$s = '.$s.' $pxml = '.$pxml;
								$sql_sup1 .= ", ".$s;
								$sql_sup2 .= ", '".$Tfichiers_config[$pxml]."'";
				}
				$sql = "INSERT INTO $Tarticles (id_article, id_rubrique, id_secteur, statut, date ".$sql_sup1
						 	 				 .") VALUES('', $id_rubrique, $num_secteur, 'publie', '$date_maj' ".$sql_sup2.")";
				spip_query($sql);
				if (mysql_error() != '') {
					 $msg .= '<br><br>$sql article = '.$sql;
					 $msg .= '<br><br>mysql_error pour le INSERT renvoie : '.mysql_error();
				}
    		$id_article = mysql_insert_id();
				$sql_aut = "INSERT INTO $Tauteurs_articles (id_article, id_auteur) VALUES ($id_article, $id_utilisateur)";
    		spip_query($sql_aut);
    		if (mysql_error() != '') {
    			 $msg .= '<br>$sql_aut = '.$sql_aut;
					 $msg .= '<br>insertion auteur_article : mysql_error => '.mysql_error();
    		}
				$sql_rub_publie = "UPDATE spip_rubriques SET statut = 'publie' WHERE id_rubrique = $id_rubrique OR id_rubrique = $id_parent_rub";
				spip_query($sql_rub_publie);
				if (mysql_error() != '') {
    			 $msg .= '<br>$sql_rub_publie = '.$sql_rub_publie;
					 $msg .= '<br>publication rubrique et rubrique type_plugin : mysql_error => '.mysql_error();
    		}
				
	}
// si tout s'est bien passé, $msg est vide...
	$msg = ($msg == '' ? "int&eacute;gration du fichier plugin.xml de ".$Tfichiers_config['prefix'].' v.'.$Tfichiers_config['version']." : <strong>OK</strong>" : $msg);
	$msg = "<span class=\"retour\">".$msg."</span><br />";
	return $msg;
	
	
	
   
}

?>				 