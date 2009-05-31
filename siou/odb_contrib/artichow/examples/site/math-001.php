<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once "../../MathPlot.class.php";


$graph = new Graph(300, 300);

$plot = new MathPlot(-3, 3, 3, -3);
$plot->setInterval(0.2);
$plot->setPadding(NULL, NULL, NULL, 20);

$function = new MathFunction('cos');
$function->setColor(new DarkGreen);
$function->mark->setType(Mark::SQUARE);
$function->mark->setSize(3);
$plot->add($function, "f(x) = cos(x)", Legend::MARK);

$function = new MathFunction('exp');
$function->setColor(new DarkRed);
$function->mark->setType(Mark::SQUARE);
$function->mark->setSize(3);
$function->mark->setFill(new DarkBlue);
$plot->add($function, "f(x) = exp(x)", Legend::MARK);

function x2($x) {
	return - $x * $x + 0.5;
}

$function = new MathFunction('x2');
$function->setColor(new DarkBlue);
$plot->add($function, "f(x) = - x * x + 0.5");

$plot->legend->setPosition(0.9, 0.8);
$plot->legend->setPadding(3, 3, 3, 3, 3);

$graph->add($plot);
$graph->draw();
?>