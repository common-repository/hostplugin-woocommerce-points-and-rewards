<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

Use \HPWooRewardsIncludes\Helper;

/**
 * Order class, handle order status
 */
class Order {

	public $points;
   /**
   * Construct
   *
   * A place to add hooks and filters
   *
   * @since 1.0
   *
   */
	public function __construct() {
		$point_class = Helper::get_class_name('\HPWooRewardsIncludes\Points');
    	$this->points = new $point_class();

		if ($this->points->is_point_system_enabled() == true) {
			add_action( 'woocommerce_order_status_completed', array($this,'add_points'), 15 );
			add_action( 'woocommerce_checkout_order_processed', array($this,'deduct_points'),  1, 1  );
			//woocommerce_new_order
			//??woocommerce_payment_complete
		}
	}

	/**
	 * Fire when order status is completed. Add points to user account
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @return void
	 *
	 */
	public function add_points($order_id) {
		$order = wc_get_order( $order_id );
		$customer_id = $order->get_customer_id();
		$this->points->add_reward_points_for_purchase($order_id, $customer_id);
	}//public

	/**
	 * trigger when checkout is processed
	 * deduct points if points is used
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @return void
	 *
	 */
	public function deduct_points($order_id) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' for Order ID: '.$order_id);
		$points_used = (int)WC()->session->get('hp_woo_rewards_points_used');
		if (WC()->session->get('hp_woo_rewards_enabled') == 'true' && $points_used > 0) {
			$order = wc_get_order( $order_id );
			$customer_id = $order->get_customer_id();
			//error_log('HP-WOO-REWARDS: Customer ID: '.$customer_id. ' to redeem: '.$points_used.' on Order: '.$order_id);
			$this->points->redeem_points_on_purchase($customer_id, $order_id, -$points_used);
		}

		//always reset session after order
		WC()->session->set('hp_woo_rewards_enabled', null);
		WC()->session->set('hp_woo_rewards_discount', null);	
		WC()->session->set('hp_woo_rewards_points_used', null);	
		WC()->session->set('hp_woo_rewards_total_points', null);	
	}
}
