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
                'not_found_in_trash' => __( 'No metadata found in trash' ),
                'menu_name' => 'EVIPNet',
            ),
			'public' => true,
            'show_ui' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'metadata'),
            'supports' => array('title', 'author'),
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


$meta_fields[] = array( "name" => "Author/Creator",
                        "desc" => "Examples of a creator include a person or an organisation. Use Surname, Name. Ex. Duncan, Phyllis-Anne",
                        "help_url " => "http://dublincore.org/documents/2000/07/16/usageguide/sectc.shtml#creator",
                        "id" => "_evipnet_dc_contribuitor_author",
                        "repeatable" => true,
                        "type" => "text");

$meta_fields[] = array( "name" => "Abstract",
                        "desc" => "Abstract or summary",
                        "help_url" => "http://dublincore.org/documents/2000/07/16/usageguide/sectb.shtml#description",
                        "id" => "_evipnet_dc_description_abstract",
                        "type" => "textarea");

$meta_fields[] = array( "name" => "Source",
                        "desc" => "The present resource may be derived from the Source resource in whole or part. Ex. Library and Information Science Research; Shakespeare's Romeo and Juliet",
                        "id" => "_evipnet_dc_source",
                        "type" => "text");

$meta_fields[] = array( "name" => "Volume and Issue",
                        "desc" => "Referent volume number and issue. Ex. Use 22(3) for Volume 22 Issue 3.",
                        "id" => "_evipnet_volume_issue",
                        "type" => "text");

$meta_fields[] = array( "name" => "Page numbers",
                        "desc" => "Start and end page. Ex. 311-338",
                        "id" => "_evipnet_pages",
                        "type" => "text");

$meta_fields[] = array( "name" => "Date",
                        "desc" => "Date of publication or distribution. Use  month and year (YYYY-MM) or just year (YYYY). Ex. 201103",
                        "help_url" => "http://dublincore.org/documents/2000/07/16/usageguide/sectd.shtml#date",
                        "id" => "_evipnet_dc_date_issued",
                        "type" => "text");

$meta_fields[] = array( "name" => "Publisher",
                        "desc" => "Entity responsible for publication, distribution, or imprint. Ex. University of Miami. Dept. of Economics",
                        "id" => "_evipnet_dc_publisher",
                        "type" => "text");

$meta_fields[] = array( "name" => "Full text URL",
                        "desc" => "Link to full text document. Ex. http://www.ncbi.nlm.nih.gov/pmc/articles/PMC2568870/",
                        "id" => "_evipnet_fulltext_url",
                        "type" => "text");

$meta_fields[] = array( "name" => "Documento",
                        "desc" => "Documento",
                        "id" => "_evipnet_fulltext_file",
                        "type" => "file");


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
   
    echo '<div class="evip-metabox-field-group">';
    
    foreach ($meta_fields as $field){
          evipnet_print_metafield($field);  
    }

    echo '</div>';
    
    // Get the location data if its already been entered
    $meta['author'] = get_post_meta($post->ID, '_meta_author', true);
    $meta['abstract'] = get_post_meta($post->ID, '_meta_abstract', true);

}

function evipnet_print_metafield($field){
    global $post;
    
    $field_id = $field["id"];
    $field_name = $field["name"];
    $field_type = $field["type"];
    $field_description = $field["desc"];
    $field_repeatable = $field["repeatable"];
    
    if ($field_repeatable == true){    
        $field_value = get_post_custom_values($field_id, $post->ID);
    }else{            
        $field_value = get_post_meta($post->ID, $field_id, true);
    }
print_r($field_value);

    echo '<div class="evip-metabox-field">';

    switch ($field_type){
        case 'text':
            echo '    <div class="evip-metabox-field-col1">';
            echo '        <label for="' . $field_id . '">'. $field_name .'</label>';
            echo '        <p class="howto">' . $field_description . '</p>';
            echo '    </div>';
            echo '    <div class="evip-metabox-field-col2" id="' . $field_id .'">';
            
            if ($field_repeatable == true){
                echo '<input class="text" name="'. $field_id . '[]" id="' . $field_id . '" value="' . $field_value[0] . '">';
                echo '<input type="button" class="addButton" value="add +"/>';
                if (count($field_value) > 1){
                    $count_item = 0;
                    foreach ($field_value as $item_value){
                            $count_item++;
                            if ($count_item > 1 && $item_value != '') 
                                echo '<input class="text" name="'. $field_id . '[]" id="' . $field_id . '" value="' . $item_value . '">';
                    }
                }
            }else{
                echo '<input class="text" name="'. $field_id . '" id="' . $field_id . '" value="' . $field_value . '">';
            }            
            echo '    </div>';
            break;
        case 'textarea':
            echo '     <div class="evip-metabox-field-col1">';
            echo '         <label for="' . $field_id . '">'. $field_name .'</label>';
            echo '     </div>';

            echo '     <div class="evip-metabox-field-col2">';            
            echo '        <textarea id="' . $field_id . '" name="'. $field_id . '" rows="5">' . $field_value . '</textarea>';
            echo '     </div>';
            break;
        case 'file':
            $attachment_id = (int) $field_value;

            $file_html = "";
            $file_name = "";
            if ($attachment_id) {
                $file_thumbnail = wp_get_attachment_image_src( $attachment_id, 'thumbnail', true );
                $file_thumbnail = $file_thumbnail[0];
                $file_html = "<img src='$file_thumbnail' alt='' />";
                $file_post = get_post($attachment_id);
                $file_name = esc_html($file_post->post_title);
            }
        
            echo '<div class="simple-fields-metabox-field">';
            echo '   <div class="simple-fields-metabox-field-file"><label>' . $field_name . '</label>';
            echo '      <div class="simple-fields-metabox-field-file-col1">';
            echo '      <div class="simple-fields-metabox-field-file-selected-image">' . $file_html  . '</div>';
            echo '   </div>';
        
            echo '   <div class="simple-fields-metabox-field-file-col2">';
            echo '      <input type="hidden" name="' . $field_id .'" id="'. $field_id .'" value="' . $field_value .'" />';
            echo '      <div class="simple-fields-metabox-field-file-selected-image-name">' . $file_name . '</div>';
            echo '          <a class="thickbox simple-fields-metabox-field-file-select" href="media-upload.php?simple_fields_dummy=1&simple_fields_action=select_file&simple_fields_file_field_unique_id=' . $field_id .'&post_id=-1&TB_iframe=true&width=640&height=426">Select file</a> | <a href="#" class="simple-fields-metabox-field-file-clear">Clear</a>';
            echo '      </div>';
            echo '   </div>';
            echo '</div>';
            break;


    }    
    echo '</div>';
    
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


    // Add values of $evipnet_meta as custom fields
 
    foreach ($evipnet_meta as $key => $value) { // Cycle through the $evipnet_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        
        //$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)

        // treatment for repeatable fields
        if (is_array($value)){
            // delete previous version of all occurences of meta field
            $repeatable_values = get_post_custom_values($key, $post->ID);
            foreach ( $repeatable_values as $old_rep_value ){
                delete_post_meta($post->ID, $key, $old_rep_value);
            }    
            // add new values for meta field
            foreach ($value as $new_rep_value){
                add_post_meta($post->ID, $key, $new_rep_value);
            }
        // treatment for single fields
        }else{        
            if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value            
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
        }

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
