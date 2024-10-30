<?php
  $customer_id = WC()->customer->get_id();
  $points = $this->points->get_customer_total_points($customer_id);
  $reward_details = $this->points->get_customer_points_details($customer_id);
  $point_name = $this->points->point_name;
?>

<h3><?php printf( __( 'Your %s', 'hostplugin-woocommerce-points-reward'), $point_name);?></h3>
<p><?php printf( __( 'You have %s %s', 'hostplugin-woocommerce-points-reward'), $points, $point_name);?></p>
<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
  <thead>
    <tr>
        <th class="woocommerce-orders-table__header"><span class="nobr"><?php _e('Date', 'hostplugin-woocommerce-points-reward'); ?></span></th>
        <th class="woocommerce-orders-table__header"><span class="nobr"><?php _e('Order ID', 'hostplugin-woocommerce-points-reward'); ?></span></th>
        <th class="woocommerce-orders-table__header"><span class="nobr"><?php _e('Comment ID', 'hostplugin-woocommerce-points-reward'); ?></span></th>
        <th class="woocommerce-orders-table__header"><span class="nobr"><?php _e('Event', 'hostplugin-woocommerce-points-reward'); ?></span></th>
        <th class="woocommerce-orders-table__header"><span class="nobr"><?php echo $point_name ?></span></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ( $reward_details as $detail ) :
      $detail['event'] = $this->points->parse_point_name($detail['event']);
      ?>
      <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status- order">
          <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-">
            <?php echo human_time_diff( $detail['date'], current_time('timestamp',1) ) . __(' ago', 'hostplugin-woocommerce-points-reward'); ?>
          </td>
          <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-">
             <?php
              if (!empty($detail['order_id'])) {
              $order = wc_get_order($detail['order_id']);
             ?>
             <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
               <?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number(); ?>
             </a>
             <?php
              }
             ?>
          </td>
          <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-">
             <?php
              if (!empty($detail['comment_id'])) {
              $comment_url = get_comment_link( $detail['comment_id'] );
             ?>
             <a href="<?php echo $comment_url ?>">
               <?php echo _x( '#', 'hash before order number', 'woocommerce' ) . $detail['comment_id']; ?>
             </a>
             <?php
              }
             ?>
          </td>
          <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-">
            <?php echo $detail['event']?>
          </td>
          <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-">
            <?php echo $detail['points']?>
          </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
