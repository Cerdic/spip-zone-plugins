Ce plugin CIC permet de g�rer le paiement via cet organisme bancaire.

INSTALLATION
============

- editez le fichier config.php et configurez les variables avec les donn�es que vous a fournit votre banque.

La banque vous fournira toutes les valeurs (except�es les url de retour)

Pour utiliser la r�ponse automatique, vous devrez fournir l'url � appeler � votre banque.

Renommez confirm.php en personnalisant le nom du fichier (s�curit�)

Ex : http://www.monsite.com/client/paiement/cmcic/confirm_blabla.php


Information
============

Le retour de paiement n'est pas une information suffisante. V�rifiez toujours sur l'interface de votre banque qu'un paiement est bien pass� en paiement
avant de le consid�rer r�ellement comme "pay�"

V�rifier que les r�pertoires de votre site ne sont pas listable (ex http://www.votresite.com/client/plugins/).
Si tel est le cas veuillez ajouter un fichier htaccess afin de s�curiser le tout.