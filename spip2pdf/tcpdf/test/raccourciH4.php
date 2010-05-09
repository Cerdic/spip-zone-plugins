<?php
echo "testing the creation of the h4 shortcut";
$html = <<<EOT
some content before 

with 

plenty line 

break

and now our {2{Catégorie A}2} with a new {2{fucking shortcut !!}2} 

EOT;


$html = preg_replace('/\{2\{(.*?)\}2\}/','<h2>$1</h2>',$html);
echo $html;

?>