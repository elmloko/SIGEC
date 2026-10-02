<script>
// Utilidades compartidas del Centro de indicadores: graficos (Chart.js), tablas ordenables y exportacion CSV
window.IndGraficos = (function () {
    var c = {
        azul: '#1A549A', azulClaro: '#7FA6D6', amarillo: '#F2BC17', verde: '#2E9E6A',
        ambar: '#E8A33D', rojo: '#D64545', rojoOscuro: '#9E2B2B', gris: '#9AA5B4', tinta: '#3B4659', linea: '#E3E8EF'
    };
    var serie = [c.azul, c.amarillo, c.verde, c.rojo, c.azulClaro, c.ambar, '#7A5BA8', c.gris, '#3C9DB5', c.rojoOscuro];
    var miles = function (n) { return Number(n).toLocaleString('es-BO'); };

    if (window.Chart) {
        Chart.defaults.font.family = '"Roboto", "Helvetica Neue", Arial, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = c.tinta;
        Chart.defaults.borderColor = c.linea;
        Chart.defaults.maintainAspectRatio = false;
        Chart.defaults.plugins.legend.labels.boxWidth = 12;
        Chart.defaults.plugins.legend.labels.boxHeight = 12;
        Chart.defaults.plugins.tooltip.callbacks.label = function (ctx) {
            var v = ctx.parsed && typeof ctx.parsed === 'object' ? (ctx.chart.options.indexAxis === 'y' ? ctx.parsed.x : ctx.parsed.y) : ctx.parsed;
            return ' ' + (ctx.dataset.label || ctx.label) + ': ' + miles(v);
        };
    }

    function crear(id, config) {
        var el = document.getElementById(id);
        if (!el || !window.Chart) { return null; }
        return new Chart(el, config);
    }

    function ejes(horizontal) {
        var valor = {beginAtZero: true, grid: {color: c.linea}, ticks: {precision: 0, callback: function (v) { return miles(v); }}};
        var cat = {grid: {display: false}};
        return horizontal ? {x: valor, y: cat} : {x: cat, y: valor};
    }

    return {
        c: c,
        barras: function (id, etiquetas, datos, o) {
            o = o || {};
            return crear(id, {
                type: 'bar',
                data: {labels: etiquetas, datasets: [{label: o.etiqueta || '', data: datos, backgroundColor: o.colores || c.azul, borderRadius: 4, maxBarThickness: 38}]},
                options: {indexAxis: o.horizontal ? 'y' : 'x', scales: ejes(o.horizontal), plugins: {legend: {display: false}}}
            });
        },
        apiladas: function (id, etiquetas, series) {
            return crear(id, {
                type: 'bar',
                data: {labels: etiquetas, datasets: series.map(function (s, i) {
                    return {label: s.label, data: s.data, backgroundColor: s.color || serie[i % serie.length], maxBarThickness: 38};
                })},
                options: {scales: {x: {stacked: true, grid: {display: false}}, y: Object.assign(ejes(false).y, {stacked: true})},
                    plugins: {legend: {position: 'bottom'}}, interaction: {mode: 'index', intersect: false}}
            });
        },
        barraLinea: function (id, etiquetas, series) {
            return crear(id, {
                type: 'bar',
                data: {labels: etiquetas, datasets: series.map(function (s) {
                    return s.tipo === 'line'
                        ? {type: 'line', label: s.label, data: s.data, borderColor: s.color, backgroundColor: s.color, borderWidth: 2.5, tension: .3, pointRadius: 3, order: 0}
                        : {type: 'bar', label: s.label, data: s.data, backgroundColor: s.color, borderRadius: 4, maxBarThickness: 34, order: 1};
                })},
                options: {scales: ejes(false), plugins: {legend: {position: 'bottom'}}, interaction: {mode: 'index', intersect: false}}
            });
        },
        dona: function (id, etiquetas, datos, colores) {
            return crear(id, {
                type: 'doughnut',
                data: {labels: etiquetas, datasets: [{data: datos, backgroundColor: colores || serie, borderColor: '#fff', borderWidth: 2}]},
                options: {cutout: '62%', plugins: {legend: {position: 'right'}, tooltip: {callbacks: {label: function (ctx) {
                    var total = ctx.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                    return ' ' + ctx.label + ': ' + miles(ctx.parsed) + ' (' + (total ? Math.round(100 * ctx.parsed / total) : 0) + '%)';
                }}}}}
            });
        }
    };
}());

(function () {
    // ordenar al hacer clic en el encabezado
    Array.prototype.forEach.call(document.querySelectorAll('.ind-tabla'), function (tabla) {
        Array.prototype.forEach.call(tabla.tHead.rows[0].cells, function (th, col) {
            th.tabIndex = 0;
            th.setAttribute('role', 'button');
            var ordenar = function () {
                var asc = th.getAttribute('aria-sort') !== 'ascending';
                Array.prototype.forEach.call(tabla.tHead.rows[0].cells, function (o) { o.removeAttribute('aria-sort'); });
                th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');
                var texto = th.getAttribute('data-tipo') === 'texto';
                var filas = Array.prototype.slice.call(tabla.tBodies[0].rows);
                filas.sort(function (a, b) {
                    var x = a.cells[col].getAttribute('data-valor'), y = b.cells[col].getAttribute('data-valor');
                    var r = texto ? String(x).localeCompare(String(y), 'es') : parseFloat(x) - parseFloat(y);
                    return asc ? r : -r;
                });
                filas.forEach(function (f) { tabla.tBodies[0].appendChild(f); });
            };
            th.addEventListener('click', ordenar);
            th.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); ordenar(); } });
        });
    });

    // exportar a CSV (se abre directo en Excel)
    Array.prototype.forEach.call(document.querySelectorAll('[data-exportar]'), function (btn) {
        btn.addEventListener('click', function () {
            var tabla = document.getElementById(btn.getAttribute('data-exportar'));
            if (!tabla) { return; }
            var limpiar = function (t) { return '"' + t.replace(/\s+/g, ' ').trim().replace(/"/g, '""') + '"'; };
            var lineas = Array.prototype.map.call(tabla.rows, function (fila) {
                if (fila.style.display === 'none') { return null; }
                return Array.prototype.map.call(fila.cells, function (celda) { return limpiar(celda.innerText); }).join(';');
            }).filter(Boolean);
            var blob = new Blob(['﻿' + lineas.join('\r\n')], {type: 'text/csv;charset=utf-8'});
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = (btn.getAttribute('data-nombre') || 'reporte') + '.csv';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 0);
        });
    });

    // filtro de texto sobre una tabla
    Array.prototype.forEach.call(document.querySelectorAll('[data-buscar]'), function (input) {
        var tabla = document.getElementById(input.getAttribute('data-buscar'));
        if (!tabla) { return; }
        input.addEventListener('input', function () {
            var q = input.value.toLowerCase().trim();
            Array.prototype.forEach.call(tabla.tBodies[0].rows, function (f) {
                f.style.display = !q || f.innerText.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    });
}());
</script>
