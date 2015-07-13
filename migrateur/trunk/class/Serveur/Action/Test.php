<?php

namespace SPIP\Migrateur\Serveur\Action;



class Test extends ActionBase {

	public function run($data = null) {
		$this->log_run("Test reçu : ");
		$this->log($data);

		return "Message reçu le " . date("Y-m-d H:i:s");
	}

}
