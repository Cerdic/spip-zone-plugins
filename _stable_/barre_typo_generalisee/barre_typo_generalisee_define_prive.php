<?php

function barre_typo_generalisee_insertion_in_body_prive ($texte)
{
	if (!function_exists('lire_config')) {
		return ($texte);
	
	}	

	include_spip('inc/barre');
	include_spip('inc/presentation');
	include_spip('inc/documents');
	
	$activer_barres = "";
	
	//barres dans la page article
	if (lire_config('barre_typo_generalisee/articles_surtitre_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_surtitre').insertBefore(\"input[@name=surtitre]\");
		$('body.articles').find('#barre_typo_article_surtitre').css(\"display\",\"block\");";
	}
	if (lire_config('barre_typo_generalisee/articles_titre_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_titre').insertBefore(\"input[@name=titre]\");
		$('body.articles').find('#barre_typo_article_titre').css(\"display\",\"block\");";
	}
	if (lire_config('barre_typo_generalisee/articles_soustitre_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_soustitre').insertBefore(\"input[@name=soustitre]\");
		$('body.articles').find('#barre_typo_article_soustitre').css(\"display\",\"block\");";
	}
	if (lire_config('barre_typo_generalisee/articles_descriptif_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.articles').find('#barre_typo_article_descriptif').css(\"display\",\"block\");";
	}
	if (lire_config('barre_typo_generalisee/articles_chapo_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_chapo').insertBefore(\"textarea[@name=chapo]\");
		$('body.articles').find('#barre_typo_article_chapo').css(\"display\",\"block\");";
	}
	if (lire_config('barre_typo_generalisee/articles_ps_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.articles').find('#barre_typo_article_ps').insertBefore(\"textarea[@name=ps]\");
		$('body.articles').find('#barre_typo_article_ps').css(\"display\",\"block\");";
	}

	//barres dans la page rubrique
	$activer_barre_rubriques = "";
	if (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_titre').insertBefore(\"input[@name=titre]\");
		$('body.rubriques').find('#barre_typo_rubrique_titre').css(\"display\",\"block\");	
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	if (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.rubriques').find('#barre_typo_rubrique_descriptif').css(\"display\",\"block\");	
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	if (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.rubriques').find('#barre_typo_rubrique_texte').css(\"display\",\"block\");
		$('body.rubriques').find('form[textarea]').attr('name', 'formulaire_rubrique');";
	}
	
	//barres dans la page groupe de mot clefs
	$activer_barre_groupemots = "";
	if (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_nom').insertBefore(\"input[@name=change_type]\");
		$('body.mots').find('#barre_typo_groupemot_nom').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('#barre_typo_groupemot_descriptif').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('#barre_typo_groupemot_texte').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}

	//barres dans la page mot clefs
	$activer_barre_mots = "";
	if (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_nom').insertBefore(\"input[@name=titre]\");
		$('body.mots').find('#barre_typo_mot_nom').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.mots').find('#barre_typo_mot_descriptif').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	if (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.mots').find('#barre_typo_mot_texte').insertBefore(\"textarea[@name=texte]\");
		$('body.mots').find('#barre_typo_mot_texte').css(\"display\",\"block\");
		$('body.mots').find('form[textarea]').attr('name', 'formulaire_mot');";
	}
	
	//barres dans la page site référencé
	$activer_barre_sites = "";
	if (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.sites').find('#barre_typo_site_nom').insertBefore(\"input[@name=nom_site]\");
		$('body.sites').find('#barre_typo_site_nom').css(\"display\",\"block\");
		$('body.sites').find('form[textarea]').attr('name', 'formulaire_site');";
	}
	if (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.sites').find('#barre_typo_site_descriptif').insertBefore(\"textarea[@name=descriptif]\");
		$('body.sites').find('#barre_typo_site_descriptif').css(\"display\",\"block\");
		$('body.sites').find('form[textarea]').attr('name', 'formulaire_site');";
	}

	//barres dans la page brève
	$activer_barre_breves = "";
	if (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.breves').find('#barre_typo_breve_titre').insertBefore(\"input[@name=titre]\");
		$('body.breves').find('#barre_typo_breve_titre').css(\"display\",\"block\");";
		//$('body.breves').find('form[textarea]').attr('name', 'formulaire_breve');";
	}
	if (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.breves').find('#barre_typo_breve_lien').insertBefore(\"input[@name=lien_titre]\");
		$('body.breves').find('#barre_typo_breve_lien').css(\"display\",\"block\");";
		//$('body.breves').find('form[textarea]').attr('name', 'formulaire_breve');";
	}
	
	
	//barres dans la page configuration
	$activer_barre_configuration = "";
	if (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.configuration').find('#barre_typo_configuration_nom_site').insertAfter(\"input[@name=nom_site]\");
		$('body.configuration').find('#barre_typo_configuration_nom_site').css(\"display\",\"block\");
		$('body.configuration').find('form[textarea]').attr('name', 'formulaire_configuration');";
	}
	if (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.configuration').find('#barre_typo_configuration_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
		$('body.configuration').find('#barre_typo_configuration_descriptif_site').css(\"display\",\"block\");
		$('body.configuration').find('form[textarea]').attr('name', 'formulaire_configuration');";
	}

	//barres dans la page auteur
	$activer_barre_auteurs = "";
	if (lire_config('barre_typo_generalisee/auteurs_signature_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.auteurs').find('#barre_typo_auteur_signature').insertAfter(\"input[@name=nom]\");
		$('body.auteurs').find('#barre_typo_auteur_signature').css(\"display\",\"block\");
		$('body.auteurs').find('form[textarea]').attr('name', 'formulaire_auteur');";
	}
	if (lire_config('barre_typo_generalisee/auteurs_quietesvous_barre_typo_generalisee') == "on")
	{
		$activer_barres .= "$('body.auteurs').find('#barre_typo_auteur_quietesvous').insertAfter(\"textarea[@name=bio]\");
		$('body.auteurs').find('#barre_typo_auteur_quietesvous').css(\"display\",\"block\");
		$('body.auteurs').find('form[textarea]').attr('name', 'formulaire_auteur');";
	}

	$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
		".$activer_barres."
		});
	</script>
";
	$barre_temporaire = "";
	
	//rubriques
	if (($_GET['exec'] == "rubriques_edit") && (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on"))
	{
		$barre_temporaire .= "<div id=\"barre_typo_rubrique_titre\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.titre')."</div>";
	}
	if (($_GET['exec'] == "rubriques_edit") && (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on"))
	{
		$barre_temporaire .= "<div id=\"barre_typo_rubrique_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.descriptif')."</div>";
	}
	if (($_GET['exec'] == "rubriques_edit") && (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on"))
	{
		$barre_temporaire .= "<div id=\"barre_typo_rubrique_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_rubrique.texte')."</div>";
	}
	
	//groupes de mots clefs
	if (($_GET['exec'] == "mots_type") && (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_groupemot_nom\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.change_type')."</div>";
	}
	if (($_GET['exec'] == "mots_type") && (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_groupemot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.descriptif')."</div>";
	}
	if (($_GET['exec'] == "mots_type") && (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_groupemot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.texte')."</div>";
	}
	
	//mots clefs
	if (($_GET['exec'] == "mots_edit") && (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_nom\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.titre')."</div>";
	}
	if (($_GET['exec'] == "mots_edit") && (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.descriptif')."</div>";
	}
	if (($_GET['exec'] == "mots_edit") && (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on"))	
	{
		$barre_temporaire .= "<div id=\"barre_typo_mot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire_mot.texte')."</div>";
	}
			
	//sites référencés
	if (($_GET['exec'] == "sites_edit") && (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_site_nom\" style=\"display: none;\">".afficher_barre('document.formulaire_site.nom_site')."</div>";		
	}
	if (($_GET['exec'] == "sites_edit") && (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_site_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire_site.descriptif')."</div>";		
	}
	

	//configuration
	if (($_GET['exec'] == "configuration") && (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")) 	
	{
		$barre_temporaire .= "<div id=\"barre_typo_configuration_nom_site\" style=\"display: none;\">".afficher_barre('document.formulaire_configuration.nom_site')."</div>";		
	}
	if (($_GET['exec'] == "configuration") && (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on")) 	
	{
		$barre_temporaire .= "<div id=\"barre_typo_configuration_descriptif_site\" style=\"display: none;\">".afficher_barre('document.formulaire_configuration.descriptif_site')."</div>";		
	}
	
	//articles
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_surtitre_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_surtitre\" style=\"display: none;\">".afficher_barre('document.formulaire.surtitre')."</div>";
	}
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_titre_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_titre\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
	}
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_soustitre_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_soustitre\" style=\"display: none;\">".afficher_barre('document.formulaire.soustitre')."</div>";
	}
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_descriptif_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";
	}
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_chapo_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_chapo\" style=\"display: none;\">".afficher_barre('document.formulaire.chapo')."</div>";
	}	
	if (($_GET['exec'] == "articles_edit") && (lire_config('barre_typo_generalisee/articles_ps_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_article_ps\" style=\"display: none;\">".afficher_barre('document.formulaire.ps')."</div>";
	}
	
	//breves
	if (($_GET['exec'] == "breves_edit") && (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_breve_titre\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
	}
	if (($_GET['exec'] == "breves_edit") && (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_breve_lien\" style=\"display: none;\">".afficher_barre('document.formulaire.lien_titre')."</div>";
	}

	//auteurs
	if (($_GET['exec'] == "auteur_infos") && (lire_config('barre_typo_generalisee/auteurs_signature_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_auteur_signature\" style=\"display: none;\">".afficher_barre('document.formulaire_auteur.nom')."</div>";
	}
	if (($_GET['exec'] == "auteur_infos") && (lire_config('barre_typo_generalisee/auteurs_quietesvous_barre_typo_generalisee') == "on"))		
	{
		$barre_temporaire .= "<div id=\"barre_typo_auteur_quietesvous\" style=\"display: none;\">".afficher_barre('document.formulaire_auteur.bio')."</div>";
	}
	
	return $texte.$ajout_texte.$barre_temporaire;
	
}
function barre_typo_generalisee_insertion_in_head_prive ($texte)
{
	if (!function_exists('lire_config')) {
		return ($texte);
	
	}
	return $texte."<script type=\"text/javascript\" src=\"".find_in_path('javascript/spip_barre.js')."\"></script>";
}

?>