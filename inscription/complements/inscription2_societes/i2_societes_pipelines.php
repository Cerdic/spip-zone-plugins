<?php
/**
 *
 * Insertion dans le pipeline i2_cfg_form
 * @return
 * @param object $flux
 */
function i2_societes_i2_cfg_form($flux){
	$flux .= recuperer_fond('fonds/inscription2_societes');
	return $flux;
}

/**
 *
 * Insertion dans le pipeline affiche_droite
 * Dans certaines pages définies, afficher le lien d'accès à la page des comptes utilisateurs
 *
 * @return array Le même tableau qu'il reçoit en argument
 * @param array $flux Un tableau donnant des informations sur le contenu passé au pipeline
 */

function i2_societes_affiche_droite($flux){
	if(((preg_match('/^inscription2/',$flux['args']['exec']))
		 || (preg_match('/^auteurs/',$flux['args']['exec']))
		 || (preg_match('/^i2_/',$flux['args']['exec']))
		 || (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription2') || preg_match('/^i2_/',_request('cfg'))))
		)
		 && ($flux['args']['exec'] != 'inscription2_adherents')){
    	$flux['data'] .= recuperer_fond('prive/i2_societes_affiche_droite');
	}
	return $flux;
}

function i2_societes_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		/**
		 *
		 * Insertion des champs dans le formulaire aprs le textarea PGP
		 *
		 */
		$inscription2 = recuperer_fond('prive/inscription2_champs_id_societe',array('id_auteur' => $flux['args']['id']));
		$flux['data'] = preg_replace('%(<li class="editer_inscription2 fieldset(.*?)</li>)%is', '$1'."\n".$inscription2, $flux['data']);
	}
	return $flux;
}

/**
 *
 * Insertion dans le pipeline post_edition
 * ajouter les champs inscription2 soumis lors de la soumission du formulaire CVT editer_auteur
 *
 * @return
 * @param object $flux
 */
function i2_societes_post_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		$id_auteur = $flux['args']['id_objet'];
		$societes = sql_select('id_objet','spip_auteurs_liens',"id_auteur=$id_auteur AND objet='societe'");
		$societes_voulues = _request('id_societe');
		if(lire_config('inscription2/id_societe') == 'on'){
			if(lire_config('i2_societes/forme') == 'multiple'){
				$societe_existantes = array();
				while($societe=sql_fetch($societes)){
					if(!in_array($societe['id_objet'],$societes_voulues)){
						sql_delete("spip_auteurs_liens","id_auteur=$id_auteur AND objet='societe' AND id_objet=".$societe['id_objet']);
					}else{
						$societe_existantes[] = $societe['id_objet'];
					}
				}
				foreach($societes_voulues as $societe_voulue){
					if(!in_array($societe_voulue,$societe_existantes)){
						sql_insertq("spip_auteurs_liens",array("id_auteur"=>$id_auteur,"objet"=>"societe","id_objet"=>$societe_voulue));
					}
				}
			}else{
				while($societe=sql_fetch($societes)){
					if($societe != $societes_voulues){
						sql_delete("spip_auteurs_liens","id_auteur=$id_auteur AND objet='societe' AND id_objet=".$societe['id_objet']);
					}else{
						$societe_existe = true;
					}
				}
				if(!$societe_existe){
					sql_insertq("spip_auteurs_liens",array("id_auteur"=>$id_auteur,"objet"=>"societe","id_objet"=>$societes_voulues));
				}
			}
		}
	}
	return $flux;
}

function i2_societes_i2_exceptions_des_champs_auteurs_elargis($array){
	// Des choses spécifiques à inscription2
	$array[] = 'id_societe';

	return $array;
}

function i2_societes_i2_exceptions_chargement_champs_auteurs_elargis($array){
	// Des choses spécifiques à inscription2
	$array[] = 'id_societe';

	return $array;
}
?>