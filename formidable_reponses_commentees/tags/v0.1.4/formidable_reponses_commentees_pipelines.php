<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formidable_reponses_commentees_formulaire_verifier($flux){
    
    if ($flux['args']['form'] == "construire_formulaire" and $nom_ou_id = _request('configurer_saisie')){
        $identifiant = 'constructeur_formulaire_'.$flux['args']['args'][0];
        $formulaire_actuel = session_get($identifiant);
        if ($nom_ou_id[0] == '@') {
			$saisies_actuelles = saisies_lister_par_identifiant($formulaire_actuel);
			$name = $saisies_actuelles[$nom_ou_id]['options']['nom'];
		} else {
			$saisies_actuelles = saisies_lister_par_nom($formulaire_actuel);
			$name = $nom_ou_id;
		}
        $nom = 'configurer_' . $name;
        // saisie inexistante => on sort
		if (!isset($saisies_actuelles[$nom_ou_id])) {
			return $flux;
		}
        foreach ($flux['data'] as $config){
            if (gettype($config) == "array"){
                $f = -1;
                foreach ($config as $fieldset){
                    $f++;
                    if ($fieldset['saisie'] == 'fieldset' and $fieldset['options']['label']=="<:saisies:option_groupe_affichage:>"){
                        $flux['data'][$nom][$f]['saisies'] = saisies_inserer($flux['data'][$nom][$f]['saisies'], array(
                                'saisie' => 'textarea',
                                'options' => array(
                                    'nom' => "saisie_modifiee_${name}[options][commentaire_apres_reponse]",
                                    'rows' => 10,
                                    'label' => _T('formidable_reponses_commentees:commentaire_apres_reponse_label'),
                                    'explication' => _T('formidable_reponses_commentees:commentaire_apres_reponse_explication')
                                )
                            ));                            
                        }
                    }
                }
            }
        }
    
    return $flux;
    }
?>