<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;
Use \HPWooRewardsIncludes\Helper;

/**
 * Add Points tab to My-account Woocommerce Customer Page
 */
class Account {

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
      add_filter( 'query_vars', array($this, 'add_query_vars'), 0 );
      add_filter( 'woocommerce_account_menu_items', array($this, 'add_link') );
      add_action( 'woocommerce_account_hp-woo-rewards-points_endpoint', array($this, 'add_reward_content'), 999 );
      add_action( 'wp_enqueue_scripts', array($this, 'add_frontend_script'), 999);
      add_action( 'woocommerce_register_form_start', array($this, 'display_signup_points'), 10, 1 );  
      add_action( 'user_register', array($this, 'add_signup_points'), 10, 1 );
      add_filter( 'woocommerce_product_review_comment_form_args',array($this,'display_review_points_message'),10,1);
    }//show rewards points only if point system is enabled
  }

  /**
   * add front end css
   *
   * @since 1.0
   * @return void
   *
   */
  public function add_frontend_script() {
    wp_enqueue_style( 'hp_woo_rewards_client_style', HOSTPLUGIN_PLUGIN_URL. 'assets/css/style.css', array(), HOSTPLUGIN_WOO_POINTS_VERSION);
    if (!wp_style_is( 'dashicons', 'enqueued' )) {
      wp_enqueue_style( 'dashicons' );
    } 
  }

  /**
   * add query vars
   *
   * @since 1.0
   * @param array $vars array contains my-account slugs
   * @return void
   *
   */
  public function add_query_vars( $vars ) {
    $vars[] = 'hp-woo-rewards-points';
    return $vars;
  }

  /**
   * Insert reward links before the last menu item (last one should be logout)
   *
   * @since 1.0
   * @param array $items
   * @return void
   *
   */
  public function add_link( $items ) {
    $point_name = $this->points->point_name;
    // Remove the logout menu item.
    $logout = $items['customer-logout'];
    unset( $items['customer-logout'] );

    // Insert your custom endpoint.
    $items['hp-woo-rewards-points'] = $point_name;

    // Insert back the logout item.
    $items['customer-logout'] = $logout;

    return $items;
  }

  /**
   * Assign reward points to user after signing up account
   *
   * @since 1.0
   * @param int $user_id
   * @return void
   *
   */
  public function add_signup_points($user_id) {
    $this->points->add_signup_point_for_customer($user_id, (int)Helper::get_settings_option('signup_points'));
  }

  /**
   * Display review points message
   *
   * @since 1.0.6
   * @param obj $comment_data
   * @return obj $comment_data
   *
   */
  public function display_review_points_message($comment_data) {

    //error_log('HP-WOO-REWARDS: calling '.__FUNCTION__.' for comment: '.print_r($comment_data, true));

    $review_points = $this->points->get_review_points();

    $message = Helper::get_settings_option('review_message');
    $values = array(
      'points_label'  => $this->points->point_name,
      'points'        => $review_points
    );
    $message = Helper::parse_customized_message($message, $values);

    $html = '';
    if ($review_points > 0 && $comment_data && $comment_data['title_reply_before']) {
      $html .= '<div class="woocommerce-message" role="alert">';
      $html .= $message;
      $html .= '</div>';
      $comment_data['title_reply_before'] = $html . $comment_data['title_reply_before'];
    }

    return $comment_data;
  }
  

  /**
   * Display how many points customer will earn when signing up a new account
   *
   * @since 1.0
   * @return void
   *
   */
  public function display_signup_points() {
    $signup_points = $this->points->get_signup_points();
    $message = Helper::get_settings_option('signup_message');
    $values = array(
      'points_label'  => $this->points->point_name,
      'points'        => $signup_points
    );
    $message = Helper::parse_customized_message($message, $values);

    if ($signup_points > 0) {
      echo '<div class="woocommerce-message" role="alert">';
      echo $message;
      echo '</div>';
    }
  }

  /**
   * load my account reward html content
   *
   * @since 1.0
   * @return void
   *
   */
  public function add_reward_content() {
    require_once HOSTPLUGIN_PLUGIN_PATH . 'templates/my_account.php';
  }
}
