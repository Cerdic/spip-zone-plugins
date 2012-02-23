<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/abstract_sql');
include_spip('inc/charsets');
include_spip('inc/lang');
include_spip('inc/headers');
include_spip('public/assembler');
include_spip('balise/formulaire_adherent');

// Balise independante du contexte ici
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE ($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION_ACTIVITE', array());
}

	//function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_stat($args, $filtres) {
		// Si le moteur n'est pas active, pas de balise
		//if ($GLOBALS['meta']["activer_moteur"] != "oui")
			//return '';

		// filtres[0] doit etre un script (a revoir)
		//else
		  //return array($filtres[0], $args[0]);
	//}

// Balise de traitement des donn�es du formulaire
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_dyn() {

		//On recupere les champs
	$id_evenement = intval(_request('id_evenement'));
	$id_adherent = intval(_request('id_adherent'));
	$nom = _request('nom');
	$membres = _request('membres');
	$non_membres = _request('non_membres');
	$inscrits = intval(_request('inscrits'));
	$montant = association_recupere_montant(_request('montant'));
	$commentaire = _request('commentaire');
	$bouton = _request('bouton');
	if ($bouton=='Confirmer'){
		//enregistrement dans la table
		$n = sql_insertq('spip_asso_activites', array(
			'id_evenement' => $id_evenement,
			'id_adherent' => $id_adherent,
			'nom' => $nom,
			'membres' => $membres,
			'non_membres' => $non_membres,
			'inscrits' => $inscrits,
			'montant' => $montant,
			'commentaire' => $commentaire,
			'date' => 'NOW()',
		));
		spip_log($n ? "enregistre activite $n" : "actvite non inseree",'associaspip');
		// envoi des emails...
		$data = sql_fetsel('*', 'spip_evenements', "id_evenement=$id_evenement " );
		$activite = $data['titre'];
		$date = $data['date_debut'];
		$lieu = $data['lieu'];
		$expediteur = $GLOBALS['association_metas']['nom'].' <'.$GLOBALS['association_metas']['email'].'>';
		$sujet = _T('asso:activite_message_sujet',array('nomasso'=>$GLOBALS['association_metas']['nom']));
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

		// ...au webmaster
		$message = _T('asso:activite_message_webmaster',array(
			'nom' => $nom,
			'activite' => $activite,
			'inscrits' => $inscrits,
			'commentaire' => $commentaire,
		));
		$envoyer_mail($GLOBALS['association_metas']['email'], $sujet, $message, $expediteur);

		// ...au demandeur
		$email = $GLOBALS['visiteur_session']['email'];
		if ($email) {
			$message = _T('asso:activite_message_confirmation_inscription', array(
				'activite' => $activite,
				'date' => association_datefr($date),
				'lieu' => $lieu,
				'nom' => $nom,
				'membres' => $membres,
				'non_membres' => $non_membres,
				'inscrits' => $inscrits,
				'montant' => $montant,
				'nomasso' => $GLOBALS['association_metas']['nom'],
			));
			$envoyer_mail($email, $sujet, $message, $expediteur);
		}
	} else {
		if ($bouton=='Soumettre'){
			//On controle les donnees du formulaire
			$bouton = 'Confirmer';	 // si pas d'erreur

			//email invalide
			if (!email_valide($email)){
				$erreur_email = 'Adresse courriel invalide !';
				$bouton = 'Soumettre';
			}
			//donnees manquantes
 			if ( empty($nom) ){
				$erreur_nom = 'Nom et pr&eacute;nom manquants !';
				$bouton = 'Soumettre';
			}
			if ( empty($inscrits) ){
				$erreur_inscrits = 'Nombre d\'inscrits manquant !';
				$bouton = 'Soumettre';
			}

			//on retourne les infos a un formulaire de previsualisation
			return inclure_balise_dynamique(
				array(
					'formulaires/formulaire_inscription_activite_previsu',0,
					array(
						'id_evenement' => $id_evenement,
						'nom' => $nom,
						'id_adherent' => $id_adherent,
						'membres' => $membres,
						'non_membres' => $non_membres,
						'inscrits' => $inscrits,
						'montant' => $montant,
						'commentaire' => $commentaire,
						'bouton' => $bouton,
						'erreur_nom' => $erreur_nom,
						'erreur_inscrits' => $erreur_inscrits,
						'erreur_montant' => $erreur_montant,
					)
				),
				false
			);
		}
	}

	//On retourne au formulaire d'inscription
	return array (
		'formulaires/formulaire_inscription_activite',0,
		array (
			'id_evenement'=> $id_evenement,
			'nom'		=> $nom,
			'id_adherent'=> $id_adherent,
			'membres'=> $membres,
			'non_membres'=> $non_membres,
			'inscrits'	=> $inscrits,
			'montant' => $montant,
			'commentaire'=> $commentaire
			)
		);

}

?>