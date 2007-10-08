<?php
function ExtensionMultilingue_BarreTypoEnrichie_toolbox($paramArray) {
if (strpos($paramArray[0], "zone_multilingue") === FALSE)
{
	$ret="";
	$nom_champ = substr($paramArray[0], strpos($paramArray[0], "'")+1, strlen(substr($paramArray[0], strpos($paramArray[0], "'")+1))-5 );
	$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	
	if ($_GET['exec'] == "rubriques_edit")
	{
		if ($_GET['new'] == "oui") 
		{
			$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
			$descriptif = "";
			$texte = "";
		} 
		else 
		{
			$id_rubrique_tmp = intval($_GET['id_rubrique']);
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique_tmp'"));
	
			if (!$row) return "";
	
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
		}
	}
	else if (($_GET['exec'] == "articles_edit") && (lire_config('ExtensionMultilingue/multiarticles_ExtensionMultilingue') == "on"))
	{
		if ($_GET['new'] == "oui") 
		{
			$surtitre = "";
			$titre = filtrer_entites(_T('info_nouvel_article'));
			$soustitre = "";
			$descriptif = "";
			$chapo = "";
			$texte = "";
			$ps = "";
		} 
		else
		{
			$id_article_tmp = intval($_GET['id_article']);
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article='$id_article_tmp'"));
	
			if (!$row) return "";
	
			$surtitre = str_replace("\"","'",$row['surtitre']);
			$titre = str_replace("\"","'",$row['titre']);
			$soustitre = str_replace("\"","'",$row['soustitre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
			$chapo = $row['chapo'];
			$ps = $row['ps'];
		}
		
	}
	else if (($_GET['exec'] == "breves_edit") && (lire_config('ExtensionMultilingue/multibreves_ExtensionMultilingue') == "on"))
	{
		if ($_GET['new'] == "oui") 
		{
			$titre = filtrer_entites(_T('titre_nouvelle_breve'));
			$texte = "";
			$lien_titre = "";
		} 
		else 
		{
			$id_breve_tmp = intval($_GET['id_breve']);
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_breves WHERE id_breve='$id_breve_tmp'"));
	
			if (!$row) return "";
	
			$titre = str_replace("\"","'",$row['titre']);
			$texte = $row['texte'];
			$lien_titre = str_replace("\"","'",$row['lien_titre']);
			
		}
	}
	else if ($_GET['exec'] == "configuration")
	{
		$titre = str_replace("\"","'",$GLOBALS['meta']["nom_site"]);
		$descriptif = $GLOBALS['meta']["descriptif_site"];

	}
	else if ($_GET['exec'] == "mots_type")
	{
		
		if ($_GET['new'] == "oui") {
		  	$titre = filtrer_entites(_T('titre_nouveau_groupe'));
		  	$descriptif = "";
			$texte = "";
		  
		} else {
			$id_groupe_tmp= intval($_GET['id_groupe']);
			$result_groupes = spip_query("SELECT * FROM spip_groupes_mots WHERE id_groupe=$id_groupe_tmp");

			while($row = spip_fetch_array($result_groupes)) {
				$titre = str_replace("\"","'",$row['titre']);
				$descriptif = $row['descriptif'];
				$texte = $row['texte'];
				
			}
		}

	}
	else if ($_GET['exec'] == "mots_edit")
	{
		

		$id_mot_tmp = intval($_GET['id_mot']);
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_mots WHERE id_mot='$id_mot_tmp'"));
		 if ($row) {
			$titre = str_replace("\"","'",$row['titre']);
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
			
	 	}
		else {
			
			$titre = filtrer_entites(_T('texte_nouveau_mot'));
			$descriptif = "";
			$texte = "";
			
			
	 	}

	}

	else if ($_GET['exec'] == "sites_edit")
	{
		
		$result = spip_query("SELECT * FROM spip_syndic WHERE id_syndic=" . intval($_GET['id_syndic']) );

		if ($row = spip_fetch_array($result)) {
			$titre = str_replace("\"","'",$row["nom_site"]);
			$descriptif = $row["descriptif"];
			
		}
		else
		{
			$titre = "";
			$descriptif = "";
		}
	}
	
	if (($_GET['exec'] == "sites_edit") || (($_GET['exec'] == "articles_edit")  && (lire_config('ExtensionMultilingue/multiarticles_ExtensionMultilingue') == "on")) || (($_GET['exec'] == "breves_edit")  && (lire_config('ExtensionMultilingue/multibreves_ExtensionMultilingue') == "on")) || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{
		

		if (($nom_champ == "titre") || ($nom_champ == "nom_site") || ($nom_champ == "change_type") || ($nom_champ == "lien_nom"))
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
				
		}
		else if (($nom_champ == "descriptif") || ($nom_champ == "descriptif_site"))
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"6\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
	
		else if ($nom_champ == "texte")
		{
			
			
			$ret .= "<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{	
        	        	$ret.="	<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
			}
        	        $ret.="	</ul>";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotextes_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"15\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($texte, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret .="</div>";
			
		}
		else if ($nom_champ == "surtitre")
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($surtitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "soustitre")
		{
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($soustitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "lien_titre")
		{
			
			$ret .= "
			<div class=\"container-onglets\">
        		<ul class=\"tabs-nav\">";
        		for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "        <li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        		}
			$ret .= "</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "
				<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typotitres_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($lien_titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($nom_champ == "chapo")
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($chapo, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
		else if ($nom_champ == "ps")
		{
			$ret .= "<div class=\"container-onglets\">
    			<ul class=\"tabs-nav\">";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
        	        		$ret.="<li class=\"\"><a href=\"#onglet-".$i.$nom_champ."\"><span>".traduire_nom_langue($langues_choisies[$i])."</span></a></li>";
        	        }
        		$ret.="	</ul>";

			for ($i=0; $i<count($langues_choisies); $i++)
			{	
				$ret .= "<div style=\"\" class=\"tabs-container\" id=\"onglet-".$i.$nom_champ."\">";
				if (lire_config('ExtensionMultilingue/typodescriptifs_ExtensionMultilingue') == "on")
				{			
					$ret .= afficher_barre("document.getElementsByName('zone_multilingue_".$i."_".$nom_champ."')[0]", false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($ps, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
    		
	}		
	return $ret;
	}	
}

function ExtensionMultilingue_header_prive($texte) {

$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	
$newtab="";
	if (($_GET['exec'] == "sites_edit") || (($_GET['exec'] == "articles_edit") && (lire_config('ExtensionMultilingue/multiarticles_ExtensionMultilingue') == "on")) || (($_GET['exec'] == "breves_edit") && (lire_config('ExtensionMultilingue/multibreves_ExtensionMultilingue') == "on")) || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{

		$newtab .= " <link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs.css')."\" type=\"text/css\" media=\"print, projection, screen\"><!-- Additional IE/Win specific style sheet (Conditional Comments) --><!--[if lte IE 7]>
        		<link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs-ie.css')."\" type=\"text/css\" media=\"projection, screen\">
        		<![endif]-->
           
        	<script type=\"text/javascript\" src=\"".find_in_path('javascript/jquery.tabs.js')."\"></script>
		       
		<script type=\"text/javascript\">
		$(document).ready(function() {";
		
		//cas de l'édition des rubriques
		if ($_GET['exec'] == "rubriques_edit")
		{
			if (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.titre", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea");
			}
			if (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea");	
			}
		}
		//cas de l'édition des articles
		else if ($_GET['exec'] == "articles_edit")
		{
			if (lire_config('barre_typo_generalisee/articles_surtitre_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.surtitre", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/articles_titre_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.titre", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/articles_soustitre_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.soustitre", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/articles_descriptif_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea");
			}
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea");		
				
			//on annule le découpage des textes trop longs fait par SPIP
			$newtab .= "$('textarea[@id=texte1]').css(\"display\", \"none\");$('textarea[@id=texte1]').val('');";
			$newtab .= "$('textarea[@id=texte2]').css(\"display\", \"none\");$('textarea[@id=texte2]').val('');";
			$newtab .= "$('textarea[@id=texte3]').css(\"display\", \"none\");$('textarea[@id=texte3]').val('');";
			$newtab .= "$('textarea[@id=texte4]').css(\"display\", \"none\");$('textarea[@id=texte4]').val('');";
			$newtab .= "$('textarea[@id=texte5]').css(\"display\", \"none\");$('textarea[@id=texte5]').val('');";
			$newtab .= "$('textarea[@id=texte6]').css(\"display\", \"none\");$('textarea[@id=texte6]').val('');";
			$newtab .= "$('textarea[@id=texte7]').css(\"display\", \"none\");$('textarea[@id=texte7]').val('');";
			$newtab .= "$('textarea[@id=texte8]').css(\"display\", \"none\");$('textarea[@id=texte8]').val('');";
			$newtab .= "$('textarea[@id=texte9]').css(\"display\", \"none\");$('textarea[@id=texte9]').val('');";
			
			if (lire_config('barre_typo_generalisee/articles_chapo_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.chapo", $langues_choisies, "textarea");	
			}
			if (lire_config('barre_typo_generalisee/articles_ps_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.ps", $langues_choisies, "textarea");	
			}
		}	
		//cas de l'édition des brèves
		else if ($_GET['exec'] == "breves_edit")
		{
			if (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.titre", $langues_choisies, "input");
			}
			$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea");	
			
			if (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.lien_titre", $langues_choisies, "input");
			}
		}
		//cas de lédition de la configuration
		else if ($_GET['exec'] == "configuration")
		{
			if (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.nom_site", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif_site", $langues_choisies, "textarea");
			}
		}
		//cas de l'édition des groupes de mots clefs
		else if ($_GET['exec'] == "mots_type") 
		{
			if (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.change_type", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea");
			}
			if (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea");
			}
		}
		//cas de l'édition des mots clefs
		else if ($_GET['exec'] == "mots_edit")
		{
			if (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.titre", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea");
			}
			if (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.texte", $langues_choisies, "textarea");
			}
		}
		//cas de l'édition des sites référencés
		else if ($_GET['exec'] == "sites_edit") 
		{
			if (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.nom_site", $langues_choisies, "input");
			}
			if (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on")
			{
				$newtab .= calculer_actions_head_multilingues("document.formulaire.descriptif", $langues_choisies, "textarea");
			}
		}
		$newtab .= "$('.container-onglets').tabs();
			$('table.spip_barre').css(\"display\", \"none\");
			$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");});";
		
		
		$newtab .= "</script>";
	}

	
	//inclure librairie pour l'affichage des onglets
	return $texte.$newtab;
}


// http://doc.spip.org/@multi_trad
function extension_multilingue_multi_trad_lang ($trads, $langue_souhaitee) {
	 

	if (isset($trads[$langue_souhaitee])) {
		return $trads[$langue_souhaitee];

	}	// cas des langues xx_yy
	else if (ereg('^([a-z]+)_', $spip_lang, $regs) AND isset($trads[$regs[1]])) {
		return $trads[$regs[1]];
	}	
	// sinon, renvoyer la premiere du tableau
	// remarque : on pourrait aussi appeler un service de traduction externe
	// ou permettre de choisir une langue "plus proche",
	// par exemple le francais pour l'espagnol, l'anglais pour l'allemand, etc.
	else  /*return array_shift($trads);*/ return "";
}

// analyse un bloc multi
// http://doc.spip.org/@extraire_trad
function extension_multilingue_extraire_trad_lang ($bloc, $langue_souhaitee) {
	$lang = '';
// ce reg fait planter l'analyse multi s'il y a de l'{italique} dans le champ
//	while (preg_match("/^(.*?)[{\[]([a-z_]+)[}\]]/siS", $bloc, $regs)) {
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

// repere les blocs multi dans un texte et extrait le bon
// http://doc.spip.org/@extraire_multi
function extension_multilingue_extraire_multi_lang ($letexte, $langue_souhaitee) {
	if (strpos($letexte, '<multi>') === false) return $letexte; // perf
	if (preg_match_all("@<multi>(.*?)</multi>@sS", $letexte, $regs, PREG_SET_ORDER))
		foreach ($regs as $reg)
			$letexte = str_replace($reg[0], extension_multilingue_extraire_trad_lang($reg[1], $langue_souhaitee), $letexte);
	return $letexte;
}

function calculer_actions_head_multilingues ($champ, $langues_choisies, $typedechamp)
{
	$nom_champ = str_replace(".", "_", $champ);
	$champ_fin = substr($champ, strrpos($champ, ".") + 1);

			$resultat .= "$('".$typedechamp."[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$resultat .= "$(\"".$typedechamp."[@name=".$champ_fin."]\").parents().filter(\"form\").bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$resultat .= "if ($('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('".$typedechamp."[@name=zone_multilingue_".$i."_".$champ_fin."]').val();
				";
			}
			
			$resultat .= "if (valeur".$champ_fin." != '') $('".$typedechamp."[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('".$typedechamp."[@name=".$champ_fin."]').val('');});";
			return $resultat;
}
?>
