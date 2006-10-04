// TODO: passer la valeur a afficher pour pouvoir faire CANCEL
// en cliquant sur un bouton, ou hors de la div, ou en tapant ESC

url_widgets_html = 'spip.php?action=widgets_html&class=';

$.setupwidget = function(){
    var me = this;

    // reglages de taille mini/maxi; pas tres beau
    var w,h;
    w = $(me).width()-50; // 50 = largeur du bouton "ok"
    if (w<100) w=100;
    if (w>700) w=700;
    h = $(me).height();
    if (h<12) h=12;

    // charger le formulaire
    $.get(url_widgets_html+encodeURIComponent(this.className),
       function (c) {
         $(me)
         .unclick()
         .html(c)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .click($.setupwidget); // recursif
           })
           .find(".widget-active")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({"width":w+'px',"height":h+'px'})
             .css({
               'fontSize': $(me).css('fontSize'),
               'fontFamily': $(me).css('fontFamily')
             })
             .each(function(){this.focus();})
           .end()
         .end()
         ;
       }
     );
  }

$(function() {
  $(".widget").click($.setupwidget);
});

