<?php



function create_evipnet_post_type() {
	register_post_type( 'evipnet',
		array(
       'labels' => array(
                'name' => __( 'EVIPNet Metadata' ),
                'singular_name' => __( 'Metadata' ),
                'add_new' => __( 'Add New Metadata' ),
                'add_new_item' => __( 'Add New Metadata' ),
                'edit_item' => __( 'Edit Metadata' ),
                'new_item' => __( 'Add New Metadata' ),
                'view_item' => __( 'View Metadata' ),
                'search_items' => __( 'Search Metadata' ),
                'not_found' => __( 'No metadata found' ),
                'not_found_in_trash' => __( 'No metadata found in trash' )
            ),
			'public' => true,
            'show_ui' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'metadata'),
            'supports' => array('title'),
            'menu_position' => 5,
            'capability_type' => 'post',
            'register_meta_box_cb' => 'add_evipnet_metaboxes'
		)
	);

    register_taxonomy( 'evipnet_keywords', 
                       'evipnet', 
                        array( 
                            'hierarchical' => false, 
                            'label' => 'Keywords', 
                            'query_var' => false, 
                            'rewrite' => false 
                        ) 
                    );  

    register_taxonomy( 'evipnet_countries', 
                       'evipnet', 
                        array( 
                            'hierarchical' => true, 
                            'label' => 'Countries', 
                            'query_var' => false, 
                            'rewrite' => false 
                        ) 
                    );  

    register_taxonomy( 'evipnet_language', 
                       'evipnet', 
                        array( 
                            'hierarchical' => true, 
                            'label' => 'Languages', 
                            'query_var' => false, 
                            'rewrite' => false 
                        ) 
                    );  

    register_taxonomy( 'evipnet_topics', 
                       'evipnet', 
                        array( 
                            'hierarchical' => true, 
                            'label' => 'Topics', 
                            'query_var' => false, 
                            'rewrite' => false 
                        ) 
                    );  

    register_taxonomy( 'type_of_evidence', 
                       'evipnet', 
                        array( 
                            'hierarchical' => true, 
                            'label' => 'Type of evidence', 
                            'query_var' => true, 
                            'rewrite' => true 
                        ) 
                    );  
}

/* Adds a box to the main column on the Post and Page edit screens */


$meta_fields[] = array( "name" => "Author",
                        "desc" => "Document author",
                        "id" => "evipnet_author",
                        "type" => "text");

$meta_fields[] = array( "name" => "Abstract",
                        "desc" => "Document abstract",
                        "id" => "evipnet_abstract",
                        "type" => "textarea");

$meta_fields[] = array( "name" => "Date",
                        "desc" => "Publish date",
                        "id" => "evipnet_date",
                        "type" => "text");

$meta_fields[] = array( "name" => "Journal",
                        "desc" => "Journal",
                        "id" => "evipnet_journal",
                        "type" => "text");

$meta_fields[] = array( "name" => "Page numbers",
                        "desc" => "Page numbers",
                        "id" => "evipnet_page",
                        "type" => "text");


function add_evipnet_metaboxes() {
    
    //remove_meta_box( 'type_of_evidencediv' , 'evipnet' , 'side' );
    add_meta_box( 'evipnet_bibliographic', 'Bibliographic information', 
                                                    'evipnet_inner_custom_box', 'evipnet' ,'normal', 'high');

}

/* Prints the box content */
function evipnet_inner_custom_box() {
    global $post, $meta_fields;
    
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="evipnet_noncename" id="evipnet_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

    echo '<div id="postcustomstuff">';
    echo '    <table id="newmeta">';
    
    foreach ($meta_fields as $meta){
          evipnet_print_metafield($meta);  
    }

    echo '    </table>';
    echo '</div>';
    
    // Get the location data if its already been entered
    $meta['author'] = get_post_meta($post->ID, '_meta_author', true);
    $meta['abstract'] = get_post_meta($post->ID, '_meta_abstract', true);

}

function evipnet_print_metafield($meta){
    global $post;
    
    $field_value = get_post_meta($post->ID, $meta['id'], true);
    
    echo '<tr><td id="newmetaleft" class="left"><strong>' . $meta['name'] .':</strong></td>';
    
    switch ($meta['type']){
        case 'text':
            echo '<td><input type="text" name="' . $meta['id']  .'" value="' . $field_value . '" style="width: 95%;" /></td>';
            break;
        case 'textarea':
            echo '<td><textarea id="' . $meta['id'] . '" name="'. $meta['id'] . '" rows="5"  tabindex="8">' . $field_value . ' </textarea></td>';
            break;

    }    
    echo '</tr>';
    
}



// Save the Metabox Data 
function save_evipnet_meta($post_id, $post) {
    global $post, $meta_fields;
 
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['evipnet_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
    }
 
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
 
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.    
    foreach ($meta_fields as $meta){    
        $id = $meta['id'];
        $evipnet_meta[$id] = $_POST[$id];
    }    

    // Add values of $events_meta as custom fields
 
    foreach ($evipnet_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
 
}

 
function evipnet_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Title",
    "type_of_evidence" => "Type of evidence",
  );
 
  return $columns;
}
function evipnet_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "description":
      the_excerpt();
      break;
    case "type_of_evidence":
      echo get_the_term_list($post->ID, 'type_of_evidence', '', ', ','');
      break;
  }
}
 
add_action('init', 'create_evipnet_post_type');
add_action('save_post', 'save_evipnet_meta', 1, 2); // save the custom fields
add_action('manage_posts_custom_column',  'evipnet_custom_columns');

add_filter('manage_edit-evipnet_columns', 'evipnet_edit_columns');


?>
