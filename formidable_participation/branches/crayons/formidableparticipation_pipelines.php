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

if (!defined('_FORMIDABLE_PARTICIPATION_ACTUALISE_MAJ')) {
	define ('_FORMIDABLE_PARTICIPATION_ACTUALISE_MAJ', false);
}
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
		$nb_inscriptions = $flux['args']['nb_inscriptions'];
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

		// Si c'est une mise à jour d'une réponse, on supprime TOUTES
		// les anciennes inscriptions avant de créer les nouvelles, mais on ne le fait qu'une fois
		static $suppression_faite;
		if (!$suppression_faite) {
			sql_delete('spip_evenements_participants', "id_formulaires_reponse=$id_formulaires_reponse");
			$suppression_faite = true;
		}

		// si evenement, on insere le participant et ses données
		// et on laisse le traitement du nombre de places à la charge du webmestre et du squelette evenements
		if(isset($id_evenement)){
				$i = 0;
				while ($i < $nb_inscriptions) {
					$i++;
					//On ne logue pas l'auteur. Si l'email sur le même id_evenement existe, mettre à jour, sauf si on demande explictement de permettre à un même email de s'inscrire plusieurs fois
					if (sql_fetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement)." AND email=".sql_quote($email)) and !$flux['args']['autoriser_email_multiple']){
						sql_updateq("spip_evenements_participants",$champs,'id_evenement='.intval($id_evenement).' AND email='.sql_quote($email));
					}
					else{
						sql_insertq("spip_evenements_participants", $champs);
					}
				}
		   if (_FORMIDABLE_PARTICIPATION_ACTUALISE_MAJ) {
			   sql_update('spip_evenements',array('maj'=>'NOW()'),"id_evenement=$id_evenement");
		   }
		}
		spip_log("pipeline evenement $id_evenement pour $email et id_auteur=$id_auteur et id_formulaires_reponse=$id_formulaires_reponse et reponse=$reponse ($nb_inscriptions fois)","formidable_participation");
	}

   return $flux;
}

/**
 * Lorsqu'une réponse est passée en refusée ou poubelle, changer le statut de l'inscription correspondant.
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
		$where = 'id_formulaires_reponse='.intval($id_formulaires_reponse);
		sql_updateq("spip_evenements_participants",$champs,$where);
		if (_FORMIDABLE_PARTICIPATION_ACTUALISE_MAJ) {
			$id_evenement = sql_getfetsel('id_evenement','spip_evenements_participants',$where);
			sql_update('spip_evenements',array('maj'=>'NOW()'),"id_evenement=$id_evenement");
		}
	}
	return $flux;
}

/**
 * Lorsqu'on édite un champ avec crayons, actualiser les liens de réponse
 * @param array $flux
 * @return $flux
**/
function formidableparticipation_crayons_post_store($flux) {
	if ($flux['args']['type'] !== 'formulaires_reponses_champ') {
		return $flux;
	}
	$id = $flux['args']['id'];
	include_spip('inc/saisies');
	// Trouver la saisie
	$data = sql_fetsel('spip_formulaires.id_formulaire, spip_formulaires_reponses.id_formulaires_reponse, nom,saisies,traitements', 'spip_formulaires_reponses_champs JOIN spip_formulaires_reponses JOIN spip_formulaires', "id_formulaires_reponses_champ=$id AND spip_formulaires_reponses.id_formulaires_reponse = spip_formulaires_reponses_champs.id_formulaires_reponse AND spip_formulaires.id_formulaire = spip_formulaires_reponses.id_formulaire");
	$traitements = unserialize($data['traitements']);
	// Pas de traitement participation, on ignore la suite
	if (!isset($traitements['participation'])) {
		return $flux;
	}

	$saisies = unserialize($data['saisies']);
	$saisie = saisies_chercher($saisies, $data['nom']);
	// On est pas en saisie evenement et bien on ignore :-)
	if ($saisie['saisie'] !== 'evenements') {
		return $flux;
	}
	// Chercher la fonction de traitement standard
	$traiter = charger_fonction('participation', 'traiter');
	// Determination des arguments
	$retours = array(
		'traitements' => array(),
		'id_formulaires_reponse' => $data['id_formulaires_reponse']
	);
	$args = array(
		'formulaire'  => $data,
		'id_formulaire' => $data['id_formulaire'],
		'id_formulaires_reponse' => $data['id_formulaires_reponse'],
		'options' => $traitements['participation']);
	unset($args['formulaire']['nom']);
	$traiter($args, $retours);
	return $flux;
}

