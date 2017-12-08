$(function() {
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    moment.locale('lt');

    $.datetimepicker.setLocale('lt');

    function bindElements() {
        $('[data-toggle="tooltip"]').tooltip();

        $('.datetime').datetimepicker({
            format:'Y-m-d H:i',
            lang: 'lt',
            dayOfWeekStart: 1
        });

        $('.date').datetimepicker({
            format:'Y-m-d',
            lang: 'lt',
            timepicker: false,
            dayOfWeekStart: 1
        });

        $('.select2').select2({
            theme: "bootstrap"
        });

        $('[data-timeago]').each(function() {
            $(this).html(moment($(this).data('timeago')).fromNow());
        });
    }

    bindElements();

    $('#calendar').fullCalendar({
        lang: 'lt',
        events: '/events',
        header: { center: 'month,agendaWeek,agendaDay' }
    });

    $(document).on('bsmodal.js-modal.loaded', function() {
        bindElements();
    });

    $(document).on('click', '.js-back', function(e) {
        e.preventDefault();
        if (document.referrer.split('/')[2] === window.location.host) {
            history.back();
        }
    });

    $('form').submit(function () {
        $('button[type="submit"]').attr('disabled', true);
    });

    $('.double-click-prevent').click(function () {
        $(this).attr('disabled', true);
        $(this).css('pointer-events', 'none');
    });

    $('form input, form select, form textarea').change(function () {
        $('button[type="submit"]').attr('disabled', false);
    });

    $(document).on('click', '.js-confirm-btn', function(e) {
        $(this).attr('disabled', true);
    });
});
