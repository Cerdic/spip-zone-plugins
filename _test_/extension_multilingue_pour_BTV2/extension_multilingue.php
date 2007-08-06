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
		

		if ($new == "oui") {
			$titre = filtrer_entites(_T('titre_nouvelle_rubrique'));
			$descriptif = "";
			$texte = "";
			
		} else {
			
			$id_rubrique_tmp = intval($_GET['id_rubrique']);
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique_tmp'"));
	
			if (!$row) return "";
	
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$texte = $row['texte'];
			
			
		}
		

		
	}
	else if ($_GET['exec'] == "configuration")
	{
		$titre = $GLOBALS['meta']["nom_site"];
		$descriptif = $GLOBALS['meta']["descriptif_site"];

	}
	else if ($_GET['exec'] == "mots_type")
	{
		
		if ($new == "oui") {
		  	$titre = filtrer_entites(_T('titre_nouveau_groupe'));
		  	$descriptif = "";
			$texte = "";
		  
		} else {
			$id_groupe_tmp= intval($_GET['id_groupe']);
			$result_groupes = spip_query("SELECT * FROM spip_groupes_mots WHERE id_groupe=$id_groupe_tmp");

			while($row = spip_fetch_array($result_groupes)) {
				$titre = $row['titre'];
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
			$titre = $row['titre'];
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
			$titre = $row["nom_site"];
			$descriptif = $row["descriptif"];
			
		}
		else
		{
			$titre = "";
			$descriptif = "";
		}
	}

	
	if (($_GET['exec'] == "sites_edit") || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
	{

		if (($champ_fin == "titre") || ($champ_fin == "nom_site") || ($champ_fin == "change_type") || ($champ_fin == "lien_nom"))
		{
			//cas des input
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
				$ret .= "<input type='text' class='formo' name=\"zone_multilingue_".$i."_".$nom_champ."\" value=\"".extraire_multi_lang($titre, $langues_choisies[$i])."\" size='40'  /></div>";
			}
        		
			$ret .= "</div>";
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$('input[@name=".$champ_fin."]').css(\"display\", \"none\");";
			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				$('input[@name=".$champ_fin."]').val('<multi>'+";
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "'[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_".$nom_champ."]').val()+";
			}
			$ret .= "'</multi>');});";
			
			$ret .=	"});</script>";
				
		}
		else if (($champ_fin == "descriptif") || ($champ_fin == "descriptif_site"))
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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);

				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"6\" cols=\"40\">".entites_html(extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$(\"textarea[@name=".$champ_fin."]\").css(\"display\", \"none\");";

			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				$(\"textarea[@name=".$champ_fin."]\").val('<multi>'+";
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "'[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val()+";
			}
			$ret .= "'</multi>');});";

			$ret .=	"});</script>";
		}
	
		else if ($champ_fin == "texte")
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
					$ret .= afficher_barre($champ_parent.".zone_multilingue_".$i."_".$nom_champ, false, $langues_choisies[$i]);
				}
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"15\" cols=\"40\">".entites_html(extraire_multi_lang($texte, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret .="</div>";
			$ret .= "<script type=\"text/javascript\">
				$(document).ready(function() {
					$(\"textarea[@name=".$champ_fin."]\").css(\"display\", \"none\");";

			$ret .= "$('form[textarea]').bind(\"submit\", function(e) { 
				$(\"textarea[@name=".$champ_fin."]\").val('<multi>'+";
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$ret .= "'[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_".$nom_champ."]').val()+";
			}
			$ret .= "'</multi>');});";

			$ret .=	"});</script>";
		}
    	
	}		
	

	return $ret;


	}	

	
	
}



function ExtensionMultilingue_header_prive($texte) {


$newtab="";
	if (($_GET['exec'] == "sites_edit") || ($_GET['exec'] == "mots_edit") || ($_GET['exec'] == "mots_type") || ($_GET['exec'] == "configuration") || ($_GET['exec'] == "rubriques_edit"))	
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
function multi_trad_lang ($trads, $langue_souhaitee) {
	 

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
	else  return array_shift($trads);
}

// analyse un bloc multi
// http://doc.spip.org/@extraire_trad
function extraire_trad_lang ($bloc, $langue_souhaitee) {
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
	return multi_trad_lang($trads, $langue_souhaitee);
}

// repere les blocs multi dans un texte et extrait le bon
// http://doc.spip.org/@extraire_multi
function extraire_multi_lang ($letexte, $langue_souhaitee) {
	if (strpos($letexte, '<multi>') === false) return $letexte; // perf
	if (preg_match_all("@<multi>(.*?)</multi>@sS", $letexte, $regs, PREG_SET_ORDER))
		foreach ($regs as $reg)
			$letexte = str_replace($reg[0], extraire_trad_lang($reg[1], $langue_souhaitee), $letexte);
	return $letexte;
}


?>
