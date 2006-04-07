<?php

include_ecrire('inc_forms');

function Forms_duplique_form(){
	$duplique = intval(_request('duplique_form'));
	if ($duplique && Forms_form_administrable($duplique)){
		// creation
			$schema = array();
			spip_query("INSERT INTO spip_forms (schema) VALUES ('".
				addslashes(serialize($schema))."')");
			$id_form = spip_insert_id();
		$query = "SELECT * FROM spip_forms WHERE id_form=$duplique";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
			$schema = $row['schema'];
			$email = $row['email'];
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];

			$query = "UPDATE spip_forms SET ".
				"titre='"._L('Copie de ').addslashes($titre)."', ".
				"descriptif='".addslashes($descriptif)."', ".
				"sondage='".addslashes($sondage)."', ".
				"schema='".addslashes($schema)."', ".
				"email='".addslashes($email)."', ".
				"champconfirm='".addslashes($champconfirm)."', ".
				"texte='".addslashes($texte)."' ".
				"WHERE id_form=$id_form";
			$result = spip_query($query);
		}
	}	
}

function exec_forms_tous(){
	//global $clean_link;
  include_spip("inc/presentation");

  Forms_verifier_base();
	Forms_duplique_form();
	
	debut_page(_L("Tous les formulaires"), "documents", "forms");
	debut_gauche();
	debut_boite_info();
	echo _L("Cliquez sur un formulaire pour le modifier ou le visualiser avant suppression.");
	fin_boite_info();
	
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
		$link=generer_url_ecrire('forms_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_L("Cr&eacute;er un nouveau formulaire"), $link, "../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "creer.gif");
		echo "</div>";
	}
	
	
	
	fin_page();
}

?>
