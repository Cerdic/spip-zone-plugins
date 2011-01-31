// Plugin jQuery pour Nyromodal
// auteur BoOz@rezo.net
// ajoute un diaporama aux galeries images nyromodal
// placer le code suivant dans le <head> du squelette SPIP
// <script src="#CHEMIN{js/nyrodiapo.js}" type="text/javascript"></script>

jQuery(function($) {
 
  // customiser la ryno box
  // http://nyromodal.nyrodev.com/
  
  $.fn.nyroModal.settings.processHandler = function(settings) {
    $.nyroModalSettings({
        // demarrer le diapo au chargement de la box
        endShowContent: function(elts, settings) { diapo(); BandoOuiNon = true },
        // bando invisible et diapo stoppé a la fermeture de la box  	
        endRemove: function(elts, settings) { DiapoOuiNon = false; BandoOuiNon = false ; $('#controles').hide('slow'); },
        padding: 10
    });
   	settings.css.content.overflow = "none" ;
   	settings.closeButton = '<a href="#" class="nyroModalClose" id="closeBut" title="fermer">Fermer</a>' ;
   	//console.log(settings);
  	
  	// bando visible au démarage de la box	
  	if(typeof(BandoOuiNon) == 'undefined')
      	BandoOuiNon = true ;
    
  };
  
  // construire le bando boutons controle invisible dans la page
  $('body').append('<div id="controles"><span id="rStop">Stop</span></div>');
  // styler le bando
  $('#controles')
  .css({background:'#fff',position:'fixed',top:'0px',width:'100%',height:'20px',opacity:'0.75',zIndex:'2000',display:'none'});
   
  //affiche le bando 5 secondes si la souris bouge et BandoOuiNon == true 
  $().mousemove(function(e){
      if(typeof(afftime) !== 'undefined')
      	clearTimeout(afftime);	
      afftime = setTimeout("$('#controles').hide('slow');",5000);
      show_boutons();
   }); 
   
  // controler le diaporama avec play / stop
  $('#rStop').css({cursor:'pointer'}).toggle(function(){
    $(this).html("Play");
  	clearTimeout(NextDiapo); 
  },function(){
  	$(this).html("Stop");
  	diapo();
  });
  
});

// appeler la prochaine photo toutes les 8 secondes
function diapo(){
	//console.log(DiapoOuiNon);    
  	if(typeof(DiapoOuiNon) == 'undefined' || BandoOuiNon == false )
      	DiapoOuiNon = true ;
      	
	if(DiapoOuiNon == true){
	// $.nyroModalNext(); pas utilisé car redemarre l'animation
	if(typeof(NextDiapo) !== 'undefined')
     clearTimeout(NextDiapo);	
	NextDiapo = setTimeout("$('.nyroModalNext').trigger('click');",8000);
	//console.log(NextDiapo);
	return false;
	}
	          
}

function show_boutons(){
if(typeof(BandoOuiNon) !== 'undefined')
if(BandoOuiNon == true){
jQuery('#controles').show();
}
}
