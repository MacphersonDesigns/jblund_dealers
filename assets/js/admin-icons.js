/* global wp */
/**
 * JBLund Dealers - Icon picker for the Service Icons settings section.
 * Opens the WP media library and writes the selected attachment ID + preview.
 */
(function ($) {
    'use strict';

    $(document).on('click', '.jblund-icon-select', function (e) {
        e.preventDefault();

        var $btn     = $(this);
        var $wrap    = $btn.closest('.jblund-icon-field');
        var $input   = $wrap.find('.jblund-icon-id');
        var $preview = $wrap.find('.jblund-icon-preview');

        var frame = wp.media({
            title:    'Select Icon',
            button:   { text: 'Use this image' },
            multiple: false,
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();

            $input.val(attachment.id);
            $preview.attr('src', attachment.url).show();
            $btn.text('Change Icon');

            // Show a Remove button if not already present
            if (!$wrap.find('.jblund-icon-remove').length) {
                $btn.after(
                    $('<button type="button" class="button-link jblund-icon-remove">')
                        .text('Remove')
                        .css({ marginLeft: '8px', color: '#b32d2e' })
                );
            }
        });

        frame.open();
    });

    $(document).on('click', '.jblund-icon-remove', function (e) {
        e.preventDefault();

        var $wrap    = $(this).closest('.jblund-icon-field');
        var $input   = $wrap.find('.jblund-icon-id');
        var $preview = $wrap.find('.jblund-icon-preview');
        var $select  = $wrap.find('.jblund-icon-select');

        $input.val('');
        $preview.attr('src', '').hide();
        $select.text('Select Icon');
        $(this).remove();
    });

}(jQuery));
