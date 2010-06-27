<?php

function rubrique_a_linscription_formulaire_traiter($flux){
	if ($flux['args']['form']=='inscription' and $flux['args']['args'][0]=='0minirezo'){
		
		$mail = _request('mail_inscription');
		$nom_inscription = str_replace('@',' (chez) ',_request('nom_inscription'));
		include_spip('base/abstract_sql');
		$id_auteur = sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($mail));
		$id_rubrique = sql_insertq("spip_rubriques", array( 'titre'=> _T('Rubrique de '.$nom_inscription), 'id_secteur'=> 0));
		sql_update("spip_rubriques",array("id_secteur"=>$id_rubrique), "id_rubrique=".$id_rubrique);
		sql_insertq('spip_auteurs_rubriques', array(
		'id_auteur' => $id_auteur,
		'id_rubrique' => $id_rubrique));
		spip_log('Création de la rubrique pour l\'auteur '.$nom_inscription.' ( '.$mail.' )','rubrique_a_linscription');
	}
	return $flux;	
}