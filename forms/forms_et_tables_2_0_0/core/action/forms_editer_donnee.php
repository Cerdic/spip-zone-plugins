<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

include_spip('base/abstract_sql');
include_spip('inc/forms');

/**
 * Lister les insertions en base pour un champ de la donnee
 *
 * @param int $id_form
 * @param int $id_donnee
 * @param string $champ
 * @param string $type
 * @param unknown_type $val
 * @param string $erreur
 * @param bool $ok
 * @return array
 */
function forms_donnee_inserer_un_champ($id_form,$id_donnee,$champ,$type,$val,&$inserts,&$erreur,&$ok){
	if ($type == 'fichier') {
		if (($val = $_FILES[$champ]) AND ($val['tmp_name'])) {
			// Fichier telecharge : deplacer dans IMG, stocker le chemin dans la base
			$dir = sous_repertoire(_DIR_IMG, "protege");
			$dir = sous_repertoire($dir, "form".$id_form);
			$source = $val['tmp_name'];
			$dest = $dir.forms_nommer_fichier_form($val['name'], $dir);
			if (!forms_deplacer_fichier_form($source, $dest)) {
				$erreur[$champ] = _T("forms:probleme_technique_upload");
				$ok = false;
			}
			else {
				
				$inserts[] = array("id_donnee"=>$id_donnee, "champ"=>$champ, "valeur"=>$dest);
			}
		}
		// Cas de la mise a jour pour laquelle on dispose deja d'un fichier uploade !
		elseif ( ($val=forms_valeurs($id_donnee,$id_form,$champ)) != NULL ) {
			$inserts[] = array("id_donnee"=>$id_donnee, "champ"=>$champ, "valeur"=>$val[$champ]);
		}
	}
	else if (is_array($val) OR strlen($val)) {
		// Choix multiples : enregistrer chaque valeur separement
		if (is_array($val))
			foreach ($val as $v){
				if (strlen($v))
					$inserts[] = array("id_donnee"=>$id_donnee, "champ"=>$champ, "valeur"=>$v);
			}
		elseif (strlen($val))
			$inserts[] = array("id_donnee"=>$id_donnee, "champ"=>$champ, "valeur"=>$val);
	}
	return $inserts;
}

/**
 * Modifier une donnee. Renvoie le nombre de valeurs inserees pour la donnee
 *
 * @param int $id_form
 * @param int $id_donnee
 * @param array $erreur
 * @param array $c
 * @param array $structure
 * @return int
 */
function forms_donnee_modifier($id_form,$id_donnee,&$erreur, $c = NULL, $structure = NULL){
	if (!$structure)
		$structure = forms_structure($id_form);
	$inserts = array();
	$champs_mod = array();
	$valeurs = array();
	$champs = array();
	foreach($structure as $champ=>$infos){
		if (!$c){
			if ($infos['type'] == 'fichier' AND isset($_FILES[$champ]['tmp_name']))
				$val = $_FILES[$champ];
			else
				$val = _request($champ);
		}
		else
			$val = isset($c[$champ])?$c[$champ]:NULL;
		if ($val!==NULL
			AND (($infos['type']!=='password') OR strlen($val))){
			
			if (isset($GLOBALS['forms_table_des_filtres_edition'][$infos['type']])){
				$filtre = reset($GLOBALS['forms_table_des_filtres_edition'][$infos['type']]);
				$filtre = str_replace("%s",'$val',$filtre);
				eval("\$val = $filtre;");
			}
			$valeurs[$champ] = $val;
			$champs[$champ] = $infos;
		}
	}
	// Envoyer aux plugins
	$valeurs = pipeline('forms_pre_edition_donnee',
		array(
			'args' => array(
				'id_form' => $id_form,
				'id_donnee' => $id_donnee,
				'champs' => $champs
			),
			'data' => $valeurs
		)
	);
	foreach($valeurs as $champ=>$val){
		$champs_mod[] = $champ;
		// un plugin a pu ajouter un 'champ factice' a enregistrer, non defini dans la structure
		$type = isset($champs[$champ]['type'])?$champs[$champ]['type']:"";
		forms_donnee_inserer_un_champ($id_form,$id_donnee,$champ,$type,$val,$inserts,$erreur,$ok);
	}
	if (!count($erreur)){
		if (count($champs_mod))
			sql_delete("spip_forms_donnees_champs",sql_in("champ",$champs_mod)." AND id_donnee=".intval($id_donnee));
		if (count($inserts))
			sql_insertq_multi("spip_forms_donnees_champs",$inserts);

		// Envoyer aux plugins apres enregistrement
		$valeurs = pipeline('forms_post_edition_donnee',
			array(
				'args' => array(
					'id_form' => $id_form,
					'id_donnee' => $id_donnee,
					'champs' => $champs
				),
				'data' => $valeurs
			)
		);
	}
	return count($inserts);
}

/**
 * Revision d'une donnee
 *
 * @param int $id_donnee
 * @param array $c
 * @return array
 */
function forms_revision_donnee($id_donnee, $c = NULL) {
	include_spip('base/abstract_sql');
	$inserts = array();
	if (!$row = sql_fetsel("id_form","spip_forms_donnees","id_donnee=".intval($id_donnee))
	 OR !$id_form = $row['id_form']
	 OR !$structure = forms_structure($id_form)
	 )
		return array('message_erreur' => _T("forms:probleme_technique"));

	include_spip("inc/forms_type_champs");
	$erreur = forms_valide_conformite_champs_reponse_post($id_form, $id_donnee, $c, $structure);

	if (!$erreur)
		forms_donnee_modifier($id_form,$id_donnee,$erreur, $c, $structure);
	if (count($erreur))
		spip_log("erreur: ".serialize($erreur));

	return $erreur;
}
	

/**
 * Determinier le rang de la prochaine donnee
 *
 * @param unknown_type $id_form
 * @return unknown
 */
function forms_donnee_prochain_rang($id_form){
	$rang = 1;
	if ($row = sql_fetsel("max(rang) AS rang_max","spip_forms_donnees","id_form=".intval($id_form)))
		$rang = $row['rang_max']+1;
	return $rang;
}
	

/**
 * Enregistrer la saisie d'un formulaire
 *
 * @param int $id_form
 * @param int $id_donnee
 * @param array $erreur
 * @param string $reponse
 * @param string $script_validation
 * @param string $script_args
 * @param array $c
 * @param int $rang
 * @return string
 */
function forms_enregistrer_reponse_formulaire($id_form, &$id_donnee, &$erreur, &$reponse, $script_validation = 'valide_form', $script_args='', $c=NULL, $rang=NULL) {
	$r = '';
	if (!is_array($erreur)) $erreur = array();
	include_spip('inc/autoriser');

	if (!$row = sql_fetsel("*","spip_forms","id_form=".intval($id_form))) {
		$erreur['message_erreur'] = _T("forms:probleme_technique");
	}
	$moderation = $row['moderation'];
	// Extraction des donnees pour l'envoi des mails eventuels
	//   accuse de reception et forward webmaster
	$email = unserialize($row['email']);
	$champconfirm = $row['champconfirm'];
	$mailconfirm = '';

	include_spip("inc/forms_type_champs");
	$erreur = array_merge($erreur,
	  forms_valide_champs_reponse_post($id_form, $id_donnee, $c));

	// Si tout est bon, enregistrer la reponse
	if (!count($erreur)) {
		$id_auteur = isset($GLOBALS['visiteur_session']['id_auteur']) ? intval($GLOBALS['visiteur_session']['id_auteur']) : 0;
		$url = (_DIR_RESTREINT==_DIR_RESTREINT_ABS)?parametre_url(self(),'id_form',''):_DIR_RESTREINT_ABS;
		if ($id_donnee<0) $url = parametre_url($url,'id_donnee','');
		$ok = true;
		$confirme = false;
		$id = _request("deja_enregistre_$id_form", $c);
		if ($id = intval($id)){
			$id_donnee = $id;
			$ok = false;
			$confirme = true;
		}

		$nom_cookie = forms_nom_cookie_form($id_form);
		if (isset($_COOKIE[$nom_cookie]))
			$cookie = $_COOKIE[$nom_cookie];
		else {
			include_spip("inc/acces");
			$cookie = creer_uniqid();
		}
		if ($row['type_form']=='sondage')
			$confirmation = 'attente';
		else
			$confirmation = 'valide';
		if ($moderation == 'posteriori')
			$statut='publie';
		else {
			$statut = 'prop';
			foreach(array('prepa','prop','publie','refuse') as $s)
				if (autoriser(
						'instituer',
						(in_array($row['type_form'],array('','sondage'))?'form':$row['type_form']).'_donnee',
						0,NULL,array('id_form'=>$id_form,'statut'=>'prepa','nouveau_statut'=>$s))){
					$statut = $s;
					break;
				}
		}
		// D'abord creer la reponse dans la base de donnees
		if ($ok) {
			if ($id_donnee>0 AND autoriser('modifier', 'donnee', $id_donnee, NULL, array('id_form'=>$id_form))){
				sql_updateq("spip_forms_donnees",
				  array("ip"=>$GLOBALS['ip'],"url"=>$url,"confirmation"=>$confirmation,"cookie"=>$cookie),
				  "id_donnee=".intval($id_donnee));
			} elseif (autoriser('creer', 'donnee', 0, NULL, array('id_form'=>$id_form))){
				if ($rang==NULL) $rang = array('rang'=>forms_donnee_prochain_rang($id_form));
				elseif(!is_array($rang)) $rang=array('rang'=>$rang);
				$id_donnee = sql_insertq("spip_forms_donnees",
					array_merge(
					 array(
					   "id_form"=>$id_form,
					   "id_auteur"=>$id_auteur,
					   "date"=>"NOW()",
					   "ip"=>$GLOBALS['ip'],
					   "url"=>$url,
					   "confirmation"=>$confirmation,
					   "statut"=>$statut,
					   "cookie"=>$cookie),
					 $rang)
					 );
				# cf. GROS HACK inc/forms_tables_affichage
				# rattrapper les documents associes a cette nouvelle donnee
				# ils ont un id = 0-id_auteur
				sql_updateq("spip_documents_donnees",array("id_donnee"=>$id_donnee),"id_donnee = ".(0-intval($GLOBALS['auteur_session']['id_auteur'])));
				# cf. GROS HACK 2 balise/forms
				# rattrapper les donnees associes a cette nouvelle donnee
				# ils ont un id = 0-id_auteur
				sql_updateq("spip_forms_donnees_donnees",array("id_donnee"=>$id_donnee),"id_donnee = ".(0-intval($GLOBALS['auteur_session']['id_auteur'])));
			}
			if (!($id_donnee>0)) {
				$erreur['message_erreur'] = _T("forms:probleme_technique");
				$ok = false;
			}
			else {
				$_GET["deja_enregistre_$id_form"] = $id_donnee;
			}
		}

		// Puis enregistrer les differents champs
		if ($ok) {
			if (!forms_donnee_modifier($id_form,$id_donnee,$erreur, $c)) {
				// Reponse vide => annuler
				$erreur['message_erreur'] = _T("forms:remplir_un_champ");
				if(!sql_countsel("spip_forms_donnees_champs","id_donnee=".intval($id_donnee)))
					sql_delete("spip_forms_donnees","id_donnee=".intval($id_donnee));
				$ok = false;
			}
		}
		if ($ok || $confirme) {
			if ($champconfirm
			  AND $row=sql_fetsel("valeur","spip_forms_donnees_champs","id_donnee=".intval($id_donnee)." AND champ=".sql_quote($champconfirm)))
					$mailconfirm = $row['valeur'];
			if (
				  (is_array($email) AND strlen(reset($email)))
				  OR ($mailconfirm)
				) {
				include_spip("inc/session");
				$hash = md5("forms confirme reponse $id_donnee $cookie ".hash_env());
				$url = generer_url_public($script_validation,"mel_confirm=oui&id_donnee=$id_donnee&hash=$hash".($script_args?"&$script_args":""));
				$r = $url;
				if ($mailconfirm) $reponse = $mailconfirm;
			}
			if ($row['type_form']=='sondage') {
				include_spip("inc/session");
				$hash = md5("forms valide reponse sondage $id_donnee $cookie ".hash_env());
				$url = generer_url_public($script_validation,"verif_cookie=oui&id_donnee=$id_donnee&hash=$hash".($script_args?"&$script_args":""));
				$r = $url;
			}
		}
	}
	return $r;
}

?>