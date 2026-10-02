/*
 * Descarga en PDF de las paginas del Centro de indicadores.
 * Arma el documento por bloques en el orden en que aparecen en pantalla:
 * KPI y fichas como imagen (html2canvas), graficos desde su propio canvas
 * y tablas como texto con paginacion (jsPDF + autoTable).
 */
(function () {
    var LIBS = [
        '/static/js/libs/jspdf/jspdf.umd.min.js',
        '/static/js/libs/jspdf/jspdf.plugin.autotable.min.js',
        '/static/js/libs/jspdf/html2canvas.min.js'
    ];
    var AZUL = [26, 84, 154], TINTA = [31, 42, 61], SUAVE = [107, 116, 137], LINEA = [220, 227, 236];
    var M = 12; // margen en mm

    function cargarScript(src) {
        return new Promise(function (ok, error) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = ok;
            s.onerror = function () { error(new Error('No se pudo cargar ' + src)); };
            document.head.appendChild(s);
        });
    }

    var librerias = null;
    function cargarLibrerias() {
        if (!librerias) {
            // en orden: autoTable necesita jsPDF ya cargado
            librerias = LIBS.reduce(function (p, src) { return p.then(function () { return cargarScript(src); }); }, Promise.resolve());
        }
        return librerias;
    }

    // las fuentes estandar del PDF no tienen algunos simbolos
    function limpiar(t) {
        return String(t || '').replace(/\s+/g, ' ').trim()
            .replace(/▲/g, '+').replace(/▼/g, '-').replace(/≥/g, '>=').replace(/≤/g, '<=')
            .replace(/→/g, '').replace(/←/g, '').replace(/[–—]/g, '-').replace(/…/g, '...')
            .replace(/[“”«»]/g, '"').replace(/[‘’]/g, "'")
            .replace(/[^\x00-\xFF]/g, '');
    }

    function texto(el, sel) {
        var x = sel ? el.querySelector(sel) : el;
        return x ? limpiar(x.innerText) : '';
    }

    function Documento(meta) {
        var jsPDF = window.jspdf.jsPDF;
        this.doc = new jsPDF({orientation: 'landscape', unit: 'mm', format: 'a4'});
        this.ancho = this.doc.internal.pageSize.getWidth();
        this.alto = this.doc.internal.pageSize.getHeight();
        this.util = this.ancho - 2 * M;
        this.meta = meta;
        this.y = M;
        this.encabezado(true);
    }

    Documento.prototype.encabezado = function (primera) {
        var d = this.doc;
        if (primera) {
            d.setFillColor.apply(d, AZUL);
            d.rect(0, 0, this.ancho, 4, 'F');
            d.setTextColor.apply(d, SUAVE);
            d.setFont('helvetica', 'normal');
            d.setFontSize(9);
            d.text('Correos de Bolivia - SIGEC - Centro de indicadores', M, M + 2);
            d.setTextColor.apply(d, TINTA);
            d.setFont('helvetica', 'bold');
            d.setFontSize(18);
            d.text(limpiar(this.meta.titulo), M, M + 11);
            d.setFont('helvetica', 'normal');
            d.setFontSize(10);
            d.setTextColor.apply(d, SUAVE);
            d.text(limpiar(this.meta.subtitulo), M, M + 17);
            d.setDrawColor.apply(d, LINEA);
            d.line(M, M + 21, this.ancho - M, M + 21);
            this.y = M + 27;
        } else {
            this.y = M;
        }
    };

    Documento.prototype.espacio = function (alto) {
        if (this.y + alto > this.alto - M - 6) {
            this.doc.addPage();
            this.encabezado(false);
        }
    };

    Documento.prototype.seccion = function (t) {
        this.espacio(14);
        var d = this.doc;
        d.setFont('helvetica', 'bold');
        d.setFontSize(11);
        d.setTextColor.apply(d, AZUL);
        d.text(limpiar(t).toUpperCase(), M, this.y + 4);
        this.y += 8;
    };

    Documento.prototype.titulo = function (t, sub) {
        this.espacio(sub ? 16 : 10);
        var d = this.doc;
        d.setFont('helvetica', 'bold');
        d.setFontSize(12);
        d.setTextColor.apply(d, TINTA);
        d.text(limpiar(t), M, this.y + 4);
        this.y += 6;
        if (sub) {
            this.parrafo(sub, 8.5);
        }
        this.y += 1;
    };

    Documento.prototype.parrafo = function (t, tam) {
        var d = this.doc;
        d.setFont('helvetica', 'normal');
        d.setFontSize(tam || 9);
        d.setTextColor.apply(d, SUAVE);
        var lineas = d.splitTextToSize(limpiar(t), this.util);
        var alto = lineas.length * (tam || 9) * 0.42;
        this.espacio(alto + 2);
        d.text(lineas, M, this.y + 3);
        this.y += alto + 2;
    };

    Documento.prototype.imagen = function (data, w, h, x, sinAvance) {
        this.espacio(h);
        this.doc.addImage(data, 'PNG', x === undefined ? M : x, this.y, w, h, undefined, 'FAST');
        if (!sinAvance) {
            this.y += h + 4;
        }
    };

    // captura un bloque HTML (KPI, ficha, ranking) a su ancho proporcional
    Documento.prototype.captura = function (el, ancho) {
        var self = this;
        return html2canvas(el, {scale: 2, backgroundColor: '#ffffff', logging: false}).then(function (c) {
            var w = ancho || self.util;
            var h = w * c.height / c.width;
            if (h > self.alto - 2 * M - 10) { // muy alto: se ajusta a una pagina
                h = self.alto - 2 * M - 10;
                w = h * c.width / c.height;
            }
            self.imagen(c.toDataURL('image/png'), w, h);
        });
    };

    // imagen de un grafico con fondo blanco
    function graficoComoImagen(canvas) {
        var c = document.createElement('canvas');
        c.width = canvas.width;
        c.height = canvas.height;
        var ctx = c.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.drawImage(canvas, 0, 0);
        return {data: c.toDataURL('image/png'), proporcion: canvas.height / canvas.width};
    }

    Documento.prototype.tabla = function (tabla) {
        var self = this;
        var filas = [];
        Array.prototype.forEach.call(tabla.tBodies[0].rows, function (tr) {
            if (tr.style.display === 'none') { return; }
            filas.push(Array.prototype.map.call(tr.cells, function (td) { return limpiar(td.innerText); }));
        });
        var cabecera = Array.prototype.map.call(tabla.tHead.rows[0].cells, function (th) { return limpiar(th.innerText); });
        var numericas = {};
        Array.prototype.forEach.call(tabla.tHead.rows[0].cells, function (th, i) {
            if (th.classList.contains('num')) { numericas[i] = {halign: 'right'}; }
        });
        this.doc.autoTable({
            head: [cabecera],
            body: filas,
            startY: this.y,
            margin: {left: M, right: M, top: M, bottom: M + 6},
            styles: {font: 'helvetica', fontSize: 7.5, cellPadding: 1.6, textColor: TINTA, lineColor: LINEA, lineWidth: 0.1, overflow: 'linebreak'},
            headStyles: {fillColor: [246, 248, 251], textColor: SUAVE, fontStyle: 'bold', fontSize: 7},
            alternateRowStyles: {fillColor: [250, 251, 253]},
            columnStyles: numericas
        });
        this.y = this.doc.lastAutoTable.finalY + 6;
        if (!filas.length) {
            self.parrafo('Sin filas que mostrar.');
        }
    };

    Documento.prototype.pies = function () {
        var d = this.doc, total = d.getNumberOfPages();
        var fecha = new Date().toLocaleString('es-BO');
        for (var i = 1; i <= total; i++) {
            d.setPage(i);
            d.setFont('helvetica', 'normal');
            d.setFontSize(8);
            d.setTextColor.apply(d, SUAVE);
            d.text('Generado el ' + limpiar(fecha), M, this.alto - 6);
            d.text('Página ' + i + ' de ' + total, this.ancho - M, this.alto - 6, {align: 'right'});
        }
    };

    // tarjeta (.ind-card): titulo + grafico, ranking, tabla o mensaje vacio
    Documento.prototype.tarjeta = function (card, ancho, x, sinAvance) {
        var canvas = card.querySelector('canvas');
        var tabla = card.querySelector('table.ind-tabla');
        var ranking = card.querySelector('.ind-ranking');
        var vacio = card.querySelector('.ind-vacio');
        var leyenda = card.querySelector('.ind-leyenda');
        var self = this;
        var t = texto(card, 'h3'), sub = texto(card, 'header p');

        if (canvas) {
            var g = graficoComoImagen(canvas);
            var h = Math.min(ancho * g.proporcion, 95);
            var w = h / g.proporcion;
            var d = this.doc;
            this.espacio(h + 12);
            d.setFont('helvetica', 'bold');
            d.setFontSize(10.5);
            d.setTextColor.apply(d, TINTA);
            d.text(d.splitTextToSize(t, ancho)[0], x, this.y + 4);
            d.setFont('helvetica', 'normal');
            d.setFontSize(8);
            d.setTextColor.apply(d, SUAVE);
            d.text(d.splitTextToSize(sub, ancho)[0] || '', x, this.y + 8.5);
            d.addImage(g.data, 'PNG', x, this.y + 11, w, h, undefined, 'FAST');
            if (!sinAvance) { this.y += h + 16; }
            return Promise.resolve(h + 16);
        }
        this.titulo(t, sub);
        if (tabla) {
            this.tabla(tabla);
            if (leyenda) { this.parrafo(texto(leyenda), 8); }
            return Promise.resolve();
        }
        if (ranking) {
            return this.captura(ranking, Math.min(ancho, this.util));
        }
        if (vacio) {
            this.parrafo(texto(vacio));
        }
        return Promise.resolve();
    };

    // recorre la pagina en orden y agrega cada bloque al PDF
    function construir(raiz, doc) {
        var bloques = Array.prototype.filter.call(raiz.children, function (el) {
            return !el.matches('.ind-tabs, .ind-filtros, .ind-nota, script, .ind-pdf-meta');
        });
        var cadena = Promise.resolve();
        bloques.forEach(function (el) {
            cadena = cadena.then(function () {
                if (el.matches('.ind-seccion')) {
                    doc.seccion(el.childNodes[0].textContent);
                } else if (el.matches('.ind-aviso, .ind-migas')) {
                    doc.parrafo(texto(el));
                } else if (el.matches('.ind-kpis, .ind-perfil')) {
                    return doc.captura(el);
                } else if (el.matches('.ind-card')) {
                    return doc.tarjeta(el, doc.util, M);
                } else if (el.matches('.ind-grid')) {
                    return grilla(el, doc);
                }
            });
        });
        return cadena;
    }

    // tarjetas de media columna con grafico: de a dos por fila
    function grilla(grid, doc) {
        var cards = Array.prototype.slice.call(grid.children);
        var cadena = Promise.resolve();
        var mitad = (doc.util - 8) / 2;
        var i = 0;
        var siguiente = function () {
            if (i >= cards.length) { return Promise.resolve(); }
            var a = cards[i];
            var b = cards[i + 1];
            var anchoCompleto = a.classList.contains('ind-span-2') || !a.querySelector('canvas');
            if (anchoCompleto) {
                i += 1;
                return doc.tarjeta(a, doc.util, M).then(siguiente);
            }
            if (b && !b.classList.contains('ind-span-2') && b.querySelector('canvas')) {
                i += 2;
                // reserva el alto del mas alto de los dos para no partirlos entre paginas
                var alto = function (c) {
                    var cv = c.querySelector('canvas');
                    return Math.min(mitad * cv.height / cv.width, 95) + 16;
                };
                doc.espacio(Math.max(alto(a), alto(b)));
                return Promise.all([doc.tarjeta(a, mitad, M, true), doc.tarjeta(b, mitad, M + mitad + 8, true)]).then(function (h) {
                    doc.y += Math.max(h[0], h[1]);
                }).then(siguiente);
            }
            i += 1;
            return doc.tarjeta(a, mitad, M).then(siguiente);
        };
        return cadena.then(siguiente);
    }

    function nombreArchivo(meta) {
        var base = (meta.archivo || meta.titulo || 'reporte').toLowerCase()
            .normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
        return base + '.pdf';
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('[data-pdf]');
        if (!btn) { return; }
        var raiz = btn.closest('.ind');
        var metaEl = raiz && raiz.querySelector('.ind-pdf-meta');
        if (!raiz || !metaEl) { return; }
        var meta = {titulo: metaEl.getAttribute('data-titulo'), subtitulo: metaEl.getAttribute('data-subtitulo'), archivo: metaEl.getAttribute('data-archivo')};
        var original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Generando PDF…';
        cargarLibrerias().then(function () {
            var doc = new Documento(meta);
            return construir(raiz, doc).then(function () {
                doc.pies();
                doc.doc.save(nombreArchivo(meta));
            });
        }).catch(function (err) {
            window.console && console.error(err);
            alert('No se pudo generar el PDF. Intente de nuevo o use Imprimir > Guardar como PDF.');
        }).then(function () {
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
}());
