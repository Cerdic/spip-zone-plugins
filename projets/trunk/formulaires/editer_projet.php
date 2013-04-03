<?php
/**
 * Plugin projets
 * (c) 2012 Cyril Marion
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_projet_identifier_dist($id_projet='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_projet), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_projet_charger_dist($id_projet='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('projet',$id_projet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
    if (!$valeurs['actif']) $valeurs['actif'] = 'oui';

	if (!intval($id_projet) and $id_parent = _request('id_parent')){
		$valeurs['id_parent'] = $id_parent;
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_projet_verifier_dist($id_projet='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    $erreurs = formulaires_editer_objet_verifier('projet',$id_projet, array('nom'));
    $verifier = charger_fonction('verifier', 'inc');

    foreach (array(
        'date_publication',
        'date_debut',
        'date_livraison_prevue',
        'date_livraison') AS $champ)
    {
        $normaliser = null;
        if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
            $erreurs[$champ] = $erreur;
        // si une valeur de normalisation a ete transmis, la prendre.
        } elseif (!is_null($normaliser)) {
            set_request($champ, $normaliser);
        // si pas de normalisation ET pas de date soumise, il ne faut pas tenter d'enregistrer ''
        } else {
            set_request($champ, null);
        }
    }
    return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_projet_traiter_dist($id_projet='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('projet',$id_projet,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_projet = $res['id_projet']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('projet' => $id_projet), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_projet, '&');
			}
		}
	}
	return $res;

}


?>
