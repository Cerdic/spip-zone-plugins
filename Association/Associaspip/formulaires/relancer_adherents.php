<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_relancer_adherents_charger_dist() {
	// ce formulaire n'est charge que depuis la page action_relances qui est appele par le submit au formulaire d'edit relance
	// on recupere donc directement avec _request les champs du formulaire d'edit relance
	$sujet = _request('sujet');
	$message = _request('message');
	$contexte['_sujet'] = $sujet;
	$contexte['_message'] = $message;
	$id_tab = association_recuperer_liste('id');
	$statut_tab = association_recuperer_liste('statut');
	$contexte['_nb_messages'] = count ($id_tab);
	// on met en hidden toutes les infos pour les envoyer a l'action de traitement. Il n'y qu'un seul input dans ce formulaire : le bouton "ok" demande confirmation
	$contexte['_hidden'] = '<input name="sujet" type="hidden" value="'.$sujet.'" />';
	$contexte['_hidden'] .= '<input name="message" type="hidden" value="'. htmlentities($message, ENT_QUOTES, 'UTF-8') .'" />';
	foreach ($id_tab as $id_auteur) { // tableau statut[] contenant uniquement les cases cochees au formulaire precedent id_auteur => statut_auteur
		$contexte['_hidden'] .= '<input name="statut['.$id_auteur.']" type="hidden" value="'.$statut_tab[$id_auteur].'" />';
	}
	$contexte['_action'] = array('relance_adherents',$id_auteur); // pour passer securiser action

	return $contexte;
}

function formulaires_relancer_adherents_traiter_dist() {
	$res = array();
	$synchro = charger_fonction('relancer_adherents','action');

	list($nb_envoyes_ok, $nb_envoyes_echec, $nb_membres, $sans_emails) = $synchro(); // la fonction action retourne le nombre d'emails envoyes(ok et echec), le nombres de membres ayant un email et un tableau des auteurs sans email
	$nb_envoyes = $nb_envoyes_ok+$nb_envoyes_echec;
	if ($nb_envoyes>1) {
		$message = $nb_envoyes . _T('asso:emails_envoyes').' '._T('asso:a').' '.$nb_membres.' ';
		if ($nb_membres>1) {
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
	if (count($sans_emails)) { // on a des adherents sans email
		$message .= '<br />'. _T('asso:aucune_adresse_trouvee_pour_les_membres') . implode(", ", $sans_emails) .".";
	}
	$res['message_ok'] = $message;

	return $res;
}

?>