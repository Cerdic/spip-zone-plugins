$(function(){ 
    $("#onglets .onglet").hide(); 
    $("#onglets .onglet:eq(0)").show(); 
}); 

$(function(){ 
    $("#onglets .onglet").not(":first").hide(); 
}); 

$(function(){ 
    $("#onglets ul a").click(function(){ 
        $("#onglets .onglet").hide(); 
        $(this.hash).show(); 
        this.blur(); 
        return false; 
    }); 
}); 



$(function(){ 
	$("#test").click(
		function(){
			$('#palette').fadeOut("slow");
			});
	}); 

$(function(){ 
	$("input.colorwell").click(
		function(){
			
			$(this).after($('#palette'));
                         $('#palette').fadeIn("fast");

			})

	}); 


$(function(){ 
	$("a.couleur1").click(
		function(){
			$('#couleur1').fadeIn("slow");
			$("a.couleur1").css({ color: "red", background: "blue" });
			$('#couleur2').fadeOut("fast");
			$("a.couleur2").css({ color: "green", background: "none" });
			$('#couleur3').fadeOut("fast");
			$("a.couleur3").css({ color: "green", background: "none" });
			$('#couleur4').fadeOut("fast");
			$("a.couleur4").css({ color: "green", background: "none" });
			$('#couleur5').fadeOut("fast");
			$("a.couleur5").css({ color: "green", background: "none" });
			$('#couleur6').fadeOut("slow");
			$("a.couleur6").css({ color: "green", background: "none" });
			}
			);
	}); 

$(function(){ 
	$("a.couleur2").click(
		function(){
			$('#couleur2').fadeIn("slow");
			$("a.couleur2").css({ color: "red", background: "blue" });
			$('#couleur1').fadeOut("fast");
			$("a.couleur1").css({ color: "green", background: "none" });
			$('#couleur3').fadeOut("fast");
			$('#couleur4').fadeOut("fast");
			$('#couleur5').fadeOut("fast");
			$('#couleur6').fadeOut("slow");
			$("a.couleur6").css({ color: "green", background: "none" });
			}
			);
	}); 


$(function(){ 
	$("a.couleur3").click(
		function(){
			$('#couleur3').fadeIn("slow");
			$("a.couleur3").css({ color: "red", background: "blue" });
			$('#couleur1').fadeOut("fast");
			$("a.couleur1").css({ color: "green", background: "none" });
			$('#couleur2').fadeOut("fast");
			$("a.couleur2").css({ color: "green", background: "none" });
			$('#couleur5').fadeOut("fast");
			$("a.couleur5").css({ color: "green", background: "none" });
			$('#couleur6').fadeOut("slow");
			$("a.couleur6").css({ color: "green", background: "none" });
			}
			);
	}); 

$(function(){ 
	$("a.couleur5").click(
		function(){
			$('#couleur5').fadeIn("slow");
			$("a.couleur5").css({ color: "red", background: "blue" });
			$('#couleur1').fadeOut("fast");
			$("a.couleur1").css({ color: "green", background: "none" });
			$('#couleur2').fadeOut("fast");
			$("a.couleur2").css({ color: "green", background: "none" });
			$('#couleur3').fadeOut("fast");
			$("a.couleur3").css({ color: "green", background: "none" });
			$('#couleur6').fadeOut("slow");
			$("a.couleur6").css({ color: "green", background: "none" });
			}
			);
	}); 

$(function(){ 
	$("a.couleur6").click(
		function(){
			$('#couleur6').fadeIn("slow");
			$("a.couleur6").css({ color: "red", background: "blue" });
			$('#couleur5').fadeOut("fast");
			$("a.couleur5").css({ color: "green", background: "none" });
			$('#couleur1').fadeOut("fast");
			$("a.couleur1").css({ color: "green", background: "none" });
			$('#couleur2').fadeOut("fast");
			$("a.couleur2").css({ color: "green", background: "none" });
			$('#couleur3').fadeOut("fast");
			$("a.couleur3").css({ color: "green", background: "none" });
			}
			);
	}); 

$(function(){ 
	$("a.mots").click(
		function(){
			$('#mots').fadeIn("slow");
			$("a.mots").css({ color: "red", background: "blue" });
			$('#articles').fadeOut("fast");
			$("a.articles").css({ color: "green", background: "none" });
			}
			);
	}); 
$(function(){ 
	$("a.articles").click(
		function(){
			$('#articles').fadeIn("slow");
			$("a.articles").css({ color: "red", background: "blue" });
			$('#mots').fadeOut("fast");
			$("a.mots").css({ color: "green", background: "none" });
			}
			);
	}); 


$(function(){ 
	$("input").click(
		function(){
			
			$(this).after($('#palette'));
                         $('#palette').fadeIn("fast");

			})

	}); 


$(function(){ 
	$(".vue_bouton input").click(
		function(){
			var $this = $(this);
                if( $this.is('.voir') ) {
                        $this.parent().next().fadeIn("slow");
                        $this.removeClass('voir');
                        $this.addClass('cache');
exit();
		 }
                else {
                         $this.parent().next().fadeOut("slow");
                        $this. removeClass('cache');
                        $this.addClass('voir');
 			exit()	
                }
            
		
			}
			);
	}); 


$(function(){ 
	$("#nuage input:submit").click(
		function(){
		nuage=$("#nuage input:text[name=couleur]").val();
		$(".nuage_stat .tag a").css("color",nuage);
		}
	)

}); 

$(function(){ 
	$(".formulaire_config_couleurs_melusine input:submit").click(
		function(){
			bandeau=$("input:text[name=bandeau]").val();
			body=$("input:text[name=pfond]").val();
			bandtex=$("input:text[name=bandtex]").val();
			chemtex=$("input:text[name=chemtex]").val();
			chemin=$("input:text[name=chemin]").val();
			bordchemin=$("input:text[name=bordchemin]").val();
			textg=$("input:text[name=textg]").val();
			
			lienmen=$("input:text[name=lienmen]").val();
			liensur=$("input:text[name=liensur]").val();
			bgmenu=$("input:text[name=bgmenu]").val();
			bgmenusur=$("input:text[name=bgmenusur]").val();
			lien=$("input:text[name=lien]").val();
			liensur0=$("input:text[name=liensur0]").val();

			fondbouton=$("input:text[name=fondbouton]").val();
			fondboutonhover=$("input:text[name=fondboutonhover]").val();
			bordbouton=$("input:text[name=bordbouton]").val();
			textebouton=$("input:text[name=textebouton]").val();
			
			fondtexte=$("input:text[name=fondtexte]").val();
			bord=$("input:text[name=bord]").val();
			texte=$("input:text[name=texte]").val();

			bandeauagenda=$("input:text[name=bandeauagenda]").val();
			textebandeau=$("input:text[name=textebandeau]").val();
			texteagenda=$("input:text[name=texteagenda]").val();
			fondagenda=$("input:text[name=fondagenda]").val();
			bordagenda=$("input:text[name=bordagenda]").val();
			lienagenda=$("input:text[name=lienagenda]").val();
			lienagendasurvol=$("input:text[name=lienagendasurvol]").val();
			


			$(".content").css("background-color",fondtexte);
			$(".content").css("color",texte);
			$(".content").css("border-color",bord);
			$(".logtypeedi").css("background-color",fondtexte);
			$(".trait").css("background-color",fondtexte);
			$(".logtypeedi").css("color",texte);
			$(".logtypeedi").css("border-color",bord);
			$(".content .editodetail").css("color",texte);
			$(".content .editodetail").css("border-color",bord);

			$("a").css("color",lien);
			$(".centre .plan ul a.article").css("color",lien);
			$("a:hover").css("color",liensur0);
			$(".centre .plan ul a.article:hover").css("color",liensur0);
			
			
			$("a.boutongauche").css("color",textebouton);
			$("a.bouton_droite").css("color",textebouton);
			$("#top a.ong1").css("color",textebouton);

			$("a.boutongauche").css("border-color",bordbouton);
			$("a.bouton_droite").css("border-color",bordbouton);
			$("#top a.ong1").css("border-color",bordbouton);

			
			$("a.boutongauche").css("background-color",fondbouton);
			$("a.bouton_droite").css("background-color",fondbouton);
			$("#top a.ong1").css("background-color",fondbouton);

			$("a.boutongauche:hover").css("background-color",fondboutonhover);
			$("a.bouton_droite:hover").css("background-color",fondboutonhover);
			$("#top a.ong1:hover").css("background-color",fondboutonhover);

			$("#menu a").css("color",lienmen);
			$("ul.menulist a").css("color",lienmen); 
			$(".tititre a").css("color",lienmen);

			$("#menu a:hover").css("color",liensur);
			$(".tititre a:hover").css("color",liensur);
			$("ul.menulist a:hover").css("color",liensur);
			$(".on").css("color",liensur);

			$("#menu a").css("background-color",bgmenu);
			$("ul.menulist a").css("background-color",bgmenu); 
			$(".tititre a").css("background-color",bgmenu);
			
			

			$("#menu a:hover").css("background-color",bgmenusur);
			$(".tititre a:hover").css("background-color",bgmenusur);
			$("ul.menulist a:hover").css("background-color",bgmenusur);
			$(".on").css("background-color",bgmenusur);

			
 			$(".breves .en-tete").css("background-color",bandeauagenda); 
			$(".breves .en-tete").css("color",textebandeau); 
$(".breves .en-tete").css("border-color",bordagenda);
			$(".album .en-tete a").css("background-color",bandeauagenda);
			$(".album .en-tete a").css("color",textebandeau);
$(".album .en-tete a").css("border-color",bordagenda);
 			$(".album .en-tete").css("background-color",bandeauagenda);
			$(".album .en-tete").css("color",textebandeau);
$(".album .en-tete").css("border-color",bordagenda);
 			$(".web .en-tete").css("background-color",bandeauagenda);
			$(".web .en-tete").css("color",textebandeau);
$(".web .en-tete").css("border-color",bordagenda);
			$(".web .en-tete-site").css("background-color",bandeauagenda);
			$(".web .en-tete-site").css("color",textebandeau);
$(".web .en-tete-site").css("border-color",bordagenda);					
			$(".galerie .intitule").css("background-color",bandeauagenda);
			$(".galerie .intitule").css("color",textebandeau);
$(".galerie .intitule").css("border-color",bordagenda);
			$(".menleg .en-tete").css("background-color",bandeauagenda);
			$(".menleg .en-tete").css("color",textebandeau);	
$(".menleg .en-tete").css("border-color",bordagenda);			
			$(".breves .en-tete").css("background-color",bandeauagenda);
			$(".breves .en-tete").css("color",textebandeau);
$(".breves .en-tete").css("border-color",bordagenda);
 			$(".forum1 .intitule").css("background-color",bandeauagenda);
			$(".forum1 .intitule").css("color",textebandeau);  
$(".forum1 .intitule").css("border-color",bordagenda); 
			$(".droite .web").css("background-color",bandeauagenda);
			$(".droite .web").css("color",textebandeau);
$(".droite .web").css("border-color",bordagenda);
			

			$(".agenda div").css("background-color",fondagenda);
$(".agenda div").css("color",texteagenda);
$(".agenda div").css("border-color",bordagenda);
$(".agenda ").css("border-color",bordagenda);
			$(".album").css("background-color",fondagenda);
$(".album").css("color",texteagenda);
$(".album").css("border-color",bordagenda);
			$(".menleg").css("background-color",fondagenda);
$(".menleg").css("color",texteagenda);
$(".menleg").css("border-color",bordagenda);
			$(".breves").css("background-color",fondagenda);
$(".breves").css("color",texteagenda);
$(".breves").css("border-color",bordagenda);
			$(".breves div.nom-breve").css("background-color",fondagenda);
$(".breves div.nom-breve").css("color",texteagenda);
$(".breves div.nom-breve").css("border-color",bordagenda);

			$(".calendar_head_mini").css("background-color",fondagenda);
$(".calendar_head_mini").css("color",texteagenda);
$(".calendar_head_mini").css("border-color",bordagenda);
			$(".maj").css("background-color",fondagenda);
$(".maj").css("color",texteagenda);
$(".maj").css("border-color",bordagenda);
			$("#web").css("background-color",fondagenda);
$("#web").css("color",texteagenda);
$("#web").css("border-color",bordagenda);

$(".recherche form").css("border-color",bordagenda);


$(".agenda .en-tete").css("background-color",bandeauagenda);
			$(".agenda .en-tete").css("color",textebandeau);
$(".agenda .en-tete").css("border-color",bordagenda);
$(".calendar_this_month").css("color",texteagenda);



$(".agenda div a").css("color",lienagenda);
$(".menleg a").css("color",lienagenda);
$(".web .site a").css("color",lienagenda);
$(".breves a").css("color",lienagenda);



			

			$(".bandeau").css("background-color",bandeau);
			$(".degrad").css("background-color",bandeau);
			$("body").css("background-color",body);
			$("#conteneur").css("background-color",body);
			$(".tititre").css("background-color",body);				
			$(".tititre .haut").css("background-color",body);	
			$(".banintit").css("color",bandtex);
			$("a.bandeau3").css("color",bandtex);
			$(".liensban a").css("color",bandtex);
			$(".bande-chemin").css("background-color",chemin);
			$(".bande-chemin").css("color",chemtex);
			$(".bande-chemin").css("border-color",bordchemin);
			$(".tititre").css("border-color",bordchemin);
			$("body").css("color",textg);


		})
		
		
		
$("#rechavancee input:submit").click(
		function(){
			
			couleur_rech_bord=$("input:text[name=couleur_bord]").val();
			couleur_rech_fond=$("input:text[name=couleur_fond]").val();
			$(".formulaire_recherche_avancee").css("border-color",couleur_rech_bord);
			$("#bloc_avancee").css("background-color",couleur_rech_fond);
			$(".formulaire_recherche_avancee").css("background-color",couleur_rech_fond);

		}); 

}); 

	