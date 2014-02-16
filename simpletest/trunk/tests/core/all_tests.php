<?php
require_once('lanceur_spip.php');

class AllTests_spipTestCore extends SpipTestSuite {
	function AllTests_spipTestCore() {
		$this->SpipTestSuite('Test de Spip Core');
		$this->addDir(__FILE__);
	}
}

?>
