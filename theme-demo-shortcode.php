<?php
/*
Plugin Name: Theme Demo Shortcode
Description: A plugin to generate shortcodes for displaying themes with images and demo links for both Themes in Arabic and English pages.
Version: 1.0
Author: mzughbor
*/

function theme_demo_shortcode_enqueue_scripts($hook)
{
    if ($hook != 'toplevel_page_theme_demo_shortcode') {
        return;
    }
    wp_enqueue_script('theme-demo-shortcode-admin-js', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), '1.0', true);
    wp_enqueue_style('theme-demo-shortcode-admin-css', plugin_dir_url(__FILE__) . 'admin.css', array(), '1.0');
}
add_action('admin_enqueue_scripts', 'theme_demo_shortcode_enqueue_scripts');

function theme_demo_shortcode_enqueue_frontend_scripts()
{
    wp_enqueue_style('theme-demo-shortcode-frontend-css', plugin_dir_url(__FILE__) . 'frontend.css', array(), '1.0');
}
add_action('wp_enqueue_scripts', 'theme_demo_shortcode_enqueue_frontend_scripts');

function theme_demo_shortcode_menu()
{
    add_menu_page('Theme Demo Shortcode', 'Theme Demo', 'manage_options', 'theme_demo_shortcode', 'theme_demo_shortcode_options');
}
add_action('admin_menu', 'theme_demo_shortcode_menu');

function theme_demo_shortcode_options()
{
    $categories = array('news', 'e-commerce', 'portfolios', 'companies', 'medicine', 'real-estate');
?>
    <div class="wrap">
        <h1>Theme Demo Shortcode</h1>
        <h2 class="nav-tab-wrapper">
            <?php foreach ($categories as $category) { ?>
                <a href="#<?php echo $category; ?>" class="nav-tab"><?php echo ucfirst($category); ?></a>
            <?php } ?>
        </h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('theme_demo_shortcode_options_group');
            do_settings_sections('theme_demo_shortcode');
            ?>
            <?php foreach ($categories as $category) { ?>
                <div id="<?php echo $category; ?>" class="tab-content" style="display:none;">
                    <h3><?php echo ucfirst($category); ?></h3>
                    <table class="form-table" id="theme-demo-fields-<?php echo $category; ?>">
                        <?php
                        $fields = get_option('theme_demo_shortcode_fields_' . $category);
                        if (!empty($fields)) {
                            foreach ($fields as $index => $field) {
                        ?>
                                <tr>
                                    <th scope="row"><label for="theme_demo_image_<?php echo $category . '_' . $index; ?>">Image URL <?php echo ($index + 1); ?></label></th>
                                    <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[<?php echo $index; ?>][image]" value="<?php echo esc_attr($field['image']); ?>" /></td>
                                    <td><button type="button" class="remove-field">Remove</button></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="theme_demo_link_<?php echo $category . '_' . $index; ?>">Demo Link <?php echo ($index + 1); ?></label></th>
                                    <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[<?php echo $index; ?>][link]" value="<?php echo esc_attr($field['link']); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="theme_demo_button_id_<?php echo $category . '_' . $index; ?>">Button ID <?php echo ($index + 1); ?></label></th>
                                    <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[<?php echo $index; ?>][button_id]" value="<?php echo esc_attr($field['button_id']); ?>" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <hr>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <th scope="row"><label for="theme_demo_image_<?php echo $category; ?>_0">Image URL 1</label></th>
                                <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[0][image]" /></td>
                                <td><button type="button" class="remove-field">Remove</button></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="theme_demo_link_<?php echo $category; ?>_0">Demo Link 1</label></th>
                                <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[0][link]" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="theme_demo_button_id_<?php echo $category; ?>_0">Button ID 1</label></th>
                                <td><input type="text" name="theme_demo_shortcode_fields_<?php echo $category; ?>[0][button_id]" /></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <hr>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                    <button type="button" class="add-field" data-category="<?php echo $category; ?>">Add Another</button>
                </div>
            <?php } ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}


function theme_demo_shortcode_settings()
{
    $categories = array('news', 'e-commerce', 'portfolios', 'companies', 'medicine', 'real-estate');
    foreach ($categories as $category) {
        register_setting('theme_demo_shortcode_options_group', 'theme_demo_shortcode_fields_' . $category, 'theme_demo_shortcode_sanitize');
    }
}
add_action('admin_init', 'theme_demo_shortcode_settings');

function theme_demo_shortcode_sanitize($input)
{
    $sanitized_input = array();
    foreach ($input as $key => $value) {
        $sanitized_input[$key]['image'] = sanitize_text_field($value['image']);
        $sanitized_input[$key]['link'] = esc_url_raw($value['link']);
        $sanitized_input[$key]['button_id'] = sanitize_text_field($value['button_id']);
    }
    return $sanitized_input;
}

function theme_demo_shortcode($atts, $content = null, $tag = '')
{
    $atts = shortcode_atts(array(
        'lang' => 'en',
    ), $atts, $tag);

    $category = str_replace('theme_demo_', '', $tag);
    $fields = get_option('theme_demo_shortcode_fields_' . $category);
    if (empty($fields)) {
        return '<p>No themes available for ' . ucfirst($category) . '.</p>';
    }

    $select_demo_label = $atts['lang'] === 'ar' ? 'اختيار القالب' : 'Select demo';
    $live_show_label = $atts['lang'] === 'ar' ? 'عرض' : 'Live show';

    $output = '<div class="container">';
    foreach ($fields as $field) {
        if (!empty($field['image']) && !empty($field['link']) && !empty($field['button_id'])) {
            $output .= '<div class="column">';
            $output .= '<img src="' . esc_url($field['image']) . '" alt="Demo Image" />';
            $output .= '<div class="button-container">';
            $output .= '<a href="https://webfixup.com/clients/order.php?step=1&amp;productGroup=4"><button id="' . esc_attr($field['button_id']) . '" class="user_selection_chosen">' . $select_demo_label . '</button></a>';
            $output .= '<a href="' . esc_url($field['link']) . '" target="_blank" rel="noopener"><button>' . $live_show_label . '</button></a>';
            $output .= '</div>';
            $output .= '</div>';
        }
    }
    $output .= '</div>';

    return $output;
}

$categories = array('news', 'e-commerce', 'portfolios', 'companies', 'medicine', 'real-estate');
foreach ($categories as $category) {
    add_shortcode('theme_demo_' . $category, 'theme_demo_shortcode');
}
