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

include_spip('inc/forms');

function bloc_edition_champ($t, $link) {
	global $couleur_claire;

	$code = $t['code'];
	$obligatoire = $t['obligatoire'];
	$type = $t['type'];
	$type_ext = $t['type_ext'];

	if ($type != 'separateur'){
		$checked = ($obligatoire == 'oui') ? " checked='checked'" : "";
		echo "&nbsp; &nbsp; <input type='checkbox' name='champ_obligatoire' value='oui' id='obli_$code'$checked> ";
		echo "<label for='obli_$code'>"._L("ce champ est obligatoire")."</label>";
		echo "<br />\n";
	}

	if ($type == 'url') {
		$checked = ($t['verif'] == 'oui') ? " checked='checked'" : "";
		echo "&nbsp; &nbsp; <input type='checkbox' name='champ_verif' value='oui' id='verif_$code'$checked> ";
		echo "<label for='verif_$code'>"._L("v&eacute;rifier l'existence du site Web")."</label>";
		echo "<br />\n";
	}
	if ($type == 'select' || $type == 'multiple') {
		global $ajout_choix;

		echo "<div style='margin: 5px; padding: 5px; border: 1px dashed $couleur_claire;'>";
		echo _L("Liste des choix propos&eacute;s")."&nbsp;:<br />\n";
		foreach ($type_ext as $code_choix => $nom_choix) {
			if ($ajout_choix == $code_choix) {
				echo "<script type='text/javascript'><!-- \nvar antifocus_choix= false; // --></script>\n";
				$js = " onfocus=\"if(!antifocus_choix){this.value='';antifocus_choix=true;}\"";
			}
			else $js = "";
			echo "<input type='text' name='$code_choix' value=\"".entites_html($nom_choix)."\" ".
				"class='fondl verdana2' size='20'$js>";
			// 
			echo " <input style='display: none;' type='submit' name='modif_choix' value=\""._T('bouton_modifier')."\" class='fondo verdana2'>";
			$supp_link = $link;
			$supp_link = parametre_url($supp_link,'supp_choix', $code_choix);
			echo " &nbsp; <span class='verdana1'>[<a href='".$supp_link."#champ_visible'>".
				_L("supprimer ce choix")."</a>]</span>";
			echo "<br />\n";
		}
		echo "<br /><input type='submit' name='ajout_choix' value=\""._L("Ajouter un choix")."\" class='fondo verdana2'>";

		echo "</div>";
		if ($type=='select')
			echo "<br /><input type='submit' name='switch_select_multi' value=\""._L("Changer en choix multiple")."\" class='fondo verdana2'>";
		if ($type=='multiple')
			echo "<br /><input type='submit' name='switch_select_multi' value=\""._L("Changer en choix unique")."\" class='fondo verdana2'>";
		echo "<br />\n";
	}
	if ($type == 'mot') {
		echo "<label for='groupe_$code'>"._L("Groupe")."</label> :";
		echo " &nbsp;<select name='groupe_champ' value='0' id='groupe_$code' class='fondo verdana2'>\n";
		$query = "SELECT * FROM spip_groupes_mots ORDER BY titre";
		$result = spip_query($query);
		while ($row = spip_fetch_array($result)) {
			$id_groupe = $row['id_groupe'];
			$titre_groupe = supprimer_tags(typo($row['titre']));
			$selected = ($id_groupe == $type_ext['id_groupe']) ? " selected='selected'": "";
			echo "<option value='$id_groupe'$selected>$titre_groupe</option>\n";
		}
		echo "</select>";
		echo "<br />\n";
	}
	if ($type == 'fichier') {
		$taille = intval($type_ext['taille']);
		if (!$taille) $taille = '';
		echo "<label for='taille_$code'>"._L("Taille maximale (en ko)")."</label> : ";
		echo "<input type='text' name='taille_champ' value='$taille' id='taille_$code' class='fondo verdana2'>\n";
		echo "<br />\n";
	}
}

function modif_edition_champ($t) {
	$type = $t['type'];
	$type_ext = &$t['type_ext'];
	$code = $t['code'];
	
	if ($type == 'url') {
		global $champ_verif;
		if ($champ_verif) $t['verif'] = $champ_verif;
		else unset($t['verif']);
	}
	if ($type == 'select' || $type == 'multiple') {
		global $ajout_choix, $supp_choix;
		if ($ajout_choix) {
			$n = 1;
			$code_choix = $code.'_'.$n;
			while (isset($type_ext[$code_choix]))
				$code_choix = $code.'_'.(++$n);
			$type_ext[$code_choix] = _L("Nouveau choix");
			$ajout_choix = $code_choix;
		}
		foreach ($type_ext as $code_choix => $nom_choix) {
			if (isset($GLOBALS[$code_choix]))
				$type_ext[$code_choix] = $GLOBALS[$code_choix];
		}
		/*if ($supp_choix) {
			unset($type_ext[$supp_choix]);
		}*/
	}
	if ($type == 'mot') {
		if ($id_groupe = intval($GLOBALS['groupe_champ']))
			$type_ext['id_groupe'] = $id_groupe;
	}
	if ($type == 'fichier') {
		$type_ext['taille'] = intval($GLOBALS['taille_champ']);
	}
	
	return $t;
}

function code_nouveau_champ($schema,$type){
	$n = 1;
	$code = $type.'_'.strval($n);
	foreach ($schema as $t) {
		list($letype, $lenumero) = split('_', $t['code'] );
		if ($type == $letype)
		{
			$lenumero = intval($lenumero);
			if ($lenumero>= $n)
				$n=$lenumero+1;
			$code = $type.'_'.strval($n);
		}
	}
	return $code;
}

function exec_forms_edit(){
	global $id_form;
	global $new;
	global $supp_form;
	global $modif_champ;
	global $ajout_champ;
	global $retour;
	global $titre;
	global $descriptif;
	global $email;
	global $champconfirm;
	global $texte;
	global $sondage;
	global $modif_champ;
	global $nom_champ;
	global $champ_obligatoire;
	global $monter;
	global $descendre;
	global $supp_choix;
	global $supp_champ;
	
	$id_form = intval($id_form);
	$supp_form = intval($supp_form);

	if ($retour)
		$retour = urldecode($retour);
  include_ecrire("inc_presentation");
	include_ecrire("inc_config");

	$id_form = intval($id_form);
	if ($id_form) {
		$query = "SELECT COUNT(*) FROM spip_reponses WHERE id_form=$id_form AND statut='valide'";
		$result = spip_query($query);
		list($nb_reponses) = spip_fetch_array($result);
	}
	else $nb_reponses = 0;

	//
	// Modifications aux donnees de base du formulaire
	//
	if (Forms_form_administrable($id_form)) {
		if ($supp_form = intval($supp_form) AND $supp_confirme AND !$supp_rejet) {
			$query = "DELETE FROM spip_forms WHERE id_form=$supp_form";
			$result = spip_query($query);
			if ($retour) {
				Header("Location: $retour");
				exit;
			}
		}
	}

	if (Forms_form_editable($id_form)) {
		if ($new == 'oui' && $titre) {
			$schema = array();
			spip_query("INSERT INTO spip_forms (schema) VALUES ('".
				addslashes(serialize($schema))."')");
			$id_form = spip_insert_id();
			unset($new);
		}

		if ($id_form && $titre) {
			$query = "UPDATE spip_forms SET ".
				"titre='".addslashes($titre)."', ".
				"descriptif='".addslashes($descriptif)."', ".
				"sondage='".addslashes($sondage)."', ".
				"email='".addslashes($email)."', ".
				"champconfirm='".addslashes($champconfirm)."', ".
				"texte='".addslashes($texte)."' ".
				"WHERE id_form=$id_form";
			$result = spip_query($query);
		}
	}

	$form_link = generer_url_ecrire('forms_edit');
	$form_link = parametre_url($form_link,"id_form",$id_form);
	if ($new)
		$form_link = parametre_url($form_link,"new",$new);
	if ($retour) 
		$form_link = parametre_url($form_link,"retour",urlencode($retour));

	//
	// Recupere les donnees
	//
	if ($new == 'oui') {
		$titre = _L("Nouveau formulaire");
		$descriptif = "";
		$sondage = "non";
		$schema = array();
		$email = "";
		$champconfirm = "";
		$texte = "";
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	}
	else {
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
			$schema = unserialize($row['schema']);
			$email = $row['email'];
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];
		}
		$js_titre = "";
	}

	//
	// Modifications au schema du formulaire
	//

	unset($champ_visible);
	unset($nouveau_champ);

	if ($id_form && Forms_form_editable($id_form)) {
		$modif_schema = false;

		// Ajout d'un champ
		if (($type = $ajout_champ) && Forms_types_champs_autorises($type)) {
			$code = code_nouveau_champ($schema,$type);
			$nom = _L("Nouveau champ");
			$schema[] = array('code' => $code, 'nom' => $nom, 'type' => $type, 'type_ext' => array());
			$champ_visible = $nouveau_champ = $code;
			$modif_schema = true;
		}
		// Modif d'un champ
		if ($code = $modif_champ) {
			unset($index);
			foreach ($schema as $index => $t) {
				if ($code == $t['code']) break;
			}
			if (isset($index)) {
				// switch select to multi ou inversement
				if (isset($_POST['switch_select_multi'])){
					if ($t['type']=='select') $newtype = 'multiple';
					if ($t['type']=='multiple') $newtype = 'select';
					
					$newcode = code_nouveau_champ($schema,$newtype);
					$t['type'] = $newtype;
					$new_type_ext = array();
					foreach($t['type_ext'] as $key=>$type_ext)
						$new_type_ext[str_replace($t['code'],$newcode,$key)] = $type_ext;
					$t['code'] = $newcode;
					$t['type_ext'] = $new_type_ext;
				}
				$t['nom'] = $nom_champ;
				$t['obligatoire'] = $champ_obligatoire;
				$t = modif_edition_champ($t);
				if (!$t['type_ext']) $t['type_ext'] = array();
				$schema[$index] = $t;
				$modif_schema = true;
			}
			$champ_visible = $code;
		}
		// Cas particulier : ajout / suppression d'un choix
		/*if ($code = $ajout_choix) {
			unset($index);
			foreach ($schema as $index => $t) {
				if ($code == $t['code']) break;
			}
			if (isset($index)) {
				$type_ext = $t['type_ext'];
				$n = 1;
				$code_choix = $code.'_'.$n;
				while ($type_ext[$code_choix])
					$code_choix = $code.'_'.(++$n);
				$type_ext[$code_choix] = _L("Nouveau choix");
				$schema[$index]['type_ext'] = $type_ext;
				$champ_visible = $t['code'];
				$ajout_choix = $code_choix;
			}
			$modif_schema = true;
		}*/
		if ($code_choix = $supp_choix) {
			foreach ($schema as $index => $t) {
				if (is_array($t['type_ext']) && isset($t['type_ext'][$supp_choix])) {
					unset($t['type_ext'][$supp_choix]);
					if (!$t['type_ext']) $t['type_ext'] = array();
					$champ_visible = $t['code'];
					$schema[$index] = $t;
				}
			}
			$modif_schema = true;
		}
		// Suppression d'un champ
		if ($code = $supp_champ) {
			unset($index);
			foreach ($schema as $index => $t) {
				if ($code == $t['code']) break;
			}
			unset($schema[$index]);
			if (!$schema) $schema = array();
			$modif_schema = true;
		}

		// Monter / descendre un champ
		if (isset($monter) && $monter > 0) {

			$monter = intval($monter);
			$n = $monter;
			while (--$n) if ($schema[$n]) break;			
			if ($t = $schema[$n]) {
				$schema[$n] = $schema[$monter];
				$schema[$monter] = $t;
				$champ_visible = $schema[$n]['code'];
			}
			$modif_schema = true;
		}
		if (isset($descendre)) {
			$descendre = intval($descendre);
			$max = max(array_keys($schema));
			$n = $descendre;
			while (++$n < $max) if ($schema[$n]) break;
			if ($t = $schema[$n]) {
				$schema[$n] = $schema[$descendre];
				$schema[$descendre] = $t;
				$champ_visible = $schema[$n]['code'];
			}
			$modif_schema = true;
		}
		if ($id_form && Forms_form_editable($id_form)) {
			if ($modif_schema) {
				ksort($schema);
				$query = "UPDATE `spip_forms` SET `schema`='".addslashes(serialize($schema))."' ".
					"WHERE `id_form`=$id_form";
				spip_query($query);
			}
		}
	}

	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "forms");

	debut_gauche();


	echo "<br /><br />\n";

	if (Forms_form_administrable($id_form) && $nb_reponses) {
		debut_boite_info();

		icone_horizontale(_L("Suivi des r&eacute;ponses")."<br />".$nb_reponses." "._L("r&eacute;ponses"),
			generer_url_ecrire('forms_reponses',"id_form=$id_form"), "forum-public-24.gif", "rien.gif");
		icone_horizontale(_L("T&eacute;l&eacute;charger les r&eacute;ponses"),
			generer_url_ecrire('forms_telecharger',"id_form=$id_form"), "doc-24.gif", "rien.gif");

		fin_boite_info();
	}



	debut_droite();

	if ($supp_form && !$supp_confirme && !$supp_rejet) {
		echo "<p><strong>"._L("Attention :")."</strong> ";
		echo _L("Des r&eacute;ponses ont &eacute;t&eacute; faites &agrave; ce formulaire. ".
			"Voulez-vous vraiment le supprimer ?")."</p>\n";
		$link = parametre_url(self(),'supp_form', $supp_form);
		//$link->addVar('supp_form', $supp_form);
		//echo $link->getForm();
		echo "<form method='POST' action='"
			. $link
			. "' style='border: 0px; margin: 0px;'>";

		echo "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondl'>";
		echo " &nbsp; ";
		echo "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondl'>";
		echo "</form><br />\n";
	}


	if ($id_form) {
		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/form-24.png");

		gros_titre($titre);

		if ($descriptif) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('info_descriptif')."</strong> ";
			echo propre($descriptif);
			echo "</div>\n";
		}

		if ($email) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('email_2')."</strong> ";
			echo $email;
			echo "</div>\n";
		}
		if ($champconfirm){
			$champconfirm_known = false;
			echo "<div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._L('Confirmer la réponse par mail avec :')."</strong> ";
			foreach ($schema as $index => $t) {
				if (($t['type'] == 'email') && ($champconfirm == $t['code'])) {
					echo $t['nom'] . " ";
					$champconfirm_known = true;
				}
			}
			echo "</div>\n";
			if (($champconfirm_known == true) && ($texte)) {
				echo "<div align='left' border: 1px dashed #aaaaaa;'>";
				echo "<strong class='verdana2'>"._T('info_texte')."</strong> ";
				echo nl2br(entites_html($texte));
				echo "</div>\n";
			}
		}

		if (count($schema)) {
			echo "<br />";
			debut_cadre_relief();

			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_invisible("preview_form");
			echo "<strong class='verdana3' style='text-transform: uppercase;'>"
				._L("Apparence du formulaire")."</strong>";
			echo "</div>\n";

			echo debut_block_invisible("preview_form");
			echo _L("Voici une pr&eacute;visualisation du formulaire tel qu'il ".
				"appara&icirc;tra aux visiteurs du site public.")."<p>\n";
			echo "<div style='margin: 10px; padding: 10px; border: 1px dashed $couleur_foncee;'>";
			echo Forms_afficher_formulaire_schema($schema);
			echo "</div>\n";
			echo fin_block();

			fin_cadre_relief();
		}

		afficher_articles(_L("Articles utilisant ce formulaire"),
			", spip_forms_articles AS lien WHERE lien.id_article=articles.id_article ".
			"AND id_form=$id_form AND statut!='poubelle' ORDER BY titre");


		fin_cadre_relief();
	}


	//
	// Icones retour et suppression
	//
	if ($retour) {
		echo "<br />\n";
		echo "<div align='$spip_lang_right'>";
		icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_FORMS."/form-24.png", "rien.gif");
		echo "</div>\n";
	}
	if ($id_form && Forms_form_administrable($id_form)) {
		echo "<br />\n";
		echo "<div align='$spip_lang_right'>";
		$link = parametre_url(self(),'supp_form', $id_form);
		//$link = clone($form_link); //PHP5--> il faut cloner explicitement
		//$link->addVar('supp_form', $id_form);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('form_tous')));
		}
		if (!$nb_reponses) {
			$link=parametre_url($link,'supp_confirme', 'oui');
		}
		icone(_L("Supprimer ce formulaire"), $link, "../"._DIR_PLUGIN_FORMS."/form-24.png", "supprimer.gif");
		echo "</div>\n";
	}


	//
	// Edition des donnees du formulaire
	//
	if (Forms_form_editable($id_form)) {
		echo "<p>";
		debut_cadre_formulaire();

		echo "<div class='verdana2'>";
		//$link = new link();
		//$link = clone($form_link); //PHP5--> il faut cloner explicitement
		//echo $link->getForm('POST');
		echo "<form method='POST' action='"
			. $form_link
			. "' style='border: 0px; margin: 0px;'>";

		$titre = entites_html($titre);
		$descriptif = entites_html($descriptif);
		$email = entites_html($email);
		$texte = entites_html($texte);

		echo "<strong><label for='titre_form'>"._L("Titre du formulaire")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='titre' id='titre_form' CLASS='formo' ".
			"value=\"".entites_html($titre)."\" size='40'$js_titre><br />\n";

		echo "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
		echo "<br />";
		echo "<textarea name='descriptif' id='desc_form' class='forml' rows='4' cols='40' wrap='soft'>";
		echo entites_html($descriptif);
		echo "</textarea><br />\n";

		echo "<strong><label for='email_form'>"._T('email_2')."</label></strong> ";
		echo "<br />";
		echo "<input type='text' name='email' id='email_form' class='forml' ".
			"value=\"".entites_html($email)."\" size='40'$js_titre><br />\n";

		echo "<strong><label for='confirm_form'>"._L('Confirmer la réponse par mail avec :')."</label></strong> ";
		echo "<br />";
		echo "<select name='champconfirm' id='confirm_form' class='forml'>\n";
		echo "<option value=''";
		if ($champconfirm=='') echo " selected='selected'";
		echo ">"._L('Pas de mail confirmation')."</option>\n";
		$champconfirm_known = false;
		foreach ($schema as $index => $t) {
			if ($t['type'] == 'email'){
				echo "<option value='" . $t['code'] . "'";
				if ($champconfirm == $t['code']){
					echo " selected='selected'";
					$champconfirm_known = true;
				}
				echo ">" . $t['nom'] . "</option>\n";
			}
		}
	 	echo "</select><br />\n";
	 	if ($champconfirm_known == true){
			echo "<strong><label for='texte_form'>"._T('info_texte')."</label></strong>";
			echo "<br />";
			echo "<textarea name='texte' id='texte_form' class='formo' rows='4' cols='40' wrap='soft'>";
			echo entites_html($texte);
			echo "</textarea><br />\n";
		}
		else {
			echo "<input type='hidden' name='texte' value=\"" . entites_html($texte);
			echo "\" />\n";
	 	}


		debut_cadre_enfonce("statistiques-24.gif");
		echo "<strong>"._L("Sondage")."</strong> : ";
		echo _L("Si votre formulaire est un sondage, les r&eacute;sultats des champs ".
			"de type &laquo; s&eacute;lection &raquo; seront additionn&eacute;s et affich&eacute;s.");
		echo "<br /><br />";
		afficher_choix('sondage', $sondage, array(
			'non' => _L("Ce formulaire n'est pas un sondage"),
			'public' => _L("Ce formulaire est un sondage public. Les r&eacute;sultats seront accessibles aux visiteurs du site."),
			'prot' => _L("Ce formulaire est un sondage prot&eacute;g&eacute;. Les r&eacute;sultats ne seront accessibles que depuis l'interface priv&eacute;e.")
		));
		fin_cadre_enfonce();

		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

		echo "</form>";

		//
		// Creer / modifier des champs
		//
		if ($id_form) {
			echo "<a name='champs'></a>";
			echo "<p><hr><p>\n";
			echo "<div class='verdana3'>";
			echo "<strong>"._L("Champs du formulaire")."</strong><br />\n";
			echo _L("Vous pouvez ici cr&eacute;er et modifier les champs que les visiteurs ".
				"pourront remplir.");
			echo "</div>\n";

			if (count($schema)) {
				$keys = array_keys($schema);
				$index_min = min($keys);
				$index_max = max($keys);
			}

			foreach ($schema as $index => $t) {
				$code = $t['code'];
				$visible = ($code == $champ_visible);
				$nouveau = ($code == $nouveau_champ);
				$obligatoire = $t['obligatoire'];
				$aff_min = $index > $index_min;
				$aff_max = $index < $index_max;
				$type = $t['type'];

				if ($nouveau) echo "<a name='nouveau_champ'></a>";
				else if ($visible) echo "<a name='champ_visible'></a>";
				echo "<p>\n";
				if (!in_array($type,array('separateur','textestatique')))
					debut_cadre_relief();
				else
					debut_cadre_enfonce();
				
				echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
				if ($aff_min || $aff_max) {
					echo "<div class='verdana1' style='float: right; font-weight: bold;'>";
					if ($aff_min) {
						$link = parametre_url($form_link,'monter', $index);
						//$link = clone($form_link); //PHP5--> il faut cloner explicitement
						//$link->addVar('monter', $index);
						echo "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._L("monter")."'></a>";
					}
					if ($aff_min && $aff_max) {
						echo " | ";
					}
					if ($aff_max) {
						$link = parametre_url($form_link,'descendre', $index);
						//$link = clone($form_link); //PHP5--> il faut cloner explicitement
						//$link->addVar('monter', $index);
						echo "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._L("descendre")."'></a>";
					}
					echo "</div>\n";
				}

				echo $visible ? bouton_block_visible("champ_$code") : bouton_block_invisible("champ_$code");
				echo "<strong>".typo($t['nom'])."</strong>";
				echo "<br /></div>";
				echo "(".Forms_nom_type_champ($t['type']).")\n";
				echo $visible ? debut_block_visible("champ_$code") : debut_block_invisible("champ_$code");

				// Modifier un champ
				/*$link = new link();
				$link = clone($form_link); //PHP5--> il faut cloner explicitement
				$link->addVar('modif_champ', $code);
				echo $link->getForm('POST', '#champ_visible');*/
				echo "<form method='POST' action='"
					. $form_link . "#champ_visible"
					. "' style='border: 0px; margin: 0px;'>";
				echo "<input type='hidden' name='modif_champ' value='$code' />";

				echo "<div class='verdana2'>";
				echo "<p>";
				if ($nouveau) {
					echo "<script type='text/javascript'><!-- \nvar antifocus_champ = false; // --></script>\n";
					$js = " onfocus=\"if(!antifocus_champ){this.value='';antifocus_champ=true;}\"";
				}
				else $js = "";
				if ($type=='separateur'){
					echo "<label for='nom_$code'>"._L("Nom du Bloc")."</label> :";
					echo " &nbsp;<input type='text' name='nom_champ' id='nom_$code' value=\"".
						entites_html($t['nom'])."\" class='fondo verdana2' size='30'$js><br />\n";
				}
				else if ($type=='textestatique'){
					echo "<label for='nom_$code'>"._L("Texte")."</label> :<br/>";
					echo " &nbsp;<textarea name='nom_champ' id='nom_$code'  class='verdana2' style='width:100%;height:5em;' $js>".
						entites_html($t['nom'])."</textarea><br />\n";
				}
				else{
					echo "<label for='nom_$code'>"._L("Nom du champ")."</label> :";
					echo " &nbsp;<input type='text' name='nom_champ' id='nom_$code' value=\"".
						entites_html($t['nom'])."\" class='fondo verdana2' size='30'$js><br />\n";
					bloc_edition_champ($t, $form_link);
				}

				echo "<div align='right'>";
				echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo verdana2'></div>\n";
				echo "</div>\n";
				echo "</form>";
				// Supprimer un champ
				/*$link = new link();
				$link = clone($form_link); //PHP5--> il faut cloner explicitement
				$link->addVar('supp_champ', $code);*/
				$link = parametre_url($form_link,'supp_champ', $code);
				echo "<div style='float: left;'>";
				icone_horizontale(_L("Supprimer ce champ"), $link."#champs","../"._DIR_PLUGIN_FORMS. "/form-24.png", "supprimer.gif");
				echo "</div>\n";

				echo fin_block();
				if (!in_array($t['type'],array('separateur','textestatique')))
					fin_cadre_relief();
				else
					fin_cadre_enfonce();
			}

			// Ajouter un champ
			echo "<p>";
			debut_cadre_enfonce();
			/*$link = new link();
			$link = clone($form_link); //PHP5--> il faut cloner explicitement
			echo $link->getForm('POST', '#nouveau_champ');*/
			echo "<form method='POST' action='"
				. $form_link. "#nouveau_champ"
				. "' style='border: 0px; margin: 0px;'>";
			echo "<strong>"._L("Ajouter un champ")."</strong><br />\n";
			echo _L("Cr&eacute;er un champ de type&nbsp;:");
			echo " \n";
			$types = array('ligne', 'texte', 'email', 'url', 'select', 'multiple', 'fichier', 'mot','separateur','textestatique');
			echo "<select name='ajout_champ' value='' class='fondo'>\n";
			foreach ($types as $type) {
				echo "<option value='$type'>".Forms_nom_type_champ($type)."</option>\n";
			}
			echo "</select>\n";
			echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' VALUE='"._T('bouton_valider')."' class='fondo'>";
			echo "</form>\n";
			fin_cadre_enfonce();
		}

		echo "</div>\n";

		fin_cadre_formulaire();
	}


	fin_page();
}
?>
