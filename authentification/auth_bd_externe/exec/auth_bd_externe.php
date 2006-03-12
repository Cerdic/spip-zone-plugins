<?php
require_once(dirname(__FILE__).'/../inc/meta_auth_bd_externe.php');


function exec_auth_bd_externe(){
	global $connect_statut;
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

	// TRAITEMENT DU FORMULAIRE DE CONNEXION AU SERVEUR DE BD
	if (($_POST['param_connexion_db']=="etape1") OR ($_POST['param_connexion_db']=="etape2")) {
		$bd_externe['serveur']=$_POST['bd_serveur_type'];
		$bd_externe['hostname']=$_POST['bd_hostname'];
		$bd_externe['login']=$_POST['bd_login'];
		$bd_externe['password']=$_POST['bd_password'];
		$bd_externe['database']=$_POST['bd_database'];
		
		// TRAITEMENT DU FORMULAIRE TABLE ET CHAMPS DE LA BASE EXTERNE
		if ($_POST['param_connexion_db']=="etape2") {
			$bd_externe['table']=$_POST['bd_table'];
			$bd_externe['champ_cle']=$_POST['bd_champ_cle'];
			$bd_externe['table_jointure']=$_POST['bd_table_jointure'];
			
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
	
		ecrire_parametrage_auth_bd_externe ($bd_externe);
	}


	
	// BLOC PARAMETRAGE CONNEXION AU SERVEUR DE BD
	debut_cadre_relief("", false, "", _T('authbdexterne:titre_param_connexion_db'));
	
	
	echo generer_url_post_ecrire('auth_bd_externe');
		

	$bd_externe=lire_parametrage_auth_bd_externe ();

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
		$serveurs=array( "mysql"=>"MySQL");
		$bd_externe['serveur']="mysql";
		echo "<p align='justify'>",
					http_img_pack('warning.gif', _T('info_avertissement'), "width='48' height='48' align='right'"),
					_T('authbdexterne:pear_warning'),
					"</p><br />";
		}
	
	$serveurs=array("mysql"=>"MySQL",
									"pgsql"=>"PostgreSQL",
									"ibase"=>"InterBase",
									"msql"=>"Mini SQL",
									"mssql"=>"Microsoft SQL Server",
									"oci8"=>"Oracle 7/8/8i",
									"odbc"=>"ODBC (Open Database Connectivity)",
									"sybase"=>"SyBase",
									"ifx"=>"Informix",
									"fbsql"=>"FrontBase");
	
	
	// Sélection du serveur de BD	
	echo _T('authbdexterne:info_serveur')." : ";
	echo "\n<select name='bd_serveur_type' class='fondl' align='middle'>\n";
	echo "<option value='".$bd_externe['serveur']."' selected>".$serveurs[$bd_externe['serveur']]."</option>\n";
	reset ($serveurs);
	while (list($key,$val) = each ($serveurs)) {
		if ($key <> $bd_externe['serveur'])
			echo "<option value='$key'>$val</option>\n";
	}
	echo "</select>";

	// Paramètres de connexion	
	echo "<p>"._T('authbdexterne:info_serveur_hostname')." : ";
	echo "<INPUT TYPE='text' NAME='bd_hostname' CLASS='fondl' VALUE=\"".$bd_externe['hostname']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_login')." : ";
	echo "<INPUT TYPE='text' NAME='bd_login' CLASS='fondl' VALUE=\"".$bd_externe['login']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_password')." : ";
	echo "<INPUT TYPE='password' NAME='bd_password' CLASS='fondl' VALUE=\"".$bd_externe['password']."\" SIZE='15' $onfocus></p>";
	echo "<p>"._T('authbdexterne:info_serveur_database')." : ";
	echo "<INPUT TYPE='text' NAME='bd_database' CLASS='fondl' VALUE=\"".$bd_externe['database']."\" SIZE='15' $onfocus></p>";
	
	// Validation étape 1
	echo "<INPUT NAME='param_connexion_db' VALUE='etape1' TYPE='hidden'>\n";
	echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";
	
	
	fin_cadre_relief();
	
	if ($bd_externe['parametrage_serveur_ok']) {
		
		// BLOC PARAMETRAGE BD
		echo "<br /><br />";
		debut_cadre_relief("", false, "", _T('authbdexterne:titre_param_db'));		
		
		
		// Specification de(s) table(s)
		echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section1')."</p>";
		echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide1')."</p>";
		echo "<p>"._T('authbdexterne:info_bd_table')." : ";
		echo "<INPUT TYPE='text' NAME='bd_table' CLASS='fondl' VALUE=\"".$bd_externe['table']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_cle')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_cle' CLASS='fondl' VALUE=\"".$bd_externe['champ_cle']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_table_jointure')." : ";
		echo "<INPUT TYPE='text' NAME='bd_table_jointure' CLASS='fondl' VALUE=\"".$bd_externe['table_jointure']."\" SIZE='15' $onfocus></p>";
		echo "<hr />";

		$cryptage=array("clear_text"=>"mot de passe en clair",
										"md5"=>"cryptage en md5",
										"challenge_md5"=>"challenge md5 (spip)",
										"crypt"=>"cryptage en crypt",
										"unix"=>"cryptage unix (crypt+salt)");
																				 
											
		// Specification des champs pour l'authentification et type de cryptage du mot de passe
		echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section2')."</p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_login_ext')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_login_ext' CLASS='fondl' VALUE=\"".$bd_externe['champ_login_ext']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_passwd')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_passwd' CLASS='fondl' VALUE=\"".$bd_externe['champ_passwd']."\" SIZE='15' $onfocus></p>";
		
		// Sélection cryptage password
		echo _T('authbdexterne:info_bd_type_passwd')." : ";
		echo "\n<select name='bd_type_passwd' class='fondl' align='middle'>\n";
		echo "<option value='".$bd_externe['type_passwd']."' selected>".$cryptage[$bd_externe['type_passwd']]."</option>\n";
		reset ($cryptage);
		while (list($key,$val) = each ($cryptage)) {
			if ($key <> $bd_externe['type_passwd'])
				echo "<option value='$key'>$val</option>\n";
		}
		echo "</select>";		
		
		echo "<p>"._T('authbdexterne:info_bd_champ_alea')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_alea' CLASS='fondl' VALUE=\"".$bd_externe['champ_alea']."\" SIZE='15' $onfocus></p>";
		echo "<hr />";
	
		// Specification des champs optionnels relatifs aux informations personnelles
		echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section3')."</p>";
		echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide3')."</p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_prenom')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_prenom' CLASS='fondl' VALUE=\"".$bd_externe['champ_prenom']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_nom')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_nom' CLASS='fondl' VALUE=\"".$bd_externe['champ_nom']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_bio')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_bio' CLASS='fondl' VALUE=\"".$bd_externe['champ_bio']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_email')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_email' CLASS='fondl' VALUE=\"".$bd_externe['champ_email']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_nom_site')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_nom_site' CLASS='fondl' VALUE=\"".$bd_externe['champ_nom_site']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_url_site')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_url_site' CLASS='fondl' VALUE=\"".$bd_externe['champ_url_site']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_pgp')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_pgp' CLASS='fondl' VALUE=\"".$bd_externe['champ_pgp']."\" SIZE='15' $onfocus></p>";
		echo "<hr />";
		
		// Spécification des champs optionnels relatifs à la détermination du statut
		echo "<p style='font-weight:bold'>"._T('authbdexterne:titre_section4')."</p>";
		echo "<p style='font-size:9px;align:justify'>"._T('authbdexterne:aide4')."</p>";
		echo "<p>"._T('authbdexterne:info_bd_champ_statut')." : ";
		echo "<INPUT TYPE='text' NAME='bd_champ_statut' CLASS='fondl' VALUE=\"".$bd_externe['champ_statut']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_val_redacteur')." : ";
		echo "<INPUT TYPE='text' NAME='bd_val_redacteur' CLASS='fondl' VALUE=\"".$bd_externe['val_redacteur']."\" SIZE='15' $onfocus></p>";
		echo "<p>"._T('authbdexterne:info_bd_val_administrateur')." : ";
		echo "<INPUT TYPE='text' NAME='bd_val_administrateur' CLASS='fondl' VALUE=\"".$bd_externe['val_administrateur']."\" SIZE='15' $onfocus></p>";
		// Validation étape 2
		echo "<INPUT NAME='param_connexion_db' VALUE='etape2' TYPE='hidden'>\n";
		echo "<INPUT TYPE='submit' NAME='Valider' VALUE='"._T('bouton_valider')."' CLASS='fondo'>";
		
		fin_cadre_relief();
	}
	
	echo "</form>";		
	
	fin_page();
}

?>