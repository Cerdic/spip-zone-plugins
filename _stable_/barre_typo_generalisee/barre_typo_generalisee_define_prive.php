<?php
function BTG_on($v, $d=null) { return lire_config("barre_typo_generalisee/{$v}_barre_typo_generalisee", $d) == 'on'; }
function BTG_insertBefore($balise, $id, $name) { 
	return "\t$('#barre_typo_$id').insertBefore('{$balise}[@name=$name]');\n\t$('form #barre_typo_$id').css('display','block');\n";
}
function BTG_insertAfter($balise, $id, $name) { 
	return "\t$('#barre_typo_$id').insertAfter('{$balise}[@name=$name]');\n\t$('form #barre_typo_$id').css('display','block');\n";
}
function BTG_barre($id, $name) {
	return "<div id='barre_typo_$id' style='display: none;'>".afficher_barre("document.getElementsByName('$name')[0]").'</div>';
}

function barre_typo_generalisee_insertion_javascript() {
	if (!function_exists('lire_config')) return '';
	include_spip('inc/barre');
	$activer_barres = "";
	
	switch($_GET['exec']) {
	case 'articles_edit':
		// barres dans la page article
		if (BTG_on('articles_surtitre')) $activer_barres .= BTG_insertBefore('input','article_surtitre','surtitre');
		if (BTG_on('articles_titre')) $activer_barres .= BTG_insertBefore('input','article_titre','titre');
		if (BTG_on('articles_soustitre')) $activer_barres .= BTG_insertBefore('input','article_soustitre','soustitre');
		if (BTG_on('articles_descriptif')) $activer_barres .= BTG_insertBefore('textarea','article_descriptif','descriptif');
		if (BTG_on('articles_chapo')) $activer_barres .= BTG_insertBefore('textarea','article_chapo','chapo');
		if (BTG_on('articles_ps')) $activer_barres .= BTG_insertBefore('textarea','article_ps','ps');
		break;
	case 'rubriques_edit':
		// barres dans la page rubrique
		if (BTG_on('rubriques_titre')) $activer_barres .= BTG_insertBefore('input','rubrique_titre','titre');
		if (BTG_on('rubriques_descriptif')) $activer_barres .= BTG_insertBefore('textarea','rubrique_descriptif','descriptif');
		if (BTG_on('rubriques_texte')) $activer_barres .= BTG_insertBefore('textarea','rubrique_texte','texte');
		break;
	case 'mots_type':
		// barres dans la page groupe de mot clefs
		if (BTG_on('groupesmots_nom')) $activer_barres .= BTG_insertBefore('input','groupemot_nom','change_type');
		if (BTG_on('groupesmots_descriptif')) $activer_barres .= BTG_insertBefore('textarea','groupemot_descriptif','descriptif');
		if (BTG_on('groupesmots_texte')) $activer_barres .= BTG_insertBefore('textarea','groupemot_texte','texte');
		break;
	case 'mots_edit':
		// barres dans la page mot clefs
		if (BTG_on('mots_nom')) $activer_barres .= BTG_insertBefore('input','mot_nom','titre');
		if (BTG_on('mots_descriptif')) $activer_barres .= BTG_insertBefore('textarea','mot_descriptif','descriptif');
		if (BTG_on('mots_texte')) $activer_barres .= BTG_insertBefore('textarea','mot_texte','texte');
		break;
	case 'sites_edit':
		// barres dans la page site reference
		if (BTG_on('sites_nom')) $activer_barres .= BTG_insertBefore('input','site_nom','nom_site');
		if (BTG_on('sites_description')) $activer_barres .= BTG_insertBefore('textarea','site_descriptif','descriptif');
		break;
	case 'breves_edit':
		// barres dans la page breve
		if (BTG_on('breves_titre')) $activer_barres .= BTG_insertBefore('input','breve_titre','titre');
		if (BTG_on('breves_lien')) $activer_barres .= BTG_insertBefore('input','breve_lien','lien_titre');
		break;
	case 'configuration':
		// barres dans la page configuration
		if (BTG_on('configuration_nom')) $activer_barres .= BTG_insertBefore('input','configuration_nom_site','nom_site');
		if (BTG_on('configuration_description')) $activer_barres .= BTG_insertBefore('textarea','configuration_descriptif_site','descriptif_site');
		break;
	case 'auteur_infos':
		// barres dans la page auteur
		if (BTG_on('auteurs_signature')) $activer_barres .= BTG_insertBefore('input','auteur_signature','nom');
		if (BTG_on('auteurs_quietesvous')) $activer_barres .= BTG_insertBefore('textarea','auteur_quietesvous','bio');
	}
	if (strlen($activer_barres))
		return "<script type=\"text/javascript\"><!--
$(document).ready(function(){
$activer_barres});
//--></script>
";
	return '';
}

function barre_typo_generalisee_barre_typo_generalisee_body_prive ($texte) {
	if (!function_exists('lire_config')) return ($texte);
	$texte .= barre_typo_generalisee_insertion_javascript();
	$barre_temporaire = "";
	
	switch($_GET['exec']) {
	case 'rubriques_edit':
		// rubriques
		if (BTG_on('rubriques_titre')) $barre_temporaire .= BTG_barre('rubrique_titre','titre');
		if (BTG_on('rubriques_descriptif')) $barre_temporaire .= BTG_barre('rubrique_descriptif','descriptif');
		if (BTG_on('rubriques_texte')) $barre_temporaire .= BTG_barre('rubrique_texte','texte');
		break;
	case 'mots_type':
		// groupes de mots clefs
		if (BTG_on('groupesmots_nom')) $barre_temporaire .= BTG_barre('groupemot_nom','change_type');
		if (BTG_on('groupesmots_descriptif')) $barre_temporaire .= BTG_barre('groupemot_descriptif','descriptif');
		if (BTG_on('groupesmots_texte')) $barre_temporaire .= BTG_barre('groupemot_texte','texte');
		break;
	case 'mots_edit':
		// mots clefs
		if (BTG_on('mots_nom')) $barre_temporaire .= BTG_barre('mot_nom','titre');
		if (BTG_on('mots_descriptif')) $barre_temporaire .= BTG_barre('mot_descriptif','descriptif');
		if (BTG_on('mots_texte')) $barre_temporaire .= BTG_barre('mot_texte','texte');
		break;
	case 'sites_edit':
		// sites references
		if (BTG_on('sites_nom')) $barre_temporaire .= BTG_barre('site_nom','nom_site');
		if (BTG_on('sites_description')) $barre_temporaire .= BTG_barre('site_descriptif','descriptif');
		break;
	case 'configuration':
		// configuration
		if (BTG_on('configuration_nom')) $barre_temporaire .= BTG_barre('configuration_nom_site','nom_site');
		if (BTG_on('configuration_description')) $barre_temporaire .= BTG_barre('configuration_descriptif_site','descriptif_site');
		break;
	case 'articles_edit':
		// articles
		if (BTG_on('articles_surtitre')) $barre_temporaire .= BTG_barre('article_surtitre','surtitre');
		if (BTG_on('articles_titre')) $barre_temporaire .= BTG_barre('article_titre','titre');
		if (BTG_on('articles_soustitre')) $barre_temporaire .= BTG_barre('article_soustitre','soustitre');
		if (BTG_on('articles_descriptif')) $barre_temporaire .= BTG_barre('article_descriptif','descriptif');
		if (BTG_on('articles_chapo')) $barre_temporaire .= BTG_barre('article_chapo','chapo');	
		if (BTG_on('articles_ps')) $barre_temporaire .= BTG_barre('article_ps','ps');
		break;
	case 'breves_edit':
		// breves
		if (BTG_on('breves_titre')) $barre_temporaire .= BTG_barre('breve_titre','titre');
		if (BTG_on('breves_lien')) $barre_temporaire .= BTG_barre('breve_lien','lien_titre');
		break;
	case 'auteur_infos':
		// auteurs
		if (BTG_on('auteurs_signature')) $barre_temporaire .= BTG_barre('auteur_signature','nom');
		if (BTG_on('auteurs_quietesvous')) $barre_temporaire .= BTG_barre('auteur_quietesvous','bio');
	}
	return $texte.$barre_temporaire;
}

?>