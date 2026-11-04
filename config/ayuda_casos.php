<?php

return [
    'venta' => [
        'Registrar una venta por envío' => [
            'steps' => [
                'Agrega los productos al carrito y selecciona Envío / Entrega a Domicilio.',
                'Completa nombre, teléfono, departamento, municipio y dirección. Añade la referencia y el costo de envío cuando correspondan.',
                'Revisa el total y el método de pago. Si eliges transferencia bancaria, adjunta el comprobante.',
                'Confirma la venta una sola vez.',
            ],
            'check' => 'Busca el pedido en Mis Ventas. Empieza como Pendiente y el stock de tienda se descuenta al registrarlo.',
        ],
        'Dar seguimiento a un envío' => [
            'roles' => ['admin'],
            'steps' => [
                'Abre Control de Envíos y Estados y localiza el pedido.',
                'Confirma el pedido antes de despacharlo. Actualiza su estado a En ruta cuando salga.',
                'Registra la entrega cuando el cliente lo reciba.',
            ],
            'check' => 'Verifica que el historial muestre el estado Entregada.',
        ],
    ],
    'inventario' => [
        'Consultar un movimiento en Kardex' => [
            'steps' => ['Abre la pestaña Kardex en Manejo de inventario.', 'Filtra por tipo de movimiento si necesitas acotar la lista.', 'Revisa la fecha, la variante y los saldos anteriores y nuevos.'],
            'check' => 'Identifica el movimiento y confirma cuántas unidades cambiaron de ubicación.',
        ],
        'La cantidad supera el saldo de bodega' => [
            'steps' => ['Revisa el saldo de bodega de la variante seleccionada.', 'Reduce la cantidad a un número entero positivo dentro del saldo disponible. Si el saldo es cero, verifica primero la recepción en Compras.'],
            'check' => 'Confirma la transferencia solo si la cantidad coincide con lo que moverás a tienda.',
        ],
    ],
    'devolucion' => [
        'Reembolsar y devolver el producto a tienda' => [
            'steps' => ['En el detalle de la venta, pulsa Registrar Devolución y selecciona los artículos y cantidades.', 'Elige el reembolso con retorno a tienda si el producto puede volver a venderse. Indica el motivo y confirma.'],
            'check' => 'Comprueba el reembolso en el historial y el ingreso de las unidades al stock de tienda.',
        ],
        'Reembolsar y enviar el producto a cuarentena' => [
            'steps' => ['En Registrar Devolución, selecciona los artículos y cantidades.', 'Elige el reembolso con destino a cuarentena, indica el motivo y confirma.'],
            'check' => 'Revisa el reembolso y confirma que el artículo quedó separado del inventario vendible.',
        ],
        'Hacer un cambio presencial' => [
            'steps' => ['En Registrar Devolución, elige cambio e indica el destino del artículo recibido.', 'Verifica la disponibilidad del reemplazo. Si se solicita modalidad, selecciona tienda y confirma.'],
            'check' => 'Revisa el registro del cambio y los movimientos de entrada y salida que correspondan.',
        ],
        'Reenviar un producto de reemplazo' => [
            'steps' => ['En una venta por envío, registra un cambio y selecciona la modalidad envío.', 'Revisa los datos de destino y el costo de envío. Confirma y abre Control de Envíos para continuar el despacho.'],
            'check' => 'Verifica que el reemplazo quedó pendiente de despacho. Registra la entrega cuando el cliente lo reciba.',
        ],
    ],
    'variantes' => [
        'Crear una variante' => [
            'steps' => ['En la edición del producto, pulsa Agregar variante.', 'Escribe un nombre distinto dentro del producto, completa atributos y ganancia, y adjunta de una a diez imágenes.', 'Elige la imagen principal y pulsa Guardar variante.'],
            'check' => 'La nueva presentación aparece en el listado con existencias iniciales en cero.',
        ],
        'Editar una variante' => [
            'steps' => ['Localiza la presentación y pulsa Editar.', 'Actualiza los datos, atributos o ganancia y guarda los cambios.'],
            'check' => 'Reabre la variante y verifica los datos y el precio. Las cantidades se gestionan desde compras e inventario.',
        ],
        'Gestionar sus imágenes' => [
            'steps' => ['Abre Editar en la variante.', 'Agrega las imágenes nuevas o marca las que quieras retirar. Elige la imagen principal y guarda.'],
            'check' => 'Vuelve a abrir la variante y revisa la galería y la imagen principal.',
        ],
        'Cambiar el estado de una variante' => [
            'steps' => ['Localiza la variante en la tabla o en las tarjetas.', 'Pulsa su control Activo o Inactivo y confirma si aparece el aviso.'],
            'check' => 'Comprueba el estado final. Las cantidades de tienda y bodega se mantienen.',
        ],
    ],
    'comisiones' => [
        'Consultar mis comisiones' => [
            'roles' => ['vendedor'],
            'steps' => ['Abre Mis Comisiones desde el menú del vendedor.', 'Filtra por fechas o estado y abre el detalle del registro que quieras revisar.'],
            'check' => 'Verifica la venta asociada y distingue las comisiones pendientes, pagadas o canceladas.',
        ],
        'Revisar comisiones por vendedor o semana' => [
            'roles' => ['admin'],
            'steps' => ['Abre Comisiones por Vendedor o Comisiones por Semana.', 'Selecciona el periodo y el vendedor que deseas revisar. Aplica los filtros y abre el desglose.'],
            'check' => 'Distingue el saldo liquidable del importe que sigue en garantía y de los pagos ya registrados.',
        ],
        'Registrar un bono' => [
            'roles' => ['admin'],
            'steps' => ['En Comisiones, pulsa Nuevo bono y elige al vendedor.', 'Escribe el concepto y el monto. Añade una nota si corresponde y pulsa Registrar bono.'],
            'check' => 'Busca el bono pendiente y confirma que está asociado al vendedor correcto.',
        ],
        'Liquidar un pago' => [
            'roles' => ['admin'],
            'steps' => ['Pulsa Liquidar pago, selecciona al vendedor y revisa el desglose y el saldo liquidable.', 'Elige el método de pago y completa la referencia y el comprobante que solicite el sistema.', 'Pulsa Revisar y confirmar. Comprueba los datos antes de pulsar Confirmar pago.'],
            'check' => 'Verifica el estado Pagada y la evidencia del pago. Las comisiones de ventas sin entregar o en garantía siguen fuera del saldo liquidable.',
        ],
    ],
    'usuarios' => [
        'Desactivar o reactivar un usuario' => [
            'roles' => ['admin'],
            'steps' => [
                'Ubica al colaborador en la tabla de usuarios.',
                'Haz clic en el interruptor de estado (Activo / Inactivo).',
                'Confirma la acción cuando el sistema solicite validación.',
            ],
            'check' => 'Los usuarios inactivos perderán el acceso a la plataforma de inmediato sin perder su historial de ventas ni comisiones previas.',
        ],
        'Modificar rol o porcentaje de comisión' => [
            'roles' => ['admin'],
            'steps' => [
                'Pulsa Editar en la fila del colaborador.',
                'Ajusta el rol asignado o el porcentaje de comisión personalizado.',
                'Guarda los cambios.',
            ],
            'check' => 'Las nuevas ventas procesadas por el vendedor tomarán el porcentaje de comisión actualizado.',
        ],
    ],
    'bitacora' => [
        'Comparar cambios con el visor Diff' => [
            'roles' => ['admin'],
            'steps' => [
                'Identifica un evento con la etiqueta verde Diff (por ejemplo, CAMBIO_ESTADO o EDITAR).',
                'Haz clic sobre el botón Diff.',
                'Revisa el panel comparativo: a la izquierda verás los valores anteriores y a la derecha los nuevos valores guardados.',
            ],
            'check' => 'El modal te mostrará con exactitud los campos que sufrieron alteración en formato legible.',
        ],
        'Filtrar eventos por fecha o módulo' => [
            'roles' => ['admin'],
            'steps' => [
                'Usa los desplegables del encabezado para elegir el módulo (ej. Ventas, Usuarios, Compras).',
                'Opcionalmente ingresa un rango de fechas desde / hasta.',
                'Pulsa Filtrar para actualizar la vista de la bitácora.',
            ],
            'check' => 'La tabla mostrará exclusivamente las actividades ocurridas en el periodo y contexto seleccionado.',
        ],
    ],
    'backups' => [
        'Descargar una copia de seguridad' => [
            'roles' => ['admin'],
            'steps' => [
                'Localiza la copia que deseas respaldar en el historial de copias.',
                'Haz clic en el botón de descarga con el icono de flecha.',
                'Guarda el archivo en una unidad externa, disco secundario o nube segura.',
            ],
            'check' => 'Verifica que el archivo descargado se guarde en tu equipo y contenga el sufijo con la fecha y hora de creación.',
        ],
    ],
    'atributos' => [
        'Editar o renombrar un atributo' => [
            'roles' => ['admin'],
            'steps' => [
                'En el listado de Atributos, ubica el atributo deseado.',
                'Haz clic en Editar, modifica el nombre de la propiedad y confirma.',
            ],
            'check' => 'El nuevo nombre se reflejará automáticamente en las fichas técnicas y selector de variantes.',
        ],
        'Eliminar un atributo sin uso' => [
            'roles' => ['admin'],
            'steps' => [
                'Verifica que el atributo no esté siendo utilizado en variantes activas con stock.',
                'Haz clic en Eliminar y confirma el mensaje de advertencia.',
            ],
            'check' => 'El atributo se retirará del listado y ya no aparecerá como opción para nuevos productos.',
        ],
    ],
];


