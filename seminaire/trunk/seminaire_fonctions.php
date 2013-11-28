<?php

/**
 * Fonction permettant de définir les autorisations des champs sur l'évènement
 * 
 * On vérifie que l'article parent a le champ séminaire à "on"
 * 
 * @param string $objet
 * @param int $id
 * 	L'identifiant de l'évènement
 * @return bool
 */
function seminaire_article_est_seminaire($objet, $id) {
	if(is_numeric($id))
		$id_article = sql_getfetsel('id_article','spip_evenements','id_evenement='.intval($id));
	
	if($id_article)
		$seminaire = sql_getfetsel('seminaire','spip_articles','id_article='.intval($id_article));
	else if($id_article = _request('id_article'))
		$seminaire = sql_getfetsel('seminaire','spip_articles','id_article='.intval($id_article));

	spip_log("$id_article - $seminaire",'test.'._LOG_ERREUR);
	return ($seminaire == 'on');
}

// autorisations des champs extras d'évènements du séminaire
foreach (array(
	'attendee',
	'origin',
	'notes') as $nom){
	$m = "autoriser_evenement_modifierextra_" . $nom . "_dist";
	$v = "autoriser_evenement_voirextra_" . $nom . "_dist";

	$code = "
		if (!function_exists('$m')) {
			function $m(\$faire, \$type, \$id, \$qui, \$opt) {
				return seminaire_article_est_seminaire(\$type, \$id);
			}
		}
		if (!function_exists('$v')) {
			function $v(\$faire, \$type, \$id, \$qui, \$opt) {
				return seminaire_article_est_seminaire(\$type, \$id);
			}
		}
	";

	eval($code);
}

?>