<?php
      function transaction_insert_head($texte){
          $texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('habillage/transaction.css').'" media="all" />'."\n";
          return $texte;
      }
?>