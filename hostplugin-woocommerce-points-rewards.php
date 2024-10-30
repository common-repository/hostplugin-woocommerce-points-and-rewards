<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */
/*
Plugin Name: Hostplugin - Woocommerce Points and Rewards
Plugin URI:	https://wordpress.org/plugins/hostplugin-woocommerce-points-and-rewards
Description: Reward your loyal customers for purchases and other actions using points which can be redeemed for discounts on future purchase.
Version: 1.1.2
Author: HostPlugin.com
Author URI: http://www.hostplugin.com
Text Domain: hostplugin-woocommerce-points-reward
Domain Path: /languages
License: GPLv2 or later
*/

if (!defined ( 'ABSPATH' )) die('Peacefully');

// Require once for the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use \HPWooRewardsIncludes\Helper;
use \HPWooRewardsIncludes\Admin\Admin;
use \HPWooRewardsIncludes\Admin\PointTable;
use \HPWooRewardsIncludes\Activation;
use \HPWooRewardsIncludes\Deactivation;
use \HPWooRewardsIncludes\Product;
use \HPWooRewardsIncludes\Comments;
use \HPWooRewardsIncludes\Upgrade;

if ( class_exists('Hostplugin_Woocommerce_Points_Rewards') ) {
	die(__('Hostplugin - WooCommerce Points & Rewards is already installed and activated. Please deactivate any other version before you activate this one.', 'hostplugin-woocommerce-points-reward'));
} 

if ( ! class_exists( 'Hostplugin_Woocommerce_Points_Rewards' ) ) {

	class Hostplugin_Woocommerce_Points_Rewards {

		private static $instance;

		public $admin;
		public $admin_settings;
		public $product_page;
		public $cart_page;
		public $admin_point_table;
		public $comments;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function instance() {
			if ( ! isset ( self::$instance ) ) {
				self::$instance = new self;
				self::$instance->setup_constants();

				$cart_class = Helper::get_class_name("\HPWooRewardsIncludes\Cart");
				$settings_class = Helper::get_class_name("\HPWooRewardsIncludes\Admin\Settings");
				$order_class = Helper::get_class_name("\HPWooRewardsIncludes\Order");
				$account_class = Helper::get_class_name("\HPWooRewardsIncludes\Account");
				$comment_class = Helper::get_class_name("\HPWooRewardsIncludes\Comments");
				$product_class = Helper::get_class_name("\HPWooRewardsIncludes\Product");
				
				self::$instance->product_page = new $product_class();
				self::$instance->cart_page = new $cart_class();
				self::$instance->order = new $order_class();
				self::$instance->account = new $account_class();
				self::$instance->comments = new $comment_class();
				self::$instance->admin = new Admin();
				self::$instance->admin_settings = new $settings_class();
				self::$instance->admin_point_table = new PointTable();				
				self::$instance->load_update_checker();
			}

			return self::$instance;
		}

		/**
		 * Setup Constants Variables
		 *
		 * @since 1.0
		 * @return void
		 *
		 */
		private function setup_constants() {
			if ( ! defined( 'HOSTPLUGIN_WOO_POINTS_VERSION' ) ) {
				define( 'HOSTPLUGIN_WOO_POINTS_VERSION', '1.1.2' );
			}

			if ( ! defined( 'HOSTPLUGIN_WOO_POINTS_LICENSE' ) ) {
				define( 'HOSTPLUGIN_WOO_POINTS_LICENSE', '' );
			}

			// ie. /home/domain/public_html/wp-content/plugins/hostplugin-woocommerce-points-rewards
			if ( ! defined( 'HOSTPLUGIN_PLUGIN_PATH' ) ) {
				define( 'HOSTPLUGIN_PLUGIN_PATH', plugin_dir_path( __FILE__) );
			}

			//ie. yourdomain.com/wp-content/plugins/hostplugin-woocommerce-points-rewards
			if ( ! defined( 'HOSTPLUGIN_PLUGIN_URL' ) ) {
				define( 'HOSTPLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			//ie. hostplugin-woocommerce-points-rewards/hostplugin-woocommerce-points-rewards.php
			if ( ! defined( 'HOSTPLUGIN_PLUGIN_BASE' ) ) {
				define( 'HOSTPLUGIN_PLUGIN_BASE', plugin_basename( dirname( __FILE__) ) . '/hostplugin-woocommerce-points-rewards.php' );
			}
			
		}//function

		private function load_update_checker() {
			
		}
	}//class
}

function hp_woo_points_rewards() {
	return Hostplugin_Woocommerce_Points_Rewards::instance();
}
hp_woo_points_rewards();

function hp_woo_points_rewards_activate() {
	hp_woo_flush_rewrite_rules();
	Activation::activate();
}
register_activation_hook( __FILE__, 'hp_woo_points_rewards_activate' );

function hp_woo_points_rewards_deactivate() {
	hp_woo_flush_rewrite_rules();
	Deactivation::deactivate();
}
register_deactivation_hook( __FILE__, 'hp_woo_points_rewards_deactivate' );

function hp_woo_flush_rewrite_rules() {
	add_rewrite_endpoint( 'hp-woo-rewards-points', EP_ROOT | EP_PAGES );
	flush_rewrite_rules();	//flush rewrite rules so that my account, points tab will work
}

function hp_woo_init() {
	hp_woo_points_rewards_upgrade_database();
	add_rewrite_endpoint( 'hp-woo-rewards-points', EP_ROOT | EP_PAGES );
	load_plugin_textdomain( 'hostplugin-woocommerce-points-reward', false,  dirname(plugin_basename(__FILE__)) . '/languages/');
}

function hp_woo_points_rewards_upgrade_database() {	
	Upgrade::upgrade();
}
add_action( 'init', 'hp_woo_init');
add_action( 'after_switch_theme', 'hp_flush_rewrite_rules' );
