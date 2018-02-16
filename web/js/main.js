$(document).ready( function () {

    document.oncontextmenu = function() {return false;};
    var anim = true;
    var dir;
    ajaxOpenDir();


    $('.box').on('click', 'input', function(){
        dir = $(this).val();
        //ajaxOpenDir(dir);
        countChecked(this);
        var check = ($(this).prop("checked"));
           // $('.copy, .move, .remove').prop('disabled',false);
        //} else $('.copy, .move, .remove').prop('disabled',true);
        $('.copy, .move, .remove').prop('disabled',!check);
    });
    
    $('.copy, .move, .remove').on('click', function(){
        console.log(dir);
        //dir = '';
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
    function countChecked(a) {
        //$("input:checkbox").removeAttr("checked");
        var n = $("input:checked").length;
        if (n == 2){
            $('input:checkbox').prop('checked', $(this).is(':checked'));
            $(a).prop('checked', true)
        }
        //console.log(n + (n <= 1 ? " is" : " are") + " checked!");
    }


    function ajaxOpenDir(dir) {
        //dir = dir || '';
        dir = (!dir) ? '' : dir;
        $.ajax({
            type: "POST",
            url: 'ajax',
            data: { dir: dir },
            success: function(data) {
                parseJSN(data);
            }
        });
    }

    function parseJSN(answer) {
        var answerPars = jQuery.parseJSON(answer);
        parseAnswerText(answerPars);
        $('.box').html(parseAnswerFiles(answerPars));
        fontSizeAnim(0);
        (!anim) ? $('.box-elem').show() : $('.box-elem').slideDown(800);
    }

    function addElem(label, checkbox, size, info) {
        checkbox = checkbox || '';
        size = size || '';
        info = info || '';

        return '<div class="box-elem">' +
                '<div class="elem">' +
                    '<p>' +
                        '<input id="checkbox" type="checkbox" value="' + checkbox + '"/>&nbsp;' +
                        '<label>' + label + '</label>' +
                    '</p>' +
                '</div>' +
                '<div class="elem-size">' + size + '</div>' +
                '<div class="elem-info">' + info + '</div>' +
            '</div>';

    }
    function parseAnswerText(answer) {
        $('.text').remove();
        if (answer.status == 300) {
            $('<textarea/>').addClass('text').css({
                "background-color": '#fff',
                position: 'absolute',
                width: '60%',
                overflow: 'scroll',
                height: '50%',
                left: '20%',
                top: '30%'
            })
                .appendTo('body')
                .append( answer.text );
        }
    }

    function parseAnswerFiles(answer) {
        var rez = '';

        if (typeof answer !== 'undefined') {
            rez += '<label>Текущий путь:&nbsp;&nbsp;&nbsp;' + answer.dir + '</label>';
            rez += addElem('/',answer.prev,'','');

            if (answer.status == 200) {
                if (typeof answer.dirs !== 'undefined'){
                    $.each(answer.dirs,function(key,value){
                        rez += addElem(
                            answer.dirs[key].label,
                            key,
                            '',
                            'Папка'
                        );
                    });
                }
                if (typeof answer.files !== 'undefined'){
                    $.each(answer.files,function(key,value){
                        rez += addElem(
                            answer.files[key].label,
                            key,
                            answer.files[key].size,
                            'Файл'
                        );
                    });
                }
            }
            // if (answer.status == 300) {
            //     rez += answer.text;
            // }
            if (answer.status == 400) {
                rez += answer.text;
            }
        }
        return rez;
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


