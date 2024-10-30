<?php
/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;
Use \HPWooRewardsIncludes\Helper;

class Comments {

  public $points;

  /**
   * Construct
   *
   * A place to add hooks and filters
   *
   * @since 1.0.6
   *
   */
  public function __construct() {
    $point_class = Helper::get_class_name('\HPWooRewardsIncludes\Points');
    $this->points = new $point_class();
    if ($this->points->is_point_system_enabled() == true) {

      add_action('comment_post', array($this, 'prepare_rewards_points_for_comment'), 10, 2 );
      add_action('transition_comment_status', array($this, 'rewards_points_for_comment'),10,3);
    }//show rewards points only if point system is enabled
  }

  public function prepare_rewards_points_for_comment( $comment_ID, $comment_approved ) {

    //error_log('HP-WOO-REWARDS: Calling '.__FUNCTION__);
    if( 1 === $comment_approved ){
      $comment = get_comment( $comment_ID);
      if ($comment)
        $this->rewards_points_for_comment('approved', 'approved', $comment) ;
    }
  }

  /**
   *
   * add reward points for product reviewing (comments)
   *
   * @since 1.0.6
   *
   */
  public function rewards_points_for_comment($new_status, $old_status, $comment) {

    //error_log('HP-WOO-REWARDS: Calling '.__FUNCTION__);
    $points_to_add = $this->can_earn_points_for_comment($new_status, $comment);
    if ($points_to_add > 0)
      $this->points->add_review_point_for_customer($comment->user_id, $comment->comment_ID, $points_to_add);
  }

  /**
   *
   * check if reviewing can earn points, if so return the # of points one can get, return false otherwise
   *
   * @since 1.0.6
   * @param obj $comment
   * @param string $old_status
   * @param string $new_status
   * @return mixed
   *
   */
  public function can_earn_points_for_comment($new_status, $comment) {

    //add points only if 1. user is registered, 2. review points in settings is > 0 & review is approved
    $review_points = (int)$this->points->get_review_points();
    if ($review_points > 0 && $new_status == 'approved' && $comment->user_id > 0) {

      $post_id = $comment->comment_post_ID;
      $product = wc_get_product( $post_id );

      //make sure it's product
      if ($product) {
        //error_log('HP-WOO-REWARDS: Preparing to add '.$review_points.' point(s) to user: '.$comment->user_id);
        return $review_points;

      }//if
    }//if

    return false;

  }//function

  
}
