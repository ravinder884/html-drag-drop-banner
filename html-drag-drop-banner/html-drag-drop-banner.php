<?php
/*
Plugin Name: HTML5 DRAG DROP BANNER
Plugin URI:  rvtechnologies.co.in
Description: EASY HTML5 DRAG DROP BANNER FOR EACH PAGE
Version:     1.0.0
Author:      rvtechnologies.co.in
Author URI:  rvtechnologies.co.in
*/


define( 'DROPZONEJS_PLUGIN_URL',   plugin_dir_url( __FILE__ ) );
define( 'DROPZONEJS_PLUGIN_VERSION', '0.0.1' );
add_action( 'plugins_loaded', 'dropzonejs_init' );
function dropzonejs_init() {
 // add_action( 'wp_enqueue_scripts', 'dropzonejs_enqueue_scripts' );
  add_shortcode( 'dropzonejs', 'dropzonejs_shortcode' );
}
function dropzonejs_enqueue_scripts() {
  wp_enqueue_script(
    'dropzonejs',
    'https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js',
    array(),
    DROPZONEJS_PLUGIN_VERSION
  );
  /*
  // Load custom dropzone javascript
  wp_enqueue_script(
    'customdropzonejs',
    DROPZONEJS_PLUGIN_URL. '/js/customize_dropzonejs.js',
    array( 'dropzonejs' ),
    DROPZONEJS_PLUGIN_VERSION
  );
  */
  wp_enqueue_style(
    'dropzonecss',
    'https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.css',
    array(),
    DROPZONEJS_PLUGIN_VERSION
  );
}
// Add Shortcode
function dropzonejs_shortcode( $atts ) {
  $url         = admin_url( 'admin-ajax.php' );
  $nonce_files = wp_nonce_field( 'protect_content', 'my_nonce_field' );
  $id = get_the_id();
  return <<<ENDFORM
<div id="dropzone-wordpress">
<form action="$url" class="dropzone needsclick dz-clickable" id="dropzone-wordpress-form" style="width:100%">
  $nonce_files
  <div class="dz-message needsclick">
    </div>
      <input type='hidden' name='postid' value='$id'>

  <input type='hidden' name='action' value='submit_dropzonejs'>
</form></div>
ENDFORM;
}

add_action( 'wp_ajax_nopriv_submit_dropzonejs', 'dropzonejs_upload' ); //allow on front-end
add_action( 'wp_ajax_submit_dropzonejs', 'dropzonejs_upload' );
/**
 * dropzonejs_upload() handles the AJAX request, learn more about AJAX in Plugins at https://codex.wordpress.org/AJAX_in_Plugins
 * @return [type] [description]
 */
function dropzonejs_upload() {

  if ( !empty( $_FILES ) && wp_verify_nonce( $_REQUEST['my_nonce_field'], 'protect_content' ) ) {
    $uploaded_bits = wp_upload_bits(
      $_FILES['file']['name'],
      null, //deprecated
      file_get_contents( $_FILES['file']['tmp_name'] )
    );


if ( ! function_exists( 'wp_handle_upload' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
}

$uploadedfile = $_FILES['file'];

$upload_overrides = array( 'test_form' => false );

$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

if ( $movefile && ! isset( $movefile['error'] ) ) {

$post_id = $_POST['postid'];
$filename1 = $movefile['url'];
$upload_dir = wp_upload_dir();

if (!file_exists($upload_dir['path'].'/banners')) {
    mkdir($upload_dir['path'].'/banners', 0755, true);
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
    $res2= set_post_thumbnail( $post_id, $attach_id );

 //     echo get_the_id();
        echo "File is valid, and was successfully uploaded.\n";

    print_r($_POST);

}


    if ( false !== $uploaded_bits['error'] ) {
      $error = $uploaded_bits['error'];
      return add_action( 'admin_notices', function() use ( $error ) {
          $msg[] = '<div class="error"><p>';
          $msg[] = sprintf( __( 'wp_upload_bits failed,  error: "<strong>%s</strong>' ), $error );
          $msg[] = '</p></div>';
          echo implode( PHP_EOL, $msg );
        } );
    }
    $uploaded_file     = $uploaded_bits['file'];
    $uploaded_url      = $uploaded_bits['url'];
    $uploaded_filetype = wp_check_filetype( basename( $uploaded_bits['file'] ), null );
    /*
    etc ...
    */

?> 
<?php

  }
  die();
}



function load_html5imageupload_style() {
   wp_register_script( 'html5imageupload', plugins_url() . '/html-drag-drop-banner/assets/js/html5imageupload.js', true, '1.0.0' );
        wp_enqueue_script( 'html5imageupload' );
}
//add_action( 'wp_enqueue_scripts', 'load_html5imageupload_style' );
function loadimgaeuploadJs() {
  if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {
   echo "<script type='text/javascript' src=".plugins_url() . "/html-drag-drop-banner/assets/js/html5imageupload.js></script>";
  }
}
add_action('wp_footer', 'loadimgaeuploadJs', 100);

function wpse_89494_enqueue_scripts() {
 if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {
        wp_enqueue_style( 'wpse_89494_style_3', plugins_url() . '/html-drag-drop-banner/assets/css/html5imageupload.css' );
  }
}



  
add_action( 'wp_enqueue_scripts', 'wpse_89494_enqueue_scripts' );


function html5adjsutablebanners($attrs) {
    ob_start(); 
    ?>


<?php 
if($attrs['height']) {
if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {  
$post_thumbnail_id = get_post_thumbnail_id( $post_id ); 
if($post_thumbnail_id =="") { ?>
<div class="addbanner"><img src="<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/assets/bannerimage.png">
</div>

 <div class="dropzone" data-width="<?php echo $attrs['width']; ?>" data-height="<?php echo $attrs['height']; ?>" data-image="<?php echo the_post_thumbnail_url(); ?>" data-url="<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/canvas.php" data-resize="false" data-originalsize="false" style="width:100%;display:none">
            <input type="file" name="thumb" style="position: absolute;">
          </div>

<?php } else { ?>

<div class="dropzone" data-width="<?php echo $attrs['width']; ?>" data-height="<?php echo $attrs['height']; ?>" data-image="<?php echo the_post_thumbnail_url(); ?>" data-url="<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/canvas.php" data-resize="false" data-originalsize="false" style="width:100%;">
            <input type="file" name="thumb" style="position: absolute;">
          </div>
<?php } ?> 
<?php }
else { 
    ?> <div class="<?php echo $attrs['class']; ?>" ><?php the_post_thumbnail(); ?> </div><?php
  } 

if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {   ?>
   <script>
    jQuery(window).load(function() {
       jQuery('#retrievingfilename').html5imageupload({
        onAfterProcessImage: function() {
            jQuery('#filename').val($(this.element).data('name'));
        },
        onAfterCancel: function() {
            jQuery('#filename').val('');
        }
    });
    jQuery('.dropzone').html5imageupload({
        onSave: function(data) {
         alert('done');
        },
        
    });
   jQuery('.dropzone').html5imageupload({data: {customValue: <?php echo get_the_id(); ?>}});
  jQuery('.dropzone').html5imageupload({  data: {customValue: <?php echo get_the_id(); ?>},
    onAfterProcessImage: function() {
         location.reload();
       },
        onAfterCancel: function() {
       }
    });
  });
 </script>
<?php } // admin editor con end here
 }
else {

if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {   ?> 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.css" rel="stylesheet">
   <script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
    <script>
Dropzone.options.dropzoneWordpressForm = {
  //acceptedFiles: "image/*", // all image mime types
  acceptedFiles: ".jpg", // only .jpg files
  maxFiles: 1,
  uploadMultiple: false,
  maxFilesize: 5, // 5 MB
  //addRemoveLinks: true,
  //dictRemoveFile: 'X (remove)',
  init: function() {
    this.on("success", function(file, messageOrDataFromServer, myEvent) {
                  window.setTimeout(function() { location.reload(); }, 1000);
            });  
  }
};
</script>
<?php } ?>

<?php
if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {  
 $post_thumbnail_id = get_post_thumbnail_id( $post_id ); 
if($post_thumbnail_id =="") {  ?>

  <div class="addbanner"><img src="<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/assets/bannerimage.png">
</div>
<div class="dropzone1" style="width:100%;display:none;"><?php echo do_shortcode('[dropzonejs]'); echo "</div>";

 }  else {
      ?> <div class="<?php echo $attrs['class']; ?>" >
         <a href="javascript:void(0)" class="btn btn-danger btn-del" style="position: absolute;"> <i class="glyphicon glyphicon-trash"></i> </a>
      <?php the_post_thumbnail(); ?> </div><?php
 }

}
else { 
    ?> <div class="<?php echo $attrs['class']; ?>" ><?php the_post_thumbnail(); ?> </div><?php
  }



 }


if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {   ?>

 

   <!--   <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->

              <script>
   jQuery(window).load(function() {
   jQuery(".btn-del").attr( "onclick" , "return delimg(<?php echo $post_thumbnail_id; ?>);" );
   jQuery(".addbanner").click(function() {
   jQuery(this).hide();
      jQuery(".dropzone").show();
      jQuery(".dropzone1").show();
       jQuery(".dz-clickable").css("width", "100%");

      })
   });
   function delimg(arg) {
       jQuery.ajax({
        type: "POST",
        url: "<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/delete.php",
        cache: false,
        data: { text1: arg,},
        dataType: "json",
        success: function(data) {
        }
    })
setTimeout(explode, 1000);
   }

function explode(){
location.reload();
}
    </script> 
   <?php } // admin editor con end here
  return ob_get_clean();   
} 
add_shortcode( 'HTML5-BANNER', 'html5adjsutablebanners' );

  function slidershowAddnew($attrs) {
    ob_start();
  if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {  
  $category   =  $attrs['category'];
  $width      =  $attrs['width'];
  $height     =  $attrs['height'];
  ?>
      
     
		 <div class="addcontrols">
        <span class="cstmbtn btn btn-success btn-ok addnewslider" title="Ok"><i class="glyphicon glyphicon-plus"></i>Add Slide</span>
	<span class="manageslide"><a href="<?php echo admin_url( '' ) ?>/edit.php?post_type=slides" class="edit">Manage / Re-order</a> 
	</div>
         <div class="dropzone" data-width="<?php echo $attrs['width']; ?>" data-height="<?php echo $attrs['height']; ?>" data-image="<?php echo the_post_thumbnail_url(); ?>" data-url="<?php echo site_url(); ?>/wp-content/plugins/html-drag-drop-banner/canvas-slideshow.php" data-resize="false" data-originalsize="false" style="width:80%;display:none">
            <input type="file" name="thumb" style="position: absolute;">
          </div>

<?php if( current_user_can('editor') || current_user_can('admin') || current_user_can('administrator') ) {   ?>
                 <script>
jQuery(window).load(function() {
jQuery('.dropzone').html5imageupload({  data: {customValue: <?php echo $category; ?>},
    onAfterProcessImage: function() {
         location.reload();
       },

    onAfterCancel: function() {
    }
});
   jQuery(".addnewslider").click(function() {
     jQuery(this).hide();
       jQuery(".dropzone").show();
     })
  });
      </script>
<?php } ?>
     <?php return ob_get_clean();  
      }
   }
add_shortcode( 'addslideshow', 'slidershowAddnew' );

