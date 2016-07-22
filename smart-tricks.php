<?php
/*
Plugin Name: Smart Tricks
Version: 0.2
Author URI: http://mehnur.org/
Plugin URI: http://mehnur.org/
Description:  Alter some loop code to include all custom posts
Author: Mehnoor Tahir
License: GPL2
*/

/* Allow custom postst to show up in the loop */
add_action('pre_get_posts', 'all_post_types' );
function all_post_types()
{
    global $wp_query;
    if(!is_admin())
    {
       $wp_query->fff=1;
        if(!is_page()) 
        {
            $wp_query->set('post_type', array_diff(get_post_types(),array('nav_menu_item','revision','attachment','page')));
        }
        else
        {
            $wp_query->set('post_type', array_diff(get_post_types(),array('nav_menu_item','revision','attachment')));
        }     
    } 
}

/* Set default category for custom post types to post type name, create category if needed */
add_action( 'save_post', 'set_default_cat_for_custom', 100, 2 );
function set_default_cat_for_custom( $post_id, $post ) {
	if( in_array( $post->post_status,array('publish')) ) {
	    if(!in_array($post->post_type, array_diff(get_post_types(),array('page','post','nav_menu_item','revision','attachment'))))
	       return;
	    $cat = get_category_by_slug($post->post_type);
	    if(!$cat) {
	        $id = wp_insert_category(array(
                'cat_name' => ucfirst($post->post_type), 
                'category_nicename' => $post->post_type,
                'category_description' => '',
                'category_parent' => ''
            ));
        }
        $categories = wp_get_post_categories($post_id);
        $slugs=array();
        foreach($categories as $cid)
        {
            $c=get_category($cid);
            if($c->slug!=$post->post_type)
                $slugs[] = $c->slug;
        }
        $slugs[] = $post->post_type;
        wp_set_object_terms($post_id, $slugs, 'category');	    
	}
}