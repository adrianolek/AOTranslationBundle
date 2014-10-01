$(function () {
    var message_ids = [];
    var focused = null;
    var form = $('#ao_translations_form');
    var rows = form.find('tr');
    form.find('textarea').change(function () {
        message_ids.push($(this).parents('tr').data('id'));
        $('#translations_ids').val($.unique(message_ids).join(','));
    }).focus(function () {
        rows.removeClass('focus');
        $(this).parents('tr').addClass('focus');
        focused = $(this);
    });

    form.find('ul.parameters a').click(function () {
        var row = $(this).parents('tr');
        if (row.find(focused).length) {
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
            row.find('textarea').each(function (i, e) {
                window.setTimeout(function () {
                    $(e).effect("highlight", {}, 1000);
                }, 700 * i);
            });
        }
        return false;
    });

    $('#ao_translation_reset_action_cache').click(function () {
        $('.flash').remove();
        $.post($(this).data('href'), function () {
            $('#ao_translations_form').before('<div class="flash flash-notice">Action cache has been cleared!</div>');
        });
    });

    $('#ao_translation_reset_cache').click(function () {
        $('.flash').remove();
        $.post($(this).data('href'), function () {
            $('#ao_translations_form').before('<div class="flash flash-notice">Cache has been cleared!</div>');
        });
    });
});
