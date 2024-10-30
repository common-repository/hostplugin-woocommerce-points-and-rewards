<h2><?php esc_html_e( 'Woo Rewards', 'hostplugin-woocommerce-points-reward' ); ?></h2>
<table class="form-table">
  <tbody><tr class="user-point-wrap">
  	<th><label for="point"><?php esc_html_e( 'Total Points', 'hostplugin-woocommerce-points-reward' ); ?></label></th>
  	<td>
      <?php
        $points = $this->points->get_customer_total_points($profileuser->ID);
      ?>
          <input type="number" step="1" name="hp_woo_reward_points" id="hp_woo_reward_points" value="<?php  esc_attr_e($points); ?>" class="short">
          <?php _e('Reason: ', 'hostplugin-woocommerce-points-reward'); ?><input type="text" name="hp_woo_reward_points_update_reason" id="hp_woo_reward_points_update_reason" placeholder="<?php _e('Optional', 'hostplugin-woocommerce-points-reward') ?>" class="regular-text">
  	</td>
  </tr>
  </tbody>
</table>

<?php
  $purchase_details = $this->points->get_customer_points_details($profileuser->ID);
  if (!empty($purchase_details)) {
?>

<table class="wp-list-table widefat fixed striped">
	<thead>
  	<tr>
  		<th scope="col" id="hp_woo_rewards_log_date" class="manage-column column-date column-primary"><?php _e('Date', 'hostplugin-woocommerce-points-reward'); ?></th>      
      <th scope="col" id="hp_woo_rewards_log_order_id" class="manage-column column-format"><?php _e('Order ID', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_comment_id" class="manage-column column-format"><?php _e('Comment ID', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_event" class="manage-column column-event"><?php _e('Event', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_point" class="manage-column column-format"><?php _e('Points', 'hostplugin-woocommerce-points-reward'); ?></th>
    </tr>
	</thead>
	<tbody>
    <?php

      foreach ($purchase_details as $detail) {

        $detail['event'] = $this->points->parse_point_name($detail['event']);
    ?>
        <tr>
          <td class="log_date column-date"><?php
          echo date_i18n( get_option( 'date_format' ), $detail['date'] )?></td>
          <td class="log_order_id column-order_id">
            <?php
            if (isset($detail['order_id']) && strlen($detail['order_id']) > 0) {
            ?>
              <a href="<?php echo admin_url( 'post.php?post='.$detail['order_id'].'&action=edit');?>">#<?php echo $detail['order_id']?></a>
           <?php
            }
            ?>
          </td>
          <td class="log_comment_id column-comment_id">
            <?php
            if (isset($detail['comment_id']) && strlen($detail['comment_id']) > 0) {
            ?>
              <a href="<?php echo get_comment_link( $detail['comment_id'] );?>">#<?php echo $detail['comment_id']?></a>
           <?php
            }
            ?>
          </td>
          <td class="log_event column-event"><?php echo $detail['event']?></td>
          <td class="log_point column-point"><?php echo $detail['points']?></td>
        </tr>
    <?php
      }//foreach
    ?>
  </tbody>
	<tfoot>
    <tr>
      <th scope="col" id="hp_woo_rewards_log_date" class="manage-column column-date column-primary"><?php _e('Date', 'hostplugin-woocommerce-points-reward'); ?></th>      
      <th scope="col" id="hp_woo_rewards_log_order_id" class="manage-column column-format"><?php _e('Order ID', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_comment_id" class="manage-column column-format"><?php _e('Comment ID', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_event" class="manage-column column-event"><?php _e('Event', 'hostplugin-woocommerce-points-reward'); ?></th>
      <th scope="col" id="hp_woo_rewards_log_point" class="manage-column column-format"><?php _e('Points', 'hostplugin-woocommerce-points-reward'); ?></th>
    </tr>
	</tfoot>
</table>
<?php
}//if log not empty
?>
