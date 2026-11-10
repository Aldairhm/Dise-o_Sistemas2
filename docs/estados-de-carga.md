# Skeletons y spinners

El sistema usa los componentes Blade `skeleton-table`, `skeleton-card`, `skeleton-form`, `skeleton-chart` y `skeleton-modal-detalle`. La animación común `ax-skeleton-shimmer` y el spinner SVG `ax-spinner` se definen en `resources/css/loading.css`, con colores para modo oscuro y respeto a `prefers-reduced-motion`.

Los indicadores existentes `fa-spinner fa-spin` y `fa-circle-notch fa-spin` también reciben un estilo local: no dependen de que termine de cargar la fuente externa de iconos.

Los skeletons representan datos que todavía se están solicitando. Los botones muestran un spinner mientras se guarda, elimina, descarga o procesa una solicitud. Los filtros de datos ya presentes y los cambios de pestaña locales se realizan inmediatamente, sin esperas artificiales.

## Formularios que navegan a otra página

```blade
<form method="POST" action="..." data-loading-form data-loading-label="Guardando...">
    @csrf
    <button type="submit">Guardar</button>
</form>
```

El módulo `resources/js/loading-states.js` se importa desde `app.js` y también se carga en las páginas de autenticación. Solo activa el estado después de una validación nativa satisfactoria, evita envíos repetidos, conserva el valor de los botones con `name` y restaura los controles al regresar mediante el navegador.

Para un filtro que navega, se pueden añadir `data-loading-target="#resultados"` y `data-loading-skeleton="#skeleton"`. El estado original de ambas regiones se restaura al volver atrás.

Si una confirmación usa `form.submit()` directamente, debe llamar primero a `AxLoading.beginForm(form, boton)` y continuar solo cuando devuelva `true`. `form.submit()` no dispara el evento `submit`.

## Solicitudes JavaScript

```js
if (!window.AxLoading.setButton(button, true, 'Guardando...')) return;
try {
    const response = await fetch(url, options);
    if (!response.ok) throw new Error('No se pudo guardar');
    // Procesar la respuesta y actualizar la interfaz.
} finally {
    window.AxLoading.setButton(button, false);
}
```

Las pantallas que ya administran sus botones mediante Alpine o un spinner específico conservan ese mecanismo. No se debe activar también el manejador de formularios nativos sobre una solicitud que se cancela con `preventDefault()`.

En filtros y detalles superpuestos, solo la petición vigente puede cambiar los resultados y retirar el indicador. Al cerrar un modal, se invalida su solicitud pendiente. Los errores muestran una vía para reintentar y conservan los nodos del modal y los datos anteriores.

`<x-loading-button>` admite `:loading="true"` para un estado estático o `x-show-loading="guardando"` dentro de Alpine. Si se combina con `x-bind:disabled`, ambas condiciones se respetan.

## Comprobación en navegador

Desde la raíz del repositorio, con las dependencias PHP instaladas y Playwright/Chromium disponibles:

```sh
node tests/browser/loading-feedback.cjs
```

`PHP_BINARY` permite seleccionar otro ejecutable PHP y `CHROMIUM_PATH` otro Chromium. Las pruebas renderizan el componente real de Blade y ejecutan el JavaScript de producción con respuestas controladas: carga lenta, errores, peticiones fuera de orden, validación, envíos duplicados, navegación atrás, imágenes y movimiento reducido. No realizan pagos ni restauraciones reales.
