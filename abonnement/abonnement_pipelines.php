<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function abonnement_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
		$legender_auteur_supp = recuperer_fond('prive/abonnement_fiche',array('id_auteur'=>$flux['args']['id_auteur']));
		$flux['data'] .= $legender_auteur_supp;
	}
	return $flux;
}


/**
 *
 * Insertion dans le pipeline editer_contenu_objet
 * Ajoute les champs abonnement sur le formulaire CVT editer_auteur
 *
 * @return array Le $flux complété
 * @param array $flux
 */
function abonnement_editer_contenu_objet($flux){

	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		/**
		 *
		 * Insertion des champs dans le formulaire aprs le textarea PGP
		 *
		 */

		$abonnement = recuperer_fond('prive/abonnement_fiche_modif',array('id_auteur'=>$flux['args']['id']));

		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$abonnement, $flux['data']);
	}
	return $flux;
}


/**
 *
 * Insertion dans le pipeline post_edition
 * ajouter les champs abonnement soumis lors de la soumission du formulaire CVT editer_auteur
 *
 * @return
 * @param object $flux
 */
function abonnement_post_edition($flux){
	// va savoir pourquoi ce truc est appelé trois fois quand on valide le form...
	if ($flux['args']['table']=='spip_auteurs') {
		spip_log('ABONNEMENT : abonnement_post_edition','abonnement');
		$id_auteur = $flux['args']['id_objet'];
		
		$abonnements = _request('abonnements') ;
		$echeances = _request('validites') ;
				
		if ($abonnements && is_array($abonnements)) {
			foreach($abonnements as $key => $id_abonnement)	{
			  if($echeances[$key])	
				if (($id = sql_getfetsel('id_auteur','spip_auteurs_elargis_abonnements','id_auteur='.$id_auteur.' and id_abonnement='.sql_quote($id_abonnement).' and validite > NOW()'))){
				
				sql_updateq("spip_auteurs_elargis_abonnements",array('validite'=>$echeances[$key]),'id_auteur='.$id_auteur.' and id_abonnement='.sql_quote($id_abonnement).' and validite > NOW()');
				
				}
				
				if($id_abonnement!='non' and !$echeances[$key]){
				
					// abonnement non trouve ?
					$abonnement = sql_fetsel('*', 'spip_abonnements', 'id_abonnement = ' . $id_abonnement);
					if (!$abonnement) {
						spip_log("abonnement $id_abonnement inexistant");
						die("abonnement $id_abonnement inexistant");
					}
					
					$date = date('Y-m-d H:i:s');
					
					// jour
					if ($abonnement['periode'] == 'jours') {
						$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n'),date('j')+$abonnement['duree'],date('Y')));
					}
					// ou mois
					else {
						$validite = date('Y-m-d H:i:s', mktime(date('H'),date('i'),date('s'),date('n')+$abonnement['duree'],date('j'),date('Y')));
					}
					
					// attention au triple appel, on verifie 
					if ((!$id = sql_getfetsel('id_auteur','spip_auteurs_elargis_abonnements','id_auteur='.$id_auteur.' and id_abonnement='.sql_quote($id_abonnement).' and validite > NOW()')))
						sql_insertq("spip_auteurs_elargis_abonnements",array('id_auteur'=> $id_auteur,'id_abonnement'=> $id_abonnement,'date'=>$date,'validite'=>$validite,'statut_paiement'=>'ok','montant'=>$abonnement['montant']));
				
				}
		
		
			}	
		}
		
		// Notifications, gestion des revisions, reindexation...
		pipeline('post_edition',
			array(
				'args' => array(
					'table' => 'spip_auteurs_elargis',
					'id_objet' => $id_auteur
				),
				'data' => $auteur
			)
		);
	}

	return $flux;
}

function abonnement_i2_cfg_form($flux) {
    $flux .= recuperer_fond('fonds/inscription2_abonnement');
	return $flux;
}

function abonnement_i2_form_debut($flux) {
	if (lire_config('abonnement/proposer_paiement')) {
		$contexte = array("abonnement" => $flux['args']['abonnement'],"hash" => $flux['args']['hash']);
		$flux['data'] .= recuperer_fond('formulaires/liste_abonnements',$contexte);
	}
	return $flux;
}

function abonnement_i2_charger_formulaire($flux) {
	if (lire_config('abonnement/proposer_paiement')) {
		// valeur par defaut
		$flux['data']['abonnement'] = '1' ;
		include_spip('inc/acces');
		$hash = creer_uniqid();	
		$flux['data']['hash'] = $hash ;
		$flux['data']['type_commande'] = "abonnement" ;	
	}
	return $flux;
}

function abonnement_i2_verifier_formulaire($flux) {
	//if (lire_config('abonnement/proposer_paiement')) {
		// rien a faire, mais sait on jamais ! un jour peut etre !
	//}
	return $flux;
}

// inscrire l'abonnement dans la base, statut "a confirmer"
// et afficher un formulaire de paiement (uniquement si la config le permet)
	
function abonnement_i2_traiter_formulaire($flux) {	
	if (lire_config('abonnement/proposer_paiement')) {
		if($id_abonnement = intval(_request('abonnement'))){	
			$id_auteur = $flux['args']['id_auteur'] ;
			$hash = _request('hash');
			$row = sql_fetsel(array('montant'), 'spip_abonnements', 'id_abonnement='.sql_quote($id_abonnement));
			$montant = $row['montant'];
			
			// Ne pas mettre deux fois le meme abo ici. (refresh par ex ou retour)
			// avec le meme hash
				sql_delete("spip_auteurs_elargis_abonnements","hash=".sql_quote($hash)." and statut_paiement='a_confirmer'");
				sql_insertq('spip_auteurs_elargis_abonnements', array(
					'id_auteur' => $id_auteur,
					'id_abonnement' => $id_abonnement,
					'date' => date("Y-m-d H:i:s"),
					'hash'=>$hash,
					'montant'=>$montant,
					'statut_paiement' => 'a_confirmer')
				);
		}
		$flux['data']['ne_pas_confirmer_par_mail'] = true ;
		$flux['data']['message_ok'] = " " ;
	}
	
	return $flux;
}

function abonnement_i2_confirmation($flux) {
	// afficher un formulaire de paiement pour l'utilisateur (uniquement si la config le permet)
	if (lire_config('abonnement/proposer_paiement')) {
		$env = $flux['args'];
		$row = sql_fetsel(array('id_auteur'), 'spip_auteurs', 'email='.sql_quote($env['email']));
		$env['id_auteur'] = $row['id_auteur'] ;
		$flux['data'] .= recuperer_fond('formulaires/abonnement_paiement',$env);
	
	// on pose une session permettant d'identifier l'abonne 
	// si desfois il lui prenait l'idée de faire "retour" avec son navigateur
	// on prend la date pour permettre la manip que pendant quelques minutes
	include_spip("inc/inscription2_session");
	i2_poser_session($row['id_auteur']);
	
	}
	return $flux;
}

//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement'] = 60*60*24 ;
	return $taches_generales;
}

?>
