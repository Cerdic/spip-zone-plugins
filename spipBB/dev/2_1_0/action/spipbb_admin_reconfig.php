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

		# scoty gaf_ecrireinstall 16/11/07 :
		# gestion du support de champs supp. sur : extra ou table
		# Cette table ($table_support) :
		# (cree par autre plugin ou spip_auteurs apres tout ??!! ou sur spipbb ??? )
		# sert de stockage des champs supplementaires auteurs,
		# certains sont requis par spipbb, d_autres sont optionnels
		# pour la fiche "profil" des visiteurs (et tous auteurs spip !)

		$support_auteurs = _request('support_auteurs');
		$table_support = _request('table_support');

		if ( !empty($support_auteurs)
			AND ( ($support_auteurs!=$GLOBALS['spipbb']['support_auteurs']) // soit on modifie le support
				OR (!empty($table_support)
					AND ($support_auteurs=='table')
					AND ($table_support!=$GLOBALS['spipbb']['table_support'])) ) ) // soit on modifie la table et c'est en base
		{

			if($support_auteurs=='table' AND $table_support) {
				# verif si "table_support" existe + liste champs existants (oui/non)
				$chps_exists = montre_table_support($table_support);
				# table existe !
				if(is_array($chps_exists)) {
					$t_creer_chps=array();
					foreach($chps_exists as $k => $v) {
						if($v=='non') {
							$t_creer_chps[]=$k;
						}
					}
					# si tous les champs supp. sont pas presents
					# on les ajoute a "table_support"
					if(count($t_creer_chps)) {
						support_ajout_champs($table_support,$t_creer_chps);
						# + recherche et transfert des extras (si existent)
						# vers "table_support".
						# Permet un passage gestion "extra" a "table"
						support_maj_extras($table_support,$t_creer_chps);
					}
				}
				else {
					# le choix de "table" est invalide
					# puisque table_support n_existe pas !
					# donc on repasse sur extra !
					$support_auteurs=='extra';
					$table_support = "";
				}
			}
			else {
				# on est en "extra", alors on nettoie $table_support au cas zou !
				$table_support = "";
			}

			$GLOBALS['spipbb']['support_auteurs'] = $support_auteurs;
			$GLOBALS['spipbb']['table_support'] = $table_support;
			$reconf=true;
		}


		# Proposer choix affichage (oui/non) des champs suppl. dans la config
		#
		$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
		$champs_definis = array();
		# on collecte les champs declarer dans globale champs_sap_spipbb
		foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
			$champs_definis[]=$champ;
		}
		# on compile par diff. cette liste
		$champs_optionnels = array_diff($champs_definis,$champs_requis);
		# on creer l_entree dans spipbb metas
		foreach ($champs_optionnels as $champ_a_valider) {
			$champ_a_valider=strtolower($champ_a_valider);
			$tbl_conf['affiche_'.$champ_a_valider]=_request('affiche_'.$champ_a_valider); # oui/non
			if (($affiche_champ = _request('affiche_'.$champ_a_valider))
				and $affiche_champ!=$GLOBALS['spipbb']['affiche_'.$champ_a_valider]) {
				$GLOBALS['spipbb']['affiche_'.$champ_a_valider]=$affiche_champ;
				$reconf=true;
			}
		}

		# gestion avatars
		#
		if (($affiche_avatar=_request('affiche_avatar'))
			and $affiche_avatar!=$GLOBALS['spipbb']['affiche_avatar']) {
			$GLOBALS['spipbb']['affiche_avatar']=$affiche_avatar;
			$reconf=true;
		}
		# on limite la taille en cas de mauvaise saisie : max 200 pixels
		$taille_image_maxi = '200';
		# sur page sujet
		if ((strlen($taille_avatar_suj=_request('taille_avatar_suj')))
			and intval($taille_avatar_suj)<>intval($GLOBALS['spipbb']['taille_avatar_suj'])) {
			$GLOBALS['spipbb']['taille_avatar_suj']=(intval($taille_avatar_suj)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_suj);
			$reconf=true;
		}
		# sur page contact
		if ((strlen($taille_avatar_cont=_request('taille_avatar_cont')))
			and intval($taille_avatar_cont)<>intval($GLOBALS['spipbb']['taille_avatar_cont'])) {
			$GLOBALS['spipbb']['taille_avatar_cont']=(intval($taille_avatar_cont)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_cont);
			$reconf=true;
		}
		# sur page profile
		if ((strlen($taille_avatar_prof=_request('taille_avatar_prof')))
			and intval($taille_avatar_prof)<>intval($GLOBALS['spipbb']['taille_avatar_prof'])) {
			$GLOBALS['spipbb']['taille_avatar_prof']=(intval($taille_avatar_prof)>$taille_image_maxi) ? $taille_image_maxi : intval($taille_avatar_prof);
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


// ------------------------------------------------------------------------------
# generer tableau des champs profils dans sap si existent
// ------------------------------------------------------------------------------
function montre_table_support($table) {
	# y a quoi dans cette table ?
	//$contenu = spip_mysql_showtable('spip_'.$table);
	$contenu = sql_showtable('spip_'.$table,true);
	$chps_presents=array();
	if(is_array($contenu)) {
		# verif nom champ (dans base/sap_spipbb.php)
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
	/* kcc ça ??
	$connexion = $GLOBALS['connexions'][0];
	$prefixe = $connexion['prefixe'];
	if ($prefixe and $prefixe!='spip') $table_support = preg_replace('/^spip/', $prefixe, $table_support);
	*/
	## h. oui pour le controle ;-) mais alors :
	$table_support = preg_replace($GLOBALS['table_prefix'], '', $table_support);

	$nomtable = "spip_".$table_support;
	foreach($creer_champs as $chp) {
		#recup def des champs (dans base/sap_spipbb.php)
		$def = $GLOBALS['champs_sap_spipbb'][$chp]['sql'];
		sql_alter("TABLE $nomtable ADD $chp $def");
	}
} // support_ajout_champs

// ------------------------------------------------------------------------------
# Deverser extras dans table support (sap)
# Et, au passage, on insert tous auteur non encore presens dans la table
// ------------------------------------------------------------------------------
function support_maj_extras($table_support,$maj_chps) {
	# recolte extras des auteurs pour les champs suppl.
	$r = sql_select("id_auteur, extra","spip_auteurs");
	while($sr = sql_fetch($r)) {
		$extras = unserialize($sr['extra']);
		// c: 10/2/8 compat pg_sql
		$set=array();
		# pour chq champs gaf en maj ou crea (pas les anciens)
		foreach($maj_chps as $chp) {
			if($chp=='date_crea_spipbb' && $extras['date_crea_spipbb']=='') {
				$set[$chp]="NOW()";
			}
			else {
				$set[$chp]=_q($extras[$chp]);
			}
		}
		//$set=substr($set,1);
		//if(strlen($set)>0) { $sep = ","; }

		$q = sql_select("id_auteur","spip_$table_support","id_auteur=".$sr['id_auteur']);
		if ($sq=sql_fetch($q)) {
			if($set!='') {
				sql_updateq("spip_$table_support",$set,"id_auteur=".$sr['id_auteur']);
			}
		}
		else {
			sql_insertq("spip_$table_support",array_merge(array('id_auteur'=>$sr['id_auteur']),$set) );
		}
	}
} // support_maj_extras

?>