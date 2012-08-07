<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * critere {orphelins} selectionne les albums sans liens avec un objet editorial
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_ALBUMS_orphelins_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];
	$cond = $crit->cond;
	$not = $crit->not?"":"NOT";

	$select = sql_get_select("DISTINCT id_album","spip_albums_liens as oooo");
	$where = "'".$boucle->id_table.".id_album $not IN ($select)'";
	if ($cond){
		$_quoi = '@$Pile[0]["orphelins"]';
		$where = "($_quoi)?$where:''";
	}

	$boucle->where[]= $where;
}


/**
 * Retirer une valeur d'un tabeau
 * param array $table_balise
 * param string $valeur
 *
 * exemple : #GET{tableau}|table_retirer_valeur{'valeur'}
 */
function table_retirer_valeur($table_balise, $valeur){

	$valeur = (string)$valeur;
	unset($table_balise[array_search($valeur, $table_balise)]); # supprime la valeur du tableau

	return $table_balise;
}


/**
 * Ajouter ou retirer un parametre d'une chaine en forme de liste concatenee
 * Utile pour generer un lien en y ajoutant/supprimant un parametre
 * @param string $balise
 * @param string $parametre =	param1|param2|...
 * @param string $delimiteur =	|/-, ...
 * @param string $action =	toggle,ajouter,retirer
 *
 * exemples : 
 * valeur initale : parametres = paramA|paramB|paramC|paramD
 * ----------
 * #ENV{parametres}|toggle_parametre{'paramB',toggle,'|'}
 * retour : parametres = paramA|paramC|paramD
 * ----------
 * #ENV{parametres}|toggle_parametre{'paramB|paramC'}
 * retour : parametres = paramA|paramD
 */
function toggle_parametre($balise, $parametre, $action='toggle', $delimiteur='|'){

	$delimiteur = (string)$delimiteur;
	$parametre = (string)$parametre;
	$action = (string)$action;
	$table_balise = explode($delimiteur, $balise); # tableau des anciens parametres
	$table_parametres = explode($delimiteur, $parametre); # tableau des nouveaux parametres

	switch ($action) {
		case 'toggle':
			foreach ($table_parametres as $parametre){
				if (!empty($balise)){
					// si le parametre est present, on le retire
					if (in_array($parametre, $table_balise)) {
						unset($table_balise[array_search($parametre, $table_balise)]); # supprime le parametre du tableau
						$balise = implode($delimiteur, $table_balise); # recree la liste
					}
					//sinon on le rajoute
					else {
						array_push($table_balise, $parametre);
						$balise = implode($delimiteur, $table_balise);
					}
				}
				else {
					$balise = $parametre;
				}
			}
			break;
		case 'retirer':
			foreach ($table_parametres as $parametre){
				if (!empty($balise) AND in_array($parametre, $table_balise)){
					unset($table_balise[array_search($parametre, $table_balise)]);
					$balise = implode($delimiteur, $table_balise);
				}
			}
			break;
		case 'ajouter':
			foreach ($table_parametres as $parametre){
				if (!empty($balise)){
					array_push($table_balise, $parametre);
					$balise = implode($delimiteur, $table_balise);
				}
				else {
					$balise = $parametre;
				}
			}
			break;
	}

	return $balise;
}


?>
