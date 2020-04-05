<?php
/**
 * Test unitaire de la fonction liens_chapitres
 * du fichier ../plugins/spip-bible/bibles_options.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-03 18:53
 */

	$test = 'liens_chapitres';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bibles_options.php",'',true);
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('liens_chapitres', essais_liens_chapitres());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_liens_chapitres(){
		$essais = array (
  0 => 
  array (
    0 => '<a href="http://localbeta.dev/spip.php?page=bible&passage=Gn1&traduction=lsg">1</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn2&traduction=lsg">2</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn3&traduction=lsg">3</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn4&traduction=lsg">4</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn5&traduction=lsg">5</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn6&traduction=lsg">6</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn7&traduction=lsg">7</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn8&traduction=lsg">8</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn9&traduction=lsg">9</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn10&traduction=lsg">10</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn11&traduction=lsg">11</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn12&traduction=lsg">12</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn13&traduction=lsg">13</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn14&traduction=lsg">14</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn15&traduction=lsg">15</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn16&traduction=lsg">16</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn17&traduction=lsg">17</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn18&traduction=lsg">18</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn19&traduction=lsg">19</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn20&traduction=lsg">20</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn21&traduction=lsg">21</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn22&traduction=lsg">22</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn23&traduction=lsg">23</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn24&traduction=lsg">24</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn25&traduction=lsg">25</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn26&traduction=lsg">26</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn27&traduction=lsg">27</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn28&traduction=lsg">28</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn29&traduction=lsg">29</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn30&traduction=lsg">30</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn31&traduction=lsg">31</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn32&traduction=lsg">32</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn33&traduction=lsg">33</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn34&traduction=lsg">34</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn35&traduction=lsg">35</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn36&traduction=lsg">36</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn37&traduction=lsg">37</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn38&traduction=lsg">38</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn39&traduction=lsg">39</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn40&traduction=lsg">40</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn41&traduction=lsg">41</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn42&traduction=lsg">42</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn43&traduction=lsg">43</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn44&traduction=lsg">44</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn45&traduction=lsg">45</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn46&traduction=lsg">46</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn47&traduction=lsg">47</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn48&traduction=lsg">48</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn49&traduction=lsg">49</a> | <a href="http://localbeta.dev/spip.php?page=bible&passage=Gn50&traduction=lsg">50</a>',
    1 => 'Gn',
    2 => 'standard',
    3 => 'lsg',
  ),
);
		return $essais;
	}




?>