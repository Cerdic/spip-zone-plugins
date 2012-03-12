<?php

/**
 * Balise #CALENDRIER_MINI
 * Auteur James (c) 2006-2012
 * Plugin pour SPIP 3.0.0
 * Licence GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('calendriermini_fonctions');

function balise_CALENDRIER_MINI($p) {
	return calculer_balise_dynamique($p,'CALENDRIER_MINI', array(VAR_DATE, 'id_rubrique','id_article', 'id_mot'));
}
 
function balise_CALENDRIER_MINI_stat($args, $filtres) {
 //les parametres passe en {...}, les filtres sont des vraiss filtres
	return $args;
}

/**
 * Syntaxe raccourcie du plugin
 * #CALENDRIER_MINI
 * #CALENDRIER_MINI{#SELF}
 * #CALENDRIER_MINI{#SELF,#URL_PAGE{json_calendrier_mini}}
 *
 * Syntaxe ancienne (ou plugin agenda)
 * #CALENDRIER_MINI{#ENV{date}}
 * #CALENDRIER_MINI{#ENV{date},date}
 * #CALENDRIER_MINI{#ENV{date},date,#SELF}
 * #CALENDRIER_MINI{#ENV{date},date,#SELF,#URL_PAGE{json_calendrier_mini}}
 *
 * @param string $date
 *   date automatique collectee par VAR_DATE
 * @param int $id_rubrique
 * @param int $id_article
 * @param int $id_mot
 * @param null $self_or_date_or_nothing
 * @param null $urljson_or_var_date_or_nothing
 * @param null $self_or_nothing
 * @param null $urljson_or_nothing
 * @return array
 */
function balise_CALENDRIER_MINI_dyn($date, $id_rubrique = 0, $id_article = 0, $id_mot = 0,
                                    $self_or_date_or_nothing = null, $urljson_or_var_date_or_nothing = null, $self_or_nothing = null, $urljson_or_nothing = null) {
	$var_date = VAR_DATE;
	$url = null;
	$url_json = null;

	if($self_or_date_or_nothing){
		// est-ce une date ou une url ?
		if (preg_match(",^[\d\s:-]+$,",$self_or_date_or_nothing)
		  AND list($annee, $mois, $jour, $heures, $minutes, $secondes) = recup_date($self_or_date_or_nothing)
		  AND $annee){
			// si c'est une date on est dans l'ancienne syntaxe
			$date = $self_or_date_or_nothing;
			$var_date = $urljson_or_var_date_or_nothing;
			$url = $self_or_nothing;
			$url_json = $urljson_or_nothing;
		}
		else {
			// sinon on est sur la nouvelle syntaxe
			$url = $self_or_date_or_nothing;
			$url_json = $urljson_or_var_date_or_nothing;
		}
	}


	/* tenir compte de la langue, c'est pas de la tarte */
	return array('formulaires/calendrier_mini', 3600, 
		array(
			'date' => $date?$date:date('Y-m'),
			'var_date' => $var_date,
			'self' => $url?$url:self(),
			'urljson' => $url_json?$url_json:generer_url_public("calendrier_mini.json"),
			'id_rubrique' => $id_rubrique,
			'id_article' => $id_article,
			'id_mot' => $id_mot
		));
}

?>