jQuery(document).ready(function($) {

	QTags.addButton( 'bold_tag', 'bold', '[bold]', '[/bold]', '', '', 1 );
	QTags.addButton( 'italic_tag', 'italic', '[italic]', '[/italic]', '', '', 2 );
	QTags.addButton( 'red_tag', 'red', '[red]', '[/red]', '', '', 3 );
	QTags.addButton( 'blue_tag', 'blue', '[blue]', '[/blue]', '', '', 4 );
	QTags.addButton( 'brown_tag', 'brown', '[brown]', '[/brown]', '', '', 5 );
	QTags.addButton( 'black_tag', 'black', '[black]', '[/black]', '', '', 6 );
	
	quicktags({
		id: 'hp_woo_rewards_signup_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_review_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_single_product_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_cart_checkout_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_total_points_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_min_purchase_amount_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_guest_reminder_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});
	quicktags({
		id: 'hp_woo_rewards_thankyou_message',
		buttons: 'bold,italic,red,blue,brown,black'
	});

	$('#hp_woo_rewards_signup_points_role').select2();

	$('#hp_woo_rewards_review_points_role').select2();

	$('#hp_woo_reset_settings').on('click', function(event) {
		if (!window.confirm("Are you sure you wish to reset the settings to defaults?")) { 
			event.preventDefault();
		}
	});
});
