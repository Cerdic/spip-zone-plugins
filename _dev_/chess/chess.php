<?php
include_once("ChessConfig.php");
include_once("PgnParser.class.php");

if(isset($_GET['pgnFile']) && isset($_GET['getGameList'])){	/* Return game list */
	$pgnObj = new PGNParser($_GET['pgnFile']);
	
	echo $pgnObj->getGameListAsJson();
}	


if(isset($_GET['pgnFile']) && isset($_GET['getGameDetails']) && isset($_GET['gameIndex'])){	/* Return game list */
	$pgnObj = new PGNParser($_GET['pgnFile']);
	echo $pgnObj->getGameDetailsAsJson($_GET['gameIndex'],$_GET['timestamp']);
}

if(isset($_GET['pgnFile']) && isset($_GET['getNumberOfGames'])){
	$pgnObj = new PGNParser($_GET['pgnFile']);
	echo $pgnObj->getNumberOfGames();	
}

?>
