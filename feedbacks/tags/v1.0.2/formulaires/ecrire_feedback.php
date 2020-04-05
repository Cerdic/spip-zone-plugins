<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2012                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_ecrire_feedback_charger_dist(){
	include_spip('inc/texte');
	$puce = definir_puce();
	$valeurs = array(
		'choix_message_feedback'=>'',
		'texte_message_feedback'=>''
	);

	return $valeurs;
}

function formulaires_ecrire_feedback_verifier_dist(){
	$erreurs = array();
	include_spip('inc/filtres');
	include_spip('inc/feedbacks');

	if(!$choix=_request('choix_message_feedback'))
		$erreurs['choix_message_feedback'] = _T('info_obligatoire');

	// NoSpam attack
    include_spip('inc/texte');
    // si nospam est present on traite les spams
    if (include_spip('inc/nospam')) {

        // on analyse le sujet
        $infos_texte = analyser_spams($texte);
        // si un lien dans le sujet = spam !
        if ($infos_texte['nombre_liens'] > 0)
                $erreurs['texte_message_feedback'] = _T('nospam:erreur_spam');	

        // on analyse le texte
        $infos_texte = analyser_spams($texte);
        if ($infos_texte['nombre_liens'] > 0) {
                // si un lien a un titre de moins de 3 caracteres = spam !
                if ($infos_texte['caracteres_texte_lien_min'] < 3) {
                        $erreurs['texte_message_feedback'] = _T('nospam:erreur_spam');
                }
                // si le texte contient plus de trois lien = spam !
                if ($infos_texte['nombre_liens'] >= 3)
                        $erreurs['texte_message_feedback'] = _T('nospam:erreur_spam');
        }
    }	

	if (!$texte=_request('texte_message_feedback'))
		$erreurs['texte_message_feedback'] = _T("info_obligatoire");
	elseif((strlen($texte)<10))
		$erreurs['texte_message_feedback'] = _T('feedback:feedback_attention_dix_caracteres');
	elseif(!(strlen($texte)<100))
		$erreurs['texte_message_feedback'] = _T('feedback:feedback_attention_cent_caracteres');
	elseif(!bloque_indesirable($texte))
		$erreurs['texte_message_feedback'] = _T('feedback:feedback_anonyme_erreur');		
	elseif(!email_detecte($texte))
		$erreurs['texte_message_feedback'] = _T('feedback:feedback_anonyme_erreur');		
	elseif(url_detect_fragment($texte) || url_detecte($texte))
		$erreurs['texte_message_feedback'] = _T('feedback:feedback_anonyme_erreur');

	if (!_request('confirmer') AND !count($erreurs)) {
		$erreurs['previsu']=' ';
	}
	return $erreurs;
}

function formulaires_ecrire_feedback_traiter_dist($id_feedback='new', $id_rubrique=1, $retour='', $lier_trad=0, $config_fonc='feedbacks_edit_config', $row=array(), $hidden=''){

	global $table_des_traitements;
	include_spip('public/composer');

	$choix = _request('choix_message_feedback');
	$texte = _request('texte_message_feedback');

	include_spip('inc/rubriques');

	// Si id_rubrique vaut 0 ou n'est pas definie, creer le feedback
	// dans la premiere rubrique racine
	if (!$id_rubrique = intval($id_rubrique)) {
		$id_rubrique = sql_getfetsel("id_rubrique", "spip_rubriques", "id_parent=0",'', '0+titre,titre', "1");
	}

	// La langue a la creation : c'est la langue de la rubrique
	$row = sql_fetsel("lang, id_secteur", "spip_rubriques", "id_rubrique=$id_rubrique");
	$lang = $row['lang'];
	$id_rubrique = $row['id_secteur']; // garantir la racine

	$champs = array(
		'id_rubrique' => $id_rubrique,
		'statut' => 'prop',
		'date_heure' => date('Y-m-d H:i:s'),
		'lang' => $lang,
		'langue_choisie' => 'non',
		'titre' => safehtml($choix),
		'texte' => safehtml($texte));
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
			),
			'data' => $champs
		)
	);
	$id_feedback = sql_insertq("spip_feedbacks", $champs);
	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_feedbacks',
				'id_objet' => $id_feedback
			),
			'data' => $champs
		)
	);
	$desc = $id_feedback;

	$message = _T('feedback:feedback_envoyer');
	return array('message_ok'=>$message);
}

?>