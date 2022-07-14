jQuery(document).ready(()=>{
    $(document).ready(()=>{
        $('#field-autorization').toggle(); 
        $('.panel-menu .avatar').on("click", ()=>{
            $('#field-autorization').toggle('');
        });

        $('#button-registration').click(()=>{
            $('.panel')
            .animate({
                left: "-100%",
            })
            $('#field-autorization')
            .animate({
                height: "500px"
            })
        })
        $('#button-autorization').click(()=>{
            $('.panel')
            .animate({
                left: "100%"
            })
            
        })

        var lineCanvas;
        var ctx;
        for (let i = 0; i < $('.line').length; i++) {
            lineCanvas = $('.line')[i]
            ctx = lineCanvas.getContext('2d');

            ctx.moveTo(0,0);
            ctx.lineTo($('.line')[0].width, 0);
            ctx.strokeStyle = "#444444"
            ctx.lineWidth = 30
            ctx.stroke();
        }

        $("#button-back-right, #button-back-left").click(()=>{
            $('.panel')
            .animate({
                left: 0
            })
        });
        $('#button-back-left').click(()=>{
            $('#field-autorization')
            .animate({
                height: '375px'
            })
        });

        $('.button-new-foto').click(()=>{
            $('.editor-avatar').css({
                "display": "block"
            });
        });
        $('#button-exit-editor').click(()=>{
            $('.editor-avatar').css({
                "display": "none"
            });
        })

        // мобильное меню
        var leftSidebar = $('.left-sidebar');
        $('.menu-mobil').click(()=>{
            if (!leftSidebar.hasClass('menuOn')) {
                leftSidebar.addClass('menuOn');
                leftSidebar.animate({
                    'left': '0px'
                }, 500)
            }else{
                leftSidebar.removeClass('menuOn');
                leftSidebar.animate({
                    'left': '-235px'
                }, 500)
            }
        });        
    });
});