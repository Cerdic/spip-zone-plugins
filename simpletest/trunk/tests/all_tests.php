<?php
require_once('lanceur_spip.php');

class AllSpipTests extends SpipTestSuite {
	function AllSpipTests() {
		$this->SpipTestSuite('Tous les tests SPIP');
		$this->addDir(__FILE__);
	}
}

?>
