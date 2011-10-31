<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/
function formulaires_editer_membres_groupe_charger_dist($id_groupe='') {
	$contexte['id_groupe'] = $id_groupe;
	/* pour passer securiser action */
	$contexte['_action'] = array("editer_membres_groupe",$id_groupe);
	return $contexte;
}

function formulaires_editer_membres_groupe_traiter($id_groupe='') {
	/* partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php */
	$res=array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	
	$bsubmit = _request('bsubmit');
	if($bsubmit == _T('pass_ok')) {
		$action_editer_membres = charger_fonction('editer_membres_groupe','action');
		$action_editer_membres($id_groupe);
	} else if ($bsubmit == _T('asso:exclure')) {
		$action_supprimer_membres = charger_fonction('exclure_du_groupe','action');
		$action_supprimer_membres($id_groupe);
	}
	$res['message_ok'] = ''; 
	$res['redirect'] = generer_url_ecrire('edit_groupe', 'id='.$id_groupe);
	return $res;
	
}
?>
