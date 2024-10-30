<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes\Admin;

/**
 * Admin Main Dashboard Page
 */
class Admin {

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

		add_action('admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action('admin_menu', array($this, 'create_admin_menu'));
		add_action('admin_footer', array($this, 'add_footer'));
		add_filter('plugin_action_links_'.HOSTPLUGIN_PLUGIN_BASE, array($this, 'add_plugin_links') );
	}

	/**
	 * add styles and script
	 *
	 * @since 1.0
	 * @param string $hook
	 * @return void
	 *
	 */
	public function enqueue($hook) {
		//load css only on dashboard and settings page
		if ($hook == 'woo-rewards_page_hp_woo_rewards_settings' || $hook == 'toplevel_page_hp_woo_rewards_points_dashboard' || $hook == 'user-edit.php') {
			wp_enqueue_style( 'hp_woo_rewards_style', HOSTPLUGIN_PLUGIN_URL . 'assets/css/admin_style.css', array(), HOSTPLUGIN_WOO_POINTS_VERSION );
		}
		
		if ($hook == 'woo-rewards_page_hp_woo_rewards_settings') {
			wp_enqueue_script( 'hp_woo_rewards_script', HOSTPLUGIN_PLUGIN_URL . 'assets/js/admin_script.js', array('quicktags'), HOSTPLUGIN_WOO_POINTS_VERSION );

			wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
			wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
		}
	}//function

	/**
	 * Create Admin Main Menu Page
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function create_admin_menu() {
		add_menu_page( __('HP Woocommerce Rewards & Points', 'hostplugin-woocommerce-points-rewards'), __('Woo Rewards', 'hostplugin-woocommerce-points-reward'), 'administrator', 'hp_woo_rewards_points_dashboard', array($this, 'create_dashboard_page'), 'dashicons-money', 57 );
	}//function

	public function add_footer() {
		$screen = get_current_screen();
		//error_log($screen->id);
		if ($screen->id == 'toplevel_page_hp_woo_rewards_points_dashboard') {
			echo '<div class="credit">Icons made by <a href="http://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>';
		}
	}

	/**
	 * Add links on plugin page
	 * 
	 * @since  1.0
	 * @param string $links
	 * @return string $links
	 */
	public function add_plugin_links( $links ) {
	   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=hp_woo_rewards_settings') ) .'">'. __('Settings', 'hostplugin-woocommerce-points-reward') .'</a>';
	   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=hp_woo_rewards_points_dashboard') ) .'">'. __('Tutorials', 'hostplugin-woocommerce-points-reward') .'</a>';

	   if (HOSTPLUGIN_WOO_POINTS_LICENSE <> 'Premium') {
	   		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=hp_woo_rewards_points_dashboard&tab=premium') ) .'">'. __('Upgrade to Premium Version', 'hostplugin-woocommerce-points-reward') .'</a>';
	   }
	   
	   return $links;
	}

	/**
	 * Create Dashboar Page (FAQ, Tutorial, How to upgrade etc)
	 *
	 * @since 1.0
	 * @return void
	 *
	 */
	public function create_dashboard_page() {
		require_once HOSTPLUGIN_PLUGIN_PATH . 'templates/admin_dashboard.php';
	}
}//admin
