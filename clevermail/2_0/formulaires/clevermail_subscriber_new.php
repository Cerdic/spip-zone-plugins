<?php
function formulaires_clevermail_subscriber_new_charger_dist() {
	$valeurs = array(
		'cm_file' => '',
	  'cm_subs' => '',
	  'cm_mode' => 1,
	  'cm_lists' => array()
	);

	return $valeurs;
}

function formulaires_clevermail_subscriber_new_verifier_dist() {
	$erreurs = array();
	if (
	    (!isset($_FILES['cm_file']) || $_FILES['cm_file']['name'] == '' || !is_uploaded_file($_FILES['cm_file']['tmp_name']))
	    && _request('cm_subs') == '') {
    $erreurs['cm_file'] = 'Vous devez choisir un fichier...';
    $erreurs['cm_subs'] = '...et/ou saisir des adresses.';
	}
  if (isset($_FILES['cm_file']) && is_uploaded_file($_FILES['cm_file']['tmp_name'])) {
    $adresses = implode('', file($_FILES['cm_file']['tmp_name']));
    // TODO : utiliser plutôt la fonction email_valide()
    if (!ereg("^([^@ ]+@[^@ ]+\.[^@. ]+[,;\t\n\r ]+)*[^@ ]+@[^@ ]+\.[^@. ]+[,;\t\n\r ]*$", $adresses)) {
      $erreurs['cm_file'] = 'Le format des adresses ne semble pas bon.';
    }
  }
  // TODO : utiliser plutôt la fonction email_valide()
  if (_request('cm_subs') != '' && !ereg("^([^@ ]+@[^@ ]+\.[^@. ]+[,;\n\r ]+)*[^@ ]+@[^@ ]+\.[^@. ]+[,;\t\n\r ]*$", _request('cm_subs'))) {
    $erreurs['cm_subs'] = 'Le format des adresses ne semble pas bon.';
	}
	if (sizeof(_request('cm_lists')) == 0) {
    $erreurs['cm_lists'] = 'Vous devez choisir au moins une liste.';
	}
	if (count($erreurs)) {
		$erreurs['message_erreur'] = 'Veuillez corriger votre saisie.';
	}
	return $erreurs;
}

function formulaires_clevermail_subscriber_new_traiter_dist() {
	$adresses = array();
  if (isset($_FILES['cm_file']) && is_uploaded_file($_FILES['cm_file']['tmp_name'])) {
    $fileContent = implode('', file($_FILES['cm_file']['tmp_name']));
    $fileContent = ereg_replace("[,;\t\n\r ]+"," ", $fileContent);
    $adresses = array_merge($adresses, explode(' ', trim($fileContent)));
  }
  if (_request('cm_subs')) {
    $textareaContent = ereg_replace("[,;\t\n\r ]+"," ", _request('cm_subs'));
    $adresses = array_merge($adresses, explode(' ', trim($textareaContent)));
  }
  $adresses = array_unique($adresses);
  $nbNewSubs = 0;
  $nbUpdSubs = 0;
  $lsr_mode = intval(_request('cm_mode'));
	if (sizeof($adresses) > 0) {
    foreach($adresses as $adresse) {
    	if (!$sub_id = sql_getfetsel("sub_id", "spip_cm_subscribers", "sub_email=".sql_quote($adresse))) {
    		$sub_id = sql_insertq("spip_cm_subscribers", array('sub_email' => $adresse));
    		sql_updateq("spip_cm_subscribers", array('sub_profile' => md5($sub_id.'#'.$adresse.'#'.time())), "sub_id=".intval($sub_id));
    	}
    	foreach(_request('cm_lists') as $lst_id) {
    		$lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($lst_id));
    		if (sql_countsel("spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == 0) {
    			sql_insertq("spip_cm_lists_subscribers", array('lst_id' => intval($lst_id), 'sub_id' => intval($sub_id), 'lsr_mode' => intval($lsr_mode), 'lsr_id' => md5('subscribe#'.$lst_id.'#'.$sub_id.'#'.time())));
          spip_log('Ajout de '.$adresse.' (id='.$sub_id.') à la liste « '.$lst_name.' » (id='.$lst_id.')', 'clevermail');
    			$nbNewSubs++;
    		} else {
    			if ($lsr_mode != sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id))) {
	    			sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => intval($lsr_mode)), "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id));
	          spip_log('Changement de mode d\'abonnement de '.$adresse.' (id='.$sub_id.') à la liste « '.$lst_name.' » (id='.$lst_id.')', 'clevermail');
	    			$nbUpdSubs++;
    			}
    		}
    	}
    }
	}
  $msg = $nbNewSubs > 0 ? $nbNewSubs.' nouveaux abonnés' : 'aucun nouvel abonné';
  $msg .= $nbNewSubs > 0 && $nbUpdSubs > 0 ? ' et ' : '';
  $msg .= $nbUpdSubs > 0 ? $nbUpdSubs.' changements de mode d\'abonnement' : '';
  
  return array('message_ok' => $msg);
}
?>