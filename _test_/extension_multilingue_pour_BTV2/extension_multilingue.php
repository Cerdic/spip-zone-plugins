<?php

function ExtensionMultilingue_BarreTypoEnrichie_toolbox($paramArray) {


if (strpos($paramArray[0], "zone_multilingue") === FALSE)
{
	$ret="";
	$nom_champ = str_replace(".", "_", $paramArray[0]);
	$champ_parent = substr($paramArray[0], 0, strrpos($paramArray[0], "."));
	$champ_fin = substr($paramArray[0], strrpos($paramArray[0], ".") + 1);
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
		

		if (($champ_fin == "titre") || ($champ_fin == "nom_site") || ($champ_fin == "change_type") || ($champ_fin == "lien_nom"))
		{
			//cas des input
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('input[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('input[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('input[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";
	
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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
				
		}
		else if (($champ_fin == "descriptif") || ($champ_fin == "descriptif_site"))
		{
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('textarea[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('textarea[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('textarea[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);

				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"6\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
	
		else if ($champ_fin == "texte")
		{
			
			
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('textarea[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('textarea[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('textarea[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"15\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($texte, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret .="</div>";
			
		}
		else if ($champ_fin == "surtitre")
		{
			//cas des input
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('input[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('input[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('input[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($surtitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($champ_fin == "soustitre")
		{
			//cas des input
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('input[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('input[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('input[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($soustitre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($champ_fin == "lien_titre")
		{
			//cas des input
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('input[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('input[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('input[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";
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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extension_multilingue_extraire_multi_lang($lien_titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			
		}
		else if ($champ_fin == "chapo")
		{
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('textarea[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('textarea[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('textarea[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);

				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"5\" cols=\"40\">".entites_html(extension_multilingue_extraire_multi_lang($chapo, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			
		}
		else if ($champ_fin == "ps")
		{
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('textarea[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				var valeur".$champ_fin."='';
			";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "if ($('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val() != '') valeur".$champ_fin."+='[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val();
				";
			}
			
			$ret .= "if (valeur".$champ_fin." != '') $('textarea[@name=".$champ_fin."]').val('<multi>'+valeur".$champ_fin."+'</multi>'); else $('textarea[@name=".$champ_fin."]').val('');});";
			
			$ret .=	"});</script>";

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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);

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


$newtab="";
	if (($_GET['exec'] == "sites_edit") || (($_GET['exec'] == "articles_edit") && (lire_config('ExtensionMultilingue/multiarticles_ExtensionMultilingue') == "on")) || (($_GET['exec'] == "breves_edit") && (lire_config('ExtensionMultilingue/multibreves_ExtensionMultilingue') == "on")) || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{

		$newtab .= " <link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs.css')."\" type=\"text/css\" media=\"print, projection, screen\"><!-- Additional IE/Win specific style sheet (Conditional Comments) --><!--[if lte IE 7]>
        		<link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs-ie.css')."\" type=\"text/css\" media=\"projection, screen\">
        		<![endif]-->
           
        	<script type=\"text/javascript\" src=\"".find_in_path('javascript/jquery.tabs.js')."\"></script>
		       
		<script type=\"text/javascript\">
		$(document).ready(function() {";
		if (($_GET['exec'] == "rubriques_edit") && (lire_config('typo_partout/rubriques_descriptif_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "rubriques_edit") && (lire_config('typo_partout/rubriques_texte_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=texte]').css(\"display\", \"none\");";	
		}
		if (($_GET['exec'] == "articles_edit"))
		{
			$newtab .= "$('textarea[@name=texte]').css(\"display\", \"none\");";	
		}
		if (($_GET['exec'] == "articles_edit") && (lire_config('typo_partout/articles_descriptif_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif]').css(\"display\", \"none\");";	
		}
		if ($_GET['exec'] == "articles_edit")
		{
			$newtab .= "$('textarea[@id=texte1]').css(\"display\", \"none\");$('textarea[@id=texte1]').val('');";
			$newtab .= "$('textarea[@id=texte2]').css(\"display\", \"none\");$('textarea[@id=texte2]').val('');";
			$newtab .= "$('textarea[@id=texte3]').css(\"display\", \"none\");$('textarea[@id=texte3]').val('');";
			$newtab .= "$('textarea[@id=texte4]').css(\"display\", \"none\");$('textarea[@id=texte4]').css(\"display\", \"none\");$('textarea[@id=texte4]').val('');";
			$newtab .= "$('textarea[@id=texte5]').css(\"display\", \"none\");$('textarea[@id=texte5]').val('');";
			$newtab .= "$('textarea[@id=texte6]').css(\"display\", \"none\");$('textarea[@id=texte6]').val('');";
			$newtab .= "$('textarea[@id=texte7]').css(\"display\", \"none\");$('textarea[@id=texte7]').val('');";
			$newtab .= "$('textarea[@id=texte8]').css(\"display\", \"none\");$('textarea[@id=texte8]').val('');";
			$newtab .= "$('textarea[@id=texte9]').css(\"display\", \"none\");$('textarea[@id=texte9]').val('');";
		}
		if (($_GET['exec'] == "articles_edit") && (lire_config('typo_partout/articles_chapo_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=chapo]').css(\"display\", \"none\");";	
		}
		if (($_GET['exec'] == "articles_edit") && (lire_config('typo_partout/articles_ps_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=ps]').css(\"display\", \"none\");";	
		}
		if (($_GET['exec'] == "breves_edit"))
		{
			$newtab .= "$('textarea[@name=texte]').css(\"display\", \"none\");";	
		}
		if (($_GET['exec'] == "configuration") && (lire_config('typo_partout/configuration_description_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif_site]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "mots_type") && (lire_config('typo_partout/groupesmots_descriptif_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "mots_type") && (lire_config('typo_partout/groupesmots_texte_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=texte]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "mots_edit") && (lire_config('typo_partout/mots_descriptif_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "mots_edit") && (lire_config('typo_partout/mots_texte_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=texte]').css(\"display\", \"none\");	";
		}
		if (($_GET['exec'] == "sites_edit") && (lire_config('typo_partout/sites_description_typo_partout') == "on"))
		{
			$newtab .= "$('textarea[@name=descriptif]').css(\"display\", \"none\");	";
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


?>
