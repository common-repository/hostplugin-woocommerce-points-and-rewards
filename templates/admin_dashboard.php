<div class="wrap" id="hp_woo_rewards_dashboard">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Hostplugin - Woocommerce Rewards & Points Dashboard', 'hostplugin-woocommerce-points-reward' ); ?></h1>

<?php
function page_tabs( $current = 'first' ) {
    $tabs = array(
        'tutorial'   => __('Tutorial', 'hostplugin-woocommerce-points-reward')
    );

    if (HOSTPLUGIN_WOO_POINTS_LICENSE <> 'Premium') {
    	$tabs['premium'] = __('Premium Version', 'hostplugin-woocommerce-points-reward');
    }
    else {
    	$tabs['support'] = __('Premium Support', 'hostplugin-woocommerce-points-reward');
    }

    $html = '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . $class . '" href="?page=hp_woo_rewards_points_dashboard&tab=' . $tab . '">' . $name . '</a>';
    }
    $html .= '</h2>';
    echo $html;
}

$tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'tutorial';
page_tabs( $tab );

if ( $tab == 'tutorial' ) {
?>
<div class="postbox-container fullsize">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">
				<h3 class="hndle"><span><?php _e('Using Hostplugin - Woocommerce Rewards & Points', 'hostplugin-woocommerce-points-reward'); ?></span></h3>
				<div class="inside vid-container">
					<iframe width="960" height="540" src="https://www.youtube.com/embed/-JQ056f7uAw?VQ=HD720" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}
else if ($tab == 'premium') {
?>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/favourite.svg" class="premium-logo"></li>
		<li>
			<h2><?php _e('Premium Version', 'hostplugin-woocommerce-points-reward')?></h2>
			<?php _e("Our premium version provides more features and functionalities that gives you more control over your WooCommerce store! For a limited time, To get a copy of our premium version simply donate at least US$29.99 and we will email you the plugin zip file within 1 business day (Don't forget to include your domain name, ie. yourdomain.com in the special instruction section to avoid any delay).", 'hostplugin-woocommerce-points-reward'); ?>
			<a href="https://www.hostplugin.com/woo-comparison.php" target="_blank"><?php _e('Please take a look on the feature comparison to see how the Premium Version can reward your loyal customers even more!', 'hostplugin-woocommerce-points-reward')?></a>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="DY6X6XWJFH8MW">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/female-customer.svg" class="premium-logo"></li>
		<li>
			<h2><?php _e('Premium Support', 'hostplugin-woocommerce-points-reward'); ?></h2>
			<?php 

			$lifetime = __('lifetime', 'hostplugin-woocommerce-points-reward');
			printf( __('Premium license entitles you to <strong>%s</strong> update and 6 months premium support. Each installation of the plugin will require a licence key in order for you to receive updates and support.', 'hostplugin-woocommerce-points-reward'), $lifetime ); ?>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/refund.png"></li>
		<li>
			<h2><?php _e('Refund Feature', 'hostplugin-woocommerce-points-reward'); ?></h2>
				<?php _e('- Option to remove points for refunded / cancelled orders', 'hostplugin-woocommerce-points-reward'); ?><br>
				<?php _e('- Option to return redeemed points for refunded / cancelled orders', 'hostplugin-woocommerce-points-reward'); ?>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/review.png"></li>
		<li>
			<h2><?php _e('Product Review', 'hostplugin-woocommerce-points-reward'); ?></h2>
				<?php _e('- Option to set maximum review points one can get', 'hostplugin-woocommerce-points-reward'); ?><br>
				<?php _e('- Option to disable points for reviewing the same product', 'hostplugin-woocommerce-points-reward'); ?><br>
				<?php _e('- who can earn product review points', 'hostplugin-woocommerce-points-reward'); ?><br>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/message.png"></li>
		<li>
			<h2><?php _e('Easy to Customize', 'hostplugin-woocommerce-points-reward'); ?></h2>
			<?php _e('Customize the frontend messages that are shown on the product, cart, checkout & order received page. You can also change the points label so that customers could earn "Coins" or "Bucks" instead of "Points".', 'hostplugin-woocommerce-points-reward'); ?>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/discount-tag.svg" class="premium-logo"></li>
		<li>
			<h2><?php _e('Maximum Points Discount', 'hostplugin-woocommerce-points-reward'); ?></h2>
			<?php _e('Completely control the maximum discount available when redeeming points.', 'hostplugin-woocommerce-points-reward'); ?>
		
	</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/min-purchase-amount.png"></li>
		<li>
			<h2><?php _e('Minimum Purchase Amount', 'hostplugin-woocommerce-points-reward'); ?></h2>
			<?php _e('Ability to set the minimum purchase amount in order to redeem points.', 'hostplugin-woocommerce-points-reward'); ?>
		</li>
	</ul>
</section>

<section>
	<ul>
		<li><img src="<?php echo HOSTPLUGIN_PLUGIN_URL; ?>/assets/images/coupon.png"></li>
		<li>
			<h2><?php _e('Coupon', 'hostplugin-woocommerce-points-reward'); ?></h2>
			<?php _e('- Ability to disable points redemption when using coupons', 'hostplugin-woocommerce-points-reward'); ?><br>
			<?php _e('- Ability to disable customers from earning points if coupons are use', 'hostplugin-woocommerce-points-reward'); ?>
		</li>
	</ul>
</section>

<?php    
}
else if ($tab == 'support') {
?>
<div class="postbox-container fullsize">
	<div class="metabox-holder">
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">
				<h3 class="hndle"><span><?php _e('Premium Support', 'hostplugin-woocommerce-points-reward'); ?></span></h3>
				<div class="inside vid-container">
					<?php _e('Please', 'hostplugin-woocommerce-points-reward'); ?>
					<a href="https://www.hostplugin.com/members/" target="_blank"><?php _e('Click Here', 'hostplugin-woocommerce-points-reward'); ?></a>, <?php _e('login and create a ticket', 'hostplugin-woocommerce-points-reward'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php	
}

?>
	
</div>
<!-- wrap
