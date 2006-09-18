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


	function Forms_install(){
		Form_verifier_base();
	}
	
	function Forms_uninstall(){
		include_spip('base/forms');
		include_spip('base/abstract_sql');
	}
	
	function Forms_verifier_base(){
		$version_base = 0.15;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['forms_base_version']) )
				&& (($current_version = $GLOBALS['meta']['forms_base_version'])==$version_base))
			return;

		include_spip('base/forms');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			// attention on vient peut etre d'une table spip-forms 1.8
			$desc = spip_abstract_showtable('spip_forms','',true);
			if (isset($desc['field'])) 
				$current_version=0.1;
			else {
				creer_base();
				ecrire_meta('forms_base_version',$current_version=$version_base);
			}
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
			spip_query("ALTER TABLE spip_forms CHANGE `descriptif` `descriptif` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `schema` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `email` `email` TEXT");
			spip_query("ALTER TABLE spip_forms CHANGE `texte` `texte` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.12);
		}
		if ($current_version<0.13){
			spip_query("ALTER TABLE spip_forms CHANGE `schema` `structure` TEXT");
			ecrire_meta('forms_base_version',$current_version=0.13);
		}
		if ($current_version<0.14){
			spip_query("ALTER TABLE spip_reponses ADD `id_article_export` BIGINT( 21 ) NOT NULL AFTER `id_auteur` ");
			ecrire_meta('forms_base_version',$current_version=0.14);
		}
		if ($current_version<0.15){
			spip_query("ALTER TABLE spip_reponses ADD `url` VARCHAR(255) NOT NULL AFTER `id_article_export` ");
			ecrire_meta('forms_base_version',$current_version=0.15);
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
		include_spip("inc/charsets");
		include_spip("inc/filtres");
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
		return $GLOBALS['cookie_prefix'].'cookie_form_'.$id_form;
	}

	function Forms_verif_cookie_sondage_utilise($id_form) {
		//var_dump($_COOKIE);
		$cookie_utilise=true;
		$nom_cookie = Forms_nom_cookie_form($id_form);
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!$cookie = addslashes($GLOBALS['cookie_form'])){
			if (!$cookie = $_COOKIE[$nom_cookie]) {
		  	$cookie_utilise=false;  // pas de cookie a l'horizon donc pas de reponse presumée
				//include_spip("inc/session");
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
		$s .= debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", true);
	
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

	function Forms_generer_mail_reponse_formulaire($id_form, $id_reponse){
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form=$id_form");
		if ($row = spip_fetch_array($result)) {
			$corps_mail = recuperer_fond('modeles/reponse_email',array('id_reponse'=>$id_reponse));
			$corps_mail_admin = recuperer_fond('modeles/reponse_email',array('id_reponse'=>$id_reponse,'mail_admin'=>'oui'));
			$champconfirm = $row['champconfirm'];
			$email = unserialize($row['email']);
			$email_dest = $email['defaut'];
			$mailconfirm = "";
			
			// recuperer l'email de confirmation
			$result2 = spip_query("SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ=".spip_abstract_quote($champconfirm));
			if ($row2 = spip_fetch_array($result2)) {
				$mailconfirm = $row2['valeur'];
			}

			// recuperer l'email d'admin
			$result2 = spip_query("SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ=".spip_abstract_quote($email['route']));
			if ($row2 = spip_fetch_array($result2)) {
				if (isset($email[$row2['valeur']]))
					$email_dest = $email[$row2['valeur']];
			}

			include_spip('inc/charset');
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
			$trans_tbl = array_flip ($trans_tbl);
			if ($mailconfirm !== '') {
				$head="From: formulaire@".$_SERVER["HTTP_HOST"]."\n";
				$sujet = $row['titre'];
				$dest = $mailconfirm;
				
				// mettre le texte dans un charset acceptable
				$mess_iso = unicode2charset(charset2unicode($corps_mail),'iso-8859-1');
				// regler les entites si il en reste
				$mess_iso = strtr($mess_iso, $trans_tbl);

				mail($dest, $sujet, $mess_iso, $head);
			}
			if ($email_dest != '') {
				$head="From: formulaire_$id_form@".$_SERVER["HTTP_HOST"]."\n";
				$sujet = $row['titre'];
				$dest = $email_dest;
				
				// mettre le texte dans un charset acceptable
				$mess_iso = unicode2charset(charset2unicode($corps_mail_admin),'iso-8859-1');
				// regler les entites si il en reste
				$mess_iso = strtr($mess_iso, $trans_tbl);
				
				mail($dest, $sujet, $mess_iso, $head);
		 	}
		}
	}

	function Forms_enregistrer_reponse_formulaire($id_form, &$erreur, &$reponse, $script_validation = 'valide_form', $script_args='') {
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
					include_spip("inc/sites");
					if (!recuperer_page($val)) {
						$erreur[$code] = _T("forms:site_introuvable");
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
			$url = parametre_url(self(),'id_form','');
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
				$query = "INSERT INTO spip_reponses (id_form, id_auteur, date, ip, url, statut, cookie) ".
					"VALUES ($id_form, '$id_auteur', NOW(), '$ip', ".spip_abstract_quote($url).", '$statut', '$cookie')";
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
					$url = generer_url_public($script_validation,"verif_cookie=oui&id_reponse=$id_reponse&hash=$hash".($script_args?"&$script_args":""));
					$r = $url;
				}
				else if (($email) || ($mailconfirm)) {
					$hash = calculer_action_auteur("forms confirme reponse $id_reponse");
					$url = generer_url_public($script_validation,"mel_confirm=oui&id_reponse=$id_reponse&hash=$hash".($script_args?"&$script_args":""));
					$r = $url;
	
					$reponse = $mailconfirm;
				}
			}
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
		
		if (!$icone) $icone = "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png";
		
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
					$vals[] = "<a href='$link'>"._T("forms:dupliquer")."</a>";
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
