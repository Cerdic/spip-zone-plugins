Ce plugin CIC permet de gérer le paiement via cet organisme bancaire.

INSTALLATION
============

- editez le fichier config.php et configurez les variables avec les données que vous a fournit votre banque.

La banque vous fournira toutes les valeurs (exceptées les url de retour)

Pour utiliser la réponse automatique, vous devrez fournir l'url à appeler à votre banque.

Renommez confirm.php en personnalisant le nom du fichier (sécurité)

Ex : http://www.monsite.com/client/paiement/cmcic/confirm_blabla.php


Information
============

Le retour de paiement n'est pas une information suffisante. Vérifiez toujours sur l'interface de votre banque qu'un paiement est bien passé en paiement
avant de le considérer réellement comme "payé"

Vérifier que les répertoires de votre site ne sont pas listable (ex http://www.votresite.com/client/plugins/).
Si tel est le cas veuillez ajouter un fichier htaccess afin de sécuriser le tout.