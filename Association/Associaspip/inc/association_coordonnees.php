<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

/* Cette fonction prend en argument un tableau d'id_auteurs et renvoie un tableau
id_auteur => array(numeros) */
function association_recuperer_telephones($id_auteurs)
{
	/* prepare la structure du tableau renvoye */
	$telephones_auteurs = array();
	foreach ($id_auteurs as $id_auteur) {
		$telephones_auteurs[$id_auteur] = array();
	}

	if (plugin_actif('COORDONNEES')) {
		$id_auteurs_list = sql_in('nl.id_objet', $id_auteurs);
		$query = sql_select('nl.id_objet as id_auteur, n.numero as numero','spip_numeros as n INNER JOIN spip_numeros_liens AS nl ON nl.id_numero=n.id_numero', $id_auteurs_list.' AND nl.objet=\'auteur\'');
		while ($data = sql_fetch($query))
			$telephones_auteurs[$data['id_auteur']][] = $data['numero'];
	}

	return $telephones_auteurs;
}

/* prend en argument un tableau d'id_auteurs et retourne un tableau id_auteur => code html listant tous les numeros de l'auteur */
function association_recuperer_telephones_string($id_auteurs)
{
	/* on recupere tous les numeros dans un tableau de tableau */
	$telephones_auteurs = association_recuperer_telephones($id_auteurs);

	$telephones_strings = array();

	/* on le transforme en tableau de strings html */
	foreach ($telephones_auteurs as $id_auteur => $telephones) {
		$telephones_strings[$id_auteur] = '';
		if (count($telephones)) {
			foreach ($telephones as $telephone) {
				$telephones_strings[$id_auteur] .=  print_tel($telephone).'<br/>';
			}
		}
	}

	return $telephones_strings;
}

/* prend en argument un id_auteur et retourne un tableau d'adresses */
/* TODO: l'affichage du pays devrait etre optionnel */
function association_recuperer_adresses($id_auteur)
{
		$tab_result=array();
		if (plugin_actif('COORDONNEES')) {
			$query = sql_select('a.titre as titre, a.voie as voie, a.complement as complement, a.boite_postale as boite_postale, a.code_postal as code_postal, a.ville as ville, a.pays as pays', 'spip_adresses as a INNER JOIN spip_adresses_liens AS al ON al.id_adresse=a.id_adresse','al.id_objet='.$id_auteur.' and al.objet=\'auteur\'');
			while ($data = sql_fetch($query)) {
				$voie = ($data['voie'])?$data['voie'].'<br/>':'';
				$complement = ($data['complement'])?$data['complement'].'<br/>':'';
				$boite_postale = ($data['boite_postale'])?$data['boite_postale'].'<br/>':'';
				$code_postal = ($data['code_postal'])?$data['code_postal'].'&nbsp;':'';
				$ville = ($data['ville'])?$data['ville'].'<br/>':'';
				$pays = ($data['pays'])?$data['pays'].'<br/>':'';
	
				$tab_result[] = $voie.$complement.$boite_postale.$code_postal.$ville.$pays;
			}
		}
		return $tab_result;
}

/* prend en argument 1 id auteur et renvoie un string html listant toutes ses adresses */
function association_recuperer_adresses_string($id_auteur)
{
	$adresses = association_recuperer_adresses($id_auteur);
	$adresses_string = '';
	if (count($adresses)) {
		foreach ($adresses as $adresse) {
			$adresses_string .= $adresse.'<br/>';
		}
	}
	return $adresses_string;
}

/* Cette fonction prend en argument un tableau d'id_auteurs et renvoie un tableau
id_auteur => array(emails)  */
function association_recuperer_emails($id_auteurs)
{
	/* prepare la structure du tableau renvoye */
	$emails_auteurs = array();
	foreach ($id_auteurs as $id_auteur) {
		$emails_auteurs[$id_auteur] = array();
	}

	/* on commence par recuperer les emails de la table spip_auteurs */
	$id_auteurs_list = sql_in('id_auteur', $id_auteurs);
	$auteurs_info = sql_select('id_auteur, email', 'spip_auteurs', $id_auteurs_list." AND email <> ''");
	
	while ($auteur_info = sql_fetch($auteurs_info)) {
		$emails_auteurs[$auteur_info['id_auteur']][] = $auteur_info['email'];
	}

	/* si le plugin coordonnees est actif, on recupere les emails contenus dans coordonnees */
	if (plugin_actif('COORDONNEES')) {
		$id_auteurs_list = sql_in('el.id_objet', $id_auteurs);
		$query = sql_select('el.id_objet as id_auteur, e.email as email','spip_emails as e INNER JOIN spip_emails_liens AS el ON el.id_email=e.id_email', $id_auteurs_list.' AND el.objet=\'auteur\'');
		while ($data = sql_fetch($query)) {
				$emails_auteurs[$data['id_auteur']][] = $data['email'];
		}
	}

	return $emails_auteurs;
}

/* prend en argument un tableau d'id_auteurs et retourne un tableau id_auteur => code html listant tous les emails de l'auteur */
function association_recuperer_emails_string($id_auteurs)
{
	/* on recupere tous les mails dans un tableau de tableau */
	$emails_auteurs = association_recuperer_emails($id_auteurs);

	$emails_strings = array();

	/* on le transforme en tableau de strings html */	
	foreach ($emails_auteurs as $id_auteur => $emails) {
		$emails_strings[$id_auteur] = '';
		if (count($emails)) {
			foreach ($emails as $email) {
				$emails_strings[$id_auteur] .= "<a href='mailto:$email' title='"
					. _L('Ecrire &agrave;') . ' ' . $email . "'>"
				 	. $email
		 			. '</a><br/>';
			}
		}
	}

	return $emails_strings;
}

function print_tel($n)
{
	$n = preg_replace('/\D/', '', $n);
	if (!intval($n)) return '';
	return preg_replace('/(\d\d)/', '\1&nbsp;', $n);
}
?>
