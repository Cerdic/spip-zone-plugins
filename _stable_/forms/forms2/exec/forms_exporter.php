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

//
// Export d'un formulaire
//
function exec_forms_exporter(){
	$id_form = _request('id_form');
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (!autoriser('administrer','form',$id_form)) {
		debut_page(_T('avis_acces_interdit'), "documents", "forms");
		debut_gauche();
		debut_droite();
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}

	include_spip('public/assembler');
	$out = recuperer_fond('snippets/forms_exporter',array('id_form'=>$id_form));
	$out = preg_replace(",\n[\s]*(?=\n),","",$out);

	$row = spip_fetch_array(spip_query("SELECT titre FROM spip_forms WHERE id_form="._q($id_form)));
 	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($row['titre']))));
	$extension = "xml";
	
	Header("Content-Type: text/xml; charset=".$GLOBALS['meta']['charset']);
	Header("Content-Disposition: attachment; filename=$filename.$extension");
	Header("Content-Length: ".strlen($out));
	echo $out;
}

?>