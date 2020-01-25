<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Un simple avec un bouton et une explication
**/
function formulaires_formidable_rio_saisies_dist(){
	$saisies = array(
		'options' => array('texte_submit' => _T('formidable_rio:texte_submit')),
		array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'creer_paiement',
				'texte' => _T('formidable_rio:explication'),
			)
		)
	);
	return $saisies;
}


function formulaires_formidable_rio_traiter_dist($id_formulaire){
	include_spip('action/editer_objet');
	$saisies = sql_getfetsel('saisies','spip_formulaires',"id_formulaire=$id_formulaire");
	$saisies = unserialize($saisies);
	$saisies = formidable_rio($saisies);
	$saisies = serialize($saisies);
	objet_modifier('formulaires',$id_formulaire,array('saisies'=>$saisies));
	$retours = array();

	return $retours;
}

/**
 * Prend les saisies, et reinitialiser recursivement les options 'info_obligatoire'
 * @param array $saisies
 * @param return $saisies
**/
function formidable_rio($saisies) {
	foreach ($saisies as &$s) {
		unset($s['options']['info_obligatoire']);
		if (isset($s['saisies'])) {
			$s['saisies'] = formidable_rio($s['saisies']);
		}
	}
	return $saisies;
}
