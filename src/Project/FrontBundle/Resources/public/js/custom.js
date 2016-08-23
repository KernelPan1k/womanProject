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
    $(document).ready(function () {
        $('.js-datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: false,
            language: 'fr',
            todayHighlight: true
        });
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('[data-upload]').click(function () {
        var target = $(this).data('upload');
        var field = $('.js-upload-handler').find('[data-upload-field=' + target + ']');
        field.click();
        field.change(function () {
            var form = field.closest('form');
            form.submit();
        });
    });

    $('#collapseContent').on('shown.bs.collapse', function () {
        $(window).scrollTop(($(this).offset().top - 40), 2000);
    });
});

$(function () {
    var networkForm = $('.form-search-network');
    var type = networkForm.find('#network_type');
    var region = networkForm.find('#network_region').parent();
    var city = networkForm.find('#network_city').parent();
    var distance = networkForm.find('#network_distance').parent();
    var CITY = '0';
    var REGION = '1';
    toogleField(type.val());
    type.change(function () {
        toogleField($(this).val());
    });
    function toogleField(val) {
        switch (val) {
            case CITY:
                region.hide();
                city.show();
                distance.show();
                break;
            case REGION:
                city.hide();
                distance.hide();
                region.show();
                break;
            default:
                return;
                break;
        }
    }
});

$(function () {
    var $form = $('[data-pagination-form]');
    var $content = $('[data-pagination-content="' + $form.attr('data-pagination-form') + '"]');
    var target = '[data-pagination-button="' + $form.attr('data-pagination-form') + '"] .next-result';
    $(document).on('click', target, function (e) {
        e.preventDefault();
        var $that = $(e.target);
        $.ajax({
            method: 'POST',
            url: $(this).attr('href'),
            data: $form.serialize(),
            beforeSend: function () {
                $('#ajax-loader').show();
            }
        }).done(function (html) {
            $that.parent().parent().remove();
            $content.append(html);
        }).fail(function () {
            $.notify({message: 'Une erreur est survenue !'}, {type: 'danger'});
        }).always(function () {
            $('#ajax-loader').hide();
        });
    });
});

$(function () {
    $('[data-click-cascade]').click(function () {
        $('[data-click-target="' + $(this).attr('data-click-cascade') + '"]').click();
    });
});

$(function () {
    $('[data-forum-parent]').click(function (e) {
        e.preventDefault();
        $('.cloned').hide().remove();
        var $that = $(this);
        var parent = $that.closest('.comment-closest');
        var $proto = $('#form-send-response').find('form');
        var clone = $proto.clone();
        clone.addClass('cloned');
        clone.appendTo(parent);
        clone.find('#comment_post_parent').val($that.data('forum-parent'))
    });
});