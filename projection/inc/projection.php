<?php

# la projection d'un objet c'est le contenu de cet objet au format
# HTML pour affichage plaisant mais contenant toutes les données
# pour permettre une recopie

# dans quel répertoire on fait ça… local/projection/


function projection($objet, $id_objet) {
	spip_log("projection $objet:$id_objet", "projection");
	spip_log($_SERVER['REQUEST_URI'], 'projection'); # verifier qu'on s'execute bien sur le cron

	if ($projection = charger_fonction('projection_'.$objet, 'inc', true)) {
	spip_log("a $projection", 'projection');
		$projection($objet, $id_objet);
	} else {
		projection_dist($objet, $id_objet);
	}

	spip_log("a $projection", 'projection');
}


function projection_dist($objet, $id_objet) {
	spip_log("Je ne sais pas faire la projection de $objet:$id_objet", "projection");

}

function inc_projection_articles_dist($objet, $id_objet) {
	if (!$dir = projection_dir($objet, $id_objet)) {
		spip_log('echec', 'projection');
		return false;
	}

	# fichier de projection
	$f = $dir.objet_type($objet).'-'.$id_objet.'.json';
	spip_log($f, 'projection');

	# contenu à enregistrer
	include_spip('abstract_sql');
	$article = sql_fetsel('*', table_objet($objet), id_table_objet($objet).'='.$id_objet);
	# todo : retirer les champs inutiles, ajouter les jointures (auteurs, mots, documents)

	# on l'écrit et zou
	# à noter : json_encode est temporaire, on veut un vrai format avec de belles propriétés (par exemple un HTML bien structuré)
	return ecrire_fichier($f, json_encode($article));
}



function projection_dir($objet, $id_objet) {
	if ($p = sous_repertoire(_DIR_VAR, 'projection')
	AND $p = sous_repertoire($p,$objet))  # on pourrait organiser par rubrique
		return $p;
}
