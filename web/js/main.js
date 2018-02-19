$(document).ready( function () {

    document.oncontextmenu = function() {return false;};
    var anim = true;
    var dir;
    //var action;
    ajaxOpenDir();


    $('.box').on('click', 'input', function(){
        dir = $(this).val();
        countChecked(this);
        var check = ($(this).prop("checked"));
        $('.copy, .move, .remove, .doload').prop('disabled',!check);
    });
    $('.doload').on('click', function(){
        //var target = e && e.target || event.srcElement;

        window.location.href="ajax?download=" + dir;

        //actionAja('doload');
        //var action = 'doload';
        //console.log(action);
        //console.log(dir);
        //dir = '';
    });
    $('.copy').on('click', function(){
        var name = prompt('Введите путь куда скопировать','');
        if (!(name == '')){
            actionAja('copy', name);
        } else alert('Вы не ввели данные!');
        //console.log(dir);
        //dir = '';
    });
    $('.move').on('click', function(){
        var name = prompt('Введите новое имя файла/папки','');
        if (!(name == '')){
            actionAja('move', name);
        } else alert('Вы не ввели данные!');
        //console.log(dir);
        //dir = '';
    });
    $('.newfolder').on('click', function(){
        var name = prompt('Введите имя папки','');
        if (!(name == '')){
            actionAja('newfolder', name);
        } else alert('Вы не ввели данные!');
        //console.log(dir);
        //dir = '';
    });
    $('.zipARX').on('click', function(){
            actionAja('zipARX');
        //console.log(dir);
        //dir = '';
    });
    $('.remove').on('click', function(){

        if(confirm('Вы уверены?')){
            actionAja('remove');
        }
        //console.log(dir);
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
                        .append('<li><a class="disabled" href="#" disabled>Переименовать</a></li>')
                        .append('<li><a class="disabled" href="#">Удалить</a></li>')
                )
                .show('fast'); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
        }
    });


    function ajaxOpenDir(dir, action, name) {
        name = name || '';
        dir = dir || '';
        action = action || '';
        //dir = (!dir) ? '' : dir;

        $.ajax({
            type: "POST",
            url: 'ajax',
            data: {
                dir: dir,
                name: name,
                action: action
            },
            success: function(data) {
                parseJSN(data);
                //console.log(action);
            }
        });
    }


//============Блок работы с файлами================================
    function parseJSN(answer) {
        var answerPars = jQuery.parseJSON(answer);
        if (answerPars.status === 'action'){
            alert(answerPars.text);
            ajaxOpenDir(answerPars.dir);
        } else {
            parseAnswerText(answerPars);
            $('.box').html(parseAnswerFiles(answerPars));
            fontSizeAnim(0);
            (!anim) ? $('.box-elem').show() : $('.box-elem').slideDown(800);
        }
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
            dir = answer.dir;
            rez += '<label>Текущий путь:&nbsp;&nbsp;&nbsp;' + answer.dir + '</label>';
            rez += addElem('/',answer.prev,'','');

            if (answer.status == 200) {
                if (typeof answer.dirs !== 'undefined'){
                    $.each(answer.dirs,function(key,value){
                        rez += addElem(
                            answer.dirs[key].label,
                            key,
                            answer.dirs[key].date,
                            answer.dirs[key].perssion,
                            '0',
                            'Папка'
                        );
                    });
                }
                if (typeof answer.files !== 'undefined'){
                    $.each(answer.files,function(key,value){
                        rez += addElem(
                            answer.files[key].label,
                            key,
                            answer.files[key].date,
                            answer.files[key].perssion,
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
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").animate({ height: '20px' });
            } else if (sel == 2) {
                $(".box").animate({ fontSize: '25px' });
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").animate({ height: '30px' });
            } else if (sel == 3) {
                $(".box").animate({ fontSize: '35px' });
                $(".elem").animate({ width: '30% !important' });
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").animate({ height: '45px' });
            }
        } else if(animat == 0) {
            if (sel == 1) {
                $(".box").css( 'fontSize', '15px' );
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").css( 'height', '20px' );
            } else if (sel == 2) {
                $(".box").css( 'fontSize', '25px' );
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").css( 'height', '30px' );
            } else if (sel == 3) {
                $(".box").css( 'fontSize', '35px' );
                $(".elem").css( 'width', '30% !important' );
                $(".elem, .elem-info, .elem-size, .elem-perssion, .elem-date").css( 'height', '45px' );
            }
        }
    }
    function addElem(label, checkbox, date, perssion, size, info) {
        checkbox = checkbox || '';
        size = size || 'Размер';
        info = info || 'Тип';
        perssion = perssion || 'Права';
        date = date || 'Дата создания';

        return '<div class="box-elem">' +
            '<div class="elem">' +
            '<p>' +
            '<input id="checkbox" type="checkbox" value="' + checkbox + '"/>&nbsp;' +
            '<label>' + label + '</label>' +
            '</p>' +
            '</div>' +
            '<div class="elem-date">' + date + '</div>' +
            '<div class="elem-perssion">' + perssion + '</div>' +
            '<div class="elem-size">' + size + '</div>' +
            '<div class="elem-info">' + info + '</div>' +
            '</div>';

    }
//============Конец блкока работы с файлами=========================

//============Блок работы с кнопками================================
    function countChecked(a) {
        //$("input:checkbox").removeAttr("checked");
        var n = $("input:checked").length;
        if (n == 2){
            $('input:checkbox').prop('checked', $(this).is(':checked'));
            $(a).prop('checked', true)
        }
        //console.log(n + (n <= 1 ? " is" : " are") + " checked!");
    }
    function actionAja(action, name) {

       return ajaxOpenDir(dir, action, name)

    }

//============Конец блкока работы с кнопками=========================







});


