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

	if (test_plugin_actif('COORDONNEES')) {
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
				$telephones_strings[$id_auteur] .=  recuperer_fond("modeles/coordonnees_telephoniques", array ('telephone' => $telephone)).'<br/>';
			}
		}
	}

	return $telephones_strings;
}

/* Cette fonction prend en argument un tableau d'id_auteurs et renvoie un tableau
id_auteur => array(adresses). Les adresses sont constituees d'une chaine, les caracteres de retour a la ligne et espace peuvent etre passe en parametre */
function association_recuperer_adresses($id_auteurs, $newline="<br/>", $espace="&nbsp;")
{
	/* prepare la structure du tableau renvoye */
	$adresses_auteurs = array();
	foreach ($id_auteurs as $id_auteur) {
		$adresses_auteurs[$id_auteur] = array();
	}
	
	if (test_plugin_actif('COORDONNEES')) {
		$id_auteurs_list = sql_in('al.id_objet', $id_auteurs);
		$query = sql_select('al.id_objet as id_auteur, a.titre as titre, a.voie as voie, a.complement as complement, a.boite_postale as boite_postale, a.code_postal as code_postal, a.ville as ville, a.pays as pays', 'spip_adresses as a INNER JOIN spip_adresses_liens AS al ON al.id_adresse=a.id_adresse',$id_auteurs_list.' AND al.objet=\'auteur\'');
		while ($data = sql_fetch($query)) {
			
			$adresses_auteurs[$data['id_auteur']][] = recuperer_fond("modeles/coordonnees_postales", array ('voie' => $data['voie'],
																'complement' => $data['complement'],
																'boite_postale' => $data['boite_postale'],
																'code_postal' => $data['code_postal'],
																'ville' => $data['ville'],
																'pays' => $data['pays']
															));
		}
	}
	return $adresses_auteurs;
}

/* prend en argument un tableau d'id_auteurs et retourne un tableau id_auteur => code html listant toutes les adresses de l'auteur */
function association_recuperer_adresses_string($id_auteur)
{
	$adresses_auteurs = association_recuperer_adresses($id_auteur);

	$adresses_string = array();

	/* on le transforme en tableau de strings html */
	foreach ($adresses_auteurs as $id_auteur => $adresses) {
		$adresses_strings[$id_auteur] = '';
		if (count($adresses)) {
			$adresses_strings[$id_auteur] = implode("<br/><br/>",$adresses);
		}
	}

	return $adresses_strings;
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
	if (test_plugin_actif('COORDONNEES')) {
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
?>
