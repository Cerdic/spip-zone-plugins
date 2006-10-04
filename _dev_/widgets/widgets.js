
url_widgets_html = 'spip.php?action=widgets_html&class=';

$.setupwidget = function(e){
    var me = this;
    var me_orig = me.innerHTML;
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
         .click(function(e){e.stopPropagation();}) //avoid cancelling on click
         .html(c)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .unclick() //remove the trap to avoid cancel onclick
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
           .find(".cancel_widget")
          	 .click(cancel_widget)	
           .end()
         .end()
         ;
         $("html").keypress(exit_on_esc).click(cancel_widget);
       }
     );
     
    function cancel_widget() {
      $(me).html(me_orig) //restore original html
      .unclick() //remove the trap to avoid cancel onclick
      .click($.setupwidget);
      return false;
    }
    function exit_on_esc(e) {
      if(e.keyCode==27) {
        $("html").unkeypress(exit_on_esc);
        return cancel_widget();
      }
    }
    e.stopPropagation(); //do not cancel widgets when creating another one
  }

$(function() {
  $(".widget").click($.setupwidget);
});

