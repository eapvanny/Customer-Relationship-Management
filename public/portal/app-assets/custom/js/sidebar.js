$(document).ready(function(){
    var current = (location.origin).concat(location.pathname).concat(location.hash);
    $('.menu-item').each(function(){
        if ($(this).attr('href') == current) {
            $(this).parents('.nav-item.has-sub').addClass('open');
            $(this).parent().addClass('active');
        }else{
            if(!$(this).parents('.nav-item-submenu').hasClass('nav-item-expanded nav-item-open')){
                $(this).removeClass('active');
            }
        } 
    })
})