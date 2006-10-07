
url_widgets_html = 'spip.php?action=widgets_html';
url_widgets_droits = 'spip.php?action=widgets_droits';
SEARCHING = '<img class="widget-searching" src="dist/images/searching.gif" style="float:right;" />';

$.hidewidgets = function() {
  $(".widget").each(function(){
    $(this)
    .hidewidget();
  });
}

$.initallwidgets = function(e) {
  $('.widget').each(function(){
    $.initwidget(this);}
  );
  e.stopPropagation();
}

$.initwidget = function(me) {
  // voir si je suis en mode "widget"
  if (!$(me).is('.widget'))
    return;

  // voir si je dispose deja du widget
  if ($(me).is('.has-widget')) {
    $(me)
    .hide()
    .next()
      .show();
  }
  // sinon charger le formulaire
  else {
    $(me)
    .append(SEARCHING); // icone d'attente
    $.get(url_widgets_html
      + '&w=' + $(me).width()
      + '&h=' + $(me).height()
      + '&em=' + $(me).css('fontSize')
      + '&class=' + encodeURIComponent(me.className)
     ,
      function (c) {
        $(me)
        .find("img.widget-searching").remove().end();
        $(me)
        .hide()
        .addClass('has-widget')
        .next()
          .html(c)
          .show() // animate
          .activatewidget();
      }
    );
  }
}

$.clickwidget = function(e){
  e.stopPropagation(); // avoid sending a global click to the body onclick
  $.initwidget(this);
}

// annule le widget ouvert
$.fn.cancelwidget = function() {
  this.each(function(){
    $(this)
    .filter('.has-widget')
    .show()
    .removeClass('has-widget')
    .next()
      .html('')
      .hide();
  });
  return this;
}
// masque le widget ouvert
$.fn.hidewidget = function() {
  this.each(function(){
    $(this)
    .filter('.has-widget')
    .show()
    .next()
      .hide();
  });
  return this;
}


$.fn.activatewidget = function() {
  this.each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        $(me)
        .prev()
          .html(d.responseText)
          .show()
          .removeClass('has-widget')
        .next()
        .hide()
        .html('');
      }).onesubmit(function(){
        $("form", me)
        .append(SEARCHING); // icone d'attente
      })
      .find(".widget-active")
        .css('font', 'inherit') // pour safari
        .css({
            'fontSize': $(me).css('fontSize'),
            'fontFamily': $(me).css('fontFamily')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            $(me)
            .prev()
            .cancelwidget();
          }
        })
      .end()
      .find(".cancel_widget")
        .click(function(){
          $(me)
          .prev()
            .cancelwidget();
          return false;
        })
      .end()
      .find(".hide_widget")
        .click(function(){
          $(me)
          .prev()
            .hidewidget();
          return false;
        })
      .end()
    .end();
  });
  return this;
}

$(function() {

  $('head')
  .prepend('<style>.widget-hover { background-image: url("dist/images/edit.gif"); background-repeat:no-repeat; background-position:right top; background-color: #e3eeee;}</style>');

  // Aller chercher les droits a partir de la liste des classes
  var vus = '';
  $(".widget")
  .each(function(){
    vus += '&'+this.className
  });

  // Quand on recupere la liste des droits, on active les widgets autorises
  if (vus)
  $.post(url_widgets_droits, {'vus': vus},
    function(c) {
      c = c.split('|');
      for (var i=0; i<c.length; i++) {
        $(".widget."+c[i])
        .each(function(){
          $(this)
          .after(this.cloneNode(true))
          .next()
            .hide()
            .html('')
            .removeAttr('id') // necessaire ??
            .removeClass('widget')
            .removeClass(c[i])
            .click($.clickwidget); // eviter qu'un clic n'annule le widget
        })
        .hover( // obligatoire pour MSIE
          function(){$(this).addClass('widget-hover');},
          function(){$(this).removeClass('widget-hover');}
        )
        .attr('title', 'Double-clic pour modifier')  // pas terrible ;-)
        .dblclick($.clickwidget);
        $("html")
        .click($.hidewidgets);
      }
    }
  );
});

