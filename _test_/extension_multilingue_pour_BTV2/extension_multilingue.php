<?php
function BTG_cfg_on($v, $d=null) { return lire_config("barre_typo_generalisee/{$v}_barre_typo_generalisee", $d) == 'on'; }
function EM_cfg_on($v, $d=null) { return lire_config("ExtensionMultilingue/{$v}_ExtensionMultilingue", $d) == 'on'; }

function ExtensionMultilingue_BT_toolbox($params) {
	if (strpos($params['champ'], "zone_multilingue") !== FALSE) return $params;

	$fetch = function_exists('spip_fetch_array')?'spip_fetch_array':'sql_fetch';
	$nom_champ = $params['objet'];


	$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	switch($_GET['exec']) {
	case 'rubriques_edit':
		if (!EM_cfg_on("rubriques_$nom_champ",'')) return $params;
		if ($_GET['new'] == "oui") {
			$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
			$descriptif = $texte = "";
		} else {
			$id_rubrique_tmp = intval($_GET['id_rubrique']);
			$row = $fetch(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique_tmp'"));
			if (!$row) return $params;
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
		}
		break;
	case 'articles_edit':
		if (!EM_cfg_on("articles_$nom_champ",'')) return $params;
		if ($_GET['new'] == "oui") {
			$surtitre = "";
			$titre = filtrer_entites(_T('info_nouvel_article'));
			$soustitre = $descriptif = $chapo = $texte = $ps = "";
		} else {
			$id_article_tmp = intval($_GET['id_article']);
			$row = $fetch(spip_query("SELECT * FROM spip_articles WHERE id_article='$id_article_tmp'"));
			if (!$row) return $params;
			$surtitre = str_replace("\"","'",$row['surtitre']);
			$titre = str_replace("\"","'",$row['titre']);
			$soustitre = str_replace("\"","'",$row['soustitre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
			$chapo = $row['chapo'];
			$ps = $row['ps'];
		}
		break;
	case 'breves_edit':
		if (!EM_cfg_on("breves_$nom_champ",''))	return $params;
		if ($_GET['new'] == "oui") {
			$titre = filtrer_entites(_T('titre_nouvelle_breve'));
			$texte = $lien_titre = "";
		} else {
			$id_breve_tmp = intval($_GET['id_breve']);
			$row = $fetch(spip_query("SELECT * FROM spip_breves WHERE id_breve='$id_breve_tmp'"));
			if (!$row) return $params;
			$titre = str_replace("\"","'",$row['titre']);
			$texte = $row['texte'];
			$lien_titre = str_replace("\"","'",$row['lien_titre']);
			
		}
		break;
	case 'configuration':
		if (!EM_cfg_on("configuration_$nom_champ",'')) return $params;
		$titre = str_replace("\"","'",$GLOBALS['meta']["nom_site"]);
		$descriptif = $GLOBALS['meta']["descriptif_site"];
		break;
	case 'mots_type':
		if (!EM_cfg_on("groupesmots_$nom_champ",'')) return $params;
		if ($_GET['new'] == "oui") {
		  	$titre = filtrer_entites(_T('titre_nouveau_groupe'));
		  	$descriptif = $texte = "";
		} else {
			$id_groupe_tmp= intval($_GET['id_groupe']);
			$result_groupes = spip_query("SELECT * FROM spip_groupes_mots WHERE id_groupe=$id_groupe_tmp");

			while($row = $fetch($result_groupes)) {
				$titre = str_replace("\"","'",$row['titre']);
				$descriptif = $row['descriptif'];
				$texte = $row['texte'];
			}
		}
		break;
	case 'mots_edit':
		if (!EM_cfg_on("mots_$nom_champ",'')) return $params;
		$id_mot_tmp = intval($_GET['id_mot']);
		$row = $fetch(spip_query("SELECT * FROM spip_mots WHERE id_mot='$id_mot_tmp'"));
		 if ($row) {
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
	 	} else {
			$titre = filtrer_entites(_T('texte_nouveau_mot'));
			$descriptif = $texte = "";
	 	}
		break;
	case 'sites_edit':
		if (!EM_cfg_on("sites_$nom_champ",'')) return $params;
		$result = spip_query("SELECT * FROM spip_syndic WHERE id_syndic=" . intval($_GET['id_syndic']) );
		if ($row = $fetch($result)) {
			$titre = str_replace("\"","'",$row["nom_site"]);
			$descriptif = $row["descriptif"];
		} else
			$titre = $descriptif = "";
		break;
	default:
		return $params;
	}

	$onglets = '<div class="container-onglets"><ul class="tabs-nav">';
	for ($i=0; $i<count($langues_choisies); $i++)
		$onglets .= "<li><a href=\"#onglet-{$i}{$nom_champ}\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
	$onglets = "\n$onglets</ul>\n";

	switch($nom_champ) {
	case 'titre':
	case 'nom_site':
	case 'change_type':
		// on gere le numero dans un input separe
		$ret .= "<label>Num&eacute;ro : <input type='text' name=\"numero_zone_multilingue_{$nom_champ}\" value=\"".extension_multilingue_extraire_numero($titre)."\" size='5' /></label>\n$onglets";
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotitres'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<input type='text' class='formo' name=\"zone_multilingue_{$i}_{$nom_champ}\" value=\"".supprimer_numero(extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i]))."\" size='40'  /></div>";
		}
		$ret .= "</div>\n";
		break;
	case 'lien_nom':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "
			<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotitres'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<input type='text' class='formo' name=\"zone_multilingue_{$i}_{$nom_champ}\" value=\"".extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i])."\" size='40'  /></div>";
		}
		$ret .= "</div>";
		break;
	case 'descriptif':
	case 'descriptif_site':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++){	
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typodescriptifs'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_{$i}_{$nom_champ}\" class=\"forml\" rows=\"6\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
			}
		$ret.="</div>";
		break;
	case 'texte':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotextes'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_{$i}_{$nom_champ}\" class=\"forml\" rows=\"15\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($texte, $langues_choisies[$i]))."</textarea></div>";
		}
		$ret .="</div>";
		break;
	case 'surtitre':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotitres'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<input type='text' class='formo' name=\"zone_multilingue_{$i}_{$nom_champ}\" value=\"".extension_multilingue_extraire_multi_lang($surtitre, $langues_choisies[$i])."\" size='40'  /></div>";
		}
		$ret .= "</div>";
		break;
	case 'soustitre':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "
			<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotitres'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<input type='text' class='formo' name=\"zone_multilingue_{$i}_{$nom_champ}\" value=\"".extension_multilingue_extraire_multi_lang($soustitre, $langues_choisies[$i])."\" size='40'  /></div>";
		}
		$ret .= "</div>";
		break;
	case 'lien_titre':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typotitres'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<input type='text' class='formo' name=\"zone_multilingue_{$i}_{$nom_champ}\" value=\"".extension_multilingue_extraire_multi_lang($lien_titre, $langues_choisies[$i])."\" size='40'  /></div>";
		}
		$ret .= "</div>";
		break;
	case 'chapo':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {	
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typodescriptifs'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_{$i}_{$nom_champ}\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($chapo, $langues_choisies[$i]))."</textarea></div>";
		}
		$ret.="</div>";
		break;
	case 'ps':
		$ret .= $onglets;
		for ($i=0; $i<count($langues_choisies); $i++) {	
			$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-{$i}{$nom_champ}\">";
			if (EM_cfg_on('typodescriptifs'))
				$ret .= afficher_barre("document.getElementsByName('zone_multilingue_{$i}_{$nom_champ}')[0]", false, $langues_choisies[$i]);
			$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_{$i}_{$nom_champ}\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($ps, $langues_choisies[$i]))."</textarea></div>";
		}
		$ret.="</div>";
		break;
	default:
	}
	$params['flux'] .= $ret;
	return $params;
}


function ExtensionMultilingue_header_prive($texte) {
	if (!in_array($_GET['exec'], array('sites_edit','articles_edit','breves_edit','mots_edit','mots_type','configuration','rubriques_edit'))) return $texte;
	$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	$newtab = "<link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs.css')."\" type=\"text/css\" media=\"print, projection, screen\">
<!-- Additional IE/Win specific style sheet (Conditional Comments) -->
<!--[if lte IE 7]><link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs-ie.css')."\" type=\"text/css\" media=\"projection, screen\"><![endif]-->
<script type=\"text/javascript\" src=\"".find_in_path('javascript/jquery.tabs.js')."\"></script>
<script type=\"text/javascript\"><!--
 $(document).ready(function() {\n";

	switch($_GET['exec']) {
	case 'rubriques_edit':
		// cas de l'edition des rubriques
		if (BTG_cfg_on('rubriques_titre') && EM_cfg_on('rubriques_titre',''))
			$newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."\t$('#barre_typo_rubrique_titre table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('rubriques_descriptif') && EM_cfg_on('rubriques_descriptif',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."\t$('#barre_typo_rubrique_descriptif table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('rubriques_texte') && EM_cfg_on('rubriques_texte',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."\t$('#barre_typo_rubrique_texte table.spip_barre').css(\"display\", \"none\");\n";	
		$newtab .= "\t$('.container-onglets').tabs();
		$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'articles_edit':
		// cas de l'edition des articles
		if (BTG_cfg_on('articles_surtitre') && EM_cfg_on('articles_surtitre','')) $newtab .= calculer_actions_head_multilingues("document.formulaire.surtitre", $langues_choisies, "input")."\t$('#barre_typo_article_surtitre table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('articles_titre') && EM_cfg_on('articles_titre','')) $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."\t$('#barre_typo_article_titre table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('articles_soustitre') && EM_cfg_on('articles_soustitre','')) $newtab .= calculer_actions_head_multilingues("document.formulaire.soustitre", $langues_choisies, "input")."\t$('#barre_typo_article_soustitre table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('articles_descriptif') && EM_cfg_on('articles_descriptif','')) $newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."\t$('#barre_typo_article_descriptif table.spip_barre').css(\"display\", \"none\");\n";

		if (EM_cfg_on('articles_texte','')) {	
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."\t$('table.spip_barre').css(\"display\", \"none\");\n";		
			// on annule le decoupage des textes trop longs fait par SPIP
			$newtab .= "\t$('textarea[@id=texte1]').css(\"display\", \"none\");$('textarea[@id=texte1]').val('');";
			$newtab .= "$('textarea[@id=texte2]').css(\"display\", \"none\");$('textarea[@id=texte2]').val('');";
			$newtab .= "$('textarea[@id=texte3]').css(\"display\", \"none\");$('textarea[@id=texte3]').val('');";
			$newtab .= "$('textarea[@id=texte4]').css(\"display\", \"none\");$('textarea[@id=texte4]').val('');";
			$newtab .= "$('textarea[@id=texte5]').css(\"display\", \"none\");$('textarea[@id=texte5]').val('');";
			$newtab .= "$('textarea[@id=texte6]').css(\"display\", \"none\");$('textarea[@id=texte6]').val('');";
			$newtab .= "$('textarea[@id=texte7]').css(\"display\", \"none\");$('textarea[@id=texte7]').val('');";
			$newtab .= "$('textarea[@id=texte8]').css(\"display\", \"none\");$('textarea[@id=texte8]').val('');";
			$newtab .= "$('textarea[@id=texte9]').css(\"display\", \"none\");$('textarea[@id=texte9]').val('');";
		}
		if (BTG_cfg_on('articles_chapo') && EM_cfg_on('articles_chapo',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.chapo", $langues_choisies, "textarea")."\t$('#barre_typo_article_chapo table.spip_barre').css(\"display\", \"none\");\n";	
		if (BTG_cfg_on('articles_ps') && EM_cfg_on('articles_ps',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.ps", $langues_choisies, "textarea")."\t$('#barre_typo_article_ps table.spip_barre').css(\"display\", \"none\");\n";	
		$newtab .= "\t$('.container-onglets').tabs();\n\t$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'breves_edit':
		// cas de l'edition des breves
		if (BTG_cfg_on('breves_titre') && EM_cfg_on('breves_titre','')) $newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."\t$('#barre_typo_breve_titre table.spip_barre').css(\"display\", \"none\");\n";
		if (EM_cfg_on('breves_texte',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."\t$('table.spip_barre').css(\"display\", \"none\");\n";	
		if (BTG_cfg_on('breves_lien') && EM_cfg_on('breves_lien_titre','')) $newtab .= calculer_actions_head_multilingues("document.formulaire.lien_titre", $langues_choisies, "input")."\t$('#barre_typo_breve_lien_titre table.spip_barre').css(\"display\", \"none\");\n";
		$newtab .= "\t$('.container-onglets').tabs();
		$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'configuration':
		// cas de l'edition de la configuration
		if (BTG_cfg_on('configuration_nom') && EM_cfg_on('configuration_nom_site',''))
			$newtab .= calculer_actions_head_multilingues_titre("document.formulaire.nom_site", $langues_choisies, "input")."\t$('#barre_typo_configuration_nom_site table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('configuration_description') && EM_cfg_on('configuration_descriptif_site',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif_site", $langues_choisies, "textarea")."\t$('#barre_typo_configuration_descriptif_site table.spip_barre').css(\"display\", \"none\");\n";
		$newtab .= "\t$('.container-onglets').tabs();\n\t$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'mots_type':
		// cas de l'edition des groupes de mots clefs
		if (BTG_cfg_on('groupesmots_nom') && EM_cfg_on('groupesmots_change_type',''))
			$newtab .= calculer_actions_head_multilingues_titre("document.formulaire.change_type", $langues_choisies, "input")."\t$('#barre_typo_groupemot_nom table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('groupesmots_descriptif') && EM_cfg_on('groupesmots_descriptif',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."\t$('#barre_typo_groupemot_descriptif table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('groupesmots_texte') && EM_cfg_on('groupesmots_texte',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."\t$('#barre_typo_groupemot_texte table.spip_barre').css(\"display\", \"none\");\n";
		$newtab .= "\t$('.container-onglets').tabs();\n\t$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'mots_edit':
		// cas de l'edition des mots clefs
		if (BTG_cfg_on('mots_nom') && EM_cfg_on('mots_titre',''))
			$newtab .= calculer_actions_head_multilingues_titre("document.formulaire.titre", $langues_choisies, "input")."\t$('#barre_typo_mot_nom table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('mots_descriptif') && EM_cfg_on('mots_descriptif',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."\t$('#barre_typo_mot_descriptif table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('mots_texte') && EM_cfg_on('mots_texte',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea")."\t$('#barre_typo_mot_texte table.spip_barre').css(\"display\", \"none\");\n";
		$newtab .= "\t$('.container-onglets').tabs();\n\t$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	case 'sites_edit':
		// cas de l'edition des sites references
		if (BTG_cfg_on('sites_nom') && EM_cfg_on('sites_nom_site',''))
			$newtab .= calculer_actions_head_multilingues_titre("document.formulaire.nom_site", $langues_choisies, "input")."\t$('#barre_typo_site_nom table.spip_barre').css(\"display\", \"none\");\n";
		if (BTG_cfg_on('sites_description') && EM_cfg_on('sites_descriptif',''))
			$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea")."\t$('#barre_typo_site_descriptif table.spip_barre').css(\"display\", \"none\");\n";
		$newtab .= "\t$('.container-onglets').tabs();\n\t$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		break;
	default:		
	}

	if (strlen($newtab))
		return "{$texte}{$newtab}\n//--></script>\n";
	// inclure librairie pour l'affichage des onglets
	return $texte;
}


//http://doc.spip.org/@multi_trad
function extension_multilingue_multi_trad_lang ($trads, $langue_souhaitee) {

	if (isset($trads[$langue_souhaitee]))
		return $trads[$langue_souhaitee];
	// cas des langues xx_yy
	if (ereg('^([a-z]+)_', $spip_lang, $regs) AND isset($trads[$regs[1]]))
		return $trads[$regs[1]];

	// sinon, renvoyer la premiere du tableau
	// remarque : on pourrait aussi appeler un service de traduction externe
	// ou permettre de choisir une langue "plus proche",
	// par exemple le francais pour l'espagnol, l'anglais pour l'allemand, etc.
	/*return array_shift($trads);*/ return "";
}

//analyse un bloc multi
//http://doc.spip.org/@extraire_trad
function extension_multilingue_extraire_trad_lang ($bloc, $langue_souhaitee) {
	$lang = '';
//ce reg fait planter l'analyse multi s'il y a de l'{italique} dans le champ
//while (preg_match("/^(.*?)[{\[]([a-z_]+)[}\]]/siS", $bloc, $regs)) {
	while (preg_match("/^(.*?)[\[]([a-z_]+)[\]]/siS", $bloc, $regs)) {
		$texte = trim($regs[1]);
		if ($texte OR $lang)
			$trads[$lang] = $texte;
		$bloc = substr($bloc, strlen($regs[0]));
		$lang = $regs[2];
	}
	$trads[$lang] = $bloc;

	// faire la traduction avec ces donnees
	return extension_multilingue_multi_trad_lang($trads, $langue_souhaitee);
}

//repere les blocs multi dans un texte et extrait le bon
//http://doc.spip.org/@extraire_multi
function extension_multilingue_extraire_multi_lang ($letexte, $langue_souhaitee) {
	if (strpos($letexte, '<multi>') === false) return $letexte; //perf
	if (preg_match_all("@<multi>(.*?)</multi>@sS", $letexte, $regs, PREG_SET_ORDER))
		foreach ($regs as $reg)
			$letexte = str_replace($reg[0], extension_multilingue_extraire_trad_lang($reg[1], $langue_souhaitee), $letexte);
	return $letexte;
}

function calculer_actions_head_multilingues($champ, $langues_choisies, $typedechamp) {
	$nom_champ = str_replace(".", "_", $champ);
	$champ_fin = substr($champ, strrpos($champ, ".") + 1);

	$resultat = "	$('{$typedechamp}[@name={$champ_fin}]').css(\"display\", \"none\");
	$(\"{$typedechamp}[@name={$champ_fin}]\").parents().filter(\"form\").bind(\"submit\", function(e) { 
		var valeur{$champ_fin}='';\n";
	
	for ($i=0; $i<count($langues_choisies); $i++)
		$resultat .= "\t\tif ($('{$typedechamp}[@name=zone_multilingue_{$i}_{$champ_fin}]').val() != '') valeur{$champ_fin}+='["
			.$langues_choisies[$i]."]'+$('{$typedechamp}[@name=zone_multilingue_{$i}_{$champ_fin}]').val();\n";
	$resultat .= "\t\tif (valeur{$champ_fin} != '') $('{$typedechamp}[@name={$champ_fin}]').val('<multi>'+valeur{$champ_fin}+'</multi>');
			else $('{$typedechamp}[@name={$champ_fin}]').val('');\n\t});\n";
	return $resultat;
}

function calculer_actions_head_multilingues_titre ($champ, $langues_choisies, $typedechamp) {
	$nom_champ = str_replace(".", "_", $champ);
	$champ_fin = substr($champ, strrpos($champ, ".") + 1);

	$resultat = "\t$('{$typedechamp}[@name={$champ_fin}]').css(\"display\", \"none\");\n"
		. "\t$(\"{$typedechamp}[@name={$champ_fin}]\").parents().filter(\"form\").bind(\"submit\", function(e) {\n\t\tvar valeur{$champ_fin}='';\n";
		
	for ($i=0; $i<count($langues_choisies); $i++)
			$resultat .= "\t\tif ($('{$typedechamp}[@name=zone_multilingue_{$i}_{$champ_fin}]').val() != '') valeur{$champ_fin}+='["
				.$langues_choisies[$i]."]'+$('{$typedechamp}[@name=zone_multilingue_{$i}_{$champ_fin}]').val();\n";
	$resultat .= "\t\tvar numero = $('{$typedechamp}[@name=numero_zone_multilingue_{$champ_fin}]').val();
		if (valeur{$champ_fin} != '') {
			if (numero != '') $('{$typedechamp}[@name={$champ_fin}]').val(numero+'. <multi>'+valeur{$champ_fin}+'</multi>'); 
			else $('{$typedechamp}[@name={$champ_fin}]').val('<multi>'+valeur{$champ_fin}+'</multi>'); 
		} else	$('{$typedechamp}[@name={$champ_fin}]').val('');
	});\n";
		return $resultat;
}

function extension_multilingue_extraire_numero($titre) {
	if (ereg("([0-9]+)\.", $titre, $match)) return $match[1];
	return '';
}
?>
