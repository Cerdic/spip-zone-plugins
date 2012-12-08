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
 * Filtre permettant de permuter un element d une chaine sous forme de liste concatenee A|B|C|D
 * Permuter, c est a dire retirer l element s il est present dans la chaine, et inversement l ajouter s il est absent.
 * Il s utilise a priori en complement du filtre |parametre_url
 *
 * Exemple d utilisation : l adresse de la page contient ?fruits=pomme|poire|melon
 * On souhaite qu'un lien recharge la page en permutant le parametre "poire"
 * On definit le lien ainsi : #SELF|parametre_url{fruits, #ENV{fruits}|permuter_parametre{poire}}
 * Le premier clic va renvoyer pomme|melon, le second clic pomme|melon|poire et ainsi de suite 
 * On peut eventuellement forcer l action a effectuer (ajouter ou retirer) et preciser le delimiteur (par defaut, un pipe)
 *
 * @param string $balise
 * @param string $parametre
 * 		le ou les parametres a retirer ou a ajouter, separes par un delimiteur : param1|param2|param3...
 * @param string $delimiteur
 *		(optionnel) caractere separant les parametres : |/-, etc.
 * @param string $action
 *		(optionnel) action a effectuer : permuter, ajouter, retirer
 *
 * exemples
 * #VAL{A|B|C|D}|permuter_parametre{B} -> A|C|D
 * #VAL{A|B|C|D}|permuter_parametre{B|C} -> A|D
 * #VAL{A|B|C|D}|permuter_parametre{B,retirer} -> A|C|D
 * #VAL{A|B|C|D}|permuter_parametre{B,ajouter} -> A|B|C|D  (aucun effet)
 * #VAL{A-B-C-D}|permuter_parametre{B-D,permuter,-} -> A|C
 */
function permuter_parametre($balise, $parametre, $action='permuter', $delimiteur='|'){

	$parametre = (string)$parametre;
	$table_balise = explode($delimiteur, $balise); # tableau des anciens parametres
	$table_parametres = explode($delimiteur, $parametre); # tableau des nouveaux parametres

	if ($parametre) {
		switch ($action) {
			case 'permuter':
				foreach ($table_parametres as $parametre){
					if (!empty($balise)){
						// si le parametre est present, on le retire...
						if (in_array($parametre, $table_balise)) {
							unset($table_balise[array_search($parametre, $table_balise)]); # supprime le parametre du tableau
							$balise = implode($delimiteur, $table_balise); # recree la liste
						}
						// ...et inversement
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
	}

	return $balise;
}


?>
