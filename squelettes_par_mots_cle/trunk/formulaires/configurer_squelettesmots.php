<?php
/***************************************************************************\
 *  Plugin Squelettes par mot clef 3			                           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('squelettesmots');

function formulaires_configurer_squelettesmots_charger_dist(){

	$_fonds = unserialize($GLOBALS['meta'][_SQUELETTES_MOTS_META]);
	$valeurs = array(
		'fonds'=>'',
		'tid_groupe'=>'',
		'type' => '',
		'actif' => '',
		'_fonds' => $_fonds?$_fonds:array()
	);
	$valeurs['_tableau_type'] = array();
	$liste = lister_tables_objets_sql();
	foreach ($liste as $table =>$info){
		$valeurs['_tableau_type'][] = $info['type'];
	}

	return $valeurs;
}
function formulaires_configurer_squelettesmots_verifier_dist(){
	return array();
}

function formulaires_configurer_squelettesmots_traiter_dist(){
	/* on charge la configuration */
	$fonds = unserialize($GLOBALS['meta'][_SQUELETTES_MOTS_META]);

	/* on charge les valeurs du formulaire */
	$field_fonds 	= _request('fonds');
	$id_groupes 	= _request('tid_groupe');
	$types 			= _request('type');
	$actif 			= _request('actif');
	
	/*On transforme les _POST en jolie tableau*/
	if($field_fonds) {
	  $new_fonds = array();
	  foreach($field_fonds as $index => $fond) {		
		$index = intval($index);
		$fond = addslashes($fond);
			if($actif[$index]
				AND $id_groupe = intval($id_groupes[$index])) {
			  $type = $types[$index];
			  $new_fonds[$fond] = array($id_groupe,$type,id_table_objet($type));
			}
	  }
	  $fonds = $new_fonds;
	}
	
	/* on ecrit la nouvelle configuration */
	ecrire_meta(_SQUELETTES_MOTS_META,serialize($fonds));

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}
?>