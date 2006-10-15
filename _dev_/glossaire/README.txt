Bonjour,

ce plug-in permet de faire des liens automatiques entre la première occurence d'un mot et un glossaire.
Pour ce faire,
-Vous devez créer une rubrique "glosssaire"
-Vous remplissez cette rubrique par des articles dont le titre est un des mots que vous souhaitez voir expliqués
-Vous éditez le fichier glossaire.php présent dans le répertoire plugin et vous y indiquez le numéro de votre rubrique "glossaire"
-Dans vos squelettes placez le filtre : "lier_glossaire" sur les champs que vous souhaitez expliquer (en général #TEXTE). Ce qui donne un résultat de la forme [(#TEXTE|lier_glossaire)]
-Recalculez vos squelettes : et regardez.