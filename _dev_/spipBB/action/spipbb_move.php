<?php
#---------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                       #
#  File    : action/spipbb_move - renumerote un objet     #
#  Authors : chryjs, 2007                                 #
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

include_spip('inc/spipbb');

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_move()
{
	global $spip_lang_left, $spipbb_fromphpbb, $dir_lang, $time_start;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($objet, $id_item, $statut) = preg_split('/\W/', $arg);

	$id_item = intval($id_item);
	$redirige = urldecode(_request('redirect'));
	$id_rubrique = _request('id_rubrique');

	if (!empty($id_rubrique)) $redirige = parametre_url($redirige, 'id_rubrique', $id_rubrique, '&') ;
	if (!$id_item) {
		redirige_par_entete($redirige);
		exit;
	}

	$row = sql_fetsel("id_".$objet." , titre", "spip_".$objet."s", "id_".$objet."='$id_item'");
	if (!$row) {
		redirige_par_entete($redirige);
		exit;
	}

	switch ($statut) {
	case "up" :
		$move_increment = -15;
		break;
	case 'down' :
		$move_increment = +15;
		break;
	default :
		$move_increment = 0;
		break;
	}
	$ancien_numero = recuperer_numero($row['titre']) ;
	$nouveau_numero = $ancien_numero + $move_increment;

	if ( ($move_increment==0) OR ($nouveau_numero<5)) {
		redirige_par_entete($redirige);
		exit;
	}

	$titre = supprimer_numero($row['titre']);
	if ($nouveau_numero<10) $titre = "0" . $nouveau_numero . ". ".trim($titre);
	else $titre = $nouveau_numero . ". ".trim($titre);

	@sql_updateq("spip_".$objet."s", array(
					'titre'=>$titre
					),
			"id_$objet='$id_item'");

	spipbb_renumerote();

	redirige_par_entete($redirige);
// echo "id_item:$id_item<br>objet:$objet<br>id_rubrique:$id_rubrique<br>move_inc:$move_increment<br>titre:$titre<br>redirect:$redirige";
} // action_spipbb_move

?>