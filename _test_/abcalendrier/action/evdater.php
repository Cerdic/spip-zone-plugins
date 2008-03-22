<?php
/* file che salva il valore della data evento della breve*/
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_dater_dist
function action_evdater_dist() {
   /**/
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^\W*(\d+)\W(\w*)$,", $arg, $r)) {
		spip_log("action_dater_dist $arg pas compris");
	}
   else
	 action_evdater_post($r);
}

// http://doc.spip.org/@action_dater_post
function action_evdater_post($r)
{
	include_spip('inc/date');
   //   print_r($_REQUEST);
  //

	//if (!isset($_REQUEST['avec_redac'])) {

      $date = format_mysql_date(_request('evenement_annee'), _request('evenement_mois'), _request('evenement_jour'), _request('evenement_heure'), _request('evenement_minute'));
		//
      if ($r[2] == 'breve')
			spip_query("UPDATE spip_breves SET evento=" . _q($date) . " WHERE id_breve=$r[1]");
         //echo "<p>ecco</p>";print_r($r );
  
}

?>
