<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Recupere les numeros (des auteurs) dans la base de donnees (et non via requete !)
 *
 * @param array $id_objets
 *   Liste des ID dont on veut recuperer les numeros
 * @param bool $plus
 *   Indique si on recupere aussi (vrai) le type du numero et le titre ou pas (faux)
 * @param string $objet
 *   Indique le type d'objet dont les id sont passes
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @return array $telephones_objets
 *   id_$objet=>array(numeros)
 *   Les numeros sont des chaines ($plus=false) ou des listes ($plus=true) array(numero, type, titre)
 */
function association_recuperer_telephones($id_objets, $plus=true, $objet='auteur')
{
	$telephones_objets = array(); 	// initialisation du tableau renvoye
	if ( !is_array($id_obets) )
		$id_objets = array($id_objets);
	foreach ($id_objets as $id_objet) { // prepare la structure du tableau renvoye
		$telephones_objets[$id_objet] = array();
	}
	if (test_plugin_actif('COORDONNEES')) {
		$liste_objets = sql_in('nl.id_objet', $id_objet);
		$query = sql_select('nl.id_objet, nl.type, n.*','spip_numeros AS n INNER JOIN spip_numeros_liens AS nl ON nl.id_numero=n.id_numero', "$liste_objets AND nl.objet='$objet' ");
		while ($data = sql_fetch($query)) {
			$numero = ($data['pays']?("+$data[pays]".($data['region']?"($data[region])":'')."$data[numero]"):$data['numero']);
			if ($plus)
				$telephones_objets[$data['id_objet']][] = array($numero, $data['type'], $data['titre']);
			else
				$telephones_objets[$data['id_auteur']][] = $numero;
		}
	}
	return $telephones_objets;
}

/**
 * Recupere les adresses dans la base de donnees
 *
 * @param array $id_auteurs
 *   Liste des id_auteur dont on veut recuperer les numeros
 * @param bool $type_num
 *   Indique si on recupere aussi (vrai) le type du numero et le titre ou pas (faux)
 * @param string $type_obj
 *   Indique le type d'objet dont les id sont passes
 *   (ceci est prevu pour etendre facilement l'usage de la fonction si necessaire,
 *   vaut "auteur" par defaut)
 * @return array
 *   id_auteur=>array(adresses)
 *   Les adresses sont constituees d'une chaine, les caracteres de retour a la
 *   ligne et espace peuvent etre passe en parametre
 */
function association_recuperer_adresses($id_auteurs, $grouping='span', $newline='<br/>', $espace='&nbsp;', $type_obj='auteur')
{
	/* prepare la structure du tableau renvoye */
	$adresses_auteurs = array();
	foreach ($id_auteurs as $id_auteur) {
		$adresses_auteurs[$id_auteur] = array();
	}
	if (test_plugin_actif('COORDONNEES')) {
		$id_auteurs_list = sql_in('al.id_objet', $id_auteurs);
		$query = sql_select('al.id_objet, a.*', 'spip_adresses AS a INNER JOIN spip_adresses_liens AS al ON al.id_adresse=a.id_adresse', "$id_auteurs_list AND al.objet='$type_obj'");
		while ($data = sql_fetch($query)) {
			$adresses_auteurs[$data['id_objet']][] =  recuperer_fond("modeles/coordonnees_adresse", array ('voie' => $data['voie'],
				'complement' => $data['complement'],
				'boite_postale' => $data['boite_postale'],
				'code_postal' => $data['code_postal'],
				'ville' => $data['ville'],
				'region' => $data['region'],
				'pays' => $data['pays'],
				'htm4span' => $grouping, 'htm4nl' => $newline, 'htm4spc' => $space,
			));
		}
	}
	return $adresses_auteurs;
}

/* prend en argument un tableau d'id_auteurs et retourne un tableau id_auteur => code html listant toutes les adresses de l'auteur */
function association_formater_adresses($id_auteur, $htm_div='div', $htm_span='span', $htm4newline='<br />', $htm4space='&nbsp;')
{
	$adresses_auteurs = association_recuperer_adresses($id_auteur,$htm_span,$htm4newline,$htm4space);
	$adresses_string = array();
	foreach ($adresses_auteurs as $id_auteur => $adresses) { // on le transforme en tableau de strings html
		$adresses_strings[$id_auteur] = '';
		if (count($adresses)) {
			$adresses_strings[$id_auteur] = "<$htm_div class='adr'>". implode("</$htm_div>\n<$htm_div class='adr'>",$adresses) ."</$htm_div>\n";
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
function association_formater_emails($id_auteurs, $htm_div='div')
{
	/* on recupere tous les mails dans un tableau de tableau */
	$emails_auteurs = association_recuperer_emails($id_auteurs);
	$emails_strings = array();
	/* on le transforme en tableau de strings html */
	foreach ($emails_auteurs as $id_auteur => $emails) {
		$emails_strings[$id_auteur] = '';
		if (count($emails)) {
			foreach ($emails as $email) {
				$emails_strings[$id_auteur] .= "<$htm_div class='email'><a href='mailto:$email' title='"
					. _T('asso:ecrire_a') ." $email'>$email</a></$htm_div>\n";
			}
		}
	}
	return $emails_strings;
}

?>