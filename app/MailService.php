<?php
/**
 * Created by PhpStorm.
 * User: eaveryanau
 * Date: 10/13/17
 * Time: 4:07 PM
 */

//function my_project_updated_send_email( $post_id ) {
//
//	// If this is just a revision, don't send the email.
//	if ( wp_is_post_revision( $post_id ) )
//		return;
//
//	$post_title = get_the_title( $post_id );
//	$post_url = get_permalink( $post_id );
//	$subject = 'A post has been updated';
//
//	$message = "A post has been updated on your website:\n\n";
//	$message .= $post_title . ": " . $post_url;
//
//	die('2222');
//	// Send email to admin.
//	wp_mail( 'admin@example.com', $subject, $message );
//}
//add_action( 'save_post', 'my_project_updated_send_email' );



//add_filter( 'wp_insert_post_data' , 'filter_post_data' , '99', 2 );
//
//function filter_post_data( $data , $postarr ) {
//	// Change post title
//
//
//	// TODO check old attributes (get product) and postform and send email if it different
//	// check post_type, and the same doing for orders
//	$product         = new WC_Product( $postarr['post_ID']);
//	var_dump($product);die();
//
//	$data['post_title'] .= '_suffix';
//	var_dump($postarr);
//	die('11');
//	return $data;
//}