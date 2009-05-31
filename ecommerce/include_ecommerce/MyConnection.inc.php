<?php
//
// !!! Don't use this sample but your actual connection include
// (php-include dir).
//
// !!! Ne pas utiliser cet exemple mais votre include de connexion habituel
// (php-include dir).
//

echo " MyDebug-[About to connect]- ";
@$MyConnection = mysql_connect("localhost", "ecommerce","ZwIRBlHGNUs8BIkpqcmj")
  or die ('Cannot connect MyServer! Unreachable server? Serveur inaccessible ?');
echo " MyDebug-[Connected]- ";

@$isMyDataBaseSelected = mysql_select_db("CMCIC",$MyConnection)
  or die ('Cannot select MyBase! Wrong base name? Nom de base erroné ?');
echo " MyDebug-[Selected]- ";

?>