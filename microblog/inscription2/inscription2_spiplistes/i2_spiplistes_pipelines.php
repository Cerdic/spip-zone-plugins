<?php
	function i2_spiplistes_i2_cfg_form($flux){
		//Le pavé de configuration dans le CFG d'inscription2
		$flux .= recuperer_fond('fonds/inscription2_spiplistes');
		return $flux;
	}
	
	function i2_spiplistes_i2_form_fin($flux){
		// Le pavé dédié aux listes dans le formulaire d'inscription 
		// ou de changement de profil
		if ((lire_config('inscription2/newsletter') == 'on') && (count(lire_config('inscription2/newsletters'))>0)){
			$flux['data'] .= recuperer_fond('formulaires/inscription2_form_listes',$flux['args']);
		}
		return $flux;
	}
	
	function i2_spiplistes_i2_charger_formulaire($flux){
		// Ajouter un array() $listes dans les $valeurs envoyées au formulaire.
		if((is_numeric($flux['data']['id_auteur'])) && (lire_config('inscription2/newsletter') == 'on')){
			// selectionner les listes de l'auteur
			$res = sql_select('id_liste',  'spip_auteurs_listes',  'id_auteur='.$flux['data']['id_auteur']);

			// boucler les resultats
			while($liste = sql_fetch($res)){
				$listes[] = $liste['id_liste'];
			}
			$flux['data']['listes'] = $listes;
		}else{
			$flux['data']['listes'] = _request('newsletters');
		}
		return $flux;
	}
	
	function i2_spiplistes_i2_traiter_formulaire($flux){
		$id_auteur = $flux['args']['id_auteur'];
		$listes = _request('newsletters'); 
		$format = _request('newsletter') ;
		if((($format == "html") or ($format == "texte")) and is_array($listes)){
			// on maj le format de reception avec le format par defaut
			sql_updateq("spip_auteurs_elargis",array('spip_listes_format'=>$format),"id_auteur=$id_auteur");
			// on abonne aux listes
			$listes_str = is_array($listes)? implode(',',$listes): '0';
			sql_delete("spip_auteurs_listes","id_auteur=$id_auteur AND id_liste NOT IN ($listes_str)");
			foreach($listes as $cle => $liste){
				if(!$id_liste = sql_getfetsel("id_liste","spip_auteurs_listes","id_auteur=$id_auteur AND id_liste=$liste")){
					$couple = array('id_auteur'=>$id_auteur,'id_liste'=>$liste,'date_inscription' => date("Y-m-d H:i:s",time()));
					sql_insertq('spip_auteurs_listes',$couple);
				}
			}
		}	
		return $flux;
	}
	
	function i2_spiplistes_i2_exceptions_des_champs_auteurs_elargis($flux){
		// On ne crée pas de champs dans la table auteurs_elargis pour ces inputs
		// $flux est un array à compléter
		$flux[] = 'newsletter';
		$flux[] = 'newsletters';
		$flux[] = 'optout';
		
		return $flux;
	}
?>
