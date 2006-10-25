<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');

function exec_forms_tous(){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');

  Forms_install();
	
	debut_page(_T("forms:tous_formulaires"), "documents", "forms");
	debut_gauche();
	debut_boite_info();
	echo _T("forms:boite_info");
	echo "<p>";
	$link = generer_action_auteur('snippet_importe',"forms-forms",generer_url_ecrire('forms_tous'));
	echo "<form action='$link' method='POST' enctype='multipart/form-data'>";
	echo form_hidden($link);
	echo "<strong><label for='file_name'>"._T("forms:importer_form")."</label></strong> ";
	echo "<br />";
	echo "<input type='file' name='snippet_xml' id='file_name' class='formo'>";
	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";
	echo "</form></p>\n";
	
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