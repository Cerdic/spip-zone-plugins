<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_configurer                      #
#  Authors : chryjs, 2008                                  #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs¡@!free¡.!fr                            #
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

	// champs supplémentaires pour les avatars & co

	# Proposer choix affichage (oui/non) des champs suppl. dans la config
	#
	$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
	$champs_definis = array();
	# on collecte les champs declares dans globale champs_sap_spipbb
	if (is_array($GLOBALS['champs_sap_spipbb'])) {
		foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
			$champs_definis[]=$champ;
		}
	}
	# on compile par diff. cette liste
	$champs_optionnels = array_diff($champs_definis,$champs_requis);
	# on creer l_entree dans spipbb metas
	foreach ($champs_optionnels as $champ_a_valider) {
		$champ_a_valider=strtolower($champ_a_valider);

		if (($affiche_champ = _request('affiche_'.$champ_a_valider))
			and $affiche_champ!=$spipbb_metas['affiche_'.$champ_a_valider]) {
			$spipbb_metas['affiche_'.$champ_a_valider]=$affiche_champ;
			$reconf=true;
		}
	}
	
	// voir si cette partie ne doit pas etre dans un configurer-particulier
	
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
		AND ( ($support_auteurs!=$spipbb_metas['support_auteurs']) // soit on modifie le support
			OR (!empty($table_support)
				AND ($support_auteurs=='table')
				AND ($table_support!=$spipbb_metas['table_support'])) ) ) // soit on modifie la table et c'est en base
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

		$spipbb_metas['support_auteurs'] = $support_auteurs;
		$spipbb_metas['table_support'] = $table_support;
		$reconf=true;
	}

	// demande de creation d'un secteur contenant un forum spipbb preconfigure
	if ($arg=="spipbb_rubriques" AND _request('spipbbrub_now') AND empty($spipbb_metas['id_secteur']) ) 
	{
	//		if (autoriser('publierdans', 'rubrique', $id_rubrique))  ??

		lang_select($GLOBALS['visiteur_session']['lang']);
		$lang = $GLOBALS['spip_lang'];
		if (!$lang) $lang=$GLOBALS['meta']['langue_site'];
		include_spip('inc/rubriques');
		$id_secteur=creer_rubrique_nommee(_T('spipbb:forums_spipbb')); // inc/rubriques
		sql_updateq('spip_rubriques',array('statut'=>'publie'),"id_rubrique=$id_secteur");
		$spipbb_metas['id_secteur'] = $id_secteur;
		$id_categorie=creer_rubrique_nommee(_T('spipbb:forums_categories'),$id_secteur);
		sql_updateq('spip_rubriques',array('statut'=>'publie'),"id_rubrique=$id_categorie");
		$id_forum = sql_insertq("spip_articles", array(
							'titre' => _T('spipbb:forums_titre'),
							'id_rubrique' => $id_categorie,
							'id_secteur' =>  $id_secteur,
							'date' => 'NOW()',
							'accepter_forum' => 'oui',
							'statut' => 'publie',
							'lang' => $lang)
					);
		// controler si le serveur n'a pas renvoye une erreur
		if ($id_forum > 0) 
			sql_insertq('spip_auteurs_articles', array('id_auteur' => $GLOBALS['visiteur_session']['id_auteur'], 'id_article' => $id_forum));;

		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_article/$id_forum'");

		$reconf=true;
	}
	
	// demande de creation d'un group de mots cles preconfigure
	if ($arg=="spipbb_mots_cles" 
		AND _request('spipbbmots_now') 
//		AND ( $spipbb_metas['config_groupe_mots']!='oui' OR $spipbb_metas['config_mot_cles']!='oui') 
		) 
	{
		// on cherche s'il n'existe pas deja
		$row = sql_fetsel('id_groupe','spip_groupes_mots', "titre = 'spipbb'" ,'','','1');
		if (!$row) { // Celui la n'existe pas
			$id_groupe = sql_insertq("spip_groupes_mots",array(
					'titre' => 'spipbb',
					'descriptif' => _T('spipbb:mot_groupe_moderation'),
					'tables_liees' => 'articles,rubriques,forum',
					'unseul' =>'non',
					'obligatoire' => 'non',
					'minirezo' => 'oui',
					'comite' => 'oui',
					'forum' => 'oui' )
						);
			$row['id_groupe'] = $id_groupe;
			}
		$spipbb_metas['id_groupe_mot'] = $row['id_groupe'];
		$spipbb_metas['config_groupe_mots']='oui';
		// on cree les mots cles associes
		$spipbb_metas['id_mot_ferme'] = spipbb_init_mot_cle("ferme",$spipbb_metas['id_groupe_mot']);
		$spipbb_metas['id_mot_annonce'] = spipbb_init_mot_cle("annonce",$spipbb_metas['id_groupe_mot']);
		$spipbb_metas['id_mot_postit'] = spipbb_init_mot_cle("postit",$spipbb_metas['id_groupe_mot']);
		$spipbb_metas['config_mot_cles']='oui';
		$reconf=true;
	}
	
	if ($reconf) {
		// controles dans save_metas		
		// sauvegarde
		$GLOBALS['spipbb']=$spipbb_metas;
		spipbb_save_metas();
	}
} // appliquer_modifs_config

?>