<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!function_exists('tms_log_clear')) {
     function tms_log_clear(){
        file_put_contents( WP_CONTENT_DIR ."/tms.log", '');
     }
}


if (!function_exists('tms_log')) {
    function tms_log($data = null, $label = '',  $clear = false){
        // hreinsa log
        if( $clear === true ){
            tms_log_clear();
        }
        if( $label ){
            error_log( "{$label}\n", 3, WP_CONTENT_DIR."/tms.log" );
        }
        if( is_array( $data ) || is_object( $data ) ) {
            error_log( print_r( $data, true ), 3, WP_CONTENT_DIR ."/tms.log" );
        } else {
            error_log( $data . "\n", 3, WP_CONTENT_DIR ."/tms.log" );
        }
    }
}



if ( ! class_exists( 'TMS_Debug_Settings' ) ) {

    class TMS_Debug_Settings {

        /**
         * Start shit up
         *
         * @since 1.0.0
         */
        public function __construct() {
            if( is_admin() ){
                add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
                add_action( 'admin_init', array( $this, 'register_settings' ) );
            }
        }
        /**
        * Add sub menu page
        *
        * @since 1.0.0
        */
        public function add_admin_menu() {
            add_options_page(
                    'Debug options',
                    'Debug options',
                    'manage_options',
                    'options_page_options',
                    array(
                        $this,
                        'settings_page'
                    )
                );
        }

        /**
        * Settings page output
        *
        * @since 1.0.0
        */
        public function  settings_page() {
        ?>
        <div class="wrap">

      <h1><?php esc_html_e( 'SEO Options', 'text-domain' ); ?></h1>

      <form method="post" action="options.php">

        <?php settings_fields( 'theme_options' ); ?>

        <table class="form-table wpex-custom-admin-login-table">

          <?php // Checkbox example ?>
         <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Template debug', 'text-domain' ); ?></th>
            <td>
              <?php  $value = self::get_theme_option( 'debug_template' );  // checkbox_example?>
              <input type="checkbox" name="theme_options[debug_template]" <?php  checked( $value, 'on' ); ?>> <?php  esc_html_e( 'Template debug mode.', 'text-domain' ); ?>
            </td>
          </tr>

					<?php //  Javascript debug ?>
         <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Javascript debug', 'text-domain' ); ?></th>
            <td>
              <?php  $value = self::get_theme_option( 'debug_js' );  // checkbox_example?>
              <input type="checkbox" name="theme_options[debug_js]" <?php  checked( $value, 'on' ); ?>> <?php  esc_html_e( 'Javascript debug mode.', 'text-domain' ); ?>
            </td>
          </tr>



        </table>

        <?php submit_button(); ?>

      </form>

    </div><!-- .wrap -->

        <?php
        }


/**
   * Returns all theme options
   *
   * @since 1.0.0
   */
  public static function get_theme_options() {
    return get_option( 'theme_options' );
  }

  /**
   * Returns single theme option
   *
   * @since 1.0.0
   */
  public static function get_theme_option( $id ) {
    $options = self::get_theme_options();
    if ( isset( $options[$id] ) ) {
      return $options[$id];
    }
  }


        /**
   * Register a setting and its sanitization callback.
   *
   * We are only registering 1 setting so we can store all options in a single option as
   * an array. You could, however, register a new setting for each option
   *
   * @since 1.0.0
   */
  public static function register_settings() {
    register_setting( 'theme_options', 'theme_options', array( 'WPEX_Theme_Options', 'sanitize' ) );
  }

  /**
   * Sanitization callback
   *
   * @since 1.0.0
   */
  public static function sanitize( $options ) {

    // If we have options lets sanitize them
    if ( $options ) {

      // Checkbox
      if ( ! empty( $options['debug_template'] ) ) {
        $options['debug_template'] = 'on';
      } else {
        unset( $options['debug_template'] ); // Remove from options if not checked
      }

			if ( ! empty( $options['debug_js'] ) ) {
				$options['debug_js'] = 'on';
			} else {
				unset( $options['debug_js'] ); // Remove from options if not checked
			}

      // Input
     /* if ( ! empty( $options['input_example'] ) ) {
        $options['input_example'] = sanitize_text_field( $options['input_example'] );
      } else {
        unset( $options['input_example'] ); // Remove from options if empty
      }*/

      // Select
      if ( ! empty( $options['select_example'] ) ) {
        $options['select_example'] = sanitize_text_field( $options['select_example'] );
      }

    }

    // Return sanitized options
    return $options;

  }



}

}





new TMS_Debug_Settings();

// Helper function to use in your theme to return a theme option value
function tms_get_theme_option( $id = '' ) {
	return TMS_Debug_Settings::get_theme_option( $id );
}



function tmw_debug_get_template_name( $page_id = null ) {
    if ( ! $template = get_page_template_slug( $page_id ) )
        return;
    if ( ! $file = locate_template( $template ) )
        return;

    $data = get_file_data(
        $file,
        array(
            'Name' => 'Template Name',
        )
    );

    return $data['Name'];
}




add_action('wp_head', 'tms_template');
function tms_template() {
	if( !is_admin() && current_user_can( 'administrator') ):
		if(tms_get_theme_option('debug_template') == 'on'):
		//	global $template;
    //        $templateName = (get_page_template_slug( get_queried_object_id() ) )? '<br>Template name: ' .  get_queried_object_id() .  basename( get_page_template() ) .   get_page_template_slug( get_queried_object_id() ) . '<br>' : '<br>';
	 	//	echo basename($template) . $templateName;
			 // get_page_template_slug( get_queried_object_id() );
			//echo '<h1>' . myprefix_get_theme_option('debug_template') . '</h1>';

	//	global $template;
		//	$templateName =  (get_page_template_slug( get_queried_object_id() ) )? '<br>Template name: ' .  get_queried_object_id() .  basename( get_page_template() ) .
		//	get_page_template_slug( get_queried_object_id() ) . '<br>' : '<br>';
			// wp_basename($template)

// str_replace(".php","",get_page_template_slug())

		//	echo  '<h1>' .    . '</h1>';
		// get_template_directory_uri()

// $post = get_post( $post );

	    // $the_templ =  '<strong style="background-color: #CCC;padding:10px">TEMPLATE = '.  basename( $template ) . '</strong><br />';


		//	 echo  '<h1>' .  get_theme_root()  . '</h1>';
		//	 echo  '<h1>' .  get_page_template(get_queried_object_id())  . '</h1>';

			 // allar page templates
			// $templates = wp_get_theme()->get_page_templates();
			 //var_dump(wp_get_theme());

		//	 var_dump($templates);
	   //     $template_name = str_replace( " ", "-", strtolower( $templates[$template] ) );


			//	echo $the_templ . ' <p>' .$template_name. '</p>';

//apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
		//	echo	get_page_template_slug(get_queried_object_id());

global $template;

	$templateName =  tmw_debug_get_template_name(get_queried_object_id());

	$templateNameStr = $templateName ?	'Template name: ' . tmw_debug_get_template_name(get_queried_object_id()) . '<br />' : '';

		echo '<div style="background-color: #CCC;padding:10px;position:absolute;left:0px;top:50px;z-index:1;"><div class="position:relative;top:80px;">' .
		$templateNameStr .

		'Template path: ' . str_replace(get_theme_root(), '',  get_page_template(get_queried_object_id())) . '</div></div>';



		endif;
	endif;
}





add_action('wp_print_scripts', 'tms_js');
function tms_js() {
	if( !is_admin() ):
		if(tms_get_theme_option('debug_js') === 'on'):
		/*	global $wp_scripts;

			echo '<pre>';
			//var_dump($wp_scripts->registered);
			echo '</pre>';
			foreach( $wp_scripts->queue as $handle ) :
			//	echo $handle . ' | ' . '<br>';
				if(!empty($wp_scripts->all_dep)):
					//foreach ($wp_scripts->all_dep as $value) {
					//	echo $value;
					//}
				endif;
			endforeach;
			*/
	 		//echo 'js is on';//  basename($template);
			//echo '<h1>' . tms_get_theme_option('debug_template') . '</h1>';
			global $wp_scripts, $wp_styles;
			$wp_scripts->all_deps( $wp_scripts->queue );
			$loaded_scripts = $wp_scripts->to_do;
			//$loaded_scripts =  $this->filter_debug_objects_files( $loaded_scripts );

			// Get all enqueue styles
			$loaded_styles = $wp_styles->do_items();
		//	$loaded_styles = $this->filter_debug_objects_files( $loaded_styles );
			?>

			<h4><?php _e( 'Enqueued Scripts' ); ?></h4>
			<table class="tablesorter">
				<thead>
				<tr>
					<th>Order</th>
					<th>Loaded</th>
					<th>Dependencies</th>
					<th>Path</th>
					<th>Version</th>
				</tr>
				</thead>
				<?php
				$i     = 1;
				foreach ( $loaded_scripts as $loaded_script ) {

					$deps         = $wp_scripts->registered[ $loaded_script ]->deps;
					$dependencies = ( count( $deps ) > 0 ) ? implode(  ', ', $deps ) : '';
					echo '<tr><td>' . $i . '</td>';
					echo '<td>' . esc_html( $loaded_script ) . '</td>';
					echo '<td>' . esc_html( $dependencies ) . '</td>';
					echo '<td>' . esc_html( $wp_scripts->registered[ $loaded_script ]->src ) . '</td>';
					echo '<td>' . esc_html( $wp_scripts->registered[ $loaded_script ]->ver ) . '</td></tr>' . "\n";

					$i ++;
				}
				?>
			</table>

			<h4><?php esc_attr_e( 'Enqueued Styles', 'debug_objects' ); ?></h4>
			<table class="tablesorter">
				<thead>
				<tr>
					<th>Order</th>
					<th>Loaded</th>
					<th>Dependencies</th>
					<th>Path</th>
					<th>Version</th>
				</tr>
				</thead>

				<?php
				$i     = 1;
				foreach ( $loaded_styles as $loaded_style ) {

					$deps         = $wp_styles->registered[ $loaded_style ]->deps;
					$dependencies = ( count( $deps ) > 0 ) ? implode( ', ', $deps ) : '';
					echo '<tr><td>' . $i . '</td>';
					echo '<td>' . esc_html( $loaded_style ) . '</td>';
					echo '<td>' . esc_html( $dependencies ) . '</td>';
					echo '<td>' . esc_html( $wp_styles->registered[ $loaded_style ]->src ) . '</td>';
					echo '<td>' . esc_html( $wp_styles->registered[ $loaded_style ]->ver ) . '</td></tr>' . "\n";

					$i ++;
				}
				?>
			</table> <?php
		endif;
	endif;
}








    /**
    * Umkringir var_dump með pre-tag
    *
    * Notað fyrir debug, á ekki að vera notuð í production
    *
    * @param mixed[] $array það sem á að birta með var_dump
    * @param bool $on það sem á að birta með var_dump
    *
    */
if (!function_exists('var_dump_pre')) {
    function var_dump_pre($array = '', $on = true)
    {

        if($on === true)
        {
            echo '<pre>'; var_dump($array); echo '</pre>';
        }
        elseif($on === false)
        {
            var_dump($array);
        }
        elseif($on == 'off')
        {
            // ekki gera neitt
        }

    }
}




/*

function Zumper_widget_enqueue_script() {
	$send_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			//'featured' => $termFeatured, //'http://localhost:8888/tripical-v2/wp-content/uploads/2017/03/bitmap.png',//$image,
			//'nonce' => $nonce

	);
	wp_localize_script( 'my_custom_script', 'frontend_ajax_object', $send_array );
    wp_enqueue_script( 'my_custom_script',   WP_CONTENT_URL . '/mu-plugins/debug.js', array('jquery') );


}
add_action('wp_enqueue_scripts', 'Zumper_widget_enqueue_script');





add_action('wp_ajax_nopriv_prufajs', 'prufajs');
add_action('wp_ajax_prufajs', 'prufajs');

if (!function_exists('prufajs')) {
	function prufajs(){
		$foo = $_POST['log'];
		$label = $_POST['label'];
		tms_log($foo, $label);
		$arrayName = array('hello' =>'Hello');
		$json = '
		{
		    "MyJSon": {
		        "activityId": 99,
		        "startTimeId": 1246,
		        "date": "2017-06-17",
		        "flexibleDayOption": null,
		        "pickup": false,
		        "pickupPlaceId": null,
		        "pickupPlaceDescription": null,
		        "pickupPlaceRoomNumber": null,
		        "dropoff": false,
		        "dropoffPlaceId": null,
		        "dropoffPlaceDescription": null,
		        "pricingCategoryBookings": [{
		            "pricingCategoryId": 102,
		            "extras": []
		        }],
		        "extras": []
		    }
		}
		';
		echo $json;//json_encode($ret);

		die();
	}

}


*/




// global varibles js
// https://wordpress.stackexchange.com/questions/119573/is-it-possible-to-use-wp-localize-script-to-create-global-js-variables-without-a
/*
function my_js_variables(){
    ?>
    <script>
    var ajaxurl = <?php echo json_encode( admin_url( "admin-ajax.php" ) ) ?>;
    var ajaxnonce = <?php echo json_encode( wp_create_nonce( "itr_ajax_nonce" ) ) ?>;
    var myarray = <?php echo json_encode( array(
        'food' => 'bard',
        'bard' => false,
        'quux' => array( 1, 2, 3, ),
    ) ) ?>;
    </script>
    <?php
}
add_action ( 'wp_head', 'my_js_variables' );

*/
/*
//Multiline error log class
// ersin güvenç 2008 eguvenc@gmail.com
//For break use "\n" instead '\n'
// http://php.net/manual/en/function.error-log.php

Class log {
  //
  const USER_ERROR_DIR = '/home/site/error_log/Site_User_errors.log';
  const GENERAL_ERROR_DIR = '/home/site/error_log/Site_General_errors.log';


//   User Errors...

    public function user($msg,$username)
    {
    $date = date('d.m.Y h:i:s');
    $log = $msg."   |  Date:  ".$date."  |  User:  ".$username."\n";
    error_log($log, 3, self::USER_ERROR_DIR);
    }

//   General Errors...

    public function general($msg)
    {
    $date = date('d.m.Y h:i:s');
    $log = $msg."   |  Date:  ".$date."\n";
    error_log($msg."   |  Tarih:  ".$date, 3, self::GENERAL_ERROR_DIR);
    }

}

$log = new log();
$log->user($msg,$username); //use for user errors
//$log->general($msg); //use for general errors
*/



/*



add_action('wp_head', 'tms_menu_debug');
function tms_menu_debug() {
	if( !is_admin() ):
    $menu_items = wp_get_nav_menu_items( 'main-menu' );
    echo '<pre>';
  /*  foreach( $menu_items as $item ) {
      print_r( $item ) ; // see what you can work with
      // carry on
    }*/
/*
$this_item = current( wp_filter_object_list( $menu_items, array( 'object_id' => get_queried_object_id() ) ) );
//echo print_r($this_item->classes);
var_dump($this_item);
//$this_item->classes[] = 'active ';
//tms_log($this_item, 'this');
    echo '</pre>';
	endif;
}
*/



/*

If you need reverse engineering to find all the pages that are working under a particular page template filename, this is one solution that may work for you.

function wpdocs_get_pages_by_template_filename( $page_template_filename ) {
    return get_pages( array(
        'meta_key' => '_wp_page_template',
        'meta_value' => $page_template_filename
    ) );
}

*/
