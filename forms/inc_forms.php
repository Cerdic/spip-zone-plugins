<?php

define_once('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS . basename(dirname(__FILE__))));
class Forms {
	/* static public */

	/* public static */
	function ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees" AND lire_meta("activer_forms")!="non") {

		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu["forms_tous"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."/form-24.png",  // icone
			_L("Formulaires et sondages") //titre
			);

		  // on voit le bouton dans la barre "forum_admin"
			$boutons_admin['forum_admin']->sousmenu["forms_reponses"]= new Bouton(
			"../"._DIR_PLUGIN_FORMS."/form-24.png",  // icone
			_L("Suivi des Reponses") //titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($onglets, $rubrique) {
		return $onglets;
	}
}

// definition de la fonction clone pour PHP<5.0
// a utiliser avec $link=clone($monautrelink)
// pour compatibilité PHP 4.0 et 5.0
if (version_compare(phpversion(), '5.0') < 0){
	if (eval('return !function_exists(clone);')==TRUE){
    eval('
    function clone($object) {
      return $object;
    }
    ');
  }
}

//
// Les deux fonctions "creer_repertoire" et "deplacer_fichier_upload" sont recopiees ici 
// a cause d'une mauvaise organisation des repertoires et inclusions...
//

function creer_repertoire_form($base, $subdir) {
	if (@file_exists("$base/.plat")) return '';
	$path = $base.'/'.$subdir;
	if (@file_exists($path)) return "$subdir/";

	@mkdir($path, 0777);
	@chmod($path, 0777);
	$ok = false;
	if ($f = @fopen("$path/.test", "w")) {
		@fputs($f, '<'.'?php $ok = true; ?'.'>');
		@fclose($f);
		include("$path/.test");
	}
	if (!$ok) {
		$f = @fopen("$base/.plat", "w");
		if ($f)
			fclose($f);
	}
	return ($ok? "$subdir/" : '');
}

function deplacer_fichier_form($source, $dest) {
	// Securite
	if (strstr($dest, "..")) {
		exit;
	}

	$ok = @copy($source, $dest);
	if (!$ok) $ok = @move_uploaded_file($source, $dest);
	if ($ok)
		@chmod($dest, 0666);
	else {
		@unlink($source);
		/*$f = fopen($dest,'w');
		if ($f)
			fclose ($f);
		unlink($dest);*/
	}

	return $ok;
}

function detruire_fichier_form($source) {
	@unlink($source);
}

function nommer_fichier_form($orig, $dir) {
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

function type_fichier_autorise($nom_fichier) {
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
function form_editable($id_form = 0) {
	global $connect_statut;
	return $connect_statut == '0minirezo';
}

function form_administrable($id_form = 0) {
	global $connect_statut;
	return $connect_statut == '0minirezo';
}

function nom_cookie_form($id_form) {
	return 'spip_cookie_form_'.$id_form;
}

function verif_cookie_sondage_utilise($id_form) {
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

function afficher_insertion_formulaire($id_article) {
	global $connect_id_auteur, $connect_statut;
	global $couleur_foncee, $couleur_claire, $options;
	global $spip_lang_left, $spip_lang_right;
	global $clean_link;

	// Ajouter un formulaire
	echo "\n<p>";
	debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/form-24.png", false);

	echo "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
	echo bouton_block_invisible("ajouter_form");
	echo "<strong class='verdana3' style='text-transform: uppercase;'>"
		._L("Ins&eacute;rer un formulaire")."</strong>";
	echo "</div>\n";

	echo debut_block_invisible("ajouter_form");
	echo "<div class='verdana2'>";
	echo _L("Vous pouvez ins&eacute;rer des formulaires dans vos articles afin de ".
		"permettre aux visiteurs d'entrer des informations. Choisissez un ".
		"formulaire dans la liste ci-dessous et recopiez le raccourci dans le texte ".
		"de l'article.");
	echo "</div>";

	$query = "SELECT id_form, titre FROM spip_forms ORDER BY titre";
	$result = spip_query($query);
	if (spip_num_rows($result)) {
		echo "<br />\n";
		echo "<div class='bandeau_rubriques' style='z-index: 1;'>";
		echo "<div class='plan-articles'>";
		while ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = typo($row['titre']);
			
			$link = new Link("?exec=forms_edit");
			$link->addVar("id_form", $id_form);
			$link->addVar("retour", $GLOBALS['clean_link']->getUrl());
			echo "<a href='".$link->getUrl()."'>";
			echo $titre."</a>\n";
			echo "<div class='arial1' style='text-align:$spip_lang_right;color: black; padding-$spip_lang_left: 4px;' ".
				"title=\""._L("Recopiez ce raccourci dans le texte de l'article pour ins&eacute;rer ce formulaire.").
				"\">";
			echo "<b>&lt;form".$id_form."&gt;</b>";
			echo "</div>";
		}
		echo "</div>";
		echo "</div>";
	}

	// Creer un formulaire
	if (form_editable()) {
		echo "\n<br />";
		$link = new Link("?exec=forms_edit&new=oui");
		$link->addVar('retour', $GLOBALS['clean_link']->getUrl());
		icone_horizontale(_L("Cr&eacute;er un nouveau formulaire"),
			$link->getUrl(), "../"._DIR_PLUGIN_FORMS."/form-24.png", "creer.gif");
	}

	echo fin_block();

	fin_cadre_relief();
}


function nom_type_champ($type) {
	static $noms;
	if (!$noms) {
		$noms = array(
			'ligne' => _L("ligne de texte"),
			'texte' => _L("texte"),
			'url' => _L("adresse de site Web"),
			'email' => _L("adresse e-mail"),
			'select' => _L("choix unique"),
			'multiple' => _L("choix multiple"),
			'fichier' => _L("fichier &agrave; t&eacute;l&eacute;charger"),
			'mot' => _L("mots-cl&eacute;s")
		);
	}
	return ($s = $noms[$type]) ? $s : $type;
}

function types_champs_autorises($type = '') {
	static $t;
	if (!$t) {
		$t = array_flip(array('ligne', 'texte', 'url', 'email', 'select', 'multiple', 'fichier', 'mot'));
	}
	return $type ? isset($t[$type]) : $t;
}

//
// Affichage d'un champ
//
function afficher_champ_select($code, $id_champ, $liste, $value, $attributs = '') {
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
			$r .= "&nbsp; <input type='radio' name='$code' id='$id' ".
				"value=\"$key\"$attributs $checked />";
			$r .= "<label for='$id'>$val</label><br />\n";
		}
	}
	if ($flag_menu) {
		$r .= "</select><br />\n";
	}
	return $r;
}

function afficher_champ_multiple($code, $id_champ, $liste, $value, $attributs = '') {
	$num_checkbox = 0;
	foreach ($liste as $key => $val) {
		$val = typo($val);
		$id = $id_champ;
		if (++$num_checkbox>1)
			$id .= '_'.strval($num_checkbox);
		$checked = isset($value[$key]) ? "checked='checked'" : "";
		$r .= "&nbsp; <input type='checkbox' name='".$code."[]' id='$id' ".
			"value=\"$key\"$attributs $checked />";
		$r .= "<label for='$id'>$val</label><br />\n";
	}
	return $r;
}

function afficher_champ_formulaire($t, $attributs = '', $erreur = false) {
	static $num_champ;

	$id_champ = 'champ'.strval(++$num_champ);
	$r = "<div class='spip_form_champ'>";

	$obligatoire = ($t['obligatoire'] == 'oui');
	$code = $t['code'];
	$nom = $t['nom'];
	$type = $t['type'];
	$type_ext = $t['type_ext'];
	
	$flag_label = ($type != 'select');
	$r .= "<strong>";
	if ($flag_label) $r .= "<label for='$id_champ'>";
	// Propre et pas typo, afin d'autoriser les notes (notamment)
	$r .= propre($nom);
	if ($flag_label) $r .= "</label>";
	$r .= "</strong>";
	if ($obligatoire) $r .= " "._T('forms:info_obligatoire_02');
	$r .= " :<br />\n";

	$class1 = $obligatoire ? "forml" : "formo";
	$class2 = $obligatoire ? "fondl" : "fondo";

	switch ($type) {
	case 'email':
		$value = $erreur ? entites_html($GLOBALS[$code]) : "";
		$r .= "<em>"._L("Veuillez entrer une adresse e-mail valide (de type vous@fournisseur.com).")."</em>";
		$r .= "<input type='text' name='$code' id='$id_champ' value=\"$value\" class='$class1' size='40'$attributs />";
		break;
	case 'url':
		$value = $erreur ? entites_html($GLOBALS[$code]) : "";
		$r .= "<em>"._L("Veuillez entrer une adresse Web valide (de type http://www.monsite.com/...).")."</em>";
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
		$r .= afficher_champ_select($code, $id_champ, $type_ext, $value, "class='$class2'$attributs");
		break;
	case 'multiple':
		$value = ($erreur && is_array($GLOBALS[$code]))
			? array_flip($GLOBALS[$code]) : array();
		$r .= afficher_champ_multiple($code, $id_champ, $type_ext, $value, "class='$class2'$attributs");
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
			$r .= afficher_champ_multiple($code, $id_champ, $liste, $value, "class='$class2'$attributs");
		}
		else {
			$value = $erreur ? $GLOBALS[$code] : "";
			$r .= afficher_champ_select($code, $id_champ, $liste, $value, "class='$class2'$attributs");
		}
		break;
	}
	if ($msg = $erreur[$code]) {
		$r = "<p class='spip_form_erreur'>"._L("Erreur&nbsp;:")." ".$msg."</p>" . $r;
	}
	$r .= "</div>";
	return $r;
}

//
// Afficher les champs d'edition specifies par un schema
//

function afficher_formulaire_schema($schema, $link = '', $ancre = '', $remplir = false) {
	global $flag_ecrire, $les_notes, $spip_lang_left, $spip_lang_right;

	if (!$link) $link = new Link();

	// Les formulaires ont leurs propres notes "de bas de page",
	// afin d'annoter les champs
	$notes_orig = $les_notes;
	$les_notes = "";
	
	$readonly = $flag_ecrire ? " readonly='readonly'" : "";
	$disabled = $flag_ecrire ? " disabled='disabled'" : "";
	$r = "";
	$r .= "\n<!-- debut formulaire -->\n";
	$r .= $link->getForm('post', '#'.$ancre, 'multipart/form-data');

	ksort($schema);
	foreach ($schema as $index => $t) {
		$r .= afficher_champ_formulaire($t, $readonly, $remplir);
	}

	$r .= "<div style='text-align:$spip_lang_right;'>";
	$r .= "<input type='submit' name='Valider' value='"._T('bouton_valider')."' ".
		"class='fondo spip_bouton'$disabled />";
	$r .= "</div></form>\n";
	if ($les_notes)
		$r .= "<div class='spip_form_notes'>$les_notes</div>\n";
	$r .= "\n<!-- fin formulaire -->\n";

	$les_notes = $notes_orig;
	return $r;
}

function generer_mail_reponse_formulaire($id_form, $id_reponse, $mailconfirm){
	$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
	$result = spip_query($query);
	if ($row = spip_fetch_array($result)) {
		$texte = $row['texte'];
		$texte = str_replace("\r\n","\n",$texte);
		$titre = $row['titre'];
		$email = $row['email'];
		$form_summary = '';
		$schema = unserialize($row['schema']);
		// Ici on parcourt les valeurs entrees pour les champs demandes
		foreach ($schema as $index => $t) {
			$type = $t['type'];
			$code = $t['code'];
			$form_summary .= $t['nom'] . " : ";

			$query2 = "SELECT * FROM spip_reponses_champs WHERE id_reponse='$id_reponse' AND champ='$code'";
			$result2 = spip_query($query2);
			$reponses = '';
			while ($row2 = spip_fetch_array($result2)) {
				$reponses .= $row2['valeur'].", ";
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
		if ($email != '') {
			$head="From: formulaire_$id_form@".$_SERVER["HTTP_HOST"]."\n";
			$link = new Link('',true);
			$fullurl="http://".$_SERVER["HTTP_HOST"].$link->getUrl();
			if ($v = strpos($fullurl,'?'))
			  $v = strrpos(substr($fullurl, 0, $v), '/');
			else $v = strrpos($fullurl, '/');
			$fullurl = substr($fullurl, 0 ,$v + 1);
			$fullurl .= _DIR_RESTREINT_ABS ."?exec=forms_reponses";

			$link = new Link($fullurl);
			$link->addVar('id_form', "$id_form");
			$message = $link->getUrl() . "\n";
			$message .= $form_summary;
			$sujet = $titre;
			$dest = $email;

			mail($dest, $sujet, $message, $head);
	 	}
	}
}

function enregistrer_reponse_formulaire($id_form, &$erreur, &$reponse) {
	$erreur = '';
	$reponse = '';
	$r = '';

	$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
	$result = spip_query($query);
	if (!$row = spip_fetch_array($result)) {
		$erreur['@'] = _L("Probl&agrave;me technique. Votre r&eacute;ponse ".
			"n'a pas pu &ecirc;tre prise en compte.");
	}
	// Extraction des donnees pour l'envoi des mails eventuels
	//   accuse de reception et forward webmaster
	$email = $row['email'];
	$champconfirm = $row['champconfirm'];
	$mailconfirm = '';

	$schema = unserialize($row['schema']);
	// Ici on parcourt les valeurs entrees pour les champs demandes
	foreach ($schema as $index => $t) {
		$code = $t['code'];
		$type = $t['type'];
		$type_ext = $t['type_ext'];
		$val = $GLOBALS[$code];
		if (!$val || ($type == 'fichier' && !$_FILES[$code]['tmp_name'])) {
			if ($t['obligatoire'] == 'oui')
				$erreur[$code] = _L("Ce champ doit &ecirc;tre rempli.");
			continue;
		}
		// Verifier la conformite des donnees entrees
		if ($type == 'email') {
			if (!strpos($val, '@') || !email_valide($val)) {
				$erreur[$code] = _L("Cette adresse n'est pas valide.");
			}
		}
		if ($type == 'url') {
			if ($t['verif'] == 'oui') {
				include_ecrire("inc_sites.php");
				if (!recuperer_page($val)) {
					$erreur[$code] = _L("Ce site n'a pas &eacute;t&eacute; trouv&eacute;.");
				}
			}
		}
		if ($type == 'fichier') {
			if (!$taille = $_FILES[$code]['size']) {
				$erreur[$code] = _L("Le transfert du fichier a &eacute;chou&eacute;.");
			}
			else if ($type_ext['taille'] && $taille > ($type_ext['taille'] * 1024)) {
				$erreur[$code] = _L("Ce fichier est trop gros.");
			}
			else if (!type_fichier_autorise($_FILES[$code]['name'])) {
				$erreur[$code] = _L("Ce type de fichier est interdit.");
			}
			if ($erreur[$code]) {
				detruire_fichier_form($_FILES[$code]['tmp_name']);
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
			$nom_cookie = nom_cookie_form($id_form);
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
				$erreur['@'] = _L("Probl&agrave;me technique. Votre r&eacute;ponse ".
					"n'a pas pu &ecirc;tre prise en compte.");
				$ok = false;
			}
		}
		// Puis enregistrer les differents champs
		if ($ok) {
			$inserts = array();
			foreach ($schema as $index => $t) {
				$type = $t['type'];
				$code = $t['code'];

				if ($type == 'fichier') {
					if (!$val = $_FILES[$code] OR !$val['tmp_name']) continue;
					// Fichier telecharge : deplacer dans IMG, stocker le chemin dans la base
					$dir = "IMG/".creer_repertoire_form("IMG", "protege");
					$dir = $dir.creer_repertoire_form($dir, "form".$id_form);
					$source = $val['tmp_name'];
					$dest = $dir.nommer_fichier_form($val['name'], $dir);
					if (!deplacer_fichier_form($source, $dest)) {
						$erreur[$code] = _L("Probl&egrave;me technique. Le transfert ".
							"du fichier a &eacute;chou&eacute;.");
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
				$erreur['@'] = _L("Veuillez remplir au moins un champ.");
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
				$hash = calculer_action_auteur("cookie $id_reponse");
				$link = new Link('plug.php?exec=valide_sondage');
				$link->addVar('verif_cookie', 'oui');
				$link->addVar('id_reponse', $id_reponse);
				$link->addVar('hash', $hash);
				$r .= "<img src='".$link->getUrl()."' width='1' height='1' alt='' />";
			}
			else if (($email) || ($mailconfirm)) {
				$hash = calculer_action_auteur("confirm $id_reponse");
				$link = new Link('plug.php?exec=valide_sondage');
				$link->addVar('mel_confirm', 'oui');
				$link->addVar('id_reponse', $id_reponse);
				$link->addVar('mailconfirm', $mailconfirm);
				$link->addVar('hash', $hash);
				$r .= "<img src='".$link->getUrl()."' width='1' height='1' alt='' />";

				$reponse = $mailconfirm;
			}
		}
	}

	return $r;
}


//
// Afficher un formulaire
//
function afficher_formulaire($id_form) {
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
	$schema = unserialize($row['schema']);

	$ancre = 'form'.(++$num_ancre);
	if ($flag_ecrire) 
		$link = new Link($GLOBALS['clean_link']->getUrl());
	else if ($GLOBALS['retour_form'])
		$link = new Link($GLOBALS['retour_form']);
	else 
		$link = new Link();
	$form_link = new link();
	$form_link = clone($link); //PHP5--> il faut cloner explicitement
	$form_link->addVar('ajout_reponse', 'oui');
	$form_link->addVar('id_form', $id_form);
	$form_link->addVar('retour_form', $link->getUrl());
	if ($sondage != 'non') {
		$form_link->addVar('ajout_cookie_form', 'oui');
	}

	$r .= "<a name='$ancre'></a>";
	$r .= "<div class='spip_forms'>\n";
	$r .= "<h3 class='spip'>".typo($titre)."</h3>\n";

	$flag_reponse = ($GLOBALS['ajout_reponse'] == 'oui' && $GLOBALS['id_form'] == $id_form);
	if ($flag_reponse) {
		$r .= enregistrer_reponse_formulaire($id_form, $erreur, $reponse);
		//print_r($_POST);
		if (!$erreur) {
			$r .= "<p class='spip_form_ok'>".
				_L("Votre r&eacute;ponse a &eacute;t&eacute; enregistr&eacute;e.");
			$link = new Link();
			if ($sondage != 'non')
				$r .= " <a href='".$link->getUrl()."#$ancre"."'>Valider</a>";
			if ($reponse)
			  $r .= _L("<br/>Un message de confirmation est envoy&eacute; &agrave; $reponse");
			$r .= "</p>";
		}
		else {
			if ($s = $erreur['@']) 
				$r .= "<p class='spip_form_erreur'>".$s."</p>";
		}
	}
	if (($sondage == 'public')&&(verif_cookie_sondage_utilise($id_form)==true)){
  	$r .= afficher_reponses_sondage($id_form);
		$r .= "</div>\n";
 	}
	else	{
		if ($descriptif) {
			$r .= "<div class='spip_descriptif'>".propre($descriptif)."</div>";
		}
		if ($sondage == 'public' || ($sondage == 'prot' && $flag_ecrire)) {
			$url_sondage = ($flag_ecrire ? "../" : "").generer_url_sondage($id_form);
			$r .= "<div style='text-align:right'>";
			$r .= "<a href='".htmlspecialchars($url_sondage)."' class='spip_in' ".
				"target=\"spip_sondage\" onclick=\"javascript:window.open(this.href, 'spip_sondage', 'scrollbars=yes, ".
			"	resizable=yes, width=450, height=300'); return false;\"".
			" onkeypress=\"javascript:window.open(this.href, 'spip_sondage', 'scrollbars=yes, ".
			"	resizable=yes, width=450, height=300'); return false;\">";
			$r .= _L("Voir les r&eacute;sultats")."</a></div>";
			$r .= "<br />\n";
		}
		$r .= afficher_formulaire_schema($schema, $form_link, $ancre, $erreur);
		$r .= "</div>\n";
 	}
	return $r;
}




//
// Afficher une liste de formulaires
//

function afficher_forms($titre_table, $requete, $icone = '') {
	global $couleur_claire, $couleur_foncee;
	global $connect_id_auteur;

	$tranches = afficher_tranches_requete($requete, 3);
	if (!$icone) $icone = "../"._DIR_PLUGIN_FORMS."/form-24.png";

	if ($tranches) {
		if ($titre_table) echo "<div style='height: 12px;'></div>";
		echo "<div class='liste'>";
		bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";

		echo $tranches;

	 	$result = spip_query($requete);
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

			$link = new Link("?exec=forms_edit");
			$link->addVar("id_form", $id_form);
			$link->addVar("retour", $GLOBALS['clean_link']->getUrl());
			if ($reponses) {
				$puce = 'puce-verte-breve.gif';
			}
			else {
				$puce = 'puce-orange-breve.gif';
			}

			$s = "<a href=\"".$link->getUrl()."\">";
			$s .= "<img src='img_pack/$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
			$s .= typo($titre);
			$s .= "</a> &nbsp;&nbsp;";
			$vals[] = $s;
			
			$s = "";
			$vals[] = $s;

			$s = "";
			if ($reponses) {
				$s .= $reponses." "._L("r&eacute;ponses");
			}
			$vals[] = $s;
			$table[] = $vals;
		}
		spip_free_result($result);
		
		$largeurs = array('','','');
		$styles = array('arial11', 'arial1', 'arial1');
		afficher_liste($largeurs, $table, $styles);
		echo "</table>";
		echo "</div>\n";
	}
	return $tous_id;
}




?>
