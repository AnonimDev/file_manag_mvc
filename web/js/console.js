$(document).ready( function () {

    $( 'html' ).keydown(function( event ){ // задаем функцию при нажатиии любой клавиши клавиатуры на элементе
        if (event.which == 13){
            var comand = $('.btn > input');
            ajaxConsole(comand.val());
            comand.val('');
        }
    });

    function ajaxConsole(comand) {
        if (!(comand == '')) {
            $.ajax({
                type: "POST",
                url: 'ajaxConsole',
                data: {comand: comand},
                success: function (data) {
                    //console.log(data);
                    parseJS(data);
                    //var answerPars = jQuery.parseJSON(data);
                    //console.log(data);
                }
            });
        } else alert('Вы не ввели команду!')
    }

    function parseJS(answer) {

        //var answerPars = jQuery.parseJSON(answer);
        //console.log(answerPars);
        //if (answerPars.status == 200){
            $('.box-1-table > tbody').html(addtable(answer));
        //}
    }
    function addtable(text) {
        return '<tr><td>' + text + '</td></tr>';
    }

});