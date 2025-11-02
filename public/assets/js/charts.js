(function () {
    'use strict';

    function parseDataAttribute(attribute) {
        if (!attribute) {
            return [];
        }

        try {
            return JSON.parse(attribute);
        } catch (err) {
            console.error('Falha ao interpretar dados do gráfico:', err);
            return [];
        }
    }

    function resizeCanvas(canvas) {
        var dpr = window.devicePixelRatio || 1;
        canvas.width = Math.max(1, Math.floor(canvas.offsetWidth * dpr));
        canvas.height = Math.max(1, Math.floor(canvas.offsetHeight * dpr));
        canvas.style.width = canvas.offsetWidth + 'px';
        canvas.style.height = canvas.offsetHeight + 'px';
        return dpr;
    }

    // quebra e truncamento de rótulos para evitar sobreposição
    function wrapLabelText(ctx, text, maxWidth, maxLines) {
        if (!text) return [];
        var words = String(text).split(/\s+/);
        var lines = [];
        var current = '';
        ctx.save();
        // assume font já configurado no chamador, mas garantimos uma fonte padrão pra medir
        ctx.font = ctx.font || '12px sans-serif';
        for (var i = 0; i < words.length; i++) {
            var word = words[i];
            var test = current ? (current + ' ' + word) : word;
            var width = ctx.measureText(test).width;
            if (width <= maxWidth || !current) {
                current = test;
            } else {
                lines.push(current);
                current = word;
                if (lines.length >= maxLines) break;
            }
        }
        if (current && lines.length < maxLines) lines.push(current);

        // se ainda exceder maxLines, truncar a última linha com elipse
        if (lines.length > maxLines) {
            lines = lines.slice(0, maxLines);
        }
        if (lines.length === maxLines) {
            var lastIdx = lines.length - 1;
            var last = lines[lastIdx];
            var ell = '...';
            while (ctx.measureText(last + ell).width > maxWidth && last.length > 0) {
                last = last.slice(0, -1);
            }
            lines[lastIdx] = last + (last.length ? ell : ell);
        }
        ctx.restore();
        return lines;
    }

    function renderCategoryChart(canvas, data) {
        if (!canvas || !canvas.getContext) {
            return;
        }

        var dpr = resizeCanvas(canvas);

        var ctx = canvas.getContext('2d');
        ctx.resetTransform && ctx.resetTransform();
        ctx.scale(dpr, dpr);
        if (!ctx) {
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!Array.isArray(data) || data.length === 0) {
            ctx.fillStyle = '#94a3b8';
            ctx.font = '14px sans-serif';
            ctx.fillText('Sem dados suficientes para exibir o gráfico.', 16, canvas.height / 2);
            return;
        }

        var maxValue = data.reduce(function (carry, item) {
            return Math.max(carry, item.valor_total || 0);
        }, 0);

        if (maxValue === 0) {
            ctx.fillStyle = '#94a3b8';
            ctx.font = '14px sans-serif';
            ctx.fillText('Sem dados suficientes para exibir o gráfico.', 16, canvas.height / 2);
            return;
        }

    var padding = 40; // space for ticks and labels
    var innerWidth = canvas.offsetWidth - padding * 2;
    var innerHeight = canvas.offsetHeight - padding * 2;
    var gapRatio = 0.2;
    var barTotalWidth = innerWidth / data.length;
    var barWidth = Math.max(12, barTotalWidth * (1 - gapRatio));
    var barGap = Math.max(8, barTotalWidth * gapRatio);

        data.forEach(function (item, index) {
            var value = item.valor_total || 0;
            var label = item.categoria || 'Sem categoria';
            var barHeight = (value / maxValue) * innerHeight;
            // compute x using total slot width (bar + gap)
            var slotX = padding + index * (barWidth + barGap);
            var x = slotX + (barGap / 2);
            var y = canvas.offsetHeight - padding - barHeight;

            // gradient fill
            var grad = ctx.createLinearGradient(x, y, x, y + barHeight);
            grad.addColorStop(0, '#06b6d4');
            grad.addColorStop(1, '#3b82f6');

            // shadow
            ctx.save();
            ctx.shadowColor = 'rgba(14, 165, 233, 0.12)';
            ctx.shadowBlur = 10;
            ctx.shadowOffsetY = 2;

            // rounded rect
            var radius = Math.min(8, barWidth * 0.12);
            var rw = barWidth;
            var rh = barHeight;
            var rx = x;
            var ry = y;
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.moveTo(rx + radius, ry);
            ctx.lineTo(rx + rw - radius, ry);
            ctx.quadraticCurveTo(rx + rw, ry, rx + rw, ry + radius);
            ctx.lineTo(rx + rw, ry + rh);
            ctx.lineTo(rx, ry + rh);
            ctx.lineTo(rx, ry + radius);
            ctx.quadraticCurveTo(rx, ry, rx + radius, ry);
            ctx.closePath();
            ctx.fill();
            ctx.restore();

            // value label on top
            ctx.fillStyle = '#cbd5e1';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(value.toLocaleString(), x + rw / 2, y - 8);

            // x-label: mantém horizontal e com largura limitada à largura da barra
            ctx.save();
            ctx.fillStyle = '#94a3b8';
            ctx.font = '12px sans-serif';
            // limitar o texto à largura da barra (sem extrapolar visualmente)
            var maxLabelWidth = Math.max(8, barWidth - 2);
            var lines = wrapLabelText(ctx, label, maxLabelWidth, 2);
            var labelX = x + rw / 2;
            // posiciona logo abaixo da área do gráfico (leva em conta padding)
            var labelTop = canvas.offsetHeight - padding + 4;
            var lineHeight = 14;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            for (var li = 0; li < lines.length; li++) {
                ctx.fillText(lines[li], labelX, labelTop + li * lineHeight);
            }
            ctx.restore();
        });

        // draw horizontal grid lines and ticks
        ctx.strokeStyle = 'rgba(148,163,184,0.12)';
        ctx.lineWidth = 1;
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px sans-serif';
        var ticks = 5;
        for (var t = 0; t <= ticks; t++) {
            var v = (maxValue / ticks) * t;
            var gy = canvas.offsetHeight - padding - (v / maxValue) * innerHeight;
            ctx.beginPath();
            ctx.moveTo(padding - 6, gy);
            ctx.lineTo(canvas.offsetWidth - padding + 6, gy);
            ctx.stroke();
            ctx.textAlign = 'right';
            ctx.fillText(Math.round(v).toLocaleString(), padding - 10, gy + 4);
        }

        // attach tooltip
        attachChartHover(canvas, data, padding, barWidth, barGap, innerHeight, maxValue);
    }

    function attachChartHover(canvas, data, padding, barWidth, barGap, innerHeight, maxValue) {
        // create tooltip element
        var parent = canvas.parentElement || document.body;
        var tooltip = parent.querySelector('.chart-tooltip');
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.className = 'chart-tooltip';
            Object.assign(tooltip.style, {
                position: 'absolute',
                padding: '6px 8px',
                background: 'rgba(15,23,42,0.95)',
                color: '#e2e8f0',
                borderRadius: '6px',
                fontSize: '12px',
                pointerEvents: 'none',
                transform: 'translate(-50%, -120%)',
                display: 'none',
                zIndex: 50,
            });
            parent.style.position = parent.style.position || 'relative';
            parent.appendChild(tooltip);
        }

        function findIndexFromEvent(e) {
            var rect = canvas.getBoundingClientRect();
            var x = e.clientX - rect.left - padding;
            var slot = barWidth + barGap;
            var idx = Math.floor(x / slot);
            if (idx < 0 || idx >= data.length) return -1;
            return idx;
        }

        canvas.addEventListener('mousemove', function (e) {
            var idx = findIndexFromEvent(e);
            if (idx === -1) {
                tooltip.style.display = 'none';
                canvas.style.cursor = '';
                return;
            }
            var item = data[idx];
            tooltip.innerHTML = '<strong>' + (item.categoria || '') + '</strong><br>' + (item.valor_total || 0).toLocaleString();
            tooltip.style.left = e.clientX - canvas.getBoundingClientRect().left + 'px';
            tooltip.style.top = e.clientY - canvas.getBoundingClientRect().top + 'px';
            tooltip.style.display = 'block';
            canvas.style.cursor = 'pointer';
        });

        canvas.addEventListener('mouseleave', function () {
            tooltip.style.display = 'none';
            canvas.style.cursor = '';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var charts = document.querySelectorAll('canvas[data-chart-type]');
        charts.forEach(function (canvas) {
            var type = canvas.getAttribute('data-chart-type');
            var dataset = parseDataAttribute(canvas.getAttribute('data-chart-values'));

            if (type === 'category-bar') {
                renderCategoryChart(canvas, dataset);
            }
        });
    });

    window.ChartUtils = {
        renderCategoryChart: renderCategoryChart
    };
})();
