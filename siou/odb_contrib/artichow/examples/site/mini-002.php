<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once "../../LinePlot.class.php";


$graph = new Graph(150, 100);

$graph->setAntiAliasing(TRUE);

$x = array(
	1, 2, 5, 0.5, 3, 8, 7, 6, 2, -4
);

$plot = new LinePlot($x);
$plot->grid->setNobackground();
$plot->setPadding(20, 8, 8, 20);
$plot->setXAxisZero(FALSE);

// Set a background gradient
$plot->setBackgroundGradient(
	new LinearGradient(
		new Color(210, 210, 210),
		new Color(255, 255, 255),
		0
	)
);

// Set semi-transparent background gradient
$plot->setFillGradient(
	new LinearGradient(
		new Color(230, 150, 150, 20),
		new Color(230, 230, 180, 50),
		90
	)
);

$plot->xAxis->label->hideFirst(TRUE);
$plot->xAxis->label->hideLast(TRUE);
$plot->xAxis->setNumberByTick('minor', 'major', 2);

$graph->add($plot);
$graph->draw();
?>