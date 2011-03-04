<?php

function sympatic_pre_edition($flux){

	if (($flux['args']['table']=='spip_auteurs') AND ($flux['args']['action']=='modifier')){
		$id_auteur = $flux['args']['id_objet'];
		// s'il y a changement de mail pour un auteur
		if (($email_nouveau=$flux['data']['email'])!=($email_ancien=sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur)))){
			spip_log("changement email $email_ancien vers $email_nouveau auteur $id_auteur","sympatic");
			include_spip('inc/sympatic');
			$result = sql_select('id_liste','spip_sympatic_abonnes','id_auteur='.intval($id_auteur));
			while ($row = sql_fetch($result)) {
				sympatic_traiter_abonnement($row['id_liste'],$id_auteur,'desabonner');
				sympatic_traiter_abonnement($row['id_liste'],$id_auteur,'abonner',$email_nouveau);
			}
		}
	}

	return $flux;
}

function sympatic_post_edition($flux){

	if (($flux['args']['table']=='spip_auteurs') AND ($flux['args']['action']=='instituer')){
		if ($flux['data']['statut'] == '5poubelle'){
			$id_auteur = $flux['args']['id_objet'];
			include_spip('inc/sympatic');
			$result = sql_select('id_liste','spip_sympatic_abonnes','id_auteur='.intval($id_auteur));
			while ($row = sql_fetch($result)) {
				sympatic_traiter_abonnement($row['id_liste'],$id_auteur,'desabonner');
			}
		}
	}

	return $flux;
}

?>
