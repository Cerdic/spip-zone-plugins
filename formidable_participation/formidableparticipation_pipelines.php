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
		$reponse = $flux['args']['choix_participation'];
		$email = $flux['args']['email'];
		$id_auteur = $flux['args']['id_auteur'];
		$nom = $flux['args']['nom'];
		$prenom = $flux['args']['prenom'];
		if($flux['args']['organisme']) $organisme = '('.$flux['args']['organisme'].')';
		$nom = "$prenom $nom $organisme";
		
		// si evenement, on insere le participant
		// et on laisse le traitement du nombre de places à la charge du webmestre et du squelette evenements
		if($id_evenement = $flux['args']['id_evenement']){
			
			    if($id_auteur>0){
				if (sql_fetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement).' AND id_auteur='.intval($id_auteur)))
				    sql_updateq('spip_evenements_participants',array('reponse'=>$reponse,'date'=>'NOW()'),'id_evenement='.intval($id_evenement).' AND id_auteur='.intval($id_auteur));
			    	else
			    	sql_insertq('spip_evenements_participants',array('id_auteur'=>$id_auteur,'id_evenement'=>$id_evenement,'nom'=>$nom,'email'=>$email,'reponse'=>$reponse,'date'=>'NOW()'));

			    } else {
				    //sans auteur logé si l'email existe, le mettre à jour
				    if (sql_fetsel('reponse','spip_evenements_participants','id_evenement='.intval($id_evenement)." AND email=".sql_quote($email)))
				    sql_updateq('spip_evenements_participants',array('reponse'=>$reponse,'date'=>'NOW()'),'id_evenement='.intval($id_evenement).' AND email='.sql_quote($email));
				else
				sql_insertq('spip_evenements_participants',array('id_evenement'=>$id_evenement,'nom'=>$nom,'email'=>$email,'reponse'=>$reponse,'date'=>'NOW()'));
			    }
			    
		}
		
		spip_log("pipeline evenement $id_evenement pour $email et id_auteur=$id_auteur reponse=$reponse","formidable_participation");
	}

   return $flux;
}

?>