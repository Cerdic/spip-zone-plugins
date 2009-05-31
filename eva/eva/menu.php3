<div id="Menu">
<div class="sommaire">

<ul>
	<li class="on"><img src="../images/logo_eva.gif" alt="Logo : EVA" /> <strong>EVA-Web 2.3</strong></li>


	<!-- Accueil -->
	<li class="off"><img src="../images/puceoff.gif" alt="-" /> <a href="/" class="off">Accueil</a></li>



	<!-- Pr&eacute;sentation -->
	<li class="<?php echo ($sect == "0" ? 'on' : 'off'); ?>"><img src="../images/puceoff.gif" alt="-" /> <a href="?presentation=presentation" class="<?php echo ($sect == "0" ? 'on' : 'off'); ?>">Pr&eacute;sentation</a></li>



	<!-- Aide aux r&eacute;dacteurs -->
	<li class="<?php echo ($sect == "aide" ? 'on' : 'off'); ?>"><img src="../images/deplierbas.gif" alt="-" /> <a href="?aide=aide" class="<?php echo ($aide == "aide" ? 'on' : 'off'); ?>">Aide aux r&eacute;dacteurs</a>

		<ul>
			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=publier" class="<?php echo ($aide == "publier" ? 'on' : 'off'); ?>">Publier avec EVA</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=article" class="<?php echo ($aide == "article" ? 'on' : 'off'); ?>">Ecrire ou modifier un article</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=raccourcis" class="<?php echo ($aide == "raccourcis" ? 'on' : 'off'); ?>">Les raccourcis typographiques</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=image" class="<?php
			if($aide == "image"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Placer une image dans un article</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=document" class="<?php
			if($aide == "document"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Joindre un document &agrave; un article</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=site" class="<?php
			if($aide == "site"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">R&eacute;f&eacute;rencer un site</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=rubrique" class="<?php
			if($aide == "rubrique"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Cr&eacute;er une (sous-)rubrique</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=album" class="<?php
			if($aide == "album"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Cr&eacute;er un album photo</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=diaporama" class="<?php
			if($aide == "diaporama"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Cr&eacute;er un diaporama</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?aide=agenda" class="<?php
			if($aide == "agenda"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Cr&eacute;er un agenda</a></li>

		</ul>

	</li>


	<!-- Personnalisation d'EVA -->
	<li class="<?php
	if ($sect == "perso") {
		echo "on" ;
	} else {
		echo "off" ;
	}
	?>"><img src="../images/deplierbas.gif" alt="-" /> <a href="?perso=personnalisation" class="<?php
	if($perso == "personnalisation"){
		echo "on" ;
	} else {
		echo "off" ;
	}
	?>">Personnalisation d'EVA</a>

		<ul>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?perso=infosite" class="<?php
			if($perso == "infosite"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Titre, logo, URL du site</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?perso=editorial" class="<?php
			if($perso == "editorial"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Editorial du site</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?perso=boutons" class="<?php
			if($perso == "boutons"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Boutons du menu</a></li>

			<li><img src="../images/puceoff.gif" alt="-" /> <a href="?perso=connexion" class="<?php
			if($perso == "connexion"){
				echo "on" ;
			} else {
				echo "off" ;
			}
			?>">Fen&ecirc;tre de connexion</a></li>

		</ul>

	</li>
</div>
</div>
