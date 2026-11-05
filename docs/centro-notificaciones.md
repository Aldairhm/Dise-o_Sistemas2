# Centro de Notificaciones — rama alarmas (base backup)

## Arquitectura y análisis

Proyecto Laravel 12, PHP 8.2 declarado en composer.json (el lock de respaldos exige PHP 8.3), Blade, Vite, Tailwind y Alpine. Autenticación de sesión, usuarios en `usuario`, roles `vendedor`, `admin` y `super_admin`. No había una bandeja persistente de avisos; los respaldos y pagos ya disponían de correo, que se conserva.

Se revisaron rutas, middleware, modelos, migraciones y puntos de escritura de ventas/despachos, comisiones y ajustes, soporte jerárquico, usuarios/perfil/autenticación, auditorías, respaldos/restauración, compras/devoluciones e inventario. También se revisaron los puntos de gestión de productos, variantes, proveedores y movimientos de bodega. El esquema no define sucursales ni administradores comerciales por vendedor: los avisos de venta corresponden al grupo activo `admin`. En soporte se respeta nivel y asignación. Los CRUD rutinarios de catálogo/proveedores y transferencias no generan avisos. No se inventó un umbral de stock ni una política de aprobación inexistente.

Estados reales de venta: Pendiente, Confirmada, En ruta, Entregada, Cancelada, Devolución y Cambio. Comisiones: Pendiente, Pagada y Cancelada. Soporte: Pendiente, En proceso, Escalada, Resuelta y Cancelada; cambios autorizados de vuelta a En proceso también generan avisos. Auditorías: activa, finalizada, expirada y revocada.

## Matriz implementada

Todos los eventos se entregan mediante base de datos y polling autenticado. El mensaje contiene el identificador y estado cuando corresponde. La prioridad y el destino se definen en backend. Ningún mensaje incluye tokens, contraseñas, justificaciones privadas de auditoría ni el contenido de notas internas.

| Evento / condición confirmada | Destinatarios | Prioridad / mensaje | Destino y acción |
|---|---|---|---|
| Venta creada mediante Eloquent | Vendedor responsable y administradores activos, incluido quien registra la venta | Informativa; identificador, vendedor, monto y estado | Detalle de venta con autorización actual |
| Venta cambia realmente de estado | Vendedor y administradores activos, incluido quien confirma el cambio de estado | Importante en cancelación/devolución; informativa en los demás; estado anterior y nuevo | Detalle de venta |
| Comisión creada | Vendedor titular | Importante; monto y estado real | Historial filtrado por comisión |
| Comisión cambia estado, monto o porcentaje | Vendedor titular | Importante; monto y estado real | Historial filtrado por comisión |
| Comisión cambia efectivamente a Pagada | Vendedor titular | Importante; “Comisión pagada”, monto y estado confirmado | Historial filtrado; no altera reglas de liquidación ni garantía |
| Ticket creado | Titular y personal activo del nivel correspondiente; si hay asignado, se limita el grupo | Importante; alta si prioridad Alta/Urgente; código, estado y prioridad | Conversación autorizada |
| Ticket cambia estado, prioridad, asignado o nivel | Titular y personal actualmente autorizado, excepto actor | Importante/alta; código, estado y prioridad | Conversación; leer no resuelve |
| Respuesta pública | Titular y personal autorizado, excepto autor/actor | Importante; aviso de nueva respuesta sin copiar texto privado | Conversación |
| Nota interna | Solo personal autorizado; nunca vendedor | Importante; aviso sin contenido de la nota | Conversación con validación de rol |
| Auditoría iniciada | Cuenta auditada, incluso si coincide con el contexto autenticado | Importante; aviso de revisión autorizada | Perfil; no se expone la investigación |
| Auditoría finalizada, expirada o revocada | Cuenta auditada | Importante; estado de la revisión | Perfil |
| Auditoría expirada o revocada | Superadministrador responsable, excepto quien realiza la acción | Alta; identificador y estado | Historial de auditorías |
| Reautenticación fallida al iniciar auditoría | Superadministradores activos | Alta; aviso de rechazo, agrupado por actor/hora | Usuarios y bitácora autorizados |
| Cambio de contraseña, username, rol, estado, comisión porcentual o MFA | Cuenta afectada | Alta; aviso genérico de modificación de acceso/configuración | Perfil |
| Cambio de rol, estado o MFA | Superadministradores activos, excepto actor | Alta; identificador de cuenta | Gestión de usuarios |
| Intentos de login alcanzan 5, 10 o 15 en el contador existente | Superadministradores activos, solo si la cuenta existe | Alta; umbral alcanzado, sin afirmar ataque; agrupación cuenta/hora/umbral | Gestión de usuarios y bitácora |
| Devolución a proveedor creada o cambia estado | Administradores activos, excepto actor | Importante; identificador y estado | Devoluciones a proveedores |
| Respaldo falla mediante evento Spatie o ejecución manual | Superadministradores activos | Crítica; fallo confirmado sin mensajes técnicos sensibles; un grupo por hora | Respaldos y registros autorizados |
| Restauración de base de datos confirmada | Superadministradores activos | Crítica; restauración confirmada | Respaldos y bitácora |

No se envían todas las ventas o comisiones al SUPER_ADMIN. La creación de un ticket confirma recepción al titular. Las ventas nuevas y sus transiciones confirmadas también notifican al actor autorizado para permitir seguimiento operativo; en otros módulos se suprimen acciones propias rutinarias. Los avisos de comisiones y seguridad se conservan aunque el titular sea el actor por su función financiera o de seguridad.

## Datos y consistencia

Migración aditiva `2026_11_04_150000_create_notification_center.php`:

- `notification_events`: clave única, tipo, categoría, prioridad, título, mensaje, tipo/ID del recurso, contexto mínimo y fecha.
- `notification_deliveries`: evento, usuario destinatario, fecha de lectura y archivado; clave única evento/usuario e índice de bandeja.
- `notification_event_key` nullable en usuario, venta, comision_vendedor, soporte_tickets, soporte_ticket_mensajes, auditoria_sesiones y devoluciones. Los datos anteriores no se modifican.

`NotifiesBusinessChanges::save` mantiene cada escritura observada y sus notificaciones en una transacción. Si existe una transacción de negocio externa, ambas se revierten juntas. El observador es síncrono: no hay ventana entre confirmar el cambio y guardar la notificación ni hace falta un worker/outbox separado. Se persiste una UUID por escritura y se deriva una clave por tipo de evento; la clave única y la entrega única evitan duplicados al repetir el procesamiento del mismo evento. Guardar el mismo estado sin cambios no genera avisos. Dos transiciones legítimas distintas pueden volver al mismo estado y generar dos eventos.

Es obligatorio usar escrituras Eloquent sobre los modelos integrados para generar avisos. Los `update` masivos del query builder, SQL directo y escrituras externas no ejecutan observadores. Los cambios masivos de `Salida` existentes no se usan como fuente: el evento se toma de Venta/ComisionVendedor. Los reintentos de una operación HTTP que ya hubiera creado **otro registro de negocio** no se convierten en una sola venta: la idempotencia del alta de negocio es distinta de la deduplicación de sus notificaciones.

Conservación: historial indefinido por defecto, archivado individual sin eliminación automática. No se borran datos de negocio ni se retroalimentan notificaciones históricas. Debe acordarse una política temporal antes de habilitar purga automática. Las claves de evento se conservan mientras exista el historial.

## Servicios y endpoints

- `NotificationService`: publicación idempotente, selección de roles, autorización y destinatarios de soporte, agrupación de fallos de respaldo.
- `NotificationObserver`: integra los siete modelos de origen.
- `NotificationController`: acceso exclusivamente a entregas del usuario autenticado.
- `GET /notificaciones`: filtros de lectura/categoría/prioridad, búsqueda limitada a 120 caracteres, orden, paginación de 20.
- `GET /notificaciones/feed`: contador exacto y ocho avisos recientes no archivados; no descarga todo el historial.
- `POST /notificaciones/leer-todas`: lectura de la bandeja propia.
- `POST /notificaciones/{id}/leer`: lectura individual idempotente.
- `POST /notificaciones/{id}/archivar`: archivado propio.
- `GET /notificaciones/{id}/abrir`: resuelve un destino permitido y revalida el recurso. Este GET no cambia la lectura; la campana realiza antes el POST protegido.
- `php artisan notifications:expire-audits`: procesa auditorías vencidas con bloqueo de fila, genera avisos y evita duplicación; programado cada minuto.

## Actualización automática y seguridad

Polling cada 30 segundos por pestaña visible, con contador desde el servidor, pausa cuando la pestaña se oculta, reintentos con espera creciente hasta cinco minutos y recuperación al volver a primer plano/conexión. BroadcastChannel y evento storage sincronizan lecturas entre pestañas; otros dispositivos se sincronizan con el próximo polling. El servidor es la fuente de verdad y conserva avisos durante desconexiones.

Feed y operaciones requieren `auth`, cuenta activa, CSRF en mutaciones y límite de 120 peticiones/minuto. Una sesión de auditoría no puede consultar ni modificar ninguna bandeja privada; la campana se oculta. Cuando el login expira, cambia o es revocado, el cliente se detiene ante redirecciones/401/403/419 y elimina la vista previa. No existe un canal global ni una conexión persistente que pueda seguir entregando datos sin reautenticación.

Cada ID se consulta dentro de `user_id = usuario autenticado`; IDs ajenos dan 404. Eventos exclusivos del SUPER_ADMIN y notas internas se filtran también con el rol actual. Los enlaces se construyen desde una lista cerrada de tipos y rutas, sin aceptar URLs del cliente. Se comprueban permisos y existencia al abrir; si ya no están disponibles, se regresa al centro con un aviso. No se conceden permisos por recibir una notificación. Las vistas Blade escapan texto y JavaScript utiliza textContent.

En AyudaController se refuerza el control jerárquico antes de responder, escalar o cambiar estado, además de consultar. Se conserva la política existente de acceso global de administradores a tickets del nivel admin. Un ticket asignado reduce destinatarios, sin inventar nuevas restricciones de negocio para el módulo.

## Instalación en el servidor del proyecto

1. Usar PHP 8.3 o superior compatible con composer.lock. Instalar las dependencias bloqueadas con `composer install`.
2. Con el entorno de base de datos del proyecto configurado y un respaldo disponible, ejecutar `php artisan migrate`. No se ha ejecutado contra una base de producción.
3. Ejecutar `npm ci` y `npm run build`; publicar también `public/js/notification-center.js`.
4. Mantener activo el scheduler habitual de Laravel (`php artisan schedule:run` cada minuto). La expiración sigue comprobándose por el middleware existente cuando se usa una sesión; el scheduler permite avisar incluso sin actividad.
5. Verificar la migración y los flujos reales en staging PostgreSQL. No hace falta contratar servicios ni configurar WebSockets/colas para este centro.

## Validación y límites

- Suite `NotificationCenterTest`: **21 pruebas, 124 comprobaciones, correctas**. Incluye destinatarios, cambios reales/no-op, pago mediante ComisionService, ausencia de anuncio prematuro de pago, rollback, notas internas, escalamiento/resolución, IDOR, lectura/archivado, filtros/paginación, idempotencia, auditoría privada, expiración programada, sesión inactiva, recursos eliminados/permisos retirados, agrupación de respaldos y umbrales reales de login.
- La suite usa SQLite **en memoria y aislado**. Reutiliza migraciones de usuarios, ventas, bitácora y soporte; emplea fixtures mínimos en las tablas heredadas con ALTER CHECK de PostgreSQL no compatible con SQLite. Sí ejecuta la migración nueva completa. No accede a la base `axstore2026` de la configuración de pruebas antigua.
- `npm run build`: correcto. `node --check public/js/notification-center.js`: correcto. Rutas nuevas registradas correctamente.
- Navegador integrado sobre base local descartable: centro y campana renderizados, contador 4→3 al leer y 3→0 al marcar todas, persistencia al recargar, cierre Escape y desplegable móvil de 390 px dentro de pantalla. Se creó una comisión mediante el modelo real en la base aislada y se verificó la llegada automática del aviso (contador 0→1) sin recargar la página. Las capturas contienen datos de prueba; no se agregan seeds demostrativos al producto.
- PHP local 8.2.12: instalación solo para validación con el control de versión ignorado. En este sandbox Windows, is_readable/is_writable devuelve falso aunque la E/S funciona; se adaptaron comprobaciones únicamente en dependencias locales excluidas de Git para ejecutar PHPUnit. Ninguna adaptación de vendor ni .env de pruebas forma parte de los entregables. La validación definitiva debe repetirse con PHP 8.3 en el entorno de destino.
- Pint detectó formato pendiente, incluyendo convenciones previas en archivos existentes; no pudo escribir desde el sandbox por la comprobación is_writable. La revisión de sintaxis se hace separadamente.
- La suite heredada completa no se ejecutó: apunta a PostgreSQL local con datos persistentes y no está aislada. No se declara probada una regresión completa de todos los módulos.
- Pendiente: ensayo de migración sobre PostgreSQL de staging, carga/concurrencia real de múltiples dispositivos, y ejecución completa del sistema con sus datos/esquema reales. Los flujos de compra, restauración y fallos de infraestructura no se han ejecutado contra infraestructura real; sus puntos de integración se revisaron en código.
- No se implementan módulos/estados inexistentes, notificación diferida de auditorías ni preferencias opcionales de avisos informativos. Los avisos de seguridad no se pueden desactivar. La implementación se entrega en la rama alarmas, creada desde backup. No se ha desplegado en producción.

## Archivos

Nuevos: migración del centro; modelos NotificationEvent/NotificationDelivery; trait NotifiesBusinessChanges; NotificationService; NotificationObserver; NotificationController; vistas notifications/index y bell; public/js/notification-center.js; NotificationCenterTest; este documento.

Modificados: modelos User, Venta, ComisionVendedor, SoporteTicket, SoporteTicketMensaje, AuditoriaSesion y Devolucion; AppServiceProvider; AuthController, AyudaController, BackupController, ComisionVendedorController y SuperAdminController; rutas web/console; cabeceras compartidas header y header-vendedor.

## Corrección del seguimiento de ventas

Se eliminó la supresión del actor para los avisos de ventas. Una venta en tienda genera su aviso de venta Entregada, además del aviso independiente de comisión cuando el proceso de negocio la genera. Una venta de envío notifica desde Pendiente, aunque todavía no tenga comisión. Los cambios reales a Confirmada, En ruta, Entregada, Cancelada, Devolución o Cambio notifican al titular y a los administradores activos, incluido quien confirma la operación. La selección de destinatarios no copia ventas rutinarias al SUPER_ADMIN ni a vendedores ajenos. Se mantienen la deduplicación, transacciones y permisos. No se recrean avisos de ventas anteriores: la corrección aplica a las operaciones que se registren después de actualizar el código.
