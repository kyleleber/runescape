/**
 * @file
 * JavaScript behaviors for the runescape map element.
 */

(function ($, Drupal, drupalSettings) {
  $.each(drupalSettings.player_coordinates, function(key, obj) {
    let new_target = '<img id="'+obj.id+'" class="avatar" src="/assets/rsc_avatars/'+obj.id+'.png" tabindex="0" title="'+obj.username+': Level '+obj.combat+' ('+obj.x+','+obj.y+')"   style="right:'+(obj.x*4.58)+'px; top:'+(obj.y * 4.08)+'px"></img>';
    $('.runescape-map-internal').append(new_target);
  });
})(jQuery, Drupal, drupalSettings);
