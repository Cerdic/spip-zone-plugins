<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');

//
// Options des rubriques
//

function configuration_rubgeo_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// Récupérer les paramètres
	$geo_rubriques = gmap_lire_config('gmap_objets_geo', 'type_rubriques', "oui");
	$geo_articles = gmap_lire_config('gmap_objets_geo', 'type_articles', "oui");
	$geo_documents = gmap_lire_config('gmap_objets_geo', 'type_documents', "oui");
	$geo_breves = gmap_lire_config('gmap_objets_geo', 'type_breves', "oui");
	$geo_mots = gmap_lire_config('gmap_objets_geo', 'type_mots', "oui");
	$geo_auteurs = gmap_lire_config('gmap_objets_geo', 'type_auteurs', "oui");
	$tout_le_site = gmap_lire_config('gmap_objets_geo', 'tout_le_site', "oui");
	$simple_rubs = gmap_lire_config('gmap_objets_geo', 'liste', "");
	$rubgeo = array();
	if ($simple_rubs)
	{
		foreach ($simple_rubs as $rub)
			$rubgeo[] = 'rubrique|'.$rub;
	}
		
	// Type d'objet géolocalisable
	$corps .= '
<fieldset class="config_group">
	<legend>'._T('gmap:configuration_rubriques_types').'</legend>
	<div class="padding"><div class="interior">
		<div class="liste_choix">
			<input type="checkbox" name="choix_type_rubrique" id="choix_type_rubrique" value="oui"'.(($geo_rubriques==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_rubrique">'._T('gmap:choix_type_rubrique').'</label><br/>
			<input type="checkbox" name="choix_type_article" id="choix_type_article" value="oui"'.(($geo_articles==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_article">'._T('gmap:choix_type_article').'</label><br/>
			<input type="checkbox" name="choix_type_document" id="choix_type_document" value="oui"'.(($geo_documents==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_document">'._T('gmap:choix_type_document').'</label><br/>
			<input type="checkbox" name="choix_type_breve" id="choix_type_breve" value="oui"'.(($geo_breves==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_breve">'._T('gmap:choix_type_breve').'</label><br/>
			<input type="checkbox" name="choix_type_mot" id="choix_type_mot" value="oui"'.(($geo_mots==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_mot">'._T('gmap:choix_type_mot').'</label><br/>
			<input type="checkbox" name="choix_type_auteur" id="choix_type_auteur" value="oui"'.(($geo_auteurs==="oui")?'checked="checked"':'').' />&nbsp;<label for="choix_type_auteur">'._T('gmap:choix_type_auteur').'</label>
		</div>
	</div></div>
	</fieldset>';
	
	// Pour sélectionner les rubriques, on utilise le selecteur de spip_bonux
	$corps .= '
<fieldset class="config_group">
	<legend>'._T('gmap:configuration_rubriques_liste').'</legend>
	<div class="padding"><div class="interior">
		<p class="explications">'._T('gmap:explication_restriction').'</p>
		<p><label for="tout_le_site">'._T('gmap:choix_restrictions').'</label><br />
		<select name="tout_le_site" id="tout_le_site" size="1">
			<option value="oui"'.(($tout_le_site==="oui") ? ' selected="selected"' : '').'>'._T('gmap:choix_tout_le_site').'</option>
			<option value="non"'.(($tout_le_site==="non") ? ' selected="selected"' : '').'>'._T('gmap:choix_restriction_rubriques').'</option>
		</select></p>
<script type="text/javascript">
//<![CDATA[
function toggleChoixListeRubriques()
{
	var bToutLeSite = (jQuery("#tout_le_site").val() === "oui") ? true : false;
	if (bToutLeSite)
		jQuery("#choix_liste_rubriques").hide();
	else
		jQuery("#choix_liste_rubriques").show();
}
jQuery(document).ready(function()
{
	jQuery("#tout_le_site").click(function()
	{
		toggleChoixListeRubriques();
	});
	toggleChoixListeRubriques();
});
//]]>
</script>
		<div id="choix_liste_rubriques">
			<p class="explications">'._T('gmap:explication_liste_rubriques').'</p>';
	$navigateur = recuperer_fond('formulaires/selecteur/rubriques',
						array(
							'selected'=>$rubgeo,
							'name'=>'les_rubriques',
							'rubriques'=>'1',
							'articles'=>'0'));
	$corps .= '
			<div class="navigateur">' . $navigateur . '</div>
			<div class="nettoyeur"></div>
		</div>
		<div class="nettoyeur"></div>
	</div></div>
</fieldset>';
	
	return gmap_formulaire_submit('configuration_rubgeo', $corps,
		find_in_path('images/logo-config-rubgeo.png'),
		_T('gmap:configuration_rubriques'));
// Plus en ajax à cause du panneau gauche qui ne serait plus raffraichi...
//	return gmap_formulaire_ajax('config_bloc_gmap', 'rubgeo', 'configurer_gmap', $corps,
//		find_in_path('images/logo-config-rubgeo.png'),
//		_T('gmap:configuration_rubriques'));
}
?>
