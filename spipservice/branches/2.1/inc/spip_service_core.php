<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 2.1                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

include_spip('inc/spip_service_utils');

/**
 * ECRITURE
 * supprime un article
 * @param $id
 * @return number
 */
function deleteArticle($id){
	$autorisation = autoriser('supprimer', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$id));
	if (_DEBUG_SPIPSERVICE) spip_log("deleteArticle - autoriserSupprimer : ".$autorisation, 'spipservice');

	if ($autorisation){
		$success = instituteArticle($id, 'poubelle');
		if ($success){
			spip_log("deleteArticle - SUCCESS", 'spipservice');
			return true;
		}else{
			spip_log("deleteArticle - FAILED", 'spipservice');
			return false;
		}
	}else{
		spip_log("deleteArticle - FAILED - action interdite", 'spipservice');
		return false;
	}
}

/**
 * ECRITURE
 * supprime une rubrique
 * @param $id
 * @return number
 */
function deleteRubrique($id){

	include_spip("action/supprimer");

	$autorisation = $autorisation = autoriser('supprimer', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$id));
	if (_DEBUG_SPIPSERVICE) spip_log("deleteRubrique - autoriserSupprimer : ".$autorisation, 'spipservice');

	if ($autorisation){
		action_supprimer_rubrique(array('rubrique-'.$id, 'rubrique', $id));
		spipServiceLog('supprimer', 'rubrique', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}else{
		spip_log("deleteRubrique - FAILED - action interdite", 'spipservice');
		return false;
	}
}

/**
 * ECRITURE
 * supprime un logo
 * @param $id (article/rubrique/breve)
 * @param $objet type d'entité dont on efface le logo [article/rubrique/breve]
 * @return number
 */
function deleteLogo($id, $objet){

	if ($id>0){

		include_spip('action/iconifier');

		$autorisation = autoriser('iconifier', 'spipservice', 0, NULL, array('type'=>$objet, 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("deleteLogo[".$objet."] - autoriserIconifier : ".$autorisation, 'spipservice');

		if ($autorisation){
			$type_court = "";
			if ($objet=='article')
			$type_court = "art";
			if ($objet=='rubrique')
			$type_court = "rub";
			if ($objet=='breve')
			$type_court = "breve";
			$fileName = getLogoFileName($id, $type_court);
			if ($fileName){
				action_spip_image_effacer_dist($fileName);
				$success = true;
			}else{
				spip_log("deleteLogo - FAILED - fichier inexistant [".$id.", ".$type_court."]", 'spipservice');
				$success = false;
			}
		}else{
			spip_log("deleteLogo - FAILED - action interdite", 'spipservice');
			$success = false;
		}
	}else{
		spip_log("deleteLogo - FAILED - id NULL or <=0", 'spipservice');
		$success = false;
	}
	if ($success){
		spipServiceLog('supprimer', 'logo_'.$objet, $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * supprime un document
 * @param $id
 * @return number
 */
function deleteDocument($id, $id_objet, $objet){

	if ($id>0){

		$autorisation = autoriser('documenter', 'spipservice', 0, NULL, array('type'=>$objet, 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("deleteDocument - autoriserSupprimer : ".$autorisation, 'spipservice');

		if ($autorisation){
			/****** CODE SPIP - ecrire/action/documenter.php - supprimer_lien_document() ****/
			if (!$id_document = intval($id))
			return false;

			// D'abord on ne supprime pas, on dissocie
			$nbSupp = sql_delete("spip_documents_liens",
			$z = "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet)." AND id_document=".$id_document);

			if ($nbSupp>0){
					
				// Si c'est une vignette, l'eliminer du document auquel elle appartient
				sql_updateq("spip_documents", array('id_vignette' => 0), "id_vignette=".$id_document);

				pipeline('post_edition',
				array(
			'args' => array(
				'operation' => 'delier_document',
				'table' => 'spip_documents',
				'id_objet' => $id_document,
				'objet' => $objet,
				'id' => $id_objet
				),
			'data' => null
				)
				);
				/****** FIN CODE SPIP - ecrire/action/documenter.php - supprimer_lien_document() ****/
				/****** CODE SPIP - ecrire/action/supprimer_document.php - action_supprimer_document_dist() ****/
				include_spip('inc/documents');
				if (!$doc = sql_fetsel('*', 'spip_documents', 'id_document='.$id_document))
				return false;

				spip_log("Suppression du document $id_document (".$doc['fichier'].")");

				// Si c'est un document ayant une vignette, supprimer aussi la vignette
				if ($doc['id_vignette']) {
					action_supprimer_document_dist($doc['id_vignette']);
					sql_delete('spip_documents_liens', 'id_document='.$doc['id_vignette']);
				}

				// Supprimer le fichier si le doc est local,
				// et la copie locale si le doc est distant
				if ($doc['distant'] == 'oui') {
					include_spip('inc/distant');
					if ($local = copie_locale($doc['fichier'],'test'))
					spip_unlink($local);
				}
				else spip_unlink(get_spip_doc($doc['fichier']));

				sql_delete('spip_documents', 'id_document='.$id_document);

				pipeline('post_edition',
				array(
			'args' => array(
				'operation' => 'supprimer_document',
				'table' => 'spip_documents',
				'id_objet' => $id_document
				),
			'data' => null
				)
				);
				$success = true;
			}else{
				spip_log("deleteDocument - FAILED - lien inexistant [id_document : ".$id_document." - id_objet : ".$id_objet."]", 'spipservice');
				$success = false;
			}
			/****** FIN CODE SPIP - ecrire/action/supprimer_document.php - action_supprimer_document_dist() ****/
		}else{
			spip_log("deleteDocument - FAILED - action interdite", 'spipservice');
			$success = false;
		}
	}else{
		spip_log("deleteDocument - FAILED - id NULL or <=0", 'spipservice');
		$success = false;
	}

	if ($success){
		spipServiceLog('supprimer', 'document_'.$objet, $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * vide le cache de spip (cacul de squelettes)
 * @return number
 */
function clearCache(){

	include_spip('inc/invalideur');

	$nbFile = purger_repertoire(_DIR_SKELS);
	if ($nbFile>0){
		if (_DEBUG_SPIPSERVICE) spip_log("clearCache - SUCCESS", 'spipservice');
		return true;
	}
	if (_DEBUG_SPIPSERVICE) spip_log("clearCache - CACHE WAS EMPTY", 'spipservice');
	return false;
}

/**
 * ECRITURE
 * vide le cache image de spip
 * @return number
 */
function clearCacheImage(){

	include_spip('inc/invalideur');

	$nbFile = purger_repertoire(_DIR_VAR,array('subdir'=>true));
	supprime_invalideurs();
	$nbFile += purger_repertoire(_DIR_CACHE);
	if ($nbFile>0){
		if (_DEBUG_SPIPSERVICE) spip_log("clearCacheImage - SUCCESS", 'spipservice');
		return true;
	}
	if (_DEBUG_SPIPSERVICE) spip_log("clearCacheImage - CACHE WAS EMPTY", 'spipservice');
	return false;
}

/**
 * ECRITURE
 * modification du statut d'un article
 * @param unknown_type $arr
 */
function instituteArticle($id, $statut, $date=NULL){

	include_spip('action/editer_article');

	$autorisation = autoriser('instituer', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$id));
	if (_DEBUG_SPIPSERVICE) spip_log("instituteArticle - autoriserInstituer : ".$autorisation, 'spipservice');
	if ($autorisation){
		if (isAvailableStatut($statut)){
			if ($date){
				$date = date('Y-m-d H:i:s', strtotime($date));
			}else{
				$date = date('Y-m-d H:i:s');
			}
			$ret = instituer_article($id, array('statut'=>$statut,'date'=>$date));
			if ($ret && $ret!=''){
				spip_log("instituteArticle - FAILED - err : ".$ret, 'spipservice');
				$success = false;
			}else{
				if (_DEBUG_SPIPSERVICE) spip_log("instituerArticle - SUCCESS", 'spipservice');
				$success = true;
			}
		}else{
			spip_log("instituteArticle - FAILED - statut not available -> ".$statut, 'spipservice');
			$success = false;
		}
	}else{
		spip_log("instituteArticle - FAILED - action interdite", 'spipservice');
	}

	if ($success){
		spipServiceLog('instituer['.$statut.'/'.$date.']', 'article', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * modification du statut d'une brève
 * @param unknown_type $arr
 */
function instituteBreve($id, $statut, $date=NULL){

	include_spip('action/editer_breve');

	$autorisation = autoriser('instituer', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$id));
	if (_DEBUG_SPIPSERVICE) spip_log("instituteBreve - autoriserInstituer : ".$autorisation, 'spipservice');
	if ($autorisation){
		if (isAvailableStatut($statut)){
			if ($date){
				$date = date('Y-m-d H:i:s', strtotime($date));
			}else{
				$date = date('Y-m-d H:i:s');
			}

			$res = sql_select("id_breve, id_rubrique, titre, date_heure, lien_titre, lien_url", "spip_breves as b", "b.id_breve=".$id);
			while ($row = sql_fetch($res)) {
				$err = revisions_breves($id, array('titre'=>$row['titre'], 'texte'=>$row['texte'], 'lien_titre'=>$row['lien_titre'], 'lien_url'=>$row['lien_url'], 'id_parent'=>$row['id_parent'], 'statut'=>$statut, 'date_heure'=>$date));
				if ($err && $err!=''){
					spip_log("instituteBreve - FAIL - institute - err -> ".$err, 'spipservice');
					$success = false;
				}else{
					if (_DEBUG_SPIPSERVICE) spip_log("instituteBreve - SUCCESS", 'spipservice');
					$success = true;
				}
			}
		}else{
			spip_log("instituteBreve - FAILED - statut not available -> ".$statut, 'spipservice');
			$success = false;
		}
	}else{
		spip_log("instituteBreve - FAILED - action interdite", 'spipservice');
	}

	if ($success){
		spipServiceLog('instituer['.$statut.'/'.$date.']', 'breve', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * creation/modification d'un article
 * @param unknown_type $arr
 */
function setArticle($id, $arr){

	include_spip('action/editer_article');

	// la methode de serialisation XML (spip_service_utils.php -> xmlIntoArray()) envoi les champs vides sous forme d'Array, on securise donc
	$arr = secureArrayFields($arr,'');
	$actionSpipService = 'modifier';
	//if (!$id) $id = $arr['id']; // si l'ID n'est pas passe en parametre on regarde dans le tableau de donnee s'il n'y est pas
	$id_parent = $arr['id_parent'];
	$titre = unformateXmlEntity($arr['titre']);
	$statut = unformateXmlEntity($arr['statut']);
	$texte = unformateXmlEntity($arr['texte']);
	$surtitre = unformateXmlEntity($arr['surtitre']);
	$soustitre = unformateXmlEntity($arr['soustitre']);
	$descriptif = unformateXmlEntity($arr['descriptif']);
	$chapo = unformateXmlEntity($arr['chapo']);

	if (!$id){
		$id = 0;
	}
	if ($id==0){
		$autorisation = autoriser('creer', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$id_parent));
		if (_DEBUG_SPIPSERVICE) spip_log("setArticle - autoriserCreer : ".$autorisation, 'spipservice');
	}else{
		$autorisation = autoriser('modifier', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("setArticle - autoriserModifier : ".$autorisation, 'spipservice');
	}

	if ($autorisation){
		// creation
		if ($id==0){
			$actionSpipService = 'inserer';
			if ($id_parent AND $GLOBALS['visiteur_session']['id_auteur']) {
				$id = insert_article($id_parent);
				if ($id){
					// cf. GROS HACK ecrire/inc/getdocument
					// rattrapper les documents associes a cet article nouveau
					// ils ont un id = 0-id_auteur
					sql_updateq("spip_documents_liens", array("id_objet" => $id), array("id_objet = ".(0-$id_auteur),"objet='article'"));
					if (_DEBUG_SPIPSERVICE) spip_log("setArticle - creation SUCCESS", 'spipservice');
					$success = true;
				}else{
					spip_log("setArticle - FAILED - create", 'spipservice');
					$success = false;
				}
			}else{
				if (!$id_parent) spip_log("setArticle - FAILED - create - err -> id_parent is not set", 'spipservice');
				if (!$GLOBALS['visiteur_session']['id_auteur']) spip_log("setArticle - FAILED - create - err -> GLOBALS['visiteur_session']['id_auteur'] is not set", 'spipservice');
				$success = false;
			}
		}
		// modification
		// Enregistre l'envoi dans la BD
		if ($id > 0){
			$err = articles_set($id, array('surtitre'=>$surtitre, 'titre'=>$titre, 'soustitre'=>$soustitre, 'descriptif'=>$descriptif, 'nom_site'=>'', 'url_site'=>'', 'chapo'=>$chapo, 'texte'=>$texte, 'ps'=>''));
			if ($err && $err!=''){
				spip_log("setArticle - FAIL - modification - err -> ".$err, 'spipservice');
				$success = false;
			}else{
				if (_DEBUG_SPIPSERVICE) spip_log("setArticle - SUCCESS - modification", 'spipservice');
				$success = true;
			}
		}else{
			spip_log("setArticle - FAIL - modification - err -> id is NULL OR insertion FAILED", 'spipservice');
			$success = false;
		}

	}else{
		spip_log("setArticle - FAILED - action interdite", 'spipservice');
		$success = false;
	}

	if ($success){
		spipServiceLog($actionSpipService, 'article', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * creation/modification d'une brève
 * @param unknown_type $arr
 */
function setBreve($id, $arr){

	include_spip('action/editer_breve');

	// la methode de serialisation XML (spip_service_utils.php -> xmlIntoArray()) envoi les champs vides sous forme d'Array, on securise donc
	$arr = secureArrayFields($arr,'');
	$actionSpipService = 'modifier';
	//if (!$id) $id = $arr['id']; // si l'ID n'est pas passe en parametre on regarde dans le tableau de donnee s'il n'y est pas
	$id_parent = $arr['id_parent'];
	$titre = unformateXmlEntity($arr['titre']);
	$statut = unformateXmlEntity($arr['statut']);
	$texte = unformateXmlEntity($arr['texte']);
	$lienTitre = unformateXmlEntity($arr['lien_titre']);
	$lienUrl = unformateXmlEntity($arr['lien_url']);

	if (!$id){
		$id = 0;
	}
	if ($id==0){
		$autorisation = autoriser('creer', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$id_parent));
		if (_DEBUG_SPIPSERVICE) spip_log("setBreve - autoriserCreer : ".$autorisation, 'spipservice');
	}else{
		$autorisation = autoriser('modifier', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("setBreve - autoriserModifier : ".$autorisation, 'spipservice');
	}

	if ($autorisation){
		// creation
		if ($id==0){
			$actionSpipService = 'inserer';
			if ($id_parent AND $GLOBALS['visiteur_session']['id_auteur']) {
				$id = insert_breve($id_parent);
				if ($id){
					revisions_breves($id_breve);
					if (_DEBUG_SPIPSERVICE) spip_log("setBreve - creation SUCCESS", 'spipservice');
					$success = true;
				}else{
					spip_log("setBreve - FAILED - create", 'spipservice');
					$success = false;
				}
			}else{
				if (!$id_parent) spip_log("setBreve - FAILED - create - err -> id_parent is not set", 'spipservice');
				if (!$GLOBALS['visiteur_session']['id_auteur']) spip_log("setBreve - FAILED - create - err -> GLOBALS['visiteur_session']['id_auteur'] is not set", 'spipservice');
				$success = false;
			}
		}
		// modification
		// Enregistre l'envoi dans la BD
		if ($id > 0){

			$err = revisions_breves($id, array('titre'=>$titre, 'texte'=>$texte, 'lien_titre'=>$lienTitre, 'lien_url'=>$lienUrl, 'id_parent'=>$idParent, 'statut'=>$statut));

			if ($err && $err!=''){
				spip_log("setBreve - FAIL - modification - err -> ".$err, 'spipservice');
				$success = false;
			}else{
				if (_DEBUG_SPIPSERVICE) spip_log("setBreve - SUCCESS - modification", 'spipservice');
				$success = true;
			}
		}else{
			spip_log("setBreve - FAIL - modification - err -> id is NULL OR insertion FAILED", 'spipservice');
			$success = false;
		}

	}else{
		spip_log("setBreve - FAILED - action interdite", 'spipservice');
		$success = false;
	}

	if ($success){
		spipServiceLog($actionSpipService, 'breve', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * creation/modification d'une rubrique
 * @param unknown_type $arr
 */
function setRubrique($id, $arr){

	include_spip('action/editer_rubrique');

	// la methode de serialisation XML (spip_service_utils.php -> xmlIntoArray()) envoi les champs vides sous forme d'Array, on securise donc
	$arr = secureArrayFields($arr,'');
	$actionSpipService = 'modifier';
	//if (!$id) $id = $arr['id']; // si l'ID n'est pas passe en parametre on regarde dans le tableau de donnee s'il n'y est pas
	$id_parent = $arr['id_parent'];
	$titre = unformateXmlEntity($arr['titre']);
	$descriptif = unformateXmlEntity($arr['descriptif']);
	$texte = unformateXmlEntity($arr['texte']);

	if (!$id){
		$id = 0;
	}
	if ($id==0){
		$autorisation = autoriser('creer', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$id_parent));
		if (_DEBUG_SPIPSERVICE) spip_log("setRubrique - autoriserCreer : ".$autorisation, 'spipservice');
	}else{
		$autorisation = autoriser('modifier', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("setRubrique - autoriserModifier : ".$autorisation, 'spipservice');
	}

	if ($autorisation){
		// creation
		if ($id==0){
			$actionSpipService = 'inserer';
			$id = insert_rubrique($id_parent);
			if ($id){
				if (_DEBUG_SPIPSERVICE) spip_log("setRubrique - SUCCESS - create", 'spipservice');
				$success = true;
			}else{
				spip_log("setRubrique - FAILED - create", 'spipservice');
				$success = false;
			}
		}
		// modification (pas moyen de savoir si ea crash??)
		revisions_rubriques($id, array('titre'=>$titre, 'texte'=>$texte, 'descriptif'=>$descriptif, 'extra'=>'','id_parent'=>$id_parent, 'confirme_deplace'=>'oui'));
		if (_DEBUG_SPIPSERVICE) spip_log("setRubrique - SUCCESS - modification", 'spipservice');
		$success = true;
	}else{
		spip_log("setRubrique - FAILED - action interdite", 'spipservice');
		$success = false;
	}

	if ($success){
		spipServiceLog($actionSpipService, 'rubrique', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * modification des champs ('titre', 'descriptif') d'un document
 * @param $id
 * @param $arr
 */
function setDocument($id, $arr){

	include_spip('inc/modifier');

	// la methode de serialisation XML (spip_service_utils.php -> xmlIntoArray()) envoi les champs vides sous forme d'Array, on securise donc
	$arr = secureArrayFields($arr,'');
	//if (!$id) $id = $arr['id']; // si l'ID n'est pas passe en parametre on regarde dans le tableau de donnee s'il n'y est pas
	$id_parent = $arr['id_parent'];
	$titre = unformateXmlEntity($arr['titre']);
	$descriptif = unformateXmlEntity($arr['descriptif']);

	if ($id>0){
		$args = array('type'=>'document', 'id'=>$id);
		$autorisation = autoriser('modifier', 'spipservice', 0, NULL, $args);
		if (_DEBUG_SPIPSERVICE) spip_log("setDocument - autoriserModifier : ".$autorisation, 'spipservice');

		if ($autorisation){
			// modification (pas moyen de savoir si ea crash??)
			revision_document($id, array('titre'=>$titre, 'descriptif'=>$descriptif));
			if (_DEBUG_SPIPSERVICE) spip_log("setDocument - SUCCESS - modification", 'spipservice');
			$success = true;
		}else{
			spip_log("setDocument - FAILED - action interdite", 'spipservice');
			$success = false;
		}
	}else{
		spip_log("setDocument - FAILED - id NULL or <=0", 'spipservice');
		$success = false;
	}

	if ($success){
		spipServiceLog($actionSpipService, 'rubrique', $id, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

/**
 * ECRITURE
 * ajoute un document [$_FILE] e un article
 * @param unknown_type $id
 * @param unknown_type $fileName
 * @param unknown_type $base64BinaryData
 */
function addDocumentArticle($id, $fileName, $base64BinaryData, $is_logo=FALSE){
	return addDocument($id, $fileName, $base64BinaryData, 'article', $is_logo);
}

/**
 * ECRITURE
 * ajoute un document [$_FILE] e une brève
 * @param unknown_type $id
 * @param unknown_type $fileName
 * @param unknown_type $base64BinaryData
 */
function addDocumentBreve($id, $fileName, $base64BinaryData, $is_logo=FALSE){
	return addDocument($id, $fileName, $base64BinaryData, 'breve', $is_logo);
}


/**
 * ECRITURE
 * ajoute un document [$_FILE] e une rubrique
 * @param unknown_type $id
 * @param unknown_type $fileName
 * @param unknown_type $base64BinaryData
 */
function addDocumentRubrique($id, $fileName, $base64BinaryData, $is_logo=FALSE){
	return addDocument($id, $fileName, $base64BinaryData, 'rubrique', $is_logo);
}

/**
 * ECRITURE
 * ajoute un document [$_FILE] a une entité
 * @param unknown_type $id
 * @param unknown_type $fileName
 * @param unknown_type $base64BinaryData
 * @param Enum[rubrique,article,breve] $type
 */
function addDocument($id, $fileName, $base64BinaryData, $type, $is_logo=FALSE){

	include_spip('action/joindre');
	include_spip('action/iconifier');

	if ($is_logo){
		$autorisation = autoriser('iconifier', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("addDocument[".$type."] - autoriserIconifier : ".$autorisation, 'spipservice');
	}else{
		$autorisation = autoriser('documenter', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id));
		if (_DEBUG_SPIPSERVICE) spip_log("addDocument[".$type."] - autoriserModifier : ".$autorisation, 'spipservice');
	}

	if ($autorisation){

		$data=base64_decode($base64BinaryData);
		//$data=base64_decode(chunk_split($base64BinaryData));
		//spip_log("addDocument[".$type."] - SHA-1 -> ".sha1($data), 'spipservice');
		$path='/tmp/'.$fileName;
		$fp=fopen($path,'w+');
		if($fp){
			fwrite($fp,$data);
			fclose($fp);
			if (_DEBUG_SPIPSERVICE) spip_log("addDocument[".$type."] - SUCCESS - write temp file - ".strlen($data)." written", 'spipservice');

			if ($is_logo){
				$typeCourt = "";
				if ($type=='article'){
					$typeCourt = "art";
				}else if ($type=='rubrique'){
					$typeCourt = "rub";
				}else if ($type=='breve'){
					$typeCourt = "breve";
				}
				// si un logo existe déjà, on le supprime
				$deleteLogoSuccess = true;
				if (getLogoFileName($id, $typeCourt)){
					$deleteLogoSuccess = deleteLogo($id, $type);
				}
				if ($deleteLogoSuccess){
					if ($typeCourt){
						action_spip_image_ajouter_dist($typeCourt."on".$id, true, array('name' => $fileName,
					 'tmp_name' => $path));
						$logoSuccess = true;
					}else{
						$logoSuccess = false;
						spip_log("addDocument[".$type."] - FAILED - type d'entité non pris en charge ['".$type."']", 'spipservice');
					}

				}else{
					$logoSuccess = false;
					spip_log("addDocument[".$type."] - FAILED - impossible de supprimer le logo déjà existant", 'spipservice');
				}
			}else{
				$id_document = joindre_documents(array(
				array('name' => $fileName,
					 'tmp_name' => $path)
				), 'document', $type, $id, $id_document_parent_for_vignette,
				$hash, $redirect, $actifs, $iframe_redirect);
			}

			if ($id_document>0 || $logoSuccess){
				if (_DEBUG_SPIPSERVICE) spip_log("addDocument[".$type."] - SUCCESS", 'spipservice');
				$success = true;
			}else{
				spip_log("addDocument[".$type."] - FAILED - ajouter_un_document()", 'spipservice');
			}
		}else{
			spip_log("addDocument[".$type."] - FAILED - write temp file - could not open in [w+] path : ".$path, 'spipservice');
		}
	}else{
		spip_log("addDocument[".$type."] - FAILED - action interdite", 'spipservice');
		$success = false;
	}
	if ($success){
		if ($is_logo)
		spipServiceLog('inserer', 'logo_'.$type, NULL, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		else
		spipServiceLog('inserer', 'document_'.$type, $id_document, $GLOBALS['visiteur_session']['id_auteur'], date('Y-m-d H:i:s'));
		return true;
	}
	return false;
}

function getAuteurLoggedIn(){
	$response = "<auteur>";
	$response .= getXMLAuteur($GLOBALS['visiteur_session']);
	$response .= '</auteur>';
	return $response;
}

/**
 * LECTURE
 * recupere les articles/breves ayant le statut specifie
 * @param unknown_type $statut
 */
function getByStatut($statut, $types){
	$response = '';
	if ($statut && $statut!=''){
		if (!$types || $types=='' ||  stristr($types, 'article')){
			// recuperation des articles
			$res = sql_select("id_article, id_rubrique, titre, statut, date", "spip_articles", "statut like '".$statut."'");
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$row['id_article']))){
					$response .= "<article>";
					$response .= getXMLArticle($row, false, true, true);
					$response .= "</article>";
				}
			}
		}

		if (!$types || $types=='' ||  stristr($types, 'breve')){
			// recuperation des breves
			$res = sql_select("id_breve, id_rubrique, titre, statut, date_heure", "spip_breves", "statut like '".$statut."'");
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$row['id_breve']))){
					$response .= "<breve>";
					$response .= getXMLBreve($row, false, true);
					$response .= "</breve>";
				}
			}
		}
	}else{
		$response ="<error>statut non specifie</error>";
	}
	return $response;
}

/**
 * LECTURE
 * recupere les articles/rubriques ayant pour auteur, l'auteur specifie
 * @param unknown_type $auteur - le nom de l'auteur
 */
function getByAuteur($auteur, $types){
	$response = '';
	if ($auteur && $auteur!=''){
		if (!$types || $types=='' ||  stristr($types, 'rubrique')){
			// recuperation des rubriques
			$res = sql_select("r.id_rubrique, r.id_parent, r.titre, r.statut",
			array(
			"spip_rubriques AS r",
			"spip_auteurs AS aut",
	        "spip_auteurs_rubriques AS lien"
	        ),
	        array(
	        "aut.nom like'%".$auteur."%'",
	        "lien.id_auteur = aut.id_auteur",
	        "r.id_rubrique = lien.id_rubrique"
	        ));
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$row['id_rubrique']))){
					$response .= "<rubrique>";
					$response .= getXMLRubrique($row, false, true, true);
					if ($recurse){
						$response .= getChildren($row['id_rubrique'], $recurse);
					}
					$response .= "</rubrique>";
				}
			}
		}
		if (!$types || $types=='' ||  stristr($types, 'article')){
			// recuperation des articles
			$res = sql_select("a.id_article, a.id_rubrique, a.titre, a.statut, a.date",
			array(
			"spip_articles AS a",
			"spip_auteurs AS aut",
	        "spip_auteurs_articles AS lien"
	        ),
	        array(
	        "aut.nom like'%".$auteur."%'",
	        "lien.id_auteur = aut.id_auteur",
	        "a.id_article = lien.id_article",
	        "a.statut not like 'poubelle'"
	        ));
	        while ($row = sql_fetch($res)) {
	        	if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$row['id_article']))){
	        		$response .= "<article>";
	        		$response .= getXMLArticle($row, false, true, true);
	        		$response .= "</article>";
	        	}
	        }
		}
	}else{
		$response ="<error>auteur non specifie</error>";
	}
	return $response;
}

/**
 * LECTURE
 * recherche les rubriques/articles/breve dont le titre contient le critere de recherche
 * @param unknown_type $statut - au moins 3 caracteres
 */
function search($search, $types){
	$response = '';
	if ($search && strlen($search)>=3){

		if (!$types || $types=='' ||  stristr($types, 'rubrique')){
			// recuperation des rubriques
			$res = sql_select("id_rubrique, id_parent, titre, statut", "spip_rubriques", "titre like '%".$search."%'");
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$row['id_rubrique']))){
					$response .= "<rubrique>";
					$response .= getXMLRubrique($row, false, true, true);
					if ($recurse){
						$response .= getChildren($row['id_rubrique'], $recurse);
					}
					$response .= "</rubrique>";
				}

			}
		}

		if (!$types || $types=='' ||  stristr($types, 'article')){
			// recuperation des articles
			$res = sql_select("id_article, id_rubrique, titre, statut, date", "spip_articles", "titre like '%".$search."%' and statut not like 'poubelle'");
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$row['id_article']))){
					$response .= "<article>";
					$response .= getXMLArticle($row, false, true, true);
					$response .= "</article>";
				}
			}
		}

		if (!$types || $types=='' ||  stristr($types, 'breve')){
			// recuperation des breves
			$res = sql_select("id_breve, id_rubrique, titre, statut, date_heure", "spip_breves", "titre like '%".$search."%' and statut not like 'poubelle'");
			while ($row = sql_fetch($res)) {
				if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$row['id_breve']))){
					$response .= "<breve>";
					$response .= getXMLBreve($row, false, true);
					$response .= "</breve>";
				}
			}
		}
	}else{
		$response ="<error>la recherche necessite au moins 3 caracteres [valeur : ".$search."]</error>";
	}
	return $response;
}

/**
 * LECTURE
 * recupere les enfants d'un noeud [0 = racine]
 */
function getChildren($idParent, $recurse, $documents=FALSE){
	if (!$idParent) $idParent=0; // recupere les rubriques racines
	$response = '';
	// recuperation des rubriques
	$res = sql_select("id_rubrique, id_parent, titre, statut", "spip_rubriques", "id_parent=$idParent");
	while ($row = sql_fetch($res)) {
		if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$row['id_rubrique']))){
			$response .= "<rubrique>";
			$response .= getXMLRubrique($row, $documents, true, true);
			if ($recurse){
				$response .= getChildren($row['id_rubrique'], $recurse);
			}
			$response .= "</rubrique>";
		}

	}
	// ni article, ni breve a la racine du site
	if ($idParent!=0){
		// recuperation des articles
		$res = sql_select("id_article, id_rubrique, titre, statut, date", "spip_articles", "id_rubrique=$idParent and statut not like 'poubelle'");
		while ($row = sql_fetch($res)) {
			if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$row['id_article']))){
				$response .= "<article>";
				$response .= getXMLArticle($row, $documents, true, true);
				$response .= "</article>";
			}
		}
		// recuperation des breves
		$res = sql_select("id_breve, id_rubrique, titre, statut, date_heure", "spip_breves", "id_rubrique=$idParent and statut not like 'poubelle'");
		while ($row = sql_fetch($res)) {
			if (autoriser('voir', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$row['id_breve']))){
				$response .= "<breve>";
				$response .= getXMLBreve($row, $documents, true);
				$response .= "</breve>";
			}
		}
	}
	return $response;
}

/**
 * LECTURE
 * recupere les donnees d'une rubrique
 * @param $id
 * @return une representation String du XML des donnees
 */
function getRubriqueData($id, $documents=false){
	$response = '';
	$res = sql_select("id_rubrique, id_parent, id_secteur, titre, descriptif, texte, statut, maj", "spip_rubriques", "id_rubrique=$id");
	while ($row = sql_fetch($res)) {
		$response .= "<rubrique>";
		$response .= getXMLRubrique($row, $documents, true, true);
		$response .= '</rubrique>';
	}
	return $response;

}

/**
 * LECTURE
 * recupere les donnees d'un article
 * @param $id
 * @return une representation String du XML des donnees
 */
function getArticleData($id, $documents=false){
	$response = '';
	$res = sql_select("id_article, id_rubrique, titre, statut, texte, surtitre, soustitre, descriptif, chapo, maj, date", "spip_articles as a", "a.id_article=".$id);
	while ($row = sql_fetch($res)) {
		$response .= "<article>";
		$response .= getXMLArticle($row, $documents, true, true);
		$response .= '</article>';
	}
	return $response;

}

/**
 * LECTURE
 * recupere les donnees d'une brève
 * @param $id
 * @return une representation String du XML des donnees
 */
function getBreveData($id, $documents=false){
	$response = '';
	$res = sql_select("id_breve, id_rubrique, titre, texte, statut, date_heure, maj, lien_titre, lien_url", "spip_breves as b", "b.id_breve=".$id);
	while ($row = sql_fetch($res)) {
		$response .= "<breve>";
		$response .= getXMLBreve($row, $documents, true);
		$response .= '</breve>';
	}
	return $response;

}

/**
 * LECTURE
 * recupere les donnees d'un document
 * @param $id
 * @return une representation String du XML des donnees
 */
function getDocumentData($id){
	$response = '';
	$res = sql_select("id_document, id_vignette, mode, fichier, extension, titre, date, descriptif, fichier, taille",
	"spip_documents", 
	"id_document=".$id);
	while ($row = sql_fetch($res)) {
		$response .= "<document>";
		$response .= getXMLDocument($row);
		$response .= '</document>';
	}
	return $response;

}

/**
 * LECTURE
 * calcul et renvoi les permissions de l'article/breve/rubrique sous forme XML
 * @param $id
 * @param $type enum['rubrique','article','breve']
 */
function getPermissions($id, $type){
	$response = '';

	// documenter
	$response .= '<permission_documenter_'.$type.'>'.((autoriser('documenter', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_documenter_'.$type.'>';
	// instituer
	$response .= '<permission_instituer_'.$type.'>'.((autoriser('instituer', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_instituer_'.$type.'>';
	// voir
	$response .= '<permission_voir_'.$type.'>'.((autoriser('voir', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_voir_'.$type.'>';
	// supprimer
	$response .= '<permission_supprimer_'.$type.'>'.((autoriser('supprimer', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_supprimer_'.$type.'>';
	// modifier
	$response .= '<permission_modifier_'.$type.'>'.((autoriser('modifier', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_modifier_'.$type.'>';
	// iconifier
	$response .= '<permission_iconifier_'.$type.'>'.((autoriser('iconifier', 'spipservice', 0, NULL, array('type'=>$type, 'id'=>$id)))?'true':'false').'</permission_iconifier_'.$type.'>';

	if ($type=='rubrique'){
		// creer article
		$response .= '<permission_ajouter_article_'.$type.'>'.((autoriser('creer', 'spipservice', 0, NULL, array('type'=>'article', 'id'=>$id)))?'true':'false').'</permission_ajouter_article_'.$type.'>';
		// creer breve
		$response .= '<permission_ajouter_breve_'.$type.'>'.((autoriser('creer', 'spipservice', 0, NULL, array('type'=>'breve', 'id'=>$id)))?'true':'false').'</permission_ajouter_breve_'.$type.'>';
		// creer rubrique
		$response .= '<permission_ajouter_rubrique_'.$type.'>'.((autoriser('creer', 'spipservice', 0, NULL, array('type'=>'rubrique', 'id'=>$id)))?'true':'false').'</permission_ajouter_rubrique_'.$type.'>';
	}

	return $response;
}

/**
 * LECTURE
 * recupere les documents associes e l'article ou la rubrique
 * @param $id
 * @param $type enum['rubrique','article','breve']
 */
function getDocuments($id, $type){
	$res = sql_select("doc.id_document, doc.fichier, doc.id_vignette, doc.mode, doc.extension, doc.titre, doc.date, doc.descriptif, doc.fichier, doc.taille",
	array(
		"spip_documents AS doc",
        "spip_documents_liens AS lien"
        ),
        array(
        "doc.id_document = lien.id_document",
        "lien.id_objet=".$id,
        "lien.objet='".$type."'"));
        while ($row = sql_fetch($res)) {
        	spip_log("one row","spipservice");
        	$response .= '<document>';
        	$response .= getXMLDocument($row);
        	$response .= '</document>';
        }
        return $response;
}

/**
 * LECTURE
 * recuperes les auteurs dune rubrique/article/breve
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getAuteurs($id, $type){
	$res = sql_select("aut.id_auteur, aut.nom, aut.email, aut.statut, aut.webmestre",
	array(
		"spip_auteurs AS aut",
        "spip_auteurs_".$type."s AS lien"
        ),
        array(
        "aut.id_auteur = lien.id_auteur",
        "lien.id_".$type."=".$id
        ));
        while ($row = sql_fetch($res)) {
        	$response .= '<auteur>';
        	$response .= getXMLAuteur($row);
        	$response .= '</auteur>';
        }
        return $response;
}

/**
 * UTILS
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getXMLArticle($row, $docs, $auteur, $perm){
	$xml = "";
	if ($row['id_article']) 	$xml .= "<id_article>".$row['id_article']."</id_article>";
	if ($row['id_rubrique'])	$xml .= "<id_parent_article>".$row['id_rubrique']."</id_parent_article>";
	if ($row['titre'])			$xml .= "<titre_article>".formateXmlEntity($row['titre'])."</titre_article>";
	if ($row['statut'])			$xml .= "<statut_article>".getCodeStatut($row['statut'])."</statut_article>";
	if ($row['texte'])			$xml .= '<texte_article>'.formateXmlEntity($row['texte']).'</texte_article>';
	if ($row['surtitre'])		$xml .= '<surtitre_article>'.formateXmlEntity($row['surtitre']).'</surtitre_article>';
	if ($row['soustitre'])		$xml .= '<soustitre_article>'.formateXmlEntity($row['soustitre']).'</soustitre_article>';
	if ($row['descriptif'])		$xml .= '<descriptif_article>'.formateXmlEntity($row['descriptif']).'</descriptif_article>';
	if ($row['chapo'])			$xml .= '<chapo_article>'.formateXmlEntity($row['chapo']).'</chapo_article>';
	if ($row['maj'])			$xml .= '<maj_article>'.$row['maj'].'</maj_article>';
	if ($row['date'])			$xml .= '<date_publi_article>'.$row['date'].'</date_publi_article>';
	if ($logoFileName=getLogoFileName($row['id_article'], 'art')){
		$xml .= "<logo_article>".url_de_base()._NOM_PERMANENTS_ACCESSIBLES.$logoFileName."</logo_article>";
	}
	if ($auteur)				$xml .= getAuteurs($row['id_article'], 'article');
	if ($docs)					$xml .= getDocuments($row['id_article'], 'article');
	if ($perm)					$xml .= getPermissions($row['id_article'], 'article');
	return $xml;
}

/**
 * UTILS
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getXMLBreve($row, $docs, $perm){
	$xml = "";
	if ($row['id_breve']) 	$xml .= "<id_breve>".$row['id_breve']."</id_breve>";
	if ($row['id_rubrique'])	$xml .= "<id_parent_breve>".$row['id_rubrique']."</id_parent_breve>";
	if ($row['titre'])			$xml .= "<titre_breve>".formateXmlEntity($row['titre'])."</titre_breve>";
	if ($row['lien_titre'])			$xml .= "<lien_titre_breve>".formateXmlEntity($row['lien_titre'])."</lien_titre_breve>";
	if ($row['lien_url'])			$xml .= "<lien_url_breve>".formateXmlEntity($row['lien_url'])."</lien_url_breve>";
	if ($row['statut'])			$xml .= "<statut_breve>".getCodeStatut($row['statut'])."</statut_breve>";
	if ($row['texte'])			$xml .= '<texte_breve>'.formateXmlEntity($row['texte']).'</texte_breve>';
	if ($row['maj'])			$xml .= '<maj_breve>'.$row['maj'].'</maj_breve>';
	if ($row['date_heure'])			$xml .= '<date_publi_breve>'.$row['date_heure'].'</date_publi_breve>';
	if ($logoFileName=getLogoFileName($row['id_breve'], 'breve')){
		$xml .= "<logo_breve>".url_de_base()._NOM_PERMANENTS_ACCESSIBLES.$logoFileName."</logo_breve>";
	}
	if ($docs)					$xml .= getDocuments($row['id_breve'], 'breve');
	if ($perm)					$xml .= getPermissions($row['id_breve'], 'breve');
	return $xml;
}

/**
 * UTILS
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getXMLRubrique($row, $docs, $auteur, $perm){
	$xml = "";
	if ($row['id_rubrique']) 	$xml .= "<id_rubrique>".$row['id_rubrique']."</id_rubrique>";
	if ($row['id_parent']) 		$xml .= "<id_parent_rubrique>".$row['id_parent']."</id_parent_rubrique>";
	if ($row['id_secteur']) 	$xml .= "<id_secteur_rubrique>".$row['id_secteur']."</id_secteur_rubrique>";
	if ($row['titre']) 			$xml .= "<titre_rubrique>".formateXmlEntity($row['titre'])."</titre_rubrique>";
	if ($row['descriptif']) 	$xml .= "<descriptif_rubrique>".formateXmlEntity($row['descriptif'])."</descriptif_rubrique>";
	if ($row['texte']) 			$xml .= "<texte_rubrique>".formateXmlEntity($row['texte'])."</texte_rubrique>";
	if ($row['statut']) 		$xml .= "<statut_rubrique>".getCodeStatut($row['statut'])."</statut_rubrique>";
	if ($row['maj']) 			$xml .= "<maj_rubrique>".$row['maj']."</maj_rubrique>";
	if ($logoFileName=getLogoFileName($row['id_rubrique'], 'rub')){
		$xml .= "<logo_rubrique>".url_de_base()._NOM_PERMANENTS_ACCESSIBLES.$logoFileName."</logo_rubrique>";
	}
	if ($auteur)				$xml .= getAuteurs($row['id_rubrique'], 'rubrique');
	if ($docs)					$xml .= getDocuments($row['id_rubrique'], 'rubrique');
	if ($perm)					$xml .= getPermissions($row['id_rubrique'], 'rubrique');
	return $xml;
}

/**
 * UTILS
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getXMLDocument($row){
	$xml = "";
	if ($row['id_document'])	$xml .= '<id_document>'.$row['id_document'].'</id_document>';
	if ($row['fichier'])		$xml .= '<fichier_document>'.$row['fichier'].'</fichier_document>';
	if ($row['extension'])		$xml .= '<extension_document>'.$row['extension'].'</extension_document>';
	if ($row['titre'])			$xml .= '<titre_document>'.formateXmlEntity($row['titre']).'</titre_document>';
	if ($row['date'])			$xml .= '<date_document>'.$row['date'].'</date_document>';
	if ($row['descriptif'])		$xml .= '<descriptif_document>'.formateXmlEntity($row['descriptif']).'</descriptif_document>';
	if ($row['fichier'])		$xml .= '<url_document>'.url_de_base()._NOM_PERMANENTS_ACCESSIBLES.$row['fichier'].'</url_document>';
	if ($row['taille'])			$xml .= '<taille_document>'.$row['taille'].'</taille_document>';
	if ($row['mode'])			$xml .= '<mode_document>'.$row['mode'].'</mode_document>';
	return $xml;
}

/**
 * UTILS
 * @param $row tableau contenant les informations
 * @return une representation XML des informations
 */
function getXMLAuteur($row){
	$xml = "";
	if ($row['id_auteur'])		$xml .= '<id_auteur>'.$row['id_auteur'].'</id_auteur>';
	if ($row['nom'])			$xml .= '<nom_auteur>'.$row['nom'].'</nom_auteur>';
	if ($row['email'])			$xml .= '<email_auteur>'.$row['email'].'</email_auteur>';
	if ($row['statut'])			$xml .= '<statut_auteur>'.$row['statut'].'</statut_auteur>';
	if ($row['webmestre'])			$xml .= '<webmestre_auteur>'.$row['webmestre'].'</webmestre_auteur>';
	return $xml;
}

/**
 * UTILS
 * recupere le nom du fichier logo de l'article ou de la rubrique ou de la breve
 * @param $id
 * @param $type enum['rub','art','breve']
 * @return le nom du fichier
 */
function getLogoFileName($id, $type){
	$dir = opendir(_DIR_LOGOS);
	while($file = readdir($dir)) {
		if($file != '.' && $file != '..' && !is_dir(_DIR_LOGOS.$file)){
			if ($file == $type.'on'.$id.''.getFileExtension($file, true)){
				closedir($dir);
				return $file;
			}
		}
	}
	closedir($dir);
	return null;
}

/**
 * inscrit en BDD l'action effectue par spipservice
 * @param unknown_type $action
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $id_auteur
 * @param unknown_type $date
 */
function spipServiceLog($action, $type, $id, $id_auteur, $date){
	$id = sql_insertq('spip_spipservice', array('id'=>$id, 'type'=>$type, 'id_auteur'=>$id_auteur, 'action'=>$action, 'date'=>$date));
}

?>