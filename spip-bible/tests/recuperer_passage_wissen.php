<?php
/**
 * Test unitaire de la fonction recuperer_passage_wissen
 * du fichier ../plugins/spip-bible/traduction/wissen.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 22:35
 */

	$test = 'recuperer_passage_wissen';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/traduction/wissen.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('recuperer_passage_wissen', essais_recuperer_passage_wissen());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_recuperer_passage_wissen(){
		$essais = array (
  1 => 
  array (
    0 => '
<br /><strong>1</strong><sup>1 </sup>Am Anfang schuf Gott Himmel und Erde.


<br /><sup>2 </sup>Die Erde war noch leer und öde,
Dunkel bedeckte sie und wogendes Wasser,
und über den Fluten schwebte Gottes Geist.

<br /><sup>3 </sup>Da sprach Gott: »Licht entstehe!«,
und das Licht strahlte auf.
<br /><sup>4 </sup>Und Gott sah das Licht an: Es war gut.
Dann trennte Gott das Licht von der Dunkelheit
<br /><sup>5 </sup>und nannte das Licht Tag,
die Dunkelheit Nacht.
Es wurde Abend und wieder Morgen:
der erste Tag.

<br /><sup>6 </sup>Dann sprach Gott:
»Im Wasser soll ein Gewölbe entstehen,
eine Scheidewand zwischen den Wassermassen!«

<br /><sup>7 </sup>So geschah es: Gott machte ein Gewölbe
und trennte so das Wasser unter dem Gewölbe
von dem Wasser, das darüber war.
<br /><sup>8 </sup>Und Gott nannte das Gewölbe Himmel.
Es wurde Abend und wieder Morgen:
der zweite Tag.

<br /><sup>9 </sup>Dann sprach Gott:
»Das Wasser unter dem Himmelsgewölbe
soll sich alles an einer Stelle sammeln,
damit das Land hervortritt.«
So geschah es.
<br /><sup>10 </sup>Und Gott nannte das Land Erde,
die Sammlung des Wassers nannte er Meer.
Und Gott sah das alles an: Es war gut.

<br /><sup>11 </sup>Dann sprach Gott:
»Die Erde lasse frisches Grün aufsprießen,
Pflanzen und Bäume von jeder Art,
die Samen und samenhaltige Früchte tragen!«
So geschah es:
<br /><sup>12 </sup>Die Erde brachte frisches Grün hervor,
Pflanzen jeder Art mit ihren Samen
und alle Arten von Bäumen
mit samenhaltigen Früchten.
Und Gott sah das alles an: Es war gut.
<br /><sup>13 </sup>Es wurde Abend und wieder Morgen:
der dritte Tag.

<br /><sup>14 </sup>Dann sprach Gott:
»Am Himmel sollen Lichter entstehen,
die Tag und Nacht voneinander scheiden,
leuchtende Zeichen,
um die Zeiten zu bestimmen:
Tage und Feste und Jahre.
<br /><sup>15 </sup>Sie sollen am Himmelsgewölbe leuchten,
damit sie der Erde Licht geben.«
So geschah es:
<br /><sup>16 </sup>Gott machte zwei große Lichter,
ein größeres, das den Tag beherrscht,
und ein kleineres für die Nacht,
dazu auch das ganze Heer der Sterne.

<br /><sup>17 </sup>Gott setzte sie an das Himmelsgewölbe,
damit sie der Erde Licht geben,
<br /><sup>18 </sup>den Tag und die Nacht regieren
und Licht und Dunkelheit voneinander scheiden.
Und Gott sah das alles an: Es war gut.
<br /><sup>19 </sup>Es wurde Abend und wieder Morgen:
der vierte Tag.

<br /><sup>20 </sup>Dann sprach Gott:
»Das Wasser soll von Leben wimmeln,
und in der Luft sollen Vögel fliegen!«

<br /><sup>21 </sup>So schuf Gott die Seeungeheuer
und alle Arten von Wassertieren,
ebenso jede Art von Vögeln
und geflügelten Tieren.
Und Gott sah das alles an: Es war gut.
<br /><sup>22 </sup>Und Gott segnete seine Geschöpfe und sagte:
»Seid fruchtbar, vermehrt euch
und füllt die Meere,
und ihr Vögel, vermehrt euch auf der Erde!«
<br /><sup>23 </sup>Es wurde Abend und wieder Morgen:
der fünfte Tag.

<br /><sup>24 </sup>Dann sprach Gott:
»Die Erde soll Leben hervorbringen:
alle Arten von Vieh und wilden Tieren
und alles, was auf der Erde kriecht.«
So geschah es.
<br /><sup>25 </sup>Gott machte die wilden Tiere und das Vieh
und alles, was auf dem Boden kriecht,
alle die verschiedenen Arten.
Und Gott sah das alles an: Es war gut.

<br /><sup>26 </sup>Dann sprach Gott:
»Nun wollen wir Menschen machen,
ein Abbild von uns, das uns ähnlich ist!
Sie sollen Macht haben über die Fische im Meer,
über die Vögel in der Luft,
über das Vieh und alle Tiere auf der Erde 
und über alles, was auf dem Boden kriecht.«

<br /><sup>27 </sup>So schuf Gott die Menschen nach seinem Bild,
als Gottes Ebenbild schuf er sie
und schuf sie als Mann und als Frau. 

<br /><sup>28 </sup>Und Gott segnete die Menschen
und sagte zu ihnen:
»Seid fruchtbar und vermehrt euch!
Füllt die ganze Erde und nehmt sie in Besitz!
Ich setze euch über die Fische im Meer,
die Vögel in der Luft
und alle Tiere, die auf der Erde leben,
und vertraue sie eurer Fürsorge an.«


<br /><sup>29 </sup>Weiter sagte Gott zu den Menschen:
»Als Nahrung gebe ich euch die Samen der Pflanzen
und die Früchte, die an den Bäumen wachsen,
überall auf der ganzen Erde.
<br /><sup>30 </sup>Den Landtieren aber und den Vögeln
und allem, was auf dem Boden kriecht,
allen Geschöpfen, die den Lebenshauch in sich tragen,
weise ich Gräser und Blätter zur Nahrung zu.«
So geschah es.

<br /><sup>31 </sup>Und Gott sah alles an, was er geschaffen hatte,
und sah: Es war alles sehr gut.
Es wurde Abend und wieder Morgen:
der sechste Tag.

<br /><strong>2</strong><sup>1 </sup>So entstanden Himmel und Erde mit allem, was lebt.
				
			
			
			',
    1 => 'Gen',
    2 => 'Gen1,1-2,1',
    3 => 'gute-nachricht-bibel',
    4 => 'de',
  ),
);
		return $essais;
	}




?>