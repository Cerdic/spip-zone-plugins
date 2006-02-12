<?php

include_once (dirname(__FILE__).'/inc_forms.php');

function forms_tous(){
	global $clean_link;
  include_ecrire("inc_presentation");
	include_ecrire('inc_base');
	creer_base(); // au cas ou

debut_page(_L("Tous les formulaires"), "documents", "forms");
debut_gauche();



debut_droite();



Forms_afficher_forms(_L("Tous les formulaires"),
	"SELECT forms.*, COUNT(id_reponse) AS reponses ".
	"FROM spip_forms AS forms LEFT JOIN spip_reponses AS reponses ".
	"ON (forms.id_form=reponses.id_form AND reponses.statut='valide') ".
	"WHERE sondage='non' GROUP BY forms.id_form ORDER BY titre");

Forms_afficher_forms(_L("Tous les sondages publics"),
	"SELECT forms.*, COUNT(id_reponse) AS reponses ".
	"FROM spip_forms AS forms LEFT JOIN spip_reponses AS reponses ".
	"ON (forms.id_form=reponses.id_form AND reponses.statut='valide') ".
	"WHERE sondage='public' GROUP BY forms.id_form ORDER BY titre",
	"statistiques-24.gif");

Forms_afficher_forms(_L("Tous les sondages prot&eacute;g&eacute;s"),
	"SELECT forms.*, COUNT(id_reponse) AS reponses ".
	"FROM spip_forms AS forms LEFT JOIN spip_reponses AS reponses ".
	"ON (forms.id_form=reponses.id_form AND reponses.statut='valide') ".
	"WHERE sondage='prot' GROUP BY forms.id_form ORDER BY titre",
	"statistiques-24.gif");

echo "<br />\n";

if (Forms_form_editable()) {
	echo "<div align='right'>";
	$link = new Link('?exec=forms_edit');
	$link->addVar('new', 'oui');
	$link->addVar('retour', $clean_link->getUrl());
	icone(_L("Cr&eacute;er un nouveau formulaire"), $link->getUrl(), "../"._DIR_PLUGIN_FORMS. "/form-24.png", "creer.gif");
	echo "</div>";
}



fin_page();
}

//$SCRIPT_NAME='forms_tous';
//chdir('../../ecrire/');
//include ("inc.php3");

?>
