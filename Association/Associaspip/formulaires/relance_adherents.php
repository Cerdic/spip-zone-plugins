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
/* ce formulaire n'est charge que depuis la page action_relances qui est appele par le submit au formulaire d'edit relance */
/* on recupere donc directement avec _request les champs du formulaire d'edit relance */
function formulaires_relance_adherents_charger_dist() {
	/* on recupere dans le post du formulaire precedent les indications a charger */
	$sujet = _request('sujet');
	$message = _request('message');
	$contexte['_sujet'] = $sujet;
	$contexte['_message'] = $message;

	$id_tab = _request('id');
	$id_tab = (isset($id_tab)) ? $id_tab:array();

	$statut_tab = _request('statut');
	$statut_tab = (isset($statut_tab)) ? $statut_tab:array();

	$contexte[_nb_messages] = count ($id_tab);
	
	/* on met en hidden toutes les infos pour les envoyer a l'action de traitement. Il n'y a aucun input dans ce formulaire
	seul le bouton ok demande confirmation */
	$contexte['_hidden'] = '<input name="sujet" type="hidden" value="'.$sujet.'" />';
	$contexte['_hidden'] .= '<input name="message" type="hidden" value="'.htmlentities($message, ENT_QUOTES, 'UTF-8').'" />';

	foreach ($id_tab as $id_auteur) {
		/* tableau statut[] contenant uniquement les cases cochees au formulaire precedent id_auteur => statut_auteur */
		$contexte['_hidden'] .= '<input name="statut['.$id_auteur.']" type="hidden" value="'.$statut_tab[$id_auteur].'" />'; 
	}

	/* pour passer securiser action */
	$contexte['_action'] = array("relance_adherents",$id_auteur);
	
	return $contexte;
}

function formulaires_relance_adherents_traiter_dist() {
	$res=array();
	$synchro = charger_fonction('modifier_relances','action');
	list($nb_envoyes_ok, $nb_envoyes_echec, $nb_membres, $sans_emails) = $synchro(); /* la fonction action retourne le nombre d'emails envoyes(ok et echec), le nombres de membres ayant un email et un tableau des auteurs sans email */

	$nb_envoyes = $nb_envoyes_ok+$nb_envoyes_echec;

	if ($nb_envoyes>1) {
		$message = $nb_envoyes . _T('asso:emails_envoyes').' '._T('asso:a').' '.$nb_membres.' ';
		if ($nb_membres > 1) {
			$message .= _T('asso:membres').'.';
		} else {
			$message .= _T('asso:membre').'.';
		}

	} else {
		$message = $nb_envoyes . _T('asso:email_envoye');
	}

	if ($nb_envoyes_echec>0) {
		$message .= ' '.$nb_envoyes_echec.' ';
		$message .= ($nb_envoyes_echec>1)?_T('asso:echecs'):_T('asso:echecs');
		$message .= '.';
	}

	/* on a des adherents sans email */
	if (count($sans_emails)) {
		$message .= '<br/>'._T('asso:aucune_adresse_trouvee_pour_les_membres').implode(", ",$sans_emails).".";
	}

	$res['message_ok'] = $message;
	
	return $res;
}
?>
