// TODO: passer la valeur a afficher pour pouvoir faire CANCEL
// en cliquant sur un bouton, ou hors de la div, ou en tapant ESC

url_widgets_html = 'spip.php?action=widgets_html&class=';

$.setupwidget = function(){
    var me = this;

    // Ce bloc ne fonctionne pas :(
    var w,h;
    w = $(me).width()+'px';
    h = $(me).height()+'px';
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
           .find("textarea,input[@type='text']")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({"width":w,"height":h})
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

