<?php
/**
 * ACF Fields for Events Custom Post Type
 * 
 * This file contains the ACF field group configuration for the Events custom post type.
 * You can import this into ACF or use it as a reference to create the fields manually.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for Events post type
 */
function register_events_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_events_fields',
        'title' => 'Event Details',
        'fields' => array(
            // Event Start Date
            array(
                'key' => 'field_event_start_date',
                'label' => 'Event Start Date',
                'name' => 'event_start_date',
                'type' => 'date_picker',
                'instructions' => 'Select the start date of the event',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'Y-m-d',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
            ),
            
            // Event End Date
            array(
                'key' => 'field_event_end_date',
                'label' => 'Event End Date',
                'name' => 'event_end_date',
                'type' => 'date_picker',
                'instructions' => 'Select the end date of the event (leave blank if single day event)',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'Y-m-d',
                'return_format' => 'Y-m-d',
                'first_day' => 1,
            ),
            
            // Event Time
            array(
                'key' => 'field_event_time',
                'label' => 'Event Time',
                'name' => 'event_time',
                'type' => 'time_picker',
                'instructions' => 'Select the time of the event',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'g:i a',
                'return_format' => 'H:i:s',
            ),
            
            // Event Location
            array(
                'key' => 'field_event_location',
                'label' => 'Event Location',
                'name' => 'event_location',
                'type' => 'text',
                'instructions' => 'Enter the location of the event (e.g., "Conference Center, London" or "Online Event")',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'Conference Center, London',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'event',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Fields for event details and metadata',
    ));
}

// Register the fields when ACF is available
add_action('acf/init', 'register_events_acf_fields');

/**
 * Add meta box for event details (fallback if ACF is not available)
 */
function add_events_meta_box() {
    if (!function_exists('get_field')) {
        add_meta_box(
            'event_details',
            'Event Details',
            'event_details_meta_box_callback',
            'event',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_events_meta_box');

/**
 * Meta box callback for event details
 */
function event_details_meta_box_callback($post) {
    wp_nonce_field('event_details_meta_box', 'event_details_meta_box_nonce');
    
    $event_start_date = get_post_meta($post->ID, '_event_start_date', true);
    $event_end_date = get_post_meta($post->ID, '_event_end_date', true);
    $event_time = get_post_meta($post->ID, '_event_time', true);
    $event_location = get_post_meta($post->ID, '_event_location', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="event_start_date">Event Start Date</label></th>
            <td><input type="date" id="event_start_date" name="event_start_date" value="<?php echo esc_attr($event_start_date); ?>" required /></td>
        </tr>
        <tr>
            <th><label for="event_end_date">Event End Date</label></th>
            <td><input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr($event_end_date); ?>" /></td>
        </tr>
        <tr>
            <th><label for="event_time">Event Time</label></th>
            <td><input type="time" id="event_time" name="event_time" value="<?php echo esc_attr($event_time); ?>" /></td>
        </tr>
        <tr>
            <th><label for="event_location">Event Location</label></th>
            <td><input type="text" id="event_location" name="event_location" value="<?php echo esc_attr($event_location); ?>" style="width: 100%;" placeholder="Conference Center, London" /></td>
        </tr>
    </table>
    <?php
}

/**
 * Save event details meta box data
 */
function save_event_details_meta_box($post_id) {
    if (!isset($_POST['event_details_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['event_details_meta_box_nonce'], 'event_details_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['post_type']) && 'event' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }
    
    $fields = array(
        '_event_start_date',
        '_event_end_date',
        '_event_time',
        '_event_location'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[str_replace('_', '', $field)])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[str_replace('_', '', $field)]));
        }
    }
}
add_action('save_post', 'save_event_details_meta_box');

/**
 * Add custom columns to events admin list
 */
function add_events_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['event_date'] = 'Event Date';
    $new_columns['event_time'] = 'Event Time';
    $new_columns['event_location'] = 'Location';
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}
add_filter('manage_event_posts_columns', 'add_events_admin_columns');

/**
 * Display custom column content
 */
function display_events_admin_columns($column, $post_id) {
    switch ($column) {
        case 'event_date':
            $start_date = get_field('event_start_date', $post_id);
            $end_date = get_field('event_end_date', $post_id);
            
            if (!$start_date) {
                $start_date = get_post_meta($post_id, '_event_start_date', true);
            }
            if (!$end_date) {
                $end_date = get_post_meta($post_id, '_event_end_date', true);
            }
            
            if ($start_date) {
                $start_formatted = date_i18n('M j, Y', strtotime($start_date));
                if ($end_date && $end_date !== $start_date) {
                    $end_formatted = date_i18n('M j, Y', strtotime($end_date));
                    echo $start_formatted . ' - ' . $end_formatted;
                } else {
                    echo $start_formatted;
                }
            } else {
                echo '—';
            }
            break;
            
        case 'event_time':
            $time = get_field('event_time', $post_id);
            if (!$time) {
                $time = get_post_meta($post_id, '_event_time', true);
            }
            if ($time) {
                echo date_i18n('g:i A', strtotime($time));
            } else {
                echo '—';
            }
            break;
            
        case 'event_location':
            $location = get_field('event_location', $post_id);
            if (!$location) {
                $location = get_post_meta($post_id, '_event_location', true);
            }
            echo $location ? esc_html($location) : '—';
            break;
    }
}
add_action('manage_event_posts_custom_column', 'display_events_admin_columns', 10, 2);

/**
 * Make custom columns sortable
 */
function make_events_columns_sortable($columns) {
    $columns['event_date'] = 'event_start_date';
    $columns['event_time'] = 'event_time';
    return $columns;
}
add_filter('manage_edit-event_sortable_columns', 'make_events_columns_sortable');

/**
 * Handle sorting for custom columns
 */
function handle_events_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    switch ($orderby) {
        case 'event_start_date':
            $query->set('meta_key', 'event_start_date');
            $query->set('orderby', 'meta_value');
            break;
        case 'event_time':
            $query->set('meta_key', 'event_time');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'handle_events_column_sorting');
