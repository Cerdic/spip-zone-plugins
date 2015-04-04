<?php

namespace SPIP\Migrateur\Client\Action;



class Test extends ActionBase {

	public function run($data = null) {
		if (is_string($data)) {
			$texte = $data;
		} else {
			$texte = "Message envoyé le " . date("Y-m-d H:i:s");
		}

		return array('Test', $texte, 'json');
	}

}
