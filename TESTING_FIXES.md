# Testing Guide - Bug Fixes

## Bugs Fixed

### 1. ✅ Products Being Deleted on Update
**Problem:** When you selected products and clicked "Update", they disappeared from the admin.

**Root Cause:** The code expected a comma-separated string but received an array from `<select multiple>`.

**Fix Location:** `includes/admin/class-wc-product-slider-admin.php:847-854`

**Test Steps:**
1. Go to WordPress Admin → Product Sliders → Edit any slider
2. In the "Products" metabox, select 2-3 WooCommerce products using Select2
3. Click "Update" button
4. ✅ **EXPECTED:** Products remain selected in the dropdown
5. ❌ **BEFORE:** Products disappeared after update

---

### 2. ✅ Custom Slides Not Showing in Frontend
**Problem:** Custom slides configured in admin were completely ignored in the frontend.

**Root Cause:** The shortcode handler never loaded or rendered custom slides.

**Fix Location:** `includes/public/class-wc-product-slider-shortcode.php`

**Test Steps:**
1. Edit a slider in admin
2. Add a custom slide:
   - Click "+ Add Custom Slide"
   - Upload an image
   - Add a URL (e.g., https://example.com)
   - Add a title
3. Click "Update"
4. Add shortcode `[wc_product_slider id="X"]` to a page
5. ✅ **EXPECTED:** Custom slide appears mixed with products
6. ❌ **BEFORE:** Only products showed, custom slides were invisible

---

### 3. ✅ Display Options Not Working
**Problem:** Admin toggles (Show Title, Show Price, etc.) had no effect on frontend.

**Root Cause:** Shortcode didn't load display options from database.

**Test Steps:**
1. Edit a slider
2. In "Display Options" metabox:
   - ✅ Check "Show Product Title"
   - ✅ Check "Show Product Price"
   - ❌ Uncheck "Show Button"
   - ❌ Uncheck "Show Product Image"
3. Click "Update"
4. View the slider on frontend
5. ✅ **EXPECTED:** Only title and price show (no button, no image)
6. ❌ **BEFORE:** All elements showed regardless of settings

---

### 4. ✅ Slider Heading Not Displaying
**Problem:** "Slider Heading" field in admin was saved but never displayed.

**Test Steps:**
1. Edit a slider
2. In "Display Options" → "Slider Heading", enter: "Featured Products"
3. Click "Update"
4. View slider on frontend
5. ✅ **EXPECTED:** See `<h2>Featured Products</h2>` above the slider
6. ❌ **BEFORE:** Heading was invisible

---

### 5. ✅ Clickable Images Setting Not Working
**Problem:** "Make Images Clickable" toggle had no effect.

**Test Steps:**
1. Edit a slider
2. In "Display Options":
   - ✅ Check "Make Images Clickable"
3. Add products AND custom slides with URLs
4. View frontend and inspect HTML
5. ✅ **EXPECTED:**
   - Products: `<a href="product-page">` wraps content
   - Custom slides: `<a href="custom-url">` wraps content
6. ❌ **BEFORE:** Always wrapped in `<a>` regardless of setting

---

## Complete Test Scenarios

### Scenario A: Products Only
1. Create a new slider
2. Select 3 WooCommerce products
3. Set "Show Title" = ON, "Show Price" = ON, "Show Button" = OFF
4. Click "Update"
5. Add shortcode to a page
6. **Verify:**
   - ✅ 3 products display
   - ✅ Titles visible
   - ✅ Prices visible
   - ✅ No buttons
   - ✅ Products persist after updating

### Scenario B: Custom Slides Only
1. Create a new slider
2. Don't select any products
3. Add 2 custom slides with images and URLs
4. Click "Update"
5. Add shortcode to a page
6. **Verify:**
   - ✅ 2 custom slides display
   - ✅ Images are clickable (link to URLs)
   - ✅ Titles show if provided

### Scenario C: Mixed (Products + Custom Slides)
1. Create a new slider
2. Select 2 products
3. Add 2 custom slides
4. Set slider heading: "Our Store"
5. Click "Update"
6. Add shortcode to a page
7. **Verify:**
   - ✅ Heading "Our Store" appears above slider
   - ✅ 4 total slides (2 products + 2 custom)
   - ✅ All slides mixed together
   - ✅ Swiper navigation works
   - ✅ Autoplay works (if enabled)

### Scenario D: Empty Slider (Edge Case)
1. Create a new slider
2. Don't select products
3. Don't add custom slides
4. Click "Update"
5. Add shortcode to a page
6. **Verify:**
   - ✅ Shows error: "No products or custom slides selected for this slider"
   - ✅ Error only visible to admins (logged-in users with edit_posts capability)
   - ✅ Regular visitors see nothing (no error message)

---

## Technical Changes Summary

### File: `includes/admin/class-wc-product-slider-admin.php`
```php
// BEFORE (broken):
$products = sanitize_text_field( wp_unslash( $_POST['wc_ps_products'] ) );
$products_array = array_filter( array_map( 'absint', explode( ',', $products ) ) );

// AFTER (fixed):
if ( isset( $_POST['wc_ps_products'] ) && is_array( $_POST['wc_ps_products'] ) ) {
    $products_array = array_filter( array_map( 'absint', wp_unslash( $_POST['wc_ps_products'] ) ) );
    update_post_meta( $post_id, '_wc_ps_products', $products_array );
} else {
    update_post_meta( $post_id, '_wc_ps_products', array() );
}
```

### File: `includes/public/class-wc-product-slider-shortcode.php`

**New Methods Added:**
- `merge_slides()` - Combines products and custom slides
- `render_product_slide()` - Renders a single product with all display options
- `render_custom_slide()` - Renders a custom image slide

**Enhanced `get_slider_config()`:**
- Now loads `custom_slides`
- Loads all display options (show_title, show_price, etc.)
- Returns 15 config values instead of 6

**Enhanced `render()` validation:**
```php
// BEFORE:
if ( empty( $config['products'] ) ) {
    return $this->render_error( 'No products selected' );
}

// AFTER:
if ( empty( $config['products'] ) && empty( $config['custom_slides'] ) ) {
    return $this->render_error( 'No products or custom slides selected' );
}
```

---

## Regression Testing

Ensure existing functionality still works:

1. ✅ Swiper.js navigation (arrows)
2. ✅ Pagination dots
3. ✅ Autoplay
4. ✅ Loop setting
5. ✅ Responsive breakpoints (1→2→3→4 slides)
6. ✅ Custom CSS field
7. ✅ Color customization (primary, secondary, button colors)
8. ✅ Border radius and slide gap
9. ✅ Product ratings (if enabled)
10. ✅ Sale badges on products

---

## Performance Notes

**No Performance Impact:**
- Only loads custom slides if they exist (not on every slider)
- Uses same post meta queries as before
- No additional database calls
- No changes to frontend JavaScript or CSS

**Backward Compatibility:**
- ✅ Existing sliders without custom slides work exactly as before
- ✅ Existing sliders with only products work the same
- ✅ No database migrations needed
- ✅ No settings need to be re-saved

---

## Next Steps After Testing

If all tests pass:

1. ✅ Mark this bug fix as complete
2. ✅ Update CHANGELOG.md with version 1.0.1
3. ✅ Run full test suite: `composer test`
4. ✅ Run PHPCS: `composer phpcs`
5. ✅ Update PROJECT_SUMMARY.md
6. ✅ Tag release: `git tag v1.0.1`
7. ✅ Deploy to production

If any test fails:
1. Document the failure scenario
2. Create a GitHub issue
3. Request additional fixes

---

**Testing Date:** _________________

**Tested By:** _________________

**Result:** ✅ PASS / ❌ FAIL

**Notes:**
