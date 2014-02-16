<?php
require_once('lanceur_spip.php');

class AllTests_spipTest extends SpipTestSuite {
    function AllTests_spipTest() {
        $this->SpipTestSuite('Test de la Classe SpipTest');
		$this->addDir(__FILE__);
    }
}

?>
