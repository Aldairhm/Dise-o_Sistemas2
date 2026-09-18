document.addEventListener("alpine:init", () => {
    Alpine.data("productoPropuesta", () => ({
        // ==================== ESTADO GENERAL ====================
        paso: 1,
        nombre: "",
        categoriaId: "",
        descripcion: "",
        comisionGeneral: "",
        tieneVariantes: "", // "" | "no" | "si" -> decide si se usa el flujo combinatorio o el registro rápido
        precioMasivo: "",
        comisionMasiva: "",
        atributos: [],
        combinaciones: [],
        guardado: false,
        guardando: false,
        error: "",

        // Cambios detectados al volver del Paso 2 cuando ya existían variantes cargadas.
        cambiosPendientes: [],
        expandirTodo: false,
        _idCounter: 0,

        init() {
            const dataElement = document.querySelector('meta[name="producto-propuesta-data"]');
            if (!dataElement?.content) return;
            try {
                const data = JSON.parse(decodeURIComponent(escape(atob(dataElement.content))));
                const producto = data.producto;
                if (!producto) return;
                this.nombre = producto.nombre || "";
                this.categoriaId = String(producto.id_categoria || "");
                this.descripcion = producto.descripcion || "";
                this.comisionGeneral = String(producto.comision ?? "");
                this.tieneVariantes = producto.tiene_variantes || "si";
                // Se descarta cualquier atributo sin nombre o sin valores: puede quedar en la
                // base de datos de guardados anteriores a esta validación, y cargarlo tal cual
                // reproduciría el mismo error al volver a guardar.
                this.atributos = (data.atributos || [])
                    .filter((atributo) => atributo?.nombre?.trim())
                    .map((atributo) => ({ nombre: atributo.nombre, valores: [...(atributo.valores || [])] }));
                this.combinaciones = (data.variantes || []).map((variante) => ({
                    id: variante.id,
                    valores: (variante.valores || []).map((valor) => ({ ...valor })),
                    sku: variante.sku || "",
                    precio_venta: variante.precio_venta || "",
                    comision: variante.comision || this.comisionGeneral || 0,
                    imagenes: (variante.imagenes_existentes || []).map((imagen) => ({ existing: true, id: imagen.id, url: imagen.url, name: imagen.nombre })),
                    imagenesEliminadas: [],
                    expandido: false,
                    manual: false,
                    _dupSku: false,
                    _dupCombinacion: false,
                }));
                this.paso = this.tieneVariantes === "no" ? 2 : 3;
                this.recalcularDuplicados();
            } catch (error) {
                this.error = "No se pudieron cargar los datos del producto.";
            }
        },

        // ==================== NAVEGACIÓN ====================
        pasosNav() {
            return this.tieneVariantes === "no"
                ? [{ id: 1, label: "Generales" }, { id: 2, label: "Detalles" }]
                : [{ id: 1, label: "Generales" }, { id: 2, label: "Atributos" }, { id: 3, label: "Variantes" }];
        },

        // ==================== PASO 1: Generales ====================
        camposFaltantesPaso1() {
            const faltantes = [];
            if (!this.nombre.trim()) faltantes.push("Nombre del producto");
            if (!this.categoriaId) faltantes.push("Categoría");
            if (this.comisionGeneral === "" || Number(this.comisionGeneral) < 0) faltantes.push("Comisión base");
            if (this.tieneVariantes === "") faltantes.push("Indica si el producto tiene variantes");
            return faltantes;
        },

        // El botón de avance y este método validan exactamente lo mismo, así el mensaje de
        // error nunca contradice lo que el botón ya permitía saltar.
        siguiente() {
            const faltantes = this.camposFaltantesPaso1();
            if (faltantes.length) {
                this.error = `Completa: ${faltantes.join(", ")}.`;
                return;
            }
            this.error = "";
            if (this.tieneVariantes === "no") {
                // Registro rápido: una sola variante, sin pasar por el editor de atributos.
                if (!this.combinaciones.length) this.combinaciones = [this.crearVariante([])];
            } else if (!this.atributos.length) {
                this.agregarAtributo();
            }
            this.paso = 2;
        },

        // ==================== PASO 2: Atributos (flujo con variantes) ====================
        agregarAtributo() {
            this.atributos.push({ nombre: "", valores: [""] });
        },

        quitarAtributo(indice) {
            const atributo = this.atributos[indice];
            this.atributos.splice(indice, 1);
            if (atributo?.nombre && this.combinaciones.length) {
                const nombreLimpio = atributo.nombre.trim().toLowerCase();
                this.combinaciones.forEach((variante) => {
                    variante.valores = variante.valores.filter(
                        (v) => v.atributo.trim().toLowerCase() !== nombreLimpio
                    );
                });
                this.recalcularDuplicados();
            }
        },

        agregarValor(atributo) {
            atributo.valores.push("");
        },

        quitarValor(atributo, indice) {
            if (atributo.valores.length === 1) {
                atributo.valores[0] = "";
            } else {
                atributo.valores.splice(indice, 1);
            }
        },

        valoresValidos(atributo) {
            return atributo.valores.map((v) => v.trim()).filter(Boolean);
        },

        gruposValidos() {
            return this.atributos
                .map((a) => ({ nombre: a.nombre.trim(), valores: this.valoresValidos(a) }))
                .filter((a) => a.nombre && a.valores.length);
        },

        // Vista previa en vivo: cuántas variantes se generarán antes de confirmar, para que la
        // decisión de "generar" no sea a ciegas.
        previewVariantes() {
            const grupos = this.gruposValidos();
            if (!grupos.length) return { cantidad: 1, detalle: "" };
            const cantidad = grupos.reduce((acc, g) => acc * g.valores.length, 1);
            const detalle = grupos.map((g) => `${g.valores.length} de ${g.nombre}`).join(" × ");
            return { cantidad, detalle };
        },

        // Compara el catálogo actual de atributos contra lo que ya está reflejado en las
        // variantes existentes. Devuelve solo lo NUEVO.
        detectarCambiosPendientes() {
            const grupos = this.gruposValidos();
            const cambios = [];
            grupos.forEach((grupo) => {
                const valoresUsados = new Set();
                this.combinaciones.forEach((variante) => {
                    variante.valores.forEach((v) => {
                        if (v.atributo.trim().toLowerCase() === grupo.nombre.toLowerCase()) {
                            valoresUsados.add(v.valor.trim().toLowerCase());
                        }
                    });
                });
                const nuevosValores = grupo.valores.filter((valor) => !valoresUsados.has(valor.toLowerCase()));
                if (nuevosValores.length) cambios.push({ atributo: grupo.nombre, nuevosValores });
            });
            return cambios;
        },

        // Cifras concretas para mostrar en el panel de reconciliación, en vez de dejar que el
        // usuario adivine qué significa "aplicar" vs "expandir".
        resumenCambio(cambio) {
            return {
                actuales: this.combinaciones.length,
                totalExpandido: this.combinaciones.length * cambio.nuevosValores.length,
            };
        },

        revisarCombinaciones() {
            const nombres = this.atributos.map((a) => a.nombre.trim().toLowerCase());
            if (this.atributos.length > 0) {
                if (this.atributos.some((a) => !a.nombre.trim() || !this.valoresValidos(a).length)) {
                    this.error = "Completa el nombre y al menos un valor para cada atributo, o elimina los vacíos.";
                    return;
                }
                if (new Set(nombres).size !== nombres.length) {
                    this.error = "No repitas el mismo atributo en este producto.";
                    return;
                }
            }
            this.error = "";

            if (!this.combinaciones.length) {
                this.generarCombinacionesDesdeCero();
                this.paso = 3;
                return;
            }

            this.cambiosPendientes = this.detectarCambiosPendientes();
            if (this.cambiosPendientes.length) return; // panel de reconciliación
            this.paso = 3;
        },

        generarCombinacionesDesdeCero() {
            const grupos = this.gruposValidos();
            if (!grupos.length) {
                this.combinaciones = [this.crearVariante([])];
                this.recalcularDuplicados();
                return;
            }
            const combos = grupos.reduce(
                (resultado, grupo) =>
                    resultado.flatMap((base) => grupo.valores.map((valor) => [...base, { atributo: grupo.nombre, valor }])),
                [[]]
            );
            this.combinaciones = combos.map((valores) => this.crearVariante(valores));
            this.recalcularDuplicados();
        },

        crearVariante(valores, base = null, expandidoDefault = false) {
            return {
                id: base?.id ?? null,
                valores: valores.map((v) => ({ atributo: v.atributo, valor: v.valor })),
                sku: base?.sku ?? "",
                precio_venta: base?.precio_venta ?? "",
                comision: base?.comision ?? (this.comisionGeneral !== "" ? Number(this.comisionGeneral) : 0),
                imagenes: [],
                imagenesEliminadas: [],
                expandido: expandidoDefault,
                manual: base?.manual ?? false,
                _dupSku: false,
                _dupCombinacion: false,
            };
        },

        // ---- Resolución del panel de reconciliación ----
        aplicarValorATodas(cambio) {
            const valor = cambio.nuevosValores[0];
            this.combinaciones.forEach((variante) => {
                const yaExiste = variante.valores.some(
                    (v) => v.atributo.trim().toLowerCase() === cambio.atributo.toLowerCase()
                );
                if (!yaExiste) variante.valores.push({ atributo: cambio.atributo, valor });
            });
            this.quitarCambioPendiente(cambio);
        },

        expandirPorCambio(cambio) {
            const nuevas = [];
            this.combinaciones.forEach((variante) => {
                cambio.nuevosValores.forEach((valor) => {
                    const yaExiste = variante.valores.some(
                        (v) => v.atributo.trim().toLowerCase() === cambio.atributo.toLowerCase()
                    );
                    const valoresBase = yaExiste
                        ? variante.valores
                        : [...variante.valores, { atributo: cambio.atributo, valor }];
                    // Al expandir, la variante original se "divide" en varias: sus imágenes ya
                    // guardadas y su id no se copian (cada hija es una fila nueva en BD), pero
                    // sí se heredan sku/precio/comisión como punto de partida para editar.
                    nuevas.push(this.crearVariante(valoresBase, { ...variante, id: null }, true));
                });
            });
            this.combinaciones = nuevas;
            this.quitarCambioPendiente(cambio);
        },

        quitarCambioPendiente(cambio) {
            this.cambiosPendientes = this.cambiosPendientes.filter((c) => c !== cambio);
            if (!this.cambiosPendientes.length) {
                this.recalcularDuplicados();
                this.paso = 3;
            }
        },

        // ==================== PASO 3: Variantes (flujo con variantes) ====================
        toggleFila(variante) {
            variante.expandido = !variante.expandido;
        },

        toggleTodas() {
            this.expandirTodo = !this.expandirTodo;
            this.combinaciones.forEach((v) => (v.expandido = this.expandirTodo));
        },

        opcionesAtributo() {
            return this.atributos.map((a) => a.nombre.trim()).filter(Boolean);
        },

        opcionesValor(nombreAtributo) {
            const atributo = this.atributos.find(
                (a) => a.nombre.trim().toLowerCase() === (nombreAtributo || "").toLowerCase()
            );
            return atributo ? this.valoresValidos(atributo) : [];
        },

        agregarValorVariante(variante) {
            variante.valores.push({ atributo: "", valor: "" });
        },

        quitarValorVariante(variante, indice) {
            variante.valores.splice(indice, 1);
            this.recalcularDuplicados();
        },

        // Si el usuario elige "+ Nuevo atributo/valor" en una fila, queda registrado en el
        // catálogo global (this.atributos) para que esté disponible como opción en el resto de
        // variantes, pero NO se aplica automáticamente a ninguna otra fila.
        registrarAtributoNuevo(nombre) {
            const limpio = (nombre || "").trim();
            if (!limpio) return;
            const existe = this.atributos.some((a) => a.nombre.trim().toLowerCase() === limpio.toLowerCase());
            if (!existe) this.atributos.push({ nombre: limpio, valores: [] });
        },

        registrarValorNuevo(nombreAtributo, valor) {
            const limpio = (valor || "").trim();
            if (!limpio) return;
            const atributo = this.atributos.find(
                (a) => a.nombre.trim().toLowerCase() === (nombreAtributo || "").trim().toLowerCase()
            );
            if (atributo && !this.valoresValidos(atributo).includes(limpio)) atributo.valores.push(limpio);
        },

        aplicarPrecioMasivo() {
            if (this.precioMasivo === "" || Number(this.precioMasivo) < 0) return;
            const precio = Number(this.precioMasivo);
            this.combinaciones.forEach((v) => (v.precio_venta = precio));
        },

        aplicarComisionMasiva() {
            if (this.comisionMasiva === "" || Number(this.comisionMasiva) < 0) return;
            const comision = Number(this.comisionMasiva);
            this.combinaciones.forEach((v) => (v.comision = comision));
        },

        agregarVarianteManual() {
            const nueva = this.crearVariante([{ atributo: "", valor: "" }], null, true);
            nueva.manual = true;
            this.combinaciones.push(nueva);
        },

        quitarVariante(indice) {
            this.combinaciones.splice(indice, 1);
            this.recalcularDuplicados();
        },

        hashCombinacion(variante) {
            return variante.valores
                .map((v) => `${v.atributo.trim().toLowerCase()}:${v.valor.trim().toLowerCase()}`)
                .sort()
                .join("|");
        },

        recalcularDuplicados() {
            const skusVistos = new Map();
            const hashesVistos = new Map();
            this.combinaciones.forEach((v) => {
                const sku = v.sku.trim().toLowerCase();
                if (sku) skusVistos.set(sku, (skusVistos.get(sku) || 0) + 1);
                const hash = this.hashCombinacion(v);
                hashesVistos.set(hash, (hashesVistos.get(hash) || 0) + 1);
            });
            this.combinaciones.forEach((v) => {
                const sku = v.sku.trim().toLowerCase();
                v._dupSku = sku ? skusVistos.get(sku) > 1 : false;
                v._dupCombinacion = hashesVistos.get(this.hashCombinacion(v)) > 1;
            });
        },

        conteoConflictos() {
            return this.combinaciones.filter((v) => v._dupSku || v._dupCombinacion).length;
        },

        volver(paso) {
            this.error = "";
            this.cambiosPendientes = [];
            this.paso = paso;
        },

        seleccionarImagenes(evento, variante) {
            variante.imagenes = [...evento.target.files];
        },

        quitarImagen(variante, indice) {
            const imagen = variante.imagenes[indice];
            if (imagen?.existing) variante.imagenesEliminadas.push(imagen.id);
            variante.imagenes.splice(indice, 1);
        },

        resumenVariante(variante) {
            return variante.valores.map((v) => `${v.atributo}: ${v.valor}`).join(" · ") || "Sin atributos";
        },

        // ==================== VALIDACIÓN Y GUARDADO (comunes a ambos flujos) ====================
        formularioValido() {
            this.recalcularDuplicados();
            // Un atributo del catálogo del producto es válido si tiene nombre Y al menos un
            // valor. Si algún atributo quedó a medias (por ejemplo el que se agrega solo al
            // entrar por primera vez al editor, y nunca se llenó ni se borró), esto lo bloquea
            // aquí mismo, antes de llegar al backend, sin importar por qué pantallas pasó el
            // usuario para llegar a este punto (incluida la edición, que puede saltarse el
            // Paso 2 por completo).
            const atributosValidos = this.atributos.length === this.gruposValidos().length;
            return (
                atributosValidos &&
                this.nombre.trim() &&
                this.categoriaId &&
                this.combinaciones.length > 0 &&
                this.combinaciones.every(
                    (v) =>
                        v.valores.every((val) => val.atributo.trim() && val.valor.trim()) &&
                        new Set(v.valores.map((val) => val.atributo.trim().toLowerCase())).size === v.valores.length &&
                        v.precio_venta !== "" &&
                        Number(v.precio_venta) >= 0 &&
                        v.comision !== "" &&
                        Number(v.comision) >= 0 &&
                        !v._dupSku &&
                        !v._dupCombinacion
                )
            );
        },

        async guardarProducto() {
            if (!this.formularioValido() || this.guardando) {
                if (this.conteoConflictos() > 0) {
                    this.error = "Hay variantes con SKU repetido o con la misma combinación de atributos (resaltadas). Corrígelas antes de guardar.";
                } else if (this.atributos.length !== this.gruposValidos().length) {
                    this.error = "Hay un atributo sin nombre o sin valores. Complétalo o elimínalo antes de guardar.";
                }
                return;
            }
            this.guardando = true;
            this.error = "";

            const payload = {
                nombre: this.nombre.trim(),
                id_categoria: this.categoriaId,
                descripcion: this.descripcion.trim() || null,
                comision_general: Number(this.comisionGeneral),
                // Se envía la versión ya filtrada (gruposValidos), no this.atributos crudo:
                // así, aunque algo se cuele por cualquier camino que no pasó por el Paso 2,
                // nunca llega vacío al backend.
                atributos: this.gruposValidos().map((g) => ({ nombre: g.nombre, valores: g.valores })),
                variantes: this.combinaciones.map((v) => ({
                    id: /^\d+$/.test(String(v.id || "")) ? Number(v.id) : null,
                    sku: v.sku.trim() || null,
                    precio_venta: Number(v.precio_venta),
                    comision: Number(v.comision),
                    valores: v.valores.map((val) => ({ atributo: val.atributo.trim(), valor: val.valor.trim() })),
                    imagenes_existentes: v.imagenes.filter((imagen) => imagen.existing).map((imagen) => imagen.id),
                })),
            };

            try {
                const urlElem = document.querySelector('meta[name="producto-propuesta-store-url"]');
                if (!urlElem) throw new Error("No se encontró la ruta para guardar.");
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) throw new Error("No se encontró el token de seguridad. Recarga la página.");

                const formData = new FormData();
                formData.append("payload", JSON.stringify(payload));
                formData.append("_token", csrfToken);
                this.combinaciones.forEach((v, i) => v.imagenes.filter((imagen) => !imagen.existing).forEach((img) => formData.append(`imagenes[${i}][]`, img)));

                const response = await fetch(urlElem.content, {
                    method: "POST",
                    headers: { Accept: "application/json", "X-CSRF-TOKEN": csrfToken },
                    body: formData,
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || "Error al procesar la solicitud.");

                this.guardado = true;
                this.paso = 4;
            } catch (e) {
                this.error = e.message;
            } finally {
                this.guardando = false;
            }
        },
    }));
});