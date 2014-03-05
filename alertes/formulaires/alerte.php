<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * @param string $objet
 * @param int $id_objet
 * @return array
 */
function formulaires_alerte_charger_dist($objet, $id_objet){
	include_spip('base/abstract_sql');
	//Recuperation de la conf' préalable
	$a = @unserialize($GLOBALS['meta']['config_alertes']);
	if(!is_array($a)){
		$a  = array();
	}
	$valeur = array(
		'editable'=>true,
		'_deja_alerte'=>false,
		'_objet'=>$objet
	);
	$valeur['_id_objet'] = $id_objet;
	//Il faut verifier que l'objet est bien abonnable
	switch($objet){
		//On veut s'abonner à un mot-clef : verifions son groupe
		case "mot":
			//Recuperation du groupe
			if ($grp = sql_select('id_groupe', 'spip_mots', 'id_mot = '.intval($id_objet))) {
				while ($res = sql_fetch($grp)) {
					//On compare à la configuration saisie
					if( in_array($res['id_groupe'],to_array($a['groupes'])) ){
						$valeur['objet_autoriser'] = 'oui';
					}else{
						$valeur['objet_autoriser'] = 'non';
					}
				}
			}
		break;
		
		//On veut s'abonner à une rubrique ou un secteur (qui est une rubrique) : verifions son secteur et sa propre éligibilité
		case "rubrique":
			//Premierement, voyons si la rubrique est autoriser d'office
			if( in_array($id_objet, to_array($a['rubriques'])) ){
				$valeur['objet_autoriser'] = 'oui';
			}else{
				if($a['secteurs']){
					//Recuperation et test sur le secteur
					if ($secteur = sql_select('id_secteur', 'spip_rubriques', 'id_rubrique = '.intval($id_objet))) {
						while ($row = sql_fetch($secteurs)) {
							//On compare à la configuration saisie
							if( in_array($row['id_secteur'],to_array($a['secteurs'])) ){
								$valeur['objet_autoriser'] = 'oui';
							}else{
								$valeur['objet_autoriser'] = 'non';
							}
						}
					}					
				}
			}
		break;
		
		//On veut s'abonner à un auteur : vérifions qu'il est dans la liste autorisée.
		case "auteur":
			if($aut = sql_select('id_auteur', 'spip_auteurs', 'id_auteur = '.intval($id_objet))) {
				$valeur['objet_autoriser'] = 'oui';
			}else{
				$valeur['objet_autoriser'] = 'non';
			}
		break;
		
		default:
			$valeur['objet_autoriser'] = 'non';
		break;
	}
	//Etat par rapport au visiteur
	if (!isset($GLOBALS['visiteur_session']['statut'])){
		$valeur['editable'] = false;
	}
	else {
		include_spip('inc/alertes');
		$alerte = alertes_trouver($id_objet,$objet,$GLOBALS['visiteur_session']['id_auteur']);
		if ($alerte['id_alerte']){
			$valeur['_deja_alerte'] = true;
		}
	}
	return $valeur;
}

function formulaires_alerte_traiter_dist($objet, $id_objet){
	$res = array('message_ok'=>' ');
	if ($id_auteur = intval($GLOBALS['visiteur_session']['id_auteur'])){
		include_spip('inc/alertes');
		if (!is_null(_request('ajouter'))){
			alertes_ajouter($id_objet, $objet, $id_auteur);
		}
		if (!is_null(_request('retirer'))){
			alertes_supprimer(array('id_objet'=>$id_objet,'objet'=>$objet,'id_auteur'=>$GLOBALS['visiteur_session']['id_auteur']));
		}
	}
	return $res;
}
?>