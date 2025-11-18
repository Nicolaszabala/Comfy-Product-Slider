# WooCommerce Product Slider - Testing Plan

**Version:** 1.0.0
**Last Updated:** 2025-01-18
**Status:** Ready for Testing

---

## Table of Contents

1. [Overview](#overview)
2. [Testing Environment](#testing-environment)
3. [Automated Testing](#automated-testing)
4. [Manual Testing](#manual-testing)
5. [Security Testing](#security-testing)
6. [Performance Testing](#performance-testing)
7. [Compatibility Testing](#compatibility-testing)
8. [Accessibility Testing](#accessibility-testing)
9. [User Acceptance Testing](#user-acceptance-testing)
10. [Test Cases](#test-cases)
11. [Bug Reporting](#bug-reporting)
12. [Release Criteria](#release-criteria)

---

## Overview

This document outlines the comprehensive testing strategy for the WooCommerce Product Slider plugin before submission to WordPress.org.

### Testing Objectives

- ✅ Ensure 100% functionality across supported environments
- ✅ Verify security compliance (OWASP Top 10)
- ✅ Confirm accessibility standards (WCAG 2.1 AA)
- ✅ Validate performance benchmarks
- ✅ Test compatibility with WordPress/WooCommerce ecosystem
- ✅ Verify user experience flows

---

## Testing Environment

### Required Test Environments

#### Environment 1: Minimum Requirements
```
OS: Ubuntu 20.04 LTS
Web Server: Apache 2.4
PHP: 7.4.33
WordPress: 6.2
WooCommerce: 8.2
MySQL: 5.7
Theme: Twenty Twenty-Three
```

#### Environment 2: Recommended
```
OS: Ubuntu 22.04 LTS
Web Server: Nginx 1.22
PHP: 8.2
WordPress: 6.4+
WooCommerce: 8.5+
MySQL: 8.0
Theme: Storefront (WooCommerce)
```

#### Environment 3: Latest
```
OS: Ubuntu 24.04 LTS
Web Server: Nginx 1.26
PHP: 8.3
WordPress: 6.7+ (latest)
WooCommerce: 9.0+ (latest)
MySQL: 8.4
Theme: Various popular themes
```

### Browser Matrix

| Browser | Versions | Platforms |
|---------|----------|-----------|
| Chrome | Latest, Latest-1 | Windows, macOS, Linux |
| Firefox | Latest, Latest-1, ESR | Windows, macOS, Linux |
| Safari | Latest, Latest-1 | macOS, iOS |
| Edge | Latest | Windows |
| Opera | Latest | Windows, macOS |

### Mobile Devices

| Device | OS | Browser |
|--------|-----|---------|
| iPhone 14 Pro | iOS 17 | Safari |
| iPhone 12 | iOS 16 | Safari, Chrome |
| iPad Pro | iPadOS 17 | Safari |
| Samsung Galaxy S23 | Android 14 | Chrome, Samsung Internet |
| Google Pixel 7 | Android 14 | Chrome |

---

## Automated Testing

### Unit Tests (PHPUnit)

**Current Status:** ✅ 71 tests, 161 assertions, 100% passing

**Run Tests:**
```bash
composer test
```

**Coverage Goal:** 80%+ code coverage

**Test Suites:**
- `WC_Product_Slider_Activator` - Activation hooks
- `WC_Product_Slider_Deactivator` - Deactivation hooks
- `WC_Product_Slider_CPT` - Custom post type registration
- `WC_Product_Slider_Sanitizer` - Input sanitization (16 tests)
- `WC_Product_Slider_Image_Handler` - Image processing
- `WC_Product_Slider_Admin` - Admin interface
- `WC_Product_Slider_Shortcode` - Shortcode rendering
- `WC_Product_Slider_Public` - Frontend functionality

### Coding Standards (PHPCS)

**Run Check:**
```bash
composer run-script phpcs
```

**Standards:**
- WordPress-Core
- WordPress-Docs
- WordPress-Extra

**Auto-fix:**
```bash
composer run-script phpcbf
```

### Static Analysis (PHPStan)

**Run Analysis:**
```bash
composer run-script phpstan
```

**Level:** 8 (maximum strictness)

### JavaScript Linting (ESLint)

**Run Lint:**
```bash
npm run lint:js
```

**Standards:**
- @wordpress/eslint-plugin
- Prettier formatting

**Auto-fix:**
```bash
npm run lint:js -- --fix
```

### CSS Linting (Stylelint)

**Run Lint:**
```bash
npm run lint:css
```

### Conflict Prevention

**Run Check:**
```bash
bash bin/check-conflicts.sh
```

**Validates:**
- Namespace isolation
- Function prefixing
- Global variable namespacing
- Hook naming conventions
- Enqueue handle prefixing
- Shortcode naming
- Database option prefixing
- Post meta key prefixing

### Continuous Integration

**GitHub Actions Workflow:**
- ✅ Conflict Prevention Check
- ✅ PHPCS (WordPress Coding Standards)
- ✅ PHPStan Level 8
- ✅ PHPUnit (PHP 7.4, 8.0, 8.1, 8.2, 8.3)
- ✅ JavaScript Linting
- ✅ Build Verification

---

## Manual Testing

### Admin Interface Testing

#### Test Case 1: Create New Slider
**Priority:** Critical
**Steps:**
1. Navigate to WP Admin → Product Sliders → Add New
2. Enter title: "Test Slider 1"
3. Verify Product Selector loads
4. Search for product: "cap"
5. Click "Add" on a product
6. Verify product appears in "Selected Products"
7. Click "Publish"
8. Verify shortcode appears in sidebar

**Expected Result:**
- Product search works with debouncing (500ms)
- Products display with thumbnail and price
- Selected products saved correctly
- Shortcode generated: `[wc_product_slider id="{ID}"]`

**Test Data:**
- At least 10 test products in WooCommerce
- Products with images
- Products on sale
- Variable products

---

#### Test Case 2: Product Selector Features
**Priority:** Critical
**Steps:**
1. Search for products
2. Add 5 different products
3. Remove a product from selected list
4. Save draft
5. Reload page
6. Verify selected products persisted

**Expected Result:**
- Search returns relevant results
- Add/remove functions work
- Data persists after save
- No JavaScript errors in console

---

#### Test Case 3: Design Settings
**Priority:** High
**Steps:**
1. Edit existing slider
2. Set Primary Color: #FF0000 (red)
3. Set Secondary Color: #00FF00 (green)
4. Save slider
5. View slider on frontend
6. Verify navigation arrows are red

**Expected Result:**
- Color picker works
- Colors saved correctly
- Frontend reflects color changes

---

#### Test Case 4: Behavior Settings
**Priority:** High
**Steps:**
1. Enable Autoplay
2. Set speed to 2000ms
3. Enable Loop
4. Save slider
5. View frontend
6. Verify slider auto-advances every 2 seconds
7. Verify slider loops back to first slide

**Expected Result:**
- All settings save correctly
- Frontend behavior matches configuration
- Slider stops on hover (UX best practice)

---

#### Test Case 5: Custom CSS Editor
**Priority:** Medium
**Steps:**
1. Open CSS Editor metabox
2. Add custom CSS:
   ```css
   .wc-ps-product {
       border: 2px solid red;
   }
   ```
3. Save slider
4. View frontend
5. Verify product cards have red border
6. Edit CSS to change border to blue
7. Save and verify change

**Expected Result:**
- CodeMirror editor loads
- Syntax highlighting works
- CSS saves correctly
- Frontend applies custom CSS
- No CSS conflicts with theme

---

#### Test Case 6: Shortcode Copy
**Priority:** High
**Steps:**
1. Publish a slider
2. Click "Copy" button in Shortcode metabox
3. Verify "Copied!" feedback appears
4. Paste in text editor
5. Verify shortcode format is correct

**Expected Result:**
- Click copies to clipboard
- Visual feedback shows for 2-3 seconds
- Shortcode format: `[wc_product_slider id="{ID}"]`

---

### Frontend Testing

#### Test Case 7: Slider Display
**Priority:** Critical
**Steps:**
1. Create new post/page
2. Add shortcode: `[wc_product_slider id="{ID}"]`
3. Publish
4. View frontend
5. Verify slider displays
6. Check all products appear
7. Test navigation arrows
8. Test pagination dots

**Expected Result:**
- Slider renders correctly
- All products visible
- Navigation functional
- Images load (lazy loading)
- No console errors

---

#### Test Case 8: Responsive Behavior
**Priority:** Critical
**Test Viewports:**
1. Mobile (375px) - 1 slide
2. Tablet (768px) - 3 slides
3. Desktop (1024px) - 4 slides
4. Large (1440px) - 4 slides

**Steps:**
1. View slider at each breakpoint
2. Verify slide count changes
3. Test touch/swipe on mobile
4. Test navigation visibility

**Expected Result:**
- Breakpoints work correctly
- Touch gestures functional on mobile
- Navigation hidden on small screens
- No horizontal scroll

---

#### Test Case 9: Product Interaction
**Priority:** Critical
**Steps:**
1. Click product image
2. Verify navigates to product page
3. Back to slider page
4. Click "Add to Cart" button
5. Verify product added to cart
6. Check cart icon updates

**Expected Result:**
- Product links work
- Add to cart functions correctly
- WooCommerce AJAX works
- Cart updates without page reload

---

#### Test Case 10: Sale Badge
**Priority:** Medium
**Steps:**
1. Add product on sale to slider
2. View frontend
3. Verify "Sale!" badge appears
4. Check badge styling

**Expected Result:**
- Badge displays for sale products only
- Badge visible and readable
- Positioned correctly (top-right)

---

### Error Handling

#### Test Case 11: Invalid Shortcode
**Priority:** High
**Steps:**
1. Add shortcode with non-existent ID: `[wc_product_slider id="99999"]`
2. View as admin
3. View as logged-out user

**Expected Result:**
- Admin sees error message
- Non-admin sees nothing (security)
- No PHP errors/warnings

---

#### Test Case 12: Empty Slider
**Priority:** Medium
**Steps:**
1. Create slider with no products
2. Publish
3. View frontend

**Expected Result:**
- Admin sees: "No products selected"
- Non-admin sees nothing
- No errors

---

#### Test Case 13: Deleted Products
**Priority:** High
**Steps:**
1. Create slider with 5 products
2. Delete 2 products from WooCommerce
3. View slider frontend

**Expected Result:**
- Slider displays only remaining 3 products
- No errors for deleted products
- Graceful degradation

---

## Security Testing

### SQL Injection Testing

**Test Cases:**
1. Product search with SQL injection attempts
2. Shortcode parameter injection
3. Custom CSS injection attempts
4. Admin form submissions with malicious data

**Tools:**
- sqlmap
- Manual injection attempts
- Burp Suite

**Expected Result:**
- All inputs sanitized
- Prepared statements used
- No SQL errors visible

---

### XSS (Cross-Site Scripting)

**Test Cases:**
1. Product names with `<script>` tags
2. Custom CSS with JavaScript
3. Shortcode parameters with XSS payloads

**Payloads:**
```
<script>alert('XSS')</script>
javascript:alert('XSS')
<img src=x onerror=alert('XSS')>
```

**Expected Result:**
- All output escaped
- Scripts don't execute
- HTML stripped where appropriate

---

### CSRF (Cross-Site Request Forgery)

**Test Cases:**
1. Save slider without nonce
2. Expired nonce submission
3. Forged nonce from different user

**Expected Result:**
- All actions require valid nonce
- Nonces verified before processing
- Failed attempts logged

---

### File Upload Security

**Test Cases:**
1. Upload PHP file disguised as image
2. Upload file with double extension (.jpg.php)
3. Upload oversized files

**Expected Result:**
- Only allowed MIME types accepted
- File type validation (not just extension)
- Max size enforced
- Files stored securely

---

### Authentication & Authorization

**Test Cases:**
1. Non-admin tries to create slider
2. Editor tries to access settings
3. Guest tries to use admin features

**Expected Result:**
- Proper capability checks
- Unauthorized access denied
- Graceful error messages

---

## Performance Testing

### Page Load Time

**Metrics:**
- Time to First Byte (TTFB): < 200ms
- First Contentful Paint (FCP): < 1.8s
- Largest Contentful Paint (LCP): < 2.5s
- Time to Interactive (TTI): < 3.5s

**Tools:**
- GTmetrix
- Google PageSpeed Insights
- WebPageTest

---

### Asset Loading

**Test Cases:**
1. Page without shortcode - verify NO slider assets load
2. Page with shortcode - verify assets load
3. Multiple sliders on one page

**Expected Result:**
- Conditional loading works
- No duplicate assets
- Assets minified and optimized

---

### Database Queries

**Test Cases:**
1. Slider with 10 products
2. Slider with 50 products
3. Page with 3 sliders

**Tools:**
- Query Monitor plugin
- MySQL slow query log

**Expected Result:**
- < 10 queries per slider
- No N+1 query problems
- Queries optimized with indexes

---

### Memory Usage

**Test Cases:**
1. Create slider with 100 products
2. Display 5 sliders on one page
3. Admin interface with large product catalog

**Tools:**
- PHP memory_get_usage()
- Server monitoring

**Expected Result:**
- Memory usage < 64MB
- No memory leaks
- Graceful handling of limits

---

## Compatibility Testing

### WordPress Versions

**Test Matrix:**
| WordPress | Status | Notes |
|-----------|--------|-------|
| 6.2 | ✅ Required | Minimum version |
| 6.3 | ✅ Test | |
| 6.4 | ✅ Test | |
| 6.5 | ✅ Test | Latest stable |
| 6.6-beta | ⚠️ Test | Upcoming release |

---

### WooCommerce Versions

**Test Matrix:**
| WooCommerce | Status | Notes |
|-------------|--------|-------|
| 8.2 | ✅ Required | Minimum version |
| 8.5 | ✅ Test | |
| 8.9 | ✅ Test | |
| 9.0 | ✅ Test | Latest stable |
| 9.1-beta | ⚠️ Test | Upcoming release |

---

### PHP Versions

**Test Matrix:**
| PHP | Status | CI | Notes |
|-----|--------|-----|-------|
| 7.4 | ✅ Required | ✅ | Minimum version |
| 8.0 | ✅ Supported | ✅ | |
| 8.1 | ✅ Supported | ✅ | |
| 8.2 | ✅ Recommended | ✅ | |
| 8.3 | ✅ Supported | ✅ | Latest |

---

### Popular Themes

**Test with:**
1. Twenty Twenty-Three (default)
2. Twenty Twenty-Four (FSE)
3. Storefront (WooCommerce official)
4. Astra
5. GeneratePress
6. OceanWP
7. Kadence
8. Neve

**Verify:**
- Styling compatibility
- No JavaScript conflicts
- Responsive design works
- No z-index issues

---

### Page Builders

**Test with:**
1. Gutenberg (Block Editor)
2. Elementor Free
3. Elementor Pro
4. WPBakery
5. Divi Builder
6. Beaver Builder

**Test Cases:**
- Insert via shortcode widget
- Visual editor integration
- Live preview works
- Save/publish functions

---

### Popular Plugins

**Test compatibility with:**
1. Yoast SEO
2. Rank Math SEO
3. WP Rocket (caching)
4. W3 Total Cache
5. WP Super Cache
6. WPML (translation)
7. Polylang
8. Contact Form 7
9. WooCommerce Subscriptions
10. WooCommerce Memberships

---

## Accessibility Testing

### WCAG 2.1 AA Compliance

**Automated Testing:**
- WAVE (Web Accessibility Evaluation Tool)
- axe DevTools
- Lighthouse Accessibility audit

**Manual Testing:**

#### Keyboard Navigation
**Test Cases:**
1. Tab through all interactive elements
2. Arrow keys in slider
3. Enter/Space to activate
4. Escape to close (if applicable)

**Expected Result:**
- All elements keyboard accessible
- Logical tab order
- Visible focus indicators
- No keyboard traps

---

#### Screen Reader Testing

**Tools:**
- NVDA (Windows)
- JAWS (Windows)
- VoiceOver (macOS/iOS)
- TalkBack (Android)

**Test Cases:**
1. Navigate through slider
2. Understand product information
3. Use controls (prev/next)
4. Add to cart

**Expected Result:**
- Proper ARIA labels
- Meaningful image alt text
- Clear announcements
- Logical reading order

---

#### Color Contrast

**Test all text/background combinations:**
- Navigation arrows vs background
- Pagination dots
- Product titles
- Prices
- Buttons

**Tool:** Chrome DevTools Color Picker

**Requirement:** 4.5:1 for normal text, 3:1 for large text

---

#### Focus Indicators

**Test:**
- All interactive elements have visible focus
- Focus outline not removed
- Sufficient contrast (3:1)

---

## User Acceptance Testing (UAT)

### Test Scenarios

#### Scenario 1: Beginner User
**Persona:** Small shop owner, limited tech knowledge

**Tasks:**
1. Install and activate plugin
2. Create first slider
3. Add 5 products
4. Customize colors
5. Add shortcode to homepage
6. Publish and verify

**Success Criteria:**
- Completes all tasks without help
- Finds all features intuitive
- Understands shortcode usage
- Satisfied with result

---

#### Scenario 2: Advanced User
**Persona:** Web developer, wants customization

**Tasks:**
1. Create slider with specific products
2. Write custom CSS for unique design
3. Use PHP filter to modify output
4. Test on staging environment
5. Deploy to production

**Success Criteria:**
- Finds CSS editor powerful
- Hooks/filters well documented
- Code quality meets standards
- Performance acceptable

---

#### Scenario 3: Agency User
**Persona:** Managing multiple client sites

**Tasks:**
1. Set up sliders on 3 different sites
2. Use different themes per site
3. Export/import slider configurations
4. Train clients on usage

**Success Criteria:**
- Consistent behavior across sites
- Theme compatibility
- Easy to teach clients
- Reliable and stable

---

## Test Cases

### Critical Path Test Cases

#### TCP-001: Create and Display Slider
**Priority:** P0 (Blocker)
**Preconditions:** WordPress + WooCommerce installed, 10+ products exist

**Steps:**
1. Create new slider titled "Homepage Slider"
2. Select 6 products via Product Selector
3. Set primary color to #0073aa
4. Enable autoplay with 3000ms delay
5. Publish slider
6. Copy shortcode
7. Add shortcode to homepage
8. View homepage

**Expected Result:**
- Slider displays with 6 products
- Navigation arrows are blue (#0073aa)
- Slider auto-advances every 3 seconds
- All product images, titles, prices display
- "Add to Cart" buttons functional

**Pass/Fail:**

---

#### TCP-002: Mobile Responsiveness
**Priority:** P0 (Blocker)

**Steps:**
1. Open slider page on iPhone (375px width)
2. Verify 1 product displays at a time
3. Swipe left/right
4. Open on tablet (768px)
5. Verify 3 products display
6. Open on desktop (1440px)
7. Verify 4 products display

**Expected Result:**
- Correct slide count at each breakpoint
- Touch gestures work on mobile
- No horizontal scrolling
- Images scale appropriately

**Pass/Fail:**

---

#### TCP-003: WooCommerce Integration
**Priority:** P0 (Blocker)

**Steps:**
1. Add product on sale to slider
2. Add variable product to slider
3. Add out-of-stock product to slider
4. View frontend
5. Click product image
6. Click "Add to Cart"

**Expected Result:**
- Sale badge appears on sale product
- Variable product shows price range
- Out-of-stock product shows "Out of Stock"
- Product link goes to correct product page
- Add to Cart adds product to WooCommerce cart
- Cart icon updates

**Pass/Fail:**

---

### Additional Test Cases

#### TC-004: Custom CSS Application
**Priority:** P1 (High)

**Steps:**
1. Add custom CSS to make product titles red
2. Save slider
3. View frontend

**Expected Result:**
- Product titles display in red
- No CSS errors in console
- Theme styles not affected

---

#### TC-005: No Products Selected
**Priority:** P2 (Medium)

**Steps:**
1. Create slider with no products
2. Publish
3. View frontend as admin
4. View frontend as guest

**Expected Result:**
- Admin sees error message
- Guest sees nothing
- No PHP warnings

---

#### TC-006: Deleted Slider
**Priority:** P2 (Medium)

**Steps:**
1. Create slider
2. Add shortcode to page
3. Delete slider
4. View page

**Expected Result:**
- Page loads without errors
- Admin sees "Slider not found"
- Guest sees nothing

---

## Bug Reporting

### Bug Report Template

```markdown
**Title:** [Component] Brief description

**Priority:** P0/P1/P2/P3

**Environment:**
- WordPress: 6.4.2
- WooCommerce: 8.9.1
- PHP: 8.2
- Theme: Storefront
- Browser: Chrome 120

**Steps to Reproduce:**
1. Step one
2. Step two
3. Step three

**Expected Result:**
What should happen

**Actual Result:**
What actually happens

**Screenshots:**
[Attach screenshots]

**Console Errors:**
```
[Paste console errors]
```

**Additional Context:**
Any other relevant information
```

---

## Release Criteria

### Must Pass (Blocker)

- ✅ All automated tests passing (71/71)
- ✅ PHPCS 0 errors, 0 warnings
- ✅ PHPStan Level 8 no errors
- ✅ ESLint 0 errors
- ✅ Conflict check passes
- ✅ All P0 test cases pass
- ✅ No critical security vulnerabilities
- ✅ Works on minimum requirements (WP 6.2, WC 8.2, PHP 7.4)
- ✅ Mobile responsive (all breakpoints)
- ✅ Keyboard accessible
- ✅ Screen reader compatible

### Should Pass (High Priority)

- ✅ 90%+ P1 test cases pass
- ✅ Performance benchmarks met (LCP < 2.5s)
- ✅ Works with top 5 themes
- ✅ Works with Gutenberg
- ✅ Color contrast passes WCAG AA
- ✅ Database queries optimized

### Nice to Have

- 🔄 100% code coverage
- 🔄 Works with 10+ themes
- 🔄 Works with all page builders
- 🔄 Multi-language testing
- 🔄 RTL support verified

---

## Testing Schedule

### Week 1: Automated Testing
- Run all automated tests
- Fix any failing tests
- Achieve 80%+ coverage
- Update documentation

### Week 2: Manual Testing - Admin
- Test all admin interfaces
- Test product selector
- Test CSS editor
- Test settings forms

### Week 3: Manual Testing - Frontend
- Test slider rendering
- Test responsiveness
- Test all browsers
- Test mobile devices

### Week 4: Compatibility Testing
- Test WordPress versions
- Test WooCommerce versions
- Test PHP versions
- Test themes and plugins

### Week 5: Security & Performance
- Security audit
- Performance testing
- Load testing
- Optimization

### Week 6: Accessibility & UAT
- WCAG compliance testing
- Screen reader testing
- User acceptance testing
- Final bug fixes

---

## Test Sign-off

### Testing Team

| Role | Name | Sign-off Date |
|------|------|---------------|
| QA Lead | | |
| Developer | | |
| Security Tester | | |
| Accessibility Tester | | |
| Product Owner | | |

### Approval

**Ready for WordPress.org Submission:** [ ] Yes [ ] No

**Release Manager:** ____________________
**Date:** ____________________
**Version:** 1.0.0

---

**Document Version:** 1.0
**Last Updated:** 2025-01-18
**Next Review:** Pre-release
