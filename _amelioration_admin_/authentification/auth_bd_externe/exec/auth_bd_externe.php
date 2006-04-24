<?php
require_once(dirname(__FILE__).'/../inc/meta_auth_bd_externe.php');
require_once(dirname(__FILE__).'/../inc/utils.php');

function exec_auth_bd_externe(){
	global $connect_statut;
	global $bd_externe;
	global $bd_externe_link;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	global $couleur_foncee;
	include_ecrire("inc_presentation");
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('titre'), "auth_bd_externe", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}		
	
	debut_page(_T('authbdexterne:titre_plugin'), "auth_bd_externe", "plugin");
	echo "<br/><br/><br/>";
																 
	gros_titre(_T('authbdexterne:titre_plugin'));	
	
	debut_gauche();
	debut_boite_info();
	echo _T('info_gauche_admin_tech');
	fin_boite_info();


	debut_droite();


	// TRAITEMENT DU FORMULAIRE DE CONNEXION AU SERVEUR DE BD (etape1)
	if ( ($_POST['param_connexion_db']=="etape1") OR ($_POST['param_connexion_db']=="etape2") OR ($_POST['param_connexion_db']=="etape3") ) {
		$bd_externe['serveur']=$_POST['bd_serveur_type'];
		$bd_externe['hostname']=$_POST['bd_hostname'];
		$bd_externe['login']=$_POST['bd_login'];
		$bd_externe['password']=$_POST['bd_password'];
		$bd_externe['database']=$_POST['bd_database'];
		$reinit_table=$_POST['reinit_table'];		
		if ($_POST['param_connexion_db']=="etape1") $bd_externe['table']=""; // Pour cohérence si on recommence tout à partir de l'etape 1
	}
	else $reinit_table=FALSE;

	// TRAITEMENT DU FORMULAIRE TABLE(S) DE LA BASE EXTERNE (etape2)
	if (($_POST['param_connexion_db']=="etape2") OR ($_POST['param_connexion_db']=="etape3")) {
		$bd_externe['table']=$_POST['bd_table'];
		$bd_externe['champ_cle']=$_POST['bd_champ_cle'];
		$bd_externe['table_jointure']=$_POST['bd_table_jointure'];
	}
	else {$bd_externe['table']="";$bd_externe['champ_cle']="";$bd_externe['table_jointure']="";}
	
	// TRAITEMENT DU FORMULAIRE CHAMPS DE LA BASE EXTERNE (etape3)
	if ($_POST['param_connexion_db']=="etape3") {	
		$bd_externe['champ_login_ext']=$_POST['bd_champ_login_ext'];
		$bd_externe['champ_passwd']=$_POST['bd_champ_passwd'];
		$bd_externe['type_passwd']=$_POST['bd_type_passwd'];
		$bd_externe['champ_alea']=$_POST['bd_champ_alea'];
		
		$bd_externe['champ_prenom']=$_POST['bd_champ_prenom'];
		$bd_externe['champ_nom']=$_POST['bd_champ_nom'];
		$bd_externe['champ_bio']=$_POST['bd_champ_bio'];
		$bd_externe['champ_email']=$_POST['bd_champ_email'];
		$bd_externe['champ_nom_site']=$_POST['bd_champ_nom_site'];
		$bd_externe['champ_url_site']=$_POST['bd_champ_url_site'];
		$bd_externe['champ_pgp']=$_POST['bd_champ_pgp'];
		
		$bd_externe['champ_statut']=$_POST['bd_champ_statut'];
		$bd_externe['val_redacteur']=$_POST['bd_val_redacteur'];
		$bd_externe['val_administrateur']=$_POST['bd_val_administrateur'];
	}

	// RAZ CHAMPS POUR COHERENCE PARAMETRAGE SUITE A UN CHANGEMENT DE TABLE
	if ($reinit_table=="oui") {		
		$bd_externe['champ_cle']="";
		$bd_externe['table_jointure']="";
		$bd_externe['champ_login_ext']="";
		$bd_externe['champ_passwd']="";
		$bd_externe['champ_alea']="";
		$bd_externe['champ_prenom']="";
		$bd_externe['champ_nom']="";
		$bd_externe['champ_bio']="";
		$bd_externe['champ_email']="";
		$bd_externe['champ_nom_site']="";
		$bd_externe['champ_url_site']="";
		$bd_externe['champ_pgp']="";
		$bd_externe['champ_statut']="";
		$bd_externe['val_redacteur']="";
		$bd_externe['val_administrateur']="";			
	}
		
	// ENREGISTREMENT DES PARAMETRES SI POST DU FORMULAIRE
	if ( ($_POST['param_connexion_db']=="etape1") OR ($_POST['param_connexion_db']=="etape2") OR ($_POST['param_connexion_db']=="etape3") )	ecrire_parametrage_auth_bd_externe ($bd_externe);

	// LECTURE DES PARAMETRES STOCKEES DANS spip_meta
	$bd_externe=lire_parametrage_auth_bd_externe ();

	// VERIFICATION : PARAMETRAGE CONNEXION RENSEIGNE
	$bd_externe['parametrage_serveur_saisi']=FALSE;	
	if (($bd_externe['login']) AND ($bd_externe['password']) AND ($bd_externe['database'])) $bd_externe['parametrage_serveur_saisi']=TRUE;	

	// VERIFICATION : CONNEXION AU SERVEUR DE BD OK	
	$bd_externe['parametrage_serveur_ok']=FALSE;
	if ($bd_externe['parametrage_serveur_saisi']) {		
		if ($bd_externe['serveur']=="mysql") require_once(dirname(__FILE__).'/../inc/mysql_query.php');
		else require_once(dirname(__FILE__).'/../inc/pear_query.php');	
		$bd_externe['parametrage_serveur_ok']=bd_externe_connect(FALSE);
		}
	

	// AFFICHAGE ZONE DE PARAMETRAGE CONNEXION AU SERVEUR DE BD DU FORMULAIRE (etape1)
	debut_cadre_relief("", false, "", _T('authbdexterne:titre_param_connexion_db'));		
	echo generer_url_post_ecrire('auth_bd_externe','','auth_bd_externe_form');
			
	// Affectation de la liste des serveurs pris en charge
	if ($is_pearDB=@include_once("DB.php"))  $serveurs=array( "mysql"=>"MySQL",
																														"pgsql"=>"PostgreSQL",
																														"ibase"=>"InterBase",
																														"msql"=>"Mini SQL",
																														"mssql"=>"Microsoft SQL Server",
																														"oci8"=>"Oracle 7/8/8i",
																														"odbc"=>"ODBC (Open Database Connectivity)",
																														"sybase"=>"SyBase",
																														"ifx"=>"Informix",
																														"fbsql"=>"FrontBase");

	else {		
		// Si Pear DB n'est pas atteignable, on ne propose que mysql et en informe l'utilisateur
		$serveurs=array( "mysql"=>"MySQL");
		$bd_externe['serveur']="mysql";
		echo "<p align='justify'>",
					http_img_pack('warning.gif', _T('info_avertissement'), "width='48' height='48' align='right'"),
					_T('authbdexterne:pear_warning'),
					"</p><br />";
		}
		
	// Liste de sélection du serveur de BD	
	afficheListeAvecLabel(_T('authbdexterne:info_serveur'),'bd_serveur_type',$serveurs,$bd_externe['serveur']);
	
	// Zones de saisie des parametres de connexion au serveur de BD
	echo "<p>"._T('authbdexterne:info_serveur_hostname')." : ";
	echo "<INPUT TYPE='text' NAME='bd_hostname' CLASS='fondl' VALUE=\"".$bd_externe['hostname']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_login')." : ";
	echo "<INPUT TYPE='text' NAME='bd_login' CLASS='fondl' VALUE=\"".$bd_externe['login']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_password')." : ";
	echo "<INPUT TYPE='password' NAME='bd_password' CLASS='fondl' VALUE=\"".$bd_externe['password']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_database')." : ";
	echo "<INPUT TYPE='text' NAME='bd_database' CLASS='fondl' VALUE=\"".$bd_externe['database']."\" SIZE='15' $onfocus></p>";
	
	// Bouton de validation de l'etape 1
	echo "<INPUT NAME='param_connexion_db' VALUE='etape1' TYPE='hidden'>\n";
	echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";
		
	// Affichage d'un avertissement si la connexion echoue alors que les parametres sont renseignes
	if (($bd_externe['parametrage_serveur_saisi']) AND (!$bd_externe['parametrage_serveur_ok'])) echo "<p align='justify'>",http_img_pack('warning.gif', _T('info_avertissement'), "width='48' height='48' align='right'"),_T('authbdexterne:db_connect_warning'),"</p><br />";	

	fin_cadre_relief();
	echo "<br />";
		

	// AFFICHAGE DU FORMULAIRE TABLE(S) DE LA BASE EXTERNE (etape2)
	if ($bd_externe['parametrage_serveur_ok']) {
						
		debut_cadre_relief("", false, "", _T('authbdexterne:titre_param_db'));		
				
		// Affichage Aide		
		echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide1')."</p>";
		
		// Liste de selection de la table
		$tables=bd_externe_show_tables();		
		afficheListeAvecLabel(_T('authbdexterne:info_bd_table'),'bd_table',$tables,$bd_externe['table'],'onChange="javascript:document.auth_bd_externe_form.reinit_table.value=\'oui\';this.form.submit();"');
		
		// Liste de selection du champ clef parmi les champs existants de la table
		if ($bd_externe['table']!="") $columns=bd_externe_show_columns($bd_externe['table']);
		else $columns[""]="";
		afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_cle'),'bd_champ_cle',$columns,$bd_externe['champ_cle']);
		
		// Liste de selection de la table utilise pour la jointure
		afficheListeAvecLabel(_T('authbdexterne:info_bd_table_jointure'),'bd_table_jointure',$tables,$bd_externe['table_jointure'],'onChange="javascript:this.form.submit();"');
		
		// Bouton de validation etape 2				
		echo "<INPUT NAME='param_connexion_db' VALUE='etape2' TYPE='hidden'>\n";
		echo "<INPUT NAME='reinit_table' VALUE='non' TYPE='hidden'>\n";
		echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";
		
		fin_cadre_relief();
		echo "<br />";
		
		
		// AFFICHAGE DU FORMULAIRE CHAMPS DE LA BASE EXTERNE (etape3)
		
		if (($bd_externe['table']!="") AND ($bd_externe['champ_cle']!="") ) {
			
			debut_cadre_relief("", false, "", _T('authbdexterne:titre_param_champs'));	
			
			// Affectation de la liste des champs possibles : cas d'une seconde table
			if ($bd_externe['table_jointure']!="")
			$columns=array_merge($columns,bd_externe_show_columns($bd_externe['table_jointure']));	
															
			// Titre section			
			echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section2')."</p>";
			
			// Liste de selection du champ login parmi les champs existants	
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_login_ext'),'bd_champ_login_ext',$columns,$bd_externe['champ_login_ext']);
			
			// Liste de selection du champ password parmi les champs existants
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_passwd'),'bd_champ_passwd',$columns,$bd_externe['champ_passwd']);
			
			// Liste de selection du type de cryptage du password
			$cryptage=array("clear_text"=>"mot de passe en clair",
											"md5"=>"cryptage en md5",
											"challenge_md5"=>"challenge md5 (spip)",
											"crypt"=>"cryptage en crypt",
											"unix"=>"cryptage unix (crypt+salt)");
			afficheListeAvecLabel(_T('authbdexterne:info_bd_type_passwd'),'bd_type_passwd',$cryptage,$bd_externe['type_passwd']);
			
			// Liste de selection du champ alea parmi les champs existants
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_alea'),'bd_champ_alea',$columns,$bd_externe['champ_alea']);
			
			echo "<hr />";
			
			// Aide
			echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section3')."</p>";
			echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide3')."</p>";
		
			// Listes de selection des champs optionnels relatifs aux informations personnelles
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_prenom'),'bd_champ_prenom',$columns,$bd_externe['champ_prenom']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_nom'),'bd_champ_nom',$columns,$bd_externe['champ_nom']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_bio'),'bd_champ_bio',$columns,$bd_externe['champ_bio']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_email'),'bd_champ_email',$columns,$bd_externe['champ_email']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_nom_site'),'bd_champ_nom_site',$columns,$bd_externe['champ_nom_site']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_url_site'),'bd_champ_url_site',$columns,$bd_externe['champ_url_site']);
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_pgp'),'bd_champ_pgp',$columns,$bd_externe['champ_pgp']);
			echo "<hr />";
			
			// Aide
			echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section4')."</p>";
			echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide4')."</p>";
			
			// Liste de selection du champ statut parmi les champs existants
			afficheListeAvecLabel(_T('authbdexterne:info_bd_champ_statut'),'bd_champ_statut',$columns,$bd_externe['champ_statut']);

			// Zones de saisies des valeurs associees aux statuts de redacteurs et d'administrateurs
			echo "<p>"._T('authbdexterne:info_bd_val_redacteur')." : ";
			echo "<INPUT TYPE='text' NAME='bd_val_redacteur' CLASS='fondl' VALUE=\"".$bd_externe['val_redacteur']."\" SIZE='15' $onfocus></p>";
			echo "<p>"._T('authbdexterne:info_bd_val_administrateur')." : ";
			echo "<INPUT TYPE='text' NAME='bd_val_administrateur' CLASS='fondl' VALUE=\"".$bd_externe['val_administrateur']."\" SIZE='15' $onfocus></p>";
			
			// Bouton de validation etape 3
			echo "<INPUT NAME='param_connexion_db' VALUE='etape3' TYPE='hidden'>\n";
			echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";
			
			fin_cadre_relief();
		}
		
	}
	
	echo "</form>";		
	
	fin_page();
}

?>