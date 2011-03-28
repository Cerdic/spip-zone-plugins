<?php

function sympatic_pre_edition($flux){

	if (($flux['args']['table']=='spip_auteurs') AND ($flux['args']['action']=='modifier')){
		$id_auteur = $flux['args']['id_objet'];
		// s'il y a changement de mail pour un auteur
		if (($email_nouveau=$flux['data']['email'])
			AND $email_nouveau!=($email_ancien=sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur)))){
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

function sympatic_affiche_milieu($flux){
	
	if (($flux['args']['exec'] == 'auteur_infos') 
		AND (intval($id_auteur = $flux['args']['id_auteur']))
		AND (autoriser('modifier','auteur',$id_auteur,null))){
		$contexte['id_auteur'] = $id_auteur;
		$flux['data'] .= "<div id='pave_sympatic'>";
		$bouton = bouton_block_depliable(_T('sympatic:icone_sympatic_tous'), false, "pave_sympatic_depliable");
		$flux['data'] .= debut_cadre_enfonce(find_in_path('images/sympatic-24.png'), true, "", $bouton);
		$flux['data'] .= recuperer_fond('prive/contenu/sympatic_auteur', $contexte);
		$flux['data'] .= fin_cadre_enfonce(true);
		$flux['data'] .= "</div>";
	}

	return $flux;
}

?>
