<?php
require_once('lanceur_spip.php');

class Test_boucle_generique extends SpipTest{
	
	function testBoucleMetaSimple(){
		$this->assertNotEqualCode('','<BOUCLE_meta(META)>#NOM</BOUCLE_meta>');
		$this->assertOkCode('ok<BOUCLE_meta(META)> </BOUCLE_meta>');
		$this->assertOkCode('<BOUCLE_meta(META)> </BOUCLE_meta>ok');
	}
	function testBoucleMetaSimpleRaccourcisFinBoucle() {
		$this->assertOkCode('<BOUCLE_meta(META) />ok');
	}
	function testBoucleMetaSimpleAvantApres() {
		$this->assertOkCode('<B_meta>ok<BOUCLE_meta(META)> </BOUCLE_meta>');
		$this->assertOkCode('<BOUCLE_meta(META)> </BOUCLE_meta>ok</B_meta>');
	}
	function testBoucleMetaSimpleSinon() {
		$this->assertNotOkCode('<BOUCLE_meta(META)> </BOUCLE_meta>ok<//B_meta>');
		$this->assertOkCode('<BOUCLE_meta(META)> </BOUCLE_meta>ok</B_meta>non<//B_meta>');
		
		$this->assertOkCode('<BOUCLE_meta(META)></BOUCLE_meta>ok<//B_meta>');
		$this->assertOkCode('<BOUCLE_meta(META)></BOUCLE_meta>non</B_meta>ok<//B_meta>');
		
		$this->assertOkCode('<BOUCLE_meta(META) />ok<//B_meta>');
		$this->assertOkCode('<BOUCLE_meta(META) />non</B_meta>ok<//B_meta>');
	}	
	function testBoucleMetaSimpleCritere() {
		$this->assertEqualCode($GLOBALS['meta']['nom_site'], '<BOUCLE_meta(META){nom=nom_site}>#VALEUR</BOUCLE_meta>');
		$this->assertEqualCode('', '<BOUCLE_meta(META){nom=gristinapolitainsic}>#VALEUR</BOUCLE_meta>');
		$this->assertOkCode('<BOUCLE_meta(META){nom=gristinapolitainsic}>#VALEUR</BOUCLE_meta>ok<//B_meta>');
	}	
	
}

?>
