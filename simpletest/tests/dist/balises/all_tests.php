<?php
require_once('lanceur_spip.php');

class AllTests_balises extends SpipTestSuite {
    function AllTests_balises() {
        $this->SpipTestSuite('Balises SPIP');
		$this->addDir(__FILE__);
    }
}

?>
