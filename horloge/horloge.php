<?php
header('Content-Type: text/xml');
$h24 = date("H:i");
$h12 = date("g:i a");
$enlong = date("H:i (g:ia) l, j F");
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<curTime>' . "\n";
echo "\t" . '<h24>' . $h24 . '</h24>' . "\n";
echo "\t" . '<h12>' . $h12 . '</h12>' . "\n";
echo "\t" . '<enlong>' . $enlong . '</enlong>' . "\n";
echo '</curTime>' . "\n";
?>
