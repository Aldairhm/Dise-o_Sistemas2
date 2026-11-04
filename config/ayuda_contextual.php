<?php

return [
    'ventas.create' => [
        'admin' => 'El carrito usa el stock de tienda. Si solo hay unidades en bodega, transfiérelas desde Manejo de inventario.',
        'vendedor' => 'El carrito usa el stock de tienda. Si el producto está agotado, pide al administrador que revise la bodega.',
        'guide' => 'venta',
    ],
    'ventas.mis-ventas' => 'Abre el detalle de una venta para revisar sus productos, estado y comprobante.',
    'ventas.index' => 'Consulta el historial de ventas y abre el detalle de la operación que quieres revisar.',
    'ventas.show' => 'Revisa el estado y los artículos antes de realizar una acción sobre esta venta.',
    'ventas.pedidos' => 'Confirma el pedido antes de despacharlo y registra la entrega cuando el cliente lo reciba.',
    'ventas.devoluciones' => [
        'admin' => 'Para registrar una devolución, abre el detalle de una venta entregada y verifica que siga en garantía.',
        'guide' => 'devolucion',
    ],
    'compras.movimientos' => [
        'admin' => 'En Transferir, elige la variante y la cantidad. En Kardex puedes comprobar el movimiento.',
        'guide' => 'inventario',
    ],
    'compras.create' => 'Selecciona el proveedor y revisa las variantes y cantidades recibidas antes de confirmar la compra.',
    'compras.historial' => 'Busca la compra en el historial para revisar lo que se recibió y su proveedor.',
    'compras.devoluciones' => 'Ubica la compra original y revisa las unidades disponibles para devolver al proveedor.',
    'productos.index' => 'Busca el producto por nombre y abre sus variantes para revisar el precio y las existencias.',
    'productos.create' => 'Completa la información del producto y crea sus variantes para distinguir las presentaciones que vendes.',
    'productos.edit' => 'Revisa las variantes del producto cuando necesites cambiar una presentación específica.',
    'categorias.index' => 'Usa las categorías para agrupar productos y facilitar su búsqueda en el catálogo.',
    'proveedores.index' => 'Busca un proveedor para consultar sus datos, sus catálogos o su historial de compras.',
    'proveedores.show' => 'Consulta aquí los datos y recursos del proveedor antes de registrar una compra.',
    'usuarios.index' => 'Revisa el rol y el estado de la cuenta antes de cambiar sus permisos o desactivarla.',
    'perfil.show' => 'Desde tu perfil puedes actualizar tus datos y cambiar la contraseña.',
    'vendedor.dashboard' => 'Elige el periodo que quieres consultar para revisar tus ventas y comisiones.',
    'comisiones.index' => 'Filtra por vendedor y periodo antes de revisar las comisiones.',
    'comisiones.ajustes' => 'Revisa el motivo y el importe del ajuste antes de confirmar.',
    'admin.reportes.movimientos' => 'Aplica los filtros antes de exportar para obtener solo los movimientos que necesitas.',
    'bitacora.index' => 'Filtra por fecha o usuario para localizar una acción y revisar su detalle.',
    'backups.index' => 'Comprueba la fecha del respaldo antes de descargarlo.',
];
