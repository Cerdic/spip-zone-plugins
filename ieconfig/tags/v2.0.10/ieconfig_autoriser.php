<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
// declarer la fonction du pipeline
function ieconfig_autoriser() {
}

function autoriser_ieconfigexport_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('exporter', 'configuration', $id, $qui, $opt);
}

function autoriser_ieconfigimport_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('importer', 'configuration', $id, $qui, $opt);
}

// Par defaut, seuls les webmestres peuvent utiliser IEconfig
function autoriser_configuration_exporter_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}

function autoriser_configuration_importer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}
