# Plan de Desarrollo - WooCommerce Product Slider Plugin

## 🎯 Visión General

Plugin profesional de WordPress/WooCommerce para crear sliders de productos altamente customizables, desarrollado con enfoque TDD (Test-Driven Development), cumpliendo todos los estándares de WordPress Marketplace.

## 📋 Requisitos Principales

### Funcionales
- ✅ Slider de productos WooCommerce configurable
- ✅ Soporte múltiples formatos de imágenes (upload manual o productos)
- ✅ Título, descripción y texto personalizado por slide
- ✅ Configuración visual de controles de navegación
- ✅ Sistema de colores personalizable
- ✅ CSS personalizado opcional
- ✅ Links configurables (productos u URLs personalizadas)
- ✅ Interfaz intuitiva sin código

### No Funcionales
- ✅ Seguridad (OWASP Top 10)
- ✅ SEO optimizado
- ✅ Alto rendimiento
- ✅ Escalable y mantenible
- ✅ Cumplimiento WordPress Coding Standards
- ✅ Accesibilidad WCAG 2.1 AA

## 🏗️ Arquitectura del Plugin

### Estructura de Directorios

```
woocommerce-product-slider/
├── assets/
│   ├── css/
│   │   ├── admin/
│   │   │   ├── admin.css
│   │   │   └── admin.min.css
│   │   └── public/
│   │       ├── slider.css
│   │       └── slider.min.css
│   ├── js/
│   │   ├── admin/
│   │   │   ├── admin-app.js (React/Vue)
│   │   │   ├── color-picker.js
│   │   │   └── media-uploader.js
│   │   └── public/
│   │       ├── slider-init.js
│   │       └── slider.min.js
│   └── images/
│       └── placeholder.png
├── includes/
│   ├── class-wc-product-slider.php (Main class)
│   ├── class-wc-product-slider-activator.php
│   ├── class-wc-product-slider-deactivator.php
│   ├── class-wc-product-slider-loader.php
│   ├── admin/
│   │   ├── class-wc-product-slider-admin.php
│   │   ├── class-wc-product-slider-metaboxes.php
│   │   ├── class-wc-product-slider-settings.php
│   │   └── partials/
│   │       ├── admin-display.php
│   │       └── metabox-display.php
│   ├── public/
│   │   ├── class-wc-product-slider-public.php
│   │   ├── class-wc-product-slider-shortcode.php
│   │   └── partials/
│   │       └── slider-display.php
│   ├── core/
│   │   ├── class-wc-product-slider-cpt.php (Custom Post Type)
│   │   ├── class-wc-product-slider-image-handler.php
│   │   ├── class-wc-product-slider-renderer.php
│   │   ├── class-wc-product-slider-sanitizer.php
│   │   └── class-wc-product-slider-cache.php
│   └── blocks/
│       ├── class-wc-product-slider-block.php (Gutenberg)
│       └── slider-block/
│           ├── block.json
│           ├── edit.js
│           └── save.js
├── languages/
│   └── woocommerce-product-slider.pot
├── tests/
│   ├── bootstrap.php
│   ├── phpunit.xml
│   ├── unit/
│   │   ├── test-class-wc-product-slider.php
│   │   ├── test-class-wc-product-slider-cpt.php
│   │   ├── test-class-wc-product-slider-sanitizer.php
│   │   └── test-class-wc-product-slider-renderer.php
│   └── integration/
│       ├── test-wc-product-slider-admin.php
│       └── test-wc-product-slider-shortcode.php
├── vendor/ (Composer dependencies)
├── .gitignore
├── .phpcs.xml.dist (WordPress Coding Standards)
├── composer.json
├── package.json
├── woocommerce-product-slider.php (Main plugin file)
└── readme.txt (WordPress.org format)
```

## 🔧 Stack Tecnológico

### Backend (PHP)
- **PHP**: 7.4+ (compatible hasta 8.2+)
- **WordPress**: 5.8+
- **WooCommerce**: 5.0+
- **Composer**: Autoloading PSR-4
- **PHPUnit**: 9.x para testing
- **WordPress Coding Standards**: PHPCS con ruleset WordPress

### Frontend
- **JavaScript**: ES6+ (Babel transpilation)
- **CSS**: SCSS/PostCSS
- **Slider Library**: Swiper.js (moderna, ligera, accesible)
- **Admin UI**: React o Vue.js (para panel de configuración)
- **Build Tools**: Webpack 5 / Vite
- **Package Manager**: npm/yarn

### Testing
- **PHPUnit**: Unit tests
- **WP_UnitTestCase**: WordPress integration tests
- **Jest**: JavaScript unit tests
- **Playwright/Cypress**: E2E tests (opcional)
- **PHP_CodeSniffer**: Code quality
- **PHPStan**: Static analysis

### CI/CD
- **GitHub Actions**: Automated testing
- **Deployment**: WordPress.org SVN

## 🎨 Funcionalidades Detalladas

### 1. Custom Post Type "Slider"

```php
Post Type: 'wc_product_slider'
- Supports: title, editor
- Capabilities: manage_woocommerce (requiere permisos de tienda)
- Taxonomía: 'slider_category' (para organizar sliders)
```

### 2. Metaboxes de Configuración

#### **Metabox 1: Productos y Contenido**
- Selector de productos WooCommerce (multi-select)
- Opción: Usar imagen del producto o subir custom
- Custom uploader para imágenes alternativas
- Campos por slide:
  - Título (override del producto)
  - Descripción corta
  - Texto del botón CTA
  - URL destino (producto, página, externa)
  - Target (_blank, _self)

#### **Metabox 2: Diseño y Estilos**
- **Navegación**:
  - Flechas (activar/desactivar)
  - Posición flechas (dentro, fuera, custom)
  - Dots/Pagination (activar/desactivar)
  - Thumbnails (activar/desactivar)
- **Layout**:
  - Slides visibles (1-6)
  - Slides a scroll
  - Espaciado entre slides
  - Altura del slider (auto, fixed, ratio)
- **Colores** (Color Picker):
  - Fondo slider
  - Color texto título
  - Color texto descripción
  - Color botones navegación
  - Color hover botones
  - Color dots activo/inactivo

#### **Metabox 3: Comportamiento**
- Autoplay (activar/desactivar)
- Velocidad autoplay (ms)
- Velocidad transición (ms)
- Loop infinito
- Pausar en hover
- Lazy loading
- Efecto de transición (slide, fade, cube, etc.)

#### **Metabox 4: Responsive**
- Breakpoints configurables
- Slides visibles por breakpoint
- Activar/desactivar navegación por breakpoint

#### **Metabox 5: Avanzado**
- CSS personalizado (CodeMirror editor)
- JavaScript hooks (para developers)
- Clase CSS adicional
- ID único del slider

### 3. Panel de Administración

**Tecnología**: React con WordPress Components (@wordpress/components)

**Características**:
- Vista previa en tiempo real
- Drag & drop para reordenar slides
- Editor visual de estilos
- Presets de diseño profesionales
- Import/Export configuraciones
- Duplicar sliders

### 4. Shortcode y Gutenberg Block

**Shortcode**:
```php
[wc_product_slider id="123" class="custom-class"]
```

**Gutenberg Block**:
- Block nativo integrado con Editor
- Preview del slider en el editor
- Selector de slider existente o crear nuevo
- Configuración inline

**Widget Legacy**:
- Compatible con widgets clásicos
- Selector dropdown de sliders

### 5. Sistema de Imágenes

**Características**:
- Integración WordPress Media Library
- Soporte múltiples formatos: JPG, PNG, WebP, SVG (sanitizado)
- Generación automática de tamaños responsivos (srcset)
- Optimización automática (WebP conversion opcional)
- Lazy loading nativo
- Alt text automático desde producto o personalizado
- Fallback a placeholder si imagen falta

### 6. Seguridad (OWASP Top 10)

#### **A01:2021 – Broken Access Control**
- Capability checks en todas las operaciones admin
- Nonces en todos los formularios
- Verificar ownership de sliders

#### **A03:2021 – Injection**
- Sanitización exhaustiva:
  - `sanitize_text_field()` para textos
  - `esc_url_raw()` para URLs
  - `wp_kses_post()` para contenido HTML
  - `intval()` para IDs numéricos
- Prepared statements para queries DB
- Escape en output:
  - `esc_html()` para texto
  - `esc_url()` para links
  - `esc_attr()` para atributos

#### **A04:2021 – Insecure Design**
- Validación de tipos de archivo
- Límites de tamaño de upload
- Rate limiting en AJAX endpoints

#### **A05:2021 – Security Misconfiguration**
- No exponer información sensible
- Headers de seguridad apropiados
- Permisos de archivos correctos

#### **A08:2021 – Software and Data Integrity Failures**
- Verificar integridad de assets con SRI
- Composer vendor/ en .gitignore
- Dependencias actualizadas

#### **A10:2021 – Server-Side Request Forgery (SSRF)**
- Validar URLs externas
- Whitelist de dominios permitidos

### 7. Performance

#### **Backend**:
- Transients API para caching de queries
- Object caching compatible
- Lazy loading de clases (autoloader)
- Queries optimizadas (avoid meta queries hell)
- Cache de configuración de slider

#### **Frontend**:
- Assets minificados y concatenados
- CSS/JS cargados solo si hay slider en página
- Inline critical CSS
- Defer/async JavaScript
- Lazy loading de imágenes
- WebP con fallback
- CDN ready

#### **Database**:
- Índices en custom tables (si se usan)
- Cleanup de transients expirados
- Uninstall limpio (remove all data)

### 8. SEO

- Semantic HTML (figure, figcaption)
- Alt text en imágenes
- Structured data (JSON-LD) para productos
- Links con rel apropiado
- No hidden content (accesible para crawlers)
- Lazy loading que no afecte LCP

### 9. Accesibilidad (WCAG 2.1 AA)

- ARIA labels en controles
- Navegación por teclado (Tab, Arrow keys, Enter, Esc)
- Focus visible y lógico
- Contraste de color adecuado (4.5:1 mínimo)
- Textos alternativos descriptivos
- No depender solo de color
- Skip links
- Reduced motion support (prefers-reduced-motion)

### 10. Internacionalización

- Text domain: 'woocommerce-product-slider'
- Todas las strings con `__()`, `_e()`, `esc_html__()`, etc.
- Archivo POT generado
- RTL support (CSS rtl.css)
- Formato de fechas/números localizado

## 🧪 Estrategia de Testing (TDD)

### Ciclo Red-Green-Refactor

**1. RED**: Escribir test que falle
```php
public function test_slider_cpt_is_registered() {
    $this->assertTrue( post_type_exists( 'wc_product_slider' ) );
}
```

**2. GREEN**: Escribir código mínimo para pasar
```php
public function register_post_type() {
    register_post_type( 'wc_product_slider', [...] );
}
```

**3. REFACTOR**: Mejorar código manteniendo tests verdes

### Cobertura de Tests

#### **Unit Tests** (80%+ coverage)
- Sanitización de inputs
- Validación de configuración
- Generación de HTML
- Helper functions
- Image handler
- Cache layer

#### **Integration Tests**
- Custom Post Type registration
- Metaboxes rendering
- Shortcode output
- Gutenberg block registration
- WooCommerce product queries
- Admin AJAX endpoints

#### **E2E Tests** (opcional pero recomendado)
- Crear slider desde admin
- Configurar opciones visuales
- Publicar slider
- Verificar rendering en frontend
- Interacción con slider (clicks, navegación)

### Test Utilities

```php
// Factory para crear sliders de test
class WC_Product_Slider_Factory {
    public static function create_slider( $args = [] ) { }
}

// Mocks para WooCommerce
class WC_Product_Mock { }
```

## 📦 Dependencias

### Composer (Backend)
```json
{
    "require": {
        "php": ">=7.4",
        "composer/installers": "^2.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "wp-coding-standards/wpcs": "^3.0",
        "phpstan/phpstan": "^1.9",
        "yoast/phpunit-polyfills": "^1.0"
    }
}
```

### NPM (Frontend)
```json
{
    "dependencies": {
        "swiper": "^11.0",
        "@wordpress/element": "^5.0",
        "@wordpress/components": "^25.0",
        "@wordpress/block-editor": "^12.0"
    },
    "devDependencies": {
        "@wordpress/scripts": "^26.0",
        "webpack": "^5.0",
        "sass": "^1.60",
        "eslint": "^8.0",
        "jest": "^29.0"
    }
}
```

## 🚀 Fases de Desarrollo

### **Fase 1: Fundación (Semana 1-2)** ✅
1. Setup del proyecto (composer, npm)
2. Configuración PHPUnit + WordPress Test Library
3. Estructura de directorios
4. Plugin principal + activator/deactivator
5. Autoloader PSR-4
6. CI/CD básico (GitHub Actions)

### **Fase 2: Core (Semana 3-4)**
1. Custom Post Type con tests
2. Clase Sanitizer con tests exhaustivos
3. Image Handler con tests
4. Metaboxes básicos
5. Admin settings page

### **Fase 3: Admin Interface (Semana 5-6)**
1. React admin app
2. Metaboxes avanzados (color picker, image uploader)
3. Live preview
4. Validación frontend
5. UX polish

### **Fase 4: Frontend Rendering (Semana 7-8)**
1. Shortcode handler con tests
2. Template rendering engine
3. Integración Swiper.js
4. Estilos CSS base
5. Responsive design

### **Fase 5: Gutenberg & Avanzado (Semana 9-10)**
1. Gutenberg block
2. Widget legacy
3. CSS personalizado (CodeMirror)
4. Import/Export
5. Presets de diseño

### **Fase 6: Performance & SEO (Semana 11)**
1. Sistema de caching
2. Asset optimization
3. Lazy loading
4. Structured data
5. Performance testing

### **Fase 7: Seguridad & Accesibilidad (Semana 12)**
1. Auditoría de seguridad
2. Penetration testing
3. Accesibilidad (ARIA, keyboard)
4. Contrast checker
5. Screen reader testing

### **Fase 8: Documentación (Semana 13)**
1. Inline documentation (PHPDoc)
2. readme.txt WordPress.org
3. Wiki/User guide
4. Developer documentation
5. Video tutorials (opcional)

### **Fase 9: Polish & Marketplace (Semana 14)**
1. WordPress Coding Standards compliance
2. Assets para marketplace (screenshots, banner, icon)
3. Testing en múltiples versiones (WP/WC/PHP)
4. Compatibilidad con temas populares
5. Submission checklist

### **Fase 10: Launch (Semana 15)**
1. Revisión final
2. Submit a WordPress.org
3. Documentación de release
4. Marketing materials
5. Support plan

## 📊 Métricas de Calidad

### Código
- **Test Coverage**: ≥ 80%
- **PHPCS**: 0 errors, 0 warnings
- **PHPStan**: Level 8
- **Complexity**: < 10 cyclomatic complexity

### Performance
- **Time to Interactive**: < 3s
- **Lighthouse Score**: ≥ 90
- **Asset Size**: JS < 100KB, CSS < 50KB (minified + gzipped)
- **Database Queries**: < 10 por slider

### Seguridad
- **0 vulnerabilities** en dependencias
- **Passed** WPScan vulnerability check
- **Passed** Plugin Check plugin

### Accesibilidad
- **WCAG 2.1 AA**: 100% compliance
- **Lighthouse Accessibility**: ≥ 95

## 🔄 Mantenimiento Post-Launch

### Versionado Semántico (SemVer)
- **Major** (1.0.0): Breaking changes
- **Minor** (1.1.0): Nuevas funcionalidades
- **Patch** (1.0.1): Bug fixes

### Roadmap Post-Launch
- **v1.1**: Animaciones avanzadas
- **v1.2**: Templates builder
- **v1.3**: A/B testing
- **v1.4**: Analytics integration
- **v2.0**: Multi-store support

### Support
- **Response time**: < 48h
- **Bug fix**: < 1 week
- **Security patch**: < 24h

## 📚 Recursos y Referencias

### WordPress
- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Plugin Handbook - Security](https://developer.wordpress.org/plugins/security/)

### WooCommerce
- [WooCommerce Documentation](https://woocommerce.com/documentation/)
- [WooCommerce Developer Resources](https://github.com/woocommerce/woocommerce/wiki)

### Testing
- [WordPress PHPUnit](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/)
- [Test Driven Development](https://www.amazon.com/Test-Driven-Development-Kent-Beck/dp/0321146530)

### Accesibilidad
- [WCAG 2.1](https://www.w3.org/WAI/WCAG21/quickref/)
- [WebAIM](https://webaim.org/)

---

## 🎯 Próximos Pasos Inmediatos

1. ✅ Aprobar este plan de desarrollo
2. ⏭️ Iniciar Fase 1: Configurar estructura base
3. ⏭️ Setup entorno de testing TDD
4. ⏭️ Crear archivo principal del plugin
5. ⏭️ Implementar primer test (CPT registration)

**¿Aprobamos este plan y comenzamos con la implementación?** 🚀
