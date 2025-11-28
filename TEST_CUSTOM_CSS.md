# TEST RÁPIDO: Custom CSS

## Cómo Probar el Fix en 2 Minutos

### Paso 1: Abrir el Editor

1. Ve a: **WP Admin → Product Sliders → Editar cualquier slider**
2. Baja hasta la meta box **"Custom CSS"**

### Paso 2: Pegar CSS de Prueba

Copia y pega esto:

```css
.wc-ps-slider {
    background: linear-gradient(to right, #ff6b6b, #4ecdc4);
    padding: 20px;
    border-radius: 10px;
}

.wc-ps-slide-item {
    border: 2px solid white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
```

### Paso 3: Guardar y Recargar

1. Click en **"Actualizar"**
2. **Recarga la página** (F5)

### Paso 4: VERIFICAR

El CSS debe aparecer EXACTAMENTE igual. Si ves esto:

```
.wc-ps-slider background linear-gradient...
```

**MALO** → El bug sigue ahí.

Si ves esto:

```css
.wc-ps-slider {
    background: linear-gradient(to right, #ff6b6b, #4ecdc4);
    padding: 20px;
    border-radius: 10px;
}
```

**BIEN** → El fix funciona.

---

## Test en Frontend

1. Copia el shortcode del slider (ejemplo: `[wc_product_slider id="123"]`)
2. Pégalo en una página
3. Ve al frontend
4. **Deberías ver:**
   - Fondo con gradiente rojo-azul
   - Padding de 20px
   - Bordes redondeados

5. **Inspeccionar elemento:**
   - Busca un tag `<style type="text/css">` con tu CSS

---

## Test de Seguridad

Intenta guardar esto:

```css
.test { color: red; }
<script>alert('hack')</script>
```

Debe guardarse SOLO:

```css
.test { color: red; }
```

El `<script>` debe ser removido automáticamente.
