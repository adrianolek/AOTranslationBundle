$(function () {
    var focused = null;
    var textareas = $('.box-body textarea');
    textareas.focus(function(){
        focused = $(this);
    });

    $('#message-parameters').find('a').click(function () {
        if (focused) {
            var event = document.createEvent('TextEvent');
            var parameter = $(this).data('parameter');

            if (event.initTextEvent) {
                var pos = focused.atCaret('getCaretPosition');
                event.initTextEvent('textInput', true, true, null, parameter);
                focused.get(0).dispatchEvent(event);
                focused.atCaret('setCaretPosition', pos + parameter.length);
            }
            else // for firefox
            {
                focused.atCaret('insert', parameter);
                focused.trigger('change');
            }
        }
        else {
            textareas.each(function (i, e) {
                window.setTimeout(function () {
                    $(e).effect("highlight", {}, 1000);
                }, 700 * i);
            });
        }
        return false;
    });
});
