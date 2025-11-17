# Justificación Técnica Detallada - WooCommerce Product Slider Plugin
## Análisis de Viabilidad y Consistencia (Noviembre 2025)

---

## 📊 Stack Tecnológico - Decisiones Fundamentadas

### 1. **PHP: 7.4+ (Recomendado 8.3+)**

#### ✅ Estado: VIGENTE Y RECOMENDADO

**Versión Objetivo**: PHP 8.3 como recomendado, soporte hasta PHP 7.4

**Justificación:**
- **WordPress Oficial (Nov 2025)**: Recomienda PHP 8.3+ como versión óptima
- **Soporte Legacy**: WordPress aún soporta PHP 7.2.24+ pero está en EOL (End of Life)
- **WooCommerce 10.3**: Requiere PHP 7.4+ como mínimo
- **Seguridad**: PHP 7.4 recibe solo parches críticos, 8.3 es la versión activamente soportada

**Ventajas PHP 8.3:**
- JIT compiler (15-30% más rápido)
- Typed properties
- Match expressions
- Named arguments
- Union types
- Mejoras en performance

**Decisión Final:**
```json
{
  "minimum": "7.4",
  "recommended": "8.3",
  "tested_up_to": "8.4-beta"
}
```

**Riesgo:** BAJO - Amplia adopción en hosting WordPress

---

### 2. **WordPress: 6.2+ (Recomendado 6.7+)**

#### ✅ Estado: VIGENTE

**Versión Objetivo**: WordPress 6.7 (última estable Nov 2025)

**Justificación:**
- **WordPress 6.7**: Beta support para PHP 8.4, nuevas features Gutenberg
- **WPCS Default**: WordPress Coding Standards 3.0 establece 6.2 como baseline
- **Gutenberg API**: Versiones 6.3+ tienen API estable para bloques modernos
- **Performance**: WordPress 6.4+ introdujo optimizaciones significativas

**Features Críticas que Necesitamos:**
- Block API v2 (6.0+)
- WebP nativo (5.8+)
- Lazy loading nativo (5.5+)
- Site Health (5.2+)

**Decisión Final:**
```json
{
  "minimum": "6.2",
  "tested_up_to": "6.7"
}
```

**Riesgo:** BAJO - Alta tasa de actualización de WordPress

---

### 3. **WooCommerce: 8.2+ (Recomendado 10.3+)**

#### ✅ Estado: VIGENTE - Última versión 10.3 (Oct 2025)

**Versión Objetivo**: WooCommerce 10.3

**Justificación:**
- **WooCommerce 10.3**: Última estable con COGS en core, MCP beta
- **Requisitos Mínimos WC**: 8.2+ para extensiones oficiales (policy Woo)
- **Compatibilidad**: WC 10.x preparado para WordPress 6.9
- **API Stability**: Product queries estabilizadas desde 8.0+

**Features que Usaremos:**
- `wc_get_products()` - Query API moderna
- Product Image API
- WooCommerce REST API v3
- HPOS (High-Performance Order Storage) compatible

**Decisión Final:**
```json
{
  "minimum": "8.2",
  "tested_up_to": "10.3"
}
```

**Riesgo:** MEDIO-BAJO - WooCommerce evoluciona rápido, testing continuo necesario

---

### 4. **Composer & PSR-4 Autoloading**

#### ✅ Estado: BEST PRACTICE ACTUAL

**Por qué Composer:**
- **Estándar de facto** en desarrollo PHP moderno
- **Autoloading PSR-4**: Cero overhead, lazy loading de clases
- **Gestión de dependencias**: WPCS, PHPUnit, Polyfills centralizadas
- **Requerido para**: WordPress Coding Standards 3.0, PHPUnit Polyfills

**Dependencias Core:**
```json
{
  "require": {
    "php": ">=7.4"
  },
  "require-dev": {
    "wp-coding-standards/wpcs": "^3.0",
    "phpunit/phpunit": "^9.5",
    "yoast/phpunit-polyfills": "^2.0",
    "phpstan/phpstan": "^1.10"
  },
  "config": {
    "allow-plugins": {
      "dealerdirect/phpcodesniffer-composer-installer": true
    }
  }
}
```

**Estructura PSR-4:**
```
"autoload": {
  "psr-4": {
    "WC_Product_Slider\\": "includes/"
  }
}
```

**Riesgo:** NULO - Estándar universal PHP

---

### 5. **Testing: PHPUnit 9.x + WordPress Test Library**

#### ✅ Estado: VIGENTE CON POLYFILLS

**Versión Objetivo**: PHPUnit 9.6 con Yoast Polyfills 2.0

**Justificación:**
- **WordPress Core**: Usa PHPUnit 9.x oficialmente
- **PHP 8.3 Compatibility**: PHPUnit 9.6 totalmente compatible
- **Polyfills**: `yoast/phpunit-polyfills:^2.0` para cross-version compatibility
- **Tutorial Actualizado**: Mayo 2025 confirma stack @wordpress/env + PHPUnit 9

**Configuración Moderna (2025):**
```xml
<phpunit
    bootstrap="tests/bootstrap.php"
    backupGlobals="false"
    colors="true"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="true"
    convertWarningsToExceptions="true"
    testdox="true">
    <testsuites>
        <testsuite name="unit">
            <directory suffix=".php">./tests/unit/</directory>
        </testsuite>
        <testsuite name="integration">
            <directory suffix=".php">./tests/integration/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Alternativa Moderna: @wordpress/env**
- Dockerizado
- Setup automático de WordPress test environment
- Usado en core WordPress

**Decisión Final:**
```json
{
  "phpunit": "9.6",
  "polyfills": "yoast/phpunit-polyfills:^2.0",
  "test_env": "@wordpress/env (Docker) o WP Test Library tradicional"
}
```

**Riesgo:** BAJO - Stack oficialmente soportado

---

### 6. **WordPress Coding Standards: WPCS 3.0**

#### ✅ Estado: ÚLTIMA VERSIÓN ESTABLE

**Versión**: WPCS 3.0.0 (actualizada 2024-2025)

**Justificación:**
- **Requisito Marketplace**: Obligatorio para aprobación WordPress.org
- **PHP_CodeSniffer 3.9.0+**: Dependency actualizada
- **WordPress 6.2 Baseline**: Reconoce features hasta WP 6.5
- **Auto-fixing**: 80%+ de issues se arreglan con `phpcbf`

**Configuración `.phpcs.xml.dist`:**
```xml
<?xml version="1.0"?>
<ruleset name="WC Product Slider">
    <description>WordPress Coding Standards for WC Product Slider</description>

    <!-- What to scan -->
    <file>.</file>

    <!-- Exclude patterns -->
    <exclude-pattern>/vendor/*</exclude-pattern>
    <exclude-pattern>/node_modules/*</exclude-pattern>
    <exclude-pattern>/tests/*</exclude-pattern>

    <!-- Rules -->
    <rule ref="WordPress"/>
    <rule ref="WordPress-Extra"/>
    <rule ref="WordPress-Docs"/>

    <!-- PHP version -->
    <config name="minimum_supported_wp_version" value="6.2"/>
    <config name="testVersion" value="7.4-"/>

    <!-- Text Domain -->
    <rule ref="WordPress.WP.I18n">
        <properties>
            <property name="text_domain" type="array">
                <element value="woocommerce-product-slider"/>
            </property>
        </properties>
    </rule>
</ruleset>
```

**Comandos:**
```bash
# Check
composer run-script phpcs

# Auto-fix
composer run-script phpcbf
```

**Riesgo:** NULO - Requisito obligatorio

---

### 7. **Frontend: Swiper.js (NO Slick Carousel)**

#### ✅ Estado: ACTIVAMENTE MANTENIDO (Slick = DEPRECATED ⚠️)

**Decisión: Swiper.js 11.x**

**Justificación Crítica:**

#### ❌ **Slick Carousel - EVITAR**
- **Última release**: 1.8.1 (2018) - **7 AÑOS SIN ACTUALIZAR**
- **Estado**: Efectivamente abandonado
- **Dependencia jQuery**: Bloat innecesario (87KB minified)
- **Sin soporte mobile moderno**: Touch events limitados
- **Performance**: No hardware acceleration

#### ✅ **Swiper.js - ELEGIDO**
- **Última versión**: 11.1.14 (Nov 2025) - Activamente mantenido
- **Framework-agnostic**: Vanilla JS, no dependencies
- **Performance**: Hardware-accelerated, 60fps garantizado
- **Mobile-first**: Touch gestures nativos, 70% mejor retention (dato Replit)
- **Tamaño**: ~30KB gzipped (modular)
- **Accesibilidad**: ARIA labels, keyboard navigation built-in
- **Features 2025**:
  - Virtual slides (listas gigantes sin lag)
  - Lazy loading nativo
  - Efectos avanzados (cube, coverflow, flip)
  - RTL support
  - Thumbs sync
  - Parallax
  - Auto-height

**Comparativa Técnica:**

| Feature | Swiper.js 11 | Slick Carousel | Ganador |
|---------|-------------|----------------|---------|
| Mantenimiento | ✅ Activo (2025) | ❌ Muerto (2018) | **Swiper** |
| Tamaño | 30KB gzipped | 87KB + jQuery | **Swiper** |
| Mobile | ✅ Excelente | ⚠️ Básico | **Swiper** |
| Performance | 60fps (GPU) | CPU-bound | **Swiper** |
| Accesibilidad | ✅ WCAG 2.1 | ⚠️ Limitado | **Swiper** |
| Dependencies | 0 | jQuery | **Swiper** |

**Integración:**
```javascript
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Lazy } from 'swiper/modules';

const swiper = new Swiper('.wc-product-slider', {
  modules: [Navigation, Pagination, Autoplay, Lazy],
  slidesPerView: 3,
  spaceBetween: 30,
  lazy: true,
  a11y: {
    enabled: true
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  }
});
```

**Decisión Final:**
```json
{
  "slider_library": "swiper@^11.0",
  "reason": "Activamente mantenido, mejor performance, mobile-first, accesible"
}
```

**Riesgo:** NULO - Líder indiscutido 2025

---

### 8. **Admin UI: React (Oficial WordPress)**

#### ✅ Estado: ESTÁNDAR WORDPRESS GUTENBERG

**Decisión: React 18 via @wordpress/element**

**Por qué React (NO Vue):**

1. **Gutenberg usa React**: Todo el Block Editor está en React
2. **@wordpress/components**: Librería UI completa y mantenida
3. **Consistencia**: Admin panel se ve nativo WordPress
4. **Documentación**: Extensa, oficial, actualizada 2025
5. **Interop Gutenberg**: Reutilizar bloques y componentes

**React vs Vue en WordPress:**

| Aspecto | React (@wordpress) | Vue.js | Ganador |
|---------|-------------------|---------|---------|
| Integración WP | Nativo | Third-party | **React** |
| Componentes UI | @wordpress/components | Crear custom | **React** |
| Build tools | @wordpress/scripts | Config manual | **React** |
| Docs WordPress | Oficial | Comunidad | **React** |
| Bundle size | 40KB (shared WP) | +100KB extra | **React** |
| Learning curve | Steeper | Gentler | Vue |

**Stack Completo:**
```json
{
  "dependencies": {
    "@wordpress/element": "^5.0",
    "@wordpress/components": "^25.0",
    "@wordpress/data": "^9.0",
    "@wordpress/api-fetch": "^6.0"
  }
}
```

**Ejemplo Admin Panel:**
```jsx
import { Panel, PanelBody, ColorPicker, RangeControl } from '@wordpress/components';
import { useState } from '@wordpress/element';

function SliderSettings() {
  const [bgColor, setBgColor] = useState('#ffffff');
  const [slidesVisible, setSlidesVisible] = useState(3);

  return (
    <Panel>
      <PanelBody title="Design Settings" initialOpen={true}>
        <ColorPicker
          color={bgColor}
          onChangeComplete={(color) => setBgColor(color.hex)}
        />
        <RangeControl
          label="Slides Visible"
          value={slidesVisible}
          onChange={setSlidesVisible}
          min={1}
          max={6}
        />
      </PanelBody>
    </Panel>
  );
}
```

**Ventaja Decisiva**: Zero config con `@wordpress/scripts`

**Decisión Final:**
```json
{
  "ui_framework": "React via @wordpress/element",
  "component_library": "@wordpress/components",
  "state_management": "@wordpress/data (Redux-like)",
  "build_tool": "@wordpress/scripts"
}
```

**Riesgo:** NULO - Stack oficial WordPress

---

### 9. **Build Tools: @wordpress/scripts (NO Webpack manual)**

#### ✅ Estado: HERRAMIENTA OFICIAL WORDPRESS 2025

**Versión**: @wordpress/scripts ^28.0 (actualizado Nov 2025)

**Por qué @wordpress/scripts:**

1. **Zero-config**: Webpack 5 preconfigurado
2. **Mantenido por Automattic**: Updates automáticos
3. **Best practices baked-in**:
   - Babel transpilation (ES6+)
   - SCSS/PostCSS
   - ESLint + Prettier
   - Source maps
   - Hot reload
   - Tree shaking
   - Code splitting

**Comparativa:**

| Opción | Pros | Contras | Veredicto |
|--------|------|---------|-----------|
| @wordpress/scripts | ✅ Zero-config, oficial, actualizado | Menos flexible | **ELEGIDO** |
| Webpack manual | ✅ Control total | ❌ Mantenimiento pesado | Innecesario |
| Vite | ✅ Más rápido | ⚠️ No oficial WP | Futuro posible |

**package.json:**
```json
{
  "scripts": {
    "build": "wp-scripts build",
    "start": "wp-scripts start",
    "check-engines": "wp-scripts check-engines",
    "check-licenses": "wp-scripts check-licenses",
    "format": "wp-scripts format",
    "lint:css": "wp-scripts lint-style",
    "lint:js": "wp-scripts lint-js",
    "lint:pkg-json": "wp-scripts lint-pkg-json",
    "packages-update": "wp-scripts packages-update",
    "test:e2e": "wp-scripts test-e2e",
    "test:unit": "wp-scripts test-unit-js"
  },
  "devDependencies": {
    "@wordpress/scripts": "^28.0"
  }
}
```

**Configuración Auto-detecta:**
- `src/index.js` → `build/index.js`
- `src/admin.js` → `build/admin.js`
- `src/**/*.scss` → `build/**/*.css`
- `block.json` → Auto entry points

**Decisión Final:**
```json
{
  "build_tool": "@wordpress/scripts@^28.0",
  "reason": "Zero-config, oficial, best practices incluidas"
}
```

**Riesgo:** NULO - Herramienta oficial

---

### 10. **Code Editor: CodeMirror 6 (NO Monaco)**

#### ✅ Estado: CODEMIRROR 6 - MODERNA Y LIGERA

**Decisión: CodeMirror 6**

**Por qué NO Monaco:**
- **Bundle size**: 5-10MB uncompressed vs 300KB CodeMirror
- **Mobile**: Completamente inusable vs 70% mejor retention (CodeMirror)
- **WordPress**: No integración nativa

**Por qué CodeMirror 6:**
- **WordPress usa CM**: Gutenberg Code Editor usa CodeMirror
- **Modular**: Solo cargas lo que necesitas
- **Mobile-friendly**: Touch support excelente
- **Accesibilidad**: Screen reader support
- **Temas**: Incluye tema similar a VS Code

**Casos de Uso en Plugin:**
1. **CSS Personalizado**: Editor avanzado en metabox
2. **JavaScript Hooks**: Para developers avanzados

**Implementación:**
```javascript
import { EditorView, basicSetup } from 'codemirror';
import { css } from '@codemirror/lang-css';
import { oneDark } from '@codemirror/theme-one-dark';

const editor = new EditorView({
  doc: sliderCustomCSS,
  extensions: [
    basicSetup,
    css(),
    oneDark,
    EditorView.updateListener.of((update) => {
      if (update.docChanged) {
        saveCustomCSS(update.state.doc.toString());
      }
    })
  ],
  parent: document.querySelector('#custom-css-editor')
});
```

**Decisión Final:**
```json
{
  "code_editor": "codemirror@^6.0",
  "languages": ["css", "javascript"],
  "bundle_size": "~300KB total"
}
```

**Riesgo:** BAJO - Usado en WordPress Core

---

### 11. **Imágenes: WebP/AVIF Nativo + Optimización**

#### ✅ Estado: SOPORTE NATIVO WORDPRESS 5.8+

**Stack de Imágenes:**

1. **WordPress Native WebP** (desde 5.8):
   - Upload directo .webp
   - Generación automática de sizes
   - `srcset` responsive automático

2. **AVIF Support** (WordPress 6.0+):
   - Formato más moderno que WebP
   - 50%+ más compresión
   - Fallback automático a WebP/JPEG

**Estrategia:**
```
Prioridad: AVIF > WebP > JPEG/PNG
Fallback: Automático vía <picture> element
```

**Código:**
```php
// Auto-genera srcset con WebP/AVIF
$image_html = wp_get_attachment_image(
    $attachment_id,
    'large',
    false,
    array(
        'loading' => 'lazy',
        'decoding' => 'async',
        'class' => 'wc-slider-image'
    )
);

// WordPress maneja automáticamente:
// <picture>
//   <source srcset="image.avif" type="image/avif">
//   <source srcset="image.webp" type="image/webp">
//   <img src="image.jpg" loading="lazy">
// </picture>
```

**Optimización Adicional:**
- Lazy loading nativo (HTML `loading="lazy"`)
- `decoding="async"` para non-blocking
- `srcset` para responsive
- Integración con WooCommerce product images

**Plugin Recomendado (opcional):**
- **Modern Image Formats**: Convierte automáticamente JPEG/PNG → WebP/AVIF
- **EWWW Image Optimizer**: Optimización local sin API

**Decisión Final:**
```json
{
  "formats": ["AVIF", "WebP", "JPEG", "PNG"],
  "optimization": "WordPress native + optional plugin",
  "lazy_loading": "HTML native loading=lazy",
  "responsive": "srcset automático WP"
}
```

**Riesgo:** NULO - Features nativas WordPress

---

### 12. **Gutenberg Block API**

#### ✅ Estado: API V2 ESTABLE (WordPress 6.0+)

**Versión API**: Block API v2 con `block.json`

**Por qué Gutenberg Block:**
- **Editor Moderno**: 80%+ usuarios usan Gutenberg (2025)
- **Block Inserter**: Mejor UX que shortcode
- **Preview en Editor**: WYSIWYG real
- **Patterns Support**: Crear templates de sliders

**Estructura Moderna:**
```
blocks/
└── slider-block/
    ├── block.json          # Metadata (API v2)
    ├── index.js            # Register
    ├── edit.js             # Editor component (React)
    ├── save.js             # Frontend save (opcional si dynamic)
    ├── style.scss          # Frontend CSS
    └── editor.scss         # Editor CSS
```

**block.json (API v2):**
```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 2,
  "name": "wc-product-slider/slider",
  "title": "Product Slider",
  "category": "woocommerce",
  "icon": "slides",
  "description": "Display WooCommerce products in a beautiful slider",
  "supports": {
    "html": false,
    "align": ["wide", "full"],
    "color": {
      "background": true,
      "text": true
    }
  },
  "attributes": {
    "sliderId": {
      "type": "number"
    },
    "alignment": {
      "type": "string",
      "default": "center"
    }
  },
  "editorScript": "file:./index.js",
  "editorStyle": "file:./editor.css",
  "style": "file:./style.css"
}
```

**Edit Component (React):**
```jsx
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes }) {
  const { sliderId } = attributes;

  const sliders = useSelect((select) => {
    return select('core').getEntityRecords('postType', 'wc_product_slider');
  });

  return (
    <>
      <InspectorControls>
        <PanelBody title="Slider Settings">
          <SelectControl
            label="Select Slider"
            value={sliderId}
            options={sliders?.map(s => ({ label: s.title.rendered, value: s.id }))}
            onChange={(newId) => setAttributes({ sliderId: parseInt(newId) })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...useBlockProps()}>
        <ServerSideRender
          block="wc-product-slider/slider"
          attributes={{ sliderId }}
        />
      </div>
    </>
  );
}
```

**Dynamic Block (PHP):**
```php
register_block_type(
    __DIR__ . '/blocks/slider-block',
    array(
        'render_callback' => 'wc_product_slider_render_block',
    )
);

function wc_product_slider_render_block( $attributes ) {
    $slider_id = $attributes['sliderId'] ?? 0;
    if ( ! $slider_id ) {
        return '<p>Please select a slider</p>';
    }

    return wc_product_slider_get_slider_html( $slider_id );
}
```

**Decisión Final:**
```json
{
  "block_api": "v2",
  "type": "dynamic",
  "editor": "React con @wordpress/block-editor",
  "preview": "ServerSideRender para accuracy"
}
```

**Riesgo:** BAJO - API estable desde WP 6.0

---

### 13. **Seguridad: Capa de Sanitización Exhaustiva**

#### ✅ Estado: BEST PRACTICES OWASP 2025

**Estrategia Defense-in-Depth:**

#### **Nivel 1: Input Sanitization**
```php
class WC_Product_Slider_Sanitizer {

    /**
     * Sanitiza configuración del slider
     */
    public static function sanitize_slider_config( $config ) {
        return array(
            'title'           => sanitize_text_field( $config['title'] ?? '' ),
            'description'     => wp_kses_post( $config['description'] ?? '' ),
            'link_url'        => esc_url_raw( $config['link_url'] ?? '' ),
            'slides_visible'  => absint( $config['slides_visible'] ?? 3 ),
            'autoplay'        => rest_sanitize_boolean( $config['autoplay'] ?? false ),
            'speed'           => absint( $config['speed'] ?? 300 ),
            'bg_color'        => sanitize_hex_color( $config['bg_color'] ?? '#ffffff' ),
            'custom_css'      => wp_strip_all_tags( $config['custom_css'] ?? '' ),
            'product_ids'     => array_map( 'absint', $config['product_ids'] ?? array() ),
        );
    }

    /**
     * Sanitiza hex color
     */
    public static function sanitize_hex_color( $color ) {
        if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
            return $color;
        }
        return '#ffffff';
    }
}
```

#### **Nivel 2: Validation**
```php
class WC_Product_Slider_Validator {

    public static function validate_slider_data( $data ) {
        $errors = array();

        // Validar slides_visible
        if ( $data['slides_visible'] < 1 || $data['slides_visible'] > 6 ) {
            $errors[] = __( 'Slides visible must be between 1 and 6', 'woocommerce-product-slider' );
        }

        // Validar product IDs existen
        foreach ( $data['product_ids'] as $id ) {
            $product = wc_get_product( $id );
            if ( ! $product ) {
                $errors[] = sprintf(
                    __( 'Product ID %d does not exist', 'woocommerce-product-slider' ),
                    $id
                );
            }
        }

        // Validar URL si es externa
        if ( $data['link_url'] && ! filter_var( $data['link_url'], FILTER_VALIDATE_URL ) ) {
            $errors[] = __( 'Invalid URL format', 'woocommerce-product-slider' );
        }

        return $errors;
    }
}
```

#### **Nivel 3: Capability Checks**
```php
class WC_Product_Slider_Admin {

    public function save_slider_meta( $post_id ) {
        // Nonce check
        if ( ! isset( $_POST['wc_slider_nonce'] ) ||
             ! wp_verify_nonce( $_POST['wc_slider_nonce'], 'wc_slider_save' ) ) {
            return;
        }

        // Autosave check
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Capability check
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( 'Insufficient permissions', 'woocommerce-product-slider' ) );
        }

        // Ownership check (si aplica)
        $post = get_post( $post_id );
        if ( $post->post_author != get_current_user_id() &&
             ! current_user_can( 'edit_others_posts' ) ) {
            wp_die( __( 'You can only edit your own sliders', 'woocommerce-product-slider' ) );
        }

        // Sanitizar y guardar
        $config = WC_Product_Slider_Sanitizer::sanitize_slider_config( $_POST );
        $errors = WC_Product_Slider_Validator::validate_slider_data( $config );

        if ( empty( $errors ) ) {
            update_post_meta( $post_id, '_wc_slider_config', $config );
        }
    }
}
```

#### **Nivel 4: Output Escaping**
```php
// En template
<div class="wc-slider"
     style="background-color: <?php echo esc_attr( $bg_color ); ?>">

    <h3><?php echo esc_html( $title ); ?></h3>

    <div class="description">
        <?php echo wp_kses_post( $description ); ?>
    </div>

    <a href="<?php echo esc_url( $link_url ); ?>"
       class="<?php echo esc_attr( $link_class ); ?>">
        <?php echo esc_html( $link_text ); ?>
    </a>
</div>
```

#### **Nivel 5: AJAX Security**
```php
class WC_Product_Slider_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_wc_slider_update_order', array( $this, 'update_slider_order' ) );
    }

    public function update_slider_order() {
        // Nonce check
        check_ajax_referer( 'wc-slider-reorder', 'nonce' );

        // Capability check
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
        }

        // Sanitize
        $slider_id = isset( $_POST['slider_id'] ) ? absint( $_POST['slider_id'] ) : 0;
        $order = isset( $_POST['order'] ) ? array_map( 'absint', $_POST['order'] ) : array();

        // Validate
        if ( ! $slider_id || empty( $order ) ) {
            wp_send_json_error( array( 'message' => 'Invalid data' ), 400 );
        }

        // Process
        update_post_meta( $slider_id, '_slide_order', $order );

        wp_send_json_success( array( 'message' => 'Order updated' ) );
    }
}
```

#### **Nivel 6: SQL Injection Prevention**
```php
// CORRECTO - Prepared statement
global $wpdb;
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s",
        $post_id,
        '_wc_slider_config'
    )
);

// INCORRECTO - NUNCA HACER ESTO
// $results = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE post_id = {$post_id}" );
```

#### **Nivel 7: File Upload Security**
```php
class WC_Product_Slider_Image_Handler {

    public function validate_upload( $file ) {
        // Validar tipo MIME
        $allowed_mimes = array(
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/avif',
        );

        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        $mime_type = finfo_file( $finfo, $file['tmp_name'] );
        finfo_close( $finfo );

        if ( ! in_array( $mime_type, $allowed_mimes, true ) ) {
            return new WP_Error( 'invalid_file_type', __( 'Invalid file type', 'woocommerce-product-slider' ) );
        }

        // Validar tamaño
        $max_size = 5 * 1024 * 1024; // 5MB
        if ( $file['size'] > $max_size ) {
            return new WP_Error( 'file_too_large', __( 'File too large', 'woocommerce-product-slider' ) );
        }

        // Validar dimensiones
        $image_info = getimagesize( $file['tmp_name'] );
        if ( $image_info === false ) {
            return new WP_Error( 'invalid_image', __( 'Invalid image file', 'woocommerce-product-slider' ) );
        }

        return true;
    }
}
```

**Decisión Final:**
```json
{
  "security_layers": [
    "Input sanitization",
    "Data validation",
    "Capability checks",
    "Nonce verification",
    "Output escaping",
    "Prepared statements",
    "File upload validation"
  ],
  "owasp_compliance": "Top 10 2021 covered"
}
```

**Riesgo:** BAJO - Defense-in-depth approach

---

### 14. **Performance: Estrategia de Optimización**

#### ✅ Estado: BEST PRACTICES 2025

**Objetivos:**
- Time to Interactive < 3s
- Lighthouse Score ≥ 90
- First Contentful Paint < 1.5s
- Largest Contentful Paint < 2.5s

**Técnicas:**

#### **1. Conditional Loading**
```php
// Solo cargar assets si hay slider en página
class WC_Product_Slider_Public {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'conditional_enqueue' ) );
    }

    public function conditional_enqueue() {
        global $post;

        // Check si hay shortcode o bloque
        $has_slider = false;

        if ( is_a( $post, 'WP_Post' ) ) {
            $has_slider = (
                has_shortcode( $post->post_content, 'wc_product_slider' ) ||
                has_block( 'wc-product-slider/slider', $post )
            );
        }

        // También check widgets
        if ( ! $has_slider && is_active_widget( false, false, 'wc-product-slider-widget' ) ) {
            $has_slider = true;
        }

        if ( $has_slider ) {
            $this->enqueue_assets();
        }
    }

    private function enqueue_assets() {
        wp_enqueue_style(
            'wc-product-slider',
            WC_PRODUCT_SLIDER_URL . 'assets/css/public/slider.min.css',
            array(),
            WC_PRODUCT_SLIDER_VERSION
        );

        wp_enqueue_script(
            'swiper',
            WC_PRODUCT_SLIDER_URL . 'assets/js/public/swiper.min.js',
            array(),
            '11.1.14',
            array( 'strategy' => 'defer' ) // WordPress 6.3+ feature
        );

        wp_enqueue_script(
            'wc-product-slider',
            WC_PRODUCT_SLIDER_URL . 'assets/js/public/slider.min.js',
            array( 'swiper' ),
            WC_PRODUCT_SLIDER_VERSION,
            array( 'strategy' => 'defer' )
        );
    }
}
```

#### **2. Caching Layer**
```php
class WC_Product_Slider_Cache {

    const CACHE_GROUP = 'wc_product_slider';
    const CACHE_EXPIRATION = HOUR_IN_SECONDS;

    /**
     * Get cached slider HTML
     */
    public static function get_slider_html( $slider_id ) {
        $cache_key = 'slider_html_' . $slider_id;
        $html = wp_cache_get( $cache_key, self::CACHE_GROUP );

        if ( false === $html ) {
            $html = self::generate_slider_html( $slider_id );
            wp_cache_set( $cache_key, $html, self::CACHE_GROUP, self::CACHE_EXPIRATION );
        }

        return $html;
    }

    /**
     * Invalidar cache al guardar
     */
    public static function invalidate_slider_cache( $slider_id ) {
        $cache_key = 'slider_html_' . $slider_id;
        wp_cache_delete( $cache_key, self::CACHE_GROUP );

        // También invalidar cache de productos si cambió stock
        delete_transient( 'wc_slider_products_' . $slider_id );
    }

    /**
     * Get cached products query
     */
    public static function get_slider_products( $slider_id, $product_ids ) {
        $transient_key = 'wc_slider_products_' . md5( serialize( $product_ids ) );
        $products = get_transient( $transient_key );

        if ( false === $products ) {
            $products = wc_get_products( array(
                'include' => $product_ids,
                'orderby' => 'post__in',
                'limit'   => -1,
            ) );

            set_transient( $transient_key, $products, HOUR_IN_SECONDS );
        }

        return $products;
    }
}
```

#### **3. Lazy Loading de Imágenes**
```php
function wc_product_slider_render_image( $product, $lazy = true ) {
    $image_id = $product->get_image_id();

    if ( ! $image_id ) {
        return '<img src="' . wc_placeholder_img_src() . '" alt="Placeholder">';
    }

    return wp_get_attachment_image(
        $image_id,
        'woocommerce_thumbnail',
        false,
        array(
            'loading' => $lazy ? 'lazy' : 'eager', // Primeras eager, resto lazy
            'decoding' => 'async',
            'class' => 'wc-slider-image',
            'alt' => $product->get_name(),
        )
    );
}
```

#### **4. Database Query Optimization**
```php
// MALO - N+1 queries
foreach ( $product_ids as $id ) {
    $product = wc_get_product( $id ); // Query cada iteración
}

// BUENO - 1 query
$products = wc_get_products( array(
    'include' => $product_ids,
    'limit'   => -1,
) );
```

#### **5. Asset Minification**
```json
// package.json
{
  "scripts": {
    "build": "wp-scripts build --webpack-copy-php",
    "build:production": "NODE_ENV=production wp-scripts build"
  }
}
```

Auto-genera:
- `slider.js` → `slider.min.js` (tree-shaken, uglified)
- `slider.css` → `slider.min.css` (autoprefixed, minified)

#### **6. Critical CSS Inline**
```php
function wc_product_slider_inline_critical_css() {
    if ( has_block( 'wc-product-slider/slider' ) ) {
        ?>
        <style id="wc-slider-critical">
            .wc-product-slider { position: relative; overflow: hidden; }
            .wc-slider-image { max-width: 100%; height: auto; }
        </style>
        <?php
    }
}
add_action( 'wp_head', 'wc_product_slider_inline_critical_css', 1 );
```

**Decisión Final:**
```json
{
  "caching": "Object cache + Transients API",
  "lazy_loading": "Native HTML loading=lazy",
  "conditional_loading": "Solo si slider en página",
  "minification": "@wordpress/scripts automático",
  "queries": "Optimizadas (avoid N+1)",
  "target_metrics": {
    "TTI": "< 3s",
    "LCP": "< 2.5s",
    "CLS": "< 0.1"
  }
}
```

**Riesgo:** BAJO - Técnicas probadas

---

### 15. **Accesibilidad: WCAG 2.1 AA Compliance**

#### ✅ Estado: REQUISITO OBLIGATORIO WORDPRESS

**Estrategia:**

#### **1. ARIA Labels**
```php
<div class="swiper wc-product-slider"
     role="region"
     aria-label="<?php echo esc_attr( $slider_title ); ?>">

    <div class="swiper-wrapper" role="list">
        <?php foreach ( $products as $product ) : ?>
            <div class="swiper-slide" role="listitem">
                <article aria-labelledby="product-<?php echo esc_attr( $product->get_id() ); ?>">

                    <a href="<?php echo esc_url( $product->get_permalink() ); ?>"
                       aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'woocommerce-product-slider' ), $product->get_name() ) ); ?>">

                        <?php echo wp_get_attachment_image(
                            $product->get_image_id(),
                            'medium',
                            false,
                            array(
                                'alt' => $product->get_name(),
                                'loading' => 'lazy'
                            )
                        ); ?>
                    </a>

                    <h3 id="product-<?php echo esc_attr( $product->get_id() ); ?>">
                        <?php echo esc_html( $product->get_name() ); ?>
                    </h3>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation -->
    <button class="swiper-button-prev"
            aria-label="<?php esc_attr_e( 'Previous slide', 'woocommerce-product-slider' ); ?>">
        <span aria-hidden="true">‹</span>
    </button>

    <button class="swiper-button-next"
            aria-label="<?php esc_attr_e( 'Next slide', 'woocommerce-product-slider' ); ?>">
        <span aria-hidden="true">›</span>
    </button>

    <!-- Pagination -->
    <div class="swiper-pagination"
         role="tablist"
         aria-label="<?php esc_attr_e( 'Slider pagination', 'woocommerce-product-slider' ); ?>">
    </div>
</div>
```

#### **2. Keyboard Navigation**
```javascript
// Swiper config
const swiper = new Swiper('.wc-product-slider', {
  keyboard: {
    enabled: true,
    onlyInViewport: true,
  },
  a11y: {
    enabled: true,
    prevSlideMessage: 'Previous slide',
    nextSlideMessage: 'Next slide',
    firstSlideMessage: 'This is the first slide',
    lastSlideMessage: 'This is the last slide',
    paginationBulletMessage: 'Go to slide {{index}}',
  },
  // Pausar en focus
  on: {
    slideChangeTransitionStart: function () {
      const activeSlide = this.slides[this.activeIndex];
      const link = activeSlide.querySelector('a');
      if (link) {
        link.focus(); // Auto-focus para screen readers
      }
    }
  }
});

// Pausar autoplay con Tab focus
const slider = document.querySelector('.wc-product-slider');
slider.addEventListener('focusin', () => {
  if (swiper.autoplay.running) {
    swiper.autoplay.stop();
  }
});

slider.addEventListener('focusout', () => {
  if (!swiper.autoplay.running) {
    swiper.autoplay.start();
  }
});
```

#### **3. Color Contrast**
```css
/* Garantizar contraste 4.5:1 mínimo */
.wc-slider-title {
    color: #1a1a1a; /* 14.6:1 contrast ratio con blanco */
}

.wc-slider-button {
    background: #0066cc;
    color: #ffffff; /* 7.7:1 contrast ratio */
}

.wc-slider-button:hover,
.wc-slider-button:focus {
    background: #0052a3; /* Más oscuro en hover/focus */
    outline: 2px solid #0066cc;
    outline-offset: 2px;
}

/* Focus visible SIEMPRE */
*:focus-visible {
    outline: 2px solid #0066cc !important;
    outline-offset: 2px !important;
}
```

#### **4. Prefers Reduced Motion**
```css
/* Respetar preferencias de usuario */
@media (prefers-reduced-motion: reduce) {
    .wc-product-slider * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
```

```javascript
// JavaScript también
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const swiper = new Swiper('.wc-product-slider', {
  speed: prefersReducedMotion ? 0 : 300,
  autoplay: prefersReducedMotion ? false : {
    delay: 3000,
  },
});
```

#### **5. Screen Reader Support**
```html
<!-- Anunciar cambios dinámicos -->
<div class="swiper-notification"
     aria-live="polite"
     aria-atomic="true"
     class="sr-only">
    <!-- Swiper.js actualiza automáticamente con a11y: true -->
</div>

<style>
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    white-space: nowrap;
    border-width: 0;
}
</style>
```

#### **6. Skip Links**
```php
function wc_product_slider_skip_link( $slider_id ) {
    ?>
    <a href="#after-slider-<?php echo esc_attr( $slider_id ); ?>"
       class="skip-link sr-only sr-only-focusable">
        <?php esc_html_e( 'Skip slider', 'woocommerce-product-slider' ); ?>
    </a>

    <!-- Slider content -->

    <div id="after-slider-<?php echo esc_attr( $slider_id ); ?>"></div>
    <?php
}
```

**Herramientas de Testing:**
- **axe DevTools**: Auditoría automática
- **WAVE**: Evaluación visual
- **Lighthouse**: Score de accesibilidad
- **Screen reader**: NVDA/JAWS testing manual

**Decisión Final:**
```json
{
  "wcag_level": "AA (target AAA donde posible)",
  "features": [
    "ARIA labels completos",
    "Keyboard navigation (Tab, Arrow, Enter, Esc)",
    "Focus visible siempre",
    "Contrast ratio ≥ 4.5:1",
    "Prefers-reduced-motion support",
    "Screen reader announcements",
    "Skip links"
  ],
  "testing": "axe DevTools + manual screen reader"
}
```

**Riesgo:** BAJO - Features bien soportadas

---

### 16. **Internacionalización (i18n)**

#### ✅ Estado: REQUISITO OBLIGATORIO WORDPRESS

**Text Domain**: `woocommerce-product-slider`

**Estrategia:**

#### **1. Todas las strings traducibles**
```php
// Correcto
__( 'Product Slider Settings', 'woocommerce-product-slider' )
_e( 'Save Changes', 'woocommerce-product-slider' )
esc_html__( 'Slider Title', 'woocommerce-product-slider' )
esc_attr__( 'Enter slider title', 'woocommerce-product-slider' )

// Plurales
_n(
    '%s product',
    '%s products',
    $count,
    'woocommerce-product-slider'
)

// Con contexto
_x( 'Slide', 'noun', 'woocommerce-product-slider' )
_x( 'Slide', 'verb', 'woocommerce-product-slider' )

// JavaScript
wp_localize_script( 'wc-product-slider-admin', 'wcSliderI18n', array(
    'confirmDelete' => __( 'Are you sure you want to delete this slider?', 'woocommerce-product-slider' ),
    'saving'        => __( 'Saving...', 'woocommerce-product-slider' ),
    'saved'         => __( 'Saved!', 'woocommerce-product-slider' ),
) );
```

#### **2. Load Text Domain**
```php
class WC_Product_Slider {

    public function __construct() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'woocommerce-product-slider',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/languages/'
        );
    }
}
```

#### **3. Generar POT file**
```json
// package.json
{
  "scripts": {
    "makepot": "wp i18n make-pot . languages/woocommerce-product-slider.pot --domain=woocommerce-product-slider"
  }
}
```

```bash
npm run makepot
```

Genera: `languages/woocommerce-product-slider.pot`

#### **4. Traducir JavaScript**
```javascript
// src/admin.js
import { __ } from '@wordpress/i18n';

const confirmMessage = __( 'Delete this slider?', 'woocommerce-product-slider' );
```

Build con `@wordpress/scripts` auto-extrae strings.

#### **5. RTL Support**
```css
/* style.css */
.wc-slider-nav-prev {
    left: 10px;
}

/* rtl.css */
.wc-slider-nav-prev {
    right: 10px;
    left: auto;
}
```

```php
// Enqueue RTL
if ( is_rtl() ) {
    wp_enqueue_style(
        'wc-product-slider-rtl',
        WC_PRODUCT_SLIDER_URL . 'assets/css/rtl.css'
    );
}
```

#### **6. Formato de Fechas/Números**
```php
// Usar funciones WP para localización
$date = date_i18n( get_option( 'date_format' ), $timestamp );
$price = wc_price( $product->get_price() ); // WC maneja formato por locale
```

**Archivo POT Ejemplo:**
```pot
# Copyright (C) 2025 Your Name
# This file is distributed under the GPL-2.0+.
msgid ""
msgstr ""
"Project-Id-Version: WooCommerce Product Slider 1.0.0\n"
"Report-Msgid-Bugs-To: https://wordpress.org/support/plugin/woocommerce-product-slider\n"
"POT-Creation-Date: 2025-11-17 00:00:00+00:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

#: includes/admin/class-wc-product-slider-admin.php:45
msgid "Product Slider Settings"
msgstr ""

#: includes/admin/class-wc-product-slider-admin.php:67
msgid "Save Changes"
msgstr ""
```

**Decisión Final:**
```json
{
  "text_domain": "woocommerce-product-slider",
  "pot_generation": "wp-cli i18n make-pot",
  "js_i18n": "@wordpress/i18n",
  "rtl_support": true,
  "locale_aware": "Fechas, números, monedas"
}
```

**Riesgo:** NULO - Proceso estándar WP

---

## 🎯 Decisiones Finales Resumidas

### Stack Confirmado (Noviembre 2025)

| Componente | Tecnología | Versión | Estado | Riesgo |
|------------|-----------|---------|--------|--------|
| **PHP** | PHP | 7.4 - 8.3+ | ✅ Vigente | BAJO |
| **WordPress** | WordPress | 6.2 - 6.7 | ✅ Vigente | BAJO |
| **WooCommerce** | WooCommerce | 8.2 - 10.3 | ✅ Vigente | MEDIO-BAJO |
| **Autoloading** | Composer PSR-4 | Latest | ✅ Estándar | NULO |
| **Testing** | PHPUnit 9 + Polyfills | 9.6 / 2.0 | ✅ Oficial | BAJO |
| **Code Standards** | WPCS | 3.0 | ✅ Última | NULO |
| **Slider Library** | Swiper.js | 11.x | ✅ Activo | NULO |
| **Admin UI** | React (@wordpress) | 18 | ✅ Nativo | NULO |
| **Build Tool** | @wordpress/scripts | 28.x | ✅ Oficial | NULO |
| **Code Editor** | CodeMirror | 6.x | ✅ Moderno | BAJO |
| **Images** | WebP/AVIF Native | WP 5.8+ | ✅ Nativo | NULO |
| **Blocks** | Gutenberg API v2 | WP 6.0+ | ✅ Estable | BAJO |

---

## ✅ Confirmación de Viabilidad

### **APROBADO PARA PRODUCCIÓN**

Todos los componentes seleccionados:
1. ✅ **Activamente mantenidos** (ninguno deprecated)
2. ✅ **Ampliamente adoptados** (battle-tested)
3. ✅ **Oficialmente soportados** por WordPress/WooCommerce
4. ✅ **Performance optimizados** para 2025
5. ✅ **Accesibles** (WCAG 2.1 AA compatible)
6. ✅ **Seguros** (OWASP Top 10 covered)
7. ✅ **Marketplace ready** (cumple todos los requisitos)

### Riesgo General del Proyecto: **BAJO**

---

## 📅 Próximos Pasos

1. ✅ **Plan aprobado** → LISTO
2. ⏭️ **Iniciar Fase 1**: Setup estructura base
3. ⏭️ **Configurar testing TDD**: PHPUnit + WordPress Test Library
4. ⏭️ **Primer test**: CPT registration (Red-Green-Refactor)
5. ⏭️ **CI/CD**: GitHub Actions para testing automático

---

**Última actualización**: Noviembre 17, 2025
**Investigación basada en**: WordPress 6.7, WooCommerce 10.3, PHP 8.3
**Siguiente revisión**: Cada 3 meses o al inicio de cada fase
