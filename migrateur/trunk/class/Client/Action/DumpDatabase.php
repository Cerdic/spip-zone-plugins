<?php

namespace SPIP\Migrateur\Client\Action;



class DumpDatabase extends ActionBase {

	public function run($data = null) {
		// si on ne peut pas gunzip, l'indiquer au serveur
		$gunzip = $this->destination->obtenir_commande_serveur('gunzip');

		if (!is_array($data)) {
			$data = array();
		}

		$data += array(
			'gzip_si_possible' => (bool)$gunzip,
			'mysqldump_si_possible' => true,
		);

		return $this->client->ask('DumpDatabase', $data, 'json');
	}


}
