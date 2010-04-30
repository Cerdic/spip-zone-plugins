<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/formidable');
include_spip('inc/saisies');
include_spip('base/abstract_sql');
include_spip('inc/autoriser');

function formulaires_formidable_charger($id_formulaire, $valeurs=array()){
	$contexte = array();
	
	// On peut donner soit un id soit un identifiant
	if (intval($id_formulaire) > 0)
		$where = 'id_formulaire = '.intval($id_formulaire);
	elseif (is_string($id_formulaire))
		$where = 'identifiant = '.sql_quote($id_formulaire);
	else
		return;
	
	// On cherche si le formulaire existe
	if ($formulaire = sql_fetsel('*', 'spip_formulaires', $where)){
		// Est-ce que la personne a le droit de répondre ?
		if (autoriser('repondre', 'formulaire', $formulaire['id_formulaire'], null, array('formulaire'=>$formulaire))){
			$saisies = unserialize($formulaire['saisies']);
			$traitements = unserialize($formulaire['traitements']);
			// On déclare les champs
			$contexte = array_fill_keys(saisies_lister_champs($saisies), '');
			$contexte['mechantrobot'] = '';
			// On ajoute le formulaire complet
			$contexte['_saisies'] = $saisies;
		
			$contexte['id'] = $formulaire['id_formulaire'];
			$contexte['_hidden'] = '<input type="hidden" name="id_formulaire" value="'.$contexte['id'].'"/>';
			
			// S'il y a des valeurs par défaut dans l'appel, alors on pré-remplit
			if ($valeurs and is_array($valeurs)){
				$contexte = array_merge($contexte, $valeurs);
			}
			
			// Si multiple = non mais que c'est modifiable, alors on va chercher la dernière réponse si elle existe
			if ($options = $traitements['enregistrement']
				and !$options['multiple']
				and $options['modifiable']
				and $reponses = formidable_verifier_reponse_formulaire($formulaire['id_formulaire'])
			){
				$id_formulaires_reponse = array_pop($reponses);
				// On va chercher tous les champs
				$champs = sql_allfetsel(
					'nom, valeur',
					'spip_formulaires_reponses_champs',
					'id_formulaires_reponse = '.$id_formulaires_reponse
				);
				// On remplit le contexte avec
				foreach ($champs as $champ){
					$test_array = unserialize($champ['valeur']);
					$contexte[$champ['nom']] = is_array($test_array) ? $test_array : $champ['valeur'];
				}
				// On ajoute un hidden pour dire que c'est une modif
				$contexte['_hidden'] .= "\n".'<input type="hidden" name="deja_enregistre_'.$formulaire['id_formulaire'].'" value="'.$id_formulaires_reponse.'"/>';
			}
		}
		else{
			$contexte['editable'] = false;
			$contexte['message_erreur'] = _T('formidable:traiter_enregistrement_erreur_deja_repondu');
		}
	}
	else{
		$contexte['editable'] = false;
		$contexte['message_erreur'] = _T('formidable:erreur_inexistant');
	}
	
	return $contexte;
}

function formulaires_formidable_verifier($id_formulaire, $valeurs=array()){
	$erreurs = array();
	
	// Sale bête !
	if (_request('mechantrobot') != ''){
		$erreurs['hahahaha'] = 'hahahaha';
		return $erreurs;
	}
	
	$id_formulaire = intval(_request('id_formulaire'));
	$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire);
	$saisies = unserialize($formulaire['saisies']);
	
	$erreurs = saisies_verifier($saisies);
		
	return $erreurs;
}

function formulaires_formidable_traiter($id_formulaire, $valeurs=array()){
	$retours = array();
	// Par défaut le formulaire se remet en route à la fin
	$retours['editable'] = true;
	
	$id_formulaire = intval(_request('id_formulaire'));
	$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire);
	$traitements = unserialize($formulaire['traitements']);
	
	if (is_array($traitements) and !empty($traitements)){
		foreach($traitements as $type_traitement=>$options){
			if ($appliquer_traitement = charger_fonction($type_traitement, 'traiter/', true))
				$retours = $appliquer_traitement(
					array(
						'formulaire' => $formulaire,
						'options' => $options
					),
					$retours
				);
		}
		
		// Si on a personnalisé le message de retour, c'est lui qui est affiché uniquement
		if ($formulaire['message_retour'])
			$retours['message_ok'] = _T_ou_typo($formulaire['message_retour']);
	}
	else{
		$retours['message_ok'] = _T('formidable:retour_aucun_traitement');
	}
	
	return $retours;
}

?>
