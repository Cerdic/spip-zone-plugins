<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Juste pour l'appel du pipeline
function formidable_autoriser(){}

// Seuls les admins peuvent éditer les formulaires
function autoriser_formulaire_editer_dist($faire, $type, $id, $qui, $options){
	if (isset($qui['statut']) and $qui['statut'] <= '0minirezo' and !$qui['restreint']) return true;
	else return false;
}

// Seuls les admins peuvent éditer les formulaires
function autoriser_formulaires_bouton_dist($faire, $type, $id, $qui, $options){
	if (isset($qui['statut']) and $qui['statut'] <= '0minirezo' and !$qui['restreint']) return true;
	else return false;
}

// On peut répondre à un formulaire si :
// - c'est un formulaire classique
// - on enregistre et que multiple = oui
// - on enregistre et que multiple = non et que la personne n'a pas répondu encore
// - on enregistre et que multiple = non et que modifiable = oui
function autoriser_formulaire_repondre_dist($faire, $type, $id, $qui, $options){
	// On regarde si il y a déjà le formulaire dans les options
	if (isset($options['formulaire']))
		$formulaire = $options['formulaire'];
	// Sinon on va le chercher
	else{
		$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id);
	}
	
	$traitements = unserialize($formulaire['traitements']);
	
	// S'il n'y a pas d'enregistrement, c'est forcément bon
	if (!($options = $traitements['enregistrement']))
		return true;
	// Sinon faut voir les options
	else{
		// Si multiple = oui c'est bon
		if ($options['multiple'])
			return true;
		else{
			// Si c'est modifiable, c'est bon
			if ($options['modifiable'])
				return true;
			else{
				include_spip('inc/formidable');
				// Si la personne n'a jamais répondu, c'est bon
				if (!formidable_verifier_reponse_formulaire($id))
					return true;
				else
					return false;
			}
		}
	}
}

// On peut modérer une réponse si on est admin
function autoriser_formulaires_reponse_instituer_dist($faire, $type, $id, $qui, $options){
	if (isset($qui['statut']) and $qui['statut'] <= '0minirezo' and !$qui['restreint']) return true;
	else return false;
}

// Au moins rédacteur pour voir les résultats
function autoriser_formulaires_reponse_voir_dist($faire, $type, $id, $qui, $options){
	if (isset($qui['statut']) and $qui['statut'] <= '1comite') return true;
	else return false;
}

?>
