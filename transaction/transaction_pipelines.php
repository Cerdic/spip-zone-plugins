<?php
      function transaction_insert_head($texte){
          $texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/transaction.css').'" media="all" />'."\n";
          return $texte;
      }
?>
