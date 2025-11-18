<?php
/**
 * Plugin Debug Script
 *
 * Sube este archivo a la raíz de WordPress y accede a:
 * tu-sitio.com/debug-plugin.php
 */

// Carga WordPress.
require_once( dirname(__FILE__) . '/wp-load.php' );

if ( ! current_user_can( 'manage_options' ) ) {
	die( 'No tienes permisos para ver esta página.' );
}

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<title>Debug: WooCommerce Product Slider</title>
	<style>
		body { font-family: Arial, sans-serif; margin: 20px; background: #f0f0f0; }
		.container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
		h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
		h2 { color: #0073aa; margin-top: 30px; }
		.success { color: #46b450; font-weight: bold; }
		.error { color: #dc3232; font-weight: bold; }
		.warning { color: #f56e28; font-weight: bold; }
		pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
		table { width: 100%; border-collapse: collapse; margin: 10px 0; }
		th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
		th { background: #0073aa; color: white; }
		.code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
	</style>
</head>
<body>
	<div class="container">
		<h1>🔍 Diagnóstico: WooCommerce Product Slider</h1>

		<h2>1. Estado del Plugin</h2>
		<?php
		$plugin_file = 'product-slider-plugin/woocommerce-product-slider.php';
		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

		if ( file_exists( $plugin_path ) ) {
			echo '<p class="success">✓ Plugin encontrado en: <code>' . $plugin_path . '</code></p>';

			if ( is_plugin_active( $plugin_file ) ) {
				echo '<p class="success">✓ Plugin está ACTIVO</p>';
			} else {
				echo '<p class="error">✗ Plugin NO está activo</p>';
				echo '<p>👉 Ve a Plugins → Plugins instalados y activa "WooCommerce Product Slider"</p>';
			}
		} else {
			echo '<p class="error">✗ Plugin NO encontrado</p>';
			echo '<p>Ruta esperada: <code>' . $plugin_path . '</code></p>';
		}
		?>

		<h2>2. Custom Post Type Registrado</h2>
		<?php
		if ( post_type_exists( 'wc_product_slider' ) ) {
			echo '<p class="success">✓ Custom Post Type "wc_product_slider" está registrado</p>';
			$cpt = get_post_type_object( 'wc_product_slider' );
			echo '<table>';
			echo '<tr><th>Propiedad</th><th>Valor</th></tr>';
			echo '<tr><td>Label</td><td>' . $cpt->label . '</td></tr>';
			echo '<tr><td>Menu Icon</td><td>' . $cpt->menu_icon . '</td></tr>';
			echo '<tr><td>Show in Menu</td><td>' . ( $cpt->show_in_menu ? 'Sí' : 'No' ) . '</td></tr>';
			echo '<tr><td>Menu Position</td><td>' . $cpt->menu_position . '</td></tr>';
			echo '</table>';
		} else {
			echo '<p class="error">✗ Custom Post Type "wc_product_slider" NO está registrado</p>';
		}
		?>

		<h2>3. Clases del Plugin</h2>
		<?php
		$classes = array(
			'WC_Product_Slider\WC_Product_Slider' => 'Clase principal',
			'WC_Product_Slider\WC_Product_Slider_Loader' => 'Loader',
			'WC_Product_Slider\Admin\WC_Product_Slider_Admin' => 'Admin',
			'WC_Product_Slider\Core\WC_Product_Slider_CPT' => 'CPT',
		);

		echo '<table>';
		echo '<tr><th>Clase</th><th>Estado</th></tr>';
		foreach ( $classes as $class => $desc ) {
			$exists = class_exists( $class );
			echo '<tr>';
			echo '<td>' . $desc . '<br><code>' . $class . '</code></td>';
			echo '<td>' . ( $exists ? '<span class="success">✓ Existe</span>' : '<span class="error">✗ No existe</span>' ) . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		?>

		<h2>4. Métodos de Admin</h2>
		<?php
		if ( class_exists( 'WC_Product_Slider\Admin\WC_Product_Slider_Admin' ) ) {
			$admin_class = 'WC_Product_Slider\Admin\WC_Product_Slider_Admin';
			$methods = array(
				'add_settings_page' => 'Registra página de settings',
				'render_settings_page' => 'Renderiza página de settings',
				'add_plugin_action_links' => 'Agrega enlace en lista de plugins',
			);

			echo '<table>';
			echo '<tr><th>Método</th><th>Descripción</th><th>Estado</th></tr>';
			foreach ( $methods as $method => $desc ) {
				$exists = method_exists( $admin_class, $method );
				echo '<tr>';
				echo '<td><code>' . $method . '()</code></td>';
				echo '<td>' . $desc . '</td>';
				echo '<td>' . ( $exists ? '<span class="success">✓ Existe</span>' : '<span class="error">✗ No existe</span>' ) . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<p class="error">✗ No se puede verificar porque la clase Admin no existe</p>';
		}
		?>

		<h2>5. Hooks Registrados</h2>
		<?php
		global $wp_filter;

		$hooks_to_check = array(
			'admin_menu' => 'Registra menús de admin',
			'plugin_action_links_product-slider-plugin/woocommerce-product-slider.php' => 'Enlaces en lista de plugins',
		);

		echo '<table>';
		echo '<tr><th>Hook</th><th>Descripción</th><th>Callbacks</th></tr>';
		foreach ( $hooks_to_check as $hook => $desc ) {
			echo '<tr>';
			echo '<td><code>' . $hook . '</code></td>';
			echo '<td>' . $desc . '</td>';
			echo '<td>';
			if ( isset( $wp_filter[$hook] ) ) {
				$callbacks = $wp_filter[$hook]->callbacks;
				$count = 0;
				foreach ( $callbacks as $priority => $functions ) {
					$count += count( $functions );
				}
				echo '<span class="success">' . $count . ' callback(s) registrado(s)</span>';
			} else {
				echo '<span class="warning">Sin callbacks</span>';
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
		?>

		<h2>6. Menú Global de WordPress</h2>
		<?php
		global $menu, $submenu;

		echo '<h3>Menú Principal</h3>';
		$found_menu = false;
		if ( ! empty( $menu ) ) {
			foreach ( $menu as $item ) {
				if ( isset( $item[2] ) && strpos( $item[2], 'wc_product_slider' ) !== false ) {
					$found_menu = true;
					echo '<p class="success">✓ Encontrado en menú principal:</p>';
					echo '<pre>' . print_r( $item, true ) . '</pre>';
					break;
				}
			}
		}
		if ( ! $found_menu ) {
			echo '<p class="warning">⚠ No encontrado en menú principal</p>';
		}

		echo '<h3>Submenús de Product Sliders</h3>';
		$parent_slug = 'edit.php?post_type=wc_product_slider';
		if ( isset( $submenu[$parent_slug] ) ) {
			echo '<p class="success">✓ Submenús encontrados:</p>';
			echo '<table>';
			echo '<tr><th>Título</th><th>Slug</th><th>Capacidad</th></tr>';
			foreach ( $submenu[$parent_slug] as $item ) {
				echo '<tr>';
				echo '<td>' . $item[0] . '</td>';
				echo '<td><code>' . $item[2] . '</code></td>';
				echo '<td>' . $item[1] . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<p class="error">✗ No hay submenús registrados para Product Sliders</p>';
		}
		?>

		<h2>7. Información del Archivo Principal</h2>
		<?php
		if ( file_exists( $plugin_path ) ) {
			echo '<table>';
			echo '<tr><th>Propiedad</th><th>Valor</th></tr>';
			echo '<tr><td>Tamaño</td><td>' . filesize( $plugin_path ) . ' bytes</td></tr>';
			echo '<tr><td>Última modificación</td><td>' . date( 'Y-m-d H:i:s', filemtime( $plugin_path ) ) . '</td></tr>';
			echo '<tr><td>Permisos</td><td>' . substr(sprintf('%o', fileperms($plugin_path)), -4) . '</td></tr>';
			echo '</table>';

			// Leer el header del plugin
			$plugin_data = get_plugin_data( $plugin_path );
			echo '<h3>Headers del Plugin</h3>';
			echo '<table>';
			foreach ( $plugin_data as $key => $value ) {
				if ( ! empty( $value ) ) {
					echo '<tr><td>' . $key . '</td><td>' . esc_html( $value ) . '</td></tr>';
				}
			}
			echo '</table>';
		}
		?>

		<h2>8. URL de Acceso a Settings</h2>
		<?php
		if ( post_type_exists( 'wc_product_slider' ) ) {
			$settings_url = admin_url( 'edit.php?post_type=wc_product_slider&page=wc-product-slider-settings' );
			echo '<p>Si todo está correcto, deberías poder acceder a la página de settings en:</p>';
			echo '<p><a href="' . $settings_url . '" class="button button-primary" style="display:inline-block; padding:10px 20px; background:#0073aa; color:white; text-decoration:none; border-radius:4px;">' . $settings_url . '</a></p>';
		}
		?>

		<h2>9. Diagnóstico y Solución</h2>
		<?php
		$issues = array();

		if ( ! is_plugin_active( $plugin_file ) ) {
			$issues[] = '❌ <strong>Plugin no está activo</strong><br>→ Solución: Ve a Plugins → Plugins instalados y activa "WooCommerce Product Slider"';
		}

		if ( ! post_type_exists( 'wc_product_slider' ) ) {
			$issues[] = '❌ <strong>CPT no registrado</strong><br>→ Solución: Desactiva y reactiva el plugin';
		}

		if ( ! isset( $submenu[$parent_slug] ) || empty( $submenu[$parent_slug] ) ) {
			$issues[] = '❌ <strong>Submenú no registrado</strong><br>→ Solución: Desactiva el plugin, borra el caché, y reactiva';
		}

		if ( empty( $issues ) ) {
			echo '<div style="background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:15px; border-radius:4px;">';
			echo '<p style="margin:0;"><strong>✓ Todo parece estar correcto</strong></p>';
			echo '<p style="margin:10px 0 0 0;">El menú "Settings" debería aparecer en:<br><strong>Product Sliders → Settings</strong></p>';
			echo '</div>';
		} else {
			echo '<div style="background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:15px; border-radius:4px;">';
			echo '<p style="margin:0;"><strong>⚠ Se encontraron problemas:</strong></p>';
			echo '<ol style="margin:10px 0 0 20px;">';
			foreach ( $issues as $issue ) {
				echo '<li style="margin:10px 0;">' . $issue . '</li>';
			}
			echo '</ol>';
			echo '</div>';
		}
		?>

	</div>
</body>
</html>
