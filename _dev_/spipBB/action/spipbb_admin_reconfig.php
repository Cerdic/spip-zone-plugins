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
		# ajouts gafospip / scoty
		# nombre lignes
		if ((strlen($fixlimit=_request('fixlimit')))
			and intval($fixlimit)!=$GLOBALS['spipbb']['fixlimit']) {
			$GLOBALS['spipbb']['fixlimit']=intval($fixlimit);
			$reconf=true;
		}
		# temps avant deplacement
		if ((strlen($lockmaint=_request('lockmaint')))
			and intval($lockmaint)!=$GLOBALS['spipbb']['lockmaint']) {
			$GLOBALS['spipbb']['lockmaint']=intval($lockmaint);
			$reconf=true;
		}
		# scoty gaf_ecrireinstall 16/11/07 :  support_auteurs : table / extra/autres..
		# => lieu de stockage des champs supplementaires auteurs
			# petits controles prealable de : table
				##$options_sap = array('extra','table','autre');
				#$options_sap = array('extra','table');
		# form support mal remplis : retour case depart
		$support_auteurs = _request('support_auteurs');
		$table_support = _request('table_support');
		if ( !empty($support_auteurs) AND
			( ($support_auteurs!=$GLOBALS['spipbb']['support_auteurs']) // soit on modifie le support
			OR (!empty($table_support) AND ($support_auteurs=='table') AND ($table_support!=$GLOBALS['spipbb']['table_support'])) ) ) // soit on modifie la table et c'est en base
		{
			// traitement specifique en cas de changement
			if($support_auteurs=='table' AND $table_support) {
				# verif
				# si table_support existe + fournis (plus bas) liste champs existent : oui/non 
				$chps_exists = montre_table_support($table_support);
				if(is_array($chps_exists)) {
					$t_creer_chps=array();
					foreach($chps_exists as $k => $v) {
						if($v=='non') {
							$t_creer_chps[]=$k;
						}
					}
					if(count($t_creer_chps)) {
					# maj - ajout champs sur table
						support_ajout_champs($table_support,$t_creer_chps);
					# transfert extras vers table sap
						support_maj_extras($table_support,$t_creer_chps);
					}
				} // sinon probleme ?
				// verifier les stockages en meta + sauver la config !!
			}
			$GLOBALS['spipbb']['support_auteurs'] = $support_auteurs;
			$GLOBALS['spipbb']['table_support'] = $table_support;
			$reconf=true;
		}

		#champs supplementaires auteurs
		$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
		$champs_definis=array();
		foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
			$champs_definis[]=$champ;
		}
		$champs_optionnels = array_diff($champs_definis,$champs_requis);
		foreach ($champs_optionnels as $champ_a_valider) {
			$tbl_conf['affiche_'.$chx]=_request('affiche_'.$chx); # oui/non
			if (($affiche_champ=_request('affiche_'.$champ_a_valider))
				and $affiche_champ!=$GLOBALS['spipbb']['affiche_'.$champ_a_valider]) {
				$GLOBALS['spipbb']['affiche_'.$champ_a_valider]=$affiche_champ;
				$reconf=true;
			}
		}

		#avatars
		if (($affiche_avatar=_request('affiche_avatar'))
			and $affiche_avatar!=$GLOBALS['spipbb']['affiche_avatar']) {
			$GLOBALS['spipbb']['affiche_avatar']=$affiche_avatar;
			$reconf=true;
		}
		# on limite la taille en cas de mauvaise saisie : max 200 pixels
		$taille_image_maxi = '200';
		# sur page sujet 	
		if ((strlen($taille_avatar_suj=_request('taille_avatar_suj')))
			and intval($taille_avatar_suj)!=$GLOBALS['spipbb']['taille_avatar_suj']) {
			$GLOBALS['spipbb']['taille_avatar_suj']=(intval($taille_avatar_suj)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_suj);
			$reconf=true;
		}		
		# sur page contact
		if ((strlen($taille_avatar_cont=_request('taille_avatar_cont')))
			and intval($taille_avatar_cont)!=$GLOBALS['spipbb']['taille_avatar_cont']) {
			$GLOBALS['spipbb']['taille_avatar_cont']=(intval($taille_avatar_cont)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_cont);
			$reconf=true;
		}		
		# sur page profile
		if ((strlen($taille_avatar_prof=_request('taille_avatar_prof')))
			and intval($taille_avatar_prof)!=$GLOBALS['spipbb']['taille_avatar_prof']) {
			$GLOBALS['spipbb']['taille_avatar_prof']=(intval($taille_avatar_prof)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_prof);
			$reconf=true;
		}

		# bouton abus
		if (($affiche_bouton_abus=_request('affiche_bouton_abus'))
			and $affiche_bouton_abus!=$GLOBALS['spipbb']['affiche_bouton_abus']) {
			$GLOBALS['spipbb']['affiche_bouton_abus']=$affiche_bouton_abus;
			$reconf=true;
		}

		# bouton rss
		if (($affiche_bouton_rss=_request('affiche_bouton_rss'))
			and $affiche_bouton_rss!=$GLOBALS['spipbb']['affiche_bouton_rss']) {
			$GLOBALS['spipbb']['affiche_bouton_rss']=$affiche_bouton_rss;
			$reconf=true;
		}
		
		if ($reconf) spipbb_save_metas();

	}

	redirige_par_entete($redirige);
} // action_spipbb_admin_reconfig

// ------------------------------------------------------------------------------
# generer tableau des champs profils dans sap si existent
// ------------------------------------------------------------------------------
function montre_table_support($table) {
	# y a quoi dans cette table ?
	//$contenu = spip_mysql_showtable('spip_'.$table);
	$contenu = sql_showtable('spip_'.$table,true);
	$chps_presents=array();
	if(is_array($contenu)) {
		# verif nom champ (dans sap_gaf.php)
		foreach($GLOBALS['champs_sap_spipbb'] as $k => $v) {
			if($contenu['field'][$k]) {
				$chps_presents[$k]='oui';
			}
			else {
				$chps_presents[$k]='non';
			}
		}
		return $chps_presents;
	}
    else { return ''; }
} // montre_table_support

// ------------------------------------------------------------------------------
# Modifier table sap : ajout champ(s)
// ------------------------------------------------------------------------------
function support_ajout_champs($table_support,$creer_champs) {
	$connexion = $GLOBALS['connexions'][0];
	$prefixe = $connexion['prefixe'];
	if ($prefixe and $prefixe!='spip') $table_support = preg_replace('/^spip/', $prefixe, $table_support);

	$nomtable = "spip_".$table_support;
	foreach($creer_champs as $chp) {
			#recup def des champs (dans sap_gaf.php)
			$def = $GLOBALS['champs_sap_gaf'][$chp]['sql'];
			sql_query("ALTER TABLE $nomtable ADD $chp $def");
	}
} // support_ajout_champs

// ------------------------------------------------------------------------------
# Deverser extras dans table support (sap)
# Et, au passage, on insert tous auteur non encore presens dans la table
// ------------------------------------------------------------------------------
function support_maj_extras($table_support,$maj_chps) {
	# recolte extras des auteurs pour les champs suppl.
	//$r = spip_query("SELECT id_auteur, extra FROM spip_auteurs");
	$r = sql_select("id_auteur, extra","spip_auteurs");
	while($sr = sql_fetch($r)) {
		$extras = unserialize($sr['extra']);
		$set='';
		# pour chq champs gaf en maj ou crea (pas les anciens)
		foreach($maj_chps as $chp) {
			if($chp=='date_crea_gaf' && $extras['date_crea_gaf']=='') {
				$set.=",".$chp."=NOW()";
			}
			else {
				$set.= ",".$chp."="._q($extras[$chp]);
			}
		}
		$set=substr($set,1);
		if(strlen($set)>0) { $sep = ","; }

		//$q = spip_query("SELECT id_auteur FROM spip_$table_support 
		//					WHERE id_auteur=".$sr['id_auteur']);
		$q = sql_select("id_auteur","spip_$table_support","id_auteur=".$sr['id_auteur']);
		if($sq=sql_fetch($q)) {
			if($set!='') {
				//spip_query("UPDATE spip_$table_support 
				//			SET $set WHERE id_auteur=".$sr['id_auteur']);
				sql_query("UPDATE spip_$table_support 
							SET $set WHERE id_auteur=".$sr['id_auteur']);
			}
		}
		else {
			//spip_query("INSERT INTO spip_$table_support 
			//			SET id_auteur=".$sr['id_auteur']." ".$sep.$set);
			sql_query("INSERT INTO spip_$table_support 
						SET id_auteur=".$sr['id_auteur']." ".$sep.$set);
		}
	}
} // support_maj_extras

?>