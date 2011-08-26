<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes                                       #
#                                                                                                      #
#  Définition de la balise #FORMULAIRE_ETIQUETTES                                                      #
#------------------------------------------------------------------------------------------------------#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_etiquettes_charger_dist($groupe, $id_groupe, $name, $aide_nuage, $aide_autocompletion, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login, $uniquement_champ, $explication, $squelette_nuage,$label){
	
	$valeurs = compact("groupe", "name", "aide_nuage", "aide_autocompletion", "aide_liste", "remplacer", "type_objet", "cle_objet", "id_objet", "proposer_login", "uniquement_champ", "explication", "squelette_nuage","label");
	
	// Les paramètres ont tous déjà été testés
	// Maintenant on teste si la personne a le droit d'ajouter des mots-clés au groupe choisi
	// ET si elle a le droit de modifier l'objet choisi
	// On ne va pas plus loin si pas d'autorisation
	include_spip('inc/autoriser');
	if(!$uniquement_champ
		and (
			(
				($remplacer and !autoriser('remplaceretiquettes', $type_objet, $id_objet))
				or !autoriser('ajouteretiquettes', $type_objet, $id_objet)
			)
			or (
				!$aide_liste
				and !autoriser('modifier', 'groupemots', $id_groupe)
			)
		)
	){
		$valeurs['erreur_autorisation'] = true;
		$valeurs['proposer_login'] &= true;
		$valeurs['message_erreur'] = _T('etiquettes:pas_le_droit');
		return $valeurs;
	}
	else{
		$valeurs['erreur_autorisation'] = false;
		$valeurs['proposer_login'] &= false;
		
		// Pour l'ajout c'est vide
		$etiquettes = "";
		
		// Mais si on modifie, le champ est rempli avec les tags liés à l'objet
		if ($remplacer and $type_objet and $id_objet){
		
			$reponse = sql_select(
				'mots.titre',
				array('mots' => 'spip_mots', 'liaison' => 'spip_mots_'.preg_replace('/^spip_/i', '', table_objet_sql($type_objet))),
				array(
					array('=', 'mots.type', sql_quote($groupe)),
					array('=', 'liaison.'.$cle_objet, $id_objet),
					array('=', 'mots.id_mot', 'liaison.id_mot')
				),
				"",
				"mots.titre"
			);
			while ($mot = sql_fetch($reponse)){
			
				// S'il y a des espaces ou virgules on entoure de guillemets
				if (strcspn($mot['titre'], ' ,"') != strlen($mot['titre']))
					$etiquettes .= ' "'.entites_html($mot['titre']).'"';
				// Sinon on renvoie tel quel
				else
					$etiquettes .= " ".entites_html($mot['titre']);
				// Enfin en vire les éventuels espaces en trop
				$etiquettes = trim($etiquettes);
			
			}
		
		}
		
		// Si c'est un champ inclu dans un autre formulaire, il faut aller chercher à la main le POST
		if ($uniquement_champ and $_POST[$name]){
			$etiquettes = $_POST[$name];
		}
		
		$valeurs[$name] = $etiquettes;
		$valeurs['identifiant'] = etiquettes_produire_id($groupe, $type_objet, $id_objet);
		
		return $valeurs;
	}
	
}

function formulaires_etiquettes_verifier_dist($groupe, $id_groupe, $name, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login, $uniquement_champ, $explication, $squelette_nuage,$label){
	
	$erreurs = array();
	
	return $erreurs;
	
}

function formulaires_etiquettes_traiter_dist($groupe, $id_groupe, $name, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login, $uniquement_champ, $explication, $squelette_nuage,$label){

	$identifiant = etiquettes_produire_id($groupe, $type_objet, $id_objet);
	$id_formulaire = "valider_etiquettes_$identifiant";
	
	// si on vient du formulaire validé on le traite
	if (_request($id_formulaire)){
		
		// On récupère les tags
		$etiquettes = trim(_request($name));
		// On utilise la tag-machine avec les millions de paramètres
		include_spip('inc/tag-machine');
		ajouter_liste_mots(
			$etiquettes,
			$id_objet,
			$groupe,
			preg_replace('/^spip_/i', '', table_objet_sql($type_objet)),
			$cle_objet,
			$remplacer
		);
		
		// Si on a modifié, on renvoie la liste telle quelle, ça évite une requête pour rien
		if ($remplacer)
			$etiquettes = entites_html($etiquettes);
		// Sinon c'est un formulaire d'ajout donc il apparaît toujours vide
		else $etiquettes = "";
		
		// On dit qu'il faut recalculer tout vu qu'on a changé
		include_spip ("inc/invalideur");
		suivre_invalideur("1");
		
		// Relance la page
		include_spip('inc/headers');
		redirige_formulaire(self());
		
	}

}

?>
