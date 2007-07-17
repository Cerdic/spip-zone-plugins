<?php

function ExtensionMultilingue_BarreTypoEnrichie_toolbox($paramArray) {

if (strpos($paramArray[0], "zone_multilingue") === FALSE)
{
	$ret="";
	$nom_champ = str_replace(".", "_", $paramArray[0]);
	$champ_parent = substr($paramArray[0], 0, strrpos($paramArray[0], "."));
	$champ_fin = substr($paramArray[0], strrpos($paramArray[0], ".") + 1);
	
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
        		<ul class=\"tabs-nav\">
        		        <li class=\"tabs-selected\"><a href=\"#onglet-1".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr'))."</span></a></li>
        		        <li class=\"\"><a href=\"#onglet-2".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en'))."</span></a></li>
        		        <li class=\"\"><a href=\"#onglet-3".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de'))."</span></a></li>
        		</ul>
			<div style=\"\" class=\"tabs-container\" id=\"onglet-1".$nom_champ."\">
       				".afficher_barre($champ_parent.".zone_multilingue_1_".$nom_champ)."<input type='text' class='formo' name=\"zone_multilingue_1_".$nom_champ."\" value=\"".extraire_multi_lang($titre, lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr'))."\" size='40'  />
			</div>
        		<div style=\"\" class=\"tabs-container tabs-hide\" id=\"onglet-2".$nom_champ."\">
        			".afficher_barre($champ_parent.".zone_multilingue_2_".$nom_champ)."<input type='text' class='formo' name=\"zone_multilingue_2_".$nom_champ."\" value=\"".extraire_multi_lang($titre, lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en'))."\" size='40'  />
			</div>
	
        		<div class=\"tabs-container tabs-hide\" id=\"onglet-3".$nom_champ."\">	
        			".afficher_barre($champ_parent.".zone_multilingue_3_".$nom_champ)."<input type='text' class='formo' name=\"zone_multilingue_3_".$nom_champ."\" value=\"".extraire_multi_lang($titre, lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de'))."\" size='40'  />
			</div>
			</div>";
		}
		else if ($champ_fin == "descriptif")
		{
			//cas des textarea
			if ($champ_fin == "texte")
			{
				$nb_rows = "rows=\"15\"";
			}
			else
			{
				$nb_rows = "rows=\"4\"";
			}
			
			$ret="
				<div class=\"container-onglets\">
        			<ul class=\"tabs-nav\">
        	        		<li class=\"tabs-selected\"><a href=\"#onglet-1".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr'))."</span></a></li>
        	        		<li class=\"\"><a href=\"#onglet-2".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en'))."</span></a></li>
        	        		<li class=\"\"><a href=\"#onglet-3".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de'))."</span></a></li>
        			</ul>
				<div style=\"\" class=\"tabs-container\" id=\"onglet-1".$nom_champ."\">
       					".afficher_barre($champ_parent.".zone_multilingue_1_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_1_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($descriptif, lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr')))."</textarea>
				</div>
        			<div style=\"\" class=\"tabs-container tabs-hide\" id=\"onglet-2".$nom_champ."\">
        			  	".afficher_barre($champ_parent.".zone_multilingue_2_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_2_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($descriptif, lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en')))."</textarea>     
				</div>
		
        			<div class=\"tabs-container tabs-hide\" id=\"onglet-3".$nom_champ."\">	
        			       ".afficher_barre($champ_parent.".zone_multilingue_3_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_3_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($descriptif, lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de')))."</textarea>
				</div>
				</div>
				";
		}
		else if ($champ_fin == "texte")
		{
			//cas des textarea
			if ($champ_fin == "texte")
			{
				$nb_rows = "rows=\"15\"";
			}
			else
			{
				$nb_rows = "rows=\"4\"";
			}
			
			$ret="
				<div class=\"container-onglets\">
        			<ul class=\"tabs-nav\">
        	        		<li class=\"tabs-selected\"><a href=\"#onglet-1".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr'))."</span></a></li>
        	        		<li class=\"\"><a href=\"#onglet-2".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en'))."</span></a></li>
        	        		<li class=\"\"><a href=\"#onglet-3".$nom_champ."\"><span>".traduire_nom_langue(lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de'))."</span></a></li>
        			</ul>
				<div style=\"\" class=\"tabs-container\" id=\"onglet-1".$nom_champ."\">
       					".afficher_barre($champ_parent.".zone_multilingue_1_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_1_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($texte, lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr')))."</textarea>
				</div>
        			<div style=\"\" class=\"tabs-container tabs-hide\" id=\"onglet-2".$nom_champ."\">
        			  	".afficher_barre($champ_parent.".zone_multilingue_2_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_2_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($texte, lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en')))."</textarea>     
				</div>
		
        			<div class=\"tabs-container tabs-hide\" id=\"onglet-3".$nom_champ."\">	
        			       ".afficher_barre($champ_parent.".zone_multilingue_3_".$nom_champ)."<textarea style=\"width: 480px;\" name=\"zone_multilingue_3_".$nom_champ."\" class=\"forml\" ".$nb_rows." cols=\"40\">".entites_html(extraire_multi_lang($texte, lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de')))."</textarea>
				</div>
				</div>
				";
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
			$('input[@name=titre]').val('<multi>[".lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr')."]'+$('input[@name=zone_multilingue_1_document_formulaire_rubrique_titre]').val()+'[".lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en')."]'+$('input[@name=zone_multilingue_2_document_formulaire_rubrique_titre]').val()+'[".lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de')."]'+$('input[@name=zone_multilingue_3_document_formulaire_rubrique_titre]').val()+'</multi>');
			$('textarea[@name=descriptif]').val('<multi>[".lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr')."]'+$('textarea[@name=zone_multilingue_1_document_formulaire_rubrique_descriptif]').val()+'[".lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en')."]'+$('textarea[@name=zone_multilingue_2_document_formulaire_rubrique_descriptif]').val()+'[".lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de')."]'+$('textarea[@name=zone_multilingue_3_document_formulaire_rubrique_descriptif]').val()+'</multi>');
			$('textarea[@name=texte]').val('<multi>[".lire_config('ExtensionMultilingue/langue1_ExtensionMultilingue','fr')."]'+$('textarea[@name=zone_multilingue_1_document_formulaire_rubrique_texte]').val()+'[".lire_config('ExtensionMultilingue/langue2_ExtensionMultilingue','en')."]'+$('textarea[@name=zone_multilingue_2_document_formulaire_rubrique_texte]').val()+'[".lire_config('ExtensionMultilingue/langue3_ExtensionMultilingue','de')."]'+$('textarea[@name=zone_multilingue_3_document_formulaire_rubrique_texte]').val()+'</multi>');
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
