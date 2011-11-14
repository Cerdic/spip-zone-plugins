<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2011
 *
 * Formulaire d'édition de tickets
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Fonction de chargement des valeurs
 */
function formulaires_editer_ticket_charger($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	// mettre une valeur new pour formulaires_editer_objet_charger()
	
	if (!intval($id_ticket)) $id_ticket='oui'; // oui pour le traitement de l'action (new, c'est pas suffisant)

	if (!autoriser('ecrire', 'ticket', $id_ticket)) {
		$editable = false;
	}else{
		$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
		$editable = true;
	}
	// si nouveau ticket et qu'une url d'exemple est donnee dans l'environnement, on la colle
	if ((!$id_ticket or $id_ticket=='oui') and ($exemple = _request('exemple'))) {
		$valeurs['exemple'] = $exemple;
	}
	
	if ((!$id_ticket or $id_ticket=='oui')){
		$valeurs['id_assigne'] = $GLOBALS['visiteur_session']['id_auteur'];
	}
	$valeurs['editable'] = $editable;
	return $valeurs;
}

/**
 *
 * Fonction de vérification des valeurs
 *
 * @return
 * @param int $id_ticket[optional]
 * @param string $retour[optional] URL de retour
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_editer_ticket_verifier($id_ticket='new', $retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('ticket',$id_ticket,array('titre','texte'));
	
	$doc = &$_FILES['ajouter_document'];
	spip_log($doc,'test');
	spip_log($_FILES,'test');
	if (isset($_FILES['ajouter_document'])
	AND $_FILES['ajouter_document']['tmp_name']) {
		include_spip('inc/ajouter_documents');
		list($extension,$doc['name']) = fixer_extension_document($doc);
		$acceptes = ticket_documents_acceptes();

		if (!in_array($extension, $acceptes)) {
			# normalement on n'arrive pas ici : pas d'upload si aucun format
			if (!$formats = join(', ',$acceptes))
				$formats = '-'; //_L('aucun');
			$erreurs['ajouter_document'] = _T('public:formats_acceptes', array('formats' => $formats));
		}
		else {
			include_spip('inc/getdocument');
			if (!deplacer_fichier_upload($doc['tmp_name'], $tmp.'.bin'))
				$erreurs['ajouter_document'] = _T('copie_document_impossible');

#		else if (...)
#		verifier le type_document autorise
#		retailler eventuellement les photos
			}

		// si ok on stocke les meta donnees, sinon on efface
		if (isset($erreurs['ajouter_document'])) {
			spip_unlink($tmp.'.bin');
			unset ($_FILES['ajouter_document']);
		} else {
			$doc['tmp_name'] = $tmp.'.bin';
			ecrire_fichier($tmp.'.txt', serialize($doc));
		}
	}
	return $erreurs;
}

function tickets_edit_config(){
	return array();
}

/**
 *
 * Fonction de traitement du formulaire
 *
 * @return
 * @param int $id_ticket[optional]
 * @param string $retour[optional] Une url de retour (on lui passera id_ticket=XX en paramètre)
 * @param object $config_fonc[optional]
 * @param object $row[optional]
 */
function formulaires_editer_ticket_traiter($id_ticket='new',$retour='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);

	$message['message_ok'] = _T('tickets:ticket_enregistre');
	/**
	 * Si pas d'adresse de retour on revient sur la page en cours avec l'id_ticket en paramètre
	 * Utile pour l'utilisation dans le public
	 */
	if (!$retour) {
		$message['redirect'] = parametre_url(parametre_url(self(),'id_ticket', $res['id_ticket']),'ticket','');
	} else {
		// sinon on utilise la redirection donnee.
		$message['redirect'] = parametre_url($retour, 'id_ticket', $res['id_ticket']);
	}
	
	return $message;
}

function ticket_documents_acceptes()
{
	$formats = trim($GLOBALS['meta']['formats_documents_ticket']);
	if (!$formats) return array('jpg','txt','gif','png');
	if ($formats !== '*')
		$formats = array_filter(preg_split(',[^a-zA-Z0-9/+_],', $formats));
	else {
		include_spip('base/typedoc');
		$formats =  array_keys($GLOBALS['tables_mime']);
	}
	sort($formats);
	return $formats;
}
?>
