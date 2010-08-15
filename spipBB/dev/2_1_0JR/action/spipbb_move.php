<?php

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# pour fonct. renumerote
include_spip('inc/spipbb_util');

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
	if (!function_exists('recuperer_numero')) include_spip('inc/filtres');

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
} // action_spipbb_move

?>
