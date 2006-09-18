<?php

include_spip('inc/checklink');
include_spip('public/assembler');

function exec_liens_tous(){
  include_spip("inc/presentation");

  checklink_verifier_base();
	
	debut_page(_L("Tous les liens"), "documents", "liens");
	debut_gauche();
	//debut_boite_info();
	//echo _L("Cliquez sur un formulaire pour le modifier ou le visualiser avant suppression.");
	//fin_boite_info();
	
	debut_droite();
	if (_request('verifier')!==NULL){
		include_spip('inc/checklink_verification');
		if (is_numeric($id_lien=_request('verifier'))) {
			$row = spip_fetch_array(spip_query("SELECT url,date_verif,statut FROM spip_liens WHERE id_lien=$id_lien"));
			if ($row)
				checklink_verifie_lien($row["url"],'', in_array($row['statut'],array('ind','oui'))?'sus':'off');
		}
		else {
			cron_checklink_verification(1);
		}
	}
	if (_request('raz')!==NULL){
		checklink_reconstruit_table();
	}
	
	$contexte = array();
	if (_request('statut')) $contexte['statut'] = _request('statut');
	echo recuperer_fond("exec/table_liens",$contexte);
	
	echo "<br />\n";
	

	
	fin_page();
}

?>