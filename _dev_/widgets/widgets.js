// TODO: passer la valeur a afficher pour pouvoir faire CANCEL
// en cliquant sur un bouton, ou hors de la div, ou en tapant ESC

url_widgets_html = 'spip.php?action=widgets_html&class=';

$.setupwidget = function(){
    var me = this;

    // Ce bloc ne fonctionne pas :(
    var w,h;
    w = $(me).width();
    h = $(me).height();

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
           .find("textarea")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({"width":"100%","height":"100%"}) // no luck!
         		 .each(function(){
               this.focus();
               $(this).css({
                 'fontSize': $(me).css('fontSize'),
                 'fontFamily': $(me).css('fontFamily')
               });
             })
           .end()
         .end()
         ;
       }
     );
  }

$(function() {
  $(".widget").click($.setupwidget);
});

