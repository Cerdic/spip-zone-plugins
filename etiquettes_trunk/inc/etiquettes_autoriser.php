<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function etiquettes_autoriser(){}

// Par défaut, les deux autorisations suivantes sont les mêmes. Ce qui reprend le fonctionnement par défaut de SPIP.

// Teste si on à le droit d'ajouter des mots à un objet. Ajouter = sans toucher aux mots qui sont déjà liés
function autoriser_ajouteretiquettes_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

// Teste si on peut remplacer la liste des mots liés à l'objet par une autre liste de mots
function autoriser_remplaceretiquettes_dist($faire, $type, $id_groupe, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

?>
