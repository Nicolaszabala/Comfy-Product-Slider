#!/bin/bash
#
# Script de auditoría para detectar potenciales conflictos con otros plugins/temas
# Ejecutar desde la raíz del plugin: bash bin/check-conflicts.sh
#

set -e

ERRORS=0
WARNINGS=0

echo "========================================="
echo "  AUDITORÍA DE PREVENCIÓN DE CONFLICTOS"
echo "========================================="
echo ""

# Color codes
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

# 1. Verificar funciones globales sin prefijo
echo "1. Verificando funciones globales..."
GLOBAL_FUNCS=$(grep -rn "^function " includes/ woocommerce-product-slider.php 2>/dev/null | grep -v "function wc_product_slider_" || true)
if [ -n "$GLOBAL_FUNCS" ]; then
    echo -e "${RED}✗ ERRORES: Funciones globales sin prefijo wc_product_slider_${NC}"
    echo "$GLOBAL_FUNCS"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✓ OK: Todas las funciones globales tienen prefijo correcto${NC}"
fi
echo ""

# 2. Verificar constantes sin prefijo
echo "2. Verificando constantes globales..."
BAD_CONSTANTS=$(grep -rn "define(" includes/ woocommerce-product-slider.php 2>/dev/null | grep -v "WC_PRODUCT_SLIDER_" | grep -v "WPINC" || true)
if [ -n "$BAD_CONSTANTS" ]; then
    echo -e "${RED}✗ ERRORES: Constantes sin prefijo WC_PRODUCT_SLIDER_${NC}"
    echo "$BAD_CONSTANTS"
    ERRORS=$((ERRORS + 1))
else
    echo -e "${GREEN}✓ OK: Todas las constantes tienen prefijo correcto${NC}"
fi
echo ""

# 3. Verificar clases sin namespace
echo "3. Verificando namespaces en clases..."
FILES_WITHOUT_NS=$(find includes/ -name "*.php" -type f -exec grep -L "^namespace WC_Product_Slider" {} \; 2>/dev/null || true)
if [ -n "$FILES_WITHOUT_NS" ]; then
    echo -e "${YELLOW}⚠ ADVERTENCIA: Archivos PHP sin namespace:${NC}"
    echo "$FILES_WITHOUT_NS"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✓ OK: Todos los archivos usan namespace${NC}"
fi
echo ""

# 4. Verificar hooks personalizados
echo "4. Verificando hooks personalizados..."
CUSTOM_HOOKS=$(grep -rn "apply_filters\|do_action" includes/ 2>/dev/null | grep -v "^\s*//" | grep -v "add_action\|add_filter" || true)
if [ -n "$CUSTOM_HOOKS" ]; then
    # Verificar que tengan prefijo (excluyendo WordPress core y WooCommerce hooks)
    BAD_HOOKS=$(echo "$CUSTOM_HOOKS" | grep -v "wc_product_slider_" | grep -v "woocommerce_" | grep -v "wp_" | grep -v "phpcs:disable\|phpcs:ignore" || true)
    if [ -n "$BAD_HOOKS" ]; then
        echo -e "${RED}✗ ERRORES: Hooks personalizados sin prefijo wc_product_slider_${NC}"
        echo "$BAD_HOOKS"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}✓ OK: Todos los hooks tienen prefijo correcto (o son WordPress/WooCommerce core)${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay hooks personalizados todavía (se verificarán en futuras fases)${NC}"
fi
echo ""

# 5. Verificar enqueue de scripts/styles
echo "5. Verificando wp_enqueue handles..."
ENQUEUES=$(grep -rn "wp_enqueue_script\|wp_enqueue_style" includes/ 2>/dev/null | grep -v "^\s*//" | grep -v "Example:" | grep -v "add_action\|add_filter" || true)
if [ -n "$ENQUEUES" ]; then
    BAD_ENQUEUES=$(echo "$ENQUEUES" | grep -v "wc-product-slider-" | grep -v "'swiper'" || true)
    if [ -n "$BAD_ENQUEUES" ]; then
        echo -e "${RED}✗ ERRORES: Handles de enqueue sin prefijo wc-product-slider-${NC}"
        echo "$BAD_ENQUEUES"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}✓ OK: Todos los handles tienen prefijo correcto (o son third-party libraries)${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay enqueues todavía (se verificarán en futuras fases)${NC}"
fi
echo ""

# 6. Verificar shortcodes
echo "6. Verificando shortcodes..."
SHORTCODES=$(grep -rn "add_shortcode" includes/ 2>/dev/null || true)
if [ -n "$SHORTCODES" ]; then
    BAD_SHORTCODES=$(echo "$SHORTCODES" | grep -v "wc_product_slider\|wc_ps_" || true)
    if [ -n "$BAD_SHORTCODES" ]; then
        echo -e "${RED}✗ ERRORES: Shortcodes sin prefijo apropiado${NC}"
        echo "$BAD_SHORTCODES"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}✓ OK: Todos los shortcodes tienen prefijo correcto${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay shortcodes todavía (se verificarán en futuras fases)${NC}"
fi
echo ""

# 7. Verificar opciones de DB
echo "7. Verificando opciones de base de datos..."
DB_OPTIONS=$(grep -rn "add_option\|get_option\|update_option\|delete_option" includes/ 2>/dev/null | grep -v "^\s*//" || true)
if [ -n "$DB_OPTIONS" ]; then
    # Excluir opciones de WordPress core
    BAD_OPTIONS=$(echo "$DB_OPTIONS" | grep -v "wc_product_slider_" | grep -v "active_plugins" | grep -v "thumbnail_size" | grep -v "thumbnail_crop" | grep -v "medium_size" | grep -v "large_size" || true)
    if [ -n "$BAD_OPTIONS" ]; then
        echo -e "${YELLOW}⚠ ADVERTENCIA: Opciones de DB potencialmente sin prefijo${NC}"
        echo "$BAD_OPTIONS"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "${GREEN}✓ OK: Todas las opciones tienen prefijo correcto (o son WordPress core)${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay opciones de DB todavía${NC}"
fi
echo ""

# 8. Verificar AJAX actions
echo "8. Verificando AJAX actions..."
AJAX_ACTIONS=$(grep -rn "wp_ajax_" includes/ 2>/dev/null || true)
if [ -n "$AJAX_ACTIONS" ]; then
    BAD_AJAX=$(echo "$AJAX_ACTIONS" | grep "add_action.*wp_ajax" | grep -v "wc_product_slider_" || true)
    if [ -n "$BAD_AJAX" ]; then
        echo -e "${RED}✗ ERRORES: AJAX actions sin prefijo wc_product_slider_${NC}"
        echo "$BAD_AJAX"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}✓ OK: Todos los AJAX actions tienen prefijo correcto${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay AJAX actions todavía${NC}"
fi
echo ""

# 9. Verificar post meta keys
echo "9. Verificando post meta keys..."
POST_META=$(grep -rn "add_post_meta\|get_post_meta\|update_post_meta\|delete_post_meta" includes/ 2>/dev/null || true)
if [ -n "$POST_META" ]; then
    # Excluir meta keys de WordPress core
    BAD_META=$(echo "$POST_META" | grep -v "_wc_ps_\|wc_product_slider_" | grep -v "_wp_attachment_image_alt" | grep -v "_thumbnail_id" || true)
    if [ -n "$BAD_META" ]; then
        echo -e "${YELLOW}⚠ ADVERTENCIA: Post meta keys potencialmente sin prefijo${NC}"
        echo "$BAD_META"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "${GREEN}✓ OK: Todos los meta keys tienen prefijo correcto (o son WordPress core)${NC}"
    fi
else
    echo -e "${GREEN}✓ OK: No hay post meta todavía${NC}"
fi
echo ""

# 10. Verificar variables globales JS (en archivos PHP que contienen JS inline)
echo "10. Verificando variables JavaScript globales..."
JS_VARS=$(grep -rn "var [a-z]" includes/ 2>/dev/null | grep -v "^\s*//" || true)
if [ -n "$JS_VARS" ]; then
    echo -e "${YELLOW}⚠ ADVERTENCIA: Posibles variables JS globales detectadas${NC}"
    echo "   Verificar que estén dentro de IIFE o namespace WCProductSlider"
    echo "$JS_VARS"
    WARNINGS=$((WARNINGS + 1))
else
    echo -e "${GREEN}✓ OK: No hay variables JS globales detectadas${NC}"
fi
echo ""

# Resumen final
echo "========================================="
echo "  RESUMEN"
echo "========================================="
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ PERFECTO: No se encontraron problemas${NC}"
    echo ""
    echo "El plugin sigue todas las convenciones de prevención de conflictos."
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠ ${WARNINGS} ADVERTENCIA(S) encontrada(s)${NC}"
    echo ""
    echo "No hay errores críticos, pero revisa las advertencias arriba."
    exit 0
else
    echo -e "${RED}✗ ${ERRORS} ERROR(ES) CRÍTICO(S) encontrado(s)${NC}"
    echo -e "${YELLOW}⚠ ${WARNINGS} ADVERTENCIA(S) encontrada(s)${NC}"
    echo ""
    echo "¡ACCIÓN REQUERIDA! Corrige los errores antes de continuar."
    exit 1
fi
