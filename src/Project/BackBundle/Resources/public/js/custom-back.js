jQuery(function () {
    jQuery.fn.ajax2choice = function () {
        return this.each(function () {
            var $that = jQuery(this);
            var url = $that.data("ajax-url");
            $that.select2({
                locale: '{{ app.request.locale }}',
                ajax: {
                    url: url,
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        }
                    }
                },
                minimumInputLength: 3
            });
        });
    };
    $("select[data-ajax-url]").ajax2choice();
    $(document).on("ajaxreload", function () {
        $("select[data-ajax-url]").ajax2choice();
    });
    jQuery("select[data-select2-options]").each(function (e) {
        var $this = jQuery(this);
        $this.select2($this.data("select2-options"));
    });
});
$(function () {
    $('[data-upload]').click(function () {
        var target = $(this).data('upload');
        var field = $('.js-upload-handler').find('[data-upload-field=' + target + ']');
        field.click();
        field.change(function () {
            var form = field.closest('form');
            form.submit();
        });
    });
});