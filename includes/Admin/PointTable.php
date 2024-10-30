<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes\Admin;

Use \HPWooRewardsIncludes\Helper;

/**
 * User Point Table on Admin
 */
class PointTable{

  private $points;

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

    $point_class = Helper::get_class_name('\HPWooRewardsIncludes\Points');
    $this->points = new $point_class();
    add_action( 'admin_menu', array($this, 'create_submenu_menu'));
    add_filter( 'manage_users_columns', array($this, 'add_point_column') );
    add_filter( 'manage_users_custom_column', array($this, 'get_point_column_data'), 10, 3 );
    add_action( 'show_user_profile', array($this, 'user_profile_section'), 999 );
    add_action( 'edit_user_profile', array($this, 'user_profile_section'), 999 );
    add_action( 'edit_user_profile_update', array($this, 'update_point_in_user_profile'), 10);
    add_action( 'personal_options_update', array($this, 'update_point_in_user_profile'), 10);
  }

  /**
   * Create Admin Main Menu Page
   *
   * @since 1.0
   * @return void
   *
   */
  public function create_submenu_menu() {
    add_submenu_page('hp_woo_rewards_points_dashboard', __('Points Table', 'hostplugin-woocommerce-points-reward'), __('Customer Points', 'hostplugin-woocommerce-points-reward'), 'administrator', 'hp_woo_rewards_points_table', array($this, 'create_subpage'));
  }//function

  /**
   * Redirect to user page
   *
   * @since 1.0
   * @return void
   *
   */
  public function create_subpage() {
    wp_redirect(admin_url( 'users.php'));
  }

  /**
	 * add extra column 'points' to admin user table
	 *
	 * @since 1.0
   * @param string $column column display name 
	 * @return void
	 *
	 */
  public function add_point_column( $column ) {
      $column['points'] = __('Points', 'hostplugin-woocommerce-points-reward');
      return $column;
  }

  /**
	 * load column 'point' data
	 *
	 * @since 1.0
   * @param string $val
   * @param string $colume_name
   * @param $int user_id  
	 * @return void
	 *
	 */
  public function get_point_column_data( $val, $column_name, $user_id ) {
      switch ($column_name) {
          case 'points' :
              return $this->points->get_customer_total_points($user_id);
              break;
          default:
      }
      return $val;
  }

  /**
	 * call when user-edit.php is loaded, display points section
	 *
	 * @since 1.0
   * @param string $profileuser 
	 * @return void
	 *
	 */
  public function user_profile_section($profileuser) {
    if (is_admin() && current_user_can('administrator')) {      
      require_once HOSTPLUGIN_PLUGIN_PATH . 'templates/user_points_details.php';
    }
  }

  /**
	 * call when save button is clicked on user-edit page. save points to user meta data
	 *
	 * @since 1.0
   * @param int $user_id 
	 * @return void
	 *
	 */
  public function update_point_in_user_profile($user_id) {
    if ( is_admin() && current_user_can('administrator') ) {
      $set_to_points = (int)sanitize_text_field($_POST['hp_woo_reward_points']);
      $update_reason = sanitize_text_field($_POST['hp_woo_reward_points_update_reason']);
      $this->points->admin_update_customer_points($user_id, $set_to_points, $update_reason);
    }//is admin?
  }//function
}
