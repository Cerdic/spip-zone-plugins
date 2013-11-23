<!DOCTYPE html>
<html>
  <head>
    <title>skel.php</title>
  </head>
  <body>
    <p>
      Ceci est un squelette avec une variable : var = <?php echo $var; ?>
    </p>
    <p>… et même une boucle</p>
    <B_objets>
      <ul>
        <BOUCLE_objets(<?php echo strtoupper($objet . 's'); ?>)>
          <li>#TITRE</li>
        </BOUCLE_objets>
      </ul>
    </B_objets>
  </body>
</html>
