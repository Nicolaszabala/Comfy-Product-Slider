=== WooCommerce Product Slider ===
Contributors: nicolaszabala
Donate link: https://github.com/Nicolaszabala/product-slider-plugin
Tags: woocommerce, slider, products, carousel, swiper
Requires at least: 6.2
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional product slider for WooCommerce with advanced customization options and modern user interface.

== Description ==

WooCommerce Product Slider allows you to create beautiful, responsive product sliders with an intuitive visual configuration interface. Perfect for showcasing featured products, sale items, or new arrivals on your WooCommerce store.

= Key Features =

* **Visual Product Selection** - Search and select products with real-time preview and thumbnails
* **One-Click Shortcode** - Copy shortcode to clipboard instantly with visual feedback
* **Custom CSS Editor** - Built-in CodeMirror 6 editor with syntax highlighting for advanced styling
* **Fully Responsive** - Mobile-first design with configurable breakpoints (1→2→3→4 slides)
* **Color Customization** - Customize navigation arrows and pagination dot colors
* **Autoplay & Loop** - Configurable slider behavior with speed control
* **WCAG 2.1 AA Compliant** - Full accessibility with keyboard navigation and screen reader support
* **SEO Optimized** - Semantic HTML with proper image alt tags and structured data
* **Performance Focused** - Lazy loading, conditional asset loading, optimized database queries

= Modern Technology Stack =

* React 18 for admin interface
* Swiper.js 11 for smooth sliders
* CodeMirror 6 for CSS editing
* WordPress Coding Standards compliant
* Test-driven development (71 unit tests, 161 assertions)
* OWASP security compliance

= Perfect For =

* **WooCommerce Store Owners** - Showcase products beautifully
* **Web Designers** - Easy customization with visual tools
* **Developers** - Clean code with hooks and filters
* **Marketers** - Boost product visibility and conversions
* **Agencies** - Reliable solution for client projects

= How It Works =

1. **Create** - Go to Product Sliders → Add New
2. **Select** - Search and choose products with live preview
3. **Customize** - Set colors, behavior, and add custom CSS
4. **Publish** - Copy shortcode with one click
5. **Display** - Add shortcode to any page or post

= Shortcode Usage =

`[wc_product_slider id="123"]`

Works everywhere:
* Posts and pages
* Widget areas (using Shortcode widget)
* Page builders (Elementor, WPBakery, Divi, Beaver Builder)
* PHP templates

= Developer Features =

* **WordPress Hooks** - Extensive filter and action hooks
* **Clean Architecture** - Namespaced classes following PSR standards
* **Conflict Prevention** - No global namespace pollution
* **Extensible** - Easy to extend and customize
* **Well Documented** - Inline documentation and external guides

== Installation ==

= Automatic Installation (Recommended) =

1. Log in to your WordPress dashboard
2. Navigate to **Plugins → Add New**
3. Search for "WooCommerce Product Slider"
4. Click **Install Now** button
5. Click **Activate** after installation
6. Ensure WooCommerce is installed and activated

= Manual Installation =

1. Download the plugin zip file
2. Navigate to **Plugins → Add New → Upload Plugin**
3. Choose the zip file and click **Install Now**
4. Click **Activate Plugin** after installation

= After Activation =

1. Ensure **WooCommerce** is installed and activated (required)
2. Go to **Product Sliders → Add New** to create your first slider
3. Follow the intuitive interface to configure your slider
4. Publish and copy the generated shortcode
5. Add shortcode to any page: `[wc_product_slider id="123"]`

= Requirements =

* WordPress 6.2 or higher
* WooCommerce 8.2 or higher
* PHP 7.4 or higher (8.2+ recommended)

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes, WooCommerce Product Slider requires WooCommerce to be installed and activated to function.

= How many products can I add to a slider? =

There's no hard limit, but we recommend 8-12 products per slider for optimal performance and user experience.

= Can I use multiple sliders on the same page? =

Absolutely! You can use multiple sliders on the same page. Each slider operates independently with its own configuration.

= Is it compatible with my theme? =

Yes! The plugin is designed to work with any WordPress theme. It's been tested with Twenty Twenty-Three, Twenty Twenty-Four, Storefront, Astra, GeneratePress, OceanWP, and many others.

= Does it work with page builders? =

Yes, it works perfectly with:
* Gutenberg (Block Editor) - Use the Shortcode block
* Elementor - Use the Shortcode widget
* WPBakery - Use the Shortcode element
* Divi - Use the Code module
* Beaver Builder - Use the HTML module
* Any page builder that supports shortcodes

= Can I customize the appearance? =

Yes, you have multiple customization options:
* Built-in color settings for navigation and pagination
* Custom CSS editor with syntax highlighting
* WordPress theme customizer integration
* PHP filters and hooks for developers

= Is the plugin translation ready? =

Yes! The plugin is fully internationalized with all strings wrapped in translation functions. It includes a .pot file for easy translation into any language.

= Does it affect my site's performance? =

No. The plugin is optimized for performance:
* Assets only load on pages containing the shortcode
* Conditional loading prevents unnecessary HTTP requests
* Images support lazy loading
* Efficient database queries with proper indexing
* Minified and optimized JavaScript/CSS

= Is it accessible? =

Yes! The plugin is WCAG 2.1 AA compliant with:
* Full keyboard navigation support
* Screen reader compatibility
* Proper ARIA labels and roles
* Sufficient color contrast ratios
* Focus indicators for all interactive elements

= How do I get support? =

* **Documentation**: Check the [GitHub Wiki](https://github.com/Nicolaszabala/product-slider-plugin/wiki)
* **Support Forum**: Post in the [WordPress.org support forum](https://wordpress.org/support/plugin/woocommerce-product-slider/)
* **Bug Reports**: Submit on [GitHub Issues](https://github.com/Nicolaszabala/product-slider-plugin/issues)

= Can I contribute? =

Yes! Contributions are welcome on [GitHub](https://github.com/Nicolaszabala/product-slider-plugin). Please read the contributing guidelines before submitting pull requests.

== Screenshots ==

1. **Admin Interface** - Product selection with real-time WooCommerce search and live preview
2. **CSS Editor** - Built-in CodeMirror 6 editor with syntax highlighting and dark theme
3. **Shortcode Generator** - One-click copy functionality with visual feedback
4. **Frontend Desktop View** - Responsive slider displaying 4 products on desktop
5. **Frontend Mobile View** - Touch-enabled slider with 1 product on mobile devices
6. **Color Customization** - Easy color picker for navigation arrows and pagination dots

== Changelog ==

= 1.0.0 - 2025-01-XX =

**Initial Public Release**

🎉 Welcome to WooCommerce Product Slider!

**Features:**
* Visual product selection interface with real-time search
* React-based admin interface for modern UX
* Custom CSS editor powered by CodeMirror 6
* Shortcode generator with clipboard support
* Responsive Swiper.js sliders with touch support
* Color customization for navigation and pagination
* Autoplay and loop configuration options
* Behavior settings (speed, transitions)

**Technical:**
* TDD methodology - 71 unit tests, 161 assertions
* WordPress Coding Standards compliant (0 errors)
* PHPStan Level 8 static analysis (0 errors)
* OWASP security compliance (Top 10 2021)
* WCAG 2.1 AA accessibility compliance
* Conflict prevention system
* Namespaced architecture
* Optimized performance

**WooCommerce Integration:**
* Full product compatibility (simple, variable, grouped)
* Sale badge display
* Price display (including sale prices)
* Add to cart functionality
* Stock status integration
* Product image handling (with lazy loading)

**Browser Support:**
* Chrome (latest 2 versions)
* Firefox (latest 2 versions)
* Safari (latest 2 versions)
* Edge (latest version)
* Mobile browsers (iOS Safari, Chrome Mobile)

**Tested With:**
* WordPress 6.2 to 6.7
* WooCommerce 8.2 to 9.0
* PHP 7.4 to 8.3

== Upgrade Notice ==

= 1.0.0 =
🎉 Welcome to the initial release of WooCommerce Product Slider! This plugin brings professional product sliders to your WooCommerce store with an intuitive interface and powerful customization options.

== Additional Information ==

= Credits =

This plugin uses the following open-source libraries:

* **Swiper.js** (v11.x) - Modern mobile touch slider by Vladimir Kharlampidi (MIT License)
* **CodeMirror** (v6.x) - Versatile text editor by Marijn Haverbeke (MIT License)
* **WordPress & WooCommerce** - Open source platforms by Automattic (GPL)

= Links =

* [Plugin Homepage](https://wordpress.org/plugins/woocommerce-product-slider/)
* [Documentation](https://github.com/Nicolaszabala/product-slider-plugin/wiki)
* [GitHub Repository](https://github.com/Nicolaszabala/product-slider-plugin)
* [Bug Reports](https://github.com/Nicolaszabala/product-slider-plugin/issues)
* [Support Forum](https://wordpress.org/support/plugin/woocommerce-product-slider/)

= Privacy Policy =

WooCommerce Product Slider does not:
* Track users
* Collect personal data
* Make external HTTP requests
* Use cookies
* Phone home

All data stays on your server. The plugin only uses WordPress and WooCommerce built-in functionality.

= License =

This plugin is licensed under GPLv2 or later.

```
WooCommerce Product Slider
Copyright (C) 2025 Nicolas Zabala

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
```

== Support This Plugin ==

If you find this plugin useful, please:
* ⭐ Rate it 5 stars on WordPress.org
* 📝 Leave a detailed review
* 🐛 Report bugs on GitHub
* 💡 Suggest features
* 🔧 Contribute code
* 📢 Share with others

Made with ❤️ following WordPress and WooCommerce best practices.
