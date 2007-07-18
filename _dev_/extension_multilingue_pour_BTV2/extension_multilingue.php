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
		$id_rubrique = intval($_GET['id_rubrique']);
		$row = spip_fetch_array(spip_query("SELECT * FROM spip_rubriques WHERE id_rubrique='$id_rubrique'"));
		if (!$row) exit;

		$titre = $row['titre'];
		
		$descriptif = $row['descriptif'];
		$texte = $row['texte'];
		
	
		if ($champ_fin == "titre")
		{
			//cas des input
			$ret="
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
		}
		else if ($champ_fin == "descriptif")
		{
			$ret="<div class=\"container-onglets\">
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
				$ret .= "<textarea style=\"width: 480px;\" name=\"zone_multilingue_".$i."_".$nom_champ."\" class=\"forml\" rows=\"4\" cols=\"40\">".entites_html(extraire_multi_lang($descriptif, $langues_choisies[$i]))."</textarea></div>";
        		}
			$ret.="</div>";
		}
		else if ($champ_fin == "texte")
		{
			$ret="<div class=\"container-onglets\">
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
			$ret.="	</div>";
		}
    	
		
	}

	return $ret;


	}	

	
	
}



function ExtensionMultilingue_header_prive($texte) {

	//inclure librairie pour l'affichage des onglets
	
	return $texte."<link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs.css')."\" type=\"text/css\" media=\"print, projection, screen\"><!-- Additional IE/Win specific style sheet (Conditional Comments) --><!--[if lte IE 7]>
        <link rel=\"stylesheet\" href=\"".find_in_path('css/jquery.tabs-ie.css')."\" type=\"text/css\" media=\"projection, screen\">
        <![endif]-->
           
        	<script type=\"text/javascript\" src=\"".find_in_path('javascript/jquery.tabs.js')."\"></script>
		";
}
function ExtensionMultilingue_body_prive($texte) {
$newtab="";
if ($_GET['exec'] == "rubriques_edit")
{	
	$langues_choisies = explode(",",lire_config('ExtensionMultilingue/langues_ExtensionMultilingue','fr,en,de'));	
	$newtab = "        
	<script type=\"text/javascript\">
	$(document).ready(function() {
	     	$('.container-onglets').tabs();
		$('table.spip_barre').css(\"display\", \"none\");
		$('input[@name=titre]').css(\"display\", \"none\");
		$('textarea[@name=descriptif]').css(\"display\", \"none\");
		$('textarea[@name=texte]').css(\"display\", \"none\");
		$('.container-onglets').find('table.spip_barre').css(\"display\", \"block\");
		$('form[textarea]').bind(\"submit\", function(e) { 
			$('input[@name=titre]').val('<multi>'+";
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$newtab .= "'[".$langues_choisies[$i]."]'+$('input[@name=zone_multilingue_".$i."_document_formulaire_rubrique_titre]').val()+";
			}
			$newtab .= "'</multi>');$('textarea[@name=descriptif]').val('<multi>'+";
			
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$newtab .= "'[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_document_formulaire_rubrique_descriptif]').val()+";
			}
			$newtab .= "'</multi>');$('textarea[@name=texte]').val('<multi>'+";
			for ($i=0; $i<count($langues_choisies); $i++)
			{
				$newtab .= "'[".$langues_choisies[$i]."]'+$('textarea[@name=zone_multilingue_".$i."_document_formulaire_rubrique_texte]').val()+";
			}
			$newtab .= "'</multi>');
		} );
	});
	</script>
        ";
}
return $newtab.$texte;
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
