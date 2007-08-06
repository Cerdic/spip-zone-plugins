			/*** DEBUT Variable de personnalisation ***/
			
			style_rempli = 'case_remplie';
			style_pas_rempli = 'case_pas_remplie';
			
			/*** FIN Variable de personnalisation ***/
			
			date_du_jour = new Date();
			annee = date_du_jour.getFullYear();
			mois = date_du_jour.getMonth()+1;
			
			
			function remplire_agenda(annee,mois){
				
				/*On vide le tout*/
				$("#agendax").html('');
				
				date_jour = annee+"-"+mois+"-01 01:01:01";
				
				/* Initialisation du calendrier*/
				$("#agendax").append('<div class="titre_calendrier"><span class="mois_precedent"><a href="#"><-</a>&nbsp;&nbsp;</span><span class="mois_courant"></span><span class="mois_suivant">&nbsp;&nbsp;<a href="#">-></a></span></div><div class="corps_calendrier"></div><div class="info_evenement"></div>');
				
				
				/* Titre des colones */
				$(".corps_calendrier").append('<div class="titre_colone">Lu</div><div class="titre_colone">Ma</div><div class="titre_colone">Me</div><div class="titre_colone">Je</div><div class="titre_colone">Ve</div><div class="titre_colone">Sa</div><div class="titre_colone">Di</div>');
				
				
				/* On ajoute le "sablier"*/
				$(".mois_courant").append('<img src="/dist/images/searching.gif" />');
				
				
				/* On vas chercher les infos et on les traites */
				$.ajax({
					type: "GET",
					url: "spip.php",
					data: "page=donnees_agendax&date="+date_jour+"&var_mode=recalcul",
					success: function(event){
							
							
							/* Nom et année du mois */
							$(".mois_courant").text($(".nom_mois",event).text());
							
							/* Combien de cases vide au debut et le nombre de jour dans le mois */
							nombre_case_vide = $(".jours_debut",event).text();
							nombre_jours = $(".nombre_jours",event).text();
							
							
							
							/* On insert les case vide du debut */
							for (i=1; i<=nombre_case_vide; i++) {
								$(".corps_calendrier").append('<div class="boite_jour boite_hors_mois"></div>');
							}
							
							
							/* On insert les case des jours du mois */
							for (j=1; j<=nombre_jours; j++) {
								$(".corps_calendrier").append('<div class="boite_jour" id="jour'+j+'">'+j+'</div>');
							}
							
							
							/* On recherche les jours avec des évènements et on met à jour les cases correspondantes */
							for (k=1; k<=31; k++) {
								if ($("#contenu"+k,event).length > 0){
									$("#jour"+k).addClass("case_remplie");
								}else{
									$("#jour"+k).addClass("case_pas_remplie");
								}
							}
							
							
							/* On insert les case vide à la fin */
							nombre_case_restante = eval("42-("+nombre_case_vide+"+"+nombre_jours+")");
							for (l=1; l<=nombre_case_restante; l++) {
								$(".corps_calendrier").append('<div class="boite_jour boite_hors_mois"></div>');
							}
						}
					}
				);
				/* On enleve le "sablier" */
				$(".mois_courant").remove("IMG");
				
			}
			
			$(document).ready(function(){
				
				var date_du_jour = new Date();
				var annee = date_du_jour.getFullYear();
				var mois = date_du_jour.getMonth()+1;
				
				remplire_agenda(annee,mois);
				
				$("div#agendax div.titre_calendrier span.mois_precedent a").click(function(){
					mois = mois - 1;
					if (mois == 0){
						mois = 12;
						annee = annee -1;
					}
					remplire_agenda(annee,mois);
					return false;
					}
				);
				
				$("div#agendax div.titre_calendrier span.mois_suivant a").click(function(){
					mois = mois + 1;
					if (mois == 13){
						mois = 1;
						annee = annee + 1;
					}
					remplire_agenda(annee,mois);
					return false;
					}
				);
			})
