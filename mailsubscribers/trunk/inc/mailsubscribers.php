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

function mailsubscribers_obfusquer_email($email){
	return md5($email)."@example.org";
}

function mailsubscribers_test_email_obfusque($email){
	return preg_match(",^[a-f0-9]+@example.org$,",$email);
}

/**
 * Informer un subscriber : ici juste l'url unsubscribe a calculer
 * @param array $infos
 * @return array mixed
 */
function mailsubscribers_informe_subscriber($infos){
	$infos['listes'] = explode(',',$infos['listes']);
	$infos['listes'] = array_map('mailsubscribers_filtre_liste',$infos['listes']);
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
function mailsubscribers_filtre_liste($liste,$category="newsletter"){
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
	if (isset($options['category']))
		$filtrer_category = $options['category'];

	$listes = array();

	// d'abord les listes connues en config
	if (!function_exists('lire_config'))
		include_spip('inc/config');
	if ($known_lists = lire_config("mailsubscribers/lists",array())
		AND is_array($known_lists)
		AND count($known_lists)){

		foreach ($known_lists as $kl){
			$id = $kl['id'];
			if (!$filtrer_category OR $id=mailsubscribers_filtre_liste($id,$filtrer_category)){
				$status = ($kl['status']=='open'?'open':'close');
				if (!$filtrer_status OR $filtrer_status==$status) {
					$listes[$id] = array(
						'id' => $id,
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
		$rows = sql_allfetsel("DISTINCT listes","spip_mailsubscribers","statut!=".sql_quote('poubelle'));
		foreach ($rows as $row){
			$ll = explode(",",$row['listes']);
			foreach($ll as $l){
				if ($id=$l
					AND (
						!$filtrer_category OR $id=mailsubscribers_filtre_liste($l,$filtrer_category)
					)){
					if (!isset($listes[$id]))
						$listes[$id] = array('id'=>$id,'titre'=>$id,'status'=>'?');
				}
			}
		}
	}

	return $listes;
}

/**
 * Renomme un identifiant de liste dans la liste des abonnés
 *
 * @note
 *   Si le nouveau nom est déjà un nom de liste existante, le renommage
 *   est tout de même effectué, sans doublonner si l'abonné y est déjà inscrit.
 * 
 * @param string $liste_ancienne
 *   Identifiant de liste à renommer (exemple newsletter::1-truc)
 * @param string $liste_nouvelle
 *   Nouvel identifiant de la liste (exemple newsletter::infolettre)
 * @return bool
 *   True si l'opération a été réalisée.
**/
function mailsubscribers_renommer_identifiant_liste($liste_ancienne, $liste_nouvelle) {
	spip_log("Renommer la liste '$liste_ancienne' en '$liste_nouvelle'", "mailsubscribers");

	while ($subscribers = sql_allfetsel(
		'id_mailsubscriber, listes',
		'spip_mailsubscribers',
		"listes REGEXP '(^|,)$liste_ancienne($|,)'",
		"","","0,50"))
	{
		if (!$subscribers) break;

		include_spip('action/editer_objet');
		$liste_nouvelle = trim($liste_nouvelle);

		foreach ($subscribers as $s) {
			$listes = explode(',', $s['listes']);
			$key = array_search($liste_ancienne, $listes);
			if ($key !== false) { // sait on jamais
				// si le nouveau nom existe déjà, pas la peine de le dupliquer !
				if (false === array_search($liste_nouvelle, $listes)) {
					$listes[$key] = $liste_nouvelle;
				} else {
					unset($listes[$key]);
				}
				$listes = implode(',', $listes);
				objet_modifier("mailsubscriber", $s['id_mailsubscriber'], array('listes' => $listes));
			}
		}
	}
	return true;
}


/**
 * Supprime un identifiant de liste dans la liste des abonnés
 *
 * Si un abonné n'est alors plus abonné à aucune liste,
 * on le met à la poubelle !
 *
 * @param string $liste
 *   Identifiant de liste à supprimer (exemple newsletter::infolettre)
 * @return bool
 *   True si l'opération a été réalisée.
**/
function mailsubscribers_supprimer_identifiant_liste($liste) {
	spip_log("Supprimer la liste '$liste'", "mailsubscribers");
	$GLOBALS['notification_instituermailsubscriber_status'] = false; // pas de notification ici

	while ($subscribers = sql_allfetsel(
		'id_mailsubscriber, listes',
		'spip_mailsubscribers',
		"listes REGEXP '(^|,)$liste($|,)'",
		"","","0,50"))
	{
		if (!$subscribers) break;

		include_spip('action/editer_objet');

		foreach ($subscribers as $s) {
			$listes = explode(',', $s['listes']);
			$key = array_search($liste, $listes);
			if ($key !== false) { // sait on jamais
				unset($listes[$key]);
			}
			if (count($listes)) {
				$listes = implode(',', $listes);
				objet_modifier("mailsubscriber", $s['id_mailsubscriber'], array('listes' => $listes));
			} else {
				objet_modifier("mailsubscriber", $s['id_mailsubscriber'], array('listes' => '', 'statut' => 'poubelle'));
			}
		}
	}
	return true;
}
