<?php
include_spip('inc/clevermail_abonnes');

function genie_clevermail_auto_ajout_abonnes_dist() {
	if ($autoListes = sql_select("lst_id, lst_name, lst_auto_subscribers, lst_auto_subscribers_mode", "spip_cm_lists", "lst_auto_subscribers != '' AND lst_auto_subscribers_updated < ".(time() - 60*60*24))) {
    include_spip('inc/distant');
		while($liste = sql_fetch($autoListes)) {
	    if ($adresses = recuperer_page($liste['lst_auto_subscribers'])) {
	      if (!clevermail_verification_adresses_email($adresses)) {
	      	// XXX : log en chaîne de langue
          spip_log('Le format des adresses e-mail ne semble pas bon dans le fichier distant d\'adresses de la liste « '.$liste['lst_name'].' » (id='.$liste['lst_id'].') : '.$liste['lst_auto_subscribers'], 'clevermail');
	      } else {
	      	$retour = clevermail_abonnes_ajout(array($liste['lst_id']), intval($liste['lst_auto_subscribers_mode']), $adresses);
	      	sql_updateq("spip_cm_lists", array('lst_auto_subscribers_updated' => time()), "lst_id=".$liste['lst_id']);
	      	// XXX : log en chaîne de langue
	      	$msg = 'Ajout automatique d\'abonnés à la liste « '.$liste['lst_name'].' » (id='.$liste['lst_id'].') à partir du fichier '.$liste['lst_auto_subscribers'].' : ';
				  if ($retour === false) {
				    $msg .= _T('clevermail:aucun_nouvel_abonne');
				  } else {
				    $msg .= $retour['nb_nouv'] > 0 ? $retour['nb_nouv']._T('clevermail:n_nouveaux_abonnes') : _T('clevermail:aucun_nouvel_abonne');
				    $msg .= $retour['nb_nouv'] > 0 && $retour['nb_maj'] > 0 ? _T('clevermail:nouveaux_abonnes_et') : '';
				    $msg .= $retour['nb_maj'] > 0 ? $retour['nb_maj']._T('clevermail:changements_mode_abonnement') : '';
				    spip_log($msg, 'clevermail');
				  }
	      }
	    } else {
	    	// XXX : log en chaîne de langue
	      spip_log('Impossible de télécharger le fichier distant d\'adresses de la liste « '.$liste['lst_name'].' » (id='.$liste['lst_id'].') : '.$liste['lst_auto_subscribers'], 'clevermail');
	    }
		}
	}
	return 1;
}
?>