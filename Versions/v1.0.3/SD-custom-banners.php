<?php

/**
 * Plugin Name: Custom Banners
 * Plugin URI:  http://www.sitedesign.gr
 * Description: Προβολή banner σε συγκεκριμένο σημείο του site αυτόματα
 * Version:     1.0.3
 * Author:   	SiteDesign
 * Author URI:	http://www.sitedesign.gr
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Εδώ προσθέτουμε τον κώδικα για το Custom Post Type και το Metabox για τις ημερομηνίες

// CSS file
function custom_banners_enqueue_styles() {
    // Get the URL of the plugin directory
    $plugin_url = plugin_dir_url(__FILE__);
    
    // Enqueue the CSS file
    wp_enqueue_style('custom-banners-style', $plugin_url . '/SD-custom-banners.css');
}
add_action('wp_enqueue_scripts', 'custom_banners_enqueue_styles');

//Create new Post Type "Live"
function create_live_post_type() {
	// Get the URL of the plugin directory
    $plugin_url = plugin_dir_url(__FILE__);
	
    register_post_type('live',
        array(
            'labels' => array(
                'name' => __('Live'),
                'singular_name' => __('Live')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'live'),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
			'menu_icon' =>  $plugin_url . '/Live-icon_20x20.png', // Set this to the URL of your desired icon
        )
    );
}
add_action('init', 'create_live_post_type');

// Εγγραφή πεδίων Advanced Custom Fields
function register_acf_fields() {
    if( function_exists('acf_add_local_field_group') ):
        acf_add_local_field_group(array(
            'key' => 'group_622e5d5de1e80',
            'title' => 'Πεδία Ημερομηνίας & Ώρας',
            'fields' => array(
                array(
                    'key' => 'field_622e5d71373f2',
                    'label' => 'Ημερομηνία Έναρξης Προβολής',
                    'name' => 'start_date',
                    'type' => 'date_time_picker',
                    'display_format' => 'd/m/Y H:i',
                    'return_format' => 'Y-m-d H:i:s',
                    'show_date' => 'true',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                ),
                array(
                    'key' => 'field_622e5e2a373f3',
                    'label' => 'Ημερομηνία Λήξης Προβολής',
                    'name' => 'end_date',
                    'type' => 'date_time_picker',
                    'display_format' => 'd/m/Y H:i',
                    'return_format' => 'Y-m-d H:i:s',
                    'show_date' => 'true',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'live',
                    ),
                ),
            ),
        ));
        
    endif;
}
add_action('acf/init', 'register_acf_fields');

// Εδώ προσθέτουμε τον κώδικα για τη δημιουργία του διαχειριστικού περιβάλλοντος για τα banners


// Εισαγωγή του διαχειριστικού μενού
add_action('admin_menu', 'custom_banners_plugin_menu');

function custom_banners_plugin_menu() {
    add_menu_page('Custom Banners Settings', 'Custom Banners', 'manage_options', 'custom-banners-settings', 'custom_banners_settings_page');
}

// Δημιουργία της σελίδας ρυθμίσεων
function custom_banners_settings_page() {
    ?>
    <div class="wrap">
        <h2>Custom Banners Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_banners_settings_group'); ?>
            <?php do_settings_sections('custom-banners-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function custom_banners_settings_section_callback() {
    echo 'Upload your banner images here:';
}

// Εισαγωγή πεδίων ρυθμίσεων
add_action('admin_init', 'custom_banners_settings_init');

function custom_banners_settings_init() {
    register_setting('custom_banners_settings_group', 'custom_banners_desktop_banner');
    register_setting('custom_banners_settings_group', 'custom_banners_mobile_banner');

    add_settings_section('custom_banners_settings_section', 'Banner Settings', 'custom_banners_settings_section_callback', 'custom-banners-settings');

    add_settings_field('custom_banners_desktop_banner', 'Desktop Banner (1120x150)', 'custom_banners_desktop_banner_callback', 'custom-banners-settings', 'custom_banners_settings_section');
    add_settings_field('custom_banners_mobile_banner', 'Mobile Banner (300x250)', 'custom_banners_mobile_banner_callback', 'custom-banners-settings', 'custom_banners_settings_section');
}

// Εδώ προσθέτουμε τον κώδικα για τη δημιουργία του διαχειριστικού περιβάλλοντος για τα banners
function custom_banners_meta_box() {
    add_meta_box('custom-banners-meta-box', 'Banners', 'custom_banners_meta_box_callback', 'live');
}
add_action('add_meta_boxes', 'custom_banners_meta_box');

function custom_banners_meta_box_callback($post) {
    wp_nonce_field(basename(__FILE__), 'custom-banners-meta-box-nonce');
    $desktop_banner = get_post_meta($post->ID, 'desktop_banner', true);
    $mobile_banner = get_post_meta($post->ID, 'mobile_banner', true);
    ?>
    <p>
        <label for="desktop_banner"><?php _e('Desktop Banner (1120x150): '); ?></label>
        <input type="text" name="desktop_banner" id="desktop_banner" value="<?php echo $desktop_banner; ?>" style="width: 100%;">
    </p>
    <p>
        <label for="mobile_banner"><?php _e('Mobile Banner (300x250): '); ?></label>
        <input type="text" name="mobile_banner" id="mobile_banner" value="<?php echo $mobile_banner; ?>" style="width: 100%;">
    </p>
    <?php
}

function save_custom_banners_meta_box_data($post_id) {
    if (!isset($_POST['custom-banners-meta-box-nonce']) || !wp_verify_nonce($_POST['custom-banners-meta-box-nonce'], basename(__FILE__))) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['desktop_banner'])) {
        update_post_meta($post_id, 'desktop_banner', sanitize_text_field($_POST['desktop_banner']));
    }

    if (isset($_POST['mobile_banner'])) {
        update_post_meta($post_id, 'mobile_banner', sanitize_text_field($_POST['mobile_banner']));
    }
}
add_action('save_post', 'save_custom_banners_meta_box_data');


function custom_banners_desktop_banner_callback() {
    $desktop_banner = get_option('custom_banners_desktop_banner');
    ?>
    <input type="hidden" name="custom_banners_desktop_banner" id="custom_banners_desktop_banner" value="<?php echo esc_attr($desktop_banner); ?>" />
    <button class="upload-desktop-banner button">Upload Desktop Banner</button>
    <div class="desktop-banner-preview">
        <?php if (!empty($desktop_banner)) : ?>
            <img src="<?php echo esc_attr($desktop_banner); ?>" style="max-width: 100%;" />
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($){
            // Upload desktop banner button
            $('.upload-desktop-banner').click(function(e) {
                e.preventDefault();
                var image = wp.media({
                    title: 'Upload Desktop Banner',
                    multiple: false
                }).open()
                .on('select', function(e){
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    $('#custom_banners_desktop_banner').val(image_url);
                    $('.desktop-banner-preview').html('<img src="' + image_url + '" style="max-width: 100%;" />');
                });
            });
        });
    </script>
    <?php
}

function custom_banners_mobile_banner_callback() {
    $mobile_banner = get_option('custom_banners_mobile_banner');
    ?>
    <input type="hidden" name="custom_banners_mobile_banner" id="custom_banners_mobile_banner" value="<?php echo esc_attr($mobile_banner); ?>" />
    <button class="upload-mobile-banner button">Upload Mobile Banner</button>
    <div class="mobile-banner-preview">
        <?php if (!empty($mobile_banner)) : ?>
            <img src="<?php echo esc_attr($mobile_banner); ?>" style="max-width: 100%;" />
        <?php endif; ?>
    </div>
    <script>
        jQuery(document).ready(function($){
            // Upload mobile banner button
            $('.upload-mobile-banner').click(function(e) {
                e.preventDefault();
                var image = wp.media({
                    title: 'Upload Mobile Banner',
                    multiple: false
                }).open()
                .on('select', function(e){
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    $('#custom_banners_mobile_banner').val(image_url);
                    $('.mobile-banner-preview').html('<img src="' + image_url + '" style="max-width: 100%;" />');
                });
            });
        });
    </script>
    <?php
}

// ------------------------------------------------------------------------------------------------
		
// Εδώ προσθέτουμε τον κώδικα για την προβολή των banners

function custom_banners_handle_mobile_banner_upload($file) {
    // Check if file is uploaded
    if (!empty($_FILES['custom_banners_mobile_banner']['tmp_name'])) {
        $uploaded_file = wp_handle_upload($_FILES['custom_banners_mobile_banner'], array('test_form' => false));
        if (!empty($uploaded_file['url'])) {
            return $uploaded_file['url']; // Return the URL of the uploaded file
        }
    }
    return $file; // Return original file if upload fails
}

//Προβολή άρθρου

function custom_live_banner_shortcode() {
	// Set the default timezone to your desired timezone
	date_default_timezone_set('Europe/Athens'); // Example: Set timezone to Athens
	// Now all date and time functions will use the specified timezone
	
    ob_start(); // Start output buffering
	
	// Check for active "live" posts with no end date or end date greater than or equal to today
    $args = array(
        'post_type' => 'live',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'end_date',
                'compare' => 'NOT EXISTS', // End date is not set
            ),
            array(
                'key' => 'end_date',
                'value' => date('Y-m-d H:i:s'), // Current date and time
                'compare' => '>=', // End date is greater than or equal to today
                'type' => 'DATETIME'
            ),
			array(
				'key' => 'end_date',
				'value' => '', // Empty end date
				'compare' => '=', // End date is empty
			),
        ),
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_key' => 'start_date'
    );

    $live_query = new WP_Query($args);

	$desktop_banner_url = get_option('custom_banners_desktop_banner');
	$mobile_banner_url = get_option('custom_banners_mobile_banner');

	


    // If there is an active post, display the title and banner
    if ($live_query->have_posts()) :
        while ($live_query->have_posts()) : $live_query->the_post();
            $desktop_banner = get_post_meta(get_the_ID(), 'desktop_banner', true);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            $featured_image = get_the_post_thumbnail(get_the_ID(), 'full'); // Get the featured image HTML
			$post_link = get_permalink();
			$post_title = get_the_title(); // Get the post title
			$trimmed_title = wp_trim_words($post_title, 8, '...'); // Trim the title to 10 words
			
			
			$end_date_utc = get_post_meta(get_the_ID(), 'end_date', true); // Assuming 'end_date' is stored in UTC timezone
			$current_date_time = date('Y-m-d H:i:s'); // Get current date and time in the specified timezone
			//ECHO 'End Date: ' . $end_date_utc;
			//echo '<br/>';
			//ECHO 'Current Date: ' . $current_date_time; 
            // Check if the end date is not set, empty, or greater than or equal to today
            if (empty($end_date) || $end_date_utc >= $current_date_time) :
?>
            <div id="banner-desktop" style="text-align:center;height:150px;background-image: url('<?php echo esc_url($desktop_banner_url); ?>');">				
					<h2><a href="<?php echo esc_url($post_link); ?>"><?php echo $trimmed_title; ?></a></h2>
            </div>

<div id="banner-mobile" style="width:300px; height:250px;background-image: url('<?php echo esc_url($mobile_banner_url); ?>');">				
					<h2><a href="<?php echo esc_url($post_link); ?>"><?php echo $trimmed_title; ?></a></h2>
            </div>
<?php
            endif;
        endwhile;
    endif;
    wp_reset_postdata();

    return ob_get_clean(); // Return the buffer
}
add_shortcode('custom_live_banner', 'custom_live_banner_shortcode');







