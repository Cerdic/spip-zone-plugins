<?php
/**
 * Insertion dans le pipeline affiche_droite (SPIP)
 * 
 * Affiche un lien vers la page de statistiques de l'auteur sur sa page auteur
 * Affiche un lien vers la page de statistiques de l'article sur la page article
 * 
 * @param array $flux Le contexte du pipeline
 * @return array $flux Le contexte modifié
 */
function bigbrother_affiche_droite($flux){
	if (($flux['args']['exec'] == 'auteur_infos') && ($flux['args']['id_auteur'] > 0)){
		$boite = debut_boite_info(true)
			. icone_horizontale(
				_T('bigbrother:voir_statistiques_auteur'),
				generer_url_ecrire('bigbrother_visites_articles_auteurs','id_auteur='.$flux['args']['id_auteur']),
				find_in_path('bigbrother-24.png', 'images/', false),
				'',
				false
			)
			. fin_boite_info(true);
		$flux['data'] .= $boite;
	}
	elseif ($flux['args']['exec'] == 'articles'){
		$boite = debut_boite_info(true)
			. icone_horizontale(
				_T('bigbrother:voir_statistiques_article'),
				generer_url_ecrire('bigbrother_visites_articles_auteurs','id_article='.$flux['args']['id_article']),
				find_in_path('bigbrother-24.png', 'images/', false),
				'',
				false
			)
			. fin_boite_info(true);
		$flux['data'] .= $boite;
	}
	return $flux;
}


function bigbrother_insert_head($flux){
	$flux .= '
<link rel="stylesheet" media="all" type="text/css" href="'.find_in_path('bigbrother.css', 'css/', false).'" />
';
	return $flux;
}

function bigbrother_header_prive($flux){
	$flux .= '
<link rel="stylesheet" media="all" type="text/css" href="'.find_in_path('bigbrother.css', 'css/', false).'" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline post_edition
 * On ajoute la journalisation pour la modification de contenu
 * ainsi que pour l'institution (changement de statut) d'objets
 * @param unknown_type $flux
 */
function bigbrother_post_edition($flux){
	if(lire_config('bigbrother/modifier')){
		$journal = charger_fonction('journal','inc');
		$qui = $GLOBALS['visiteur_session']['nom'] ? $GLOBALS['visiteur_session']['nom'] : $GLOBALS['ip'];
		$qui_ou_ip = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

		$quoi = $flux['args']['type'];
		if(!$quoi){
			$table = $flux['args']['table'];
			$quoi = objet_type($table);
		}

		if($flux['args']['action'] == 'instituer'){
			$faire = 'instituer';
			$texte = 'bigbrother:action_instituer_objet';
		}else if(!isset($flux['args']['action']) && isset($flux['args']['operation'])){
			if($flux['args']['operation'] == 'ajouter_document'){
				$faire = 'inserer';
				$texte = "bigbrother:action_".$flux['args']['operation'];
			}else{
				$faire = $flux['args']['operation'];
				$texte = "bigbrother:action_$faire";
			}

			spip_log($flux,'journal');
		}
		else{
			spip_log($flux,'journal');
			$faire = 'modifier';
			$texte = 'bigbrother:action_modifier_objet';
			/**
			 * Les actions de modifications passent un array à $flux['data'] des champs modifiés
			 * On le serialize pour son insertion future en base
			 */
			if(is_array($flux['data'])){
				$infos['modifs'] = serialize($flux['data']);
			}
		}


		$texte_infos = array('qui'=>$qui,'type'=> $quoi,'id'=>$flux['args']['id_objet']);

		$journal(
			_T($texte,$texte_infos),
			array('qui' => $qui_ou_ip,'faire' => $faire,'quoi' => $quoi,'id' => $flux['args']['id_objet'],'infos' => $infos)
		);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post_insertion
 * On ajoute la journalisation pour la création de contenu
 * @param unknown_type $flux
 */
function bigbrother_post_insertion($flux){
	if(lire_config('bigbrother/inserer')){
		$journal = charger_fonction('journal','inc');
		$qui = $GLOBALS['visiteur_session']['nom'] ? $GLOBALS['visiteur_session']['nom'] : $GLOBALS['ip'];
		$qui_ou_ip = $GLOBALS['visiteur_session']['id_auteur'] ? $GLOBALS['visiteur_session']['id_auteur'] : $GLOBALS['ip'];

		$table = $flux['args']['table'];
		$quoi = objet_type($table);
		$faire = 'inserer';
		$texte = 'bigbrother:action_inserer_objet';

		$texte_infos = array('qui'=>$qui,'type'=> $quoi,'id'=>$flux['args']['id_objet']);

		$journal(
			_T($texte,$texte_infos),
			array('qui' => $qui_ou_ip,'faire' => $faire,'quoi' => $quoi,'id' => $flux['args']['id_objet'])
		);
	}
}

function bigbrother_jquery_plugins($array){
	$array[] = _DIR_LIB_FLOT.'/jquery.flot.js';
	$array[] = 'javascript/flot_extras.js';
	return $array;
}

function bigbrother_affichage_final($flux){
	// Si la config est ok, à chaque hit, on teste s'il faut enregistrer la visite ou pas
	if (lire_config('bigbrother/visite') == 'oui')
		bigbrother_tester_la_visite_du_site();
	return $flux;
}
?>
