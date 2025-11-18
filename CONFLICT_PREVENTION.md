# WordPress Conflict Prevention Guidelines

Este documento establece las convenciones estrictas para evitar conflictos con otros plugins/temas.

## ✅ Estado Actual (Implementado)

### 1. Namespaces PHP
- **Namespace principal**: `WC_Product_Slider\`
- **Sub-namespaces**: `WC_Product_Slider\Core\`, `WC_Product_Slider\Admin\`, etc.
- **Estado**: ✅ IMPLEMENTADO correctamente en todas las clases

### 2. Funciones Globales
**Prefijo obligatorio**: `wc_product_slider_`

**Funciones actuales**:
- ✅ `wc_product_slider_activate()`
- ✅ `wc_product_slider_deactivate()`
- ✅ `wc_product_slider_run()`

**Regla**: NUNCA crear funciones globales sin este prefijo.

### 3. Constantes Globales
**Prefijo obligatorio**: `WC_PRODUCT_SLIDER_`

**Constantes actuales**:
- ✅ `WC_PRODUCT_SLIDER_VERSION`
- ✅ `WC_PRODUCT_SLIDER_PLUGIN_DIR`
- ✅ `WC_PRODUCT_SLIDER_PLUGIN_URL`
- ✅ `WC_PRODUCT_SLIDER_PLUGIN_BASENAME`

**Regla**: NUNCA crear constantes globales sin este prefijo.

## 🚧 Pendiente de Implementación (Fases Futuras)

### 4. Hooks Personalizados (Filtros y Acciones)
**Prefijo obligatorio**: `wc_product_slider_`

**Ejemplos correctos**:
```php
// Filtros
apply_filters( 'wc_product_slider_slider_config', $config );
apply_filters( 'wc_product_slider_slide_html', $html, $slide_id );

// Acciones
do_action( 'wc_product_slider_before_render', $slider_id );
do_action( 'wc_product_slider_after_save', $slider_id, $data );
```

**Ejemplos INCORRECTOS** (pueden causar conflictos):
```php
// ❌ NO HACER - Muy genérico
apply_filters( 'slider_config', $config );
do_action( 'before_render', $slider_id );
```

### 5. CSS Classes y IDs
**Prefijo obligatorio**: `wc-ps-` (WooCommerce Product Slider)

**Ejemplos correctos**:
```css
.wc-ps-slider { }
.wc-ps-slide { }
.wc-ps-navigation { }
#wc-ps-admin-panel { }
```

**Ejemplos INCORRECTOS**:
```css
/* ❌ NO HACER - Muy genérico */
.slider { }
.slide { }
.navigation { }
```

### 6. JavaScript Variables Globales
**Evitar variables globales**. Si es absolutamente necesario, usar namespace:

**Correcto**:
```javascript
window.WCProductSlider = {
    init: function() { },
    config: { }
};
```

**INCORRECTO**:
```javascript
// ❌ NO HACER
var slider = { };
var config = { };
```

### 7. Enqueue Scripts y Styles
**Handles únicos obligatorios**: `wc-product-slider-{nombre}`

**Ejemplos correctos**:
```php
wp_enqueue_script( 'wc-product-slider-admin', ... );
wp_enqueue_style( 'wc-product-slider-swiper', ... );
wp_enqueue_script( 'wc-product-slider-frontend', ... );
```

**INCORRECTO**:
```php
// ❌ NO HACER
wp_enqueue_script( 'slider', ... );
wp_enqueue_style( 'admin', ... );
```

### 8. Base de Datos

#### Opciones (wp_options)
**Prefijo obligatorio**: `wc_product_slider_`

**Ejemplos correctos**:
```php
add_option( 'wc_product_slider_settings', $settings );
get_option( 'wc_product_slider_version' );
update_option( 'wc_product_slider_cache_key', $key );
```

#### Tablas Personalizadas (si necesario)
**Prefijo obligatorio**: `{$wpdb->prefix}wc_product_slider_`

**Ejemplos correctos**:
```php
$table_name = $wpdb->prefix . 'wc_product_slider_analytics';
$table_name = $wpdb->prefix . 'wc_product_slider_cache';
```

#### Post Meta Keys
**Prefijo obligatorio**: `_wc_ps_`

**Ejemplos correctos**:
```php
add_post_meta( $post_id, '_wc_ps_slider_config', $config );
get_post_meta( $post_id, '_wc_ps_products', true );
update_post_meta( $post_id, '_wc_ps_style', $style );
```

### 9. Shortcodes
**Prefijo obligatorio**: `wc_product_slider_` o `wc_ps_`

**Ejemplos correctos**:
```php
add_shortcode( 'wc_product_slider', 'render_callback' );
add_shortcode( 'wc_ps_slider', 'render_callback' );
```

**INCORRECTO**:
```php
// ❌ NO HACER - Muy genérico
add_shortcode( 'slider', 'render_callback' );
add_shortcode( 'products', 'render_callback' );
```

### 10. REST API Endpoints
**Namespace obligatorio**: `wc-product-slider/v1`

**Ejemplos correctos**:
```php
register_rest_route( 'wc-product-slider/v1', '/sliders', ... );
register_rest_route( 'wc-product-slider/v1', '/slider/(?P<id>\d+)', ... );
```

### 11. AJAX Actions
**Prefijo obligatorio**: `wc_product_slider_`

**Ejemplos correctos**:
```php
add_action( 'wp_ajax_wc_product_slider_save', 'callback' );
add_action( 'wp_ajax_nopriv_wc_product_slider_load', 'callback' );
```

### 12. Transients y Cache
**Prefijo obligatorio**: `wc_ps_`

**Ejemplos correctos**:
```php
set_transient( 'wc_ps_slider_' . $id, $data, HOUR_IN_SECONDS );
get_transient( 'wc_ps_products_cache' );
delete_transient( 'wc_ps_temp_' . $key );
```

### 13. Custom Post Types y Taxonomies
**Prefijo obligatorio**: `wc_product_slider_` o `wc_ps_`

**Implementado**:
- ✅ CPT: `wc_product_slider` (máximo 20 caracteres en WordPress)

**Si agregamos taxonomías**:
```php
register_taxonomy( 'wc_ps_category', ... );
register_taxonomy( 'wc_ps_tag', ... );
```

### 14. Nonces
**Prefijo obligatorio**: `wc_product_slider_` o `wc_ps_`

**Ejemplos correctos**:
```php
wp_create_nonce( 'wc_product_slider_save_slider' );
wp_verify_nonce( $_POST['nonce'], 'wc_ps_delete_slider' );
```

### 15. Roles y Capabilities
**Prefijo obligatorio**: `wc_product_slider_`

**Si creamos capabilities custom**:
```php
add_cap( 'manage_wc_product_sliders' );
add_cap( 'edit_wc_product_slider' );
add_cap( 'delete_wc_product_slider' );
```

## 🔍 Checklist de Revisión

Antes de hacer commit de nuevas features, verificar:

- [ ] ¿Todas las funciones globales tienen prefijo `wc_product_slider_`?
- [ ] ¿Todas las constantes tienen prefijo `WC_PRODUCT_SLIDER_`?
- [ ] ¿Todas las clases usan namespace `WC_Product_Slider\`?
- [ ] ¿Todos los hooks tienen prefijo `wc_product_slider_`?
- [ ] ¿Todas las clases CSS tienen prefijo `wc-ps-`?
- [ ] ¿Todos los handles de scripts/styles tienen prefijo `wc-product-slider-`?
- [ ] ¿Todas las opciones de DB tienen prefijo `wc_product_slider_`?
- [ ] ¿Todos los post meta tienen prefijo `_wc_ps_`?
- [ ] ¿Todos los shortcodes tienen prefijo apropiado?
- [ ] ¿Todos los AJAX actions tienen prefijo `wc_product_slider_`?
- [ ] ¿Todos los transients tienen prefijo `wc_ps_`?
- [ ] ¿Todos los nonces tienen prefijo apropiado?

## 🛡️ Protección Adicional

### Evitar Contaminar el Global Scope

```php
// ✅ CORRECTO - Todo en namespace
namespace WC_Product_Slider\Admin;

class Settings {
    // ...
}

// ❌ INCORRECTO - Clase global
class Settings {
    // ...
}
```

### Encapsular JavaScript

```javascript
// ✅ CORRECTO - IIFE para encapsular
(function($) {
    'use strict';

    var WCProductSlider = {
        init: function() { }
    };

    $(document).ready(function() {
        WCProductSlider.init();
    });

})(jQuery);

// ❌ INCORRECTO - Variables en global scope
var init = function() { };
$(document).ready(init);
```

### Defensivo con jQuery

```javascript
// ✅ CORRECTO - No asume $ es jQuery
(function($) {
    $('.wc-ps-slider').slider();
})(jQuery);

// ❌ INCORRECTO - Puede fallar si otro plugin modifica $
$('.slider').slider();
```

## 📝 Notas Importantes

1. **WordPress tiene 20 caracteres de límite para CPT slugs** - Por eso usamos `wc_product_slider` (18 chars) no `woocommerce_product_slider`

2. **Prefijos diferentes según contexto**:
   - PHP functions/constants: `wc_product_slider_`
   - CSS/HTML: `wc-ps-`
   - DB options: `wc_product_slider_`
   - Post meta: `_wc_ps_` (underscore previene mostrar en custom fields UI)
   - Transients: `wc_ps_` (más corto por límites de longitud)

3. **Namespace PHP no previene conflictos de functions globales** - Por eso ambos son necesarios

4. **Testing**: Siempre probar el plugin con otros plugins populares instalados (Yoast SEO, WooCommerce, Contact Form 7, etc.)

## 🔧 Herramientas de Auditoría

```bash
# Buscar funciones globales sin prefijo
grep -rn "^function [^wc_product_slider]" includes/

# Buscar constantes sin prefijo
grep -rn "^define(" includes/ | grep -v "WC_PRODUCT_SLIDER"

# Buscar add_action/add_filter sin prefijo en hooks custom
grep -rn "add_action\|add_filter" includes/ | grep -v "wc_product_slider_"

# Buscar enqueue sin prefijo
grep -rn "wp_enqueue" includes/ | grep -v "wc-product-slider"
```

---

**Mantenido por**: Equipo de desarrollo
**Última actualización**: 2025-11-17
**Versión**: 1.0.0
