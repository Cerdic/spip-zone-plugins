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

/**
 * Insertion des donnees pour un champ
 *
 * @param unknown_type $id_form
 * @param unknown_type $id_donnee
 * @param unknown_type $champ
 * @param unknown_type $type
 * @param unknown_type $val
 * @param unknown_type $erreur
 * @param unknown_type $ok
 * @return unknown
 */
function forms_insertions_reponse_un_champ($id_form,$id_donnee,$champ,$type,$val,&$erreur,&$ok){
	$inserts = array();
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
				$inserts[] = "(".intval($id_donnee).","._q($champ).","._q($dest).")";
			}
		}
		// Cas de la mise a jour pour laquelle on dispose deja d'un fichier uploade !
		elseif ( ($val=forms_valeurs($id_donnee,$id_form,$champ)) != NULL ) {
			$inserts[] = "(".intval($id_donnee).","._q($champ).","._q($val[$champ]).")";
		}
	}
	else if (is_array($val) OR strlen($val)) {
		// Choix multiples : enregistrer chaque valeur separement
		if (is_array($val))
			foreach ($val as $v){
				if (strlen($v))
					$inserts[] = "(".intval($id_donnee).","._q($champ).","._q($v).")";
			}
		elseif (strlen($val))
			$inserts[] = "(".intval($id_donnee).","._q($champ).","._q($val).")";
	}
	return $inserts;
}


	function forms_modifier_reponse($id_form,$id_donnee,&$erreur, $c = NULL, $structure = NULL){
		if (!$structure)	$structure = forms_structure($id_form);
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
			$ins = forms_insertions_reponse_un_champ($id_form,$id_donnee,$champ,$type,$val,$erreur,$ok);
			$inserts = array_merge($inserts,$ins);
		}
		if (!count($erreur)){
			if (count($champs_mod)){
				include_spip('base/abstract_sql');
				$in_champs = calcul_mysql_in('champ',join(',',array_map('_q', $champs_mod)));
				spip_query("DELETE FROM spip_forms_donnees_champs WHERE $in_champs AND id_donnee=".intval($id_donnee));
			}
			if (count($inserts)){
				spip_query($q="INSERT INTO spip_forms_donnees_champs (id_donnee, champ, valeur) ".
					"VALUES ".join(',', $inserts));
			}
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

	
	function forms_revision_donnee($id_donnee, $c = NULL) {
		include_spip('base/abstract_sql');
		$inserts = array();
		$result = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee=".intval($id_donnee));
		if (!$row = spip_fetch_array($result)) {
			$erreur['@'] = _T("forms:probleme_technique");
		}
		$id_form = $row['id_form'];
		$structure = forms_structure($id_form);
		include_spip("inc/forms_type_champs");

		$erreur = forms_valide_conformite_champs_reponse_post($id_form, $id_donnee, $c, $structure);
		if (!$erreur)
			forms_modifier_reponse($id_form,$id_donnee,$erreur, $c, $structure);
		if (count($erreur))
			spip_log("erreur: ".serialize($erreur));

		return $erreur;
	}
	
	
	function forms_rang_prochain($id_form){
		$rang = 1;
		$res = spip_query("SELECT max(rang) AS rang_max FROM spip_forms_donnees WHERE id_form=".intval($id_form));
		if ($row = spip_fetch_array($res))
			$rang = $row['rang_max']+1;
		return $rang;
	}
	


	function forms_enregistrer_reponse_formulaire($id_form, &$id_donnee, &$erreur, &$reponse, $script_validation = 'valide_form', $script_args='', $c=NULL, $rang=NULL) {
		$r = '';
		include_spip('inc/autoriser');

		$result = spip_query("SELECT * FROM spip_forms WHERE id_form=".intval($id_form));
		if (!$row = spip_fetch_array($result)) {
			$erreur['@'] = _T("forms:probleme_technique");
		}
		$moderation = $row['moderation'];
		// Extraction des donnees pour l'envoi des mails eventuels
		//   accuse de reception et forward webmaster
		$email = unserialize($row['email']);
		$champconfirm = $row['champconfirm'];
		$mailconfirm = '';

		include_spip("inc/forms_type_champs");
		$erreur = forms_valide_champs_reponse_post($id_form, $id_donnee, $c);

		// Si tout est bon, enregistrer la reponse
		if (!$erreur) {
			global $auteur_session;
			$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
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
					spip_query("UPDATE spip_forms_donnees SET ip="._q($GLOBALS['ip']).", url="._q($url).", confirmation="._q($confirmation).", cookie="._q($cookie)." ".
						"WHERE id_donnee=".intval($id_donnee));
				} elseif (autoriser('creer', 'donnee', 0, NULL, array('id_form'=>$id_form))){
					if ($rang==NULL) $rang = array('rang'=>forms_rang_prochain($id_form));
					elseif(!is_array($rang)) $rang=array('rang'=>$rang);
					spip_query("INSERT INTO spip_forms_donnees (id_form, id_auteur, date, ip, url, confirmation,statut, cookie, "
					  . implode(',',array_keys($rang)).") "
					  .	"VALUES (".intval($id_form).","._q($id_auteur).", NOW(),"._q($GLOBALS['ip']).","
					  . _q($url).", '$confirmation', '$statut',"._q($cookie).","
					  . implode(',',array_map('_q',$rang)) .")");
					$id_donnee = spip_insert_id();
					# cf. GROS HACK inc/forms_tables_affichage
					# rattrapper les documents associes a cette nouvelle donnee
					# ils ont un id = 0-id_auteur
					spip_query("UPDATE spip_documents_donnees SET id_donnee = $id_donnee WHERE id_donnee = ".(0-$GLOBALS['auteur_session']['id_auteur']));
					# cf. GROS HACK 2 balise/forms
					# rattrapper les documents associes a cette nouvelle donnee
					# ils ont un id = 0-id_auteur
					spip_query("UPDATE spip_forms_donnees_donnees SET id_donnee = $id_donnee WHERE id_donnee = ".(0-$GLOBALS['auteur_session']['id_auteur']));
				}
				if (!($id_donnee>0)) {
					$erreur['@'] = _T("forms:probleme_technique");
					$ok = false;
				}
				else {
					$_GET["deja_enregistre_$id_form"] = $id_donnee;
				}
			}

			// Puis enregistrer les differents champs
			if ($ok) {
				#$inserts = forms_insertions_reponse_post($id_form,$id_donnee,$erreur,$ok,$c);
				if (!forms_modifier_reponse($id_form,$id_donnee,$erreur, $c)) {
					// Reponse vide => annuler
					$erreur['@'] = _T("forms:remplir_un_champ");
					$row = spip_query("SELECT * FROM spip_forms_donnees_champs WHERE id_donnee=".intval($id_donnee));
					if(!$row = spip_fetch_array($row))
						spip_query("DELETE FROM spip_forms_donnees WHERE id_donnee=".intval($id_donnee));
					$ok = false;
				}
			}
			/*if ($ok) {
				include_spip('inc/securiser_action');
				spip_query("DELETE FROM spip_forms_donnees_champs WHERE id_donnee=".intval($id_donnee));
				spip_query("INSERT INTO spip_forms_donnees_champs (id_donnee, champ, valeur) ".
					"VALUES ".join(',', $inserts));
			}*/
			if ($ok || $confirme) {
				if ($champconfirm)
					if ($row=spip_fetch_array(spip_query("SELECT * FROM spip_forms_donnees_champs WHERE id_donnee=".intval($id_donnee)." AND champ="._q($champconfirm))))
						$mailconfirm = $row['valeur'];
				if (($email) || ($mailconfirm)) {
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
