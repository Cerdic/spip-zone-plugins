<?php
/**
 * Plugin Coordonnees
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Definition des saisies du formulaire
 */
function formulaires_editer_numero_saisies_dist(){
	$saisies = array (
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'titre',
				'label' => _T('numero:label_titre'),
				'placeholder' => _T('numero:placeholder_titre')
			)
		),
		array (
			'saisie' => 'selection',
			'options' => array (
				'nom' => 'type',
				'label' => _T('numero:label_type'),
				'datas' => array (
					'home' => _T('numero:type_tel_home'),
					'work' => _T('numero:type_tel_work'),
					'cell' => _T('numero:type_tel_cell'),
					'pref' => _T('numero:type_tel_pref'),
					'voice' => _T('numero:type_tel_voice'),
					'msg' => _T('numero:type_tel_msg'),
					'fax' => _T('numero:type_tel_fax'),
					'dsl' => _T('numero:type_tel_dsl'),
					'video' => _T('numero:type_tel_video'),
					'pager' => _T('numero:type_tel_pager'),
					'bbs' => _T('numero:type_tel_bbs'),
					'modem' => _T('numero:type_tel_modem'),
					'car' => _T('numero:type_tel_car'),
					'isdn' => _T('numero:type_tel_isdn'),
					'pcs' => _T('numero:type_tel_pcs')
				)
			)
		),
		array (
			'saisie' => 'input',
			'options' => array (
				'nom' => 'numero',
				'label' => _T('numero:label_numero'),
				'obligatoire' => 'oui'
			),
			// decommenter ces lignes quand les numeros
			// internationaux seront pris en compte par 'verifier'
			/*'verifier' => array ( 
				'type' => 'telephone'
			)*/
		),
	);
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_numero_identifier_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_numero), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_numero_charger_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('numero',$id_numero,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	// valeur de la saisie "type" dans la table de liens
	if ( $associer_objet ) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		$valeurs['type'] = sql_getfetsel('type', 'spip_numeros_liens', 'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_numero='.intval($id_numero) );
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_numero_verifier_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// verification generique
	$erreurs = formulaires_editer_objet_verifier('numero',$id_numero);

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_numero_traiter_dist($id_numero='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('numero',$id_numero,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_numero = $res['id_numero']) {
		list($objet, $id_objet) = explode('|', $associer_objet);
		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('numero' => $id_numero), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], 'id_numero', '', '&');
			}
		}
		// remplir le champ "type" dans la table de liens
		if ( $type = _request('type') ) {
			sql_updateq('spip_numeros_liens',
				array('type' => $type),
				'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND id_numero='.intval($id_numero)
			);
		}
	}
	return $res;

}


?>
