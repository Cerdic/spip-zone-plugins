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

?>
