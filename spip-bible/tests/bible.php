<?php
/**
 * Test unitaire de la fonction bible
 * du fichier ../plugins/spip-bible/bible_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-02-27 23:50
 */

	$test = 'bible';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('bible', essais_bible());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_bible(){
		$essais = array (
  0 => 
  array (
    0 => '<quote>
Or la terre était vide et vague, les ténèbres couvraient l\'abîme, un vent de Dieu tournoyait sur les eaux.

<accronym title=\'Gen&egrave;se\'>Gn</accronym> 1,2 (<i>Bible de J&eacute;rusalem (1973)</i>)

</quote>',
    1 => 'Gn1,2',
    2 => 'jerusalem',
    3 => 'non',
    4 => 'non',
    5 => 'oui',
  ),
  1 => 
  array (
    0 => '<quote>
 Vision d\'Abdias. Voici ce que dit le Seigneur DIEU à Edom&nbsp;: Nous avons appris une nouvelle
 de la part du SEIGNEUR, 
 et un émissaire a été envoyé parmi les nations&nbsp;: 
 Levez-vous&nbsp;! 
 Levons-nous contre elle&nbsp;! 
 Au combat&nbsp;!Je te rends petit parmi les nations, te voilà l\'objet du plus grand mépris. L\'arrogance de ton cœur t\'a trompé, toi qui demeures dans les creux des rochers, 
 toi qui habites la hauteur
 et qui te dis&nbsp;: 
 Qui me fera descendre à terre&nbsp;?Quand tu prendrais de la hauteur, tel un aigle, quand ton nid serait placé parmi les étoiles, 
 je t\'en ferais descendre
 —&nbsp;déclaration du SEIGNEUR. Si des voleurs, des pillards nocturnes, viennent chez toi, comment restes-tu tranquille&nbsp;? 
 Ne voleront-ils pas ce qui leur est nécessaire&nbsp;? 
 Et si des vendangeurs viennent chez toi, 
 laisseront-ils autre chose que du grappillage&nbsp;?Comment&nbsp;! Esaü est mis à nu&nbsp;! Ses cachettes sont éventrées&nbsp;!On te chasse de ton territoire. Tes alliés te trompent, tes amis te possèdent&nbsp;; 
 ils se servent de ton pain comme d\'un piège pour toi, par-dessous.
 — «&nbsp;Il n\'y a plus d\'intelligence en lui&nbsp;!&nbsp;»
 N\'est-ce pas en ce jour-là —&nbsp;déclaration du SEIGNEUR&nbsp;—
 que je ferai disparaître d\'Edom les sages, 
 et de la région montagneuse d\'Esaü l\'intelligence&nbsp;?Tes guerriers seront terrifiés, Témân, pour que tout homme soit retranché de la région montagneuse d\'Esaü
 par la tuerie&nbsp;! </quote>',
    1 => 'Ab1-9',
    2 => 'NBS',
  ),
);
		return $essais;
	}





?>