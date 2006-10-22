<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
	function Forms_install(){
		include_spip('base/forms_upgrade');
		Forms_upgrade();
	}
	
	function Forms_uninstall(){
		include_spip('base/forms');
		include_spip('base/abstract_sql');
	}
	
	function Forms_deplacer_fichier_form($source, $dest) {
		include_spip('inc/getdocument');
		if ($ok = deplacer_fichier_upload($source, $dest, true))
			if (file_exists($source)) // argument move pas pris en compte avant spip 1.9.2
				@unlink($source);
	
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

	function Forms_extraire_reponse($id_reponse){
		// Lire les valeurs entrees
		$result = spip_query("SELECT * FROM spip_reponses_champs AS r JOIN spip_forms_champs AS ch ON ch.champ=r.champ WHERE r.id_reponse="._q($id_reponse)." ORDER BY ch.cle");
		$valeurs = array();
		$retour = urlencode(self());
		$libelles = array();
		$values = array();
		$url = array();
		while ($row = spip_fetch_array($result)) {
			$cle = $row['cle'];
			$libelles[$cle]=$row['titre'];
			$champ = $row['champ'];
			$type = $row['type'];
			if ($type == 'fichier') {
				$values[$cle][] = $row['valeur'];
				$url[$cle][] = generer_url_ecrire("forms_telecharger","id_reponse=$id_reponse&champ=$champ&retour=$retour");
			}
			else if (in_array($type,array('select','multiple'))) {
				if ($row3=spip_fetch_array(spip_query("SELECT * FROM spip_forms_champs_choix WHERE cle=$cle AND choix="._q($row['valeur']))))
					$values[$cle][]=$row3['titre'];
				else
					$values[$cle][]= $row['valeur'];
				$url[$cle][] = '';
			}
			else if ($type == 'mot') {
				$id_groupe = intval($row['extra_info']);
				$id_mot = intval($row['valeur']);
				if ($row3 = spip_fetch_array(spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe AND id_mot="._q($id_mot)))){
					$values[$cle][]=$row3['titre'];
					$url[$cle][]= generer_url_ecrire("mots_edit","id_mot=$id_mot");
				}
				else {
					$values[$cle][]= $row['valeur'];
					$url[$cle][] = '';
				}
			}
			else {
				$values[$cle][] = $row['valeur'];
				$url[$cle][] = '';
			}
		}
		return array($libelles,$values,$url);
	}
	
	function Forms_duplique_form(){
		$duplique = intval(_request('duplique_form'));
		if ($duplique && Forms_form_administrable($duplique)){
			include_spip('base/abstract_sql');
			// creation
			$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($duplique));
			$names = "";
			$values = "";
			if ($row = spip_fetch_array($result)) {
				foreach($row as $nom=>$valeur){
					if ($nom=='titre') $valeur = _T("forms:formulaires_copie",array('nom'=>$valeur));
					if ($nom!='id_form'){
						$names .= "$nom,";
						$values .= _q($valeur).",";
					}
				}
				$names = substr($names,0,strlen($names)-1);
				$values = substr($values,0,strlen($values)-1);
				spip_abstract_insert('spip_forms',"($names)","($values)");
				$id_form = spip_insert_id();
				if ($id_form){
					$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($duplique));
					while($row = spip_fetch_array($res)) {
						$names = "id_form,";
						$values = "$id_form,";
						foreach($row as $nom=>$valeur){
							if ($nom!='id_form'){
								$names .= "$nom,";
								$values .= _q($valeur).",";
							}
						}
						$names = substr($names,0,strlen($names)-1);
						$values = substr($values,0,strlen($values)-1);
						spip_query("REPLACE INTO spip_forms_champs ($names) VALUES ($values)");
					}
					$res = spip_query("SELECT * FROM spip_forms_champs_choix WHERE id_form="._q($duplique));
					while($row = spip_fetch_array($res)) {
						$names = "id_form,";
						$values = "$id_form,";
						foreach($row as $nom=>$valeur){
							if ($nom!='id_form'){
								$names .= "$nom,";
								$values .= _q($valeur).",";
							}
						}
						$names = substr($names,0,strlen($names)-1);
						$values = substr($values,0,strlen($values)-1);
						spip_query("REPLACE INTO spip_forms_champs_choix ($names) VALUES ($values)");
					}
				}
			}
		}
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

	function Forms_generer_mail_reponse_formulaire($id_form, $id_reponse, $env){
		if (!is_array($env)) $env=array();
		$modele_mail = 'form_reponse_email';
		if (isset($env['modele']))
			$modele_mail = $env['modele'];
		$result = spip_query("SELECT * FROM spip_forms WHERE id_form=$id_form");
		if ($row = spip_fetch_array($result)) {
			$modele = "modeles/$modele_mail";
			if ($f = find_in_path(($m = "$modele-$id_form").".html"))
				$modele = $m;
			$corps_mail = recuperer_fond($modele,array_merge($env,array('id_reponse'=>$id_reponse)));
			$corps_mail_admin = recuperer_fond($modele,array_merge($env,array('id_reponse'=>$id_reponse,'mail_admin'=>'oui')));
			$champconfirm = $row['champconfirm'];
			$email = unserialize($row['email']);
			$email_dest = $email['defaut'];
			$mailconfirm = "";
			
			// recuperer l'email de confirmation
			$result2 = spip_query("SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ="._q($champconfirm));
			if ($row2 = spip_fetch_array($result2)) {
				$mailconfirm = $row2['valeur'];
			}

			// recuperer l'email d'admin
			$result2 = spip_query("SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ="._q($email['route']));
			if ($row2 = spip_fetch_array($result2)) {
				if (isset($email[$row2['valeur']]))
					$email_dest = $email[$row2['valeur']];
			}

			include_spip('inc/mail');
			if ($mailconfirm !== '') {
				$head="From: formulaire@".$_SERVER["HTTP_HOST"]."\n";
				$sujet = $row['titre'];
				$dest = $mailconfirm;
				// mettre le texte dans un charset acceptable et sans entites
				//$mess_iso = unicode2charset(html2unicode(charset2unicode($corps_mail)),'iso-8859-1');
				//mail($dest, $sujet, $mess_iso, $head);
				$headers = "";
				if (preg_match(",<html>(.*)</html>,Uims",$corps_mail,$regs)){
					$charset = $GLOBALS['meta']['charset'];
					$headers .=
					"MIME-Version: 1.0\n".
					"Content-Type: text/html; charset=$charset\n".
					"Content-Transfer-Encoding: 8bit\n";
					if (preg_match(",<h[1-6]>(.*)</h[1-6]>,Uims",$regs[1],$hs))
						$sujet=$hs[1];
				}
				envoyer_mail($dest, $sujet, $corps_mail, "formulaire@".$_SERVER["HTTP_HOST"], $headers);
			}
			if ($email_dest != '') {
				$head="From: formulaire_$id_form@".$_SERVER["HTTP_HOST"]."\n";
				$sujet = $row['titre'];
				$dest = $email_dest;
				// mettre le texte dans un charset acceptable et sans entites
				//$mess_iso = unicode2charset(html2unicode(charset2unicode($corps_mail_admin)),'iso-8859-1');
				//mail($dest, $sujet, $mess_iso, $head);
				$headers = "";
				if (preg_match(",<html>.*</html>,Uims",$corps_mail_admin,$regs)){
					$charset = $GLOBALS['meta']['charset'];
					$headers .=
					"MIME-Version: 1.0\n".
					"Content-Type: text/html; charset=$charset\n".
					"Content-Transfer-Encoding: 8bit\n";
					if (preg_match(",<h[1-6]>(.*)</h[1-6]>,Uims",$regs[1],$hs))
						$sujet=$hs[1];
				}
				envoyer_mail($dest, $sujet, $corps_mail_admin, "formulaire@".$_SERVER["HTTP_HOST"], $headers);
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
	
		$res2 = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form));
		while($row2 = spip_fetch_array($res2)){
			$code = $row2['champ'];
			$type = $row2['type'];
			$val = _request($code);
			if (!$val || ($type == 'fichier' && !$_FILES[$code]['tmp_name'])) {
				if ($row2['obligatoire'] == 'oui')
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
				if ($row2['verif'] == 'oui') {
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
				else if ($row2['extra_info'] && $taille > ($row2['extra_info'] * 1024)) {
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
					"VALUES ($id_form, '$id_auteur', NOW(), '$ip', "._q($url).", '$statut', '$cookie')";
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

?>