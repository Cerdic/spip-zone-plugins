<?php
/*
Gestion des acces aux rubriques et articles par groupes d'utilisateurs
Contrib de Coyote, alias JPK ou Jean-Pierre KUNTZ
V0.1 - 16 juillet 2005 Coyote
	version initiale avec gestion multilingue
v0.2 - 02 août 2005 Coyote
	maj pour compatibilité avec MySQL 3.23
	la table jpk_auteurs_groupes devient jpk_groupes_auteurs
	correction affichage des titres de rubriques typo()
v0.3 - 05 août 2005 Coyote
	ajout d'un bouton pour suppression d'un groupe
	transforme tous les mysql_query() en spip_query()
	Correction du test d’accès, utilisation du login au lieu de l’id_auteur
v0.61 - mars 2006 cy_altern
			utilisation d'un préfixe pour les table jpk_xxx
			+ intégration des sous-groupes et des statuts
			+ interface admin enrichie des explorateurs de groupes/sous-groupes et groupes/rubriques	
	v0.7 - juillet 2006 cy_altern
			renommage des tables en prefixeSPIP_accesgroupes_xxx
			+ restrictions séparées possibles pour public/privé
			+ correction bogue suppression/modification
			+ message demande d'inscription au groupe
			+ passage en BOUCLE_xx (ACCESGROUPES) du contrôle d'accès dans les squelettes
			+ critère  accesgroupes_invisible et filtre accesgroupes_visualise pour enrichir les squelettes
	v1.0 - septembre 2006 cy_altern
			passage en plugin pour compatibilité spip 1.9
			nombreuses modifs des requètes SQL et renvoi de certaines clause des WHERE en post-traitement
		pour cause d'alias impossibles dans les clauses WHERE
			(résultat de l'utilisation de abstract_sql de spip 1.9 qui permet d'éviter la détection du préfixe des tables de spip ?)
			+ filtrage des éléments à accès restreint directement dans les requètes SQL des boucles par surcharge des fonctions du core
			+ gestion du cache en fonction des combinaisons de rubriques restreintes pour éviter les mauvaises surprises
			+ possibilité de modifier le propriétaire d'un groupe par les admins généraux
			+ amélioration du traitement des messages de demande d'accès
			+ marquage des groupes désactivés dans l'interface admin

*/


include_spip('base/db_mysql');
include_spip('base/abstract_sql');


function exec_accesgroupes_admin() {
	// définir comme constante le chemin du répertoire du plugin
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$pp = explode("/", end($p));
	define('_DIR_PLUGIN_ACCESGROUPES',(_DIR_PLUGINS.$pp[0]));
	
	$url_auteur = (($GLOBALS['spip_version_code']>1.92)?"auteur_infos":"auteurs_edit");
	$url_naviguer = "naviguer";
	// initialisation d'un éventuel message de maintenance + état d'erreur potentiel
	$msg_text = '';
	$alerte = 0;
	
	// toutes les fonctions utilisées dans accesgroupes_admin uniquement
	include_spip('exec/accesgroupes_admin_fonctions');
	include_spip('base/create');
	
	// initialiser le tableau qui sera utilisé pour la limitation des possibilités de type d'accès
	// pour les rubriques déja restreintes par le groupe en cours (prive+public/prive/public)
	// => cf tableau en dessous du select des rubriques à restreindre
	$Trub_grpe_ec_parent = array();
	global $Trub_grpe_ec_parent;

	// créer les tables accesgroupes_xxx au cas ou elles sont absentes
	creer_base(); //
	
	// les fonctions de spip nécessaires pour afficher les éléments de l'interface
	include_spip("inc/presentation");		

	// les variables spip nécessaires
	global $connect_statut, $connect_toutes_rubriques;
	
	// TRAITEMENT des DONNEES RENVOYEES PAR LES FORMULAIRES : AJOUT - MODIFICATION - SUPPRESSION
	
	//        isset($_GET['groupe'])? $groupe = $_GET['groupe'] : $groupe = 0;
	$groupe = ((isset($_POST['groupe']))? $_POST['groupe'] : ((isset($_GET['groupe']))? $_GET['groupe'] : 0));
	
	// pour traiter les admins restreints
	$id_util_restreint = 0;
	if (accesgroupes_est_admin_restreint() == TRUE) {
		$id_util_restreint = accesgroupes_trouve_id_utilisateur();
		$Trub_restreint =  accesgroupes_cree_Trub_admin ();
	}	
	
	//$msg_text .= 'accesgroupes_est_admin_restreint = '.accesgroupes_est_admin_restreint().' $Trub_restreint = '.print_r($Trub_restreint).'<br>$id_util_restreint ='.$id_util_restreint.' accesgroupes_trouve_id_utilisateur() = '.accesgroupes_trouve_id_utilisateur();
	//$msg_text .= '<br>$_REQUEST = '.print_r($_REQUEST);
	//$msg_text . = '<br>$GLOBALS[auteur_session]= '.$GLOBALS['auteur_session'];
	
	// désactive toutes les fcts de modifs du groupe si admin restreint + pas proprio !!! ATTENTION ce if est extra long !!!
	if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
		
		// AUTEURS ======== gestion des auteurs
		// AUTEUR Ajout
		if (isset($_POST['add_auteur'])){
			$auteur = $_POST['auteur'];
			$sql = "INSERT INTO spip_accesgroupes_auteurs(id_grpacces,id_auteur, dde_acces, proprio)
										VALUES($groupe, $auteur, 0, $id_util_restreint)";
			$result = spip_query($sql);
		}
		
		// AUTEUR Modif = accepter un auteur ayant envoyé un message de demande d'accès
		if (isset($_GET['mod_auteur'])){
			$mod_auteur = $_GET['mod_auteur'];
			if (isset($_GET['message']) AND $_GET['message'] == 'accepte') {
				$sql21 = "SELECT dde_acces FROM spip_accesgroupes_auteurs
														WHERE id_auteur = $mod_auteur
														AND id_grpacces = $groupe
														LIMIT 1";
				$result21 = spip_query($sql21);
				$row21 = spip_fetch_array($result21);
				$id_message_efface = $row21['dde_acces'];
			}
			// modifier l'auteur (dde_acces passe à 0)
			$sql = "UPDATE spip_accesgroupes_auteurs
											SET dde_acces = 0, proprio = $id_util_restreint
											WHERE id_grpacces = $groupe
											AND id_auteur = $mod_auteur";
			spip_query($sql);
			// gérer l'effacement du message à l'admin du groupe + envoyer message retour au demandeur
			if (isset($_GET['message']) AND $_GET['message'] == 'accepte') {
				$sql22 = "DELETE FROM spip_messages
															WHERE id_message = $id_message_efface
															LIMIT 1";
				spip_query($sql22);
				$sql23 = "DELETE FROM spip_auteurs_messages
															WHERE id_message = $id_message_efface";
				spip_query($sql23);
				$sql24 = "SELECT nom FROM spip_accesgroupes_groupes
														WHERE id_grpacces = $groupe
														LIMIT 1";
				$result24 = spip_query($sql24);
				$row24 = spip_fetch_array($result24);
				$nom_groupe = $row24['nom'];
				$titre_mess = addslashes(_T('accesgroupes:titre_message_retour'));
				$message = _T('accesgroupes:message_retour').'<strong>'.$nom_groupe.'</strong>'._T('accesgroupes:message_accepte');
				$message = addslashes($message);
				$sql25 = "SELECT MAX(id_message) AS maxId FROM spip_messages";
				$result25 = spip_query($sql25);
				$row25 = spip_fetch_array($result25);
				$id_forum = $row25['maxId'] + 1;
				$date_pub = date("y-m-d H:i:s");
				$auteur_message = accesgroupes_trouve_id_utilisateur();
				$sql26 = "INSERT INTO spip_messages (id_message, titre, texte, type, date_heure, rv, statut, id_auteur, maj)
												VALUES ($id_forum, '$titre_mess', '$message', 'normal', '$date_pub', 'non', 'publie', $auteur_message, '$date_pub')";
				spip_query($sql26);
				if (mysql_error() == '') {
					$sql27 = "INSERT INTO spip_auteurs_messages (id_auteur, id_message, vu)
																VALUES ($mod_auteur, $id_forum, 'non')";
					spip_query($sql27);
				}
			}
			spip_query("OPTIMIZE TABLE spip_auteurs_messages");
			spip_query("OPTIMIZE TABLE spip_messages");
		}
		
		// AUTEUR Suppression...
		if (isset($_GET['del_auteur']) AND $_GET['del_auteur'] != '') {
			$del_auteur = $_GET['del_auteur'];
			if (isset($_GET['message']) AND $_GET['message']== 'refuse') {
				$sql21 = "SELECT dde_acces FROM spip_accesgroupes_auteurs
														WHERE id_auteur = $del_auteur
														AND id_grpacces = $groupe
														LIMIT 1";
				$result21 = spip_query($sql21);
				$row21 = spip_fetch_array($result21);
				$id_message_efface = $row21['dde_acces'];
			}
			// effacer l'auteur
			$sql = "DELETE FROM spip_accesgroupes_auteurs
											WHERE id_grpacces = $groupe
											AND id_auteur = $del_auteur";
			$result = spip_query($sql);
			// gérer l'effacement du message à l'admin du groupe + envoyer message retour au demandeur
			if (isset($_GET['message']) AND $_GET['message']== 'refuse') {
				$sql22 = "DELETE FROM spip_messages
															WHERE id_message = $id_message_efface
															LIMIT 1";
				spip_query($sql22);
				$sql23 = "DELETE FROM spip_auteurs_messages
															WHERE id_message = $id_message_efface";
				spip_query($sql23);
				$sql24 = "SELECT nom FROM spip_accesgroupes_groupes
														WHERE id_grpacces = $groupe
														LIMIT 1";
				$result24 = spip_query($sql24);
				$row24 = spip_fetch_array($result24);
				$nom_groupe = $row24['nom'];
				$titre_mess = addslashes(_T('accesgroupes:titre_message_retour'));
				$message = _T('accesgroupes:message_retour').'<strong>'.$nom_groupe.'</strong>'._T('accesgroupes:message_refuse');
				$message = addslashes($message);
				$sql25 = "SELECT MAX(id_message) AS maxId FROM spip_messages";
				$result25 = spip_query($sql25);
				$row25 = spip_fetch_array($result25);
				$id_forum = $row25['maxId'] + 1;
				$date_pub = date("y-m-d H:i:s");
				$auteur_message = accesgroupes_trouve_id_utilisateur();
				$sql26 = "INSERT INTO spip_messages (id_message, titre, texte, type, date_heure, rv, statut, id_auteur, maj)
												VALUES ($id_forum, '$titre_mess', '$message', 'normal', '$date_pub', 'non', 'publie', $auteur_message, '$date_pub')";
				spip_query($sql26);
				if (mysql_error() == '') {
					$sql27 = "INSERT INTO spip_auteurs_messages (id_auteur, id_message, vu)
																VALUES ($del_auteur, $id_forum, 'non')";
					spip_query($sql27);
				}
			}
			spip_query("OPTIMIZE TABLE spip_accesgroupes_auteurs");
			spip_query("OPTIMIZE TABLE spip_auteurs_messages");
			spip_query("OPTIMIZE TABLE spip_messages");

		}
		
		// SS-GROUPES ======== gestion des sous-groupes
		// SS-GROUPES Ajout...
		if (isset($_POST['add_ss_groupe'])){
			// vérification que le sous-groupe à créer n'est pas dans l'ascendance du groupe en cours
			// "never trust user" : en principe ce cas n'est pas possible mais un hack du POST est si vite arrivé...
			if (accesgroupes_verifie_inclusions_groupe($_POST['add_ss_groupe'], $groupe) != FALSE) {
				$sql = "INSERT INTO spip_accesgroupes_auteurs(id_grpacces,id_ss_groupe,dde_acces, proprio)
												VALUES($groupe,{$_POST['ss_groupe']}, 0, $id_util_restreint)";
				$result = spip_query($sql);
			}
			else {
				echo _T('accesgroupes:erreur_inclusion_recurrente');
			}
		}
		
		// SS-GROUPES Modif...
		if (isset($_GET['mod_ss_groupe'])){
			$sql = "UPDATE spip_accesgroupes_auteurs SET dde_acces = 0, proprio = $id_util_restreint
										WHERE id_grpacces = $groupe
											AND id_ss_groupe = {$_GET['mod_ss_groupe']}";
			$result = spip_query($sql);
		}
		
		// SS-GROUPES Suppression...
		if (isset($_GET['del_ss_groupe'])){
			$sql = "DELETE FROM spip_accesgroupes_auteurs
										WHERE id_grpacces=$groupe
											AND id_ss_groupe = {$_GET['del_ss_groupe']}";
			$result = spip_query($sql);
		}
		
		// STATUTS ======== gestion des statuts intégrés dans les groupes
		// STATUTS Ajout...
		if (isset($_POST['add_statut'])){
			$sp_statut = $_POST['sp_statut'];
			$sql = "INSERT INTO spip_accesgroupes_auteurs(id_grpacces,sp_statut,dde_acces, proprio)
												VALUES ($groupe,'$sp_statut',0, $id_util_restreint)";
			$result = spip_query($sql);
		}
		
		// STATUTS Modif...
		if (isset($_GET['mod_statut'])){
			$mod_statut = $_GET['mod_statut'];
			$sql = "UPDATE spip_accesgroupes_auteurs
										SET dde_acces = 0, proprio = $id_util_restreint
										WHERE id_grpacces = $groupe AND sp_statut = '$mod_statut'";
			$result = spip_query($sql);
		}
		
		// STATUTS Suppression...
		if (isset($_GET['del_statut'])){
			$del_statut = $_GET['del_statut'];
			$sql = "DELETE FROM spip_accesgroupes_auteurs
										WHERE id_grpacces = $groupe AND sp_statut = '$del_statut'";
			$result = spip_query($sql);
		}
		
		
		// GROUPE ======== gestion des groupes
		// GROUPE Modification
		if (isset($_POST['mod_groupe'])){
			$_POST['actif'] == 1 ? $actif = 1 : $actif = 0;
			$_POST['demandes_acces'] == 1 ? $demande_acces = 1 : $demande_acces = 0;
			$description_modif = addslashes($_POST['description']);
			$nom_modif = addslashes($_POST['nom']);
			$id_grpe_change_proprio = $_POST['groupe'];
			$sql121 = "SELECT proprio
												FROM spip_accesgroupes_groupes
													WHERE id_grpacces = $id_grpe_change_proprio
													LIMIT 1";
			$result121 = spip_query($sql121);
			$row121 = spip_fetch_array($result121);
			$ancien_proprio = $row121['proprio'];
			if (isset($_POST['proprio']) AND $_POST['proprio'] != '' AND $id_util_restreint == 0) {  // chgmnt de proprio autorisé que pour les admins généraux
				$proprio_grpe_modif = $_POST['proprio'];
			}
			else {
				$proprio_grpe_modif =  $ancien_proprio;
			}
			$sql = "UPDATE spip_accesgroupes_groupes
							SET nom='".$nom_modif."',
								description = '".$description_modif."',
								actif = $actif,
											proprio = $proprio_grpe_modif,
												demande_acces = $demande_acces
							WHERE id_grpacces = $id_grpe_change_proprio";
			$result = spip_query($sql);
			accesgroupes_debug($result);
			if (mysql_error() != '') {
				$alerte = 1;
				$msg_text .= _T('accesgroupes:erreur_modif_proprio').$id_grpe_change_proprio.' : '.mysql_error();
			}
			else {	
				// seuls les admins généraux peuvent faire les opérations liées au changement de proprio
				if ($id_util_restreint == 0) {
					// réatribuer tous les accès aux rubriques  de ce groupe dont le proprio est l'ancien proprio ou les admins au nv proprio
					$sql118 = "UPDATE spip_accesgroupes_acces
																SET proprio = \"".$proprio_grpe_modif."\"
																	WHERE id_grpacces = \"".$id_grpe_change_proprio."\"
													AND (proprio = $ancien_proprio
													OR proprio = 0)";
					spip_query($sql118);
					if (mysql_error() != '') {
						$alerte = 1;
						$msg_text .= _T('accesgroupes:erreur_modif_proprio_acces').$id_grpe_change_proprio.' : '.mysql_error();
					}
					else {
						// réatribuer tous les proprios des auteurs appartenants à ce groupe au nv proprio
						// (plus souvenir de pourquoi on a besoin et quand on utilise le proprio des auteurs mais dans le doute... ou si ça doit servir un jour
						$sql119 = "UPDATE spip_accesgroupes_auteurs
																			SET proprio = \"".$proprio_grpe_modif."\"
																			WHERE id_grpacces = \"".$id_grpe_change_proprio."\" ";
						spip_query($sql119);
						if (mysql_error() != '') {
							$alerte = 1;
							$msg_text .= _T('accesgroupes:erreur_modif_proprio_auteurs').$id_grpe_change_proprio.' : '.mysql_error();
						}
					}
				}
			}  // fin des changements de proprios
		}
		
		
	} // fin du IF admin restreints + proprio
	
	// GROUPE Ajout (actif est mis à 1 systématiquement lors de la création d'un groupe)
	if (isset($_POST['add_groupe'])){
		// éviter les duplicatas de noms de groupes
		if (accesgroupes_verifie_duplicata_groupes($_POST['nom']) == FALSE) {
			$msg_text = '<h2 style="color: #f00;">'.$_POST['nom'].' : '._T('accesgroupes:duplicata_nom').'</h2>';
		}
		else {
			if (isset($_POST['proprio']) AND $_POST['proprio'] != '') {
				$proprio_nv_grpe = $_POST['proprio'];
			}
			else {
				$proprio_nv_grpe =  $id_util_restreint;
			}
			$sql = "INSERT INTO spip_accesgroupes_groupes(nom, description, actif, proprio, demande_acces)
								VALUES(\"".addslashes($_POST['nom'])."\",\"".addslashes($_POST['description'])."\", 1, $proprio_nv_grpe, ".$_POST['demandes_acces'].")
				";
			$result = spip_query($sql);
			accesgroupes_debug($result);
			$groupe = mysql_insert_id();
		}
	}
	
	
	// limitation admins restreints + proprio
	if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
		// GROUPE Suppression
		if ( (isset($_POST['del_groupe_all']) AND isset($_POST['groupe']) AND $_POST['groupe'] != '') OR (isset($_GET['del_groupe']) AND $_GET['del_groupe'] != '') ) {
			isset($_GET['del_groupe']) ? $id_grpe_asupr = $_GET['del_groupe'] : $id_grpe_asupr = $_POST['groupe'];
			$sql111 = "DELETE FROM spip_accesgroupes_acces WHERE id_grpacces = ".$id_grpe_asupr;
			spip_query($sql111);
			if (mysql_error() != '') {
				$alerte = 1;
				$msg_text .= _T('accesgroupes:erreur_supr_rubriques').$id_grpe_asupr.' : '.mysql_error();
			}
			else {
				$sql112 = "DELETE FROM spip_accesgroupes_auteurs WHERE id_grpacces = ".$id_grpe_asupr;
				spip_query($sql112);
				if (mysql_error() != '') {
					$alerte = 1;
					$msg_text .= _T('accesgroupes:erreur_supr_auteurs').$id_grpe_asupr.' : '.mysql_error();
				}
				else {
					$sql113 = "DELETE FROM spip_accesgroupes_auteurs WHERE id_ss_groupe = ".$id_grpe_asupr;
					spip_query($sql113);
					if (mysql_error() != '') {
						$alerte = 1;
						$msg_text .= _T('accesgroupes:erreur_supr_ssgrpes').$id_grpe_asupr.' : '.mysql_error();
					}
					else {
						$sql114 = "DELETE FROM spip_accesgroupes_groupes WHERE id_grpacces = ".$id_grpe_asupr;
						spip_query($sql114);
						if (mysql_error() != '') {
							$alerte = 1;
							$msg_text .= _T('accesgroupes:erreur_supr_groupe').$id_grpe_asupr.' : '.mysql_error();
						}
						else {
							$sql115 = "OPTIMIZE TABLE spip_accesgroupes_groupes";
							spip_query($sql115);
							$sql116 = "OPTIMIZE TABLE spip_accesgroupes_auteurs";
							spip_query($sql116);
							$sql117 = "OPTIMIZE TABLE spip_accesgroupes_acces";
							spip_query($sql117);
							$msg_text .= _T('accesgroupes:supr_groupe_ok').$id_grpe_asupr;
						}
					}
				}
			}
		}
	}  // fin du IF admin restreint + proprio
	
	
	
	// ACCES  ======== gestion des accès restreints
	// ACCES AJOUT
	if (isset($_POST['add_rub']) && $_POST['add_id_rubrique'] > 0){
		$acces_groupe_ec = $_POST['groupe'];
		$acces_id_parent = $_POST['add_id_rubrique'];
		$acces_prive_public = $_POST['prive_public'];
		// ajout rubrique restreinte si admin général ou admin restreint + admin de la rubrique
		//echo '<br>accesgroupes_est_admin_rubrique('.$acces_id_parent.') = '.accesgroupes_est_admin_rubrique($acces_id_parent);
		if ($id_util_restreint == 0 OR ($id_util_restreint !== 0 AND accesgroupes_est_admin_rubrique($acces_id_parent) == TRUE) ) {
			$sql601 = "INSERT INTO spip_accesgroupes_acces(id_grpacces, id_rubrique, id_article, dtdb, dtfn, proprio, prive_public)
									VALUES(\"".$acces_groupe_ec."\", \"".$acces_id_parent."\", \"\",now(),now(), $id_util_restreint, \"".$acces_prive_public."\")";
			$result601 = spip_query($sql601);
		}  // fin du IF admin restreint + proprio

	}
	// ACCES MODIFICATION
	if (isset($_POST['modif_id_groupe']) AND isset($_POST['modif_id_rubrique'])){
		//         	 $msg_text = "<h2>"._T('accesgroupes:acces_double')."</h2>";
		$modif_prive_public = $_POST['modif_prive_public'];
		$modif_id_rubrique = $_POST['modif_id_rubrique'];
		$modif_id_groupe = $_POST['modif_id_groupe'];
		// si admin restreint, modif autorisées si proprio de l'accès
		if ($id_admin_restreint == 0 OR  ($id_admin_restreint !== 0 AND accesgroupes_est_proprio_acces($modif_id_rubrique) == TRUE)) {
			$sql602 = "UPDATE spip_accesgroupes_acces
									SET prive_public = $modif_prive_public
									WHERE id_grpacces = $modif_id_groupe
									AND id_rubrique = $modif_id_rubrique
									LIMIT 1" ;
			$result602 = spip_query($sql602);
		}
	}
	
	// ACCES SUPPRESSION
	if (isset($_GET['del_rub'])){
		// si admin restreint, suppression autorisée si proprio de l'accès
		$id_parent_del = $_GET['del_rub'];
		if ($id_admin_restreint == 0 OR  ($id_admin_restreint !== 0 AND accesgroupes_est_proprio_acces($id_parent_del) == TRUE)) {
			$sql = "DELETE FROM spip_accesgroupes_acces WHERE id_grpacces = $groupe AND id_rubrique = \"".$id_parent_del."\"";
			$result = spip_query($sql);
			accesgroupes_debug($result);
		}
	}
	//accesgroupes_rub_reinit();
	
	
	// DEBUT AFFICHAGE DE LA PAGE
	debut_page(_T('accesgroupes:module_titre'));
	
	// SECURITE ========
	if ($connect_statut != "0minirezo") {
		echo "\r\n<h3><font color='red'>"._T('avis_non_acces_page')."</font></h3>";
		fin_page();
		exit;
	}
	
	// never trust users...
	// test existence du groupe pour ne pas afficher la page admin s'il n'existe pas dans la base alors qu'il est envoyé par $_POST ou $_GET
	if ($groupe != 0) {
		$sql91 = "SELECT COUNT(*) AS verif_grpe FROM spip_accesgroupes_groupes WHERE id_grpacces = $groupe LIMIT 1";
		$result91 = spip_query($sql91);
		if ($row91 = spip_fetch_array($result91)) {
			$row91['verif_grpe'] != 1 ? $groupe = 0 : $groupe = $groupe;
		}
	}
	
	// GAUCHE ========
	debut_gauche();
	debut_boite_info();
	echo "<b>"._T('accesgroupes:module_titre')."</b><br />"._T('accesgroupes:module_info');
	if (isset($msg) AND $msg != '') {
		echo $msg;
	}
	// affichage de la version en cours de acces_groupes à partir de plugin.xml
	$Tlecture_fich_plugin = file(_DIR_PLUGIN_ACCESGROUPES.'/plugin.xml');
	$stop_prochain = 0;
	foreach ($Tlecture_fich_plugin as $ligne) {
		if ($stop_prochain == 1) {
			echo '<br /><br/>Version : <strong>'.$ligne.'</strong>';
			break;
		}
		if (substr_count($ligne, '<version>') > 0) {
			$stop_prochain = 1;
		}
	}
	fin_boite_info();
	
	$sql = "SELECT * FROM spip_accesgroupes_groupes";
	$result = spip_query($sql);
	debut_cadre_relief('../'._DIR_PLUGIN_ACCESGROUPES.'/img_pack/groupe-24.png');
// DEB GROUPE
	echo generer_url_post_ecrire("accesgroupes_admin", '', 'frm_groupe', '', '');
	//echo "\r\n<form action=\"$PHP_SELF?exec=accesgroupes_admin\" name=\"frm_groupe\" method=\"post\">";
	echo "\r\n<br />"._T('accesgroupes:select').": <select name=\"groupe\" size=\"1\" onchange='submit()';>";
	echo "\r\n<option value=\"0\">"._T('accesgroupes:select_vide')."</option>";
	while ($row = spip_fetch_array($result)){
		echo "<option value=\"".$row['id_grpacces']."\"".($groupe== $row['id_grpacces'] ? ' selected':'').">";
		if ($row['actif'] != 1) {
			echo '('.typo($row['nom']).' : '._T('accesgroupes:inactif').')';
		}
		else {
			echo typo($row['nom']);
		}
		echo "</option>";
	}
	echo "</select>";
	
	$sql_grp  = "SELECT * FROM spip_accesgroupes_groupes WHERE id_grpacces=\"$groupe\"";
	$result_grp = spip_query($sql_grp );
	if ($row = spip_fetch_array($result_grp))
	$nom = $row['nom'];
	$desc = $row['description'];
	$actif = $row['actif'];
	$prive_public = $row['prive_public'];
	$demande_acces = $row['demande_acces'];
	$le_proprio = $row['proprio'];
	if ($le_proprio != 0) {
		$sql258 = "SELECT spip_auteurs.nom
											FROM spip_auteurs
												WHERE id_auteur = $le_proprio
												LIMIT 1";
		$result258 = spip_query($sql258);
		$row258 = spip_fetch_array($result258);
		$nom_proprio = $row258['nom'];
	}
	else {
		$nom_proprio = _T('accesgroupes:tous_les_admins');
	}
	
	echo "\r\n<table style=\"width: 100%;\"><tr><td class='serif2'>";
	echo bouton_block_invisible('groupeinfo')._T('accesgroupes:creer');
	if ($groupe > 0 AND ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) ) {
		echo '/'._T('accesgroupes:modifier')." : ";
	}
	// echo "$desc";
	echo "\r\n</td></tr></table>\n";
	
	echo debut_block_invisible('groupeinfo');
	// partie nom et description
	//echo "\r\n<input type=\"hidden\" name=\"groupe\" value=\"$groupe\" />";
	echo "\r\n<table width=\"100%\">";
	echo "\r\n<tr><td colspan=\"2\" style=\"font-weight: bold;\">"._T('accesgroupes:nom')." : </td></tr>";
	echo "\r\n<tr><td colspan=\"2\"><input type=\"text\" name=\"nom\" value=\"$nom\" size=\"18\" />\r\n</td>\r\n</tr>";			
	echo "\r\n<tr><td colspan=\"2\" style=\"padding-top: 7px; font-weight: bold;\">"._T('accesgroupes:description')." : </td></tr>";
	echo "\r\n<tr><td colspan=\"2\"><textarea name=\"description\" rows=\"2\" cols=\"15\">$desc</textarea></td>\r\n</tr>";
	// bouton créer
	echo "\r\n<tr><td  style=\"padding-top: 3px;\"><input type=\"submit\" name=\"add_groupe\" value=\""._T('accesgroupes:creer')."\" class='fondo' style='font-size:10px;' />";
	echo "\r\n</td>\r\n</tr>";
	// partie demande d'accès
	echo "\r\n<tr><td colspan=\"2\" style=\"padding-top: 7px; font-weight: bold;\">"._T('accesgroupes:autoriser_demandes');
	echo " <span style=\"font-size: 10px; font-weight: normal;\">"._T('accesgroupes:help_inscriptions')."</span>\r\n</td></tr>";
	echo "\r\n<tr><td colspan=\"2\">";
	echo _T('accesgroupes:oui')."<input name=\"demandes_acces\" value=\"1\" type=\"radio\" ".($demande_acces == 1 ? "checked=\"checked\"" : "").">";
	echo "&nbsp;&nbsp;<input name=\"demandes_acces\" value=\"0\" type=\"radio\" ".($demande_acces == 0 ? "checked=\"checked\"" : "")."\">"._T('accesgroupes:non');
	echo "\r\n</td>\r\n</tr>";
	
	// partie activé/désactivé
	// admins restreints interdits si pas proprios			
	if ($groupe > 0 AND ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE))) {
		
		echo "\r\n<tr><td colspan=\"2\" style=\"padding-top: 7px; font-weight: bold;\">"._T('accesgroupes:etat_groupe')." : </td></tr>";
		echo "\r\n<tr><td colspan=\"2\">"._T('accesgroupes:actif');
		echo "\r\n<input type=\"radio\" id=\"actif\" name=\"actif\" value=\"1\" ".(($actif != 0)? 'checked="checked" ' : '')."/>";
		echo "\r\n&nbsp;&nbsp;<input type=\"radio\" id=\"inactif\" name=\"actif\" value=\"0\" ".(($actif == 0)? 'checked="checked" ' : '')."/>";
		echo "\r\n"._T('accesgroupes:inactif');
		echo "\r\n</td>\r\n</tr>";
	}
	// partie changer de proprio
	// admin généraux uniquement (PAS admins restreints)
	if ($groupe > 0 AND $id_util_restreint == 0) {
		echo "\r\n<tr><td colspan=\"2\" style=\"padding-top: 7px; font-weight: bold;\">"._T('accesgroupes:changer_proprio_groupe')." : ";
		echo "<span style=\"font-size: 10px; font-weight: normal;\">("._T('accesgroupes:changer_proprio_help').")</span>";
		$sql256 = "SELECT spip_auteurs.nom, spip_auteurs.id_auteur
										FROM spip_auteurs_rubriques
											LEFT JOIN spip_auteurs
											ON spip_auteurs_rubriques.id_auteur = spip_auteurs.id_auteur
											GROUP BY nom
											ORDER BY nom";
		$result256 = spip_query($sql256);
		//echo '<br>mysql_error $sql256 = '.mysql_error();					
		echo "\r\n<select name=\"proprio\" id=\"proprio\">";
		echo "<option value=\"0\" ";
		if ($le_proprio == 0) {
			echo "selected=\"selected\" ";
		}
		echo ">"._T('accesgroupes:tous_les_admins')."</option>";
		while ($row256 = spip_fetch_array($result256)) {
			echo "<option value=\"".$row256['id_auteur']."\" ";
			if ($le_proprio == $row256['id_auteur']) {
				echo "selected=\"selected\" ";
			}
			echo ">".$row256['nom']."</option>";
		}
		echo "</select>";
		echo " </td></tr>";
	}
	// boutons modifier + effacer
	// admins restreints interdits si pas proprios			
	if ($groupe > 0 AND ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE))){
		echo "\r\n<tr style=\"padding-top: 7px; font-weight: bold;\"><td>";
		echo "\r\n<input type=\"submit\" name=\"mod_groupe\" value=\""._T('accesgroupes:modifier')."\" class='fondo' style='font-size:10px;' />";   			
		echo "\r\n</td>\r\n<td>";
		echo "\r\n<input type=\"button\" onclick=\"bascule_effacer();\" id=\"del_groupe\" name=\"del_groupe\" value=\""._T('accesgroupes:supprimer')."\" CLASS='fondo' STYLE='font-size:10px;' />";
		echo "\r\n<input type=\"submit\" id=\"del_groupe_all\" name=\"del_groupe_all\" value=\""._T('accesgroupes:supprimer_tout')."\" CLASS='fondo' STYLE='font-size:10px; display: none;' />";
		echo "\r\n<script language=\"JavaScript\">
						function bascule_effacer() {
											if (document.getElementById('del_groupe') && document.getElementById('del_groupe_all')) {
												document.getElementById('del_groupe').style.display = 'none';
												document.getElementById('del_groupe_all').style.display = 'inline';
												document.getElementById('help_del_groupe').style.display = 'inline';
												document.getElementById('inactif').checked = 'checked';
											}
						}";
		echo "\r\n</script>";
		echo "\r\n</td></tr>";
	} // fin limitations admins restreints
	echo "\r\n<tr><td colspan=\"2\" style=\"background-color: #eeeeee; border: 1px solid #cccccc; font-size: 10px;\">";
	echo "\r\n<span id=\"help_del_groupe\" style=\"display: none;\">"._T('accesgroupes:help_supprimer')."</span>";
	echo "\r\n</td>\r\n</tr>\r\n</table>";
	echo fin_block();
	fin_cadre_relief();
	echo "\r\n</form><!-- frm_groupe -->";
	
	
	// afficher la répartition des rubriques par groupes
	if ($groupe > 0){
		debut_cadre_relief('rubrique-24.gif');
		echo accesgroupes_affiche_groupes_rubriques();	
		fin_cadre_relief();
	}
	
	// afficher l'arborescence des groupes
	
	echo '<div style="width: 220px !important;">'; // blocage de la largeur du cadre raccourcis pour Mozilla Seamonkey
	debut_raccourcis();
	
	echo "\r\n<table CELLPADDING=2 CELLSPACING=0 class='arial2' style='border: 1px solid #aaa; width: 100%;'>\n";
	echo "\r\n<tr style='background-color: #fff;'><th colspan=\"2\">"._T('accesgroupes:arborescence_groupes')."</th><th colspan=\"2\">&nbsp;</th></tr>";
	$sql102 = "SELECT id_grpacces, nom, actif
								FROM spip_accesgroupes_groupes
								GROUP BY nom";
	$result102 = spip_query($sql102);
	while ($row = spip_fetch_array($result102)){
		$id_ec = $row['id_grpacces'];
		$nom_ec = typo($row['nom']); //$row['nom'];
		echo "\r\n<tr style='background-color: #eeeeee;'>";
		echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
		if (accesgroupes_est_admin_restreint() == TRUE AND accesgroupes_est_proprio($id_ec) == TRUE) {
			echo "<img src='img_pack/admin-12.gif' alt='|_' style='vertical-align:top;'>";
		}
		echo "\r\n<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-12.png' alt='|_'></td>";
		$h = generer_url_ecrire("accesgroupes_admin","groupe=$id_ec");
		echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">";
		if ($row['actif'] != 1) {
			echo '('.$nom_ec.' : <span style="color: #6c3;">'._T('accesgroupes:inactif').'</span>)';
		}
		else {
			echo $nom_ec;
		}
		echo "</a><br />";
		echo accesgroupes_affiche_descendance($id_ec, accesgroupes_descendance_groupe($id_ec));
		echo "\r\n</td>";
		echo "\r\n<td style='border-top: 1px solid #cccccc; text-align : right; padding-right: 20px; vertical-align: middle;'>";
		// supprimer rapide pour les admins pas restreints
		if (($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($id_ec) == TRUE) ) AND $groupe != 0) {
			$h = generer_url_ecrire("accesgroupes_admin","del_groupe=$id_ec&groupe=$groupe");
			echo "\r\n<a style=\"vertical-align: bottom;\" href=\"$h\">". http_img_pack('croix-rouge.gif', _T('accesgroupes:suppression'), "width='7' height='7' border='0' style='vertical-align: middle;'")."</a>";
		}
		else {
			echo "&nbsp;";
		}					
		echo "\r\n</td>";
		echo "\r\n</tr>";
	}

	echo "\r\n</table>";
	
	fin_raccourcis();
	echo '</div>';
	
	// DROITE ========
	debut_droite();
	if ($msg_text != '') {
		$tete_msg = '<span style="color: '.($alerte != 1 ? '#6c3' : '#f00').'; text-align: center; font-weight: bold; font-size: 1.2em;">';
		$queue_msg = '</span>';
		$msg_text = $tete_msg.$msg_text.$queue_msg;
	}
	echo "<a name=\"auteurs\">";
	if ($groupe <= 0){  // si pas de groupe envoyé par $_GET ou $_POST
		// affichage des groupes existants et des rubriques qu'ils contrôlent
		gros_titre(_T('accesgroupes:titre_groupes'));
		echo "\r\n<br />";
		if ($msg_text != '') {
			debut_cadre_trait_couleur("fiche-perso-24.gif", false, "", _T('accesgroupes:titre_msg_text'));
			debut_cadre_couleur();
			echo $msg_text;
			fin_cadre_couleur();
			fin_cadre_trait_couleur();
		}
		debut_cadre_trait_couleur("auteur-24.gif", false, "", _T('accesgroupes:membres'));
		gros_titre(_T('accesgroupes:choisir'));
		fin_cadre_trait_couleur();
		echo "\r\n<br />";
		
		debut_cadre_trait_couleur("rubrique-24.gif", false, "", _T('accesgroupes:organisation'));
		echo accesgroupes_affiche_groupes_rubriques();
		fin_cadre_trait_couleur();
	}
	else {  // attention else extra long !! : la page de gestion du groupe déterminé par $_GET ou $_POST
		$sql567 = "SELECT actif FROM spip_accesgroupes_groupes WHERE id_grpacces = $groupe LIMIT 1";
		$result567 = spip_query($sql567);
		$row = spip_fetch_array($result567);
		$inactif = ($row['actif'] != 1 ? ' <span style="color: #6c3;">'._T('accesgroupes:inactif').'</span>' : '');
		gros_titre(_T('accesgroupes:titre_page_groupe').' : '.$nom.' (n&deg; '.$groupe.')'.$inactif);
		if ($desc != "") {
			echo "<div align='$spip_lang_left' style='margin-top: 10px; padding: 5px; border: 1px dashed #aaa; font-family: Verdana,Arial,Sans,sans-serif; font-size: 10px;'>";
			echo "$desc ("._T('accesgroupes:proprio')." : $nom_proprio)";
			echo "</div>\r\n<br />";
		}
		if ($msg_text != '') {
			debut_cadre_trait_couleur("fiche-perso-24.gif", false, "", _T('accesgroupes:titre_msg_text'));
			debut_cadre_couleur();
			echo $msg_text;
			fin_cadre_couleur();
			fin_cadre_trait_couleur();
		}
		debut_cadre_trait_couleur("auteur-24.gif", false, "", _T('accesgroupes:auteurs')._T('accesgroupes:du_groupe'));
		
		// admins restreints interdits si pas proprios			
		if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
			debut_cadre_couleur();
			$suffixe_action_form = ($groupe AND $groupe != '') ? "groupe=$groupe" : "";
			echo generer_url_post_ecrire("accesgroupes_admin", "$suffixe_action_form", "frm_auteur", "#frm_auteur", "");
			echo "<a name=\"frm_auteur\">";
			//echo "\r\n<form action=\"$PHP_SELF?exec=accesgroupes_admin".$suffixe_action_form."\" name=\"frm_auteur\" method=\"post\">";
			echo "\r\n<input type=\"hidden\" name=\"groupe\" value=\"$groupe\" />";
			echo _T('accesgroupes:ajouter_auteur');
			$group = false;
			$group2 = false;
			
			$sql1 = "SELECT DISTINCT spip_auteurs.*
								FROM spip_auteurs
									LEFT JOIN spip_accesgroupes_auteurs
									ON spip_auteurs.id_auteur =  spip_accesgroupes_auteurs.id_auteur
										WHERE statut != '5poubelle'
									AND statut != 'nouveau'
									ORDER BY statut, nom";
			$result1 = spip_query($sql1);
			if (mysql_errno() != 0) {
				echo mysql_errno().": ".mysql_error();
			}
			echo "\r\n<select name='auteur' size='1' style='width:150px;' class='fondl'>";
			while ($row = spip_fetch_array($result1)) {
				$id_auteur = $row["id_auteur"];
				$nom = $row["nom"];
				$email = $row["email"];
				$statut = $row["statut"];
				
				$statut = str_replace("0minirezo", html_entity_decode(_T('info_administrateurs')), $statut);
				$statut = str_replace("1comite", html_entity_decode(_T('info_redacteurs')), $statut);
				$statut = str_replace("6visiteur", html_entity_decode(_T('info_visiteurs')), $statut);
				$statut = str_replace("6forum", html_entity_decode(_T('info_visiteurs')), $statut);
				
				$premiere = strtoupper(substr(trim($nom), 0, 1));
				
				if ($connect_statut != '0minirezo') {
					if ($p = strpos($email, '@')) {
						$email = substr($email, 0, $p).'@...';
					}
				}
				if ($email) {
					$email = " ($email)";
				}
				
				if ($statut != $statut_old) {
					echo "\r\n<option value=\"x\"> </option>";
					echo "\r\n<option value=\"x\" style='background-color: $couleur_claire; font-weight:bold ;'> ".$statut." :</option>";
				}
				
				if ($premiere != $premiere_old AND ($statut != _T('info_administrateurs') OR !$premiere_old)) {
					//                        echo "\r\n<option value=\"x\"> </option>";
				}
				
				$texte_option = supprimer_tags(couper(typo("$nom$email"), 40));
				echo "\r\n<option value=\"$id_auteur\">&nbsp;&nbsp;&nbsp;&nbsp;$texte_option</option>";
				$statut_old = $statut;
				$premiere_old = $premiere;
			}
			
			echo "\r\n</select>";
			echo "\r\n<input type=\"submit\" name=\"add_auteur\" value=\""._T('accesgroupes:ajouter')."\"  class='fondo'/>";
			fin_cadre_couleur();		
		} // fin restriction admin restreint
		
		// tableau des auteurs ayant accès
		echo "\r\n<table CELLPADDING=2 CELLSPACING=0 class='arial2' style='width: 100%; border: 1px solid #aaa;'>\n";
		echo "\r\n<tr><th colspan=\"2\">"._T('accesgroupes:auteurs_groupe')."</th><th colspan=\"2\">&nbsp;</th></tr>";

		$sql2 = "SELECT spip_auteurs.id_auteur, spip_auteurs.nom, spip_auteurs.statut
											FROM spip_accesgroupes_auteurs
											LEFT JOIN spip_auteurs
											ON spip_auteurs.id_auteur = spip_accesgroupes_auteurs.id_auteur
											WHERE id_grpacces = $groupe
											AND dde_acces = 0";											
		// , spip_accesgroupes_auteurs.dde_acces
		$result2 = spip_query($sql2);
		//echo '<br>mysql_error $sql2 = '.mysql_error();							
		while ($row = spip_fetch_array($result2)){
			if ($row['id_auteur'] == 0) {
				continue;
			}
			echo "\r\n<tr style='background-color: #eeeeee;'>";
			echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
			$statut_util_ec = accesgroupes_trouve_statut($row['id_auteur']);
			$statut_util_ec == '0minirezo' ?  $ico_statut = 'admin-12.gif' : ($statut_util_ec == '1comite' ? $ico_statut = 'redac-12.gif' : $ico_statut = 'visit-12.gif');
			echo "\r\n<img src='img_pack/".$ico_statut."' alt='|_' style='vertical-align:top;'></td>";			
			$h = generer_url_ecrire("$url_auteur","id_auteur=".$row['id_auteur']);
			echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">".$row['nom']."</a></td>";
			echo "\r\n<td style='border-top: 1px solid #cccccc; text-align : right; padding-right: 20px;'>";
			// admins restreints interdits de modifs des membres du groupe si pas proprios
			if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
				$h = generer_url_ecrire("accesgroupes_admin","del_auteur=".$row['id_auteur']."&groupe=$groupe");
				echo "\r\n<a href=\"$h\"> "._T('lien_retirer_auteur')." &nbsp;  ".http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' style='vertical-align: middle;'")."</a>";
			}
			else {
				echo $row['id_auteur'];
			}
			echo "\r\n</td>";
			echo "\r\n<td style='border-top: 1px solid #ccc;'><a href=\"mailto:$email\"></a></td>";
			echo "\r\n</tr>";
		}
		echo "</table>";
		
		// tableau des auteurs en attente d'une demande d'accès
		$sql2 = "SELECT spip_auteurs.id_auteur, spip_auteurs.nom, spip_auteurs.statut
							FROM spip_accesgroupes_auteurs
								LEFT JOIN spip_auteurs
											ON spip_accesgroupes_auteurs.id_auteur = spip_auteurs.id_auteur
							WHERE id_grpacces = $groupe
									AND dde_acces != 0";
		// , spip_accesgroupes_auteurs.dde_acces											
		$result2 = spip_query($sql2);
		if (spip_num_rows($result2) > 0) {
			echo "\r\n<br /><table CELLPADDING=2 CELLSPACING=0 class='arial2' style='width: 100%; border: 1px solid #aaaaaa;'>\n";
			echo "\r\n<tr><th colspan=\"4\">"._T('accesgroupes:auteurs_en_attente')."</th></tr>";
			while ($row = spip_fetch_array($result2)){
				echo "\r\n<tr style='background-color: #eeeeee;'>";
				echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
				$statut_util_ec = accesgroupes_trouve_statut($row['id_auteur']);
				$statut_util_ec == '0minirezo' ?  $ico_statut = 'admin-12.gif' : ($statut_util_ec == '1comite' ? $ico_statut = 'redac-12.gif' : $ico_statut = 'visit-12.gif');
				echo "\r\n<img src='img_pack/".$ico_statut."' alt='|_' style='vertical-align:top;'></td>";
				$h = generer_url_ecrire("$url_auteur","id_auteur=".$row['id_auteur']."&groupe=$groupe");
				echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">".$row['nom']."</a></td>";
				echo "\r\n<td style='border-top: 1px solid #cccccc; text-align : right; padding-right: 20px;'>";
				if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
					$h = generer_url_ecrire("accesgroupes_admin","del_auteur=".$row['id_auteur']."&groupe=$groupe&message=refuse");
					echo "\r\n<a href=\"$h\"> "._T('lien_retirer_auteur')." &nbsp;  ".http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' style='vertical-align: middle;'")."</a>";
					$h = generer_url_ecrire("accesgroupes_admin","mod_auteur=".$row['id_auteur']."&groupe=$groupe&message=accepte");
					echo "&nbsp;|&nbsp;<a href=\"$h\"> "._T('accesgroupes:accepter')."</a>";
				}
				else {
					echo $row['id_auteur'];
				}
				echo "\r\n</td>";
				echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"mailto:$email\"></a></td>";
				echo "\r\n</tr>";
			}
			echo "</table>";
		}
		fin_cadre_trait_couleur();
		
		
		// inclure/gérer des ss-groupes ou des statuts dans les groupes
		// début des sous-groupes
		echo "\r\n<br />";
		debut_cadre_trait_couleur("../"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-24.png", false, "", _T('accesgroupes:ss_groupes')._T('accesgroupes:du_groupe'));
		
		// admins restreints interdits si pas proprios
		if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
			debut_cadre_couleur();
			echo _T('accesgroupes:ajouter_ss_groupe');
			echo "\r\n<select name='ss_groupe' SIZE='1' style='width:150px;' class='fondl'>\r\n";
			
			$sql101 = "SELECT id_grpacces, nom FROM spip_accesgroupes_groupes WHERE id_grpacces != $groupe AND actif = 1 ORDER BY nom";
			$result101 = spip_query($sql101);
			
			echo "\n<option value=\"x\"> </option>\r\n";
			while ($row = spip_fetch_array($result101)) {
				$id_ss_groupe = $row["id_grpacces"];
				$nom_ss_groupe = $row["nom"];
				// ne pas afficher les groupes déja inclus dans le groupe en cours (et leurs descendants)
				if (accesgroupes_verifie_inclusions_groupe($groupe, $id_ss_groupe) == FALSE) {
					continue;
				}
				// ne pas afficher les groupes appartenant à l'ascendance du groupe en cours
				if (accesgroupes_verifie_inclusions_groupe($id_ss_groupe, $groupe) == FALSE) {
					continue;
				}
				$nom_ss_grpe = strtoupper(substr(trim($nom_ss_groupe), 0, 1));
				echo "\n<option value=\"$id_ss_groupe\" style='background-color: $couleur_claire;'> $nom_ss_groupe</option>\r\n";
			}
			
			echo "</select>\r\n";
			
			echo "<input type=\"submit\" name=\"add_ss_groupe\" value=\""._T('accesgroupes:ajouter')."\"  CLASS='fondo'/>";
			fin_cadre_couleur();
		} // fin limitation admin restreint
		
		echo "\r\n<table CELLPADDING=2 CELLSPACING=0 class='arial2' style='width: 100%; border: 1px solid #aaa;'>\n";
		echo "\r\n<tr><th colspan='3'>"._T('accesgroupes:ss_groupes_groupe')."</th></tr>";
		$sql102 = "SELECT spip_accesgroupes_auteurs.id_ss_groupe, spip_accesgroupes_groupes.nom, spip_accesgroupes_auteurs.id_grpacces
										FROM spip_accesgroupes_auteurs
												LEFT JOIN spip_accesgroupes_groupes
												ON spip_accesgroupes_auteurs.id_ss_groupe = spip_accesgroupes_groupes.id_grpacces
										WHERE actif = 1
										ORDER BY nom";
		// , spip_accesgroupes_auteurs.dde_acces
		//        								 WHERE spip_accesgroupes_auteurs.id_grpacces = $groupe
		$result102 = spip_query($sql102);
		//echo '<br>mysql_error $sql102 = '.mysql_error();							
		while ($row = spip_fetch_array($result102)){
			if ($row['id_grpacces'] != $groupe) {
				continue;
			}
			$id_ec = $row['id_ss_groupe'];
			$nom_ec = $row['nom'];
			echo "\r\n<tr style='background-color: #eeeeee;'>";
			echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
			echo "\r\n<img src='"._DIR_PLUGIN_ACCESGROUPES."/img_pack/groupe-12.png' alt='|_' style='vertical-align:top;'></td>";
			$h = generer_url_ecrire("accesgroupes_admin","groupe=$id_ec");
			echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">".$nom_ec."</a><br />";
			echo accesgroupes_affiche_descendance($id_ec, accesgroupes_descendance_groupe($id_ec));
			echo "</td>";
			echo "\r\n<td style='border-top: 1px solid #cccccc; text-align : right; padding-right: 20px;'>";
			if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
				$h = generer_url_ecrire("accesgroupes_admin","del_ss_groupe=".$row['id_ss_groupe']."groupe=$groupe");
				echo "\r\n<a href=\"$h\">"._T('accesgroupes:retirer_groupe')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' style='vertical-align: middle;'")."</a>";
				//                         echo $row['dde_acces'] == 1 ? "&nbsp;|&nbsp;<a href=\"".$PHP_SELF."?exec=accesgroupes_admin&mod_ss_groupe=".$row['id_ss_groupe']."&groupe=".$groupe."\">"._T('accesgroupes:accepter')."</a>" : "";
			}
			else {
				echo "&nbsp;";
			}					
			echo "</td>";
			echo "</tr>";
		}
		echo "\r\n</table>";
		fin_cadre_trait_couleur();
		
		// début des statuts			
		echo "\r\n<br />";
		debut_cadre_trait_couleur("../"._DIR_PLUGIN_ACCESGROUPES."/img_pack/statuts-24.png", false, "", _T('accesgroupes:statuts')._T('accesgroupes:du_groupe'));
		$Tstatuts = array("0minirezo" => "Administrateurs", "1comite" => "R&eacute;dacteurs", "6forum" => "Visiteurs");
		if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
			debut_cadre_couleur();
			echo _T('accesgroupes:ajouter_statut');
			echo "\r\n<select name='sp_statut' SIZE='1' style='width:150px;' class='fondl'>\r\n";
			$sql105 = "SELECT sp_statut FROM spip_accesgroupes_auteurs WHERE id_grpacces = $groupe AND sp_statut != '' GROUP BY sp_statut";
			$result105 = spip_query($sql105);
			while ($row = spip_fetch_array($result105)) {
				$Tresult105[] = $row['sp_statut'];
			}
			echo "\r\n<option value=\"x\"> </option>\r\n";
			foreach ($Tstatuts as $statut_ec => $nom_stat) {
				if (!in_array($statut_ec, $Tresult105)) {
					echo "\r\n<option value=\"$statut_ec\" style='background-color: $couleur_claire;'> $nom_stat</option>\r\n";
				}
			}
			echo "\r\n</select>\r\n";
			echo "\r\n<input type=\"submit\" name=\"add_statut\" value=\""._T('accesgroupes:ajouter')."\"  CLASS='fondo'/>";
			fin_cadre_couleur();
		} // fin limitation admin restreint
		$sql104 = "SELECT spip_accesgroupes_auteurs.sp_statut, spip_accesgroupes_auteurs.id_grpacces
							FROM spip_accesgroupes_auteurs
											LEFT JOIN spip_accesgroupes_groupes
											ON spip_accesgroupes_groupes.id_grpacces = spip_accesgroupes_auteurs.id_grpacces
							WHERE actif = 1
							AND sp_statut != ''";
		$result104 = spip_query($sql104);
		// , spip_accesgroupes_auteurs.dde_acces
		//echo '<br>mysql_error $sql104 = '.mysql_error();
		echo "\r\n<table CELLPADDING=2 CELLSPACING=0 class='arial2' style='width: 100%; border: 1px solid #aaaaaa;'>\n";
		echo "\r\n<tr><th colspan=\"3\">"._T('accesgroupes:statut_groupe')."</th></tr>";
		while ($row = spip_fetch_array($result104)) {
			if ($row['id_grpacces'] != $groupe) {
				continue;
			}	
			$statut_ec = $row['sp_statut'];
			$nom_statut = $Tstatuts[$statut_ec];
			$statut_ec == '0minirezo' ?  $ico_statut = 'admin-12.gif' : ($statut_ec == '1comite' ? $ico_statut = 'redac-12.gif' : $ico_statut = 'visit-12.gif');
			
			echo "\r\n<tr style='background-color: #eeeeee;'>";
			echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:top;'>";
			echo "\r\n<img src='img_pack/".$ico_statut."' alt='|_' style='vertical-align:top;'></td>";
			echo "\r\n<td style='border-top: 1px solid #cccccc;'>".$nom_statut."</td>";			
			echo "\r\n<td style='border-top: 1px solid #cccccc; text-align : right; padding-right: 20px;'>";
			if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio($groupe) == TRUE)) {
				$h = generer_url_ecrire("accesgroupes_admin","del_statut=$statut_ec&groupe=$groupe");
				echo "\r\n<a href=\"$h\">"._T('accesgroupes:retirer_statut')."&nbsp;".http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' style='vertical-align: middle;'")."</a>";
			}
			else {
				echo "&nbsp;";
			}
			echo "\r\n</td>";
			echo "\r\n</tr>";
		}
		echo "\r\n</table>";
		fin_cadre_trait_couleur();
		
		echo "\r\n</form>";
		
		echo "<br />";
		
		echo "\r\n<script language=\"JavaScript\" type=\"text/javascript\">
											Tacces_rub = new Array();
										// fct pour sélectionner le radio correspondant au type d'accès restreint (privé+public/privé/public) de la rubrique sélectionnée dans la liste déroulante
										// et limiter le choix du type de restriction en fct du type d'accès du parent par masquage des radios interdits
												function select_rubrique_acces() {
																document.getElementById('span_prive_public_1').style.display = 'inline';
																document.getElementById('span_prive_public_2').style.display = 'inline';
																for (i in Tacces_rub) {
																		if (document.getElementById('add_id_rubrique').value == Tacces_rub[i][0]) {
																				document.getElementById('prive_public_' + Tacces_rub[i][1]).checked = 'checked';
																				if (Tacces_rub[i][2] == 0) {
																					document.getElementById('span_prive_public_1').style.display = 'none';
																					document.getElementById('span_prive_public_2').style.display = 'none';
																				}
																				if (Tacces_rub[i][2] == 1) {
																					document.getElementById('span_prive_public_2').style.display = 'none';
																				}
																				if (Tacces_rub[i][2] == 2) {
																					document.getElementById('span_prive_public_1').style.display = 'none';
																				}
																				return true;
																		}
																}
															// si la rubrique n'appartient pas à Tacces_rub, sélectionner prive+public par défaut
																document.getElementById('prive_public_0').checked = 'checked';
												}
						";
		echo "			</script>";
		
		debut_cadre_trait_couleur("rubrique-24.gif", false, "", _T('accesgroupes:rubriques_restreintes'));
		
		echo "<div style=\"font-weight: bold; margin-bottom: 5px; font-size: 10px;\">";
		echo _T('accesgroupes:rubriques_autorisees_info');
		echo "<span style=\"color: #f00; background-color: #c0cad4; padding: 2px;\">";
		echo _T('accesgroupes:prive_public');
		echo "</span> <span style=\"color: #093; background-color: #c0cad4; padding: 2px;\">";
		echo _T('accesgroupes:prive_seul');
		echo "</span> <span style=\"color: #f90; background-color: #c0cad4; padding: 2px;\">";
		echo _T('accesgroupes:public_seul');
		echo "</span></div>";
		echo "<div style=\"font-weight: bold; margin-bottom: 5px; font-size: 10px;\">";
		echo _T('accesgroupes:prive_public_autres');
		echo "<span style=\"color: #00f; background-color: #c0cad4; padding: 2px; font-size: 10px;\">";
		echo _T('accesgroupes:prive_public_tous');
		echo "</span></div>";
		echo generer_url_post_ecrire("accesgroupes_admin", '', 'frm_rubrique', '#frm_rubrique', '');
		echo "<a name=\"frm_rubrique\">";
		//echo "\r\n<form action=\"$PHP_SELF?exec=accesgroupes_admin\" name=\"frm_rubrique\" method=\"post\" style=\"margin-bottom: 0px;\">\n";
		echo "\r\n<select id=\"add_id_rubrique\" onChange=\"select_rubrique_acces();\" name=\"add_id_rubrique\" style=\"background-color:#fff; font-size:90%; font-face:verdana,arial,helvetica,sans-serif; max-height: 24px; margin-top: 2px;\" class=\"forml\" size=\"1\">\n";
		if ($connect_toutes_rubriques) {
			echo "\r\n<option ".mySel("0",$id_parent).http_style_background("racine-site-12.gif",  "$spip_lang_left no-repeat; background-color:$couleur_foncee; padding-$spip_lang_left: 16px; font-weight:bold; color:white") .">"._T("info_racine_site")."\n";
		}
		else {
			$Trub_autorise = accesgroupes_cree_Trub_admin();
			if (count($Trub_autorise) > 0) {
				accesgroupes_enfant(0);
			}
			else {
				echo "\r\n<option ".mySel("0",$id_parent).http_style_background("racine-site-12.gif",  "$spip_lang_left no-repeat; background-color:$couleur_foncee; padding-$spip_lang_left: 16px; font-weight:bold; color:white");" \">"._T("info_non_deplacer")."\n";
			}
		}
		
		if (lire_meta('multi_rubriques') == 'oui') {
			echo " [".traduire_nom_langue(lire_meta('langue_site'))."]";
		}

		// si le parent ne fait pas partie des rubriques restreintes, modif impossible
		//if (acces_rubrique($id_parent)) {
		if (accesgroupes_acces_rubrique($id_parent)) {
			accesgroupes_enfant(0);
		}
		
		echo "</select>";
		
		echo "\r\n<span style=\"font-size: 10px; float: right; text-align: left;\">";
		echo "\r\n<span id=\"span_prive_public_2\"><input id=\"prive_public_2\" style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"prive_public\" value=\"2\"".($prive_public_ec == 2 ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:public')."</span>";
		echo "\r\n<span id=\"span_prive_public_1\"><br /><input id=\"prive_public_1\" style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"prive_public\" value=\"1\"".($prive_public_ec == 1 ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:prive')."</span>";
		echo "\r\n<span id=\"span_prive_public_0\"><br /><input id=\"prive_public_0\" style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"prive_public\" value=\"0\"".(($prive_public_ec == 0 OR $prive_public_ec == '') ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:les_2')."</span>";
		echo "\r\n</span>";
		echo "\r\n<span style=\"float: right;\">"._T('accesgroupes:portee_acces')."</span>";
		echo "\r\n<input type=\"submit\" name=\"add_rub\" value=\""._T('accesgroupes:autoriser')."\"  class='fondo'/> \n";
		echo "\r\n<br /><span style=\"font-size: 9px;\">("._T('accesgroupes:help_portee_acces').")</span>";
		echo "\r\n<input type=\"hidden\" name=\"groupe\" value=\"$groupe\" />\n";
		echo "\r\n</form>\n";
		echo "\r\n<script language=\"JavaScript\" type=\"text/javascript\">select_rubrique_acces();</script>";
		$sql603 = "SELECT spip_rubriques.*, spip_accesgroupes_acces.prive_public, spip_auteurs.nom, spip_auteurs.id_auteur
								FROM spip_accesgroupes_acces
												LEFT JOIN spip_rubriques
												ON spip_accesgroupes_acces.id_rubrique = spip_rubriques.id_rubrique
												LEFT JOIN spip_auteurs
												ON spip_accesgroupes_acces.proprio = spip_auteurs.id_auteur
												WHERE id_grpacces = \"$groupe\"";
		

		$result603 = spip_query($sql603);
		accesgroupes_debug($result603);
		echo "\r\n<table CELLPADDING=2 CELLSPACING=0 class='arial2' style='border: 1px solid #aaaaaa; width: 100%; clear: right; margin-top: 0px;'>\n";
		echo "\r\n<tr><th colspan='5'>"._T('accesgroupes:autoriser_info')."</tr>";
		echo "\r\n<tr style=\"background-color: #fff; border-top: solid 1px #ccc;\"><td style=\"font-size: 10px; border-top: solid 1px #ccc;\"></td><td style=\" border-top: solid 1px #ccc;\">"._T('accesgroupes:nom')."</td><td colspan=\"2\" style=\" border-top: solid 1px #ccc;\">"._T('accesgroupes:portee_acces')."</td><td  style=\"margin-left: 20px; padding-right: 20px; border-top: solid 1px #ccc;\" >"._T('accesgroupes:suppression')."</td></tr>";
		while ($row = spip_fetch_array($result603)){
			$prive_public_ec = $row['prive_public'];
			$modif_id_rubrique = $row['id_rubrique'];
			$id_proprio_ec = $row['id_auteur'];
			$nom_proprio_ec = $row['nom'];
			echo "\r\n<tr style='background-color: #eeeeee;'>";
			echo "\r\n<td class='verdana11' style='border-top: 1px solid #cccccc; width: 14px; vertical-align:middle;'>";
			echo "\r\n<img src='img_pack/rubrique-12.gif' alt='|_'></td>";
			$h = generer_url_ecrire("naviguer","id_rubrique=".$row['id_rubrique']);
			echo "\r\n<td style='border-top: 1px solid #cccccc;'><a href=\"$h\">".typo($row['titre'])."</a></td>";
			echo "\r\n<td style='border-top: 1px solid #cccccc; text-align: right;'>";
			if ($id_util_restreint == 0 OR ($id_util_restreint != 0 AND accesgroupes_est_proprio_acces($row['id_rubrique']) == TRUE)) {
				//  AND accesgroupes_est_proprio($groupe) == TRUE
				
				echo generer_url_post_ecrire("accesgroupes_admin", '', 'form_modif_rubrique_'.$row['id_rubrique'], '#form_modif_rubrique_'.$row['id_rubrique'], '');
				echo "<a name=\"form_modif_rubrique_".$row['id_rubrique']."\">";
				//$h = generer_url_ecrire("accesgroupes_admin","groupe=$groupe");
				//echo "\r\n<form action=\"$h\" name=\"form_modif_rubrique_".$modif_id_rubrique."\" method=\"post\">\n";
				echo "\r\n<input  style=\"font-size: 75%;\" type=\"submit\" name=\"submit_modif_rub\" value=\""._T('accesgroupes:modifier')."\"  CLASS='fondo'/></td>\n";
				echo "\r\n<td style=\"border-top: 1px solid #cccccc;\">";
				// selon le prive/public de l'ascendance de la rubrique, limiter les possibilités de prive/public pour la rubrique
				if ($Trub_grpe_ec_parent[$row['id_rubrique']] > 1) {
					echo "\r\n<input style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"modif_prive_public\" value=\"2\"".($prive_public_ec == 2 ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:public');
				}
				if ($Trub_grpe_ec_parent[$row['id_rubrique']] > 0 AND $Trub_grpe_ec_parent[$row['id_rubrique']] != 2) {
					echo "\r\n<br /><input style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"modif_prive_public\" value=\"1\"".($prive_public_ec == 1 ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:prive');
				}
				echo "\r\n<br /><input style=\"text-align: right; vertical-align: bottom;\" type=\"radio\" name=\"modif_prive_public\" value=\"0\"".(($prive_public_ec == 0 OR $prive_public_ec == '') ? " checked=\"checked\"" : "")."  />"._T('accesgroupes:les_2');
				echo "\r\n<input type=\"hidden\" name=\"modif_id_rubrique\" value=\"$modif_id_rubrique\" />\n";
				echo "\r\n<input type=\"hidden\" name=\"modif_id_groupe\" value=\"$groupe\" /></td>";
				echo "\r\n<input type=\"hidden\" name=\"groupe\" value=\"$groupe\" /></td>";
				echo "\r\n</form>\n";
				$h = generer_url_ecrire("accesgroupes_admin","del_rub=".$row['id_rubrique']."&groupe=$groupe");
				echo "\r\n<td style=\"border-top: 1px solid #ccc; padding-right: 10px;\" ><a href=\"$h\">"._T('supprimer')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' style='vertical-align: middle;'") ."</a></td>\n";
				
			}
			else {
				echo "\r\n</td>";
				echo "\r\n<td style=\"border-top: 1px solid #ccc; padding-right: 10px;\" colspan=\"2\">";
				echo '('._T('accesgroupes:restriction_cree_par').' : ';
				if ($nom_proprio_ec != '') {
					$h = generer_url_ecrire("$url_auteur","id_auteur=$id_proprio_ec");
					echo "<a href=\"$h\">$nom_proprio_ec</a>";
				}
				else {
					echo _T('accesgroupes:tous_les_admins');
				}		
				echo ")</td>";
			}
			echo "\r\n</tr>";
		}
		echo "\r\n</table>";
		
		fin_cadre_trait_couleur();
		
		// liste des autres groupes ayant accès à la rubrique ajoutée ou modifiée...
		if (isset($_POST['add_id_rubrique']) OR isset($_POST['modif_id_rubrique'])) {
			$id_rubrique_am = ( isset($_POST['add_id_rubrique']) ? $_POST['add_id_rubrique'] : $_POST['modif_id_rubrique'] );
			$sql655 = "SELECT spip_accesgroupes_groupes.nom, spip_accesgroupes_acces.id_grpacces
									FROM  spip_accesgroupes_groupes
											LEFT JOIN spip_accesgroupes_acces
											ON spip_accesgroupes_acces.id_grpacces = spip_accesgroupes_groupes.id_grpacces
									WHERE id_rubrique = $id_rubrique_am";
			$result655 = spip_query($sql655);
			accesgroupes_debug($result655);
			//        								AND spip_accesgroupes_acces.id_grpacces != $groupe";
			if ($id_rubrique_am > 0 && spip_num_rows($result655) > 1 ){
				debut_cadre_relief("mot-cle-24.gif");
				if (isset($_POST['add_id_rubrique'])) {
					echo gros_titre(_T('accesgroupes:acces_rubrique_add_par'));
				}
				else {
					echo gros_titre(_T('accesgroupes:acces_rubrique_modif_par'));
				}
				echo "\r\n<ul>";
				while ($row = spip_fetch_array($result655)){
					if ($row['id_grpacces'] == $groupe) {
						continue;
					}
					echo "\r\n<li>".typo($row['nom'])."</li>";
				}
				echo "\r\n</ul>";
				fin_cadre_relief();
			}
		}
		
	}   // fin du else extra-long (affichage interface gestion si groupe déterminé)
	
	
	echo fin_page();
	
	
}  // fin fct exec_accesgroupes_admin



?>