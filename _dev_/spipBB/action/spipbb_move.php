<?php
#---------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                       #
#  File    : action/spipbb_move - renumerote un objet     #
#  Authors : chryjs, 2007                                 #
#            2004+ Jean-Luc Bechennec certaines fonctions #
#  Contact : chryjs¡@!free¡.!fr                           #
#---------------------------------------------------------#

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

include_spip('inc/minipres');
include_spip('inc/spipbb');

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_move()
{
	global $spip_lang_left, $spipbb_fromphpbb, $dir_lang, $time_start;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_item = intval($arg);
	if (empty($id_item))
	{
		minipres( _T('spipbb:titre_spipbb'), "<strong>"._T('avis_non_acces_page')."</strong>" );
		exit;
	}
	$objet = _request('objet');
	$id_rubrique = intval(_request('id_rubrique'));
	$move_increment = intval(_request('move'));

	$query = "SELECT id_$objet, titre FROM spip_".$objet."s WHERE id_$objet=$id_item";
	$result = sql_query($query);
	$row = sql_fetch($result);
	$numero = recuperer_numero($row['titre']) + $move_increment;
	$titre = supprimer_numero($row['titre']);
	$titre = $numero . ". ".trim($titre);
	$redirige = urldecode(_request('redirect'));
/*
	@sql_updateq("spip_".$objet."s", array(
					'titre'=>$titre
					),
			"id_$objet='$id_item'");

	$redirect = parametre_url($redirige,
		'id_rubrique', $id_rubrique, '&') ;

	redirige_par_entete($redirect);
*/
echo "id_item:$id_item<br>objet:$objet<br>id_rubrique:$id_rubrique<br>move_inc:$move_increment<br>redirect:$redirige";
} // action_spipbb_move

?>