<?php
require_once('lanceur_spip.php');

	
class Test_balise_generique extends SpipTest{
	
	function testBaliseInexistante(){
		$this->assertEqualCode('','#JENEXISTEPAS');
		$this->assertEqualCode('','[(#JENEXISTEPAS)]');
		$this->assertEqualCode('','[avant(#JENEXISTEPAS)apres]');
		
		// ceux-ci sont plus etonnant mais c'est ce qui se passe effectivement
		$this->assertEqualCode('{rien}','#JENEXISTEPAS{rien}');
		$this->assertEqualCode('{rien}','[(#JENEXISTEPAS{rien})]');
		$this->assertEqualCode('avant{rien}apres','[avant(#JENEXISTEPAS{rien})apres]');
	}
	
	function testBaliseDeclaree(){
		$this->options_recuperer_code(array(
			'fonctions' => '
					function balise_JEXISTE_dist($p){
						$p->code = "ok";
						return $p;
					}',
		));
		$this->assertOkCode('#JEXISTE');
		$this->assertOkCode('[(#JEXISTE)]');
	}
	function testBaliseDeclareeAvantApres(){
		$this->assertEqualCode('avantokapres','[avant(#JEXISTE)apres]');
		$this->assertEqualCode('avant apres','[avant(#JEXISTE|oui)apres]');
		$this->assertEqualCode('','[avant(#JEXISTE|non)apres]');
	}	
	function testBaliseDeclareeEtParams(){	
		$this->assertOkCode('#JEXISTE{param}');
		$this->assertOkCode('#JEXISTE{param,param}');
		$this->assertOkCode('#JEXISTE{#SELF,#SQUELETTE}');
		$this->assertOkCode('#JEXISTE{#VAL{#SELF}}');
		$this->assertOkCode('[(#JEXISTE{[(#VAL{[(#SELF)]})]})]');
	}
	function testBaliseDeclareeEtParamsUtiles(){
		$this->options_recuperer_code(array(
			'fonctions' => '
					function balise_ZEXISTE_dist($p){
						if (!$p1 = interprete_argument_balise(1,$p))
							$p1 = "\'\'";
						$p->code = "affiche_zexiste($p1)";
						return $p;
					}
					function affiche_zexiste($p1){
						return $p1;
					}
					',
		));
		$this->assertEqualCode('','#ZEXISTE');
		$this->assertOkCode('#ZEXISTE{ok}');
		$this->assertEqualCode('avantokapres','[avant(#ZEXISTE{ok})apres]');
		$this->assertEqualCode('avant apres','[avant(#ZEXISTE{ok}|oui)apres]');
		$this->assertEqualCode('','[avant(#ZEXISTE{ok}|non)apres]');
	}
	function testBaliseSurchargee(){
		$this->options_recuperer_code(array(
			'fonctions' => '
					function balise_REXISTE_dist($p){
						$p->code = "oups";
						return $p;
					}
					function balise_REXISTE($p){
						if (!$p1 = interprete_argument_balise(1,$p))
							$p1 = "\'\'";
						$p->code = "affiche_rexiste($p1)";
						return $p;
					}					
					function affiche_rexiste($p1){
						return $p1;
					}
					',
		));
		$this->assertEqualCode('','#REXISTE');
		$this->assertEqualCode('ok','#REXISTE{ok}');
		
		// vider les options
		$this->options_recuperer_code();
	}	
}


?>
