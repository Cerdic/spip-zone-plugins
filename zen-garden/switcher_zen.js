$(document).ready(function() {
    $(".formulaire_switcher_zen input").css("display","none");
    $(".formulaire_switcher_zen select").change(function(){
        $("body").css("color","blue");
        this.ajaxSubmit();
        }
        )
});
