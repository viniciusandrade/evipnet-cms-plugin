<?php

add_filter( 'media_send_to_editor', 'evip_media_send_to_editor', 15, 2 );
add_filter( 'media_upload_tabs', 'evip_media_upload_tabs', 15);
add_filter( 'media_upload_form_url', 'evip_media_upload_form_url');
add_filter( 'attachment_fields_to_edit', 'evip_attachment_fields_to_edit', 10, 2 );
add_action( 'admin_head', 'evip_admin_head_select_file' );
add_action( 'admin_init', 'evip_post_admin_init' );


function evip_post_admin_init() {
	if ($_GET["simple_fields_action"] == "select_file") {
		add_filter('gettext', 'evip_hijack_thickbox_text', 1, 3);
	}
}
function evip_hijack_thickbox_text($translated_text, $source_text, $domain) {
	if ($_GET["simple_fields_action"] == "select_file") {
		if ('Insert into Post' == $source_text) {
			return __('Select', 'simple_fields' );
		}
	}
	return $translated_text;
}


/*
	hide some stuff in the file browser
*/
function evip_admin_head_select_file() {
	if (isset($_GET["simple_fields_action"]) && $_GET["simple_fields_action"] == "select_file") {
		?>
		<style type="text/css">
			.wp-post-thumbnail,
			tr.image_alt,
			tr.post_title,
			tr.align,
			tr.image-size
			 {
				display: none;
			}
	
		</style>
		<?php
	}
}

// remove some fields in the file select dialogue, since simple fields don't use them anyway
function evip_attachment_fields_to_edit($form_fields, $post) {
	if (isset($_GET["simple_fields_action"]) && $_GET["simple_fields_action"] == "select_file") {
		unset(
			$form_fields["post_excerpt"],
			$form_fields["post_content"],
			$form_fields["url"],
			$form_fields["image_url"],
			$form_fields["image_alt"],
			$form_fields["menu_order"]
		);
		#bonny_d($form_fields);
	}
	return $form_fields;
}

// if we have simple fields args in GET, make sure our simple fields-stuff are added to the form
function evip_media_upload_form_url($url) {
	// $url:
	// http://localhost/wp-admin/media-upload.php?type=file&tab=library&post_id=0
	/*
	Array
	(
	    [simple_fields_dummy] => 1
	    [simple_fields_action] => select_file
	    [simple_fields_file_field_unique_id] => simple_fields_fieldgroups_8_4_0
	    [tab] => library
	)
	*/
	foreach ($_GET as $key => $val) {
		if (strpos($key, "simple_fields_") === 0) {
			$url = add_query_arg($key, $val, $url);
		}
	}
	return $url;
}

// remove gallery and remote url tab in file select
function evip_media_upload_tabs($arr_tabs) {
	if ($_GET["simple_fields_action"] == "select_file" || $_GET["simple_fields_action"] == "select_file_for_tiny") {
		unset($arr_tabs["gallery"], $arr_tabs["type_url"]);
	}
	return $arr_tabs;
}

// send the selected file to simple fields
function evip_media_send_to_editor($html, $id) {
	/*
	post_id	1060, -1 since dda17 October, 2
	tab	library
	type	file
	
	POST
	_wp_http_referer=/wp-admin/media-upload.php?simple_fields_action=select_file&simple_fields_file_field_unique_id=simple_fields_fieldgroups_8_4_new0&tab=library
	*/
	parse_str($_POST["_wp_http_referer"], $arr_postinfo);
	#bonny_d($arr_url);
	/*
	Array
	(
	    [/wp-admin/media-upload_php?simple_fields_dummy] => 1
	    [simple_fields_action] => select_file
	    [simple_fields_file_field_unique_id] => simple_fields_fieldgroups_8_4_new1
	    [tab] => library
	)
	*/
	// only act if file browser is initiated by simple fields
	if (isset($arr_postinfo["simple_fields_action"]) && $arr_postinfo["simple_fields_action"] == "select_file") {

		// add the selected file to input field with id simple_fields_file_field_unique_id
		$simple_fields_file_field_unique_id = $arr_postinfo["simple_fields_file_field_unique_id"];
		$file_id = (int) $id;
		
		$image_thumbnail = wp_get_attachment_image_src( $file_id, 'thumbnail', true );
		$image_thumbnail = $image_thumbnail[0];
		$image_html = "<img src='$image_thumbnail' alt='' />";
		$file_name = rawurlencode(get_the_title($file_id));

		?>
		<script type="text/javascript">
			var win = window.dialogArguments || opener || parent || top;
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").val(<?php echo $file_id ?>);
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").closest(".simple-fields-metabox-field-file").find(".simple-fields-metabox-field-file-selected-image").html("<?php echo $image_html ?>");
			win.jQuery("#<?php echo $simple_fields_file_field_unique_id ?>").closest(".simple-fields-metabox-field-file").closest(".simple-fields-metabox-field").find(".simple-fields-metabox-field-file-selected-image-name").text(unescape("<?php echo $file_name?>"));
			win.tb_remove();
		</script>
		<?php
		exit;
	} else {
		return $html;
	}

}

?>
