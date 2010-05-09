<?php
echo "testing cleaning the br and p \n";
$html = <<<EOT
<p class="spip">OFFRANDES et SADHOUS</p>
<p>
<p class="spip"><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;Des centaines de sadhous (saints hommes) y viennent pour participer à la fête, ce sont souvent des "renonçants",

<br><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;qui ont quitté tous les bien matériels de leur vie et vont de pèlerinage en pèlerinage pour recevoir les bienfaits des Dieux, 
<br><br/><br /><img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" style="height: 11px; width: 8px;" class="" height="11" width="8">&nbsp;souvent accompagnés d’un disciple.</p>

Une image sur deux lignes : 

<img src="local/cache-vignettes/L8xH11/puce-68c92.gif" alt="-" 
 style="height: 11px; width: 8px;" class="" height="11" width="8" />

EOT;

$pattern = array('/<img[^>]*>/i');
$replacement = array("");
echo preg_replace($pattern,$replacement,$html);	

?>