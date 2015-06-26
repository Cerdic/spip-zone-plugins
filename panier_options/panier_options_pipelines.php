<?php
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Inserer des elements supplementaires
 *
 * @return $flux 
 */

function panier_options_recuperer_fond($flux){
	
	// surcharge la configuration du panier
    	if ($flux['args']['fond'] == 'formulaires/configurer_paniers'){
    		include_spip('inc/config');
    		$flux['args']['contexte']['code_avantage']  = lire_config("paniers/panier_options/code_avantage"); 
                $flux['args']['contexte']['pourcentage_avantage'] = lire_config("paniers/panier_options/pourcentage_avantage");
              
    		$option_champ = recuperer_fond('formulaires/configurer_paniers_options', $flux['args']['contexte']);
    		$flux['data']['texte'] = str_replace('<!--extra-->', '<!--extra-->' . $option_champ, $flux['data']['texte']);   			
    	}
    	
    	// surcharge le formulaire du panier
    	if ($flux['args']['fond'] == 'formulaires/panier'){
    		
    		include_spip('inc/config');
    		$pourcentage_avantage = lire_config("paniers/panier_options/pourcentage_avantage");
    		$code_avantage = lire_config("paniers/panier_options/code_avantage"); 
    		
    		//seulement si l'option est configurée
    		if($pourcentage_avantage && $code_avantage){
    			$flux['args']['contexte']['_pourcentage_avantage'] = $pourcentage_avantage;
    			$code_valide=false;
    			
    			//est-ce que l'avantage est déjà validé ?
    			if (!$id_panier) $id_panier = session_get('id_panier');
    			$avantage_valide = sql_getfetsel('options','spip_paniers',array('id_panier = '.sql_quote($id_panier)));
    			if($avantage_valide=="avantage_valide") $code_valide=true;
    			
    			//sinon tester si votre_code_avantage est le bon
    			if($code_valide==false){
				$config_code_avantage = lire_config("paniers/panier_options/code_avantage");
				$request_code_avantage=_request('votre_code_avantage');
				if($request_code_avantage==$config_code_avantage)$code_valide=true;
			}
			if($code_valide){
				//afficher le formulaire avec l'avantage calculé
				$option_calculer = recuperer_fond('formulaires/avantage_calculer_total', $flux['args']['contexte']);
				$flux['data']['texte'] = preg_replace('%(<tr class="total_ttc(.*?)</tr>)%is', ' '."\n".$option_calculer, $flux['data']['texte']);
			}
    		
			//affiche le champ input votre_code_avantage
			$option_champ = recuperer_fond('formulaires/avantage_option_input', $flux['args']['contexte']);
			$flux['data']['texte'] = str_replace('</table>', '</table>' . $option_champ, $flux['data']['texte']);
    		}
    	}
    	

    	return $flux;
}
    
function panier_options_formulaire_charger($flux){

	if ($flux['args']['form'] == 'panier'){
		$flux['data']['_forcer_request'] = true; // forcer la prise en compte du post
	}
	return $flux;
}

function panier_options_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'configurer_paniers'){
		//verifier le intval	
	}
	if ($flux['args']['form'] == 'panier'){
                $code_valide=false;
                $config_code_avantage = lire_config("paniers/panier_options/code_avantage");
                $request_code_avantage=_request('votre_code_avantage');
    		if($request_code_avantage==$config_code_avantage){$code_valide=true;};
    		
		if($request_code_avantage!='' && $code_valide==false){
			$flux['data']['votre_code_avantage']=_T('panier_options:code_avantage_invalide');	
		}

	}
	return $flux;
}

function panier_options_formulaire_traiter($flux){
		
	if ($flux['args']['form'] == 'configurer_paniers'){
		include_spip('inc/config');
		$code_avantage  = _request("code_avantage"); 
                $pourcentage_avantage = _request("pourcentage_avantage");
                ecrire_config("paniers/panier_options/code_avantage",$code_avantage); 
                ecrire_config("paniers/panier_options/pourcentage_avantage",$pourcentage_avantage);
              			
	}
	if ($flux['args']['form'] == 'panier'){
		if (!$id_panier) $id_panier = session_get('id_panier');
    		//spip_log("dans formulaire_charger du panier".$id_panier,"formulaires_pipelines");
		//tester si le code est bon
                $code_valide=false;
                $config_code_avantage = lire_config("paniers/panier_options/code_avantage");
                $request_code_avantage=_request('votre_code_avantage');
    		if($request_code_avantage==$config_code_avantage){$code_valide=true;};
    		if($code_valide) {
    			sql_updateq('spip_paniers',array('options'=>'avantage_valide'),'id_panier = '.$id_panier );
    			$flux['data']['message_ok'] = _T('panier_options:panier_modifie_ok');
    		}
	
    	}
	return $flux;
}
