<?php
/**
 * Plugin mailsuscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip("action/editer_objet");
include_spip('inc/mailsuscribers');
include_spip('inc/config');
include_spip('inc/autoriser');

/**
 * Inscrit un suscriber par son email
 * si le suscriber existe deja, on met a jour les informations (nom, listes, lang)
 * l'ajout d'une inscription a une liste est cumulatif : si on appelle plusieurs fois la fonction avec le meme email
 * et plusieurs listes differentes, l'inscrit sera sur chaque liste
 * Pour retirer une liste il faut desinscrire
 *
 * Quand aucune liste n'est indiquee :
 *   si l'email n'est inscrit a rien, on l'inscrit a la liste generale 'newsletter'
 *   si l'email est deja inscrit, on ne change pas ses inscriptions, mais on modifie ses informations (nom, lang)
 *
 * @param $email
 *   champ obligatoire
 * @param array $options
 *   nom : string
 *   listes : array (si non fourni, inscrit a la liste generale 'newsletter')
 *   lang : string
 *   force : bool permet de forcer une inscription sans doubleoptin (passe direct en valide)
 * @return bool
 *   true si inscrit comme demande, false sinon
 */
function newsletter_suscribe_dist($email,$options = array()){

	$set = array();
	foreach (array('lang', 'nom') as $k){
		if (isset($options[$k]))
			$set[$k] = $options[$k];
	}
	if (isset($options['listes'])
	  AND is_array($options['listes'])){
		$set['listes'] = array_map('mailsuscribers_normaliser_nom_liste',$options['listes']);
		$set['listes'] = implode(',',$set['listes']);
	}

	// chercher si un tel email est deja en base
	$row = sql_fetsel('*','spip_mailsuscribers','email='.sql_quote($email));

	// Si c'est une creation d'inscrit
	if (!$row){
		// on utilise pas objet_inserer car email unique et on ne veut pas passer par etape insertion email='' qui peut echouer
		// en cas de doublon
		$set['email'] = $email;
		if (!isset($set['lang']))
			$set['lang'] = $GLOBALS['meta']['langue_site'];
		if (!isset($set['listes']))
			$set['listes'] = mailsuscribers_normaliser_nom_liste();
		// statut et date par defaut
		$set['statut'] = 'prepa';
		$set['date'] = date('Y-m-d H:i:s');

		// Envoyer aux plugins
		$champs = pipeline('pre_insertion',
			array(
				'args' => array(
					'table' => 'spip_mailsuscribers',
				),
				'data' => $set
			)
		);

		if ($id = sql_insertq('spip_mailsuscribers', $set)){

			pipeline('post_insertion',
				array(
					'args' => array(
						'table' => 'spip_mailsuscribers',
						'id_objet' => $id,
					),
					'data' => $champs
				)
			);

			$row = sql_fetsel('*','spip_mailsuscribers','id_mailsuscriber='.intval($id));
			$set = array();
		}

		else {
			spip_log("Impossible de creer un mailsuscriber : ".var_export($set,true),"mailsuscribers."._LOG_ERREUR);
			return false;
		}
	}
	else {
		if (!$row['listes'] AND !isset($set['listes']))
			$set['listes'] = mailsuscribers_normaliser_nom_liste();
		// si c'est un inscrit existant faire les mises a jour des listes si besoins
		if (isset($set['listes'])){
			$set['listes'] = array_merge(explode(',',$row['listes']),explode(',',$set['listes']));
			$set['listes'] = array_map('trim',$set['listes']);
			$set['listes'] = array_unique($set['listes']);
			$set['listes'] = array_filter($set['listes']);
			$set['listes'] = implode(",",$set['listes']);
			if (!$set['listes'])
				$set['listes'] = mailsuscribers_normaliser_nom_liste();
		}
	}

	// si pas deja valide
	if ($row['statut']!=='valide'){
		// changer le statut en prop (doubleoptin) ou valide (simpleoptin)
		if (
			(isset($options['force']) AND $options['force'])
			OR !lire_config('mailsuscribers/double_optin',0)){

			$set['statut'] = 'valide';
		}
		else {
			$set['statut'] = 'prop';
		}
	}
	if (count($set)){
		autoriser_exception("modifier","mailsuscriber",$row['id_mailsuscriber']);
		autoriser_exception("instituer","mailsuscriber",$row['id_mailsuscriber']);
		objet_modifier("mailsuscriber",$row['id_mailsuscriber'],$set);
		autoriser_exception("modifier","mailsuscriber",$row['id_mailsuscriber'],false);
		autoriser_exception("instituer","mailsuscriber",$row['id_mailsuscriber'],false);
	}

	return true;
}