<?php
global $spip_version_affichee;
if (substr($spip_version_affichee,0,3) == '1.9') {
$fond = "inclusions/forum-pagination19";
}
else {
$fond = "inclusions/forum-pagination";
}
$delais = 0;

include ("inc-public.php3");

?>
