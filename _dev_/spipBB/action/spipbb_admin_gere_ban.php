<?php
#---------------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                             #
#  File    : action/spipbb_admin_gere_ban                       #
#  Authors : chryjs 2007 et als                                 #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs      #
#  Contact : chryjs!@!free!.!fr                                 #
#  Note : contient une partie copiee de phpbb (voir note)       #
# [fr] Gestion du banissement                                   #
# [en] Manage ban lists                                         #
#---------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

function action_spipbb_admin_gere_ban_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action(); // $arg=ban_user par ex
	$r = rawurldecode(_request('redirect'));

	spipbb_log($arg,3,"A_a_s_a_g_b_d");

	switch ($arg) {
	case "ban-user" :
			$liste_user=_request('ban_user');
			if ( $liste_user AND is_array($liste_user) ) {
				$liste_id=join(",",$liste_user);
				// construction de  INSERT INTO spip_ban_liste ( ban_login ) (SELECT login from spip_auteurs)
				$req= sql_query("INSERT IGNORE INTO spip_ban_liste ( ban_login ) "
					. "SELECT login from spip_auteurs "
					. "WHERE id_auteur IN ($liste_id) ");
			}
		break;
	case "unban-user" :
			$liste_unban=_request('unban_user');
			if ( $liste_unban AND is_array($liste_unban) ) {
				$ban_id=join(",",$liste_unban);
				$req=sql_query("UPDATE spip_ban_liste SET ban_login=NULL WHERE id_ban IN ($ban_id)");
				$req_nettoie=sql_query("DELETE FROM spip_ban_liste WHERE ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
			}
		break;
	case "ban-ip" :
			$adresse=trim(_request('ban_ip')); 

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
						$req= sql_query("INSERT IGNORE INTO spip_ban_liste SET ban_ip='$adr' ");
					}
				} // while
			} // if $adresse
		break;
	case "unban-ip" :
			$liste_unban=_request('unban_ip');
			if ( $liste_unban AND is_array($liste_unban) ) {
				$liste_id=join(",",$liste_unban);
				$req=sql_query("UPDATE spip_ban_liste SET ban_ip=NULL WHERE id_ban IN ($liste_id)");
				$req_nettoie=sql_query("DELETE FROM spip_ban_liste WHERE ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
			}
		break;
	case "ban-email" :
			$adresse=trim(_request('ban_email')); // tester pour verifier que c'est bien une email conforme
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
						$req= sql_query("INSERT IGNORE INTO spip_ban_liste SET ban_email='$adr' ");
					}
				} // while
			} // if $adresse
		break;
	case "unban-email" :
			$liste_unban=_request('unban_email');
			if ( $liste_unban AND is_array($liste_unban) ) {
				$liste_id=join(",",$liste_unban);
				$req=sql_query("UPDATE spip_ban_liste SET ban_email=NULL WHERE id_ban IN ($liste_id)");
				$req_nettoie=sql_query("DELETE FROM spip_ban_liste WHERE ban_login IS NULL AND ban_ip IS NULL AND ban_email IS NULL");
			}
		break;
	}

	redirige_par_entete($r); // ajax_retour ?
} // action_spipbb_admin_gere_ban_dist

?>
