<?php
/**
 * Plugin Name: WC Product Slider - Debug Test
 * Description: Prueba rápida para diagnosticar problemas del plugin
 * Version: 1.0.0
 * Author: Debug
 */

add_action('admin_notices', function() {
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="notice notice-info">';
    echo '<h2>🔍 Diagnóstico WooCommerce Product Slider</h2>';

    // 1. Check WooCommerce
    if (class_exists('WooCommerce')) {
        echo '<p>✅ <strong>WooCommerce está activo</strong></p>';
    } else {
        echo '<p>❌ <strong>WooCommerce NO está activo</strong> - El plugin requiere WooCommerce</p>';
    }

    // 2. Check CPT
    if (post_type_exists('wc_product_slider')) {
        echo '<p>✅ <strong>Custom Post Type "wc_product_slider" está registrado</strong></p>';
        $url = admin_url('edit.php?post_type=wc_product_slider');
        echo '<p>📌 <a href="' . $url . '" class="button button-primary">Ir a Product Sliders</a></p>';
    } else {
        echo '<p>❌ <strong>Custom Post Type "wc_product_slider" NO está registrado</strong></p>';
        echo '<p>→ Esto explica por qué no ves el menú</p>';
    }

    // 3. Check plugin active
    $plugin_file = 'product-slider-plugin/woocommerce-product-slider.php';
    if (is_plugin_active($plugin_file)) {
        echo '<p>✅ <strong>Plugin está activo</strong></p>';
    } else {
        echo '<p>❌ <strong>Plugin NO está activo</strong></p>';
    }

    // 4. Check classes
    $classes_ok = true;
    if (class_exists('WC_Product_Slider\WC_Product_Slider')) {
        echo '<p>✅ Clase principal existe</p>';
    } else {
        echo '<p>❌ Clase principal NO existe</p>';
        $classes_ok = false;
    }

    if (class_exists('WC_Product_Slider\Core\WC_Product_Slider_CPT')) {
        echo '<p>✅ Clase CPT existe</p>';
    } else {
        echo '<p>❌ Clase CPT NO existe</p>';
        $classes_ok = false;
    }

    // 5. Check submenu
    global $submenu;
    $parent_slug = 'edit.php?post_type=wc_product_slider';
    if (isset($submenu[$parent_slug])) {
        echo '<p>✅ <strong>Submenú registrado con ' . count($submenu[$parent_slug]) . ' items:</strong></p>';
        echo '<ul>';
        foreach ($submenu[$parent_slug] as $item) {
            echo '<li>' . $item[0] . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>⚠️ <strong>Submenú NO registrado</strong></p>';
    }

    // Recommendation
    echo '<hr>';
    echo '<h3>🔧 Solución:</h3>';
    if (!class_exists('WooCommerce')) {
        echo '<p><strong>1. Instala y activa WooCommerce</strong></p>';
    } else if (!post_type_exists('wc_product_slider')) {
        echo '<p><strong>1. Desactiva el plugin "WooCommerce Product Slider"</strong></p>';
        echo '<p><strong>2. Reactiva el plugin</strong></p>';
        echo '<p>Esto debería registrar el Custom Post Type y hacer aparecer el menú.</p>';
    } else {
        echo '<p>✅ Todo parece estar bien. El menú debería aparecer en la barra lateral como "Product Sliders"</p>';
    }

    echo '</div>';
});
