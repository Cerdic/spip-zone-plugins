<?php
session_start();
include_once('../odb_contrib/artichow/BarPlot.class.php');
include_once('../odb_contrib/artichow/LinePlot.class.php');

if(isset($_REQUEST['graphe'])) $graphe=$_REQUEST['graphe'];
else die('Vous devez specifier le nom du graphe (cle du tableau de sessions)');
foreach(array('titre','titreX','titreY','valeurs','labelX','labelY','hauteur','largeur') as $var) {
	if(isset($_SESSION[$graphe][$var])) $$var=$_SESSION[$graphe][$var];
	else $$var='';
}
//echo "<pre>";print_r($_SESSION);die('</pre>');
if($hauteur=='') $hauteur=300;
if($largeur=='') $largeur=400;

if(isset($_SESSION[$graphe]['valeurs']) === FALSE) {
	die('Veuillez passer un tableau nomme valeurs en session');
}

// On vérifie que les données passées en GET sont correctes
if(is_array($valeurs) === FALSE) {
	die('Veuillez passer un tableau nomme valeurs en session');
}


$graph = new Graph($largeur, $hauteur);
$plot = new BarPlot($valeurs);

$plot->setSpace(4, 4, 10, 0);
$plot->setPadding(50, 15, 10, 40);

$plot->title->set($titre);
$plot->title->setFont(new TuffyBold(11));
$plot->title->border->show();
$plot->title->setBackgroundColor(new Color(255, 255, 255, 25));
$plot->title->setPadding(4, 4, 4, 4);
$plot->title->move(-20, 25);

$plot->yAxis->title->set($titreY);
$plot->yAxis->title->setFont(new TuffyBold(10));
$plot->yAxis->title->move(-15, 0);
$plot->yAxis->setTitleAlignment(Label::TOP);

$plot->xAxis->title->set($titreX);
$plot->xAxis->title->setFont(new TuffyBold(10));
$plot->xAxis->setTitleAlignment(Label::RIGHT);

$plot->setBackgroundGradient(
	new LinearGradient(
		new Color(230, 230, 230),
		new Color(255, 255, 255),
		0
	)
);

$plot->barBorder->setColor(new Color(0, 0, 150, 20));

$plot->setBarGradient(
	new LinearGradient(
		new Color(150, 150, 210, 0),
		new Color(230, 230, 255, 30),
		0
	)
);

if(is_array($labelX)) {
	$plot->xAxis->setLabelText($labelX);
	$plot->xAxis->label->setFont(new TuffyBold(7));
}

$graph->shadow->setSize(4);
$graph->shadow->setPosition(Shadow::LEFT_TOP);
$graph->shadow->smooth(TRUE);
$graph->shadow->setColor(new Color(160, 160, 160));

$graph->add($plot);
$graph->draw();
?>
