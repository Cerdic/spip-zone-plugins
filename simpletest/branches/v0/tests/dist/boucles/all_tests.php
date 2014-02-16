<?php
require_once('lanceur_spip.php');

class AllTests_dist_boucles extends SpipTestSuite {
    function AllTests_dist_boucles() {
        $this->SpipTestSuite('Boucles SPIP');
		$this->addDir(__FILE__);
    }
}

?>
