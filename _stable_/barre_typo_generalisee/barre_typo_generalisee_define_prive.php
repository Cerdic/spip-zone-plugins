<?php

function barre_typo_generalisee_insertion_in_head_prive ($texte)
{
	if (!function_exists('lire_config')) {
		return ($texte);
	
	}	
	include_spip('inc/barre');
	//include_spip('inc/presentation');
	//include_spip('inc/documents');
	
	$activer_barres = "";
	
	//barres dans la page article
	if ($_GET['exec'] == "articles_edit")
	{
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
	}
	//barres dans la page rubrique
	else if ($_GET['exec'] == "rubriques_edit")
	{
		if ((lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
		|| (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
		|| (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on"))
		{
			$activer_barres .= "$('body.rubriques').find('form[textarea]').attr('name', 'formulaire');";
		}
	
		if (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_titre').insertBefore(\"input[@name=titre]\");
			$('body.rubriques').find('#barre_typo_rubrique_titre').css(\"display\",\"block\");";
		}
		if (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.rubriques').find('#barre_typo_rubrique_descriptif').css(\"display\",\"block\");";
		}
		if (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.rubriques').find('#barre_typo_rubrique_texte').insertBefore(\"textarea[@name=texte]\");
			$('body.rubriques').find('#barre_typo_rubrique_texte').css(\"display\",\"block\");";
		}
	}
	
	else if ($_GET['exec'] == "mots_type")
	{
		//barres dans la page groupe de mot clefs
		if (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_nom').insertBefore(\"input[@name=change_type]\");
			$('body.mots').find('#barre_typo_groupemot_nom').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.mots').find('#barre_typo_groupemot_descriptif').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_groupemot_texte').insertBefore(\"textarea[@name=texte]\");
			$('body.mots').find('#barre_typo_groupemot_texte').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
	}
	
	else if ($_GET['exec'] == "mots_edit")
	{
		//barres dans la page mot clefs
		if (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_mot_nom').insertBefore(\"input[@name=titre]\");
			$('body.mots').find('#barre_typo_mot_nom').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_mot_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.mots').find('#barre_typo_mot_descriptif').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.mots').find('#barre_typo_mot_texte').insertBefore(\"textarea[@name=texte]\");
			$('body.mots').find('#barre_typo_mot_texte').css(\"display\",\"block\");
			$('body.mots').find('form[textarea]').attr('name', 'formulaire');";
		}
	}
	
	else if ($_GET['exec'] == "sites_edit")
	{
		//barres dans la page site référencé
		if (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.sites').find('#barre_typo_site_nom').insertBefore(\"input[@name=nom_site]\");
			$('body.sites').find('#barre_typo_site_nom').css(\"display\",\"block\");
			$('body.sites').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.sites').find('#barre_typo_site_descriptif').insertBefore(\"textarea[@name=descriptif]\");
			$('body.sites').find('#barre_typo_site_descriptif').css(\"display\",\"block\");
			$('body.sites').find('form[textarea]').attr('name', 'formulaire');";
		}
	}
	
	else if ($_GET['exec'] == "breves_edit")
	{
		//barres dans la page brève
		if (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.breves').find('#barre_typo_breve_titre').insertBefore(\"input[@name=titre]\");
			$('body.breves').find('#barre_typo_breve_titre').css(\"display\",\"block\");";
		}
		if (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.breves').find('#barre_typo_breve_lien').insertBefore(\"input[@name=lien_titre]\");
			$('body.breves').find('#barre_typo_breve_lien').css(\"display\",\"block\");";
		}
	}
	
	else if ($_GET['exec'] == "configuration")
	{
		//barres dans la page configuration
		if ((lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on") || (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on"))
		{
			$activer_barres .= "$('body.configuration').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.configuration').find('#barre_typo_configuration_nom_site').insertAfter(\"input[@name=nom_site]\");
			$('body.configuration').find('#barre_typo_configuration_nom_site').css(\"display\",\"block\");";
		}
		if (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.configuration').find('#barre_typo_configuration_descriptif_site').insertBefore(\"textarea[@name=descriptif_site]\");
			$('body.configuration').find('#barre_typo_configuration_descriptif_site').css(\"display\",\"block\");";
		}
	}

	else if ($_GET['exec'] == "auteur_infos")
	{
		//barres dans la page auteur
		if (lire_config('barre_typo_generalisee/auteurs_signature_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.auteurs').find('#barre_typo_auteur_signature').insertAfter(\"input[@name=nom]\");
			$('body.auteurs').find('#barre_typo_auteur_signature').css(\"display\",\"block\");
			$('body.auteurs').find('form[textarea]').attr('name', 'formulaire');";
		}
		if (lire_config('barre_typo_generalisee/auteurs_quietesvous_barre_typo_generalisee') == "on")
		{
			$activer_barres .= "$('body.auteurs').find('#barre_typo_auteur_quietesvous').insertAfter(\"textarea[@name=bio]\");
			$('body.auteurs').find('#barre_typo_auteur_quietesvous').css(\"display\",\"block\");
			$('body.auteurs').find('form[textarea]').attr('name', 'formulaire');";
		}
	}
	
	if ($activer_barres != "")
	{
		$ajout_texte = "<script type=\"text/javascript\">
		$(document).ready(function(){
		".$activer_barres."
		});</script>";
	}
	else
	{
		$ajout_texte = "";
	}	



	return $texte.$ajout_texte;
	
}
function barre_typo_generalisee_insertion_in_body_prive ($texte)
{
	if (!function_exists('lire_config')) {
		return ($texte);
	
	}
	
	
		$barre_temporaire = "";
	
	//rubriques
	if ($_GET['exec'] == "rubriques_edit") 
	{
		if (lire_config('barre_typo_generalisee/rubriques_titre_barre_typo_generalisee') == "on")
		{
			$barre_temporaire .= "<div id=\"barre_typo_rubrique_titre\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/rubriques_descriptif_barre_typo_generalisee') == "on")
		{
			$barre_temporaire .= "<div id=\"barre_typo_rubrique_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";
		}
		if (lire_config('barre_typo_generalisee/rubriques_texte_barre_typo_generalisee') == "on")
		{
			$barre_temporaire .= "<div id=\"barre_typo_rubrique_texte\" style=\"display: none;\">".afficher_barre('document.formulaire.texte')."</div>";
		}
	}
	//groupes de mots clefs
	else if ($_GET['exec'] == "mots_type") 
	{
		if (lire_config('barre_typo_generalisee/groupesmots_nom_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_groupemot_nom\" style=\"display: none;\">".afficher_barre('document.formulaire.change_type')."</div>";
		}
		if (lire_config('barre_typo_generalisee/groupesmots_descriptif_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_groupemot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";
		}
		if (lire_config('barre_typo_generalisee/groupesmots_texte_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_groupemot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire.texte')."</div>";
		}
	}
	
	//mots clefs
	else if ($_GET['exec'] == "mots_edit")
	{
		if (lire_config('barre_typo_generalisee/mots_nom_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_mot_nom\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/mots_descriptif_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_mot_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";
		}
		if (lire_config('barre_typo_generalisee/mots_texte_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_mot_texte\" style=\"display: none;\">".afficher_barre('document.formulaire.texte')."</div>";
		}
	}		
	//sites référencés
	else if ($_GET['exec'] == "sites_edit")
	{
		if (lire_config('barre_typo_generalisee/sites_nom_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_site_nom\" style=\"display: none;\">".afficher_barre('document.formulaire.nom_site')."</div>";		
		}
		if (lire_config('barre_typo_generalisee/sites_description_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_site_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";		
		}
	}	

	//configuration
	else if ($_GET['exec'] == "configuration")
	{
		if (lire_config('barre_typo_generalisee/configuration_nom_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_configuration_nom_site\" style=\"display: none;\">".afficher_barre('document.formulaire.nom_site')."</div>";		
		}
		if (lire_config('barre_typo_generalisee/configuration_description_barre_typo_generalisee') == "on") 	
		{
			$barre_temporaire .= "<div id=\"barre_typo_configuration_descriptif_site\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif_site')."</div>";		
		}
	}
	//articles
	else if ($_GET['exec'] == "articles_edit") 
	{
		if (lire_config('barre_typo_generalisee/articles_surtitre_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_surtitre\" style=\"display: none;\">".afficher_barre('document.formulaire.surtitre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/articles_titre_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_titre\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/articles_soustitre_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_soustitre\" style=\"display: none;\">".afficher_barre('document.formulaire.soustitre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/articles_descriptif_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_descriptif\" style=\"display: none;\">".afficher_barre('document.formulaire.descriptif')."</div>";
		}
		if (lire_config('barre_typo_generalisee/articles_chapo_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_chapo\" style=\"display: none;\">".afficher_barre('document.formulaire.chapo')."</div>";
		}	
		if (lire_config('barre_typo_generalisee/articles_ps_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_article_ps\" style=\"display: none;\">".afficher_barre('document.formulaire.ps')."</div>";
		}
	}
	//breves
	else if ($_GET['exec'] == "breves_edit")
	{
		if (lire_config('barre_typo_generalisee/breves_titre_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_breve_titre\" style=\"display: none;\">".afficher_barre('document.formulaire.titre')."</div>";
		}
		if (lire_config('barre_typo_generalisee/breves_lien_barre_typo_generalisee') == "on")	
		{
			$barre_temporaire .= "<div id=\"barre_typo_breve_lien\" style=\"display: none;\">".afficher_barre('document.formulaire.lien_titre')."</div>";
		}
	}
	//auteurs
	else if ($_GET['exec'] == "auteur_infos")
	{
		if (lire_config('barre_typo_generalisee/auteurs_signature_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_auteur_signature\" style=\"display: none;\">".afficher_barre('document.formulaire.nom')."</div>";
		}
		if (lire_config('barre_typo_generalisee/auteurs_quietesvous_barre_typo_generalisee') == "on")		
		{
			$barre_temporaire .= "<div id=\"barre_typo_auteur_quietesvous\" style=\"display: none;\">".afficher_barre('document.formulaire.bio')."</div>";
		}
	}
	
	return $texte.$barre_temporaire;
}

?>