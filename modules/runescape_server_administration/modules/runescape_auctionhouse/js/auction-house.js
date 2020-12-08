/**
 * @file
 * JavaScript behaviors for the runescape map element.
 */

(function ($) {

  $('.tablink').on('click', function() {
    let target = $(this).attr('data-tab-target');
    $('.tabcontent').each(function() {
      $(this).hasClass(target) ? $(this).show() : $(this).hide();
    });
  });

  $('.tablink:first').trigger('click');

})(jQuery);
