jQuery(document).ready(function($) {
  $(document).on('click', '#hp_woo_rewards_apply_points, #hp_woo_rewards_remove_points', function(event) {

    if ($(this).attr('id') == 'hp_woo_rewards_remove_points') {
      $('#hp_woo_remove_point_button').trigger('click');
    }
    else {
      $('#hp_woo_apply_point_button').trigger('click');
    }

    event.preventDefault();
  });
});
