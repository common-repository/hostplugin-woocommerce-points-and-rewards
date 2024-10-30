<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

/**
 * Cart & Checkout & Order received page
 */
class Cart {

	public $points;
	protected $customer_id;

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

		if ($this->points->is_point_system_enabled()) {
			add_action( 'init', array($this, 'set_customer_id'));
			add_action( 'woocommerce_before_cart', array($this,'display_total_points'), 0 );
			add_action( 'woocommerce_before_cart', array($this,'display_points_in_account'));
			add_action( 'woocommerce_before_checkout_form', array($this,'display_total_points'), 15 );
			add_action( 'woocommerce_before_checkout_form', array($this,'display_guest_reminder'));
			add_action( 'woocommerce_cart_coupon', array($this, 'add_apply_points_button') );
			add_action( 'woocommerce_cart_calculate_fees', array($this,'apply_point_discount'));
			add_action( 'woocommerce_thankyou_order_received_text', array($this, 'show_points_on_thankyou_page'), 0, 1 );
			add_action( 'wp_enqueue_scripts', array($this, 'add_frontend_script'));
			add_action( 'woocommerce_checkout_process', array($this, 'validate_order'));
		}
	}

	/**
	 * set customer id
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function set_customer_id() {
		//get current user id can only be called inside an action
		//so this has to be removed from __construct
		$this->customer_id = get_current_user_id();
	}

	/**
	 * add front end css and js
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function add_frontend_script() {
		wp_enqueue_script('hp_woo_rewards_client_script', HOSTPLUGIN_PLUGIN_URL. 'assets/js/front_end_script.js', array('jquery'), HOSTPLUGIN_WOO_POINTS_VERSION, true);
		wp_enqueue_style( 'hp_woo_rewards_client_style', HOSTPLUGIN_PLUGIN_URL. 'assets/css/style.css', array(), HOSTPLUGIN_WOO_POINTS_VERSION);
	}

	/**
	 * use session to save the following info:
	 * 1. whether customer wants to use points
	 * 2. the max discount he or she can redeem (based on the cart net amount)
	 * 3. customer's total point in his account
	 * 4. point used (based on the cart net amount)
	 *
	 * @since 1.0
	 * @param float $discount
	 * @param int $customer_total_point
	 * @param int $points_used
	 * @return void
	 *
	 */
	private function set_apply_point_session($discount, $customer_total_point, $points_used) {
		//error_log('HP-WOO-REWARDS: Calling '.__FUNCTION__);
		if (isset( $_POST['hp_woo_apply_point_button'] ) && $_POST['hp_woo_apply_point_button'] == "Apply") {
			//error_log('apply points here');
			WC()->session->set('hp_woo_rewards_enabled', 'true');	//customer wants to use points
		}

		if (isset( $_POST['hp_woo_remove_point_button'] ) && $_POST['hp_woo_remove_point_button'] == "Remove") {
			WC()->session->set('hp_woo_rewards_enabled', null);
		}

		//doesn't matter if customer wants to use points or not, update the session
		//error_log('HP-WOO-REWARDS: customer total points: '.$customer_total_point.' max discount customer can use in cart: '.$discount);
		WC()->session->set('hp_woo_rewards_discount', $discount);
		WC()->session->set('hp_woo_rewards_points_used', $points_used);
		WC()->session->set('hp_woo_rewards_total_points', $customer_total_point);
	}

	/**
	 * triggers when woo needs cart calculation. This function is to apply or remove point discounts
	 *
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function apply_point_discount() {
		//error_log('HP-WOO-REWARDS: Calling '.__FUNCTION__);

		if (is_user_logged_in()) {
			//error_log('HP-WOO-REWARDS: User is logged in and hp_woo_rewards is currently enabled');
			$max_discount = $this->get_max_cart_discount();
			$discount = $this->points->get_point_discount($this->customer_id, $max_discount);

			$customer_total_point = $this->points->get_customer_total_points($this->customer_id);
			$points_used = $this->points->currency_to_points($discount);

			$this->set_apply_point_session($discount, $customer_total_point, $points_used);

			if (WC()->session->get('hp_woo_rewards_enabled') == 'true' && $discount > 0) {
				WC()->cart->add_fee( __('Point Discount', 'hostplugin-woocommerce-points-reward'), -$discount );
			}
		}//customer logged in
	}//function

	/**
	 * display hidden buttons right next to apply coupon button
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function add_apply_points_button() {
		echo '<button type="submit" class="button hide" id="hp_woo_apply_point_button" name="hp_woo_apply_point_button" value="Apply">'.__('Apply ', 'hostplugin-woocommerce-points-reward').$this->points->point_name.'</button>';
		echo '<button type="submit" class="button hide" id="hp_woo_remove_point_button" name="hp_woo_remove_point_button" value="Remove">'.__('Remove ', 'hostplugin-woocommerce-points-reward').$this->points->point_name.'</button>';
	}

	/**
	 * trigger when customer clicks on place order
	 * check if customer really has enough point to be used, if not, throw error
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function validate_order() {
		//error_log('HP-WOO-REWARDS: Calling '.__FUNCTION__);
		if (WC()->session->get('hp_woo_rewards_enabled') == 'true') {
			//error_log('HP-WOO-REWARDS: customer wants to use points');
			$fees = WC()->cart->get_fees();

			//error_log('HP-WOO-REWARDS: Checking all fees: '.print_r($fees, true));
			foreach ($fees as $fee) {
				if ($fee->name == __('Point Discount', 'hostplugin-woocommerce-points-reward')) {
					$discount = abs(floatval($fee->amount));	//convert to +
					$max_discount = $this->get_max_cart_discount();
					$customer_discount = $this->points->get_point_discount($this->customer_id, $max_discount);

					//error_log('HP-WOO-REWARDS: Discount applied on cart is: '.$discount.' Customer max discount: '.$customer_discount);

					if ($discount > $customer_discount) {
						$error_text = __('The maximum discount you can get is: ', 'hostplugin-woocommerce-points-reward'). get_woocommerce_currency_symbol(). ' '.$customer_discount;
						wc_add_notice( $error_text, 'error' );
						return;
					}
				}
			}//for each fees
		}//customer is using points for order
	}

	/**
	 * Display earned points on thank you page
	 *
	 * @since 1.0
	 * @param string $thank_you_msg
	 * @return void
	 *
	 */
	public function show_points_on_thankyou_page($thank_you_msg) {
		$message = Helper::get_settings_option('thankyou_message');

		$values = array(
	      'points_label'  => $this->points->point_name
	    );

		$message = Helper::parse_customized_message($message, $values);
		$thank_you_msg .= ' '.$message;
		return $thank_you_msg;
	}

	/**
	 * Check if customer is logged in, if not, display a message to remind customer to
	 * create account / login in order to earn points
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function display_guest_reminder() {
		if (!is_user_logged_in()) {
			$message = Helper::get_settings_option('guest_reminder_message');

			$values = array(
		      'points_label'  => $this->points->point_name
		    );

			$message = Helper::parse_customized_message($message, $values);				
			echo '<div class="woocommerce-info" role="alert">';
			echo $message;
			echo '</div>';
		}
	}

	/**
	 * Check if customer is logged in, if not, display a message to remind customer to
	 * create account / login in order to earn points
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function display_points_in_account() {
		if (is_user_logged_in()) {
			$this->customer_id = get_current_user_id();
			$points = $this->points->get_customer_total_points($this->customer_id);
			if ($points > 0) {
				$point_name = $this->points->point_name;
				$message = Helper::get_settings_option('total_points_message');

				$values = array(
			      'points_label'  => $this->points->point_name,
			      'points'        => $points
			    );

				$message = Helper::parse_customized_message($message, $values);
				echo '<div class="woocommerce-info" role="alert"><span>';
				echo $message;
				echo('</span>');
				if (WC()->session->get('hp_woo_rewards_enabled') == 'true') {
					echo(' <a id="hp_woo_rewards_remove_points" class="button" href="#">'.__('Remove ', 'hostplugin-woocommerce-points-reward'). $point_name.'</a>');
				}
				else {
					echo('<a id="hp_woo_rewards_apply_points" class="button" href="#">'.__('Apply ', 'hostplugin-woocommerce-points-reward'). $point_name.'</a>');
				}
				
				echo('</span></div>');
			}
		}//customer has points to spend
		else {
			WC()->session->set('hp_woo_rewards_enabled', null);
			WC()->session->set('hp_woo_rewards_discount', null);	
			WC()->session->set('hp_woo_rewards_points_used', null);	
			WC()->session->set('hp_woo_rewards_total_points', null);
		}
	}

	/**
	 * Display how many points customer will earn, for all products in cart
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function display_total_points() {
		//show even if customer is not logged in
		
		$total_points =$this->points->get_total_points_in_cart();
		//display only if points you can earn is more than 0
		if ($total_points > 0) {
			$message = Helper::get_settings_option('cart_checkout_message');
			$values = array(
		      'points_label'  => $this->points->point_name,
		      'points'        => $total_points
		    );

			$message =Helper::parse_customized_message($message, $values);
			echo '<div class="woocommerce-info" role="alert">';
			echo $message;
		  	echo '</div>';
		}
	}

	/**
	 * Get the maximum discount based on items in cart
	 *
	 * @since 1.0
	 * @return float
	 *
	 */
	protected function get_max_cart_discount() {
		$max_discount = WC()->cart->get_cart_contents_total(); //total in cart minus all discounts
		return round($max_discount, 2);
	}
}
