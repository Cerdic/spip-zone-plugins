<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * Formulaire d'édition de tickets
 * 
 * @package SPIP\Tickets\Formulaires
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/filtres'); # Pour extraire_muti dans editer_ticket.html

/**
 * Identification unique d'un formulaire poste
 *  
 * @param int $id_ticket[optional]
 * 		Identifiant numérique du ticket ou new si nouveau
 * @param string $retour[optional]
 * 		URL de retour
 * @param string $associer_objet[optional]
 * 		Objet à lier au ticket (ex: "article|12")
 * @param string $config_fonc[optional]
 * 		Fonction de configuration du formulaire
 * @param array $row[optional]
 * @return string
 */
function formulaires_editer_ticket_identifier($id_ticket='new', $retour='', $associer_objet='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_ticket)));
}

/**
 * Fonction de chargement des valeurs du formulaire
 * 
 * @param int $id_ticket[optional]
 * 		Identifiant numérique du ticket ou new si nouveau
 * @param string $retour[optional]
 * 		URL de retour
 * @param string $associer_objet[optional]
 * 		Objet à lier au ticket (ex: "article|12")
 * @param string $config_fonc[optional]
 * 		Fonction de configuration du formulaire
 * @param array $row[optional]
 * @return array $valeurs
 * 		Un tableau des valeurs chargées au formulaire
 */
function formulaires_editer_ticket_charger($id_ticket='new', $retour='', $associer_objet='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	// mettre une valeur new pour formulaires_editer_objet_charger()
	if (!intval($id_ticket)) $id_ticket='oui'; // oui pour le traitement de l'action (new, c'est pas suffisant)

	if (!autoriser('ecrire', 'ticket', $id_ticket, '', array('associer_objet'=>$associer_objet)))
		$valeurs['editable'] = false;
	else{
		if(is_numeric($id_ticket) && !autoriser('modifier','ticket',$id_ticket))
			$valeurs['editable'] = false;
		else{
			$valeurs = formulaires_editer_objet_charger('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
			$valeurs['editable'] = true;
		
			// si nouveau ticket
			if (!$id_ticket OR $id_ticket=='oui'){
				$valeurs['id_assigne'] = $GLOBALS['visiteur_session']['id_auteur'];
				// Si un des champs de ce tableau est passé dans l'URL, on l'utilise dans le formulaire
				foreach(array('composant','version','severite','navigateur','tracker','id_assigne','exemple') as $champ){
					if(!$valeurs[$champ] && _request($champ))
						$valeurs[$champ] = _request($champ);
				}
			}

			$valeurs['groupesmots'] = array_map('array_shift', sql_allfetsel("id_groupe", "spip_groupes_mots", "FIND_IN_SET('tickets', tables_liees)"));
			foreach ($valeurs['groupesmots'] as $id_groupe) {
				$valeurs['groupemots_'.$id_groupe] = valeur_champ_groupemots_ticket('tickets', $id_ticket.'-'.$id_groupe, 'groupemots_ticket');
			}
		}
	}
	$valeurs['public'] = test_espace_prive() ? '' : 'on';

	return $valeurs;
}

/**
 *
 * Fonction de vérification des valeurs
 *
 * @param int $id_ticket[optional]
 * 		Identifiant numérique du ticket ou new si nouveau
 * @param string $retour[optional]
 * 		URL de retour
 * @param string $associer_objet[optional]
 * 		Objet à lier au ticket (ex: "article|12")
 * @param string $config_fonc[optional]
 * 		Fonction de configuration du formulaire
 * @param array $row[optional]
 * @return array $erreurs
 * 		Un tableau des erreurs de validation
 */
function formulaires_editer_ticket_verifier($id_ticket='new', $retour='', $associer_objet='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('ticket',$id_ticket,array('titre','texte'));
	
	/**
	 * Utilisation des fonctions de nospam pour filtrer un peu
	 */
	if (include_spip('inc/nospam')) {
		include_spip('inc/texte');
		$texte = _request('texte');
        $caracteres = compter_caracteres_utiles($texte);
        // moins de 10 caracteres sans les liens = spam !
        if ($caracteres < 10)
			$erreurs['texte'] = _T('forum:forum_attention_dix_caracteres');

        // on analyse le titre
        $infos_titre = analyser_spams(_request('titre'));
        // si un lien dans le titre = spam !
        if ($infos_titre['nombre_liens'] > 0)
			$erreurs['titre'] = _T('nospam:erreur_spam');
        // on analyse le texte
        $infos_texte = analyser_spams($texte);

        if ($infos_texte['nombre_liens'] > 0) {
        	// plus de 3 liens = spam, sauf si todo est là et donc 6 liens
			$max_liens = defined('_DIR_PLUGIN_TODO') ? 6 : 3;
			// si un lien a un titre de moins de 3 caracteres = spam, sauf si todo est là
			$max_caracteres_liens = defined('_DIR_PLUGIN_TODO') ? 1 : 3;
			if ($infos_texte['caracteres_texte_lien_min'] < $max_caracteres_liens)
				$erreurs['texte'] = _T('nospam:erreur_spam');
			if ($infos_texte['nombre_liens'] > $max_liens && !isset($GLOBALS['visiteur_session']['id_auteur']))
				$erreurs['texte'] = _T('nospam:erreur_spam');
        }
	}
	if(count($erreurs) == 0){
		if (!isset($GLOBALS['visiteur_session']['tmp_ticket_document'])) {
			include_spip('inc/session');
			session_set('tmp_ticket_document',sous_repertoire(_DIR_TMP, 'documents_ticket') . md5(uniqid(rand())));
		}
		$tmp = $GLOBALS['visiteur_session']['tmp_ticket_document'];
		$doc = &$_FILES['ajouter_document'];
		if (isset($_FILES['ajouter_document'])
		AND $_FILES['ajouter_document']['tmp_name']) {
			include_spip('inc/joindre_document');
			include_spip('action/ajouter_documents');
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
	}
	return $erreurs;
}

function tickets_edit_config(){
	return array();
}

/**
 * Fonction de traitement du formulaire
 *
 * @param int $id_ticket[optional]
 * 		Identifiant numérique du ticket ou new si nouveau
 * @param string $retour[optional]
 * 		URL de retour
 * @param string $config_fonc[optional]
 * 		Fonction de configuration du formulaire
 * @param array $row[optional]
 * @return array $message
 * 		Un tableau des éléments de retour du formulaire CVT
 */
function formulaires_editer_ticket_traiter($id_ticket='new',$retour='', $associer_objet='', $config_fonc='tickets_edit_config', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('ticket',$id_ticket,0,0,$retour,$config_fonc,$row,$hidden);
	
	/* Enregistrement des mots-clés */
	foreach (explode(',',_request('groupesmots')) as $id_groupe) {
		groupemots_ticket_revision($res['id_ticket'].'-'.$id_groupe, array('groupemots_ticket'=> implode(',',_request('groupemots_'.$id_groupe))), 'groupes_mot');
	}

	if (isset($res['message_erreur']))
		$message['message_erreur'] = $res['message_erreur'];
	else {
		$message['message_ok'] = _T('tickets:ticket_enregistre');
		/**
		 * Si pas d'adresse de retour on revient sur la page en cours avec l'id_ticket en paramètre
		 * Utile pour l'utilisation dans le public
		 */
		if (!$retour)
			$message['redirect'] = parametre_url(parametre_url(self(),'id_ticket', $res['id_ticket']),'ticket','');
		else {
			if (strncmp($retour,'javascript:',11)==0)
				$message['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour,11).'/*]]>*/</script>';
			else // sinon on utilise la redirection donnee.
				$message['redirect'] = parametre_url($retour, 'id_ticket', $res['id_ticket']);
		}
		if ($associer_objet){
			if (intval($associer_objet)){
				// compat avec l'appel de la forme ajouter_id_article
				$objet = 'article';
				$id_objet = intval($associer_objet);
			}
			else {
				list($objet,$id_objet) = explode('|',$associer_objet);
			}
			if ($objet AND $id_objet AND autoriser('associertickets',$objet,$id_objet)){
				include_spip('action/editer_liens');
				objet_associer(array('ticket'=>$res['id_ticket']), array($objet=>$id_objet));
				if (isset($res['redirect']))
					$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $res['id_ticket'], '&');
			} else {
				spip_log("associer le ticket ".$res['id_ticket']." avec $associer_objet refusé",'test.'._LOG_ERREUR);
			}
		}
	}

	return $message;
}

/**
 * Fonction listant les extensions de fichiers que l'on peut mettre en ligne depuis le 
 * formulaire de tickets
 * 
 * @return array $formats
 * 		Le tableau des extensions autorisées
 */
function ticket_documents_acceptes(){
	include_spip('inc/config');
	$formats = trim(lire_config('tickets/general/formats_documents_ticket'));
	if (!$formats) return array('jpg','gif','png','txt');
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
