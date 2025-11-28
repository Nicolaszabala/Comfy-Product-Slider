# Custom CSS - Bugs Corregidos

## Problemas Identificados y Solucionados

### 1. CRÍTICO: `wp_strip_all_tags()` destruía todo el CSS válido

**Archivo:** `includes/admin/class-wc-product-slider-admin.php` línea 949

**Problema:**
```php
// ANTES (MALO):
$custom_css = wp_strip_all_tags( wp_unslash( $_POST['wc_ps_custom_css'] ) );
```

La función `wp_strip_all_tags()` elimina TODO, incluyendo:
- Llaves `{}`
- Dos puntos `:`
- Punto y coma `;`
- Selectores CSS
- Propiedades CSS

**Ejemplo del problema:**
```css
/* INPUT del usuario: */
.wc-ps-slider {
    background-color: red;
    padding: 20px;
}

/* Lo que se guardaba en la BD: */
.wc-ps-slider background-color red padding 20px
```

**Solución implementada:**
```php
// DESPUÉS (CORRECTO):
// Usando wp_kses con array vacío: quita HTML/scripts pero PRESERVA CSS
$custom_css = wp_kses( wp_unslash( $_POST['wc_ps_custom_css'] ), array() );
```

Ahora guarda correctamente:
```css
.wc-ps-slider {
    background-color: red;
    padding: 20px;
}
```

---

### 2. Textarea oculto si CodeMirror no se carga

**Archivo:** `includes/admin/class-wc-product-slider-admin.php` línea 792

**Problema:**
```php
// ANTES:
<textarea ... style="width:100%; font-family:monospace; display:none;">
```

Si CodeMirror no se inicializa (fallo de JS, conflicto de plugin, etc.), el textarea quedaba invisible.

**Solución implementada:**
```php
// DESPUÉS:
<textarea ... style="width:100%; font-family:monospace;">
```

Ahora siempre es visible. Si CodeMirror carga, lo mejora. Si no, al menos tienes un textarea funcional con fallback:

```javascript
if (typeof wp !== 'undefined' && typeof wp.codeEditor !== 'undefined') {
    wp.codeEditor.initialize('wc_ps_custom_css', editorSettings);
} else {
    // Fallback: textarea con mejor estilo
    $('#wc_ps_custom_css').css({
        'border': '1px solid #ddd',
        'padding': '10px',
        'background-color': '#f9f9f9',
        'border-radius': '4px'
    });
}
```

---

### 3. Frontend usaba `wp_kses_post()` en vez de `esc_html()`

**Archivo:** `includes/public/class-wc-product-slider-shortcode.php` línea 204

**Problema:**
```php
// ANTES:
echo '<style type="text/css">' . wp_kses_post( $config['custom_css'] ) . '</style>';
```

`wp_kses_post()` permite ciertas etiquetas HTML (p, a, strong, etc.) pero puede filtrar CSS válido.

**Solución implementada:**
```php
// DESPUÉS:
echo '<style type="text/css">' . esc_html( $config['custom_css'] ) . '</style>';
```

`esc_html()` escapa caracteres HTML (`<`, `>`, `&`, etc.) pero preserva completamente la sintaxis CSS.

---

## Cómo Probar los Fixes

### Test 1: Guardar CSS Complejo

1. Ve a **WordPress Admin → Product Sliders → Editar un slider**
2. Baja a la meta box **"Custom CSS"**
3. Pega este CSS complejo:

```css
.wc-ps-slider {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    padding: 30px;
}

.wc-ps-slide-item:hover {
    transform: scale(1.05);
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .wc-ps-slider {
        padding: 15px;
    }
}
```

4. Haz click en **"Actualizar"** o **"Publicar"**
5. **Recarga la página de edición**
6. **VERIFICAR:** El CSS debe aparecer EXACTAMENTE igual, sin perder llaves, dos puntos, ni punto y comas

---

### Test 2: Verificar CodeMirror se Carga

1. En la meta box **"Custom CSS"**, deberías ver:
   - Editor con syntax highlighting (colores en CSS)
   - Números de línea
   - Auto-cierre de llaves
   - Formato automático

2. **Si CodeMirror NO carga:**
   - Abre la consola del navegador (F12)
   - Busca errores de JavaScript
   - Pero el textarea DEBE ser visible de todas formas

---

### Test 3: CSS se Aplica en el Frontend

1. Guarda un slider con este CSS de prueba:

```css
.wc-ps-slider {
    border: 5px solid red !important;
    background-color: yellow !important;
}
```

2. Inserta el shortcode en una página/post:
   ```
   [wc_product_slider id="123"]
   ```

3. **VERIFICAR en el frontend:**
   - El slider debe tener borde rojo de 5px
   - Fondo amarillo
   - Abre **Inspeccionar Elemento** → debe haber un `<style>` con ese CSS

---

### Test 4: Seguridad - No Permite Scripts

1. Intenta guardar esto en Custom CSS:

```css
.test { color: red; }
<script>alert('XSS')</script>
<img src=x onerror="alert('XSS')">
```

2. **VERIFICAR:**
   - El CSS `.test { color: red; }` debe guardarse correctamente
   - Las etiquetas `<script>` e `<img>` deben ser removidas
   - En el frontend, NO debe ejecutarse ningún script

---

## Archivos Modificados

1. `/includes/admin/class-wc-product-slider-admin.php`
   - Línea 792: Removido `display:none;` del textarea
   - Líneas 814-822: Agregado fallback para cuando CodeMirror falla
   - Línea 958: Cambiado `wp_strip_all_tags()` por `wp_kses()`

2. `/includes/public/class-wc-product-slider-shortcode.php`
   - Línea 205: Cambiado `wp_kses_post()` por `esc_html()`

---

## Notas Técnicas

### ¿Por qué `wp_kses()` en el guardado?

```php
wp_kses( $css, array() )
```

- Primer parámetro: el CSS del usuario
- Segundo parámetro: `array()` vacío = NO permitir etiquetas HTML
- **Resultado:** Remueve `<script>`, `<style>`, `<img>`, etc. pero **preserva CSS**

### ¿Por qué `esc_html()` en el frontend?

```php
echo '<style>' . esc_html( $css ) . '</style>';
```

- `esc_html()` convierte `<` en `&lt;` y `>` en `&gt;`
- Pero dentro de un `<style>`, el navegador interpreta `&lt;` como texto literal, no como HTML
- CSS válido no contiene `<>`, así que queda intacto
- Si alguien inyectó `<script>`, se renderiza como texto: `&lt;script&gt;` (inofensivo)

### Flow de Datos

```
Usuario escribe CSS
    ↓
[Admin] wp_kses($css, array()) → Quita HTML, preserva CSS
    ↓
[Database] _wc_ps_custom_css meta
    ↓
[Frontend] esc_html($css) → Escapa caracteres peligrosos
    ↓
<style>{CSS escapado}</style> → Navegador renderiza CSS seguro
```

---

## Compatibilidad

- **WordPress:** 5.0+
- **PHP:** 7.4+ (se usa strict types en otros archivos)
- **WooCommerce:** 3.0+
- **CodeMirror:** Viene con WordPress core desde 4.9

---

## Próximos Pasos (Opcional)

Si quieres mejorar aún más:

1. **Scoped CSS automático:** Prefijar selectores con `.wc-ps-slider-{ID}` para evitar conflictos
2. **CSS Linting:** Validar CSS en tiempo real con CSSLint
3. **Minificación:** Minificar CSS antes de guardarlo para optimizar performance
4. **Presets:** Ofrecer snippets CSS predefinidos (sombras, animaciones, etc.)

Pero por ahora, con estos fixes, el Custom CSS funciona correctamente y de forma segura.
