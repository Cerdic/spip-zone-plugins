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

function exec_donnees_tous(){
	global $spip_lang_right;
  include_spip("inc/presentation");
	include_spip('public/assembler');

  Forms_install();
	
	echo debut_page(_T("forms:tous_formulaires"), "documents", "forms");
	$row=spip_fetch_array(spip_query("SELECT titre FROM spip_forms WHERE id_form="._q(_request('id_form'))));
	echo gros_titre($row['titre']);
	echo "<div class='verdana2'>";
	echo '<p><div id="sorting">
	<div>Tri en cours, un instant...</div>
	</div>
	<div id="filter"></div></p></div>';
	
	
	$contexte = array('id_form'=>_request('id_form'),'couleur_claire'=>$GLOBALS['couleur_claire'],'couleur_foncee'=>$GLOBALS['couleur_foncee']);
	echo recuperer_fond("exec/donnees_tous",$contexte);
	
	echo "<br />\n";
	

	echo fin_page();
}

?>