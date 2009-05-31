<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Novembre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################


function action_rec_mc_ajoutgroupes() {
	global $_POST;
	global $redirect;
	global $action, $arg, $hash, $id_auteur;
	# arg => rien !
	
	// controle action
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action $arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}

	$rub=_request('rub');
	$ag1=_request('idgrp');
	 $exc=_request('motsexclus') ;
//	echo list()=$exc;
#plug(11-07) : insertion dans la base
//		if($rub>-1) {
		if (($ag1=_request('idgrp'))==NULL ) {$ag1=array(0);}
			$listiddel = "";
			$sql = "SELECT id_groupe FROM spip_groupes_mots ORDER BY id_groupe";
			$result = mysql_query($sql);
			if ($result) {
				while ($row = mysql_fetch_assoc($result)) {
					extract($row);
					
					if (in_array($id_groupe, $ag1)) {
							$sql3 = "SELECT COUNT(id_groupe) AS num FROM spip_rmc_rubs_groupes WHERE id_groupe=$id_groupe AND id_rubrique=$rub";
							$result3 = spip_query($sql3);
						
							if ($result3) {
								$row = mysql_fetch_assoc($result3);
								extract($row);

								if ($num == 0) {
									$sql2 = "INSERT INTO spip_rmc_rubs_groupes (id_rubrique,id_groupe) VALUES ($rub,$id_groupe)";
									spip_query($sql2);
								
								}
								mysql_free_result($result3);
							}
					}
					else {
						if ($listiddel == "") $listiddel .= "$id_groupe";
						else $listiddel .= ",$id_groupe";
					}
				}
				mysql_free_result($result);
			}	
			$bilan="";
			if ($listiddel != "") {
				$sql = "delete FROM spip_rmc_rubs_groupes WHERE id_rubrique=$rub AND id_groupe IN ($listiddel)";
				$result = spip_query($sql);
			}
			
			$listiddel="";
			
			$listidmtdel ="";
		if (($exc=_request('motsexclus'))==NULL ) {$exc=array(0);}
			$sql5 = "SELECT id_mot FROM spip_mots ORDER BY id_mot";
			$result5 = mysql_query($sql5);
			if ($result5) {
				while ($row5 = mysql_fetch_assoc($result5)) {
					extract($row5);
					
					if (in_array($id_mot, $exc)) {
							$sql6 = "SELECT COUNT(id_mot_exclu) AS nummt FROM spip_rmc_mots_exclus WHERE id_mot_exclu=$id_mot AND id_rubrique=$rub";
							$result6 = spip_query($sql6);
						
							if ($result6) {
								$row6 = mysql_fetch_assoc($result6);
								extract($row6);

								if ($nummt == 0) {
									$sql7 = "INSERT INTO spip_rmc_mots_exclus (id_mot_exclu,id_rubrique) VALUES ($id_mot,$rub)";
									spip_query($sql7);
								
								}
								mysql_free_result($result6);
							}
					}
					else {
						if ($listidmtdel == "") $listidmtdel .= "$id_mot";
						else $listidmtdel .= ",$id_mot";
					}
				}
				mysql_free_result($result5);
			}	
			if ($listidmtdel != "") {
				$sql = "delete FROM spip_rmc_mots_exclus WHERE id_rubrique=$rub AND id_mot_exclu IN ($listidmtdel)";
				$result = spip_query($sql);
			}
			
			$listidmtdel="";
	
	# h.20/03 controle ???
	redirige_par_entete(rawurldecode($redirect));	
}
?>
