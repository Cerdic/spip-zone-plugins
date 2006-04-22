<?php
$actions=file_get_contents("actions.exemple");
include("actionParser.php");
$parser = new actionParser("aze");
$parser->parse($actions);
echo $parser->getSql();
?>
