<?php
$log_file = "./log/".str_replace("%","_",urlencode($Order_Reference))."_1.log";
$fp = @fopen($log_file,"w");  
if (is_writable($log_file))
{
  fwrite($fp,"\n -- sauvegarder      : \n ".$CMCIC_exec_S." -- ");
  fwrite($fp,"\n -- sauvegarde ok    : \n ".$CMCIC_done_S." -- ");
  fwrite($fp,"\n -- prendreDonnees 1 : \n ".$CMCIC_exec_1." -- ");
  fwrite($fp,"\n -- prendreDonnees   : \n ".$CMCIC_updt_D." -- ");
  fwrite($fp,"\n -- verifierMAC 2    : \n ".$CMCIC_exec_2." -- ");
  fwrite($fp,"\n -- verifierMAC 3    : \n ".$CMCIC_exec_3." -- ");
  fwrite($fp,"\n -- verifierMAC      : \n ".$CMCIC_updt_M." -- ");
  fwrite($fp,"\n -- creerReponse 4   : \n ".$CMCIC_exec_4." -- ");
  fwrite($fp,"\n -- creerReponse     : \n ".$CMCIC_updt_G." -- ");
  // reading this logs, executing step by step, and browsing CMCIC tables
  // will answer most of your questions.
}
else
  echo "<br><br> Unable to write Log files ! <br><br>";
?>