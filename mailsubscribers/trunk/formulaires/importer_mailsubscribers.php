<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 C�dric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('inc/session');
include_spip('inc/mailsubscribers');

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_importer_mailsubscribers_charger_dist(){
	$valeurs = array(
		'file_import' => '',
		'valid_subscribers' => '',
		'listes_import_subscribers' => '',
		'desactiver_notif' => '',
		'vider_table' => '',
	);

	$valeurs['_listes_dispo'] = mailsubscribers_listes();
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_importer_mailsubscribers_verifier_dist(){
	$erreurs = array();
	if (_request('go')){
		$filename=session_get('importer_mailsubscribers::filename');
	}
	else {
		$files = importer_mailsubscribers_file();
		if (is_string($files)) // erreur
			$erreurs['file_import'] = $files;
		else {
			$files = reset($files);
			$filename = _DIR_TMP.basename($files['tmp_name']);
			move_uploaded_file($files['tmp_name'], $filename);
			session_set('importer_mailsubscribers::filename',$filename);
		}
	}

	if (!$filename){
		$erreurs['file_import'] = _T('info_obligatoire');
	}
	elseif (!_request('go')){
		$importer_csv = charger_fonction("importer_csv","inc");
		$test = importer_mailsubscribers_data($filename);
		$head = array_keys(reset($test));

		$erreurs['test'] = "";
		if (in_array("statut",$head) AND in_array("listes",$head)){
			$erreurs['test'] .= "<p class='notice'>"._T('mailsubscriber:texte_avertissement_import')."</p>";
		}
		$erreurs['test'] .= "|{{".implode("}}|{{",$head)."}}|\n";
		$nbmax = 10;
		$count = count($test);
		while ($row = array_shift($test) AND $nbmax--){
			$erreurs['test'].="|".implode("|",$row)."|\n";
		}
		$erreurs['test'] .= "\n";
		$erreurs['test'] .= "<p class='explication'>{{".singulier_ou_pluriel($count,'mailsubscriber:info_1_adresse_a_importer','mailsubscriber:info_nb_adresses_a_importer')."}}</p>";

		if (!in_array("statut",$head)){
			$erreurs['demander_statut'] = ' ';
		}
		if (!in_array("listes",$head)){
			$erreurs['demander_listes'] = ' ';
		}
	}

	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_importer_mailsubscribers_traiter_dist(){
	refuser_traiter_formulaire_ajax();// pour recharger toute la page

	if (_request('desactiver_notif'))
		$GLOBALS['notification_instituermailsubscriber_status'] = false; // pas de notification pour cet import
	if (_request('vider_table') AND autoriser('detruire')){
		include_spip('base/abstract_sql');
		sql_delete("spip_mailsubscribers");
	}

	$res = array('editable'=>true);
	$options = array();
	if (_request('valid_subscribers'))
		$options['statut'] = 'valide';
	if ($l = _request('listes_import_subscribers'))
		$options['listes'] = $l;
	$r = importer_mailsubscribers_importe(session_get('importer_mailsubscribers::filename'), $options);

	$message =
		sinon(
			singulier_ou_pluriel($r['count'],'mailsubscriber:info_1_mailsubscriber','mailsubscriber:info_nb_mailsubscribers'),
			_T('mailsubscriber:info_aucun_mailsubscriber')
		);
	if (count($r['erreurs'])){
		$message .= "<p>Erreurs : <br />".implode("<br />",$r['erreurs'])."</p>";
		$res['message_erreur'] = $message;
	}
	else {
		$res['message_ok'] = $message;
	}


	return $res;
}


function importer_mailsubscribers_file(){
	static $files = array();
	// on est appele deux fois dans un hit, resservir ce qu'on a trouve a la verif
	// lorsqu'on est appelle au traitement

	if (count($files))
		return $files;

	$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
	$files = array();
	if (is_array($post)){
		include_spip('action/ajouter_documents');
		include_spip('inc/joindre_document');

	  foreach ($post as $file) {
	  	if (is_array($file['name'])){
	  		while (count($file['name'])){
					$test=array(
						'error'=>array_shift($file['error']),
						'name'=>array_shift($file['name']),
						'tmp_name'=>array_shift($file['tmp_name']),
						'type'=>array_shift($file['type']),
						);
					if (!($test['error'] == 4)){
						if (is_string($err = joindre_upload_error($test['error'])))
							return $err; // un erreur upload
						if (!is_array(verifier_upload_autorise($test['name'])))
							return _T('medias:erreur_upload_type_interdit',array('nom'=>$test['name']));
						$files[]=$test;
					}
	  		}
	  	}
	  	else {
		  	//UPLOAD_ERR_NO_FILE
				if (!($file['error'] == 4)){
					if (is_string($err = joindre_upload_error($file['error'])))
						return $err; // un erreur upload
					if (!is_array(verifier_upload_autorise($file['name'])))
						return _T('medias:erreur_upload_type_interdit',array('nom'=>$file['name']));
					$files[]=$file;
				}
	  	}
		}
		if (!count($files))
			return _T('medias:erreur_indiquez_un_fichier');
	}
	return $files;
}

function importer_mailsubscribers_data($filename){

	$header = true;
	$importer_csv = charger_fonction("importer_csv","inc");

	// lire la premiere ligne et voir si elle contient 'email' pour decider si entete ou non
	if ($handle = @fopen($filename, "r")){
		$line = fgets($handle, 4096);
		if (!$line OR stripos($line,'email')===false)
			$header = false;
		@fclose($handle);
	}

	$data_raw = $importer_csv($filename,$header);

	// colonner : si colonne email on prend toutes les colonnes
	// sinon on ne prend que la premiere colonne, comme un email
	$data = array();
	while ($data_raw AND count($data_raw)){

		$row = array_shift($data_raw);
		$row = array_combine(array_map('strtolower',array_keys($row)),array_values($row));

		$d = array();
		if (isset($row['email']))
			$d['email'] = $row['email'];
		else
			$d['email'] = reset($row);

		foreach(array('nom','lang','listes','statut','date') as $k)
			if (isset($row[$k]))
				$d[$k] = $row[$k];

		// Mailchimp
		if (isset($row['prenom'])){
			$d['nom'] = trim($row['prenom'] . (isset($d['nom'])?" ".$d['nom']:""));
		}
		if (isset($row['confirm_time']) AND !isset($d['date'])){
			$d['date'] = $row['confirm_time'];
			if (!isset($d['statut']))
				$d['statut'] = 'valide';
		}

		$data[] = $d;
	}

	return $data;
}

/**
 *
 * @param string $filename
 * @param array $options
 *   statut
 *   listes
 * @return array
 */
function importer_mailsubscribers_importe($filename,$options=array()){
	$res = array('count'=>0,'erreurs'=>array());

	$data = importer_mailsubscribers_data($filename);
	$newsletter_subscribe = charger_fonction('subscribe','newsletter');
	include_spip('inc/filtres'); // email_valide
	include_spip('action/editer_objet');
	include_spip('inc/mailsubscribers');
	set_request('id_auteur',''); // pas d'auteur associe a nos inscrits

	foreach ($data as $d){
		// strategie d'import en fonction de la qualite des donnees

		// si pas de colonne email explicite, on prend la premiere colonne et on importe en mail si valide, tel quel
		// mais graceful (sans forcer le reabonnement d'un desabonne)
		$email = $d['email'];
		if ($email AND email_valide($email) AND !mailsubscribers_test_email_obfusque($email)){
			$set = array();
			if (isset($d['nom'])) $set['nom'] = $d['nom'];
			if (isset($d['lang'])) $set['lang'] = $d['lang'];
			if (isset($d['listes'])) $set['listes'] = explode(',',$d['listes']);

			if (!isset($d['statut']) AND isset($options['statut']))
				$d['statut'] = $options['statut'];
			if (!isset($set['listes']) AND isset($options['listes']) AND is_array($options['listes']))
				$set['listes'] = $options['listes'];

			if (!isset($d['statut'])){
				$set['graceful']=true;
				$newsletter_subscribe($email,$set);
				spip_log("Importer $email ".var_export($set,true),"mailsubscribers");
				$res['count']++;
			}
			// si statut explicite, il faut importer a la main pour respecter le statut demande
			else {
				if (isset($set['listes'])) $set['listes'] = implode(',',$set['listes']);
				if (isset($d['date'])) $set['date'] = $d['date'];
				if ($row = sql_fetsel("id_mailsubscriber,listes","spip_mailsubscribers","email=".sql_quote($email)." OR email=".sql_quote(mailsubscribers_obfusquer_email($email)))
				  AND $id = $row["id_mailsubscriber"]){
					$set['email'] = $email; // si mail obfusque
					$set['statut'] = $d['statut'];
					// si la liste vient des options on la merge avec l'existante
					if (!isset($d['listes']) AND isset($set['listes']) AND $row['listes']){
						$l = array_unique(array_merge(explode(",",$row['listes']),explode(",",$set['listes'])));
						$set['listes'] = implode(",",$l);
					}
					objet_modifier("mailsubscriber",$id,$set);
					$res['count']++;
				}
				else {
					$set['email'] = $email;
					if ($id = objet_inserer("mailsubscriber",0,$set)) {
						// on garde tous les champs car objet_inserer n'a pas forcement fait le boulot (depend de http://core.spip.org/projects/spip/repository/revisions/20021)
						$set['statut'] = $d['statut'];
						objet_modifier("mailsubscriber",$id,$set);
						$res['count']++;
					}
					else {
						$res['erreurs'][] = "erreur import \"<tt>$email</tt>\"";
					}
				}
			}
		}
		else {
			// ne pas produire une erreur pour un email vide
			if ($email)
				$res['erreurs'][] = "email invalide \"<tt>$email</tt>\"";
		}
	}

	// debloquer les flags edition
	include_spip('inc/drapeau_edition');
	debloquer_tous($GLOBALS['visiteur_session']['id_auteur']);


	return $res;
}
?>