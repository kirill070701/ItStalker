jQuery(document).ready(()=>{
    $(document).ready(()=>{
        var leftSidebar = $('.left-sidebar');
        if ($(window).width() <= 700) {
            $('.search').detach().appendTo('.blog .left-sidebar');
        }
        $(window).resize(()=>{
            var width = $(window).width();
            if (width <= 700) {
                $('.search').detach().appendTo('.blog .left-sidebar');
                $('.left-sidebar').css({
                    'left': '-235px'
                })
            }else{
                $('.left-sidebar').css({
                    'left': '0px'
                })
                leftSidebar.removeClass('menuOn');
            }
            
            if (width > 700 && $('main .search').length) {
                $('header .panel-menu').before( $('.search'));
            }
        })
    })
})