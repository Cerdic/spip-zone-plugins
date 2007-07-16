<?php

function typo_partout_insertion_in_body_prive ($texte)
{
	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	
	$activer_barres = "";
	
	//barres dans la page article
	if (lire_config('typo_partout/articles_surtitre_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/articles_titre_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/articles_soustitre_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/articles_descriptif_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/articles_chapo_typo_partout') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_chapo').insertBefore(\"textarea[@name=chapo]\");
		$('body.articles').find('.cadre-formulaire').find('#barre_typo_article_chapo').css(\"display\",\"block\");";
	}
	if (lire_config('typo_partout/articles_ps_typo_partout') == "on")
	{
		//a faire
	}

	//barres dans la page rubrique
	$activer_barre_rubriques = "";
	if (lire_config('typo_partout/rubriques_titre_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/rubriques_descriptif_typo_partout') == "on")
	{
		$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_rubrique_descriptif').css(\"display\",\"block\");	
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	if (lire_config('typo_partout/rubriques_texte_typo_partout') == "on")
	{
		$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.rubriques').find('.cadre-formulaire').find('#barre_typo_rubrique_texte').css(\"display\",\"block\");
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	
	//barres dans la page groupe de mot clefs
	$activer_barre_groupemots = "";
	if (lire_config('typo_partout/groupesmots_nom_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/groupesmots_descriptif_typo_partout') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_descriptif').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('typo_partout/groupesmots_texte_typo_partout') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_texte').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}

	//barres dans la page mot clefs
	$activer_barre_mots = "";
	if (lire_config('typo_partout/mots_nom_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/mots_descriptif_typo_partout') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_descriptif').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('typo_partout/mots_texte_typo_partout') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('.cadre-formulaire').find('#barre_typo_mot_texte').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	
	//barres dans la page site référencé
	$activer_barre_sites = "";
	if (lire_config('typo_partout/sites_nom_typo_partout') == "on")
	{
		
	}
	if (lire_config('typo_partout/sites_description_typo_partout') == "on")
	{
		$activer_barres .= "$('body.sites').find('#barre_typo_site_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.sites').find('.cadre-formulaire').find('#barre_typo_site_descriptif').css(\"display\",\"block\");
		$('body.sites').find('form[textarea]').attr('name', 'formulaire_site');";
	}

	//barres dans la page brève
	$activer_barre_breves = "";
	if (lire_config('typo_partout/breves_titre_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/breves_lien_typo_partout') == "on")
	{
		//a faire
	}
	
	
	//barres dans la page configuration
	$activer_barre_configuration = "";
	if (lire_config('typo_partout/configuration_nom_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/configuration_description_typo_partout') == "on")
	{
		$activer_barres .= "$('body.configuration').find('#barre_typo_configuration_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
		$('body.configuration').find('.cadre-couleur').find('#barre_typo_configuration_descriptif_site').css(\"display\",\"block\");
		$('body.configuration').find('form[textarea]').attr('name', 'formulaire_configuration');";
	}

	//barres dans la page auteur
	$activer_barre_auteurs = "";
	if (lire_config('typo_partout/auteurs_signature_typo_partout') == "on")
	{
		//a faire
	}
	if (lire_config('typo_partout/auteurs_quietesvous_typo_partout') == "on")
	{
		//a faire
	}

	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
		".$activer_barres."
		});
	</script>
";
	$barre_temporaire = "";
	
	//rubriques
	if (($_GET['exec'] == "rubriques_edit") && (lire_config('typo_partout/rubriques_descriptif_typo_partout') == "on"))
	{
		$barre_temporaire .= "<div id=\"barre_typo_rubrique_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.descriptif')."</div>";
	}
	if (($_GET['exec'] == "rubriques_edit") && (lire_config('typo_partout/rubriques_texte_typo_partout') == "on"))
	{
		$barre_temporaire .= "<div id=\"barre_typo_rubrique_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.texte')."</div>";
	}
	
	//groupes de mots clefs
	if (($_GET['exec'] == "mots_type") && (lire_config('typo_partout/groupesmots_descriptif_typo_partout') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.descriptif')."</div>";
	}
	if (($_GET['exec'] == "mots_type") && (lire_config('typo_partout/groupesmots_texte_typo_partout') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.texte')."</div>";
	}
	
	//mots clefs
	if (($_GET['exec'] == "mots_edit") && (lire_config('typo_partout/mots_descriptif_typo_partout') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.descriptif')."</div>";
	}
	if (($_GET['exec'] == "mots_edit") && (lire_config('typo_partout/mots_texte_typo_partout') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.texte')."</div>";
	}
			
	//sites référencés
	if (($_GET['exec'] == "sites_edit") && (lire_config('typo_partout/sites_description_typo_partout') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_site_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_site.descriptif')."</div>";		
	}
	

	//configuration
	if (($_GET['exec'] == "configuration") && (lire_config('typo_partout/configuration_description_typo_partout') == "on")) 	
	{
		$barre_temporaire .= "<div id=\"barre_typo_configuration_descriptif_site\" style=\"display: none;\">".afficher_barre('document.formulaire_configuration.descriptif_site')."</div>";		
	}
	
	//articles
	if (($_GET['exec'] == "articles_edit") && (lire_config('typo_partout/articles_chapo_typo_partout') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_chapo\" style=\"display: none;\">".afficher_barre('document.formulaire.chapo')."</div>";
	}	
	
	return $texte.$ajout_texte.$barre_temporaire;
	
}
function typo_partout_insertion_in_head_prive ($texte)
{
	return $texte."<script type=\"text/javascript\" src=\"".find_in_path('javascript/spip_barre.js')."\"></script>";
}

?>