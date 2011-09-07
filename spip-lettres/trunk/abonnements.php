[(#REM)
	Exemple de boucles présentant les abonnements de l'internaute connecté
]
<BOUCLE_auteur(ABONNES){0,10}{objet=auteurs}{id_objet=#SESSION{id_auteur}}>
	Votre n° d'abonné : #ID_ABONNE<br>
	
	<B_abonnements>
	Voici les thèmes des abonnements auxquels vous avez souscrit :
	<ul>
		<BOUCLE_abonnements(ABONNES_RUBRIQUES){id_abonne}>
			<BOUCLE_theme(THEMES){id_rubrique}>
				<li>#TITRE</li>
			</BOUCLE_theme>
		</BOUCLE_abonnements>
	</ul>
	</B_abonnements>
	Vous n'avez souscrit aucun abonnement sur ce site.
	<//B_abonnements>
</BOUCLE_auteur>
	Cette page permet aux inscrits de connaître l'état de leurs abonnements aux lettres de ce site.
	#LOGIN
<//B_auteur>
