<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $tipo === 'ticket' ? 'Ticket de Venta #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) : ($tipo === 'credito_fiscal' ? 'Crédito Fiscal CCF #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) : 'Factura Consumidor Final #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT)) }} | AXStore
    </title>
    <!-- Tailwind CSS CDN para renderizado confiable de impresión -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .font-mono-receipt {
            font-family: 'JetBrains Mono', monospace;
        }

        /* ESTILOS DE IMPRESIÓN */
        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 auto !important;
                max-width: 100% !important;
            }

            @page {
                margin: 8mm;
            }

            @page ticket-page {
                size: 80mm auto;
                margin: 3mm;
            }

            .is-ticket {
                page: ticket-page;
                width: 76mm !important;
                max-width: 76mm !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="py-6 px-4">

    <!-- BARRA SUPERIOR DE ACCIONES (OCULTA AL IMPRIMIR) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-600/20">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-black text-slate-800">
                        {{ $tipo === 'ticket' ? 'Ticket de Caja / POS' : ($tipo === 'credito_fiscal' ? 'Comprobante de Crédito Fiscal (CCF)' : 'Factura de Consumidor Final') }}
                    </h2>
                    <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $tipo === 'credito_fiscal' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        {{ $tipo === 'credito_fiscal' ? 'Ley Tributaria SV (CCF)' : ($tipo === 'factura_comercial' ? 'Comercial SV' : 'Punto de Venta') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">República de El Salvador • Venta #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition-all shadow-md shadow-blue-600/20 cursor-pointer"
            >
                <i class="fas fa-print"></i>
                <span>Imprimir Documento</span>
            </button>
            <button 
                onclick="window.close()" 
                class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold px-3.5 py-2.5 rounded-xl text-xs border border-slate-200 transition-colors shadow-xs cursor-pointer"
            >
                <i class="fas fa-times"></i>
                <span>Cerrar</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CASO 1: TICKET DE CAJA / POS (80mm TÉRMICO)                               -->
    <!-- ========================================================================= -->
    @if($tipo === 'ticket')
    <div class="print-container is-ticket w-[340px] max-w-full mx-auto bg-white p-5 rounded-2xl shadow-sm border border-slate-200 font-mono-receipt text-xs text-slate-800 leading-tight">
        
        <!-- ENCABEZADO TICKET -->
        <div class="text-center pb-3 border-b border-dashed border-slate-300">
            <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-12 w-auto mx-auto mb-2 object-contain">
            <h1 class="text-base font-black tracking-wider text-slate-900">AXSTORE</h1>
            <p class="text-[11px] font-bold text-slate-600">AXSTORE S.A. DE C.V.</p>
            <p class="text-[10px] text-slate-500">NIT: 0614-150995-102-1 • NRC: 284910-3</p>
            <p class="text-[10px] text-slate-500">Giro: Venta de Accesorios y Mercadería</p>
            <p class="text-[10px] text-slate-500">San Salvador, El Salvador, C.A.</p>
            <p class="text-[10px] text-slate-500">Tel: (503) 2225-8800</p>
        </div>

        <!-- DATOS DE LA TRANSACCIÓN -->
        <div class="py-2.5 border-b border-dashed border-slate-300 text-[11px] space-y-1">
            <div class="flex justify-between">
                <span class="font-bold">TICKET N°:</span>
                <span class="font-black text-slate-900">#VNT-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Fecha:</span>
                <span>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Hora:</span>
                <span>{{ \Carbon\Carbon::parse($venta->fecha)->format('h:i:s A') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Cajero/Vendedor:</span>
                <span class="truncate max-w-[170px]">{{ $venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'Josue' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pago:</span>
                <span class="font-semibold">{{ $venta->metodo_pago }}</span>
            </div>
            <div class="flex justify-between">
                <span>Cliente:</span>
                <span>{{ $cliente['nombre'] }}</span>
            </div>
        </div>

        <!-- DETALLE DE ARTÍCULOS -->
        <div class="py-2.5 border-b border-dashed border-slate-300">
            <div class="flex justify-between text-[10px] font-bold uppercase text-slate-500 pb-1 mb-1 border-b border-slate-100">
                <span>Cant / Producto</span>
                <span>Total</span>
            </div>

            <div class="space-y-2">
                @foreach($calculos['lineas'] as $item)
                <div>
                    <div class="flex justify-between font-bold text-slate-900">
                        <span class="truncate pr-2">{{ $item['cantidad'] }}x {{ $item['producto'] }}</span>
                        <span>${{ number_format($item['subtotal'], 2) }}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 flex justify-between pl-3">
                        <span>{{ $item['variante'] }} (P.U. ${{ number_format($item['precio_unitario'], 2) }})</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- TOTALES TICKET -->
        <div class="py-2.5 border-b border-dashed border-slate-300 space-y-1 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal:</span>
                <span>${{ number_format($calculos['subtotal_general'], 2) }}</span>
            </div>
            @if(!empty($calculos['precio_envio']) && $calculos['precio_envio'] > 0)
            <div class="flex justify-between text-slate-700 font-bold">
                <span>Costo de Envío:</span>
                <span>+${{ number_format($calculos['precio_envio'], 2) }}</span>
            </div>
            @endif
            @if($calculos['descuento'] > 0)
            <div class="flex justify-between text-slate-700 font-bold">
                <span>Descuento:</span>
                <span>-${{ number_format($calculos['descuento'], 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm font-black text-slate-900 pt-1 border-t border-slate-200">
                <span>TOTAL A PAGAR:</span>
                <span>${{ number_format($calculos['total_pagar'], 2) }}</span>
            </div>
        </div>

        <!-- NOTA LEGAL SALVADOREÑA Y AGRADECIMIENTO -->
        <div class="text-center pt-3 text-[10px] text-slate-500 space-y-1">
            <p class="font-bold text-slate-700">VENTA A CONSUMIDOR FINAL</p>
            <p>Precios incluyen el 13% de IVA (Ley de El Salvador)</p>
            <p class="mt-2 italic">¡Gracias por preferir a AXStore!</p>
            <p>Conserve este ticket para reclamos o cambios dentro de 3 días hábiles.</p>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- CASO 2: FACTURA COMERCIAL (CONSUMIDOR FINAL - FORMATO FORMAL)              -->
    <!-- ========================================================================= -->
    @elseif($tipo === 'factura_comercial')
    <div class="print-container max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 text-slate-800 text-xs">
        
        <!-- ENCABEZADO Y DATOS DE LA EMPRESA -->
        <div class="grid grid-cols-12 gap-4 pb-6 border-b border-slate-200">
            <div class="col-span-8 flex items-center gap-4">
                <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-16 w-auto max-w-[90px] object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-black text-slate-900">AXSTORE S.A. DE C.V.</h1>
                    <p class="text-xs font-semibold text-slate-600">Comercio al por menor de accesorios, iluminación y repuestos</p>
                    <p class="text-[11px] text-slate-500">Alameda Franklin Delano Roosevelt #2135, San Salvador, El Salvador</p>
                    <p class="text-[11px] text-slate-500">NIT: 0614-150995-102-1 • NRC: 284910-3 • Tel: (503) 2225-8800</p>
                </div>
            </div>

            <!-- CUADRO DE FOLIO DE FACTURA -->
            <div class="col-span-4 rounded-xl border-2 border-blue-600 p-3 text-center bg-blue-50/30">
                <span class="text-[11px] font-black uppercase tracking-wider text-blue-700 block">
                    FACTURA CONSUMIDOR FINAL
                </span>
                <span class="text-[10px] text-slate-500 font-bold block mb-1">SERIE: AX-2026</span>
                <span class="text-lg font-black text-slate-900 font-mono-receipt block">
                    N° FAC-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                </span>
                <span class="text-[10px] text-slate-400 block mt-1">Resolución DGT El Salvador</span>
            </div>
        </div>

        <!-- DATOS DEL CLIENTE Y DE LA OPERACIÓN -->
        <div class="grid grid-cols-12 gap-4 py-4 border-b border-slate-200 bg-slate-50/50 p-4 rounded-xl my-4">
            <div class="col-span-7 space-y-1.5">
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[70px]">Cliente:</span>
                    <span class="font-black text-slate-900 text-sm">{{ $cliente['nombre'] }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[70px]">DUI / NIT:</span>
                    <span class="font-semibold text-slate-800">{{ $cliente['documento'] }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[70px]">Dirección:</span>
                    <span class="text-slate-700">{{ $cliente['direccion'] }}</span>
                </div>
            </div>

            <div class="col-span-5 space-y-1.5 border-l border-slate-200 pl-4">
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600">Fecha de Emisión:</span>
                    <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600">Hora:</span>
                    <span>{{ \Carbon\Carbon::parse($venta->fecha)->format('h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600">Condición de Pago:</span>
                    <span class="font-semibold text-slate-800">{{ $venta->metodo_pago }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-bold text-slate-600">Vendedor:</span>
                    <span class="text-slate-700">{{ $venta->usuario?->nombre_real ?? $venta->usuario?->username ?? 'Josue' }}</span>
                </div>
            </div>
        </div>

        <!-- TABLA OFICIAL SALVADOREÑA DE FACTURA CONSUMIDOR FINAL -->
        <table class="w-full text-left text-xs mb-4">
            <thead class="bg-slate-100 uppercase text-[10px] font-bold text-slate-600 border border-slate-200">
                <tr>
                    <th class="py-2.5 px-3 text-center w-12">Cant.</th>
                    <th class="py-2.5 px-3">Descripción de Bienes / Servicios</th>
                    <th class="py-2.5 px-3 text-right w-24">Precio Unit.</th>
                    <th class="py-2.5 px-3 text-right w-24">No Sujetas</th>
                    <th class="py-2.5 px-3 text-right w-24">Exentas</th>
                    <th class="py-2.5 px-3 text-right w-28">Gravadas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 border-x border-b border-slate-200">
                @foreach($calculos['lineas'] as $item)
                <tr>
                    <td class="py-2 px-3 text-center font-bold text-slate-800">{{ $item['cantidad'] }}</td>
                    <td class="py-2 px-3">
                        <p class="font-bold text-slate-900">{{ $item['producto'] }}</p>
                        <span class="text-[11px] text-slate-500">{{ $item['variante'] }} (SKU: {{ $item['sku'] }})</span>
                    </td>
                    <td class="py-2 px-3 text-right text-slate-700">${{ number_format($item['precio_unitario'], 2) }}</td>
                    <td class="py-2 px-3 text-right text-slate-400">$0.00</td>
                    <td class="py-2 px-3 text-right text-slate-400">$0.00</td>
                    <td class="py-2 px-3 text-right font-black text-slate-900">${{ number_format($item['subtotal'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- RESUMEN Y LIQUIDACIÓN LEGAL DE FACTURA -->
        <div class="grid grid-cols-12 gap-6 mt-4">
            <div class="col-span-7 flex flex-col justify-between">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Valor en Letras:</span>
                    <p class="text-xs font-black text-slate-800">{{ $calculos['monto_letras'] }}</p>
                </div>

                <div class="text-[10px] text-slate-400 pt-3">
                    <p class="font-bold text-slate-600">República de El Salvador • Código Tributario Art. 114</p>
                    <p>Operación efectuada a Consumidor Final. Los precios consignados incluyen el 13% de IVA.</p>
                    <p class="text-slate-500 mt-0.5">Conserve esta factura para reclamos o cambios dentro de 3 días hábiles.</p>
                </div>
            </div>

            <div class="col-span-5 bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Sumas Gravadas:</span>
                    <span class="font-bold text-slate-800">${{ number_format($calculos['subtotal_general'], 2) }}</span>
                </div>
                @if(!empty($calculos['precio_envio']) && $calculos['precio_envio'] > 0)
                <div class="flex justify-between text-blue-700 font-bold">
                    <span>(+) Costo de Envío:</span>
                    <span>+${{ number_format($calculos['precio_envio'], 2) }}</span>
                </div>
                @endif
                @if($calculos['descuento'] > 0)
                <div class="flex justify-between text-emerald-700 font-bold">
                    <span>(-) Descuentos:</span>
                    <span>-${{ number_format($calculos['descuento'], 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Ventas No Sujetas:</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Ventas Exentas:</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between text-base font-black text-blue-700 pt-2 border-t border-slate-200">
                    <span>TOTAL A PAGAR:</span>
                    <span>${{ number_format($calculos['total_pagar'], 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- CASO 3: FACTURA TRIBUTARIA (COMPROBANTE DE CRÉDITO FISCAL - CCF)          -->
    <!-- ========================================================================= -->
    @elseif($tipo === 'credito_fiscal')
    <div class="print-container max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border-2 border-slate-300 text-slate-800 text-xs">
        
        <!-- ENCABEZADO FORMAL CCF -->
        <div class="grid grid-cols-12 gap-4 pb-6 border-b-2 border-slate-300">
            <div class="col-span-7">
                <div class="flex items-center gap-3.5 mb-1.5">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="AXStore Logo" class="h-16 w-auto max-w-[90px] object-contain shrink-0">
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">AXSTORE S.A. DE C.V.</h1>
                        <p class="text-[11px] font-bold text-purple-800">DOCUMENTO TRIBUTARIO ELECTRÓNICO (DTE - 03)</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-600 font-semibold">Giro: Venta de Accesorios, Iluminación y Mercadería en General</p>
                <p class="text-[11px] text-slate-500">Alameda Franklin Delano Roosevelt #2135, San Salvador, El Salvador</p>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 font-bold text-[11px] text-slate-700">
                    <span>NIT: 0614-150995-102-1</span>
                    <span>NRC: 284910-3</span>
                    <span>Tel: (503) 2225-8800</span>
                </div>
            </div>

            <!-- CUADRO DE REGISTRO TRIBUTARIO CCF -->
            <div class="col-span-5 rounded-xl border-2 border-purple-800 p-3.5 text-center bg-purple-50/40">
                <span class="text-xs font-black uppercase tracking-wider text-purple-900 block">
                    COMPROBANTE DE CRÉDITO FISCAL
                </span>
                <span class="text-[10px] font-bold text-slate-500 block">REPÚBLICA DE EL SALVADOR</span>
                <span class="text-lg font-black text-slate-900 font-mono-receipt block my-1">
                    N° CCF-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}
                </span>
                <span class="text-[10px] text-purple-800 font-semibold block">DGT Ministerio de Hacienda</span>
            </div>
        </div>

        <!-- DATOS DEL CONTRIBUYENTE RECEPTOR (LEYES TRIBUTARIAS EL SALVADOR) -->
        <div class="my-4 border border-slate-300 rounded-xl p-4 bg-slate-50/70 space-y-2">
            <div class="grid grid-cols-12 gap-3">
                <div class="col-span-8 flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[90px]">Razón Social:</span>
                    <span class="font-black text-slate-900 text-sm">{{ $cliente['nombre'] }}</span>
                </div>
                <div class="col-span-4 flex items-baseline gap-2 justify-end">
                    <span class="font-bold text-slate-600">Fecha:</span>
                    <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 text-xs">
                <div class="col-span-4 flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[90px]">NRC:</span>
                    <span class="font-mono-receipt font-black text-purple-900">{{ $cliente['nrc'] ?: '284910-3' }}</span>
                </div>
                <div class="col-span-4 flex items-baseline gap-2">
                    <span class="font-bold text-slate-600">NIT / DUI:</span>
                    <span class="font-mono-receipt font-semibold text-slate-800">{{ $cliente['documento'] }}</span>
                </div>
                <div class="col-span-4 flex items-baseline gap-2 justify-end">
                    <span class="font-bold text-slate-600">Condición:</span>
                    <span class="font-bold text-slate-800">{{ $venta->metodo_pago }}</span>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 text-xs pt-1 border-t border-slate-200">
                <div class="col-span-7 flex items-baseline gap-2">
                    <span class="font-bold text-slate-600 min-w-[90px]">Giro Comercial:</span>
                    <span class="text-slate-800 truncate">{{ $cliente['giro'] }}</span>
                </div>
                <div class="col-span-5 flex items-baseline gap-2 justify-end">
                    <span class="font-bold text-slate-600">Dirección:</span>
                    <span class="text-slate-800 truncate">{{ $cliente['direccion'] }} ({{ $cliente['departamento'] }})</span>
                </div>
            </div>
        </div>

        <!-- TABLA OFICIAL SEGÚN CÓDIGO TRIBUTARIO EL SALVADOR (PRECIOS NETOS SIN IVA) -->
        <table class="w-full text-left text-xs mb-4">
            <thead class="bg-purple-900 text-white uppercase text-[10px] font-bold tracking-wider">
                <tr>
                    <th class="py-2.5 px-3 text-center w-12">Cant.</th>
                    <th class="py-2.5 px-3">Descripción de Bienes / Servicios</th>
                    <th class="py-2.5 px-3 text-right w-24">Precio Neto</th>
                    <th class="py-2.5 px-3 text-right w-24">No Sujetas</th>
                    <th class="py-2.5 px-3 text-right w-24">Exentas</th>
                    <th class="py-2.5 px-3 text-right w-28">Gravadas Netas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 border-x border-b border-slate-300">
                @foreach($calculos['lineas'] as $item)
                <tr>
                    <td class="py-2 px-3 text-center font-bold text-slate-800">{{ $item['cantidad'] }}</td>
                    <td class="py-2 px-3">
                        <p class="font-bold text-slate-900">{{ $item['producto'] }}</p>
                        <span class="text-[11px] text-slate-500">{{ $item['variante'] }} (SKU: {{ $item['sku'] }})</span>
                    </td>
                    <td class="py-2 px-3 text-right text-slate-700">${{ number_format($item['precio_unitario_neto'], 2) }}</td>
                    <td class="py-2 px-3 text-right text-slate-400">$0.00</td>
                    <td class="py-2 px-3 text-right text-slate-400">$0.00</td>
                    <td class="py-2 px-3 text-right font-black text-slate-900">${{ number_format($item['subtotal_neto'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- DESGLOSE TRIBUTARIO OFICIAL DE EL SALVADOR -->
        <div class="grid grid-cols-12 gap-6 mt-4">
            
            <!-- VALOR EN LETRAS Y FIRMAS DE LEY -->
            <div class="col-span-7 flex flex-col justify-between space-y-4">
                <div class="p-3 bg-purple-50/50 border border-purple-200 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-900 block">Total en Letras:</span>
                    <p class="text-xs font-black text-slate-900">{{ $calculos['monto_letras'] }}</p>
                </div>

                <!-- FIRMAS CONFORMES EXIGIDAS POR CÓDIGO TRIBUTARIO SALVADOREÑO -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200 text-[10px] text-slate-600">
                    <div class="border border-slate-200 p-2.5 rounded-lg text-center">
                        <div class="border-b border-slate-300 h-8 mb-1"></div>
                        <p class="font-bold text-slate-800">Entregado por (Emisor)</p>
                        <p class="text-[9px] text-slate-400">AXStore S.A. de C.V.</p>
                    </div>
                    <div class="border border-slate-200 p-2.5 rounded-lg text-center">
                        <div class="border-b border-slate-300 h-8 mb-1"></div>
                        <p class="font-bold text-slate-800">Recibido Conforme (Cliente)</p>
                        <p class="text-[9px] text-slate-400">Nombre, Firma y DUI</p>
                    </div>
                </div>

                <p class="text-[9px] text-slate-400">
                    Comprobante de Crédito Fiscal emitido en cumplimiento de los Artículos 107 y 108 del Código Tributario de la República de El Salvador.
                </p>
            </div>

            <!-- CUADRO DE LIQUIDACIÓN TRIBUTARIA -->
            <div class="col-span-5 bg-slate-50 border-2 border-slate-300 rounded-xl p-3.5 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Sumas Gravadas (Neto):</span>
                    <span class="font-bold text-slate-900">${{ number_format($calculos['ventas_gravadas_netas'], 2) }}</span>
                </div>
                <div class="flex justify-between text-purple-800 font-bold bg-purple-50 px-2 py-1 rounded">
                    <span>13% IVA (Débito Fiscal):</span>
                    <span>+${{ number_format($calculos['iva_13'], 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-900">${{ number_format($calculos['subtotal'], 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Ventas No Sujetas:</span>
                    <span>$0.00</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Ventas Exentas:</span>
                    <span>$0.00</span>
                </div>
                @if($calculos['retencion_1'] > 0)
                <div class="flex justify-between text-red-600 font-bold">
                    <span>(-) 1% Retención IVA (Gran Contribuyente):</span>
                    <span>-${{ number_format($calculos['retencion_1'], 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-black text-purple-900 pt-2 border-t-2 border-slate-300">
                    <span>TOTAL A PAGAR:</span>
                    <span>${{ number_format($calculos['total_pagar'], 2) }}</span>
                </div>
            </div>
        </div>

    </div>
    @endif

    <script>
        // Dispara automáticamente la impresión al cargar
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 400);
        });
    </script>
</body>
</html>
