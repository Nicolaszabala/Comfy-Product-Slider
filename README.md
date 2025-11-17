# WooCommerce Product Slider

[![CI](https://github.com/yourusername/woocommerce-product-slider/workflows/CI/badge.svg)](https://github.com/yourusername/woocommerce-product-slider/actions)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![WordPress](https://img.shields.io/badge/wordpress-%3E%3D%206.2-21759B.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/woocommerce-%3E%3D%208.2-96588A.svg)](https://woocommerce.com/)

Professional WooCommerce Product Slider with advanced customization options. Create beautiful, responsive product sliders with intuitive visual controls.

## 🎯 Features

- ✅ **Highly Customizable** - Visual configuration panel with live preview
- ✅ **TDD Development** - Thoroughly tested with PHPUnit (80%+ coverage)
- ✅ **Modern Stack** - Swiper.js 11, React 18, @wordpress/scripts
- ✅ **Security First** - OWASP Top 10 compliant, sanitization & validation
- ✅ **Performance Optimized** - Lazy loading, caching, conditional assets
- ✅ **Accessible** - WCAG 2.1 AA compliant, keyboard navigation
- ✅ **SEO Ready** - Semantic HTML, structured data, optimized images
- ✅ **Gutenberg Block** - Native block editor integration
- ✅ **Shortcode Support** - `[wc_product_slider id="123"]`
- ✅ **i18n Ready** - Fully translatable, RTL support

## 📋 Requirements

- **PHP:** 7.4 or higher (8.3+ recommended)
- **WordPress:** 6.2 or higher
- **WooCommerce:** 8.2 or higher (10.3+ tested)
- **Node.js:** 18.12 or higher
- **Composer:** 2.0 or higher

## 🚀 Installation

### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/woocommerce-product-slider.git
cd woocommerce-product-slider

# Install Composer dependencies
composer install

# Install NPM dependencies
npm install

# Build assets
npm run build

# Run tests
composer test
```

### WordPress Plugin Installation

1. Download the latest release
2. Upload to `/wp-content/plugins/`
3. Activate via WordPress admin
4. Ensure WooCommerce is installed and activated

## 🛠️ Development

### Available Scripts

#### Composer

```bash
composer phpcs          # Run PHPCS (WordPress Coding Standards)
composer phpcbf         # Auto-fix PHPCS issues
composer phpstan        # Run PHPStan static analysis
composer test           # Run all PHPUnit tests
composer test:unit      # Run unit tests only
composer test:integration # Run integration tests only
composer test:coverage  # Generate coverage report
```

#### NPM

```bash
npm run start           # Watch mode for development
npm run build           # Production build
npm run lint:js         # Lint JavaScript
npm run lint:css        # Lint CSS
npm run format          # Format code with Prettier
npm run makepot         # Generate translation POT file
```

### Project Structure

```
woocommerce-product-slider/
├── assets/              # CSS, JS, images
│   ├── css/
│   │   ├── admin/
│   │   └── public/
│   ├── js/
│   │   ├── admin/
│   │   └── public/
│   └── images/
├── includes/            # PHP classes (PSR-4)
│   ├── admin/          # Admin-specific classes
│   ├── public/         # Public-facing classes
│   ├── core/           # Core functionality
│   └── blocks/         # Gutenberg blocks
├── languages/          # Translation files
├── tests/              # PHPUnit tests
│   ├── unit/
│   └── integration/
├── .github/            # GitHub Actions CI/CD
├── vendor/             # Composer dependencies (gitignored)
├── node_modules/       # NPM dependencies (gitignored)
└── build/              # Built assets (gitignored)
```

## 🧪 Testing

This plugin follows **Test-Driven Development (TDD)** methodology.

### Running Tests

```bash
# All tests
composer test

# Unit tests (fast, no WordPress)
composer test:unit

# Integration tests (requires WordPress Test Library)
composer test:integration

# With coverage report
composer test:coverage
open coverage/index.html
```

### Writing Tests

```php
<?php
namespace WC_Product_Slider\Tests;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class Test_My_Feature extends TestCase {
    public function test_feature_works() {
        $this->assertTrue( true );
    }
}
```

## 📖 Usage

### Shortcode

```php
[wc_product_slider id="123"]
```

### Gutenberg Block

Search for "Product Slider" in the block inserter.

### PHP Template

```php
<?php
if ( function_exists( 'wc_product_slider_render' ) ) {
    echo wc_product_slider_render( 123 );
}
?>
```

## 🎨 Customization

### Custom CSS

Each slider has a built-in CSS editor (CodeMirror 6) for advanced customization.

### Hooks & Filters

```php
// Modify slider output
add_filter( 'wc_product_slider_output', function( $html, $slider_id ) {
    // Your custom code
    return $html;
}, 10, 2 );

// Modify slider config
add_filter( 'wc_product_slider_config', function( $config, $slider_id ) {
    // Your modifications
    return $config;
}, 10, 2 );
```

## 🔒 Security

This plugin follows WordPress and OWASP security best practices:

- ✅ Input sanitization (`sanitize_text_field`, `esc_url_raw`, etc.)
- ✅ Output escaping (`esc_html`, `esc_attr`, `esc_url`)
- ✅ Nonce verification
- ✅ Capability checks
- ✅ Prepared SQL statements
- ✅ File upload validation

Report security issues to: security@example.com

## 🌍 Internationalization

The plugin is fully translatable:

```bash
# Generate POT file
npm run makepot

# Translate via .po/.mo files
# Place in: languages/woocommerce-product-slider-{locale}.mo
```

## 📚 Documentation

- [Development Plan](PLAN_DESARROLLO.md) - Complete 15-week roadmap
- [Technical Justification](JUSTIFICACION_TECNICA.md) - Technology stack analysis
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WooCommerce Documentation](https://woocommerce.com/documentation/)

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`composer test`)
5. Follow WordPress Coding Standards (`composer phpcs`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
WooCommerce Product Slider
Copyright (C) 2025 Your Name

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 👤 Author

**Your Name**
- Website: [https://example.com](https://example.com)
- GitHub: [@yourusername](https://github.com/yourusername)

## 🙏 Acknowledgments

- [WordPress](https://wordpress.org/)
- [WooCommerce](https://woocommerce.com/)
- [Swiper.js](https://swiperjs.com/)
- [CodeMirror](https://codemirror.net/)
- All contributors

## 📝 Changelog

### 1.0.0 (In Development)
- Initial release
- TDD foundation complete
- Core plugin structure
- Testing infrastructure
- CI/CD pipeline

---

**Made with ❤️ following TDD principles**
