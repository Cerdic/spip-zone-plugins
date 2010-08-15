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
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

include_spip('inc/spipbb_util');

# fonction requises
include_spip('inc/spipbb_inc_metas');

# recup des metas
$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);

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

#	if ( ($action=="save") AND spipbb_is_configured() ) {
# h. pourquoi ce controle
# il y a un controle avant et apres cette "action" ! et vu les conditions de cette
# fonction on ne peut atteindre la reecriture de la config !!

# Il serait bien de pourvoir renvoyer en arg de $redirige un code d'erreur
# interpreter dans spipbb_cofiguration pour indiquer (en rouge) l'objet qui
# empeche la config : "vous n'avez pas remplis ce champs, choisir ceci ... " !!?
#(voir gafospip)


	if ($action=="save") {

		$reconf=false;

		if (($config_spipbb=_request('config_spipbb'))
			and $config_spipbb!=$GLOBALS['spipbb']['configure']) {
			$GLOBALS['spipbb']['configure']=$config_spipbb;
			$reconf=true;
		}
		if ((strlen($spipbb_id_secteur=_request('spipbb_id_secteur')))
			and intval($spipbb_id_secteur)<>intval($GLOBALS['spipbb']['id_secteur'])) {
			$GLOBALS['spipbb']['id_secteur']=intval($spipbb_id_secteur);
			$reconf=true;
		}
		if ((strlen($id_groupe_mot=_request('id_groupe_mot')))
				and intval($id_groupe_mot)<>intval($GLOBALS['spipbb']['id_groupe_mot'])) {

			# solution boiteuse (au 1/12/07) ; peut etre revoir l_ensemble !
			# Mais il est important de conserver la correspondance article/post
			# avec leur mot-clef associes (ferme et annonce)

			# on memorise le precedent id_groupe
			$id_groupe_premodif = $GLOBALS['spipbb']['id_groupe_mot'];

			$GLOBALS['spipbb']['id_groupe_mot']=intval($id_groupe_mot);
			$reconf=true;
		}
		else if ( empty($GLOBALS['spipbb']['id_groupe_mot']) and
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
				#
				$id_groupe_premodif='non';

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
			and intval($id_mot_ferme)<>intval($GLOBALS['spipbb']['id_mot_ferme'])) {

			#on memorise le precedent mot
			$id_ferme_premodif = $GLOBALS['spipbb']['id_mot_ferme'];

			$GLOBALS['spipbb']['id_mot_ferme']=intval($id_mot_ferme);
			$reconf=true;
		}
		if ((strlen($id_mot_annonce=_request('id_mot_annonce')))
			and intval($id_mot_annonce)<>intval($GLOBALS['spipbb']['id_mot_annonce'])) {

			#on memorise le precedent mot
			$id_annonce_premodif = $GLOBALS['spipbb']['id_mot_annonce'];

			$GLOBALS['spipbb']['id_mot_annonce']=intval($id_mot_annonce);
			$reconf=true;
		}
		if ((strlen($id_mot_postit=_request('id_mot_postit')))
			and intval($id_mot_postit)<>intval($GLOBALS['spipbb']['id_mot_postit'])) {
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
			and intval($sw_nb_spam_ban)<>intval($GLOBALS['spipbb']['sw_nb_spam_ban'])) {
			$GLOBALS['spipbb']['sw_nb_spam_ban']=intval($sw_nb_spam_ban);
			$reconf=true;
		}
		if ((strlen($sw_ban_ip=_request('sw_ban_ip')))
			and intval($sw_ban_ip)<>intval($GLOBALS['spipbb']['sw_ban_ip'])) {
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
		# nombre lignes dans divers tableau de presentation
		if ((strlen($fixlimit=_request('fixlimit')))
			and intval($fixlimit)<>intval($GLOBALS['spipbb']['fixlimit'])) {
			$GLOBALS['spipbb']['fixlimit']=intval($fixlimit);
			$reconf=true;
		}
		# temps avant deplacement d_un thread
		if ((strlen($lockmaint=_request('lockmaint')))
			and intval($lockmaint)<>intval($GLOBALS['spipbb']['lockmaint'])) {
			$GLOBALS['spipbb']['lockmaint']=intval($lockmaint);
			$reconf=true;
		}
		if (($affiche_bouton_abus=_request('affiche_bouton_abus'))
			and $affiche_bouton_abus!=$GLOBALS['spipbb']['affiche_bouton_abus']) {
			$GLOBALS['spipbb']['affiche_bouton_abus']=$affiche_bouton_abus;
			$reconf=true;
		}
		if (($affiche_bouton_rss=_request('affiche_bouton_rss'))
			and $affiche_bouton_rss!=$GLOBALS['spipbb']['affiche_bouton_rss']) {
			$GLOBALS['spipbb']['affiche_bouton_rss']=$affiche_bouton_rss;
			$reconf=true;
		}


		# bouton speciaux
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
		# c: 27/12/7
		# Par defaut on n'apparait pas dans la liste des membres sauf si option modifie
		if (($affiche_membre_defaut=_request('affiche_membre_defaut'))
			and $affiche_membre_defaut!=$GLOBALS['spipbb']['affiche_membre_defaut']) {
			$GLOBALS['spipbb']['affiche_membre_defaut']=$affiche_membre_defaut;
			$reconf=true;
		}
		# c: 27/12/7
		# on peut parametrer le niveau de log de spipbb compris entre 0 et 3 ( _SPIPBB_LOG_LEVEL par defaut )
		if ((strlen($log_level=_request('log_level')))
			and intval($log_level)<>intval($GLOBALS['spipbb']['log_level'])) {
			$log_level=intval($log_level);
			$GLOBALS['spipbb']['log_level']=($log_level>3) ? _SPIPBB_LOG_LEVEL : ( ($log_level<0) ? _SPIPBB_LOG_LEVEL : $log_level ) ;
			$reconf=true;
		}

		# h. 1/12/08 #########################################
		# Recuperer la jointure de mot-clef annonce, ferme pour articles et posts
		# d_une precedente install gafospip ( et spipbb anc. generation ??? )
		# et/ou
		# d_un changement de mot-clef (-> nouvel ID)
		#
		# Rappel :: les mots annonce et ferme peuvent etre associes
		# aux articles comme au posts
		#
		if($GLOBALS['spipbb']['id_mot_annonce']>0 AND $GLOBALS['spipbb']['id_mot_ferme']>0) {
			$mots_base=array('annonce','ferme');
			$mots_preced=array();

			# gafospip ? (passe juste en premiere install !!)
			if( isset($id_groupe_premodif) AND $id_groupe_premodif=='non' AND strlen($GLOBALS['meta']['gaf_install']) )
			{
				$gaf_install = @unserialize($GLOBALS['meta']['gaf_install']);
				$id_groupe_preced = $gaf_install['groupe'];

				# cherche mot "annonce" et "ferme" dans ce groupe
				foreach($mots_base as $m) {
					$q = sql_select("id_mot",
									"spip_mots",
									"titre="._q($m)." AND id_groupe="._q($id_groupe_preced) );
					if($row = sql_fetch($q)) {
						$mots_preced[$m] = $row['id_mot'];
					}
				}
				recreer_jointures_mots($GLOBALS['spipbb']['id_mot_annonce'], $GLOBALS['spipbb']['id_mot_ferme'], $mots_preced, $mots_base);
			}
			# si antecedent non gafospip (sinon rien a faire)
			elseif(isset($id_groupe_premodif) AND is_int($id_groupe_premodif) AND $id_groupe_premodif>0)
			{
				$mots_preced['annonce']=$id_annonce_premodif;
				$mots_preced['ferme']=$id_ferme_premodif;
				recreer_jointures_mots($GLOBALS['spipbb']['id_mot_annonce'], $GLOBALS['spipbb']['id_mot_ferme'], $mots_preced, $mots_base);
				nettoyer_ante_jointures($mots_preced, $mots_base);
			}

		}

		# enreg. metas spipbb
		if ($reconf) spipbb_save_metas();

	}

	redirige_par_entete($redirige);
} // action_spipbb_admin_reconfig


#
# relier les mots annonce/ferme (ou equiv.) avec les articles/posts antecedents
#
function recreer_jointures_mots($id_mot_annonce, $id_mot_ferme, $mots_preced, $mots_base) {
	foreach($mots_base as $m) {
		$id_nouv = ($m=="annonce")?$id_mot_annonce:$id_mot_ferme;
		if($mots_preced[$m]!=0) {
			# recup jointure mot - articles
			$qa = sql_select("id_article","spip_mots_articles","id_mot="._q($mots_preced[$m]));
			while ($ra = sql_fetch($qa)) {
				$id_art = $ra['id_article'];
				@sql_insertq("spip_mots_articles",array('id_mot'=> $id_nouv, 'id_article'=> $id_art) );
			}

			# recup jointure mot - posts
			$qf = sql_select("id_forum","spip_mots_forum","id_mot="._q($mots_preced[$m]));
			while ($rf=sql_fetch($qf)) {
				$id_post = $rf['id_forum'];
					@sql_insertq("spip_mots_forum",array( 'id_mot'=>$id_nouv, 'id_forum'=>$id_post) );
			}
		}
	}
}
#
# nettoyer les precedentes jointures
#
function nettoyer_ante_jointures($mots_preced, $mots_base) {
	foreach($mots_base as $m) {
		if($mots_preced[$m]!=0) {
			# erase - articles
			$qa = sql_delete("spip_mots_articles","id_mot="._q($mots_preced[$m]));
			# erase - posts
			// c: 6/12/8 select ???
			$qf = sql_select("id_forum","spip_mots_forum","id_mot="._q($mots_preced[$m]));
		}
	}
}



?>