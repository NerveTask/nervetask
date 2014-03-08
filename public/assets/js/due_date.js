// due_date.js

(function($) {
    var storageFormat = "{yyyy}-{MM}-{dd} {hh}:{mm}:{ss}"
    $(document).ready(function() {
        // var el = $('.nervetask-due-date');
        var input = $('#nervetask-new-task-due-date-visible');
        var output = $('#nervetask-new-task-due-date-feedback');
        var hidden = $('#nervetask-new-task-due-date');

        input.keyup(function() {
            var val = input.val().trim();
            if (/^\d+$/.test(val)) {
                val = val.toNumber();
            }
            var text, date = Date.create(String(val));
            if (!date.isValid()) {
                text = 'Invalid date.'
            } else {
                text = date.full();
                hidden.val(date.format(storageFormat));
            }
            output.text(text);
        });
        input.val('next friday at 5pm').keyup();
    });

})(jQuery);