<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

	function Forms_install(){
		Form_verifier_base();
	}
	
	function Forms_uninstall(){
		include_spip('base/forms');
		include_spip('base/abstract_sql');
	}
	
	function Forms_verifier_base(){
		$version_base = 0.13;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['forms_base_version']) )
				&& (($current_version = $GLOBALS['meta']['forms_base_version'])==$version_base))
			return;

		include_spip('base/forms');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('forms_base_version',$current_version=$version_base);
		}
		if ($current_version<0.11){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			$query = "ALTER TABLE spip_forms CHANGE `email` `email` TEXT NOT NULL ";
			$res = spip_query($query);
			$query = "SELECT * FROM spip_forms";
			$res = spip_query($query);
			while ($row = spip_fetch_array($res)){
				$email = $row['email'];
				$id_form = $row['id_form'];
				if (unserialize($email)==FALSE){
					$email=addslashes(serialize(array('defaut'=>$email)));
					$query = "UPDATE spip_forms SET email='$email' WHERE id_form=$id_form";
					spip_query($query);
				}
			}
			ecrire_meta('forms_base_version',$current_version=0.11);
		}
		if ($current_version<0.12){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			spip_query("ALTER TABLE `spip_forms` CHANGE `descriptif` `descriptif` TEXT");
			spip_query("ALTER TABLE `spip_forms` CHANGE `schema` `schema` TEXT");
			spip_query("ALTER TABLE `spip_forms` CHANGE `email` `email` TEXT");
			spip_query("ALTER TABLE `spip_forms` CHANGE `texte` `texte` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.12);
		}
		if ($current_version<0.13){
			spip_query("ALTER TABLE `spip_forms` CHANGE `schema` `structure` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.13);
		}
		
		ecrire_metas();
	}

	function Forms_deplacer_fichier_form($source, $dest) {
		// Securite
		if (strstr($dest, "..")) {
			exit;
		}
	
		$ok = @rename($source, $dest);
		if (!$ok) $ok = @move_uploaded_file($source, $dest);
		if ($ok)
			@chmod($dest, 0666);
		else {
			@unlink($source);
		}
	
		return $ok;
	}

	function Forms_nommer_fichier_form($orig, $dir) {
		include_ecrire("inc_charsets.php");
		include_ecrire("inc_filtres.php");
		if (ereg("^(.*)\.([^.]+)$", $orig, $match)) {
			$ext = strtolower($match[2]);
			$orig = $match[1];
		}
		$base = ereg_replace("[^.a-zA-Z0-9_=-]+", "_", 
			translitteration(supprimer_tags(basename($orig))));
		$n = 0;
		$fichier = $base.'.'.$ext;
		while (@file_exists($dir . $fichier)) {
			$fichier = $base.'-'.(++$n).'.'.$ext;
		}
		return $fichier;
	}

	function Forms_type_fichier_autorise($nom_fichier) {
		if (ereg("\.([^.]+)$", $nom_fichier, $match)) {
			$ext = addslashes(strtolower($match[1]));
			switch ($ext) {
			case 'htm':
				$ext = 'html';
				break;
			case 'jpeg':
				$ext = 'jpg';
				break;
			case 'tiff':
				$ext = 'tif';
				break;
			}
			$query = "SELECT * FROM spip_types_documents WHERE extension='$ext' AND upload='oui'";
			$result = spip_query($query);
			return (spip_num_rows($result) > 0);
		}
		return false;
	}

	// Fonction utilitaires
	function Forms_form_editable($id_form = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}
	
	function Forms_form_administrable($id_form = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}

	function Forms_nom_cookie_form($id_form) {
		return 'spip_cookie_form_'.$id_form;
	}

	function Forms_verif_cookie_sondage_utilise($id_form) {
		//var_dump($_COOKIE);
		$cookie_utilise=true;
		$nom_cookie = 'spip_cookie_form_'.$id_form;
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!$cookie = addslashes($GLOBALS['cookie_form'])){
			if (!$cookie = $_COOKIE[$nom_cookie]) {
		  	$cookie_utilise=false;  // pas de cookie a l'horizon donc pas de reponse presumée
				//include_ecrire("inc_session.php");
				//$cookie = creer_uniqid();
			}
		}
		$query = "SELECT id_reponse FROM spip_reponses ".
			"WHERE id_form=$id_form AND cookie='".addslashes($cookie)."'";
		if (!spip_num_rows(spip_query($query)))
		  $cookie_utilise=false;  // cet utilisateur n'a pas deja repondu !
		return $cookie_utilise;
	}

	//
	// Afficher un pave formulaires dans la colonne de gauche
	// (edition des articles)
	
	function Forms_afficher_insertion_formulaire($id_article) {
		global $connect_id_auteur, $connect_statut;
		global $couleur_foncee, $couleur_claire, $options;
		global $spip_lang_left, $spip_lang_right;
	
		$s = "";
		// Ajouter un formulaire
		$s .= "\n<p>";
		$s .= debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/form-24.png", true);
	
		$s .= "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
		$s .= bouton_block_invisible("ajouter_form");
		$s .= "<strong class='verdana3' style='text-transform: uppercase;'>"
			._T("forms:article_inserer_un_formulaire")."</strong>";
		$s .= "</div>\n";
	
		$s .= debut_block_invisible("ajouter_form");
		$s .= "<div class='verdana2'>";
		$s .= _T("forms:article_inserer_un_formulaire_detail");
		$s .= "</div>";
	
		$query = "SELECT id_form, titre FROM spip_forms ORDER BY titre";
		$result = spip_query($query);
		if (spip_num_rows($result)) {
			$s .= "<br />\n";
			$s .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$s .= "<div class='plan-articles'>";
			while ($row = spip_fetch_array($result)) {
				$id_form = $row['id_form'];
				$titre = typo($row['titre']);
				
				$link = generer_url_ecrire('forms_edit',"id_form=$id_form&retour=".urlencode(self()));
				$s .= "<a href='".$link."'>";
				$s .= $titre."</a>\n";
				$s .= "<div class='arial1' style='text-align:$spip_lang_right;color: black; padding-$spip_lang_left: 4px;' "."title=\""._T("forms:article_recopier_raccourci")."\">";
				$s .= "<strong>&lt;form".$id_form."&gt;</strong>";
				$s .= "</div>";
			}
			$s .= "</div>";
			$s .= "</div>";
		}
	
		// Creer un formulaire
		if (Forms_form_editable()) {
			$s .= "\n<br />";
			$link = generer_url_ecrire('forms_edit',"new=oui&retour=".urlencode(self()));
			$s .= icone_horizontale(_T("forms:icone_creer_formulaire"),
				$link, "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "creer.gif", false);
		}
	
		$s .= fin_block();
	
		$s .= fin_cadre_relief(true);
		return $s;
	}

	function Forms_nom_type_champ($type) {
		static $noms;
		if (!$noms) {
			$noms = array(
				'ligne' => _T("forms:champ_type_ligne"),
				'texte' => _T("forms:champ_type_texte"),
				'url' => _T("forms:champ_type_url"),
				'email' => _T("forms:champ_type_email"),
				'select' => _T("forms:champ_type_select"),
				'multiple' => _T("forms:champ_type_multiple"),
				'fichier' => _T("forms:champ_type_fichier"),
				'mot' => _T("forms:champ_type_mot"),
				'separateur' => _T("forms:champ_type_separateur"),
				'textestatique' => _T("forms:champ_type_textestatique")
			);
		}
		return ($s = $noms[$type]) ? $s : $type;
	}

	function Forms_types_champs_autorises($type = '') {
		static $t;
		if (!$t) {
			$t = array_flip(array('ligne', 'texte', 'url', 'email', 'select', 'multiple', 'fichier', 'mot','separateur','textestatique'));
		}
		return $type ? isset($t[$type]) : $t;
	}

	//
	// Affichage d'un champ
	//
	function Forms_afficher_champ_select($code, $id_champ, $liste, $value, $attributs = '') {
		$num_radio = 0;
		$flag_menu = (count($liste) > 6);
		// Selon le nombre de choix, on dessine une liste deroulante ou des boutons radio
		if ($flag_menu) {
			$checked = (!$value) ? " selected='selected'" : "";
			$r .= "&nbsp; <select name='$code' id='$id_champ' $attributs>\n";
			$r .= "<option value=''$selected> </option>\n";
		}
		$att = $flag_menu ? "selected" : "checked";
		foreach ($liste as $key => $val) {
			$val = typo($val);
			$id = $id_champ;
			if (++$num_radio>1)
				$id .= '_'.strval($num_radio);
			$checked = ($value == $key) ? "$att='$att'" : "";
			if ($flag_menu) {
				$r .= "<option value=\"$key\" $checked>".supprimer_tags($val)."</option>\n";
			}
			else {
				$r .= "<span class='spip_form_choix_unique'>";
				$r .= "&nbsp; <input type='radio' name='$code' id='$id' ".
					"value=\"$key\"$attributs $checked />";
				$r .= "<label for='$id'>$val</label>";
				$r .= "</span> \n";
			}
		}
		if ($flag_menu) {
			$r .= "</select><br />\n";
		}
		return $r;
	}

	function Forms_afficher_champ_multiple($code, $id_champ, $liste, $value, $attributs = '') {
		$num_checkbox = 0;
		$r = "";
		foreach ($liste as $key => $val) {
			$val = typo($val);
			$id = $id_champ;
			if (++$num_checkbox>1)
				$id .= '_'.strval($num_checkbox);
			$checked = isset($value[$key]) ? "checked='checked'" : "";
			$r .= "<span class='spip_form_choix_multiple'>";
			$r .= "&nbsp; <input type='checkbox' name='".$code."[]' id='$id' ".
				"value=\"$key\"$attributs $checked />";
			$r .= "<label for='$id'>$val</label>";
			$r .= "</span> \n";
		}
		return $r;
	}

	function Forms_afficher_champ_formulaire($t, $attributs = '', $erreur = false) {
		static $num_champ;
	
		$id_champ = 'champ'.strval(++$num_champ);
		$r = "<div class='spip_form_champ'>";
	
		$obligatoire = ($t['obligatoire'] == 'oui');
		$code = $t['code'];
		$nom = $t['nom'];
		$type = $t['type'];
		$type_ext = $t['type_ext'];
		
		$flag_label = (!in_array($type,array('select','textestatique')));
		$flag_champ = (!in_array($type,array('textestatique')));
		
		if ($flag_champ) $r .= "<span class='spip_form_label'>";
		if ($flag_label) $r .= "<label for='$id_champ'>";
		// Propre et pas typo, afin d'autoriser les notes (notamment)
		$r .= propre($nom);
		if ($flag_label) $r .= "</label>"; 
 		if ($flag_champ)
		{
			if ($obligatoire) 
				$r .= "<span class='spip_form_label_obligatoire'>"
							. _T('forms:info_obligatoire_02')
							. "</span>";
			$r .= " :";
			$r .= "</span>\n";
		}
	
		$class1 = $obligatoire ? "forml" : "formo";
		$class2 = $obligatoire ? "fondl" : "fondo";
	
		$span = "<span class='spip_form_label_details'>";
	
		switch ($type) {
		case 'email':
			$value = $erreur ? entites_html($GLOBALS[$code]) : "";
			$r .= $span . _T("forms:champ_email_details")."</span>";
			$r .= "<input type='text' name='$code' id='$id_champ' value=\"$value\" class='$class1' size='40'$attributs />";
			break;
		case 'url':
			$value = $erreur ? entites_html($GLOBALS[$code]) : "";
			$r .= $span . _T("forms:champ_url_details")."</span>";
			$r .= "<input type='text' name='$code' id='$id_champ' value=\"$value\" class='$class1' size='40'$attributs />";
			break;
		case 'ligne':
			$value = $erreur ? entites_html($GLOBALS[$code]) : "";
			$r .= "<input type='text' name='$code' id='$id_champ' value=\"$value\" class='$class1' size='40'$attributs />";
			break;
		case 'texte':
			$value = $erreur ? entites_html($GLOBALS[$code]) : "";
			$r .= "<textarea name='$code' id='$id_champ' class='$class1' rows='4' cols='40'$attributs>";
			$r .= $value;
			$r .= "</textarea>";
			break;
		case 'select':
			$value = $erreur ? $GLOBALS[$code] : "";
			$r .= Forms_afficher_champ_select($code, $id_champ, $type_ext, $value, "class='$class2'$attributs");
			break;
		case 'multiple':
			$value = ($erreur && is_array($GLOBALS[$code]))
				? array_flip($GLOBALS[$code]) : array();
			$r .= Forms_afficher_champ_multiple($code, $id_champ, $type_ext, $value, "class='$class2'$attributs");
			break;
		case 'fichier':
			// Pas de valeur par defaut pour les champs "fichier"
			$r .= "<input type='file' name='$code' id='$id_champ' class='$class2' size='25'$attributs>";
			break;
		case 'mot':
			// Distinction unique / multiple selon le parametrage du groupe de mots
			$id_groupe = intval($type_ext['id_groupe']);
			$query = "SELECT unseul FROM spip_groupes_mots WHERE id_groupe=$id_groupe";
			$row = spip_fetch_array(spip_query($query));
			$multiple = ($row['unseul'] != 'oui');
	
			// Recuperer les choix
			$query = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe ORDER BY titre";
			$result = spip_query($query);
			$liste = array();
			while ($row = spip_fetch_array($result)) {
				$id_mot = $row['id_mot'];
				$titre = $row['titre'];
				$liste[$id_mot] = $titre;
			}
	
			if ($multiple) {
				$value = ($erreur && is_array($GLOBALS[$code]))
					? array_flip($GLOBALS[$code]) : array();
				$r .= Forms_afficher_champ_multiple($code, $id_champ, $liste, $value, "class='$class2'$attributs");
			}
			else {
				$value = $erreur ? $GLOBALS[$code] : "";
				$r .= Forms_afficher_champ_select($code, $id_champ, $liste, $value, "class='$class2'$attributs");
			}
			break;
		}
		if ($msg = $erreur[$code]) {
			$r = "<p class='spip_form_erreur'>"._T("forms:form_erreur")." ".$msg."</p>" . $r;
		}
		$r .= "</div>";
		return $r;
	}

	//
	// Afficher les champs d'edition specifies par un structure
	//
	
	function Forms_afficher_formulaire_structure($structure, $link = '', $ancre = '', $remplir = false) {
		global $flag_ecrire, $les_notes, $spip_lang_left, $spip_lang_right;
		
		// Les formulaires ont leurs propres notes "de bas de page",
		// afin d'annoter les champs
		$notes_orig = $les_notes;
		$les_notes = "";
		
		$readonly = $flag_ecrire ? " readonly='readonly'" : "";
		$disabled = $flag_ecrire ? " disabled='disabled'" : "";
		$r = "";
		
		$champs = "";
		$fieldset = false;
	
		ksort($structure);
		foreach ($structure as $index => $t) {
			if ($t['type']!='separateur')
				$champs .= Forms_afficher_champ_formulaire($t, $readonly, $remplir);
			else{
				$champs .= "</fieldset><fieldset>\n";
				$fieldset = true;
			}
		}
	
		if ($fieldset)
			$champs = "<fieldset>\n" . $champs . "</fieldset>\n";

		$r .= $champs;
		$r .= "<div style='text-align:$spip_lang_right;'>";
		$r .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' ".
			"class='fondo spip_bouton'$disabled />";
		$r .= "</div></form>\n";
		if ($les_notes)
			$r .= "<div class='spip_form_notes'>$les_notes</div>\n";
		//$r .= "\n<!-- fin formulaire -->\n";
	
		$les_notes = $notes_orig;
		return $r;
	}


	function Forms_traduit_reponse($type,$code, $liste, $value) {
		$out = $value;
		switch ($type){
			case 'multiple':
			case 'select':
				if (isset($liste[$value])) $out = typo($liste[$value]);
				break;
			case 'mot':
				$id_groupe = intval($liste['id_groupe']);
				$id_mot = intval($value);
				$query_mot = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe AND id_mot=$id_mot";
				$result_mot = spip_query($query_mot);
				if ($row = spip_fetch_array($result_mot)) {
					$out = typo($row['titre']);
				}
		}
		return $out;
	}

	function Forms_generer_mail_reponse_formulaire($id_form, $id_reponse){
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$texte = $row['texte'];
			$texte = str_replace("\r\n","\n",$texte);
			$titre = $row['titre'];
			$champconfirm = $row['champconfirm'];
			$email = unserialize($row['email']);

			$form_summary = '';
			$email_dest = $email['defaut'];
			$mailconfirm = "";
			
			$structure = unserialize($row['structure']);
			// Ici on parcourt les valeurs entrees pour les champs demandes
			foreach ($structure as $index => $t) {
				$type = $t['type'];
				$code = $t['code'];
				$type_ext = $t['type_ext'];
				$form_summary .= $t['nom'] . " : ";
	
				$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ='$code'";
				$result2 = spip_query($query2);
				$reponses = '';
				while ($row2 = spip_fetch_array($result2)) {
					if ($email['route']==$code && isset($email[$row2['valeur']]))
						$email_dest = $email[$row2['valeur']];
					if ($code == $champconfirm)
						$mailconfirm = $row2['valeur'];
						
					//$reponses .= $row2['valeur'].", ";
					$reponses .= Forms_traduit_reponse($type, $code,$type_ext,$row2['valeur']).", ";
				}
				if (strlen($reponses) > 2)
					$form_summary .= substr($reponses,0,strlen($reponses)-2);
				$form_summary .= "\n";
			}
	
		 	if ($mailconfirm != '') {
				$head="From: formulaire@".$_SERVER["HTTP_HOST"]."\n";
				$message = $texte . "\n" . $form_summary;
				$sujet = $titre;
				$dest = $mailconfirm;
	
				mail($dest, $sujet, $message, $head);
			}
			if ($email_dest != '') {
				$head="From: formulaire_$id_form@".$_SERVER["HTTP_HOST"]."\n";
				$fullurl = _DIR_RESTREINT_ABS .generer_url_ecrire("forms_reponses");
	
				$link = parametre_url($fullurl,'id_form',$id_form);
				$message = $link . "\n";
				$message .= $form_summary;
				$message .= "mail confirmation :$mailconfirm:";
				$sujet = $titre;
				$dest = $email_dest;
	
				mail($dest, $sujet, $message, $head);
		 	}
		}
	}

	function Forms_enregistrer_reponse_formulaire($id_form, &$erreur, &$reponse) {
		$erreur = '';
		$reponse = '';
		$r = '';
	
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if (!$row = spip_fetch_array($result)) {
			$erreur['@'] = _T("forms:probleme_technique");
		}
		// Extraction des donnees pour l'envoi des mails eventuels
		//   accuse de reception et forward webmaster
		$email = unserialize($row['email']);
		$champconfirm = $row['champconfirm'];
		$mailconfirm = '';
	
		$structure = unserialize($row['structure']);
		// Ici on parcourt les valeurs entrees pour les champs demandes
		foreach ($structure as $index => $t) {
			$code = $t['code'];
			$type = $t['type'];
			$type_ext = $t['type_ext'];
			$val = $GLOBALS[$code];
			if (!$val || ($type == 'fichier' && !$_FILES[$code]['tmp_name'])) {
				if ($t['obligatoire'] == 'oui')
					$erreur[$code] = _T("forms:champ_necessaire");
				continue;
			}
			// Verifier la conformite des donnees entrees
			if ($type == 'email') {
				if (!strpos($val, '@') || !email_valide($val)) {
					$erreur[$code] = _T("forms:adresse_invalide");
				}
			}
			if ($type == 'url') {
				if ($t['verif'] == 'oui') {
					include_ecrire("inc_sites.php");
					if (!recuperer_page($val)) {
						$erreur[$code] = _T("site_introuvable");
					}
				}
			}
			if ($type == 'fichier') {
				if (!$taille = $_FILES[$code]['size']) {
					$erreur[$code] = _T("forms:echec_upload");
				}
				else if ($type_ext['taille'] && $taille > ($type_ext['taille'] * 1024)) {
					$erreur[$code] = _T("forms:fichier_trop_gros");
				}
				else if (!Forms_type_fichier_autorise($_FILES[$code]['name'])) {
					$erreur[$code] = _T("fichier_type_interdit");
				}
				if ($erreur[$code]) {
					supprimer_fichier($_FILES[$code]['tmp_name']);
				}
			}
		}
	
		// Si tout est bon, enregistrer la reponse
		if (!$erreur) {
			global $auteur_session;
			$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
			$ip = addslashes($GLOBALS['REMOTE_ADDR']);
			$ok = true;
			
			if ($row['sondage'] != 'non') {
				$statut = 'attente';
				$cookie = addslashes($GLOBALS['cookie_form']);
				$nom_cookie = Forms_nom_cookie_form($id_form);
			}
			else {
				$statut = 'valide';
				$cookie = '';
			}
			// D'abord creer la reponse dans la base de donnees
			if ($ok) {
				$query = "INSERT INTO spip_reponses (id_form, id_auteur, date, ip, statut, cookie) ".
					"VALUES ($id_form, '$id_auteur', NOW(), '$ip', '$statut', '$cookie')";
				spip_query($query);
				$id_reponse = spip_insert_id();
				if (!$id_reponse) {
					$erreur['@'] = _T("forms:probleme_technique");
					$ok = false;
				}
			}
			// Puis enregistrer les differents champs
			if ($ok) {
				$inserts = array();
				foreach ($structure as $index => $t) {
					$type = $t['type'];
					$code = $t['code'];
	
					if ($type == 'fichier') {
						if (!$val = $_FILES[$code] OR !$val['tmp_name']) continue;
						// Fichier telecharge : deplacer dans IMG, stocker le chemin dans la base
						$dir = sous_repertoire(_DIR_IMG, "protege");
						$dir = sous_repertoire($dir, "form".$id_form);
						$source = $val['tmp_name'];
						$dest = $dir.Forms_nommer_fichier_form($val['name'], $dir);
						if (!Forms_deplacer_fichier_form($source, $dest)) {
							$erreur[$code] = _T("forms:probleme_technique_upload");
							$ok = false;
						}
						else {
							$inserts[] = "($id_reponse, '$code', '".addslashes($dest)."')";
						}
					}
					else {
						if (!$val = $GLOBALS[$code]) continue;
						// Choix multiples : enregistrer chaque valeur separement
						else if (is_array($val)) {
							foreach ($val as $v) {
								$inserts[] = "($id_reponse, '$code', '".addslashes($v)."')";
							}
						}
						else {
							$inserts[] = "($id_reponse, '$code', '".addslashes($val)."')";
							if ($code == $champconfirm)
								$mailconfirm = $val;
						}
					}
				}
	
				if (!count($inserts)) {
					// Reponse vide => annuler
					$erreur['@'] = _T("forms:remplir_un_champ");
					$query = "DELETE FROM spip_reponses WHERE id_reponse=$id_reponse";
					spip_query($query);
					$ok = false;
				}
			}
			if ($ok) {
				$query = "INSERT INTO spip_reponses_champs (id_reponse, champ, valeur) ".
					"VALUES ".join(',', $inserts);
				spip_query($query);
				if ($row['sondage'] != 'non') {
					$hash = calculer_action_auteur("forms valide reponse sondage $id_reponse");
					$url = generer_url_public("valide_sondage","verif_cookie=oui&id_reponse=$id_reponse&hash=$hash");
					$r .= "<img src='".$url."' width='1' height='1' alt='' />";
				}
				else if (($email) || ($mailconfirm)) {
					$hash = calculer_action_auteur("forms confirme reponse $id_reponse");
					$url = generer_url_public("valide_sondage","mel_confirm=oui&id_reponse=$id_reponse&hash=$hash");
					$r .= "<img src='".$url."' width='1' height='1' alt='' />";
	
					$reponse = $mailconfirm;
				}
			}
		}
	
		return $r;
	}

	//
	// Afficher un formulaire
	//
	function Forms_afficher_formulaire($id_form) {
		static $num_ancre;
		global $flag_ecrire;
	
		$id_form = intval($id_form);
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if (!$row = spip_fetch_array($result)) return;
		
		$r = '';
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$sondage = $row['sondage'];
		$structure = unserialize($row['structure']);
	
		$ancre = 'form'.(++$num_ancre);

		if ($flag_ecrire) 
			$link = str_replace('&amp;', '&', self());
		else if ($GLOBALS['retour_form'])
			$link = $GLOBALS['retour_form'];
		else 
			$link = self();
		$retour = self();

		$formhead = "";
		$formhead = "<form method='post' action='$link#$ancre' enctype='multipart/form-data' style='border: 0px; margin: 0px;'>\n";
		$formhead .= "<div><input type='hidden' name='ajout_reponse' value='oui' />";
		$formhead .= "<input type='hidden' name='id_form' value='$id_form' />";
		$formhead .= "<input type='hidden' name='retour_form' value='$retour' />";
		if ($sondage != 'non') {
			$formhead .= "<input type='hidden' name='ajout_cookie_form' value='oui' />";
		}
		$formhead .= "</div>";
	
		$r .= "<a name='$ancre'></a>";
		$r .= "<div class='spip_forms'>\n";
		$r .= "<h3 class='spip'>".typo($titre)."</h3>\n";
	
		$flag_reponse = ($GLOBALS['ajout_reponse'] == 'oui' && $GLOBALS['id_form'] == $id_form);
		if ($flag_reponse) {
			$r .= Forms_enregistrer_reponse_formulaire($id_form, $erreur, $reponse);
			//print_r($_POST);
			if (!$erreur) {
				$r .= "<p class='spip_form_ok'>".
					_T("forms:reponse_enregistree");
				if ($sondage != 'non')
					$r .= " <a href='".self()."#$ancre"."'>"._T("forms:valider")."</a>";
				if ($reponse){
					$r .= "<span class='spip_form_ok_confirmation'>";
				  $r .= _T("forms:avis_message_confirmation",array('mail'=>$reponse));
				  $r .= "</span>";
				}
				$r .= "</p>";
			}
			else {
				if ($s = $erreur['@']) 
					$r .= "<p class='spip_form_erreur'>".$s."</p>";
			}
		}
		if (($sondage == 'public')&&(Forms_verif_cookie_sondage_utilise($id_form)==true)&&(_DIR_RESTREINT!="")){
			$r .= Forms_afficher_reponses_sondage($id_form);
			$r .= "</div>\n";
	 	}
		else	{
			if ($descriptif) {
				$r .= "<div class='spip_descriptif'>".propre($descriptif)."</div>";
			}
			if ($sondage == 'public' || ($sondage == 'prot' && $flag_ecrire)) {
				$url_sondage = ($flag_ecrire ? "../" : "").Forms_generer_url_sondage($id_form);
				$r .= "<div style='text-align:right'>";
				$r .= "<a href='".htmlspecialchars($url_sondage)."' class='spip_in' ".
					"target=\"spip_sondage\" onclick=\"javascript:window.open(this.href, 'spip_sondage', 'scrollbars=yes, ".
				"	resizable=yes, width=450, height=300'); return false;\"".
				" onkeypress=\"javascript:window.open(this.href, 'spip_sondage', 'scrollbars=yes, ".
				"	resizable=yes, width=450, height=300'); return false;\">";
				$r .= _T("forms:voir_resultats")."</a></div>";
				$r .= "<br />\n";
			}
			$r .= $formhead;
			$r .= Forms_afficher_formulaire_structure($structure, $form_link, $ancre, $erreur);
			$r .= "</div>\n";
	 	}
		return $r;
	}
	
	//
	// Afficher une liste de formulaires
	//
	
	function Forms_afficher_forms($titre_table, $requete, $icone = '') {
		global $couleur_claire, $couleur_foncee;
		global $connect_id_auteur;

		$tous_id = array();
		
		$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
		$from = $requete['FROM'] ? $requete['FROM'] : 'spip_articles AS articles';
		$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
		$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
		$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
		$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
		$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';
	
		$cpt = "$from$join$where$group";
		$tmp_var = substr(md5($cpt), 0, 4);

		if (!$group){
			$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
			if (! ($cpt = $cpt['n'])) return $tous_id ;
		}
		else
			$cpt = spip_num_rows(spip_query("SELECT $select FROM $cpt"));
		if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);
	
		$nb_aff = 1.5 * _TRANCHES;
		$deb_aff = intval(_request('t_' .$tmp_var));
	
		if ($cpt > $nb_aff) {
			$nb_aff = (_TRANCHES); 
			$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		}
		
		if (!$icone) $icone = "../"._DIR_PLUGIN_FORMS."/form-24.png";
		
		if ($cpt) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
	
			echo $tranches;
	
			$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
			$num_rows = spip_num_rows($result);
	
			$ifond = 0;
			$premier = true;
			
			$compteur_liste = 0;
			while ($row = spip_fetch_array($result)) {
				$vals = '';
				$id_form = $row['id_form'];
				$reponses = $row['reponses'];
				$titre = $row['titre'];

				$tous_id[] = $id_form;

				$retour = parametre_url(self(),'duplique_form','');
				$link = generer_url_ecrire('forms_edit',"id_form=$id_form&retour=".urlencode($retour));
				if ($reponses) {
					$puce = 'puce-verte-breve.gif';
				}
				else {
					$puce = 'puce-orange-breve.gif';
				}
	
				$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
				$vals[] = $s;
				
				//$s .= typo($titre);
				$s = icone_horizontale(typo($titre), $link,"../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "",false);
				$vals[] = $s;
				
				$s = "";
				$vals[] = $s;
	
				$s = "";
				if ($reponses) {
					$s .= _T("forms:nombre_reponses",array('nombre'=>$reponses));
				}
				$vals[] = $s;
				
				$s = "";
				if(Forms_form_administrable($id_form)){
					$link = parametre_url(self(),'duplique_form',$id_form);
					$vals[] = "<a href='$link'>"._L("Dupliquer")."</a>";
				}
				$vals[] = $s;

				$table[] = $vals;
			}
			spip_free_result($result);
			
			$largeurs = array('','','','','');
			$styles = array('arial11', 'arial11', 'arial1', 'arial1','arial1');
			echo afficher_liste($largeurs, $table, $styles);
			echo "</table>";
			echo "</div>\n";
		}
		return $tous_id;
	}


?>
