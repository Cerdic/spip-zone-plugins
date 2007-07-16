<?php

function typo_partout_insertion_in_body_prive ($texte)
{
	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	$activer_barre_rubriques = "";
	if (lire_config('typo_partout/rubriques_typo_partout') == "on")
	{
		$activer_barre_rubriques = "$('body.rubriques').find('#barre_typo_rubrique_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.rubriques').find('#barre_typo_rubrique_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_rubrique_texte').css(\"display\",\"block\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_rubrique_descriptif').css(\"display\",\"block\");	
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	$activer_barre_mots = "";
	if (lire_config('typo_partout/mots_typo_partout') == "on")
	{
		$activer_barre_mots="$('body.mots').find('#barre_typo_mot_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('#barre_typo_mot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_texte').css(\"display\",\"block\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_descriptif').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	$activer_barre_configuration = "";
	if (lire_config('typo_partout/configuration_typo_partout') == "on")
	{
		$activer_barre_configuration="$('body.configuration').find('#barre_typo_configuration_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
		$('body.configuration').find('.cadre-couleur').find('#barre_typo_configuration_descriptif_site').css(\"display\",\"block\");
		$('body.configuration').find('form[textarea]').attr('name', 'formulaire_configuration');";
	}
	$activer_barre_sites = "";
	if (lire_config('typo_partout/sites_typo_partout') == "on")
	{
		$activer_barre_sites="$('body.sites').find('#barre_typo_site_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.sites').find('.cadre-formulaire').find('#barre_typo_site_descriptif').css(\"display\",\"block\");
		$('body.sites').find('form[textarea]').attr('name', 'formulaire_site');";
	}
	$activer_barre_articles = "";
	if (lire_config('typo_partout/articles_typo_partout') == "on")
	{
		$activer_barre_articles="$('body.articles').find('#barre_typo_article_chapo').insertBefore(\"textarea[@name=chapo]\");
		$('body.articles').find('.cadre-formulaire').find('#barre_typo_article_chapo').css(\"display\",\"block\");";
	}
	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
		".$activer_barre_rubriques.$activer_barre_mots.$activer_barre_configuration.$activer_barre_sites.$activer_barre_articles."
		});
	</script>
";

	if (($_GET['exec'] == "rubriques_edit") && (lire_config('typo_partout/rubriques_typo_partout') == "on"))
	{
		$barre_temporaire = "<div id=\"barre_typo_rubrique_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.texte')."</div>
		<div id=\"barre_typo_rubrique_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.descriptif')."</div>";
	}
	else if ((($_GET['exec'] == "mots_edit")||($_GET['exec'] == "mots_type"))	&& (lire_config('typo_partout/mots_typo_partout') == "on"))	
	{
		$barre_temporaire = "<div id=\"barre_typo_mot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.texte')."</div>
		<div id=\"barre_typo_mot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.descriptif')."</div>";
	}		
	else if (($_GET['exec'] == "sites_edit") && (lire_config('typo_partout/sites_typo_partout') == "on"))		
	{
		$barre_temporaire = "<div id=\"barre_typo_site_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_site.descriptif')."</div>";		
	}
	else if (($_GET['exec'] == "configuration") && (lire_config('typo_partout/configuration_typo_partout') == "on")) 	
	{
		$barre_temporaire = "<div id=\"barre_typo_configuration_descriptif_site\" style=\"display: none;\">".afficher_barre('document.formulaire_configuration.descriptif_site')."</div>";		
	}
	else if (($_GET['exec'] == "articles_edit") && (lire_config('typo_partout/articles_typo_partout') == "on"))		
	{
		$barre_temporaire = "<div id=\"barre_typo_article_chapo\" style=\"display: none;\">".afficher_barre('document.formulaire.chapo')."</div>";
	}	
	else
	{
		$barre_temporaire = "";
	}
	return $texte.$ajout_texte.$barre_temporaire;
	
}
function typo_partout_insertion_in_head_prive ($texte)
{
	return $texte."<script type=\"text/javascript\" src=\"".find_in_path('javascript/spip_barre.js')."\"></script>";
}

?>