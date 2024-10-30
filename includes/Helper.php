<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

class Helper {

	/**
	 * $default_settings array
	 * @var array
	 */
	public static $default_settings = null;


	/**
	 * Set default settings
	 * Otherwise return the whole array
	 *
	 * @since 1.1.0
	 * @return array
	 *
	 */
	public static function set_default_settings() {
		self::$default_settings = array(
		'is_system_enabled' 				=> 'on',
		'points_name'						=> 'Points',
		'dollar_spent'						=> '1',
		'points_earned'						=> '1',
		'redeem_points'						=> '100',
		'redeem_dollar'						=> '1',
		'signup_points'						=> '50',
		'review_points'						=> '10',
		'max_review_points_for_user'		=> '',
		'min_purchase_amount'				=> '10',
		'max_points_discount'				=> '',
		'max_points_discount_type' 			=> 'percentage',
		'is_sales_enabled'					=> '',
		'is_remove_earned_points_if_refunded' => '',
		'is_return_redeemed_points_if_refunded' => '',
		'round_off'							=> 'nearest-integer',
		'signup_points_role'				=> array('customer'),
		'review_points_role'				=> array('customer'),
		'user_cannot_earn_points_for_reviewing_same_product' 	=> 'on',
		'disable_point_when_using_coupon' 	=> 'on',
		'tax_included_in_point_calculation' => '',
		'no_earn_point_when_using_coupon' 	=> 'on',
		'signup_message'					=> __('Sign up now and receive [bold]{points} {points_label}[/bold]', 'hostplugin-woocommerce-points-reward'),
		'review_message'					=> __('Add your review and receive [bold]{points} {points_label}[/bold]', 'hostplugin-woocommerce-points-reward'),
		'single_product_message'			=> __('Purchase this product and earn [bold]{points} {points_label}[/bold]', 'hostplugin-woocommerce-points-reward'),
		'cart_checkout_message'				=> __('Complete your order and earn [bold]{points} {points_label}[/bold] for a discount on a future purchase', 'hostplugin-woocommerce-points-reward'),
		'guest_reminder_message'			=> __('Please create an account or login to your existing account to receive your {points_label}', 'hostplugin-woocommerce-points-reward'),
		'thankyou_message'					=> __('We will add {points_label} to your account once the order is completed.', 'hostplugin-woocommerce-points-reward'),
		'total_points_message'				=> __('You have {points} {points_label} in your account', 'hostplugin-woocommerce-points-reward'),
		'min_purchase_amount_message' 		=> __('You need to add {money} product(s) to your order to redeem your {points_label}', 'hostplugin-woocommerce-points-reward')
		);
	}

	/**
	 * Get option values from database, return specific option if $option_name is specified
	 * Otherwise return the whole array
	 *
	 * @since 1.0
	 * @param string $option_name optional
	 * @return mixed
	 *
	 */
	public static function get_settings_option($option_name = '') {
		$option = get_option('hp_woo_rewards_points_settings_option');
		if (!empty($option_name)) {
			$return_value = '';
			if (isset($option) && !empty($option[$option_name])) {
				if (is_array($option[$option_name]))
					$return_value = $option[$option_name];
				else
					$return_value = sanitize_text_field($option[$option_name]);
			}
			return $return_value;
		}

		return $option;
	}

	/**
	 * Get class name
	 *
	 * @since 1.0
	 * @param string $base_name
	 * @return string
	 *
	 */
	public static function get_class_name($base_name) {
		$class_name = $base_name.HOSTPLUGIN_WOO_POINTS_LICENSE;
		return $class_name;
	}

	/**
	 * Get default settings
	 *
	 * @since 1.0
	 * @return mixed
	 *
	 */
	public static function get_default_settings($option_name = '') {

		if (self::$default_settings == null)
			self::set_default_settings();

		if (!empty($option_name)) {
			return self::$default_settings[$option_name];
		}
		return self::$default_settings;
	}

	/**
	 * parse customized message set in admin settings
	 *
	 * @since 1.0
	 * @param array $args
	 * @param string $point # of points, can be range of points
	 * @return string
	 *
	 */
	public static function parse_customized_message($string, $args = array()) {

		foreach ($args as $key => $value) {
			$key = '{'.$key.'}';
			$string = str_ireplace($key, $value, $string);
		}

		$string = str_ireplace("[bold]", '<b>', $string);
		$string = str_ireplace("[/bold]", '</b>', $string);
		$string = str_ireplace("[italic]", '<i>', $string);
		$string = str_ireplace("[/italic]", '</i>', $string);
		$string = str_ireplace("[red]", '<span class="hp_woo_color_red">', $string);
		$string = str_ireplace("[/red]", '</span>', $string);
		$string = str_ireplace("[blue]", '<span class="hp_woo_color_blue">', $string);
		$string = str_ireplace("[/blue]", '</span>', $string);
		$string = str_ireplace("[brown]", '<span class="hp_woo_color_brown">', $string);
		$string = str_ireplace("[/brown]", '</span>', $string);
		$string = str_ireplace("[black]", '<span class="hp_woo_color_black">', $string);
		$string = str_ireplace("[/black]", '</span>', $string);

		return $string;
	}

	/**
	 * get all roles and return as an array
	 * @since  1.0.3
	 * @param  boolean $all
	 * @return array
	 */
	public static function get_roles($all = false) {
		$roles = get_editable_roles();
		$role_options = array();
		if ($all) $role_options['all'] = 'All';

		foreach ($roles as $key => $value) {
			$role_options[$key] = $value['name'];
		}

		return $role_options;
	}
}
