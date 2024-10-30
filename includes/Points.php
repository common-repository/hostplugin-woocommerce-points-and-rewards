<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

Use \HPWooRewardsIncludes\Helper;

/**
 * Points Class
 */
class Points {

	public $option;
	public $point_name;

	const LOG_TYPE_PURCHASE_REWARDS = 1;			//(+)   receive purchase rewards
	const LOG_TYPE_REDEEM_REWARDS = 2;				//(-)   redeem points
	const LOG_TYPE_SIGNUP_REWARDS = 3;				//(+)   get signup rewards
	const LOG_TYPE_ADJUSTMENT_BY_SELLER = 4;		//(+/-) manually adjust by admin / seller
	const LOG_TYPE_REVIEW_REWARDS = 5;				//(+)   get review rewards

  /**
   * Construct
   *
   * A place to add hooks and filters
   *
   * @since 1.0
   *
   */
	public function __construct() {
		$this->option = Helper::get_settings_option();
		$this->point_name = $this->get_point_name();
	}

	/**
	 * Check if point system is enabled
	 *
	 * @since 1.0
	 * @return boolean
	 *
	 */
	public function is_point_system_enabled() {
		if ($this->option['is_system_enabled'] == 'on') return true;
		return false;
	}

	/**
	 * Calculate the point conversion. If I spend $1 how much points do I get?
	 *
	 * @since 1.0
	 * @return float
	 *
	 */
	private function get_point_conversions() {
		$point_conversion = (int)$this->option['points_earned'] / (int)$this->option['dollar_spent'];
		return floatval($point_conversion);
	}

	/**
	 * Calculate the points one will earn by using the purchase amount (before round conversion)
	 *
	 * @since 1.0
	 * @param string $purchase_amount
	 * @return float
	 *
	 */
	protected function calculate_points($purchase_amount) {
		$points = floatval($purchase_amount) * $this->get_point_conversions();
		return $points;
	}

	/**
	 * return the points based on the rounded conversion selected on admin settings page
	 *
	 * @since 1.0
	 * @param floatval $points
	 * @return integer
	 *
	 */
	private function round_conversion($points) {
		if ($this->option['round_off'] == 'round-down') {
			return floor($points);
		}
		else if ($this->option['round_off'] == 'round-up') {
			return ceil($points);
		}

		return round($points);
	}

	/**
	 * Calculate the maximum point discount for customer
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param floatval $max_amount maximum discount amount
	 * @return float
	 *
	 */
	public function get_point_discount($customer_id, $max_amount = 0) {
		$customer_points = $this->get_customer_total_points($customer_id);
		$customer_discount = $customer_points * $this->get_redeem_point_conversions();

		//max reached
		if ($customer_discount > $max_amount) $customer_discount = $max_amount;
		$customer_discount = floatval($customer_discount);
		return round($customer_discount, 2);
	}

	/**
	 * Calculate the redeem point conversion. 1 point is equivalent to how much $?
	 *
	 * @since 1.0
	 * @return float
	 *
	 */
	private function get_redeem_point_conversions() {
		$redeem_conversion = (int)$this->option['redeem_dollar'] / (int)$this->option['redeem_points'];
		return floatval($redeem_conversion);
	}	

	/**
	 * Get total points based on the given price
	 *
	 * @since 1.0
	 * @param floatval $price
	 * @return int
	 *
	 */
	public function get_total_points($price) {
		$points = $this->calculate_points($price);
		$points = $this->round_conversion($points);
		return (int)$points;
	}

	/**
	 * Convert price into points
	 *
	 * @since 1.0
	 * @param floatval $price
	 * @return int
	 *
	 */
	public function currency_to_points($price = 0) {
		$points = $price / $this->get_redeem_point_conversions();
		return $this->round_conversion($points);	//or always round up / round up to the nearest int?
	}

	/**
	 * Get total points one has earned for a particular customer
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @return int
	 *
	 */
	public function get_customer_total_points($customer_id = 0) {
		return (int)get_user_meta( $customer_id, 'hp_woo_rewards_points', true );
	}

	/**
	 * Get signup points
	 *
	 * @since 1.0
	 * @return mixed
	 *
	 */
	public function get_signup_points() {
		if ((int)$this->option['signup_points'] > 0)
			return (int)$this->option['signup_points'];

		return false;
	}

	/**
	 * Get review points (points for reviewing 1 product) defined in the admin settings
	 *
	 * @since 1.0.6
	 * @return mixed
	 *
	 */
	public function get_review_points() {
		if ((int)$this->option['review_points'] > 0)
			return (int)$this->option['review_points'];

		return false;
	}

	/**
	 * Add points details for a particular customer
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param boolean $is_reserve return reverse array?
	 * @return array
	 *
	 */
	public function get_customer_points_details($customer_id = 0, $is_reverse = true) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id);
		$purchase_details = get_user_meta($customer_id, 'hp_woo_rewards_points_details', true);

		//error_log('HP-WOO-REWARDS: Get : '.print_r($purchase_details, true));
		if (empty($purchase_details)) $purchase_details = array();
		//oldest entry goes first?
		if ($is_reverse) krsort($purchase_details);

		return $purchase_details;
	}

	/**
	 * Update user metadata for particular customer
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param int $points_to_add points to add / subtract
	 * @return void
	 *
	 */
	protected function update_customer_points($customer_id = 0, $points_to_add = 0) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id. ' Points to add: '.$points_to_add);
		if ($points_to_add <> 0) {
			$total_points = $this->get_customer_total_points($customer_id);
			$total_points = $total_points + $points_to_add;
			//error_log('HP-WOO-REWARDS: set points to: '.$total_points.' for customer: '.$customer_id);
			update_user_meta( $customer_id, 'hp_woo_rewards_points', $total_points );
		}
	}

	/**
	 * Add points details to customer's metadata
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param string $points_string
	 * @param string $note
	 * @param const $log_type
	 * @param int $order_id (optional)
	 * @param int $product_id (optional)
	 * @return boolean
	 *
	 */
	protected function update_customer_points_details($customer_id = 0, $points_string = '', $note, $log_type, $order_id = null, $comment_id = null) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id);

		$points = intval($points_string);
		//
		if ($points <> 0) {
			$purchase_details = $this->get_customer_points_details($customer_id);
			$today_date = current_time( 'timestamp' );
			$detail = array(
				'order_id' 			=> $order_id,
				'comment_id'		=> $comment_id,
				'date'		 		=> $today_date,
				'event'				=> $note,
				'event_type'		=> $log_type,
				'points'			=> $points_string
			);

			//error_log('HP-WOO-REWARDS: Before update purchase details: '.print_r($purchase_details, true));
			array_push($purchase_details, $detail);
			//error_log('HP-WOO-REWARDS: After update purchase details: '.print_r($purchase_details, true));
			update_user_meta( $customer_id, 'hp_woo_rewards_points_details', $purchase_details);
		}		
	}

	/**
	 * Calculate the total points one has earned based on the order_id
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @return int
	 *
	 */
	public function get_total_points_in_order($order_id) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' for order ID: '.$order_id);
		$order = wc_get_order( $order_id );
		//error_log('HP-WOO-REWARDS: Order: '.print_r($order, true));
		//error_log('HP-WOO-REWARDS: Net total: '.floatval($order->get_subtotal()).' Total Discount: '.floatval($order->get_discount_total()));
		$net_total_after_discount = floatval($order->get_subtotal()) - floatval($order->get_discount_total());
		$earned_points = $this->get_total_points($net_total_after_discount);
		//error_log('HP-WOO-REWARDS: total points in this order: '.$earned_points);

		return apply_filters( 'hp_woo_rewards_get_total_points_in_order', $earned_points, $order, $net_total_after_discount );		
	}

	/**
	 * Get the total points one can possibly earn in cart
	 *
	 * @since 1.0
	 * @see  https://github.com/woocommerce/woocommerce/issues/16528
	 * @return int
	 *
	 */
	public function get_total_points_in_cart () {
		$price = WC()->cart->get_cart_contents_total(); //total minus discounts (discount and coupons)
		$fees = WC()->cart->get_fees();
		//error_log('HP-WOO-REWARDS: fees: '.print_r($fees, true));

		$discount_in_fee = 0;

		//get_cart_contents_total is supposed return the total minus all discounts and coupons
		//however fees can be negative so need to check if discount is added in fees (ie. < 0)
		foreach ($fees as $fee) {
			if ($fee->total < 0)
				$discount_in_fee = $discount_in_fee + floatval($fee->total);
		}

		$price = $price + $discount_in_fee;

		//error_log('HP-WOO-REWARDS: Discount in fee: '.$discount_in_fee);
		//error_log('HP-WOO-REWARDS: Total minus all discounts: '.$price);
		$total_points_in_cart = $this->get_total_points($price);
		//error_log('HP-WOO-REWARDS: '.__FUNCTION__.' total points in cart: '.$total_points_in_cart);
		return apply_filters( 'hp_woo_rewards_get_total_points_in_cart', $total_points_in_cart, $price );		
	}

	/**
	 * Check if points already processed for a particular order
	 * return associated points if found, otherwise return false
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @param int $customer_id
	 * @param const $log_type
	 * @return mixed
	 *
	 */
	protected function is_already_processed($order_id, $customer_id, $log_type, $comment_id = null) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' for order ID: '.$order_id. ' comment id: '.$comment_id . ' and customer: '.$customer_id. ' for log type: '.$log_type);
		$points_details = $this->get_customer_points_details($customer_id);
		//error_log('HP-WOO-REWARDS: points details: '.print_r($points_details, true));
		foreach ($points_details as $detail) {
			if ($comment_id > 0) {
				//review type
				if ($detail['event_type'] == $log_type && $detail['comment_id'] == $comment_id) {
					//error_log('HP-WOO-REWARDS: Already processed points: '. $detail['points'].' to customer account');
					return (int)$detail['points'];
				}
			}
			else {
				//other type
				if ( ($order_id == 0 || $detail['order_id'] == $order_id) &&  $detail['event_type'] == $log_type  ) {
					//error_log('HP-WOO-REWARDS: Already processed points: '. $detail['points'].' to customer account');
					return (int)$detail['points'];
				}
			}
		}//foreach

		return false;
	}

	/**
	 * Add reward points for a particular order
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @param int $customer_id
	 * @return void
	 *
	 */
	public function add_reward_points_for_purchase($order_id, $customer_id) {
		//error_log('HP-WOO-REWARDS: calling add_points_for_order() for Order ID: '.$order_id.' Customer ID: '.$customer_id);
		$earned_points = $this->get_total_points_in_order($order_id);
		if ($customer_id) {
			//error_log('HP-WOO-REWARDS: customer ID found: '.$customer_id);
			if ($this->is_already_processed($order_id, $customer_id, self::LOG_TYPE_PURCHASE_REWARDS) === false) {
				//error_log('HP-WOO-REWARDS: preparing to add rewards points to customer: '.$customer_id);
				$this->update_customer_points($customer_id, $earned_points);
				$note = __('{points_label} earned for purchase', 'hostplugin-woocommerce-points-reward');
				$earned_points_string = "+$earned_points";
				$this->update_customer_points_details($customer_id, $earned_points_string, $note, self::LOG_TYPE_PURCHASE_REWARDS, $order_id);
			}//check if points already added to the system
		}//customer id found
	}//function

	/**
	 * Add signup points for customer
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param int $signup_points
	 * @return void
	 *
	 */
	public function add_signup_point_for_customer($customer_id, $signup_points) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id.' Signup points: '.$signup_points);
	    if ((int)$this->option['signup_points'] > 0 && $this->is_already_processed(0, $customer_id, self::LOG_TYPE_SIGNUP_REWARDS) === false) {
				$this->update_customer_points($customer_id, $signup_points);
				$point_string = "+".$signup_points;
				$event = __('{points_label} earned for account signup', 'hostplugin-woocommerce-points-reward');
				$this->update_customer_points_details($customer_id, $point_string, $event, self::LOG_TYPE_SIGNUP_REWARDS, $order_id = null);
	    }
	}

	/**
	 * Add review points for customer
	 *
	 * @since 1.0.6
	 * @param int $customer_id
	 * @param int $review_points
	 * @return void
	 *
	 */
	public function add_review_point_for_customer($customer_id, $comment_id, $review_points) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id.' Review points to add: '.$review_points);

	    if ($review_points > 0 && $this->is_already_processed(0, $customer_id, self::LOG_TYPE_REVIEW_REWARDS, $comment_id) === false) {
			$this->update_customer_points($customer_id, $review_points);
			$point_string = "+".$review_points;

			$event = __('{points_label} earned for reviewing product', 'hostplugin-woocommerce-points-reward');
			$this->update_customer_points_details($customer_id, $point_string, $event, self::LOG_TYPE_REVIEW_REWARDS, $order_id = null, $comment_id);
	    }
	}

	/**
	 * admin (or whoever can update points on admin side) update user points
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param int $new_points points to be set
	 * @param string $update_reason
	 *
	 */
	public function admin_update_customer_points($customer_id, $new_points, $update_reason) {
		if (!is_admin()) return;

		$old_points = $this->get_customer_total_points($customer_id);
		if ($old_points <> $new_points) {
		 $add_points = $new_points - $old_points;
		 $point_string = "".$add_points;
		 if ($add_points > 0) $point_string = "+".$point_string;
		 $point_string = "".$point_string;
		 $current_user = wp_get_current_user();
		 $event = __('{points_label} adjusted by ', 'hostplugin-woocommerce-points-reward').' '.$current_user->user_login;
		 if (strlen($update_reason) > 0) $event .= ' '. __('Note: ', 'hostplugin-woocommerce-points-reward').$update_reason;

		 $this->update_customer_points($customer_id, $add_points);
		 $this->update_customer_points_details($customer_id, $point_string, $event, self::LOG_TYPE_ADJUSTMENT_BY_SELLER, $order_id = null);
		}//point changed
	}

	/**
	 * customer redeem points and use it on purchase
	 *
	 * @since 1.0
	 * @param int $customer_id
	 * @param int $order_id
	 * @param int $deduct_points
	 * @return void
	 *
	 */
	public function redeem_points_on_purchase($customer_id, $order_id, $deduct_points) {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' Customer ID: '.$customer_id.' Order ID: '.$order_id.' Redeem points: '.$deduct_points);

		if ($this->is_already_processed($order_id, $customer_id, self::LOG_TYPE_REDEEM_REWARDS) === false) {
			$this->update_customer_points($this->customer_id, -$deduct_points);			
			$note = __('{points_label} redeemed towards purchase', 'hostplugin-woocommerce-points-reward');
			$earned_points_string = "$deduct_points";
			$this->update_customer_points($customer_id, $deduct_points);
			$this->update_customer_points_details($customer_id, $earned_points_string, $note, self::LOG_TYPE_REDEEM_REWARDS, $order_id);
		}
	}

	/**
	 *
	 * return point label
	 *
	 * @since 1.0
	 * @return string
	 *
	 */
	public function get_point_name() {
		return __('Points', 'hostplugin-woocommerce-points-reward');
	}

	/**
	 *
	 * parse point label, replace {points_label} to the one defined in settings
	 *
	 * @since 1.0
	 * @param string $message to be parsed
	 * @return string
	 *
	 */
	public function parse_point_name($message) {
		//error_log('replacing '.$message.' with '.$this->point_name);
		return str_ireplace("{points_label}", $this->point_name, $message);
	}
}
