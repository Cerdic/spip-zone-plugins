<?php
echo "testing cleaning the puce \n";
$html = <<<EOT
<p class="spip">OFFRANDES et SADHOUS</p>

<p class="spip"><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;Des centaines de sadhous (saints hommes) y viennent pour participer à la fête, ce sont souvent des "renonçants",

<br><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;qui ont quitté tous les bien matériels de leur vie et vont de pèlerinage en pèlerinage pour recevoir les bienfaits des Dieux, 
<br><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;souvent accompagnés d’un disciple.</p>
EOT;

$pattern = '/<[^>]+src="[^>]+puce[^>]+"[^>]+>/';
$replacement = "<li>";
echo preg_replace($pattern,$replacement,$html);	

?>