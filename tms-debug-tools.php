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

          <?php // Template debug ?>
         <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Template debug', 'text-domain' ); ?></th>
            <td>
              <?php  $value = self::get_theme_option( 'debug_template' );  // checkbox_example?>
              <input type="checkbox" name="theme_options[debug_template]" <?php  checked( $value, 'on' ); ?>> <?php  esc_html_e( 'Template debug mode.', 'text-domain' ); ?>
            </td>
          </tr>

					<?php // Template column in page table row ?>
         <tr valign="top">
            <th scope="row"><?php esc_html_e( 'Template in page table column', 'text-domain' ); ?></th>
            <td>
              <?php  $value = self::get_theme_option( 'debug_template_table_col' );  // checkbox_example?>
              <input type="checkbox" name="theme_options[debug_template_table_col]" <?php  checked( $value, 'on' ); ?>> <?php  esc_html_e( 'Template page column debug mode.', 'text-domain' ); ?>
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



			if ( ! empty( $options['debug_template_table_col'] ) ) {
				$options['debug_template_table_col'] = 'on';
			} else {
				unset( $options['debug_template_table_col'] ); // Remove from options if not checked
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

			global $template;
				//		$templateName =  (get_page_template_slug( get_queried_object_id() ) )? '<br>Template name: ' .  get_queried_object_id() .  basename( get_page_template() ) .

//			           $templateNameStr .= str_replace(".php","",get_page_template_slug());


//$templateNameStr = basename( get_page_template());//get_page_template_slug( get_queried_object_id() );

$templatePath = (get_page_template_slug( get_queried_object_id() ) )? '<br>Template name: ' .  get_page_template_slug( get_queried_object_id() ) . '<br>' : '<br>';
	 		$templatePathStr = str_replace(get_theme_root(), '', $template);


		$templateName =  tmw_debug_get_template_name(get_queried_object_id());
		$templateNameStr .= $templateName ?	'Template name: ' . tmw_debug_get_template_name(get_queried_object_id()) . '<br />' : '<br />';

		echo '<div style="background-color: #CCC;padding:10px;position:absolute;right:0px;top:50px;z-index:9999999;"><div class="position:relative;top:80px;">' .
		'Template path: ' . $templatePathStr . '<br>' .
		$templateNameStr .

	//	'Template path: ' . str_replace(get_theme_root(), '',  get_page_template(get_queried_object_id())) .

		'</div></div>';



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
           // stop
        }

    }
}





if ( !function_exists('tms_add_template_column_content')) {

		if(tms_get_theme_option('debug_template_table_col') == 'on'){

						function tms_add_template_column( $columns ) {
						  $columns['tms_template_info'] = 'Template info';
						  return $columns;
						}


						/* Display custom column's content */
						function tms_add_template_column_content( $column, $post_id ) {
						  //We will check whether the column is our defined column above by name
						  if ( 'tms_template_info' === $column ) {


							$post = get_post( $post_id );
							//esc_html( $post->ID )

							$templateName =  tmw_debug_get_template_name($post_id);
							$templateNameStr = $templateName ?	'name: <strong>' . tmw_debug_get_template_name($post_id) . '</strong><br />' : '';

							echo $templateNameStr . 'path: <strong>' . str_replace(get_theme_root(), '',  get_page_template($post_id)) . '</strong>';
							?>

							<?php
						  }
						}

						// for posts
						add_filter( 'manage_posts_columns', 'tms_add_template_column' );
						add_action( 'manage_posts_custom_column', 'tms_add_template_column_content', 10, 2 );
						// for pages
						add_filter( 'manage_pages_columns', 'tms_add_template_column' );
						add_action( 'manage_pages_custom_column', 'tms_add_template_column_content', 10, 2 );
		}
}
