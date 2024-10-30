<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes\Admin;

Use \HPWooRewardsIncludes\Helper;

class Settings {
 
	/**
	 * Construct
	 *
	 * A place to add hooks and filters
	 *
	 * @since 1.0
	 *
	 */
	public function __construct() {
		if ( !is_admin() ) {
	        return;
	    }

		add_action('admin_menu', array($this, 'create_submenu_menu'));
		add_action('admin_init', array($this, 'register_settings'));
		add_action('woocommerce_screen_ids', array($this, 'set_screen_id'));
	}

	/**
	 * Create Admin Menu and Sub Page
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function create_submenu_menu() {

		add_submenu_page('hp_woo_rewards_points_dashboard', __('Settings', 'hostplugin-woocommerce-points-reward'), __('Settings', 'hostplugin-woocommerce-points-reward'), 'administrator', 'hp_woo_rewards_settings', array($this, 'create_subpage'));
	}//function

	/**
	 * Callback to display admin settings html
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function create_subpage() {
		require_once HOSTPLUGIN_PLUGIN_PATH . 'templates/admin_settings.php';
	}//function

	/**
	 * Add Woocommerce Screen ID so that tooltips works
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function set_screen_id($screen) {
		$screen[] = 'woo-rewards_page_hp_woo_rewards_settings';
		return $screen;
	}//function

	/**
	 * Call WP Settings API
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function register_settings() {

		$this->check_system_requirement();
		$option = Helper::get_settings_option();

		register_setting('hp_woo_rewards_points_settings_option', 'hp_woo_rewards_points_settings_option', array($this, 'validate_fields'));

		add_settings_section("hp_woo_rewards_points_section", __("General Settings", 'hostplugin-woocommerce-points-reward'), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_section("hp_woo_rewards_points_signup_section", __("Signup Settings", 'hostplugin-woocommerce-points-reward'), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_section("hp_woo_rewards_points_review_section", __("Product Review Settings", 'hostplugin-woocommerce-points-reward'), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_section("hp_woo_rewards_points_refund_section", __("Refund Settings", 'hostplugin-woocommerce-points-reward'), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_section("hp_woo_rewards_points_coupon_section", __("Coupon Settings", 'hostplugin-woocommerce-points-reward'), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_section("hp_woo_rewards_points_message_section", __("Messages", 'hostplugin-woocommerce-points-reward').wc_help_tip('{points}: number of points<br>
			{points_label}: points label', true), "hp_woo_rewards_points_section_callback", "hp_woo_rewards_points_settings");

		add_settings_field("is_system_enabled", __("Enable Reward System: ", 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for' => 'is_system_enabled',
				'value'		=> $option['is_system_enabled']
				)
		);

		add_settings_field("points_name", __("Points Label: ", 'hostplugin-woocommerce-points-reward').wc_help_tip('Points Label you wish to display on the frontend', false), 'hp_woo_rewards_points_text_input', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for' => 'points_name',
				'value'		=> $option['points_name'],
				'premium'	=> true
			)
		);

		add_settings_field("points_earned", __("Points Conversion: ",'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_field_points_conversion', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
				array(
				'dollar_spent' => $option['dollar_spent'],
				'points_earned' => $option['points_earned']
				));

		$round_off_options = array(
			'nearest-integer' => 'Round Up to the Nearest Integer',
			'round-down' => 'Always Round Down',
			'round-up' => 'Always Round Up'
		);
		add_settings_field("round_off", __('Round Off: ','hostplugin-woocommerce-points-reward').wc_help_tip('How points should be rounded', false), 'hp_woo_rewards_points_select', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for'	=> 'round_off',
				'options' 	=> $round_off_options,
				'value'		=>$option['round_off']
			)
		);

		add_settings_field("points_redeem", __("Redeem Points Conversion: ",'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_field_redeem_points', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'redeem_points' => $option['redeem_points'],
				'redeem_dollar' => $option['redeem_dollar']
			));

		add_settings_field("max_points_discount", __("Maximum Points Discount").wc_help_tip('Set the maximum points discount can be redeemed in the cart. Leave blank to disable', false), 'hp_woo_rewards_points_field_max_points_discount', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for' => 'max_points_discount',
				'max_points_discount' => $option['max_points_discount'],
				'max_points_discount_type' => $option['max_points_discount_type'],
				'premium'	=> true)
		);

		add_settings_field("min_purchase_amount", __("Minimum Purchase Amount").wc_help_tip('Set the minimum purchase amount in the shopping cart in order to redeem points. Leave blank to disable', false), 'hp_woo_rewards_points_number_input', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for' => 'min_purchase_amount',
				'value'		=> $option['min_purchase_amount'],
				'money'		=> true,
				'premium'	=> true)
		);		

		add_settings_field("tax_included_in_point_calculation", __("Should Tax included in point calculation?".wc_help_tip('If checked, paying tax can also earn points', false), 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_section',
			array(
				'label_for' => 'tax_included_in_point_calculation',
				'value'		=> $option['tax_included_in_point_calculation'],
				'premium'	=> true
				)
		);

		/**********************************************************************/
		/* Signup Section */
		/**********************************************************************/

		add_settings_field("signup_points", __("Signup Points: ", 'hostplugin-woocommerce-points-reward').wc_help_tip('User will get [x] points when signing up an account. Leave blank to disable', false), 'hp_woo_rewards_points_number_input', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_signup_section',
			array(
				'label_for' => 'signup_points',
				'value'		=> $option['signup_points']
			)
		);

		$role_options = Helper::get_roles(true);
		add_settings_field("signup_points_role", __("Who can earn signup points? ", 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_select', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_signup_section',
			array(
				'label_for' => 'signup_points_role',
				'options'	=> $role_options,
				'multiple'	=> true,
				'id'		=> 'hp_woo_rewards_signup_points_role',
				'value'		=> $option['signup_points_role'],
				'premium'	=> true
			)
		);

		/**********************************************************************/
		/* Review Section */
		/**********************************************************************/

		add_settings_field("review_points", __("Review Points: ", 'hostplugin-woocommerce-points-reward').wc_help_tip('User will get [x] points when reviewing the product. Leave blank to disable', false), 'hp_woo_rewards_points_number_input', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_review_section',
			array(
				'label_for' => 'review_points',
				'value'		=> $option['review_points']
			)
		);

		add_settings_field("max_review_points_for_user", __("Maximum Review Points: ", 'hostplugin-woocommerce-points-reward').wc_help_tip('Maximum Points One user can earn for reviewing products. Leave blank to disable', false), 'hp_woo_rewards_points_number_input', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_review_section',
			array(
				'label_for' => 'max_review_points_for_user',
				'value'		=> $option['max_review_points_for_user'],
				'premium'	=> true
			)
		);

		add_settings_field("user_cannot_earn_points_for_reviewing_same_product", __("User cannot earn points for reviewing the same product?", 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_review_section',
			array(
				'label_for' => 'user_cannot_earn_points_for_reviewing_same_product',
				'value'		=> $option['user_cannot_earn_points_for_reviewing_same_product'],
				'premium'	=> true
				)
		);

		add_settings_field("review_points_role", __("Who can earn review points? ", 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_select', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_review_section',
			array(
				'label_for' => 'review_points_role',
				'options'	=> $role_options,
				'multiple'	=> true,
				'id'		=> 'hp_woo_rewards_review_points_role',
				'value'		=> $option['review_points_role'],
				'premium'	=> true
			)
		);

		/**********************************************************************/
		/* Refund Section */
		/**********************************************************************/

		add_settings_field("is_remove_earned_points_if_refunded", __("Remove earned points for Cancelled / Refunded Order?".wc_help_tip('If checked, previously earned points will be deducted if order is cancelled / refunded. Order status must be set to Cancelled / Refunded in order to trigger this event', false), 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_refund_section',
			array(
				'label_for' => 'is_remove_earned_points_if_refunded',
				'value'		=> $option['is_remove_earned_points_if_refunded'],
				'premium'	=> true
				)
		);

		add_settings_field("is_return_redeemed_points_if_refunded", __("Return Redeemed points for Cancelled / Refunded Order?".wc_help_tip('If checked, redeemed points will be returned to customer if order is cancelled / refunded. Order status must be set to Cancelled / Refunded in order to trigger this event', false), 'hostplugin-woocommerce-points-reward'), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_refund_section',
			array(
				'label_for' => 'is_return_redeemed_points_if_refunded',
				'value'		=> $option['is_return_redeemed_points_if_refunded'],
				'premium'	=> true
				)
		);

		/**********************************************************************/
		/* Coupon Section */
		/**********************************************************************/

		add_settings_field("disable_point_when_using_coupon", __("Can't be combined with other woo coupons", '
			').wc_help_tip('If checked, customer cannot redeem points if coupons are used', false), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_coupon_section',
			array(
				'label_for' => 'disable_point_when_using_coupon',
				'value'		=> $option['disable_point_when_using_coupon'],
				'premium'	=> true
				)
		);

		add_settings_field("no_earn_point_when_using_coupon", __("Can't Earn Points if coupons are used", 'hostplugin-woocommerce-points-reward').wc_help_tip('If checked, customer cannot earn points if coupons are used', false), 'hp_woo_rewards_points_checkbox', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_coupon_section',
			array(
				'label_for' => 'no_earn_point_when_using_coupon',
				'value'		=> $option['no_earn_point_when_using_coupon'],
				'premium'	=> true
				)
		);

		/**********************************************************************/
		/* Message Section */
		/**********************************************************************/

		add_settings_field("signup_message", __("Signup Page message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'signup_message',
				'value'		=> $option['signup_message'],
				'premium'	=> true)
		);

		add_settings_field("review_message", __("Add a Review message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'review_message',
				'value'		=> $option['review_message'],
				'premium'	=> true)
		);

		add_settings_field("single_product_message", __("Product Page Message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'single_product_message',
				'value'		=> $option['single_product_message'],
				'premium'	=> true)
		);
		
		add_settings_field("cart_checkout_message", __("Cart / Checkout Page Message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'cart_checkout_message',
				'value'		=> $option['cart_checkout_message'],
				'premium'	=> true)
		);

		add_settings_field("min_purchase_amount_message", __("Min Purchase Amount Message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'min_purchase_amount_message',
				'value'		=> $option['min_purchase_amount_message'],
				'premium'	=> true)
		);

		add_settings_field("total_points_message", __("Total Points"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'total_points_message',
				'value'		=> $option['total_points_message'],
				'premium'	=> true)
		);

		add_settings_field("guest_reminder_message", __("Guest Reminder").wc_help_tip('This message will show up on the checkout page to remind guest to create / login in order to receive points', false), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'guest_reminder_message',
				'value'		=> $option['guest_reminder_message'],
				'premium'	=> true)
		);

		add_settings_field("thankyou_message", __("Order Received / Thank you message"), 'hp_woo_rewards_points_textarea', 'hp_woo_rewards_points_settings', 'hp_woo_rewards_points_message_section',
			array(
				'label_for' => 'thankyou_message',
				'value'		=> $option['thankyou_message'],
				'premium'	=> true)
		);


	}//function

	/**
	 * Validate Settings fields
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function validate_fields($input) {
		
		$validated = array();
		$defaults = Helper::get_default_settings();

		if (isset($_POST['hp_woo_reset_settings'])) {
			//reset to defaults
			$validated = $defaults;
			return $validated;
		}
		
		$validated['is_system_enabled'] = (isset($input['is_system_enabled']) && !empty($input['is_system_enabled'])) ? 'on' : false;

	    $validated['dollar_spent'] = ((int)$input['dollar_spent'] <= 0) ? 1: (int)$input['dollar_spent'];
	    $validated['points_earned'] = ((int)$input['points_earned'] <= 0) ? 1: (int)$input['points_earned'];
	    $validated['redeem_points'] = ((int)$input['redeem_points'] <= 0) ? 1: (int)$input['redeem_points'];
	    $validated['redeem_dollar'] = ((int)$input['redeem_dollar'] <= 0) ? 1: (int)$input['redeem_dollar'];
	    $validated['signup_points'] = ((int)$input['signup_points'] <= 0) ? '': (int)$input['signup_points'];
	    $validated['review_points'] = ((int)$input['review_points'] <= 0) ? '': (int)$input['review_points'];

	    $validated['min_purchase_amount'] = (int)$input['min_purchase_amount'];
	    if ($validated['min_purchase_amount'] == 0) $validated['min_purchase_amount'] = "";

	    $validated['round_off'] = ($input['round_off'] <> 'nearest-integer' && $input['round_off'] <> 'round-up' && $input['round_off'] <> 'round-down')? 'nearest-integer' : $input['round_off'];
	    
	    $validated['points_name'] = $defaults['points_name'];

	    $validated['max_points_discount'] = (int)$input['max_points_discount'];
	    if ($validated['max_points_discount'] == 0) $validated['max_points_discount'] = "";

	    $validated['max_points_discount_type'] = ($input['max_points_discount_type'] <> 'percentage' && $input['max_points_discount_type'] <> 'currency')? 'currency' : $input['max_points_discount_type'];
	    
	    $validated['disable_point_when_using_coupon'] = (isset($input['disable_point_when_using_coupon']) && !empty($input['disable_point_when_using_coupon'])) ? 'on' : false;
	    
	    $validated['tax_included_in_point_calculation'] = (isset($input['tax_included_in_point_calculation']) && !empty($input['tax_included_in_point_calculation'])) ? 'on' : false;
	    
		$validated['no_earn_point_when_using_coupon'] = (isset($input['no_earn_point_when_using_coupon']) && !empty($input['no_earn_point_when_using_coupon'])) ? 'on' : false;	    

		$validated['is_remove_earned_points_if_refunded'] = (isset($input['is_remove_earned_points_if_refunded']) && !empty($input['is_remove_earned_points_if_refunded'])) ? 'on' : false;

		$validated['is_return_redeemed_points_if_refunded'] = (isset($input['is_return_redeemed_points_if_refunded']) && !empty($input['is_return_redeemed_points_if_refunded'])) ? 'on' : false;

		$validated['signup_message'] = $defaults['signup_message'];
		$validated['review_message'] = $defaults['review_message'];
		$validated['single_product_message'] = $defaults['single_product_message'];
		$validated['cart_checkout_message'] = $defaults['cart_checkout_message'];
		$validated['guest_reminder_message'] = $defaults['guest_reminder_message'];
		$validated['thankyou_message'] = $defaults['thankyou_message'];
		$validated['total_points_message'] = $defaults['total_points_message'];
		$validated['min_purchase_amount_message'] = $defaults['min_purchase_amount_message'];
		$validated['signup_points_role'] = $input['signup_points_role'];

		$validated =  apply_filters( 'hp_woo_rewards_validate_fields', $validated, $input );
		return $validated;
	}

	/**
	 * Check if system meets min requirement, if not display settings error
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	private function check_system_requirement() {

		$error_message = '';
		if (version_compare(PHP_VERSION, '5.3.8', '<')) {
		    $error_message .= __("This plugin requires PHP 5.3.8+", 'hostplugin-woocommerce-points-reward').'<br>';
		}

		if (!class_exists( 'WooCommerce' )) {
			//3.2
			$error_message .= __("This plugin requires the Woocommerce plugin", 'hostplugin-woocommerce-points-reward').'<br>';
		}

		if (!empty($error_message))
			add_settings_error('hp_woo_rewards_points_settings_error','hp_woo_error',$error_message,'error');
	}
}
