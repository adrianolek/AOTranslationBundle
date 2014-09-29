(function() {
    var $, methods;
    $ = jQuery;
    methods = {
        insert: function(value) {
            var after, before, o, pos;
            o = this[0];
            pos = methods['getCaretPosition'].apply(this);
            before = o.value.substring(0, pos);
            after = o.value.substring(pos, o.value.length);
            $(o).val(before + value + after);
            pos += value.length;
            return methods['setCaretPosition'].apply(this, [pos]);
        },
        getCaretPosition: function() {
            var caretPos, o, sel;
            o = this[0];
            caretPos = 0;
            if (document.selection) {
                o.focus();
                sel = document.selection.createRange();
                sel.moveStart('character', -o.value.length);
                caretPos = sel.text.length;
            } else if (o.selectionStart || o.selectionStart === '0') {
                caretPos = o.selectionStart;
            }
            return caretPos;
        },
        setCaretPosition: function(pos) {
            var f, o;
            o = this[0];
            if (o.setSelectionRange) {
                o.focus();
                return o.setSelectionRange(pos, pos);
            } else if (o.createTextRange) {
                f = function() {
                    return o.focus();
                };
                setTimeout(f, 10);
                f = function(o, pos) {
                    var range;
                    range = o.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', pos);
                    range.moveStart('character', pos);
                    return range.select();
                };
                setTimeout("f(o, pos)", 20);
                return pos;
            }
        }
    };

    $.fn.atCaret = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            return $.error('Method ' + method + ' does not exist on jQuery.atCaret');
        }
    };
}).call(this);