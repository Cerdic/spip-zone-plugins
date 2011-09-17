<?php
function clevermail_verification_adresses_email($adresses) {
  include_spip('inc/filtres');
	if (is_string($adresses)) {
		$adresses = clevermail_chaine_email_en_tableau($adresses);
	}
	foreach($adresses as $adresse) {
		if (!email_valide($adresse)) {
			return false;
		}
	}
	return true;
}

function clevermail_chaine_email_en_tableau($adresses) {
	return array_unique(explode(' ', trim(preg_replace('`[,;\t\n\r ]+`', ' ', $adresses))));
}

function clevermail_abonnes_ajout($lst_ids, $lsr_mode, $adresses) {
	if (clevermail_verification_adresses_email($adresses)) {
		$adresses = clevermail_chaine_email_en_tableau($adresses);
	  $nb_nouv = 0;
	  $nb_maj = 0;
	  if (sizeof($adresses) > 0) {
	    foreach($adresses as $adresse) {
	      if (!$sub_id = sql_getfetsel("sub_id", "spip_cm_subscribers", "sub_email=".sql_quote($adresse))) {
	      	// Ajout d'un nouvel abonné
	        $sub_id = sql_insertq("spip_cm_subscribers", array('sub_email' => $adresse));
	        sql_updateq("spip_cm_subscribers", array('sub_profile' => md5($sub_id.'#'.$adresse.'#'.time())), "sub_id=".intval($sub_id));
	      }
	      foreach($lst_ids as $lst_id) {
	        $lst_name = sql_getfetsel("lst_name", "spip_cm_lists", "lst_id=".intval($lst_id));
	        if (sql_countsel("spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id)) == 0) {
	        	// Ajout de l'abonnement à cette liste
	          sql_insertq("spip_cm_lists_subscribers", array('lst_id' => intval($lst_id), 'sub_id' => intval($sub_id), 'lsr_mode' => intval($lsr_mode), 'lsr_id' => md5('subscribe#'.$lst_id.'#'.$sub_id.'#'.time())));
	          // XXX : log en chaîne de langue 
	          spip_log('Ajout de '.$adresse.' (id='.$sub_id.') à la liste « '.$lst_name.' » (id='.$lst_id.')', 'clevermail');
	          $nb_nouv++;
	        } else {
	          if ($lsr_mode != sql_getfetsel("lsr_mode", "spip_cm_lists_subscribers", "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id))) {
	          	// Mise à jour du mode d'abonnement
	            sql_updateq("spip_cm_lists_subscribers", array('lsr_mode' => intval($lsr_mode)), "lst_id=".intval($lst_id)." AND sub_id=".intval($sub_id));
	            // XXX : log en chaîne de langue
	            spip_log('Changement de mode d\'abonnement de '.$adresse.' (id='.$sub_id.') à la liste « '.$lst_name.' » (id='.$lst_id.')', 'clevermail');
	            $nb_maj++;
	          }
	        }
	      }
	    }
	  }
	} else {
		return false;
	}
  
  return array('nb_nouv' => $nb_nouv, 'nb_maj' => $nb_maj);
}
?>