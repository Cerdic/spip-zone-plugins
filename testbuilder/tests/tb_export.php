<?php
/**
 * Test unitaire de la fonction tb_export
 * du fichier ../plugins/testbuilder/inc/tb_lib.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-02-27 13:55
 */

	$test = 'tb_export';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/testbuilder/inc/tb_lib.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('tb_export', essais_tb_export());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_tb_export(){
		$essais = array (
  0 => 
  array (
    0 => '\'\'',
    1 => '',
  ),
  1 => 
  array (
    0 => '\'0\'',
    1 => '0',
  ),
  2 => 
  array (
    0 => '\'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->http://www.spip.net] http://www.spip.net\'',
    1 => 'Un texte avec des <a href="http://spip.net">liens</a> [Article 1->art1] [spip->http://www.spip.net] http://www.spip.net',
  ),
  3 => 
  array (
    0 => '\'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;\'',
    1 => 'Un texte avec des entit&eacute;s &amp;&lt;&gt;&quot;',
  ),
  4 => 
  array (
    0 => '\'Un texte sans entites &<>"\\\'\'',
    1 => 'Un texte sans entites &<>"\'',
  ),
  5 => 
  array (
    0 => '\'{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>\'',
    1 => '{{{Des raccourcis}}} {italique} {{gras}} <code>du code</code>',
  ),
  6 => 
  array (
    0 => '\'Un modele <modeleinexistant|lien=[->http://www.spip.net]>\'',
    1 => 'Un modele <modeleinexistant|lien=[->http://www.spip.net]>',
  ),
);
		return $essais;
	}



?>