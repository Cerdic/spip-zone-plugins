<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function sympatic_pre_edition($flux){
	if (($flux['args']['table'] == 'spip_auteurs') and ($flux['args']['action'] == 'modifier')) {
		$id_auteur = $flux['args']['id_objet'];
		// s'il y a changement de mail pour un auteur
		if (($email_nouveau=$flux['data']['email'])
			and $email_nouveau != ($email_ancien=sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur)))
		){
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
	if (($flux['args']['table'] == 'spip_auteurs') and ($flux['args']['action'] == 'instituer')) {
		if ($flux['data']['statut'] == '5poubelle') {
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

function sympatic_affiche_auteurs_interventions($flux){
	if ($id_auteur = $flux['args']['id_auteur'] and autoriser('modifier', 'auteur', $id_auteur)) {
		$flux['data'] .= recuperer_fond('prive/inclure/sympatic_auteur', array('id_auteur' => $id_auteur));
	}
	return $flux;
}
