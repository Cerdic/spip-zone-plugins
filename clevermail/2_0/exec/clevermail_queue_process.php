<?php
function exec_clevermail_queue_process() {
  include_spip('genie/clevermail_queue_process');
  // On force l'envoi en affichant une trace
  genie_clevermail_queue_process_dist('yes');
}
?>