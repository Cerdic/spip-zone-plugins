<?php
/**
 * Change le statut des emplacements
 *
 * Il s'agit d'une 'action SPIP' (<i>url_action()</i>) qui doit recevoir en argument une
 * chaine construite sur le modèle : 'action-ID' avec 'action' :
 * <ul>
 * <li>'desactiver' : désactivation : passe en '1inactif' si était '2actif'</li>
 * <li>'activer' : activation : passe en '2actif' si était '1inactif'</li>
 * <li>'rompu' : introuvable : passe en '4rompu' si était '1inactif' ou '2actif'</li>
 * <li>'out_trash' : sortie de poubelle : passe en '1inactif' si était '5poubelle'</li>
 * <li>'trash' : poubelle : passe en '5poubelle' si était '1inactif' ou '2actif'</li>
 * </ul>
 *
 * @name 		Activer banniere
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Actions
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_activer_banniere(){
	include_spip('inc/autoriser');
	include_spip('inc/pubban_process');
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($activer, $id_empl) = explode('-', $arg);

	if (intval($id_empl)){
		$statut = pubban_recuperer_banniere($id_empl, "statut");
		$editer_empl = charger_fonction('editer_banniere', 'inc');
		switch ($activer) {
			case 'activer' :
				if($statut == '1inactif') 
					$ok = $editer_empl($id_empl, array("statut" => "2actif"));
				break;
			case 'desactiver' :
				if($statut == '2actif') 
					$ok = $editer_empl($id_empl, array("statut" => "1inactif"));
				break;
			case 'rompu' :
				if(in_array($statut, array('1inactif', '2actif')))
					$ok = $editer_empl($id_empl, array("statut" => "4rompu"));
				break;
			case 'trash' :
				if(in_array($statut, array('1inactif', '2actif')))
					$ok = $editer_empl($id_empl, array("statut" => "5poubelle"));
				break;
			case 'out_trash' :
				if($statut == '5poubelle') 
					$ok = $editer_empl($id_empl, array("statut" => "1inactif"));
				break;
		}
		if ($redirect = _request('redirect') ) {
			$redirect = str_replace('&amp;', '&', $redirect);
			if( $mode = _request('mode') )
				$redirect = parametre_url($redirect, 'mode', $mode);
			include_spip('inc/headers');
			redirige_par_entete( $redirect );
		}
	}

	return;
}
?>