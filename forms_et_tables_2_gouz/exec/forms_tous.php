<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * � 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms');

function exec_forms_tous(){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');

  _Forms_install();
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T("forms:tous_formulaires"), "documents", "forms");
	echo debut_gauche('', true);
	echo debut_boite_info(true);
	echo _T("forms:boite_info");
	echo fin_boite_info(true);
	
	echo creer_colonne_droite('', true);
	if (include_spip('inc/snippets'))
		echo boite_snippets(_T('forms:formulaire'),_DIR_PLUGIN_FORMS."img_pack/form-24.gif",'forms','forms');
	
	echo debut_droite('', true);
	
	/*$contexte = array('couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("exec/forms_tous",$contexte);	*/
	
	$contexte = array('type_form'=>'','titre_liste'=>_T('forms:tous_formulaires'),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("fonds/tables_tous",$contexte);
	echo "<br />\n";

	$contexte = array('type_form'=>'sondage','titre_liste'=>_T('forms:tous_sondages'),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("fonds/tables_tous",$contexte);
	echo "<br />\n";

	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('creer','form')) {
		echo "<div style='text-align:right'>";
		$link=generer_url_ecrire('forms_edit', 'new=oui');
		$link=parametre_url($link,'retour',str_replace('&amp;', '&', self('&amp;', true)));
		echo icone(_T("forms:icone_creer_formulaire"), $link, "../"._DIR_PLUGIN_FORMS. "img_pack/form-24.png", "creer.gif");
		echo "</div>";
	}
	
	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}

?>