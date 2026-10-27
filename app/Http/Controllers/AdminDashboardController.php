<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\DashboardReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected DashboardReporteService $dashboardService
    ) {}

    /**
     * Dashboard Administrativo Ejecutivo.
     * Muestra los 4 KPIs estratégicos, gráficos interactivos de analítica y resumen de inventario.
     */
    public function index(Request $request)
    {
        $filtrosEntrada = [
            'preset' => $request->query('preset'),
            'desde' => $request->query('desde'),
            'hasta' => $request->query('hasta'),
            'vendedor_id' => $request->query('vendedor_id'),
            'producto_id' => $request->query('producto_id'),
        ];

        // 1. Resolver fechas
        $rango = $this->dashboardService->resolverRangoFechas($filtrosEntrada);
        $filtros = array_merge($filtrosEntrada, $rango);

        // 2. Calcular KPIs
        $kpis = $this->dashboardService->getKpis($filtros);

        // 3. Obtener series de datos para gráficos
        $graficoComparativa = $this->dashboardService->getGraficoComparativaFinanciera($filtros);
        $graficoMovimientos = $this->dashboardService->getGraficoMovimientosYComisiones($filtros);

        // Si es una petición AJAX (por ejemplo al cambiar filtros en tiempo real)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'kpis' => $kpis,
                'graficoComparativa' => $graficoComparativa,
                'graficoMovimientos' => $graficoMovimientos,
                'rango' => $rango,
            ]);
        }

        // 4. Catálogos para filtros
        $vendedores = User::where('estado', 1)
            ->orderBy('nombre_real')
            ->get(['id', 'nombre_real', 'username', 'rol']);

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('admin.dashboard', compact(
            'kpis',
            'graficoComparativa',
            'graficoMovimientos',
            'filtros',
            'vendedores',
            'productos'
        ));
    }

    /**
     * Vista de Informes y Reportes de Movimientos.
     * Soporta visualización interactiva y vista limpia de impresión / exportación formal.
     */
    public function reportesMovimientos(Request $request)
    {
        $filtrosEntrada = [
            'preset' => $request->query('preset'),
            'desde' => $request->query('desde'),
            'hasta' => $request->query('hasta'),
            'tipo' => $request->query('tipo', 'todos'),
            'vendedor_id' => $request->query('vendedor_id'),
            'producto_id' => $request->query('producto_id'),
            'q' => $request->query('q', ''),
            'modo' => $request->query('modo', 'normal'), // 'normal' o 'imprimir'
        ];

        // 1. Resolver fechas
        $rango = $this->dashboardService->resolverRangoFechas($filtrosEntrada);
        $filtros = array_merge($filtrosEntrada, $rango);

        // 2. KPIs del periodo para el bloque ejecutivo del reporte
        $kpis = $this->dashboardService->getKpis($filtros);

        // 3. Movimientos (si es modo imprimir, se cargan todos sin paginar; si es normal, 25 por página)
        $perPage = ($filtros['modo'] === 'imprimir' || $request->has('imprimir')) ? 500 : 25;
        $reporteData = $this->dashboardService->getReporteMovimientos($filtros, $perPage);

        $movimientos = $reporteData['paginados'];
        $todosMovimientos = $reporteData['todos_movimientos'];
        $totales = $reporteData['totales'];

        // Gráficos para el informe visual
        $graficoComparativa = $this->dashboardService->getGraficoComparativaFinanciera($filtros);
        $graficoMovimientos = $this->dashboardService->getGraficoMovimientosYComisiones($filtros);

        // 4. Catálogos para filtros
        $vendedores = User::where('estado', 1)
            ->orderBy('nombre_real')
            ->get(['id', 'nombre_real', 'username', 'rol']);

        $productos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        // Folio único y metadatos del reporte para formalidad institucional
        $folioReporte = 'REP-' . date('Ymd') . '-' . strtoupper(substr(md5(json_encode($filtros) . time()), 0, 4));
        $fechaGeneracion = now()->format('d/m/Y H:i:s');
        $usuarioEmisor = Auth::user()?->nombre_real ?? Auth::user()?->username ?? 'Administrador';

        // ── Registro en Bitácora ──
        if ($request->query('modo') === 'imprimir') {
            ActivityLogger::log(
                'EXPORTAR',
                'Reportes',
                "El usuario generó la vista de impresión / PDF del informe de movimientos (Folio: {$folioReporte})."
            );
        } else {
            ActivityLogger::log(
                'EXPORTAR',
                'Reportes',
                "Consulta y generación del informe de movimientos y flujo financiero (Folio: {$folioReporte})."
            );
        }

        return view('admin.reportes.movimientos', compact(
            'kpis',
            'movimientos',
            'todosMovimientos',
            'totales',
            'graficoComparativa',
            'graficoMovimientos',
            'filtros',
            'vendedores',
            'productos',
            'folioReporte',
            'fechaGeneracion',
            'usuarioEmisor'
        ));
    }

    /**
     * Exporta el informe oficial en formato Microsoft Excel (.xlsx) con diseño corporativo completo:
     * - Paleta de colores de marca AXStore (Azul marino, Índigo, Esmeralda, Rosa).
     * - Tarjetas ejecutivas con los 4 KPIs estratégicos del periodo.
     * - Tabla de movimientos con encabezados formateados, bordes y colores alternados.
     * - Formato numérico de moneda y alineación profesional de columnas.
     * - Gráficos visuales integrados en la hoja de cálculo.
     */
    public function exportarMovimientosCsv(Request $request)
    {
        $filtrosEntrada = [
            'preset' => $request->query('preset'),
            'desde' => $request->query('desde'),
            'hasta' => $request->query('hasta'),
            'tipo' => $request->query('tipo', 'todos'),
            'vendedor_id' => $request->query('vendedor_id'),
            'producto_id' => $request->query('producto_id'),
            'q' => $request->query('q', ''),
        ];

        $rango = $this->dashboardService->resolverRangoFechas($filtrosEntrada);
        $filtros = array_merge($filtrosEntrada, $rango);

        $kpis = $this->dashboardService->getKpis($filtros);
        $reporteData = $this->dashboardService->getReporteMovimientos($filtros, 10000);
        $movimientos = $reporteData['todos_movimientos'];
        $totales = $reporteData['totales'];

        // Crear libro de Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('AXStore ERP')
            ->setLastModifiedBy('AXStore Sistema')
            ->setTitle('Informe de Movimientos & Flujo Financiero')
            ->setSubject('Auditoría Interna AXStore')
            ->setDescription('Reporte financiero y detallado de movimientos de inventario');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Movimientos');
        $sheet->setShowGridLines(true);

        // ── 1. LOGO Y ENCABEZADO INSTITUCIONAL FORMAL (ESTILO PDF) ───────────
        $logoPath = public_path('assets/images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('AXStore Logo');
            $drawing->setDescription('Logo Oficial AXStore');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('B2');
            $drawing->setHeight(52);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Título de la empresa y membrete
        $sheet->mergeCells('D2:K2');
        $sheet->setCellValue('D2', 'AXSTORE · COMERCIO & LOGÍSTICA DIGITAL');
        $sheet->getStyle('D2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A'], 'size' => 14],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('D3:K3');
        $sheet->setCellValue('D3', 'San Salvador, El Salvador · Tu tienda online  |  PBX: (503) 2200-0000 · soporte@axstore.com');
        $sheet->getStyle('D3')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '64748B']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells('D4:K4');
        $sheet->setCellValue('D4', 'INFORME OFICIAL DE MOVIMIENTOS & FLUJO FINANCIERO');
        $sheet->getStyle('D4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 11],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        // Ficha ejecutiva a la derecha (Estilo tarjeta PDF)
        $sheet->mergeCells('M2:Q2');
        $sheet->setCellValue('M2', 'TIPO DE DOCUMENTO: AUDITORÍA INTERNA');
        $sheet->getStyle('M2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);

        $sheet->setCellValue('M3', 'Folio:');
        $sheet->setCellValue('N3', 'REP-' . date('Ymd') . '-' . strtoupper(substr(md5(json_encode($filtros) . time()), 0, 4)));
        $sheet->setCellValue('M4', 'Emisión:');
        $sheet->setCellValue('N4', now()->format('d/m/Y H:i:s'));
        $sheet->setCellValue('P3', 'Auditor:');
        $sheet->setCellValue('Q3', Auth::user()?->nombre_real ?? 'Administrador');
        $sheet->setCellValue('P4', 'Filtro:');
        $sheet->setCellValue('Q4', ucfirst($filtros['tipo']));

        $sheet->getStyle('M3:Q4')->applyFromArray([
            'font' => ['size' => 8.5, 'color' => ['rgb' => '334155']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
        ]);
        $sheet->getStyle('M3')->getFont()->setBold(true);
        $sheet->getStyle('M4')->getFont()->setBold(true);
        $sheet->getStyle('P3')->getFont()->setBold(true);
        $sheet->getStyle('P4')->getFont()->setBold(true);
        $sheet->getStyle('M2:Q4')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(8);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(10);

        // Barra decorativa azul institucional (Fila 5)
        $sheet->mergeCells('A5:Q5');
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);

        // Fila 6: Metadatos del periodo activo
        $sheet->mergeCells('A6:Q6');
        $sheet->setCellValue('A6', '   Período Auditado: ' . \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') . '   |   Registros Procesados: ' . $totales['cantidad_movimientos'] . ' operaciones');
        $sheet->getStyle('A6:Q6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E293B'], 'size' => 9.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(22);
        $sheet->getRowDimension(7)->setRowHeight(8);

        // ── 2. TARJETAS DE KPIS ESTRATÉGICOS (FILAS 8-10) ─────────────────────
        // KPI 1: Ingresos Totales
        $sheet->mergeCells('A8:C8');
        $sheet->mergeCells('A9:C9');
        $sheet->mergeCells('A10:C10');
        $sheet->setCellValue('A8', '1. INGRESOS TOTALES');
        $sheet->setCellValue('A9', (float) $kpis['ingresos']['total']);
        $sheet->setCellValue('A10', $kpis['ingresos']['ventas_count'] . ' ventas concretadas');
        $sheet->getStyle('A8:C8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF'], 'size' => 8.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A9:C9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A'], 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '$#,##0.00'],
        ]);
        $sheet->getStyle('A10:C10')->applyFromArray([
            'font' => ['size' => 8, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A8:C10')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]],
        ]);

        // KPI 2: Costo de Ventas
        $sheet->mergeCells('E8:G8');
        $sheet->mergeCells('E9:G9');
        $sheet->mergeCells('E10:G10');
        $sheet->setCellValue('E8', '2. COSTO DE VENTAS (COGS)');
        $sheet->setCellValue('E9', (float) ($kpis['costos']['cogs_periodo'] ?? 0));
        $sheet->setCellValue('E10', $kpis['costos']['unidades_vendidas'] . ' unidades despachadas');
        $sheet->getStyle('E8:G8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '9F1239'], 'size' => 8.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE4E6']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('E9:G9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '881337'], 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF1F2']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '$#,##0.00'],
        ]);
        $sheet->getStyle('E10:G10')->applyFromArray([
            'font' => ['size' => 8, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF1F2']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('E8:G10')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FDA4AF']]],
        ]);

        // KPI 3: Ganancia Neta
        $sheet->mergeCells('I8:K8');
        $sheet->mergeCells('I9:K9');
        $sheet->mergeCells('I10:K10');
        $sheet->setCellValue('I8', '3. GANANCIA NETA');
        $sheet->setCellValue('I9', (float) $kpis['ganancias']['neta']);
        $sheet->setCellValue('I10', 'Margen real: ' . $kpis['ganancias']['margen_porcentaje'] . '%');
        $sheet->getStyle('I8:K8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '065F46'], 'size' => 8.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('I9:K9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '047857'], 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '$#,##0.00'],
        ]);
        $sheet->getStyle('I10:K10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '047857']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('I8:K10')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '6EE7B7']]],
        ]);

        // KPI 4: Comisiones
        $sheet->mergeCells('M8:O8');
        $sheet->mergeCells('M9:O9');
        $sheet->mergeCells('M10:O10');
        $sheet->setCellValue('M8', '4. COMISIONES DE VENTAS');
        $sheet->setCellValue('M9', (float) $kpis['ganancias']['comisiones_periodo']);
        $sheet->setCellValue('M10', 'Pendiente: $' . number_format($kpis['comisiones_por_pagar']['total_pendiente'], 2));
        $sheet->getStyle('M8:O8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '3730A3'], 'size' => 8.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('M9:O9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '312E81'], 'size' => 14],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'numberFormat' => ['formatCode' => '$#,##0.00'],
        ]);
        $sheet->getStyle('M10:O10')->applyFromArray([
            'font' => ['size' => 8, 'color' => ['rgb' => '4338CA']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('M8:O10')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'A5B4FC']]],
        ]);

        $sheet->getRowDimension(8)->setRowHeight(18);
        $sheet->getRowDimension(9)->setRowHeight(24);
        $sheet->getRowDimension(10)->setRowHeight(16);
        $sheet->getRowDimension(11)->setRowHeight(10);

        // ── 3. ENCABEZADOS DE LA TABLA DE MOVIMIENTOS (FILA 12) ───────────────
        $headers = [
            'A12' => '#',
            'B12' => 'Fecha',
            'C12' => 'Hora',
            'D12' => 'Tipo / Categoría',
            'E12' => 'Folio / Ref.',
            'F12' => 'Producto / Descripción',
            'G12' => 'SKU',
            'H12' => 'Cant.',
            'I12' => 'Signo',
            'J12' => 'C. Unitario ($)',
            'K12' => 'C. Total ($)',
            'L12' => 'Ingreso Total ($)',
            'M12' => 'Comisión ($)',
            'N12' => 'Ganancia Neta ($)',
            'O12' => 'Estado',
            'P12' => 'Usuario / Responsable',
            'Q12' => 'Origen / Destino / Cliente',
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $sheet->getStyle('A12:Q12')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']], // Azul Intenso Corporativo
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '1E40AF']]],
        ]);
        $sheet->getRowDimension(12)->setRowHeight(26);

        // ── 4. CUERPO DE DATOS CON ESTILOS Y COLORES IDÉNTICOS AL PDF ───────
        $currentRow = 13;
        foreach ($movimientos as $index => $m) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $m['fecha_formateada'] ?? '');
            $sheet->setCellValue('C' . $currentRow, $m['hora_formateada'] ?? '');
            $sheet->setCellValue('D' . $currentRow, $m['categoria'] ?? '');
            $sheet->setCellValue('E' . $currentRow, $m['folio'] ?? '');
            $sheet->setCellValue('F' . $currentRow, $m['descripcion'] ?? '');
            $sheet->setCellValue('G' . $currentRow, $m['sku'] ?? '');
            $sheet->setCellValue('H' . $currentRow, (int) ($m['cantidad'] ?? 0));
            $sheet->setCellValue('I' . $currentRow, $m['signo_stock'] ?? '');
            $sheet->setCellValue('J' . $currentRow, (float) ($m['costo_unitario'] ?? 0));
            $sheet->setCellValue('K' . $currentRow, (float) ($m['costo_total'] ?? 0));
            $sheet->setCellValue('L' . $currentRow, (float) ($m['ingreso_total'] ?? 0));
            $sheet->setCellValue('M' . $currentRow, (float) ($m['comision'] ?? 0));
            $sheet->setCellValue('N' . $currentRow, (float) ($m['ganancia_neta'] ?? 0));
            $sheet->setCellValue('O' . $currentRow, $m['estado'] ?? '');
            $sheet->setCellValue('P' . $currentRow, $m['usuario_responsable'] ?? '');
            $sheet->setCellValue('Q' . $currentRow, $m['origen_destino'] ?? '');

            // Formato alternado de filas (Zebra striping)
            if ($index % 2 === 1) {
                $sheet->getStyle("A{$currentRow}:Q{$currentRow}")->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
            }

            // Alineaciones
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}:C{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}:E{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("O{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Folio en negrita azul oscuro
            $sheet->getStyle("E{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('1E3A8A');

            // Categoría / Tipo con colores idénticos a los Badges del PDF
            $badgeColorKey = $m['tipo_badge'] ?? 'slate';
            $badgeColors = match ($badgeColorKey) {
                'rose' => ['bg' => 'FFE4E6', 'text' => '9F1239'],      // Venta / Salida
                'emerald' => ['bg' => 'D1FAE5', 'text' => '065F46'],   // Compra / Entrada
                'amber' => ['bg' => 'FEF3C7', 'text' => '92400E'],     // Transferencia
                'purple' => ['bg' => 'F3E8FF', 'text' => '6B21A8'],    // Devolución
                'indigo' => ['bg' => 'E0E7FF', 'text' => '3730A3'],    // Comisión
                default => ['bg' => 'F1F5F9', 'text' => '334155'],
            };
            $sheet->getStyle("D{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $badgeColors['text']], 'size' => 8.5],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $badgeColors['bg']]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Signo y cantidad coloreados (+ en verde esmeralda, - en rojo rose, neutro)
            $signo = $m['signo_stock'] ?? '';
            if ($signo === '+') {
                $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('059669');
            } elseif ($signo === '-') {
                $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('E11D48');
            } else {
                $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getFont()->getColor()->setRGB('475569');
            }

            // Formato de moneda profesional
            $sheet->getStyle("J{$currentRow}:N{$currentRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle("J{$currentRow}:N{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Colores por columna de montos idénticos al PDF:
            // C. Total en tono carbón, Ingreso en Azul Marino corporativo
            $sheet->getStyle("L{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('1E3A8A');
            // Comisión en Índigo
            if ((float) ($m['comision'] ?? 0) > 0) {
                $sheet->getStyle("M{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('4338CA');
            }
            // Ganancia Neta: Verde Esmeralda si es positiva, Rojo si es negativa
            $ganancia = (float) ($m['ganancia_neta'] ?? 0);
            if ($ganancia > 0) {
                $sheet->getStyle("N{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('059669'); // Verde esmeralda
            } elseif ($ganancia < 0) {
                $sheet->getStyle("N{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('DC2626'); // Rojo
            }

            // Estado badge simple
            $estadoTxt = strtolower($m['estado'] ?? '');
            if (str_contains($estadoTxt, 'entregad') || str_contains($estadoTxt, 'completad')) {
                $sheet->getStyle("O{$currentRow}")->getFont()->getColor()->setRGB('059669');
            } elseif (str_contains($estadoTxt, 'devoluci') || str_contains($estadoTxt, 'cancel')) {
                $sheet->getStyle("O{$currentRow}")->getFont()->getColor()->setRGB('DC2626');
            }

            $sheet->getRowDimension($currentRow)->setRowHeight(21);
            $currentRow++;
        }

        // ── 5. FILA DE TOTALES CONSOLIDADOS ───────────────────────────────────
        $sheet->mergeCells("A{$currentRow}:G{$currentRow}");
        $sheet->setCellValue("A{$currentRow}", 'TOTALES GENERALES (' . $totales['cantidad_movimientos'] . ' REGISTROS)');
        $sheet->setCellValue("H{$currentRow}", $totales['total_unidades']);
        $sheet->setCellValue("I{$currentRow}", '—');
        $sheet->setCellValue("J{$currentRow}", '—');
        $sheet->setCellValue("K{$currentRow}", (float) $totales['total_costos']);
        $sheet->setCellValue("L{$currentRow}", (float) $totales['total_ingresos']);
        $sheet->setCellValue("M{$currentRow}", (float) $totales['total_comisiones']);
        $sheet->setCellValue("N{$currentRow}", (float) $totales['total_ganancias']);
        $sheet->setCellValue("O{$currentRow}", '—');
        $sheet->setCellValue("P{$currentRow}", '—');
        $sheet->setCellValue("Q{$currentRow}", '—');

        $sheet->getStyle("A{$currentRow}:Q{$currentRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'borders' => [
                'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE, 'color' => ['rgb' => '2563EB']],
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE, 'color' => ['rgb' => '2563EB']],
            ],
        ]);
        $sheet->getStyle("K{$currentRow}:N{$currentRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("K{$currentRow}:N{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($currentRow)->setRowHeight(24);

        // ── 6. BORDES GLOBALES Y AUTO-AJUSTE DE ANCHO DE COLUMNAS ─────────────
        $sheet->getStyle("A12:Q{$currentRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
            ],
        ]);

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Anchos mínimos garantizados
        $sheet->getColumnDimension('F')->setWidth(38); // Producto
        $sheet->getColumnDimension('Q')->setWidth(35); // Origen/Destino

        // ── 7. HOJA DE GRÁFICOS Y ANÁLISIS VISUAL ─────────────────────────────
        $graficoComparativa = $this->dashboardService->getGraficoComparativaFinanciera($filtros);
        $sheetGraficos = $spreadsheet->createSheet();
        $sheetGraficos->setTitle('Gráficos & Analítica');
        $sheetGraficos->setShowGridLines(true);

        // Título de la hoja de gráficos
        $sheetGraficos->mergeCells('A1:J1');
        $sheetGraficos->setCellValue('A1', 'AXSTORE · ANALÍTICA Y GRÁFICOS COMPARATIVOS FINANCIEROS');
        $sheetGraficos->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
        ]);
        $sheetGraficos->getRowDimension(1)->setRowHeight(28);

        // Tabla de datos base para el gráfico nativo
        $sheetGraficos->setCellValue('A3', 'Fecha');
        $sheetGraficos->setCellValue('B3', 'Ingresos ($)');
        $sheetGraficos->setCellValue('C3', 'Costos ($)');
        $sheetGraficos->setCellValue('D3', 'Ganancia Neta ($)');

        $sheetGraficos->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $gRow = 4;
        $labels = $graficoComparativa['labels'] ?? [];
        $ingresos = $graficoComparativa['ingresos'] ?? [];
        $costos = $graficoComparativa['costos'] ?? [];
        $ganancias = $graficoComparativa['ganancias'] ?? [];

        foreach ($labels as $idx => $label) {
            $sheetGraficos->setCellValue('A' . $gRow, $label);
            $sheetGraficos->setCellValue('B' . $gRow, $ingresos[$idx] ?? 0);
            $sheetGraficos->setCellValue('C' . $gRow, $costos[$idx] ?? 0);
            $sheetGraficos->setCellValue('D' . $gRow, $ganancias[$idx] ?? 0);
            $sheetGraficos->getStyle("B{$gRow}:D{$gRow}")->getNumberFormat()->setFormatCode('$#,##0.00');
            $gRow++;
        }

        $endGRow = max(4, $gRow - 1);
        $sheetGraficos->getStyle("A3:D{$endGRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheetGraficos->getColumnDimension($col)->setAutoSize(true);
        }

        // Crear Gráfico Nativo de Excel (Líneas comparativas)
        if (count($labels) > 0) {
            $dataSeriesLabels = [
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_STRING, "'Gráficos & Analítica'!\$B\$3", null, 1),
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_STRING, "'Gráficos & Analítica'!\$C\$3", null, 1),
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_STRING, "'Gráficos & Analítica'!\$D\$3", null, 1),
            ];

            $xAxisTickValues = [
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_STRING, "'Gráficos & Analítica'!\$A\$4:\$A\${$endGRow}", null, count($labels)),
            ];

            $dataSeriesValues = [
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Gráficos & Analítica'!\$B\$4:\$B\${$endGRow}", null, count($labels)),
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Gráficos & Analítica'!\$C\$4:\$C\${$endGRow}", null, count($labels)),
                new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues(\PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Gráficos & Analítica'!\$D\$4:\$D\${$endGRow}", null, count($labels)),
            ];

            $series = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
                \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_LINECHART,
                \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_STANDARD,
                range(0, count($dataSeriesValues) - 1),
                $dataSeriesLabels,
                $xAxisTickValues,
                $dataSeriesValues
            );

            $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$series]);
            $legend = new \PhpOffice\PhpSpreadsheet\Chart\Legend(\PhpOffice\PhpSpreadsheet\Chart\Legend::POSITION_TOP, null, false);
            $title = new \PhpOffice\PhpSpreadsheet\Chart\Title('Evolución Financiera (Ingresos vs. Costos vs. Ganancia Neta)');

            $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
                'chart_financiero',
                $title,
                $legend,
                $plotArea,
                true
            );

            $chart->setTopLeftPosition('F3');
            $chart->setBottomRightPosition('Q22');
            $sheetGraficos->addChart($chart);
        }

        // Seleccionar la primera hoja como activa al abrir
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'reporte_movimientos_axstore_' . date('Ymd_His') . '.xlsx';

        // ── Registro en Bitácora ──
        ActivityLogger::log(
            'EXPORTAR',
            'Reportes',
            "Descarga del informe oficial de movimientos en Excel (.xlsx) [Preset: {$filtros['preset']}, Desde: {$filtros['desde']} Hasta: {$filtros['hasta']}]."
        );

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
