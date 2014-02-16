<?php
require_once('lanceur_spip.php');
include_spip('simpletest/browser');
include_spip('simpletest/web_tester');

class Test_inclure extends SpipTest{


	function testInclureNormal(){
		$this->assertEqualCode('Hello World','<INCLURE{fond=tests/core/inc/inclus_hello_world}>');
		$this->assertEqualCode('Hello World','<INCLURE{fond=tests/core/inc/inclus_hello_world}/>');
	}
	function testInclureDouble(){
		$this->assertEqualCode('Hello WorldHello World','<INCLURE{fond=tests/core/inc/inclus_hello_world}>'
				.'<INCLURE{fond=tests/core/inc/inclus_hello_world}>');
		$this->assertEqualCode('Hello WorldHello World','
				 <INCLURE{fond=tests/core/inc/inclus_hello_world}>'
				.'<INCLURE{fond=tests/core/inc/inclus_hello_world}>');
	}
	function testInclureArray(){
		$array = '#ARRAY{
			0,tests/core/inc/inclus_hello_world,
			1,tests/core/inc/inclus_hello_world,
			2,tests/core/inc/inclus_hello_world}';
		$this->assertEqualCode('Hello WorldHello WorldHello World',"<INCLURE{fond=$array}>");
	}	
	
	
	function testInclureNormalParam(){
		$this->assertEqualCode('Kitty','<INCLURE{fond=tests/core/inc/inclus_param_test}{test=Kitty}>');
		$this->assertEqualCode('Kitty','<INCLURE{fond=tests/core/inc/inclus_param_test}{test=Kitty}/>');
	}
	
	function testInclureArrayParam(){
		$array = '#ARRAY{
			0,tests/core/inc/inclus_param_test,
			1,tests/core/inc/inclus_hello_world,
			2,tests/core/inc/inclus_param_test}';		
		$this->assertEqualCode('KittyHello WorldKitty',"<INCLURE{fond=$array}{test=Kitty}>");
		$this->assertEqualCode('KittyHello WorldKitty',"<INCLURE{fond=$array}{test=Kitty}/>");
	}
}

class Test_inclure_inline extends SpipTest{
	
	function testInclureInlineNormal(){
		$this->assertEqualCode('Hello World','#INCLURE{fond=tests/core/inc/inclus_hello_world}');
		$this->assertEqualCode('Hello World','[(#INCLURE{fond=tests/core/inc/inclus_hello_world})]');
	}
	function testInclureDouble(){
		$this->assertEqualCode('Hello WorldHello World','#INCLURE{fond=tests/core/inc/inclus_hello_world}'
				.'#INCLURE{fond=tests/core/inc/inclus_hello_world}');
		$this->assertEqualCode('Hello WorldHello World','
				 #INCLURE{fond=tests/core/inc/inclus_hello_world}'
				.'#INCLURE{fond=tests/core/inc/inclus_hello_world}');
	}
	function testInclureArray(){
		$array = '#ARRAY{
			0,tests/core/inc/inclus_hello_world,
			1,tests/core/inc/inclus_hello_world,
			2,tests/core/inc/inclus_hello_world}';
		$this->assertEqualCode('Hello WorldHello WorldHello World',"#INCLURE{fond=$array}");
	}	
	
	
	function testInclureNormalParam(){
		$this->assertEqualCode('Kitty','[(#INCLURE{fond=tests/core/inc/inclus_param_test}{test=Kitty})]');
		$this->assertEqualCode('Kitty','[(#INCLURE{fond=tests/core/inc/inclus_param_test}{test=Kitty})]');
	}
	
	function testInclureArrayParam(){
		$array = '#ARRAY{
			0,tests/core/inc/inclus_param_test,
			1,tests/core/inc/inclus_hello_world,
			2,tests/core/inc/inclus_param_test}';		
		$this->assertEqualCode('KittyHello WorldKitty',"[(#INCLURE{fond=$array}{test=Kitty})]");
		$this->assertEqualCode('KittyHello WorldKitty',"[(#INCLURE{fond=$array}{test=Kitty})]");
	}
	
	/**
	 * Un inclure manquant doit creer une erreur de compilation pour SPIP
	 * qui ne doivent pas s'afficher dans le public si visiteur
	 */ 
	function testInclureManquantGenereErreurCompilation(){
		foreach(array(
			'<INCLURE{fond=carabistouille/de/tripoli/absente}/>ok',
			'#CACHE{0}[(#INCLURE{fond=carabistouille/de/montignac/absente}|non)ok]',
		) as $code) {
			$infos = $this->recuperer_infos_code($code);
			$this->assertTrue($infos['erreurs']);
		}
	}
	
	function testInclureManquantNAffichePasErreursDansPublic(){
		foreach(array(
			'<INCLURE{fond=carabistouille/de/tripoli/absente}/>ok',
			'#CACHE{0}[(#INCLURE{fond=carabistouille/de/montignac/absente}|non)ok]',
		) as $code) {
			// non loggue, on ne doit pas voir d'erreur...
			$browser = &new SimpleBrowser();
			$browser->get($f=$this->urlTestCode($code));
			# $this->dump($f);
			$this->assertEqual($browser->getResponseCode(), 200);
			$this->assertOk($browser->getContent());
			
			// loggue admin, on doit voir une erreur ...
			# todo
		}
	}
}


?>
