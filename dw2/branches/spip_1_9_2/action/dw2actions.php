<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| diverses actions. Ecriture BDD !
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

#
# action generique
#
function action_dw2actions() {

	global $action, $arg, $hash, $id_auteur;
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action-$arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}

	preg_match('/^(\w+)\W(.*)$/', $arg, $r);
	$var_nom = 'action_dw2actions_' . $r[1];
	if (function_exists($var_nom)) {
		spip_log("$var_nom $r[2]");
		$var_nom($r[2]);
	}
	else {
		spip_log("action $action: $arg incompris");
	}
}

#
# les actions ...
#

//
// suppression fiche (depuis archive)
function action_dw2actions_supprimefiche($arg) {
	global $redirect;
	$arg = intval($arg);
	
	spip_query("DELETE FROM spip_dw2_doc WHERE id_document=$arg");
	
	redirige_par_entete(rawurldecode($redirect));
}

//
// modifier nom, categorie ou compteur d une fiche
function action_dw2actions_modifierfiche($arg) {
	global $redirect;
	global $n_nom, $n_categorie, $n_total;
	$arg = intval($arg);
	
	// On controle que le champ "nom" pas vide
	if (!empty ($n_nom)) {
		spip_query("UPDATE spip_dw2_doc SET categorie='$n_categorie', nom='$n_nom', total='$n_total' WHERE id_document=$arg");
	}
	redirige_par_entete(rawurldecode($redirect));
}

//
// deplace un Doc d un article a un autre (ou rubrique)
function action_dw2actions_deplacerdocument($arg) {
	global $redirect;
	global $new_doctype, $new_iddoctype, $anc_doctype;
	$arg = intval($arg); // id_document
	if(isset($new_iddoctype)) {
		spip_query("UPDATE spip_dw2_doc SET doctype='$new_doctype', id_doctype='$new_iddoctype' WHERE id_document='$arg'");
		spip_query("DELETE FROM spip_documents_".$anc_doctype."s WHERE id_document='$arg'");
		spip_query("INSERT INTO spip_documents_".$new_doctype."s (id_document, id_$new_doctype) VALUES ('$arg','$new_iddoctype')");
	}
	redirige_par_entete(rawurldecode($redirect));
}

//
// mise a jour du Titre et Descriptif du document
function action_dw2actions_majtitredocument($arg) {
	global $redirect;
	global $titre_document, $descriptif_document;
	$arg = intval($arg);
	
	include_spip('inc/filtres');
	
	$titre_document = addslashes(corriger_caracteres($titre_document));
	$descriptif_document = addslashes(corriger_caracteres($descriptif_document));
	
	spip_query("UPDATE spip_documents SET titre='$titre_document', descriptif='$descriptif_document' WHERE id_document='$arg'");
	
	redirige_par_entete(rawurldecode($redirect));
}

//
// modifier nom, categorie ou compteur d une fiche
function action_dw2actions_modifiercategorie($arg) {
	global $redirect;
	global $nouv_categ;
	
	// On controle que nouv_categ (nouveau nom categorie) ne soit pas vide
	if (!empty ($nouv_categ)) {
		spip_query("UPDATE spip_dw2_doc SET categorie='".$nouv_categ."' WHERE categorie='".$arg."'");
	}
	redirige_par_entete(rawurldecode($redirect));
}

//
// changer statut d'une Fiche de Doc
function action_dw2actions_changerstatut($arg) {
	global $redirect;
	global $chg_statut_doc, $num_arch, $inverse;
	
	// $arg -> 'archive' par defaut !!
	// inverser formulaire outils - rendre 'actif'
	if($inverse) { $arg='actif'; }
	
	// vient de dw2_outils, dw2_accueil
	if($num_arch) {
		$chg_statut_doc = explode(',',$num_arch);
		reset($chg_statut_doc);
	}
	
	// vient dw2_modif (var) ou ci-dessus (tableau)
	if(is_array($chg_statut_doc)) {
		foreach($chg_statut_doc as $k) {
			spip_query("UPDATE spip_dw2_doc SET statut='".$arg."' WHERE id_document=$k");
		}
	}
	else {
		spip_query("UPDATE spip_dw2_doc SET statut='".$arg."' WHERE id_document=$chg_statut_doc");
	}
	
	redirige_par_entete(rawurldecode($redirect));
}

//
// modifier intitule de serveur ftp
function action_dw2actions_modifierintitule($arg) {
	global $redirect;
	global $designe;
	$arg = intval($arg); // id_serv
	if($designe!='') {
		spip_query("UPDATE spip_dw2_serv_ftp SET designe='$designe' WHERE id_serv=$arg");
	}
	redirige_par_entete(rawurldecode($redirect));
}


//
// effacer un serveur ftp
function action_dw2actions_effaceserveur($arg) {
	global $redirect;

	$arg = intval($arg);
	#$redirect = generer_url_ecrire("dw2_deloc");
	spip_query("DELETE FROM spip_dw2_serv_ftp WHERE id_serv=$arg");

	redirige_par_entete(rawurldecode($redirect));
}

//
//
function action_dw2actions_inclusdocserveur($arg) {
	global $redirect;
	global $id_serv, $sitedist, $id_type, $repert_dest, $taille, $fichier;
	
	
	// reconstruction chemin-fichier-spipien, type IMG/nnn/fichier
	if (ereg("\.([^.]+)$", $fichier, $match))
		{ $ext = strtolower($match[1]); }
	$lien_fichier = "IMG/".$ext."/".$fichier;

	// insert dans spip_documents
	$req = "INSERT INTO spip_documents (id_vignette, id_type, date, fichier, taille, mode) ".
			"VALUES ('0', '$id_type', NOW(), '$lien_fichier', '$taille', 'document')";
	$result = spip_query($req);
	$id_document = spip_insert_id();
	
	// prim insert dans spip_dw2_doc
	$url = "/".$repert_dest.$fichier;
	spip_query("INSERT INTO spip_dw2_doc (id_document, nom, url, date_crea, heberge, id_serveur) ".
			"VALUES ('$id_document', '$fichier', '$url', NOW(), '$sitedist', '$id_serv')");
		
	redirige_par_entete(rawurldecode($redirect."&id_document=".$id_document."&id_serv=".$id_serv));
}

//
//
function action_dw2actions_docserveurlier($arg) {
	include_spip("inc/dw2_inc_ajouts");
	
	$arg = intval($arg); // arg -> id_document
	
	global $trt_doc, $descrip_doc, $id_rub, 
			$proposition, //-> id_article
			$proposition, 
			$fichier, $type_categorie, 
			$id_serv;
			
			
	if(!$id_rub) {
		// si val vide retourne sur affect_doc
		$redirect=generer_url_ecrire("dw2_affect_doc");
		redirige_par_entete(rawurldecode($redirect."&id_document=".$arg."&id_serv=".$id_serv));
	}
	
	// enregistrer le #titre et #descriptif du doc
	include_spip('inc/filtres');
	$trt_doc = addslashes(corriger_caracteres($trt_doc));
	$descrip_doc = addslashes(corriger_caracteres($descrip_doc));
	spip_query ("UPDATE spip_documents SET titre='$trt_doc', descriptif='$descrip_doc' WHERE id_document=$arg");


	if($proposition=='') {
		// lier doc à la rubrique dans spip
		spip_query("INSERT INTO spip_documents_rubriques (id_document, id_rubrique) ".
				"VALUES ('$arg', '$id_rub')");
		$doctype='rubrique';
		$id_doctype=$id_rub;

	}
	else {
		spip_query("INSERT INTO spip_documents_articles(id_document, id_article) ".
				"VALUES ('$arg', '$proposition')");
		$doctype='article';
		$id_doctype=$proposition;

	}
	
	// secteur ou rubrique
		$query="SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rub";
		$row=spip_fetch_array(spip_query($query));
		if($row['id_secteur']!=$id_rub) { $id_sect = $row['id_secteur']; }
		
	// update final de spip_dw2_doc
		if ($type_categorie=="secteur")
			{ $class_cat=$id_sect; }
		else { $class_cat=$id_rub; }
		spip_query ("UPDATE spip_dw2_doc SET doctype='$doctype', id_doctype='$id_doctype', ".
					"categorie='".select_categorie_doc($class_cat)."' ".
					"WHERE id_document=$arg");
	
	$redirect=generer_url_ecrire("dw2_import");
	redirige_par_entete(rawurldecode($redirect."&id_serv=".$id_serv));
}


//
// annuler insert doc depuis serveur
function action_dw2actions_annuledocserveur($arg) {
	global $redirect;
	
	$arg = intval($arg); // arg -> id_document
	
	spip_query("DELETE FROM spip_dw2_doc WHERE id_document=$arg");
	spip_query("DELETE FROM spip_documents WHERE id_document=$arg");
	spip_query("DELETE FROM spip_documents_articles WHERE id_document=$arg");
	spip_query("DELETE FROM spip_documents_rubriques WHERE id_document=$arg");
	
	redirige_par_entete(rawurldecode($redirect));
}


//
// changer association fichier/serveur
function action_dw2actions_changerassociation($arg) {
	global $redirect;
	// arg -> $id_document
	global $id_serveur, $heberge, $url, $erazfichier;
	
	// recup de l'ancienne url du fichier
	if ($erazfichier=='oui') { 
		$query = spip_query("SELECT url FROM spip_dw2_doc WHERE id_document=$arg");
		$row = spip_fetch_array($query);
		$anc_url = $row['url'];
	}
	
	// update de ces champs !
	$nomfichier = substr(strrchr($url,'/'), 1);
	spip_query("UPDATE spip_dw2_doc SET heberge='$heberge', url='$url', id_serveur='$id_serveur' 
				WHERE id_document='$arg'");
	
	// supprimer le fichier local
	if ($erazfichier=='oui') {
		unlink("..".$anc_url);
	}

	redirige_par_entete(rawurldecode($redirect));
}



//
// enreg/modif/duplic un serveur
function action_dw2actions_serveredit($arg) {
	global $redirect;
	global $serv_ftp, $host_dir, $port, $login, $mot_passe, $site_distant, $chemin_distant, $id_serv, $duplic;
	
	// requis
	include_spip("inc/dw2_inc_deloc");
	
	$flag_err=array();
	
	if($duplic=="oui") { $faire_insert = true; }
	if(empty($id_serv)) { $faire_insert = true; }
	
		// On vérifie si les champs sont vides
	if(empty($serv_ftp) OR empty($login) OR empty($mot_passe) OR empty($site_distant) OR empty($chemin_distant))
    	{
		$flag_err[] = "1";
		$faire_insert = false;
		}
	
	// Verif pas de '/' en fin site_distant  +  scheme'http'/'ftp' + 2 blocs sep. '.' du host
	if(ereg("/$", $site_distant)) { $flag_err[] = "2"; $faire_insert = false; }
	if (!verif_scheme_host($site_distant)) { $flag_err[] = "6"; $faire_insert = false; }
	
	// controles syntaxe ftp... trop varié alors juste pas de slash de fin!
	if(ereg("/$", $serv_ftp)) { $flag_err[] = "5"; $faire_insert = false; }
					
	// pas de '/' en tete de $chemin_distant
	if(ereg("^/{1,}", $chemin_distant)) { $flag_err[] = "3"; $faire_insert = false; }
	if (!ereg("([^/]/{1})$", $chemin_distant)) { $flag_err[] = "4"; $faire_insert = false; }

	if(count($flag_err)=='0') {
		$maj = true;
	}
	
	// on corrige (tout est relatif) $port !
	$port = intval($port);
	
	if($faire_insert) {
		$query = "INSERT INTO spip_dw2_serv_ftp (serv_ftp, host_dir, port, login, mot_passe, site_distant, chemin_distant, date_crea) ".
				"VALUES ('$serv_ftp', '$host_dir', '$port', '$login', '$mot_passe', '$site_distant', '$chemin_distant', NOW())";
		$result = spip_query($query);
		$id_serv = spip_insert_id();
		spip_query("UPDATE spip_dw2_serv_ftp SET designe='"._T('dw:serveur')." - $id_serv' WHERE id_serv=$id_serv");
	}
	elseif($maj) {
		spip_query("UPDATE spip_dw2_serv_ftp 
					SET serv_ftp='$serv_ftp', host_dir='$host_dir', port='$port', login='$login', 
					mot_passe='$mot_passe', site_distant='$site_distant', chemin_distant='$chemin_distant' 
					WHERE id_serv=$id_serv");
	}
	
	if(count($flag_err)=='0') {
		// Controle de connexion
		$repertoire_dest = $host_dir.$chemin_distant;
		$retour_conex = connexion_serv($serv_ftp, $port, $login, $mot_passe, $repertoire_dest);
		$conex=$retour_conex[0];
		$message_conex=$retour_conex[1];
		@ftp_quit($conex);
		$retour = $message_conex;
	}
	else {
		$retour=join(",",$flag_err);
	}

	redirige_par_entete(rawurldecode($redirect."&id_serv=".$id_serv."&retour=".$retour));

}

// h.29/12 modif restriction telech generique
// pour document, article, rubrique et 'tous secteurs' (racine).
function action_dw2actions_restrictgen($arg) {
	global $redirect;
	global $restreint, $type;
	$arg = intval($arg); //id_
	$restreint = intval($restreint);
	
	if($type=='racine') {
		$q=spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent='0'");
		while($r=spip_fetch_array($q)) {
			$idr=$r['id_rubrique'];
			$ex=spip_query("SELECT id FROM spip_dw2_acces_restreint WHERE id_rubrique=$idr");
			// déjà une ligne ?
			if($s=spip_num_rows($ex)) {
				spip_query("UPDATE spip_dw2_acces_restreint SET restreint='$restreint' WHERE id_rubrique=$idr");
			}
			else {
				spip_query("INSERT INTO spip_dw2_acces_restreint (id_rubrique, restreint) VALUES ('$idr','$restreint')");
			}
		}
	}
	else {
		$q=spip_query("SELECT id FROM spip_dw2_acces_restreint WHERE id_$type=$arg");
		// déjà une ligne ?
		if($s=spip_num_rows($q)) {
			spip_query("UPDATE spip_dw2_acces_restreint SET restreint='$restreint' WHERE id_$type=$arg");
		}
		else {
			spip_query("INSERT INTO spip_dw2_acces_restreint (id_$type, restreint) VALUES ('$arg','$restreint')");
		}
	}
	redirige_par_entete(rawurldecode($redirect));
}

//h.08/02
// Efface de table acces_restreint rub-racine si TOUTES =0
function action_dw2actions_menageracine($arg) {
	global $redirect;
	global $tbl_racine; // tbl des id_rub-racine
	# arg : nbr_secteurs
	$tbl = explode(',',$tbl_racine);

	foreach($tbl as $id) {
		spip_query("DELETE FROM spip_dw2_acces_restreint WHERE id_rubrique=".$id);
	}
	redirige_par_entete(rawurldecode($redirect));
}


//h.12/02
// nettoyage catalogue DW2 (une 'tite erreur de manip ?!')
function action_dw2actions_netcat($arg) {
	global $redirect;
	global $choixselect; // choix sur 'date'
	global $jour, $mois, $annee, $heure, $minute;
	# arg : ne vaut rien ;-) ==>'rien'
	$date=$annee."-".$mois."-".$jour." ".$heure.":".$minute.":00";
	if($choixselect=='date') {
		spip_query("DELETE FROM spip_dw2_doc 
					WHERE date_crea BETWEEN '$date' AND NOW()
				");
	}
	
	redirige_par_entete(rawurldecode($redirect."&date=".$date));
}


?>
