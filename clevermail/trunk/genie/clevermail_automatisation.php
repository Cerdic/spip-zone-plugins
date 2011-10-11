<?php
function genie_clevermail_automatisation_dist() {
	if ($autoLists = sql_select("lst_id, lst_auto_mode, lst_auto_hour, lst_auto_week_days, lst_auto_month_day", "spip_cm_lists", "lst_auto_mode != 'none'")) { 
		while($list = sql_fetch($autoLists)) {
			$createAuto = false;
			if (!$lastCreate = sql_getfetsel("pst_date_create", "spip_cm_posts", "lst_id=".intval($list['lst_id']), "", "pst_date_create DESC", "0,1")) {
				// Il n'y a pas encore eu de message dans cette liste
				$lastCreate = 60*60*24; // On se place le 2 janvier 1970, SPIP n'aime pas epoc avec le critere "age"
			}
			if (date("d/m/Y") != date("d/m/Y", $lastCreate)                       // Aujourd'hui est un autre jour
          && intval(date("H")) > (intval($list['lst_auto_hour']) - 1)) {    // L'heure est venue
        switch($list['lst_auto_mode']) {
					case 'day':
						$createAuto = true;
						break;
					case 'week':
						if (in_array(date("w"), explode(',', $list['lst_auto_week_days']))) {     // Le bon jour de la semaine
              $createAuto = true;
	          }
	          break;
					case 'month':
	          if (intval(date("j")) == intval($list['lst_auto_month_day'])) {    // Le bon jour du mois
              $createAuto = true;
	          }
						break;
					default:
						break;
				}
				if ($createAuto) {
          include_spip('inc/clevermail_post_create');
          if ($pst_id = clevermail_post_create($list['lst_id'])) {
            include_spip('inc/clevermail_post_queue');
            clevermail_post_queue($pst_id);
          }
				}
		  }
		}
	}
	return 1;
}
?>