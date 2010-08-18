<?php
// inspire de ecrire/action/configurer.php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);
include_spip('inc/spipbb_inc_metas');

function action_spipbb_configurer() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$r = rawurldecode(_request('redirect'));
	$r = parametre_url($r, 'configuration', $arg,"&");
	spipbb_appliquer_modifs_config($arg);
	redirige_par_entete($r);
} // action_spipbb_configurer

function spipbb_appliquer_modifs_config($arg='') {

	if ( ($liste_user=_request('ban_user'))!==NULL ) {
		if ( $liste_user AND is_array($liste_user) ) {
			$liste_id=join(",",$liste_user);
			// construction de  INSERT INTO spip_ban_liste ( ban_login ) (SELECT login from spip_auteurs)
			// c: 10/2/8 ca fonctionne partout ca ? IGNORE ?
			@sql_query("INSERT IGNORE INTO spip_ban_liste ( ban_login ) "
				. "SELECT login from spip_auteurs "
				. "WHERE id_auteur IN ($liste_id) ");
		}
	}

	if ( ($liste_unban=_request('unban_user'))!==NULL ) {
		if ( $liste_unban AND is_array($liste_unban) ) {
			$ban_id=join(",",$liste_unban);
			@sql_updateq("spip_ban_liste",array('ban_login'=>"NULL"),"id_ban IN ($ban_id)");
			@sql_delete("spip_ban_liste","ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
		}
	}

	if ( ($adresse=_request('ban_ip'))!==NULL ) {
		if ( $adresse AND strlen($adresse)>0 ) // test pour verifier que c'est bien une saisie conforme
		{
			$ip_list = array();
			$ip_list_temp = explode(',', $adresse); // et oui on peut avoir une liste !

			for($i = 0; $i < count($ip_list_temp); $i++)
			{
				if ( preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', trim($ip_list_temp[$i]), $ip_range_explode) )
				{
					// Ca commence !! me demandez pas comment ca marche je l'ai repris de phpbb :-)
					// donc cette partie (preg_match) est (c) 2001 acydburn The phpbb-group - Licence GPL
					$ip_1_counter = $ip_range_explode[1];
					$ip_1_end = $ip_range_explode[5];

					while ( $ip_1_counter <= $ip_1_end )
					{
						$ip_2_counter = ( $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[2] : 0;
						$ip_2_end = ( $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[6];

						if ( $ip_2_counter == 0 && $ip_2_end == 254 )
						{
							$ip_2_counter = 255;
							$ip_2_fragment = 255;
							$ip_list[] = "$ip_1_counter.255.255.255";
						}

						while ( $ip_2_counter <= $ip_2_end )
						{
							$ip_3_counter = ( $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[3] : 0;
							$ip_3_end = ( $ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end ) ? 254 : $ip_range_explode[7];

							if ( $ip_3_counter == 0 && $ip_3_end == 254 )
							{
							$ip_3_counter = 255;
							$ip_3_fragment = 255;

							$ip_list[] = "$ip_1_counter.$ip_2_counter.255.255";
							}

							while ( $ip_3_counter <= $ip_3_end )
							{
								$ip_4_counter = ( $ip_3_counter == $ip_range_explode[3] && $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1] ) ? $ip_range_explode[4] : 0;
								$ip_4_end = ( $ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end ) ? 254 : $ip_range_explode[8];

								if ( $ip_4_counter == 0 && $ip_4_end == 254 )
								{
									$ip_4_counter = 255;
									$ip_4_fragment = 255;

									$ip_list[] = "$ip_1_counter.$ip_2_counter.$ip_3_counter.255";
								}

								while ( $ip_4_counter <= $ip_4_end )
								{
									$ip_list[] = "$ip_1_counter.$ip_2_counter.$ip_3_counter.$ip_4_counter";
									$ip_4_counter++;
								}
								$ip_3_counter++;
							} // while ip3
							$ip_2_counter++;
						} // while ip2
						$ip_1_counter++;
					} // while ip1
				} // if preg_match
				else if ( preg_match('/^([\w\-_]\.?){2,}$/is', trim($ip_list_temp[$i])) )
				{
					$ip = gethostbynamel(trim($ip_list_temp[$i]));
					for($j = 0; $j < count($ip); $j++)
					{
						if ( !empty($ip[$j]) )
						{
							$ip_list[] = $ip[$j];
						}
					}
				}
				else if ( preg_match('/^([0-9]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$/', trim($ip_list_temp[$i])) )
				{
					$ip_list[] = str_replace('*', '255', trim($ip_list_temp[$i]));
				}
			} // for
			while (list(,$adr)=each($ip_list)) {
				$adr=trim($adr);
				if (!empty($adr)) {
					// c: 10/2/8 compat pg_sql
					//$req= sql_query("INSERT IGNORE INTO spip_ban_liste SET ban_ip='$adr' ");
					@sql_insertq("spip_ban_liste",array('ban_ip'=>$adr) );
				}
			} // while
		} // if $adresse
	}

	if ( ($liste_unban=_request('unban_ip'))!==NULL ) {
		if ( $liste_unban AND is_array($liste_unban) ) {
			$liste_id=join(",",$liste_unban);
			@sql_updateq("spip_ban_liste",array('ban_ip'=>"NULL"),"id_ban IN ($liste_id)");
			@sql_delete("spip_ban_liste","ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
		}
	}

	if ( ($adresse=_request('ban_email'))!==NULL ) { // tester pour verifier que c'est bien une email conforme
		if ( $adresse AND strlen($adresse)>0 ) {
			$email_list = array();
			$email_list_temp = explode(',', $adresse);

			for($i = 0; $i < count($email_list_temp); $i++)
			{
				//
				// [fr] Cet test d'ereg est base sur un exemple de php@unreelpro.com
				// decrit dans les annotations de la documentation php sur php.net (section ereg)
				// [en] This ereg match is based on one by php@unreelpro.com
				// contained in the annotated php manual at php.net (ereg section)
				//
				if (preg_match('/^(([a-z0-9&\'\.\-_\+])|(\*))+@(([a-z0-9\-])|(\*))+\.([a-z0-9\-]+\.)*?[a-z]+$/is', trim($email_list_temp[$i])))
				{
					$email_list[] = trim($email_list_temp[$i]);
				} // preg_match
			} // for

			while (list(,$adr)=each($email_list)) {
				$adr=trim($adr);
				if (!empty($adr)) {
					@sql_insertq("spip_ban_liste",array('ban_email'=>$adr));
				}
			} // while
		} // if $adresse
	}

	if ( ($liste_unban=_request('unban_email'))!==NULL ) {
		if ( $liste_unban AND is_array($liste_unban) ) {
			$liste_id=join(",",$liste_unban);
			@sql_updateq("spip_ban_liste",array('ban_email'=>"NULL"),"id_ban IN ($liste_id)");
			@sql_delete("spip_ban_liste","ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
		}
	}

	$reconf=false;
	$spipbb_metas=@unserialize($GLOBALS['meta']['spipbb']);
	
	foreach(spipbb_liste_metas() as $i=>$v) {
		if ( (($x=_request($i))!==NULL) AND $x<>$spipbb_metas[$i] ) {
			$reconf=true;
			// cas particuliers ?
			switch ($i) {
			
			case 'id_groupe_mot' :
				// creer un traitement de controle
			default :
				$spipbb_metas[$i]=$x;			
			} // switch
		} // if modif
	} // foreach
	
	if ($reconf) {
		// controles dans save_metas		
		// sauvegarde
		$GLOBALS['spipbb']=$spipbb_metas;
		spipbb_save_metas();
	}
} // appliquer_modifs_config

?>