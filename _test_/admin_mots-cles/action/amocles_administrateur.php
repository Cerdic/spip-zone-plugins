<?php

	// action/editer_auteurs.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
/***************************************************************************\
 * Certains éléments ici sont directement extraits de SPIP 192c
 
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// CP-20071105 - Adapté de action_editer_auteurs_dist()
function action_amocles_administrateur () {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$redirect = urldecode(_request('redirect'));
	if ($script = _request('script'))
		//$redirect = parametre_url($redirect,'script',$script,'&');
		$redirect = parametre_url($redirect,'titre',$script,'&');
	if ($titre = _request('titre'))
		$redirect = parametre_url($redirect,'titre',$titre,'&');

	if     (preg_match(",^\W*(\d+)\W(\w*)\W-(\d+)$,", $arg, $r)) {
		amocles_supprimer_admin_et_rediriger($r[2], $r[1], $r[3], parametre_url($redirect,'type',$r[2],'&'));
	}
	elseif (preg_match(",^\W*(\d+)\W(\w*)\W(\d+)$,", $arg, $r)) {
		amocles_ajouter_admin_et_redirige($r[2], $r[1], $r[3], parametre_url($redirect,'type',$r[2],'&'));
	}
	elseif (preg_match(",^\W*(\d+)\W(\w*)$,", $arg, $r)) {
		if  ($nouv_auteur = intval(_request('nouv_auteur'))) {
			amocles_ajouter_admin_et_redirige($r[2], $r[1], $nouv_auteur, parametre_url($redirect,'type',$r[2],'&'));
		} else if ($cherche = _request('cherche_auteur')) {
			if ($p = strpos($redirect, '#')) {
				$ancre = substr($redirect,$p);
				$redirect = substr($redirect,0,$p);
			} else $ancre ='';
			$redirect = parametre_url($redirect,'type',$r[2],'&');
			$res = rechercher_auteurs($cherche);
			$n = count($res);

			if ($n == 1)
			# Bingo. Signaler le choix fait.
				amocles_ajouter_admin_et_redirige($r[2], $r[1], $res[0], "$redirect&ids=" . $res[0] . "&cherche_auteur=" . rawurlencode($cherche) . $ancre);
			# Trop vague. Le signaler.
			elseif ($n > 16)
				redirige_par_entete("$redirect&cherche_auteur=$cherche&ids=-1" . $ancre);
			elseif (!$n)
			# Recherche vide (mais faite). Le signaler 
				redirige_par_entete("$redirect&cherche_auteur=$cherche&ids="  . $ancre);
			else
			# renvoyer un formulaire de choix
				redirige_par_entete("$redirect&cherche_auteur=$cherche&ids=" . join(',',$res)  . $ancre);

		}
	} else spip_log("action_amocles_administrateur: $arg pas compris", _AMOCLES_PREFIX);
}

// CP-20071105 - Adapté de supprimer_auteur_et_rediriger()
function amocles_supprimer_admin_et_rediriger ($type, $id, $id_auteur, $redirect) {

	if (preg_match(',^[a-z]*$,',$type)) {
		include_spip('inc/amocles_api');
		$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if(
			($ii = __plugin_lire_s_meta(_AMOCLES_META_PREFERENCES))
			&& isset($ii['admins_groupes_mots_ids'])
			) {
			$ii = array_values($ii['admins_groupes_mots_ids']);
			$ids = array_merge(array(1), $ii);
			if(in_array($id_auteur, $ids)) {
				$result = array();
				foreach($ids as $value) {
					if($value != $id_auteur) {
						$result[] = $value;
					}
				}
				if(count($result)) {
					__plugin_ecrire_s_meta ('admins_groupes_mots_ids', $result, _AMOCLES_META_PREFERENCES);
					__ecrire_metas();
					spip_log("KEYWORDS ADMIN #$id_auteur REMOVED BY ID_AUTEUR #$connect_id_auteur", _AMOCLES_PREFIX);
				}
			}
		}
	}
	if ($redirect) redirige_par_entete($redirect);
}

// 
function amocles_ajouter_admin_et_redirige($type, $id, $id_auteur, $redirect) {
	
	// $redirect par en script ??
	// obligé de forcer.
	$redirect = "ecrire/?exec=amocles_configuration";

	if (preg_match(',^[a-z]*$,',$type)) {
		include_spip('inc/amocles_api');
		$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if(
			($ii = __plugin_lire_s_meta(_AMOCLES_META_PREFERENCES))
			&& isset($ii['admins_groupes_mots_ids'])
			) {
			$ii = array_values($ii['admins_groupes_mots_ids']);
			$ids = array_merge(array(1), $ii);
			if(!in_array($id_auteur, $ids)) {
				$ids[] = $id_auteur;
				sort($ids);
				__plugin_ecrire_s_meta('admins_groupes_mots_ids', $ids, _AMOCLES_META_PREFERENCES);
				__ecrire_metas();
				spip_log("KEYWORDS ADMIN #$id_auteur ADDED BY ID_AUTEUR #$connect_id_auteur", _AMOCLES_PREFIX);	
			}
		}
	}
	redirige_par_entete($redirect);
}

// http://doc.spip.org/@rechercher_auteurs
function rechercher_auteurs($cherche_auteur)
{
	include_spip('inc/mots');
	include_spip('inc/charsets'); // pour tranlitteration
	$result = spip_query("SELECT id_auteur, nom FROM spip_auteurs");
	$table_auteurs = array();
	$table_ids = array();
	while ($row = spip_fetch_array($result)) {
		$table_auteurs[] = $row["nom"];
		$table_ids[] = $row["id_auteur"];
	}
	return mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);
}

?>
