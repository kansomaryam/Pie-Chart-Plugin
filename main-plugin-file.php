<?php
/*
Plugin Name: Custom Pie Chart Without ACF
Description: Allows users to create customizable pie charts directly from the WordPress admin panel.
Version: 1.0
Author: Your Name
*/

// Register styles and scripts
function custom_pie_chart_enqueue_scripts() {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '2.9.4', true);
    wp_enqueue_script('custom-pie-chart-js', plugin_dir_url(__FILE__) . 'scripts.js', array('chart-js'), '1.0', true);
    wp_enqueue_style('custom-pie-chart-css', plugin_dir_url(__FILE__) . 'styles.css');
}
add_action('wp_enqueue_scripts', 'custom_pie_chart_enqueue_scripts');
add_action('admin_enqueue_scripts', 'custom_pie_chart_enqueue_scripts');

// Add admin menu item
function custom_pie_chart_admin_menu() {
    add_menu_page('Custom Pie Charts', 'Pie Charts', 'manage_options', 'custom-pie-charts', 'custom_pie_chart_admin_page', 'dashicons-chart-pie');
}
add_action('admin_menu', 'custom_pie_chart_admin_menu');

// Admin page for creating pie charts
function custom_pie_chart_admin_page() {
    ?>
    <div class="wrap">
        <h1>Create Custom Pie Chart</h1>
        <form method="post">
            <p><label for="chart_title">Chart Title:</label>
            <input type="text" id="chart_title" name="chart_title" value=""></p>
            <div id="chartDataContainer">
                <!-- Placeholder for dynamic fields -->
            </div>
            <button type="button" onclick="addChartData();">Add Slice</button>
            <p><input type="submit" name="save_chart" value="Save Chart" class="button-primary"></p>
        </form>
        <?php
        if (isset($_POST['save_chart'])) {
            $title = sanitize_text_field($_POST['chart_title']);
            $labels = array_map('sanitize_text_field', $_POST['labels']);
            $values = array_map('intval', $_POST['values']);
            $colors = array_map('sanitize_hex_color', $_POST['colors']);

            $chart_id = 'custom_pie_chart_' . wp_generate_uuid4();
            $chart_data = json_encode(array('labels' => $labels, 'values' => $values, 'colors' => $colors));
            update_option($chart_id, $chart_data);  // Save the data
            
            echo '<div>Chart saved successfully! Use the following shortcode to embed the pie chart: <input type="text" value="[custom_pie_chart id=&quot;' . esc_attr($chart_id) . '&quot;]" readonly></div>';
        }
        ?>
    </div>
    <script>
    function addChartData() {
        const container = document.getElementById('chartDataContainer');
        const index = container.children.length;
        const html = `
            <div class="chart-slice" id="slice_${index}">
                <label>Label: <input type="text" name="labels[]" required></label>
                <label>Value: <input type="number" name="values[]" required></label>
                <label>Color: <input type="color" name="colors[]" required></label>
                <button type="button" onclick="removeChartData('slice_${index}');">Remove</button>
            </div>`;
        container.innerHTML += html;
    }
    function removeChartData(id) {
        const element = document.getElementById(id);
        element.parentNode.removeChild(element);
    }
    </script>
    <?php
}

// Shortcode to display the pie chart
function custom_pie_chart_shortcode($atts) {
    $atts = shortcode_atts(array('id' => ''), $atts);
    $chart_data = get_option($atts['id']);
    if (!$chart_data) {
        return 'No chart configuration found.';
    }
    $data = json_decode($chart_data, true);
    return '<canvas id="customPieChartCanvas" data-chart=\'' . esc_attr($chart_data) . '\'></canvas>';
}
add_shortcode('custom_pie_chart', 'custom_pie_chart_shortcode');
