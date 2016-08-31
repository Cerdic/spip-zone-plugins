<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function deplacer_rubriques_autoriser(){}

function autoriser_configurer_deplacer_rubriques_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt); // seulement les webmestres
}
