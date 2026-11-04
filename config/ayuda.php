<?php

return [
    'venta' => [
        'title' => 'Registrar una venta en tienda',
        'question' => '¿Cómo registro una venta?',
        'keywords' => 'ventas carrito terminal POS pago comprobante',
        'route' => 'ventas.create',
        'steps' => [
            'Abre Registrar Venta y busca el producto o la variante en Catálogo de Productos.',
            'Pulsa Agregar y verifica que la cantidad del carrito no supere el stock de tienda.',
            'Selecciona la modalidad Tienda (Mostrador), indica el método de pago y completa los datos solicitados.',
            'Revisa el total y confirma la venta. Consulta Mis Ventas para encontrar el registro y su comprobante.',
        ],
    ],
    'inventario' => [
        'title' => 'Transferir existencias de bodega a tienda',
        'question' => '¿Cómo paso productos de bodega a tienda?',
        'keywords' => 'stock inventario kardex transferencias unidades existencias',
        'route' => 'compras.movimientos',
        'steps' => [
            'En Manejo de inventario, abre la pestaña Transferir.',
            'Elige una variante con existencias en bodega e indica una cantidad entera positiva que no supere el saldo disponible.',
            'Añade una observación si corresponde, pulsa Transferir y confirma con Sí, transferir.',
            'Comprueba en Kardex que bajó el saldo de bodega y subió el de tienda por la misma cantidad.',
        ],
    ],
    'devolucion' => [
        'title' => 'Procesar una devolución de cliente',
        'question' => '¿Cómo registro una devolución?',
        'keywords' => 'devoluciones reembolso cambio garantía cuarentena',
        'route' => 'ventas.devoluciones',
        'steps' => [
            'Localiza la venta entregada, abre su detalle y pulsa Registrar Devolución si la operación sigue dentro de garantía.',
            'Marca los artículos que recibiste e indica las cantidades y el motivo.',
            'Selecciona la resolución según corresponda, revisa el destino del artículo y adjunta evidencia si aplica.',
            'Confirma la operación y comprueba el registro en Devoluciones de clientes.',
        ],
    ],
];



