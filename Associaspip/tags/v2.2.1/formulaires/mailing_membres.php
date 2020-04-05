<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

/***************************************************************************/
/* Formulaire en trois temps gere par la variable confirmForm              */
/*  - 0 : selection des destinataires et redaction du message              */
/*  - 1 : page de confirmation(requiert juste la validation)               */
/*  - 2 : reponse de l'action (erreurs d'envois et autres)                 */
/*                                                                         */
/***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_mailing_membres_charger_dist() {
	$confirmForm = intval(_request('confirmForm'));
	$selectedId = _request('id');
	if (($selectedId=='') || ($confirmForm==0)) { // pas de destinataires selectionnés ou on vient de modifier les filtres, on les charges tous
		$contexte['selectTous'] = 1;
	} else {
		$contexte['selectId'] = $selectedId;
	}

	// filtres : on recupere par request les valeurs des filtres si il y a lieu, sinon on les mets a leur valeur par defaut
	$id_groupe = intval(_request('filtre_id_groupe'));
	if ($id_groupe!=0) { //on a bien un filtre sur le groupe, on envoie le critere de boucle
		$contexte['filtre_id_groupe'] = $id_groupe;
		$contexte['id_groupe'] = $id_groupe;
	}

	$statut_interne = trim(_request('filtre_statut_interne'));
	$relance = intval(_request('filtre_relance')); // si a 1, c'est un mail de relance
	if ($relance != 0) { // on ne peut envoyer de relance qu'aux adherent echus
		$statut_interne = 'echu';
	}
	$contexte['filtre_relance'] = $relance;

	// on verifie que le statut interne soit bien a une valeur possible, sinon on le met a actif soit tout sauf sorti
	switch ($statut_interne) {
	case 'sorti':
	case 'prospect':
	case 'ok':
	case 'echu':
	case 'relance':
		$contexte['statut_interne'] = $statut_interne;
		break;
	case 'tous':
		$contexte['statut_interne'] = "sorti:prospect:ok:echu:relance";
	case 'actif':
	default:
		$contexte['statut_interne'] = "prospect:ok:echu:relance";
		$statut_interne = 'actif';
		break;
	}
	$contexte['filtre_statut_interne'] = $statut_interne;

	if ($confirmForm == 1) { // on affiche le formulaire de confirmation
		$contexte['_nb_messages'] = count($selectedId);
		$contexte['_action'] = array('mailing_membres',$id_auteur); // pour passer securiser action
		$contexte['confirmForm'] = 1;
	}

	// contenu du message et titre: on recupere par request le sujet et message, si ils sont vide et qu'on est en mode relance, on rempli avec le texte par defaut
	$sujet = _request('_sujet');
	$message = _request('_message');
	if (($relance != 0) && ($sujet=='') && ($message=='')) {
		$sujet = _T('asso:titre_relance');
		$message = _T('asso:message_relance');
	}

	$contexte['_sujet'] = $sujet;
	$contexte['_message'] = $message;


	// verifie si on doit afficher le formulaire de confirmation
	if ($confirmForm == 2) {
		$contexte['editable']=false;
		$contexte['confirmForm'] = 1;
	} else {
		$contexte['editable']=true;
	}
	return $contexte;
}

function formulaires_mailing_membres_traiter_dist() {
	$res = array();
	$res['message_ok'] = '';
	// Si on ne fait que mettre a jour le filtre, on ne fait rien (le formulaire se recharge tout seul)
	if (intval(_request('majFiltres'))==1) {
		// forcer le filtre de statut a echu si on fait une relance
		if (intval(_request('filtre_relance'))==1) {
			set_request('filtre_statut_interne', 'echu');
		}
		return $res;
	}

	// on a envoye le formulaire de selection, preparer l'affichage du formulaire de confirmation
	if (_request('confirmForm')=='') {
		set_request('confirmForm', 1); // pour afficher le formulaire de confirmation
		return $res;
	}

	set_request('confirmForm', 2); // pour ne pas afficher le formulaire de selection ni de confirmation

	// on vient soumettre le formulaire de confirmation, on envoie vraiment les mails maintenant
	$f = charger_fonction('mailing_membres','action');

	list($nb_envoyes_ok, $nb_envoyes_echec, $nb_membres, $sans_emails) = $f(); // la fonction action retourne le nombre d'emails envoyes(ok et echec), le nombres de membres ayant un email et un tableau des auteurs sans email
	$nb_envoyes = $nb_envoyes_ok+$nb_envoyes_echec;
	if ($nb_envoyes>1) {
		$message = _T('asso:emails_envoyes', array('nombre'=>$nb_envoyes)).' / '.$nb_membres.' ';
		$message .= ($nb_membres>1)_T('asso:membres'):_T('asso:libelle_membre');
		$message .= '.';
	} else {
		$message = _T('asso:email_envoye', array('nombre'=>$nb_envoyes));
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
