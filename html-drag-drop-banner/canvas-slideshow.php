<?php
include '../../../wp-load.php'; 

header('Content-Type: application/json');
//ini_set('memory_limit','16M');

$error					= false;

$absolutedir			= dirname(__FILE__);
$dir					= '/tmp/';
$serverdir				= $absolutedir.$dir;

$tmp					= explode(',',$_POST['data']);
$imgdata 				= base64_decode($tmp[1]);

$extension				= strtolower(end(explode('.',$_POST['name'])));
$filename				= substr($_POST['name'],0,-(strlen($extension) + 1)).'.'.substr(sha1(time()),0,6).'.'.$extension;

$handle					= fopen($serverdir.$filename,'w');
fwrite($handle, $imgdata);
fclose($handle);

$response = array(
		"status" 		=> "success",
		"url" 			=> $dir.$filename.'?'.time(), //added the time to force update when editting multiple times
		"filename" 		=> $filename,
                "page_id"               => $_POST['customValue'],
                "imagepath"             => site_url()."/wp-content/plugins/html-drag-drop-banner/tmp/".$filename,
);

$taxonomy = $_POST['customValue'];
$filename1 = site_url()."/wp-content/plugins/html-drag-drop-banner/tmp/".$filename;
$upload_dir = wp_upload_dir();

global $wpdb;

if (!file_exists($upload_dir['path'].'/banners')) {
    mkdir($upload_dir['path'].'/banners', 0777, true);
}
    $image_data = file_get_contents($filename1);
    $filename = basename($filename1);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/banners/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/banners/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );



   $my_post = array(
  'post_title'    => $filename,
  'post_type'  => 'slides',
  'post_status'   => 'publish',
  'post_author'   => 1,
);
 $postid = wp_insert_post( $my_post );
$res2= set_post_thumbnail( $postid, $attach_id );

 $wpdb->query(
              'DELETE  FROM wp_term_relationships
               WHERE object_id = "'.$postid.'"'
);

$wpdb->insert('wp_term_relationships', array(
    'object_id' => $postid,
    'term_taxonomy_id' => $taxonomy,
));



if (!empty($_POST['original'])) {
	$tmp				= explode(',',$_POST['original']);
	$originaldata		= base64_decode($tmp[1]);
	$original			= substr($_POST['name'],0,-(strlen($extension) + 1)).'.'.substr(sha1(time()),0,6).'.original.'.$extension;

	$handle				= fopen($serverdir.$original,'w');
	fwrite($handle, $originaldata);
	fclose($handle);
	
	$response['original']	= $original;
}

print json_encode($response);
