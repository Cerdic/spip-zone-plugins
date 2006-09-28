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
		echo "<label for='obli_$code'>"._T("forms:edit_champ_obligatoire")."</label>";
		echo "<br />\n";
	}

	if ($type == 'url') {
		$checked = ($t['verif'] == 'oui') ? " checked='checked'" : "";
		echo "&nbsp; &nbsp; <input type='checkbox' name='champ_verif' value='oui' id='verif_$code'$checked> ";
		echo "<label for='verif_$code'>"._T("forms:verif_web")."</label>";
		echo "<br />\n";
	}
	if ($type == 'select' || $type == 'multiple') {
		global $ajout_choix;

		echo "<div style='margin: 5px; padding: 5px; border: 1px dashed $couleur_claire;'>";
		echo _T("forms:liste_choix")."&nbsp;:<br />\n";
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
				_T("forms:supprimer_choix")."</a>]</span>";
			echo "<br />\n";
		}
		echo "<br /><input type='submit' name='ajout_choix' value=\""._T("forms:ajouter_choix")."\" class='fondo verdana2'>";

		echo "</div>";
		if ($type=='select')
			echo "<br /><input type='submit' name='switch_select_multi' value=\""._T("forms:changer_choix_multiple")."\" class='fondl verdana2'>";
		if ($type=='multiple')
			echo "<br /><input type='submit' name='switch_select_multi' value=\""._T("forms:changer_choix_unique")."\" class='fondl verdana2'>";
		echo "<br />\n";
	}
	if ($type == 'mot') {
		echo "<label for='groupe_$code'>"._T("forms:champ_nom_groupe")."</label> :";
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
		echo "<label for='taille_$code'>"._T("forms:taille_max")."</label> : ";
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
			$type_ext[$code_choix] = _T("forms:nouveau_choix");
			include_spip('inc/charset');
			$type_ext[$code_choix] = unicode2charset(html2unicode($type_ext[$code_choix]));
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

function code_nouveau_champ($structure,$type){
	$n = 1;
	$code = $type.'_'.strval($n);
	foreach ($structure as $t) {
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

function forms_update(){
	$id_form = intval(_request('id_form'));
	$new = _request('new');
	$supp_form = intval(_request('supp_form'));
	$modif_champ = _request('modif_champ');
	$ajout_champ = _request('ajout_champ');
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$champconfirm = _request('champconfirm');
	$texte = _request('texte');
	$sondage = _request('sondage');
	$nom_champ = _request('nom_champ');
	$champ_obligatoire = _request('champ_obligatoire');
	$monter = _request('monter');
	$descendre = _request('descendre');
	$supp_choix = _request('supp_choix');
	$supp_champ = _request('supp_champ');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	//
	// Modifications des donnees de base du formulaire
	//
	if (Forms_form_administrable($id_form)) {
		if ($supp_form = intval($supp_form) AND $supp_confirme AND !$supp_rejet) {
			$query = "DELETE FROM spip_forms WHERE id_form=$supp_form";
			$result = spip_query($query);
			if ($retour) {
				$retour = urldecode($retour);
				Header("Location: $retour");
				exit;
			}
		}
	}
	
	$nouveau_champ = $champ_visible = NULL;

	$structure = array();
	if (Forms_form_editable($id_form)) {
		// creation
		if ($new == 'oui' && $titre) {
			$structure = array();
			spip_query("INSERT INTO spip_forms (structure) VALUES ('".
				addslashes(serialize($structure))."')");
			$id_form = spip_insert_id();
			unset($new);
		}
		// maj
		if ($id_form && $titre) {
			$query = "UPDATE spip_forms SET ".
				"titre='".addslashes($titre)."', ".
				"descriptif='".addslashes($descriptif)."', ".
				"sondage='".addslashes($sondage)."', ".
				"email='".addslashes(serialize($email))."', ".
				"champconfirm='".addslashes($champconfirm)."', ".
				"texte='".addslashes($texte)."' ".
				"WHERE id_form=$id_form";
			$result = spip_query($query);
		}
		// lecture
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
			$structure = unserialize($row['structure']);
			$email = unserialize($row['email']);
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];
		}
	}	
	
	if ($id_form && Forms_form_editable($id_form)) {
		$modif_structure = false;
		$champ_visible = NULL;
		// Ajout d'un champ
		if (($type = $ajout_champ) && Forms_types_champs_autorises($type)) {
			$code = code_nouveau_champ($structure,$type);
			$nom = _T("forms:nouveau_champ");
			include_spip('inc/charset');
			$nom = unicode2charset(html2unicode($nom));
			$structure[] = array('code' => $code, 'nom' => $nom, 'type' => $type, 'type_ext' => array());
			$champ_visible = $nouveau_champ = $code;
			$modif_structure = true;
		}
		// Modif d'un champ
		if ($code = $modif_champ) {
			unset($index);
			foreach ($structure as $index => $t) {
				if ($code == $t['code']) break;
			}
			if (isset($index)) {
				// switch select to multi ou inversement
				if (isset($_POST['switch_select_multi'])){
					if ($t['type']=='select') $newtype = 'multiple';
					if ($t['type']=='multiple') $newtype = 'select';
					
					$newcode = code_nouveau_champ($structure,$newtype);
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
				$structure[$index] = $t;
				$modif_structure = true;
			}
			$champ_visible = $code;
		}
		// Cas particulier : suppression d'un choix
		if ($code_choix = $supp_choix) {
			foreach ($structure as $index => $t) {
				if (is_array($t['type_ext']) && isset($t['type_ext'][$supp_choix])) {
					unset($t['type_ext'][$supp_choix]);
					if (!$t['type_ext']) $t['type_ext'] = array();
					$champ_visible = $t['code'];
					$structure[$index] = $t;
				}
			}
			$modif_structure = true;
		}
		// Suppression d'un champ
		if ($code = $supp_champ) {
			unset($index);
			foreach ($structure as $index => $t) {
				if ($code == $t['code']) break;
			}
			if (isset($index)&&($structure[$index]['code']==$code)){
				unset($structure[$index]);
				if (!$structure) $structure = array();
				$modif_structure = true;
			}
		}

		// Monter / descendre un champ
		if (isset($monter) && $monter > 0) {

			$monter = intval($monter);
			$n = $monter;
			while (--$n) if ($structure[$n]) break;			
			if ($t = $structure[$n]) {
				$structure[$n] = $structure[$monter];
				$structure[$monter] = $t;
				$champ_visible = $structure[$n]['code'];
			}
			$modif_structure = true;
		}
		if (isset($descendre)) {
			$descendre = intval($descendre);
			$max = max(array_keys($structure));
			$n = $descendre;
			while (++$n < $max) if ($structure[$n]) break;
			if ($t = $structure[$n]) {
				$structure[$n] = $structure[$descendre];
				$structure[$descendre] = $t;
				$champ_visible = $structure[$n]['code'];
			}
			$modif_structure = true;
		}
		if ($id_form && Forms_form_editable($id_form)) {
			if ($modif_structure) {
				ksort($structure);
				$query = "UPDATE spip_forms SET `structure`='".addslashes(serialize($structure))."' ".
					"WHERE id_form=$id_form";
				spip_query($query);
			}
		}
	}
	
	return array($id_form,$champ_visible,$nouveau_champ);
}

function debut_block_route($id,$visible){
	$display = $visible?'block':'none';
	return "<script type='text/javascript'><!--
					document.write('<div id=\"$id\" style=\"display: $display; margin-top: 1;\">');
					//--></script>
					<noscript>
					<div id='bock_email_route_$code' style='display: block;'>
					</noscript>";	
}
function fin_block_route(){
	return 	"<script type='text/javascript'><!--
					document.write('<\/div>');
					//--></script>
					<noscript>
						</div>
					</noscript>";
}

function exec_forms_edit(){
	global $spip_lang_right;
	$id_form = intval(_request('id_form'));
	$new = _request('new');
	$supp_form = intval(_request('supp_form'));
	$modif_champ = _request('modif_champ');
	$ajout_champ = _request('ajout_champ');
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$champconfirm = _request('champconfirm');
	$texte = _request('texte');
	$sondage = _request('sondage');
	$nom_champ = _request('nom_champ');
	$champ_obligatoire = _request('champ_obligatoire');
	$monter = _request('monter');
	$descendre = _request('descendre');
	$supp_choix = _request('supp_choix');
	$supp_champ = _request('supp_champ');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	
  Forms_verifier_base();

	if ($retour)
		$retour = urldecode($retour);
  include_spip("inc/presentation");
	include_spip("inc/config");

	if ($id_form) {
		$query = "SELECT COUNT(*) FROM spip_reponses WHERE id_form=$id_form AND statut='valide'";
		$result = spip_query($query);
		list($nb_reponses) = spip_fetch_array($result,SPIP_NUM);
	}
	else $nb_reponses = 0;

	$clean_link = parametre_url(self(),'new','');
	$form_link = generer_url_ecrire('forms_edit');
	if ($new == 'oui' && !$titre)
		$form_link = parametre_url($form_link,"new",$new);
	if ($retour) 
		$form_link = parametre_url($form_link,"retour",urlencode($retour));

	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "forms","");
		
	unset($champ_visible);
	unset($nouveau_champ);
	//
	// Recupere les donnees
	//
	if ($new == 'oui' && !$titre) {
		$titre = _T("forms:nouveau_formulaire");
		include_spip('inc/charset');
		$titre = unicode2charset(html2unicode($titre));
		$descriptif = "";
		$sondage = "non";
		$structure = array();
		$email = array();
		$champconfirm = "";
		$texte = "";
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	}
	else {
		//
		// Modifications au structure du formulaire
		//
		list($id_form,$champ_visible,$nouveau_champ) = forms_update();
		
		$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
			$structure = unserialize($row['structure']);
			$email = unserialize($row['email']);
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];
		}
		$js_titre = "";
	}
	$form_link = parametre_url($form_link,"id_form",$id_form);
	$clean_link = parametre_url($clean_link,"id_form",$id_form);


	debut_gauche();


	echo "<br /><br />\n";
	
	if (Forms_form_administrable($id_form) && $nb_reponses) {
		debut_boite_info();

		$nretour = urlencode(self());
		icone_horizontale(_T("forms:suivi_reponses")."<br />".$nb_reponses." "._T("forms:reponses"),
			generer_url_ecrire('forms_reponses',"id_form=$id_form"), "forum-public-24.gif", "rien.gif");
		icone_horizontale(_T("forms:telecharger_reponses"),
			generer_url_ecrire('forms_telecharger',"id_form=$id_form&retour=$nretour"), "doc-24.gif", "rien.gif");

		fin_boite_info();
	}



	debut_droite();

	if ($supp_form && $supp_confirme==NULL && $supp_rejet==NULL) {
		if ($nb_reponses){
			echo "<p><strong>"._T("forms:attention")."</strong> ";
			echo _T("forms:info_supprimer_formulaire_reponses")."</p>\n";
		}
		else{
			echo "<p>";
			echo _T("forms:info_supprimer_formulaire")."</p>\n";
		}
		$link = parametre_url($clean_link,'supp_form', $supp_form);
		echo "<form method='POST' action='"
			. $link
			. "' style='border: 0px; margin: 0px;'>";
		echo "<div style='text-align:$spip_lang_right'>";
		echo "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
		echo " &nbsp; ";
		echo "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
		echo "</div>";
		echo "</form><br />\n";
	}


	if ($id_form) {
		debut_cadre_relief("../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png");

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
			echo $email['defaut'];
			echo "</div>\n";
		}
		if ($champconfirm){
			$champconfirm_known = false;
			echo "<div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('forms:confirmer_reponse')."</strong> ";
			foreach ($structure as $index => $t) {
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

		if (count($structure)) {
			echo "<br />";

			echo "<div style='padding: 2px; background-color: $couleur_claire; color: black;'>&nbsp;";
			echo bouton_block_invisible("preview_form");
			echo "<strong class='verdana3' style='text-transform: uppercase;'>"
				._T("forms:apparence_formulaire")."</strong>";
			echo "</div>\n";

			echo debut_block_invisible("preview_form");
			echo _T("forms:info_apparence")."<p>\n";
			//echo "<div class='spip_forms'>";
			echo propre("<form$id_form>");
			//echo "</div>\n";
			echo fin_block();

		}

		afficher_articles(_T("forms:articles_utilisant"),
			array('FROM' => 'spip_articles AS articles, spip_forms_articles AS lien',
			'WHERE' => "lien.id_article=articles.id_article AND id_form=$id_form AND statut!='poubelle'",
			'ORDER BY' => "titre"));

		fin_cadre_relief();
	}


	//
	// Icones retour et suppression
	//
	echo "<div style='text-align:$spip_lang_right'>";
	if ($retour) {
		icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "rien.gif",'right');
	}
	if ($id_form && Forms_form_administrable($id_form)) {
		echo "<div style='float:$spip_lang_left'>";
		$link = parametre_url($clean_link,'supp_form', $id_form);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('form_tous')));
		}
		icone(_T("forms:supprimer_formulaire"), $link, "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png", "supprimer.gif");
		echo "</div>";
	}
	echo "<div style='clear:both;'></div>";
	echo "</div>";


	//
	// Edition des donnees du formulaire
	//
	if (Forms_form_editable($id_form)) {
		echo "<p>";
		debut_cadre_formulaire();

		echo "<div class='verdana2'>";
		echo "<form method='POST' action='"
			. $form_link
			. "' style='border: 0px; margin: 0px;'>";

		$titre = entites_html($titre);
		$descriptif = entites_html($descriptif);
		$texte = entites_html($texte);

		echo "<strong><label for='titre_form'>"._T("forms:titre_formulaire")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='titre' id='titre_form' CLASS='formo' ".
			"value=\"".entites_html($titre)."\" size='40'$js_titre><br />\n";

		echo "<strong><label for='desc_form'>"._T('info_descriptif')."</label></strong>";
		echo "<br />";
		echo "<textarea name='descriptif' id='desc_form' class='forml' rows='4' cols='40' wrap='soft'>";
		echo entites_html($descriptif);
		echo "</textarea><br />\n";


		
		// Routage facultatif des emails en fonction d'un champ select
		$email_route_known = false;
		$jshide = "";
		$s = "";
		$options = "";
		if (is_array($structure)){
			foreach ($structure as $index => $t) {
				if ($t['type'] == 'select'){
					$visible = false;
					$code = $t['code'];
					$options .= "<option value='$code'";
					if ($email['route'] == $code){
						$options .= " selected='selected'";
						$email_route_known = $visible = true;
					}
					$options .= ">" . $t['nom'] . "</option>\n";
					$s .= debut_block_route("bock_email_route_$code",$visible);
					$jshide .=  "cacher_email_route('bock_email_route_$code');\n";
					
					$s .= "<table id ='email_route_$code'>\n";
					$s .= "<tr><th>".$t['nom']."</th><th>";
					$s .= "<strong><label for='email_route_$code'>"._T('email_2')."</label></strong>";
					$s .= "</th></tr>\n";
					$js = "";
					$type_ext = $t['type_ext'];
					foreach ($type_ext as $code_choix => $nom_choix) {
						$s .= "<tr><td>$nom_choix</td><td>";
						$s .= "<input type='text' name='email[$code_choix]' value=\"";
						$s .= isset($email[$code_choix])?entites_html($email[$code_choix]):"";
						$s .= "\" class='fondl verdana2' size='20'$js>";
						$s .= "</td></tr>";
					}
					$s .="</table>";
					$s .= fin_block_route("bock_email_route_$code",$visible);
				}
			}
			$jshide = "<script type='text/javascript'><!--
			function montrer_email_route(obj) {
				layer = findObj(obj);
				if (layer)
					layer.style.display = 'block';
			}
			function cacher_email_route(obj) {
				layer = findObj(obj);
				if (layer)
					layer.style.display = 'none';
			}
			function update_email_route_visibility(obj){
				$jshide
				cacher_email_route('bock_email_route_');
				montrer_email_route(obj);
			}
			//--></script>\n";
			echo $jshide;

			echo "<strong><label for='email_route_form'>"._T('forms:choisir_email')."</label></strong> ";
			echo "<br />";
			echo "<select name='email[route]' id='email_route_form' class='forml'";
			echo "onchange='update_email_route_visibility(\"bock_email_route_\"+options[selectedIndex].value)' ";
			echo ">\n";
			echo "<option value=''>"._T('forms:email_independant')."</option>\n";
			echo $options;
		 	echo "</select><br />\n";
		}
	 	
		echo debut_block_route("bock_email_route_",$email_route_known==false);
		echo "<strong><label for='email_form'>"._T('email_2')."</label></strong> ";
		echo "<br />";
		echo "<input type='text' name=\"email[defaut]\" id='email_form' class='forml' ".
			"value=\"".entites_html($email['defaut'])."\" size='40'$js_titre>\n";
		echo fin_block_route();
	 	echo $s;
		echo "<br/>";
	 	
	 	//-----

		echo "<strong><label for='confirm_form'>"._T('forms:confirmer_reponse')."</label></strong> ";
		echo "<br />";
		echo "<select name='champconfirm' id='confirm_form' class='forml'>\n";
		echo "<option value=''";
		if ($champconfirm=='') echo " selected='selected'";
		echo ">"._T('forms:pas_mail_confirmation')."</option>\n";
		$champconfirm_known = false;
		foreach ($structure as $index => $t) {
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
		echo "<strong>"._T("forms:sondage")."</strong> : ";
		echo _T("forms:info_sondage");
		echo "<br /><br />";
		afficher_choix('sondage', $sondage, array(
			'non' => _T("forms:sondage_non"),
			'public' => _T("forms:sondage_pub"),
			'prot' => _T("forms:sondage_prot")
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
			echo "<strong>"._T("forms:champs_formuaire")."</strong><br />\n";
			echo _T("forms:info_champs_formulaire");
			echo "</div>\n";

			if (count($structure)) {
				$keys = array_keys($structure);
				$index_min = min($keys);
				$index_max = max($keys);
			}

			foreach ($structure as $index => $t) {
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
						echo "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."monter-16.png' style='border:0' alt='"._T("forms:champ_monter")."'></a>";
					}
					if ($aff_min && $aff_max) {
						echo " | ";
					}
					if ($aff_max) {
						$link = parametre_url($form_link,'descendre', $index);
						echo "<a href='".$link."#champs'><img src='"._DIR_IMG_PACK."descendre-16.png' style='border:0' alt='"._T("forms:champ_descendre")."'></a>";
					}
					echo "</div>\n";
				}

				echo $visible ? bouton_block_visible("champ_$code") : bouton_block_invisible("champ_$code");
				echo "<strong>".typo($t['nom'])."</strong>";
				echo "<br /></div>";
				echo "(".Forms_nom_type_champ($t['type']).")\n";
				echo $visible ? debut_block_visible("champ_$code") : debut_block_invisible("champ_$code");

				// Modifier un champ
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
					echo "<label for='nom_$code'>"._T("forms:champ_nom_bloc")."</label> :";
					echo " &nbsp;<input type='text' name='nom_champ' id='nom_$code' value=\"".
						entites_html($t['nom'])."\" class='fondo verdana2' size='30'$js><br />\n";
				}
				else if ($type=='textestatique'){
					echo "<label for='nom_$code'>"._T("forms:champ_nom_texte")."</label> :<br/>";
					echo " &nbsp;<textarea name='nom_champ' id='nom_$code'  class='verdana2' style='width:100%;height:5em;' $js>".
						entites_html($t['nom'])."</textarea><br />\n";
				}
				else{
					echo "<label for='nom_$code'>"._T("forms:champ_nom")."</label> :";
					echo " &nbsp;<input type='text' name='nom_champ' id='nom_$code' value=\"".
						entites_html($t['nom'])."\" class='fondo verdana2' size='30'$js><br />\n";
					bloc_edition_champ($t, $form_link);
				}

				echo "<div align='right'>";
				echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo verdana2'></div>\n";
				echo "</div>\n";
				echo "</form>";
				// Supprimer un champ
				$link = parametre_url($form_link,'supp_champ', $code);
				echo "<div style='float: left;'>";
				icone_horizontale(_T("forms:supprimer_champ"), $link."#champs","../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "supprimer.gif");
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
			echo "<form method='POST' action='"
				. $form_link. "#nouveau_champ"
				. "' style='border: 0px; margin: 0px;'>";
			echo "<strong>"._T("forms:ajouter_champ")."</strong><br />\n";
			echo _T("forms:ajouter_champ_type");
			echo " \n";
			$types = array('ligne', 'texte', 'email', 'url', 'select', 'multiple', 'fichier', 'mot','separateur','textestatique');
			echo "<select name='ajout_champ' value='' class='fondo'>\n";
			foreach ($types as $type) {
				echo "<option value='$type'>".Forms_nom_type_champ($type)."</option>\n";
			}
			echo "</select>\n";
			echo " &nbsp; <input type='submit' name='valider' id='ajout_champ' VALUE='"._T('bouton_ajouter')."' class='fondo'>";
			echo "</form>\n";
			fin_cadre_enfonce();
		}

		echo "</div>\n";

		fin_cadre_formulaire();
	}


	fin_page();
}
?>
