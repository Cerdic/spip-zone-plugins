/*
 *  widgets.js (c) Fil 2006 -- licence GPL
 */

// ouvre un widget
$.fn.openwidget = function() {
  return this
  .each(function(){
    // verifier que je suis un widget
    if (!$(this).is('.widget'))
      return;

    // voir si je dispose deja du widget comme voisin
    if ($(this).is('.has-widget')) {
      $(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      $(this)
      .append(SEARCHING); // icone d'attente
      var me=this;
      $.get(url_widgets_html
        + '&w=' + $(this).width()
        + '&h=' + $(this).height()
        + '&em=' + $(this).css('fontSize')
        + '&class=' + encodeURIComponent(me.className)
       ,
        function (c) {
          $(me)
          .find("img.widget-searching")
            .remove()
          .end()
          .hide()
          .addClass('has-widget')
          .next()
            .html(c)
            .show() // animate
            .activatewidget();
        }
      );
    }
  });
}

// annule le widget ouvert
$.fn.cancelwidget = function() {
  return this
  .filter('.has-widget')
  .show()
  .removeClass('has-widget')
  .next()
    .html('')
    .hide();
}

// masque le widget ouvert
$.fn.hidewidget = function() {
  return this
  .filter('.has-widget')
  .show()
  .next()
    .hide()
    .removeClass('widget-hover');
}

// active un widget qui vient d'etre charge
$.fn.activatewidget = function() {
  return this
  .each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        $(me)
        .prev()
          .html(d.responseText)
          .prepend(ICONCLICK)
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
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .cancelwidget();
        })
      .end()
      .find(".hide_widget")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .hidewidget();
        })
      .end()
    .end();
  });
}

// initialise les widgets (cree le clone actif)
$.fn.initwidget = function(){
  this
  .each(function(){
    $(this)
    .after(
      this.cloneNode(true)
    );
  })
  .next()
    .hide()
    .html('')
    .removeAttr('id') // necessaire ??
    .removeClass('widget')
    .click(function(e){
      e.stopPropagation();
    })
  .prev()
  .prepend(ICONCLICK)
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    e.stopPropagation();
    $(this).openwidget();
  });

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        $(this).addClass('widget-hover');
      },function(){
        $(this).removeClass('widget-hover');
      }
    );
  }

  return this;
}

// demarrage
$(document).ready(function() {

  // Aller chercher les droits a partir de la liste des class
  var vus = '';
  $(".widget")
  .each(function(){
    vus += '&'+this.className
  });

  // Quand on recupere la liste des droits, on active les widgets autorises
  url_widgets_droits = 'spip.php?action=widgets_droits';
  if (vus)
  $.post(url_widgets_droits, {'vus': vus},
    function(c) {
      c = c.split('|');
      if (!c.length) return;

      url_widgets_html = 'spip.php?action=widgets_html';
      SEARCHING = '<img class="widget-searching" src="dist/images/searching.gif" style="float:right;" />';
      ICONCLICK = "<img class='widget-edit' onclick='event.stopPropagation();$(this).parent().openwidget();' style='float:right;border:0' src='dist/images/edit.gif' title='&Eacute;diter' />";

      for (var i=0; i<c.length; i++) {
        $(".widget."+c[i])
        .initwidget();
      }

      // fermer tous les widgets ouverts
      $("html")
      .click(function() {
        $(".widget.has-widget:hidden")
        .hidewidget();
      });
      $('head')
      .prepend('<style>img.widget-edit {visibility: hidden;} .widget:hover img.widget-edit, .widget-hover img.widget-edit {visibility: visible;} .widget:hover, .widget-hover {background-color: #e3eeee;}</style>');
    }
  );
});
