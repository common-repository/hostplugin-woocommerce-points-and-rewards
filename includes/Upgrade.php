<?php

/**
 * @package  hostpluginWoocommercePointsRewards
 */

namespace HPWooRewardsIncludes;

Use \HPWooRewardsIncludes\Helper;

class Upgrade {

	/**
	 *
	 * upgrade database
	 *
	 * @since 1.0.3
	 * @return void
	 *
	 */
	public static function upgrade() {
		//compare version in database against const version
		//if lower, upgrade database

		$db_version = get_option( 'hp_woo_rewards_points_version' );

		//if the database version is less than const version (ie. db not updated)
		
		if (version_compare($db_version, HOSTPLUGIN_WOO_POINTS_VERSION, '<') ) {
		    //error_log('HP-WOO-REWARDS: Database version is '.$db_version.' Need to upgrade to '.HOSTPLUGIN_WOO_POINTS_VERSION);

		    //if database version is less than 1.0.3
		    if (version_compare($db_version, '1.0.3', '<')) {
		    	self::update_database_1_0_3();
		    }

		    //if database version is less than 1.0.6
		    if (version_compare($db_version, '1.0.6', '<')) {
		    	self::update_database_1_0_6();
		    }

		    //if database version is less than 1.1.1
		    if (version_compare($db_version, '1.1.1', '<')) {
		    	self::update_database_1_1_1();
		    }

		    update_option( 'hp_woo_rewards_points_version', HOSTPLUGIN_WOO_POINTS_VERSION );
		}
	}

	/**
	 * upgrade database for v1.0.3
	 * @return void
	 */
	public static function update_database_1_0_3() {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__);
		$database_option = get_option( 'hp_woo_rewards_points_settings_option' );
		//error_log('HP-WOO-REWARDS: Before update: '.print_r($database_option, true));
		
		if (isset($database_option['signup_points_role']) && !empty($database_option['signup_points_role'])) {
			;
		}
		else {
			$database_option['signup_points_role'] = Helper::get_default_settings('signup_points_role');
			update_option( 'hp_woo_rewards_points_settings_option', $database_option );
			//error_log('HP-WOO-REWARDS: After update: '.print_r($database_option, true));
		}
	}

	/**
	 * upgrade database for v1.0.6
	 * @return void
	 */
	public static function update_database_1_0_6() {
		//error_log('HP-WOO-REWARDS: calling '.__FUNCTION__);
		$database_option = get_option( 'hp_woo_rewards_points_settings_option' );
		//error_log('HP-WOO-REWARDS: Before update: '.print_r($database_option, true));

		if (isset($database_option['review_points']) && !empty($database_option['review_points'])) {
			;
		}
		else {
			$database_option['review_points'] = Helper::get_default_settings('review_points');			
		}

		if (isset($database_option['review_points_role']) && !empty($database_option['review_points_role'])) {
			;
		}
		else {
			$database_option['review_points_role'] = Helper::get_default_settings('review_points_role');
		}

		if (isset($database_option['max_review_points_for_user']) && !empty($database_option['max_review_points_for_user'])) {
			;
		}
		else {
			$database_option['max_review_points_for_user'] = Helper::get_default_settings('max_review_points_for_user');
		}

		if (isset($database_option['user_cannot_earn_points_for_reviewing_same_product']) && !empty($database_option['user_cannot_earn_points_for_reviewing_same_product'])) {
			;
		}
		else {
			$database_option['user_cannot_earn_points_for_reviewing_same_product'] = Helper::get_default_settings('user_cannot_earn_points_for_reviewing_same_product');
		}

		//error_log('HP-WOO-REWARDS: After update: '.print_r($database_option, true));
		update_option( 'hp_woo_rewards_points_settings_option', $database_option );
	}

	/**
	 * upgrade database for v1.1.1
	 * @return void
	 */
	public static function update_database_1_1_1() {		
		$database_option = get_option( 'hp_woo_rewards_points_settings_option' );
		
		if (isset($database_option['tax_included_in_point_calculation']) && !empty($database_option['tax_included_in_point_calculation'])) {
			;
		}
		else {
			$database_option['tax_included_in_point_calculation'] = Helper::get_default_settings('tax_included_in_point_calculation');
			update_option( 'hp_woo_rewards_points_settings_option', $database_option );			
		}
	}
}