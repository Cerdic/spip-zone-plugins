<?php
/**
 * Utilisations de pipelines par Formulaires de participation
 *
 * @plugin     Formulaires de participation
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableparticipation\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Traiter les formulaires de participation
 * @param array $flux
 * @return array
 */
function formidableparticipation_traiter_formidableparticipation($flux){
	//au moins une reponse et un email
	if ($flux['args']['choix_participation'] && $flux['args']['email']){
		$id_evenement = $flux['args']['id_evenement'];
		$reponse = $flux['args']['choix_participation'];
		$email = $flux['args']['email'];
		$id_auteur = $flux['args']['id_auteur'];
		$nom = $flux['args']['nom'];
		$prenom = $flux['args']['prenom'];
		$id_formulaires_reponse = $flux['args']['id_formulaires_reponse'];
		if($flux['args']['organisme']) $organisme = '('.$flux['args']['organisme'].')';
		$nom = "$prenom $nom $organisme";

		$champs = array(
			'id_auteur'=>$id_auteur,
			'nom'=>$nom,
			'email'=>$email,
			'reponse'=>$reponse,
			'id_evenement'=>$id_evenement,
			'date'=>date('Y-m-d H:i:s'),
			'id_formulaires_reponse' => $id_formulaires_reponse
		);

		// si evenement, on insere le participant et ses données
		// et on laisse le traitement du nombre de places à la charge du webmestre et du squelette evenements
		if(isset($id_evenement)){
				//on ne logue pas l'auteur, si l'email sur le même id_evenement existe, mettre à jour, sauf si on demande explictement de permettre à un même email de s'inscrire plusieurs fois
				if (sql_fetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement)." AND email=".sql_quote($email)) and !$flux['args']['autoriser_email_multiple']){
					sql_updateq("spip_evenements_participants",$champs,'id_evenement='.intval($id_evenement).' AND email='.sql_quote($email));
				}
				else{
					sql_insertq("spip_evenements_participants", $champs);
				}
		}

		spip_log("pipeline evenement $id_evenement pour $email et id_auteur=$id_auteur et id_formulaires_reponse=$id_formulaires_reponse et reponse=$reponse","formidable_participation");
	}

   return $flux;
}

/**
 * Lorsqu'une réponse est passée en refusée ou poubelle, supprimer l'inscription correspondant.
 * Réciproquement, lorsqu'une réponse est passée en validée, créer une inscription
 * @param array $flux
 * @return array $flux
 **/
function formidableparticipation_post_edition($flux) {
	if (isset($flux['args']['table'])
		and $flux['args']['table'] == 'spip_formulaires_reponses'
		and $flux['args']['action'] == 'instituer'
		and $id_formulaires_reponse = $flux['args']['id_objet']
		and isset($flux['data']['statut'])
		and $statut = $flux['data']['statut']
		and $statut_ancien = $flux['args']['statut_ancien']
		and $statut != $statut_ancien
	) {
		if ($statut == 'publie') {
			$champs = array('reponse' => 'oui');
		} else {
			$champs = array('reponse' => 'non');
		}
		sql_updateq("spip_evenements_participants",$champs,'id_formulaires_reponse='.intval($id_formulaires_reponse));
	}
	return $flux;
}

