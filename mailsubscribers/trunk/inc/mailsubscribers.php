<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Normaliser le nom d'une liste de diffusion
 *
 * @param string $liste
 * @param string $category
 * @return string
 */
function mailsubscribers_normaliser_nom_liste($liste='', $category="newsletter"){
	$category = strtolower(trim(preg_replace(",\W,","",$category)));

	if (!$liste)
		return "$category::$category"; // valeur fixe en cas de reantrance

	if (strpos($liste,"::")!==false){
		$liste = explode("::",$liste);
		return mailsubscribers_normaliser_nom_liste($liste[1],$liste[0]);
	}
	include_spip("inc/charsets");
	$liste = translitteration($liste);
	$liste = strtolower($liste);

	$liste = trim(preg_replace(",[^\w-],","",$liste));
	$liste = "$category::$liste";
	return $liste;
}


/**
 * Informer un subscriber : ici juste l'url unsubscribe a calculer
 * @param array $infos
 * @return array mixed
 */
function mailsubscribers_informe_subscriber($infos){
	$infos['listes'] = explode(',',$infos['listes']);
	$infos['listes'] = array_map('mailsuscribers_filtre_liste',$infos['listes']);
	$infos['listes'] = array_filter($infos['listes']);

	$infos['url_unsubscribe'] = mailsubscriber_url_unsubscribe($infos['email'],$infos['jeton']);
	unset($infos['jeton']);
	return $infos;
}

/**
 * Filtrer une liste a partir de sa category
 * @param $liste
 * @param string $category
 * @return string
 *   chaine vide si la liste n'est pas dans la category
 *   nom de la liste sans le prefix de la category si ok
 */
function mailsuscribers_filtre_liste($liste,$category="newsletter"){
	if (strncmp($liste,"$category::",$l=strlen("$category::"))==0){
		return substr($liste,$l);
	}
	return '';
}

/**
 * Renvoi les listes de diffusion disponibles avec leur status
 * (open,close,?)
 *
 * @param array $options
 *   category : filtrer les listes par category (dans ce cas la categorie est enlevee de l'id)
 *   status : filtrer les listes sur le status
 * @return array
 *   listes
 */
function mailsubscribers_listes($options = array()){
	$filtrer_status = $filtrer_category = false;
	if (isset($options['status']))
		$filtrer_status = $options['status'];

	$listes = array();

	// d'abord les listes connues en config
	if ($known_lists = lire_config("mailsubscribers/lists",array())
		AND is_array($known_lists)
		AND count($known_lists)){

		foreach ($known_lists as $kl){
			$id = $kl['id'];
			if (!$filtrer_category OR $id=mailsuscribers_filtre_liste($id,$filtrer_category)){
				$status = ($kl['status']=='open'?'open':'close');
				if (!$filtrer_status OR $filtrer_status==$status) {
					$listes[$id] = array(
						'titre' => $kl['titre'],
						'status' => $status
					);
				}
			}
		}
	}

	// puis trouver toutes les listes qui existent en base et non connues en config
	// pas la peine si on a demande de filtrer les listes open ou close
	if ($filtrer_status!=='?') {
		$rows = sql_allfetsel("DISTINCT listes","spip_mailsubscribers","statut=".sql_quote('valide'));
		foreach ($rows as $row){
			$ll = explode(",",$row['listes']);
			foreach($ll as $l){

				if ($id=$l
					AND (
						!$filtrer_category OR $id=mailsuscribers_filtre_liste($l,$filtrer_category)
					)){
					if (!isset($listes[$id]))
						$listes[$id] = array('titre'=>$id,'status'=>'?');
				}
			}
		}
	}

	return $listes;
}