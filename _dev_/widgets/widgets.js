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
         .width(w) // no luck!
         .height(h)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .click($.setupwidget); // recursif
           })
           .find("input[@type='text']")
             .css('backgroundColor', 'yellow')
             .css({'font':'inherit'})
             .each(function(){this.focus();}) // complique...
           .end()
         .end()
         ;
       }
     );
  }

$(function() {
  $(".widget")
  .click($.setupwidget)
  .hover(
    function() {$(this).css('backgroundColor', 'yellow')},
    function() {$(this).css('backgroundColor', '')}
  )
  ;
});

