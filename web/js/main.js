$(document).ready( function () {

    document.oncontextmenu = function() {return false;};
    var anim = true;
    ajaxOpenDir();


    $('.box').on('click', 'input', function(){
        var dir = $(this).val();
        ajaxOpenDir(dir);
    });

    $('.box').on('click', 'label', function(){
        var dir = $(this).prev();
        dir = $(dir).val();
        ajaxOpenDir(dir);
    });
    $(document).on('click', 'a', function(){
        var Elem = $('.selected-html-element').prev().val();

        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();
        ajaxOpenDir(Elem);
        //console.log(Elem);
    });

    $(document).on('change', '.sel', function () {
        fontSizeAnim(anim);
    });
    $(document).on('change', '.anim', function () {
        var animElem = $('.anim').val();
        anim = (animElem == 0) ? false : true;
    });
    $('.box').on('mousedown', 'label', function( event ){
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();
        if (event.which == 3){
            // Получаем элемент на котором был совершен клик:
            var target = $(event.target);

            // Добавляем класс selected-html-element что бы наглядно показать на чем именно мы кликнули (исключительно для тестирования):
            target.addClass('selected-html-element');

            // Создаем меню:
            $('<div/>', {
                class: 'context-menu' // Присваиваем блоку наш css класс контекстного меню:
            })
                .css({
                    left: event.pageX+'px', // Задаем позицию меню на X
                    top: event.pageY+'px' // Задаем позицию меню по Y
                })
                .appendTo('body') // Присоединяем наше меню к body документа:
                .append( // Добавляем пункты меню:
                    $('<ul/>').append('<li class="qw"><a href="#">Открыть</a></li>')
                        .append('<li><a href="#">Переименовать</a></li>')
                        .append('<li><a href="#">Удалить</a></li>')
                )
                .show('fast'); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
        }
    });

    function ajaxOpenDir(dir) {
        if (dir == undefined){
            var dir = '';
        }
        $.ajax({
            //async: false,
            type: "POST",
            url: 'ajax',
            data: {
                dir: dir
            },
            success: function(data) {
                $('.box').html(data);
                fontSizeAnim(0);
                if (anim == 0) {
                    $('.box-elem').show();
                } else $('.box-elem').slideDown(800);
            }
        });
    }
    function fontSizeAnim(animat) {
        var sel = $('.sel').val();
        if (animat == 1) {
            if (sel == 1) {
                $(".box").animate({ fontSize: '15px' });
                $(".elem, .elem-info, .elem-size").animate({ height: '20px' });
            } else if (sel == 2) {
                $(".box").animate({ fontSize: '25px' });
                $(".elem, .elem-info, .elem-size").animate({ height: '30px' });
            } else if (sel == 3) {
                $(".box").animate({ fontSize: '35px' });
                $(".elem").animate({ width: '30% !important' });
                $(".elem, .elem-info, .elem-size").animate({ height: '45px' });
            }
        } else if(animat == 0) {
            if (sel == 1) {
                $(".box").css( 'fontSize', '15px' );
                $(".elem, .elem-info, .elem-size").css( 'height', '20px' );
            } else if (sel == 2) {
                $(".box").css( 'fontSize', '25px' );
                $(".elem, .elem-info, .elem-size").css( 'height', '30px' );
            } else if (sel == 3) {
                $(".box").css( 'fontSize', '35px' );
                $(".elem").css( 'width', '30% !important' );
                $(".elem, .elem-info, .elem-size").css( 'height', '45px' );
            }
        }
    }
});


