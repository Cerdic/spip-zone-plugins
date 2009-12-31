<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

/**
 * Generer un tableau descritpif complet de la structure d'un form
 * d'apres la base
 *
 * @param int $id_form
 * @param bool $complete : inclure la traduction des valeurs en clair (mot, select, multiple)
 * @return array
 */
function forms_structure($id_form, $complete = true){
	include_spip('inc/texte'); # typo et textebrut
	// Preparer la table de traduction code->valeur & mise en table de la structure pour eviter des requettes
	// a chaque ligne
	$structure = array();
	$rows = sql_allfetsel("*","spip_forms_champs","id_form=".intval($id_form),"","rang");
	foreach($rows as $row){
		$type = $row['type'];
		$champ = $row['champ'];
		foreach ($row as $k=>$v)
			$structure[$champ][$k] = $v;
		if ($complete){
			if (($type == 'select') OR ($type == 'multiple')){
				$rows2 = sql_allfetsel("*","spip_forms_champs_choix","id_form=".intval($id_form)." AND champ=".sql_quote($champ),"","rang");
				foreach($rows2 as $row2){
					$structure[$champ]['choix'][$row2['choix']] = $c = trim(textebrut(typo($row2['titre'])));
					$structure[$champ]['choixrev'][$c] = $row2['choix'];
				}
			}
			else if ($type == 'mot') {
				$id_groupe = intval($row['extra_info']);
				$rows2 = sql_allfetsel("id_mot, titre","spip_mots","id_groupe=".intval($id_groupe));
				foreach($rows2 as $row2){
					$structure[$champ]['choix'][$row2['id_mot']] = $c = trim(textebrut(typo($row2['titre'])));
					$structure[$champ]['choixrev'][$c] = $row2['id_mot'];
				}
			}
		}
	}
	return $structure;
}

/**
 * Recuperer les valeurs en base d'une donnee pour un champ
 * gere le cas scalaire/tableau
 * retourne toujours les valeurs non traduites
 *
 * @param unknown_type $id_donnee
 * @param unknown_type $id_form
 * @param unknown_type $champ
 * @return unknown
 */
function forms_valeurs($id_donnee,$id_form = NULL,$champ=NULL){
	static $unseul = array();
	$valeurs = array();
	if ($id_form===NULL
	  AND !$id_form = sql_getfetsel("id_form","spip_forms_donnees","id_donnee=".intval($id_donnee)))
		return $valeurs;

	$selchamp = "";
	if ($champ!==NULL) $selchamp = "d.champ=".sql_quote($champ)." AND";
	$rows = sql_allfetsel("*","spip_forms_donnees_champs AS d JOIN spip_forms_champs AS c ON (c.champ=d.champ AND c.id_form=".intval($id_form).")","$selchamp d.id_donnee=".intval($id_donnee));
	foreach($rows as $row){
		if ($row['type']=='multiple')
			$valeurs[$row['champ']][]= $row['valeur'];
		elseif ($row['type']=='mot'){
			$id_groupe = intval($row['extra_info']);
			if (!isset($unseul[$id_groupe])){
				$unseul[$id_groupe] = sql_getfetsel("unseul","spip_groupes_mots","id_groupe=".intval($id_groupe));
			}
			if ($unseul[$id_groupe]=='oui')
				$valeurs[$row['champ']]= $row['valeur'];
			else
				$valeurs[$row['champ']][]= $row['valeur'];
		}
		else
			$valeurs[$row['champ']]= $row['valeur'];
	}
	return $valeurs;
}

/**
 * deplacement d'un fichier uploade
 *
 * @param unknown_type $source
 * @param unknown_type $dest
 * @return unknown
 */
function forms_deplacer_fichier_form($source, $dest) {
	/* le core interdit l'upload depuis l'espace prive... pourquoi tant de haine ?
	include_spip('inc/getdocument');
	if ($ok = deplacer_fichier_upload($source, $dest, true))
		if (file_exists($source)) // argument move pas pris en compte avant spip 1.9.2
			@unlink($source);*/
	$ok = @rename($source, $dest);
	if (!$ok) $ok = @move_uploaded_file($source, $dest);
	if ($ok)
		@chmod($dest, _SPIP_CHMOD & ~0111);
	else {
		$f = @fopen($dest,'w');
		if ($f) {
			fclose ($f);
		} else {
			include_spip('inc/headers');
			redirige_par_entete(generer_url_action("test_dirs", "test_dir=". dirname($dest), true));
		}
		@unlink($dest);
	}
	return $ok;
}

/**
 * Nommage d'un fichier uploade
 *
 * @param unknown_type $orig
 * @param unknown_type $dir
 * @return unknown
 */
function forms_nommer_fichier_form($orig, $dir) {
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

/**
 * Verification du type du fichier uploade
 *
 * @param unknown_type $nom_fichier
 * @return unknown
 */
function forms_type_fichier_autorise($nom_fichier) {
	if (preg_match(",[.]([^.]+)$,", $nom_fichier, $match)) {
		$ext = strtolower($match[1]);
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
		return (sql_countsel("spip_types_documents","extension=".sql_quote($ext)." AND upload='oui'")>0);
	}
	return false;
}

/**
 * Nom du cookie de formulaire
 *
 * @param unknown_type $id_form
 * @return unknown
 */
function forms_nom_cookie_form($id_form) {
	return $GLOBALS['cookie_prefix'].'cookie_form_'.$id_form;
}

/**
 * Verifier si un cookie de sondage a ete utilise
 *
 * @param unknown_type $id_form
 * @return unknown
 */
function forms_verif_cookie_sondage_utilise($id_form) {
	global $auteur_session;
	$id_auteur = isset($GLOBALS['visiteur_session']['id_auteur']) ? intval($GLOBALS['visiteur_session']['id_auteur']) : 0;
	$cookie = $_COOKIE[forms_nom_cookie_form($id_form)];
	$where_cookie = "AND (";
	if ($cookie) {
		$where_cookie.="cookie=".sql_quote($cookie). ($id_auteur?" OR id_auteur=".intval($id_auteur):"");
	}
	else if ($id_auteur)
			$where_cookie.="id_auteur=".intval($id_auteur);
		else
			return false;
	$where_cookie .= ')';
	//On retourne le tableau des id_donnee de l'auteur ou false
	$rows = sql_allfetsel("id_donnee","spip_forms_donnees","statut='publie' AND id_form=".intval($id_form)." $where_cookie");
	if (count($rows))
		return array_map('reset',$rows);
	
	return false;
}

/**
 * Generer le mail envoye apres la reponse a un formulaire
 *
 * @param int $id_form
 * @param int $id_donnee
 * @param array $env
 */
function forms_generer_mail_reponse_formulaire($id_form, $id_donnee, $env){
	if (!is_array($env)) $env=array();
	$modele_mail_admin = 'form_reponse_email_admin';
	$modele_mail_confirm = 'form_reponse_email_confirm';
	if (isset($env['modele']))
		$modele_mail_confirm = $env['modele'];
	if (isset($env['modele_admin']))
		$modele_mail_admin = $env['modele_admin'];
	if ($row = sql_fetsel("*","spip_forms","id_form=".intval($id_form))) {
		$modele_admin = "modeles/$modele_mail_admin";
		$modele_confirm = "modeles/$modele_mail_confirm";
		if ($f = find_in_path(($m_admin = "$modele_admin-$id_form").".html"))
			$modele_admin = $m_admin;
		if ($f = find_in_path(($m_confirm = "$modele_confirm-$id_form").".html"))
			$modele_confirm = $m_confirm;
		$corps_mail_confirm = recuperer_fond($modele_confirm,array_merge($env,array('id_donnee'=>$id_donnee)));
		$corps_mail_admin = recuperer_fond($modele_admin,array_merge($env,array('id_donnee'=>$id_donnee,'mail_admin'=>'oui','documents_mail'=>$row['documents_mail'])));
		$champconfirm = $row['champconfirm'];
		$email = unserialize($row['email']);
		$email_dest = $email['defaut'];
		$mailconfirm = "";

		// recuperer documents
		$documents_mail = false;
		if ($row['documents_mail']=='oui'){
			$rows2 = sql_allfetsel("champ","spip_forms_champs","id_form=".intval($id_form)." AND type='fichier'");
			foreach($rows2 as $row2) {
				if ($row3 = sql_fetsel("valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee)." AND champ=".sql_quote($row2['champ']))) {
					$documents[] = $row3['valeur'];
					$documents_mail = true;
				}
			}
		}
		// recuperer l'email de confirmation
		if ($row2 = sql_fetsel("valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee)." AND champ=".sql_quote($champconfirm))) {
			$mailconfirm = $row2['valeur'];
		}

		// recuperer l'email d'admin
		if ($row2 = sql_fetsel("valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee)." AND champ=".sql_quote($email['route']))) {
			if (isset($email[$row2['valeur']]))
				$email_dest = $email[$row2['valeur']];
		}

		include_spip('inc/mail');
		$from_host = parse_url($GLOBALS['meta']['adresse_site']);
		$from_host = $from_host['host'];
		if ($mailconfirm !== '') {
			$from = $GLOBALS['meta']['email_webmaster'];
			//$from = "formulaire@$from_host";
			$head="From: $from\n";
			$sujet = $row['titre'];
			$dest = $mailconfirm;
			// mettre le texte dans un charset acceptable et sans entites
			//$mess_iso = unicode2charset(html2unicode(charset2unicode($corps_mail)),'iso-8859-1');
			//mail($dest, $sujet, $mess_iso, $head);
			$headers = "";
			if (preg_match(",<html>(.*)</html>,Uims",$corps_mail_confirm,$regs)){
				$charset = $GLOBALS['meta']['charset'];
				$headers .=
				"MIME-Version: 1.0\n".
				"Content-Type: text/html; charset=$charset\n".
				"Content-Transfer-Encoding: 8bit\n";
				if (preg_match(",<h[1-6]>(.*)</h[1-6]>,Uims",$regs[1],$hs))
					$sujet=$hs[1];
			}
			envoyer_mail($dest, $sujet, $corps_mail_confirm, $from, $headers);
		}
		if ($email_dest != '') {
			$from = $mailconfirm?$mailconfirm:"formulaire_$id_form@$from_host";
			$head="From: $from\n";
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
			//joindre les documents si necessaire
			if ($documents_mail && is_array($documents)) {
				$random_hash = md5(date('r', time()));
				$headers = "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
				$charset = $GLOBALS['meta']['charset'];
				$corps_mail_admin = 	"This is a multi-part message in MIME format." .
							"\r\n--PHP-mixed-".$random_hash .
							"\r\nContent-Type: text/plain; charset=\"" . $charset . "\"" .
							"\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . $corps_mail_admin;

				foreach($documents as $document){
					$filename = substr(strrchr($document, "/"), 1);
					$filetype = substr(strrchr($document, "."), 1);
					$corps_mail_admin .= "\r\n--PHP-mixed-".$random_hash."\r\nContent-Type: application/".$filetype."; name=\"".$filename."\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n" .
								chunk_split(base64_encode(file_get_contents($document)), 72)."\r\n";
				}
				$corps_mail_admin .= "\r\n--PHP-mixed-".$random_hash."--\r\n";
			}
			envoyer_mail($dest, $sujet, $corps_mail_admin, $from, $headers);
	 	}
	}
}

/**
 * Decrire une donnee avec les valeurs de ses champs principaux
 *
 * @param unknown_type $id_donnee
 * @param unknown_type $specifiant
 * @param unknown_type $linkable
 * @return unknown
 */
function forms_liste_decrit_donnee($id_donnee, $specifiant=true, $linkable=true){
	$t = array();$titreform="";
	$id_form = 0;
	$type_form = "";
	if ($specifiant) $specifiant = "c.specifiant='oui' AND ";
	else $specifiant="";
	if ($linkable) $linkable = " AND f.linkable='oui'";
	else $linkable = "";
	$rows2 = sql_allfetsel(
	  "c.titre,dc.valeur,f.titre AS titreform,f.id_form,f.type_form",
	  "spip_forms_donnees_champs AS dc 
	    JOIN spip_forms_donnees AS d ON d.id_donnee=dc.id_donnee
	    JOIN spip_forms_champs AS c ON c.champ=dc.champ AND c.id_form=d.id_form
	    JOIN spip_forms AS f ON f.id_form=d.id_form",
	  "$specifiant dc.id_donnee=".intval($id_donnee).$linkable,
	  "",
	  "c.rang");
	foreach($rows2 as $row2){
		$t[$row2['titre']] = $row2['valeur'];
		$titreform = $row2['titreform'];
		$id_form = $row2['id_form'];
		$type_form = $row2['type_form'];
	}
	return array($id_form,$titreform,$type_form,$t);
}

?>
