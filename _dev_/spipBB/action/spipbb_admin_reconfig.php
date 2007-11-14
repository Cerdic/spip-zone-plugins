<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_admin_reconfig : savue la conf  #
#  Authors : chryjs, 2007                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

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

include_spip('inc/spipbb');

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_admin_reconfig()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$action=$arg;
	include_spip('inc/headers');

	$redirige = urldecode(_request('redirect'));

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	if ( ($action=="save") AND spipbb_is_configured() ) {

		$reconf=false;
		if (($config_spipbb=_request('config_spipbb'))
			and $config_spipbb!=$GLOBALS['spipbb']['configure']) {
			$GLOBALS['spipbb']['configure']=$config_spipbb;
			$reconf=true;
		}
		if ((strlen($spipbb_id_secteur=_request('spipbb_id_secteur')))
			and intval($spipbb_id_secteur)!=$GLOBALS['spipbb']['id_secteur']) {
			$GLOBALS['spipbb']['id_secteur']=intval($spipbb_id_secteur);
			$reconf=true;
		}

		if ((strlen($id_groupe_mot=_request('id_groupe_mot')))
			and intval($id_groupe_mot)!=$GLOBALS['spipbb']['id_groupe_mot']) {
			$GLOBALS['spipbb']['id_groupe_mot']=intval($id_groupe_mot);
			$reconf=true;
		} else if ( empty($GLOBALS['spipbb']['id_groupe_mot']) and
				(strlen($nom_groupe_mot=trim(_request('nom_groupe_mot')))) ) {
				// on cherche s'il n'existe pas deja
				$row = sql_fetsel('id_groupe','spip_groupes_mots', "titre = '$nom_groupe_mot'" ,'','','1');
				if (!$row) { // Celui la n'existe pas
					$res = sql_insertq("spip_groupes_mots",array(
							'titre' => $nom_groupe_mot,
							'descriptif' => _T('spipbb:mot_groupe_moderation'),
							'articles' => 'oui',
							'rubriques' => 'oui',
							'minirezo' => 'oui',
							'comite' => 'oui',
							'forum' => 'oui' )
								);
					$row['id_groupe'] = $res;
				}
				$GLOBALS['spipbb']['id_groupe_mot'] = $row['id_groupe'];
				// on cree les mots cles associes
				$row = sql_fetsel('count(*) AS total','spip_mots',
						array('id_groupe='.$GLOBALS['spipbb']['id_groupe_mot']));
				if (!$row or $row['total']<3) {
					$GLOBALS['spipbb']['id_mot_ferme'] = spipbb_init_mot_cle("ferme",$GLOBALS['spipbb']['id_groupe_mot']);
					$GLOBALS['spipbb']['id_mot_annonce'] = spipbb_init_mot_cle("annonce",$GLOBALS['spipbb']['id_groupe_mot']);
					$GLOBALS['spipbb']['id_mot_postit'] = spipbb_init_mot_cle("postit",$GLOBALS['spipbb']['id_groupe_mot']);
				} // L'admin doit choisir lui meme ses mots clefs
				$reconf=true;
		}
		if ((strlen($id_mot_ferme=_request('id_mot_ferme')))
			and intval($id_mot_ferme)!=$GLOBALS['spipbb']['id_mot_ferme']) {
			$GLOBALS['spipbb']['id_mot_ferme']=intval($id_mot_ferme);
			$reconf=true;
		}
		if ((strlen($id_mot_annonce=_request('id_mot_annonce')))
			and intval($id_mot_annonce)!=$GLOBALS['spipbb']['id_mot_annonce']) {
			$GLOBALS['spipbb']['id_mot_annonce']=intval($id_mot_annonce);
			$reconf=true;
		}
		if ((strlen($id_mot_postit=_request('id_mot_postit')))
			and intval($id_mot_postit)!=$GLOBALS['spipbb']['id_mot_postit']) {
			$GLOBALS['spipbb']['id_mot_postit']=intval($id_mot_postit);
			$reconf=true;
		}
		if (($squelette_groupeforum=_request('squelette_groupeforum'))
			and $squelette_groupeforum!=$GLOBALS['spipbb']['squelette_groupeforum']) {
			$GLOBALS['spipbb']['squelette_groupeforum']=$squelette_groupeforum;
			$reconf=true;
		}
		if (($squelette_filforum=_request('squelette_filforum'))
			and $squelette_filforum!=$GLOBALS['spipbb']['squelette_filforum']) {
			$GLOBALS['spipbb']['squelette_filforum']=$squelette_filforum;
			$reconf=true;
		}
		if (($config_spam_words=_request('config_spam_words'))
			and $config_spam_words!=$GLOBALS['spipbb']['config_spam_words']) {
			$GLOBALS['spipbb']['config_spam_words']=$config_spam_words;
			$reconf=true;
		}
		if ((strlen($sw_nb_spam_ban=_request('sw_nb_spam_ban')))
			and intval($sw_nb_spam_ban)!=$GLOBALS['spipbb']['sw_nb_spam_ban']) {
			$GLOBALS['spipbb']['sw_nb_spam_ban']=intval($sw_nb_spam_ban);
			$reconf=true;
		}
		if ((strlen($sw_ban_ip=_request('sw_ban_ip')))
			and intval($sw_ban_ip)!=$GLOBALS['spipbb']['sw_ban_ip']) {
			$GLOBALS['spipbb']['sw_ban_ip']=intval($sw_ban_ip);
			$reconf=true;
		}
		if (($sw_ban_ip=_request('sw_admin_can_spam'))
			and $sw_ban_ip!=$GLOBALS['spipbb']['sw_admin_can_spam']) {
			$GLOBALS['spipbb']['sw_admin_can_spam']=$sw_ban_ip;
			$reconf=true;
		}
		if (($sw_modo_can_spam=_request('sw_modo_can_spam'))
			and $sw_modo_can_spam!=$GLOBALS['spipbb']['sw_modo_can_spam']) {
			$GLOBALS['spipbb']['sw_modo_can_spam']=$sw_modo_can_spam;
			$reconf=true;
		}
		if (($sw_send_pm_warning=_request('sw_send_pm_warning'))
			and $sw_send_pm_warning!=$GLOBALS['spipbb']['sw_send_pm_warning']) {
			$GLOBALS['spipbb']['sw_send_pm_warning']=$sw_send_pm_warning;
			$reconf=true;
		}
		if (($sw_warning_from_admin=_request('sw_warning_from_admin'))
			and $sw_warning_from_admin!=$GLOBALS['spipbb']['sw_warning_from_admin']) {
			$GLOBALS['spipbb']['sw_warning_from_admin']=$sw_warning_from_admin;
			$reconf=true;
		}
		if ((strlen($sw_warning_pm_titre=_request('sw_warning_pm_titre')))
			and $sw_warning_pm_titre!=$GLOBALS['spipbb']['sw_warning_pm_titre']) {
			$GLOBALS['spipbb']['sw_warning_pm_titre']=$sw_warning_pm_titre;
			$reconf=true;
		}
		if ((strlen($sw_warning_pm_message=_request('sw_warning_pm_message')))
			and $sw_warning_pm_message!=$GLOBALS['spipbb']['sw_warning_pm_message']) {
			$GLOBALS['spipbb']['sw_warning_pm_message']=$sw_warning_pm_message;
			$reconf=true;
		}

		if ($reconf) spipbb_save_metas();

	}

	redirige_par_entete($redirige);
} // action_spipbb_admin_reconfig

?>
