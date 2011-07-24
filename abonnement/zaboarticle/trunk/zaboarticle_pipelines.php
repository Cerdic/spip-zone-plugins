<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// abonnement payant gere par panier / commande etc
// espace public
// ˆ la validation du paiement de la commande inserer l'abonnement ˆ l'article 
function zaboarticle_post_insertion($flux){
	
	if (
		$flux['args']['table'] == 'spip_commandes'
		and ($id_commande = intval($flux['args']['id_objet'])) > 0
		and $flux['data']['statut'] == 'paye'
	){
	// On recupere le contenu des articles dans les details de la commande
		$commande_abos = sql_allfetsel(
		'*',
		'spip_commandes_details',
		'id_commande = '.$id_commande." AND objet='article'"
		);
	// On recupere le id_auteur (unique) du flux data
	$id_auteur=$flux['args']['id_auteur'];
	if (_DEBUG_ABONNEMENT) spip_log('zaboarticle_post_insertion id_auteur= '.$id_auteur,'abonnement');


	// Pour chaque commande d'article
		foreach($commande_abos as $abo){
		$id_article = $abo['id_objet'];
		if (_DEBUG_ABONNEMENT) spip_log('zaboarticle_post_insertion > id_article='.$id_article.'et auteur= '.$id_auteur,'abonnement');
		$date = date('Y-m-d H:i:s');
		$duree = 3; //valable 3 jours
		$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$duree,date('Y')));

				
		// attention aux doublons, on verifie 
		if ((!$id = sql_getfetsel('id_auteur','spip_contacts_abonnements','id_auteur='.$id_auteur.' and objet=article and id_article='.sql_quote($id_article).' and validite > NOW()')))
				sql_insertq("spip_contacts_abonnements",
					array(
					"id_auteur"=>$id_auteur,
					"objet"=>'article',
					"id_objet"=>$id_article,
					'date' => $date,
					'validite'=>$validite,
					'statut_abonnement'=>'paye',
					'prix'=>$abo['prix_unitaire_ht'])
					);
		}

	}

return $flux;

}

//espace prive
//affiche les articles auxquels l'auteur est abonne (sur auteur_infos)
function zaboarticle_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
		$legender_auteur_supp = recuperer_fond('prive/zaborubrique_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$legender_auteur_supp .= recuperer_fond('prive/zaboarticle_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= $legender_auteur_supp;
	}
	return $flux;
}

//affiche liste des articles pour s'abonner dans le formulaire d'un auteur (un peu lourd!)
function zaboarticle_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		$zabo = recuperer_fond('prive/zaborubrique_fiche_modif',array('id_auteur'=>$flux['args']['id']));
		$zabo .= recuperer_fond('prive/zaboarticle_fiche_modif',array('id_auteur'=>$flux['args']['id']));

		$flux['data'] = preg_replace('%(<!--editer_abonnement-->)%is', '$1'."\n".$zabo, $flux['data']);
	}
	return $flux;
}

?>
