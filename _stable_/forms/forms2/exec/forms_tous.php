<?php

include_spip('inc/forms');

function Forms_duplique_form(){
	$duplique = intval(_request('duplique_form'));
	if ($duplique && Forms_form_administrable($duplique)){
		// creation
			$structure = array();
			spip_query("INSERT INTO spip_forms (structure) VALUES ('".
				addslashes(serialize($structure))."')");
			$id_form = spip_insert_id();
		$query = "SELECT * FROM spip_forms WHERE id_form=$duplique";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$sondage = $row['sondage'];
			$structure = $row['structure'];
			$email = $row['email'];
			$champconfirm = $row['champconfirm'];
			$texte = $row['texte'];

			$query = "UPDATE spip_forms SET ".
				"titre='"._T('forms:formulaires_copie').addslashes($titre)."', ".
				"descriptif='".addslashes($descriptif)."', ".
				"sondage='".addslashes($sondage)."', ".
				"structure='".addslashes($structure)."', ".
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
	include_spip('public/assembler');

  Forms_install();
	Forms_duplique_form();
	
	debut_page(_T("forms:tous_formulaires"), "documents", "forms");
	debut_gauche();
	debut_boite_info();
	echo _T("forms:boite_info");
	fin_boite_info();
	
	debut_droite();
	
	$contexte = array('couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("exec/forms_tous",$contexte);	
	
	echo "<br />\n";
	
	if (Forms_form_editable()) {
		echo "<div align='right'>";
		$link=generer_url_ecrire('forms_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self()));
		icone(_T("forms:icone_creer_formulaire"), $link, "../"._DIR_PLUGIN_FORMS. "/img_pack/form-24.png", "creer.gif");
		echo "</div>";
	}
	
	fin_page();
}

?>
