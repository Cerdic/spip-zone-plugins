<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/newsletters");

function action_generer_newsletter_dist($id_newsletter = null, $force = false){
	if (is_null($id_newsletter)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_newsletter = $securiser_action();
	}

	include_spip('inc/autoriser');
	if (autoriser('generer', 'newsletter', $id_newsletter)){
		$row = sql_fetsel('*', 'spip_newsletters', 'id_newsletter=' . intval($id_newsletter));

		// si cuite on ne genere pas, sauf si force
		if (!$row['baked'] OR $force){
			$patron = $row['patron'];
			$date = intval($row['date_redac'])?$row['date_redac']:$row['date'];

			$set = array();
			$set['html_email'] = newsletters_recuperer_fond($id_newsletter, $patron, $date);
			if (trouver_fond("$patron.texte","newsletters"))
				$set['texte_email'] = newsletters_recuperer_fond($id_newsletter, "$patron.texte", $date);
			else
				$set['texte_email'] = newsletters_html2text($set['html_email']);

			$set['html_page'] = '';
			if (trouver_fond("$patron.page","newsletters"))
				$set['html_page'] = newsletters_recuperer_fond($id_newsletter, "$patron.page", $date);

			#header('Content-Type: text/plain; charset=utf-8');
			#echo($set['texte_email']);
			#die();

			include_spip("action/editer_objet");
			objet_modifier("newsletter",$id_newsletter,$set);

		}
	}
}

/**
 * Recuperer un fond avec des liens internes public, et la bonne date
 *
 * @param int $id_newsletter
 * @param string $patron
 * @param null|string $date
 * @return string
 */
function newsletters_recuperer_fond($id_newsletter, $patron, $date = null){

	if (is_null($date))
		$date = date('Y-m-d 00:00:00');

	// on passe la globale lien_implicite_cible_public en true
	// pour avoir les liens internes en public (en non prive d'apres le contexte)
	// credit de l'astuce: denisb & rastapopoulos
	$GLOBALS['lien_implicite_cible_public'] = true;

	$texte = recuperer_fond(
		"newsletters/$patron",
		array(
			'date' => $date,
			'id_newsletter' => $id_newsletter,
		)
	);

	// on revient a la config initiale
	unset($GLOBALS['lien_implicite_cible_public']);

	return $texte;
}
