(function () {
    'use strict';

    function parseDataAttribute(attribute) {
        if (!attribute) {
            return [];
        }

        try {
            return JSON.parse(attribute);
        } catch (err) {
            console.error('Falha ao interpretar dados do grafico:', err);
            return [];
        }
    }

    function resizeCanvas(canvas) {
        var dpr = window.devicePixelRatio || 1;

        canvas.style.removeProperty('width');
        canvas.style.removeProperty('height');

        var width = Math.max(1, Math.floor(canvas.offsetWidth));
        var height = Math.max(1, Math.floor(canvas.offsetHeight));

        canvas.width = Math.max(1, Math.floor(width * dpr));
        canvas.height = Math.max(1, Math.floor(height * dpr));

        return dpr;
    }

    function truncateText(ctx, text, maxWidth) {
        var value = String(text || '');
        var ellipsis = '...';

        if (ctx.measureText(value).width <= maxWidth) {
            return value;
        }

        while (value.length > 0 && ctx.measureText(value + ellipsis).width > maxWidth) {
            value = value.slice(0, -1);
        }

        return value ? value + ellipsis : ellipsis;
    }

    function renderEmptyChart(ctx, canvasWidth, canvasHeight) {
        ctx.fillStyle = '#94a3b8';
        ctx.font = '14px sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText('Sem dados suficientes para exibir o grafico.', 16, canvasHeight / 2);
    }

    function renderCategoryChart(canvas, data) {
        if (!canvas || !canvas.getContext) {
            return;
        }

        var dpr = resizeCanvas(canvas);
        var ctx = canvas.getContext('2d');
        var canvasWidth = canvas.offsetWidth;
        var canvasHeight = canvas.offsetHeight;

        if (!ctx) {
            return;
        }

        if (ctx.resetTransform) {
            ctx.resetTransform();
        }
        ctx.scale(dpr, dpr);
        ctx.clearRect(0, 0, canvasWidth, canvasHeight);

        if (!Array.isArray(data) || data.length === 0) {
            renderEmptyChart(ctx, canvasWidth, canvasHeight);
            return;
        }

        var maxValue = data.reduce(function (carry, item) {
            return Math.max(carry, Number(item.valor_total || 0));
        }, 0);

        if (maxValue === 0) {
            renderEmptyChart(ctx, canvasWidth, canvasHeight);
            return;
        }

        var topPadding = 30;
        var rightPadding = 22;
        var bottomPadding = canvasWidth < 520 ? 72 : 60;
        var leftPadding = 48;
        var innerWidth = Math.max(1, canvasWidth - leftPadding - rightPadding);
        var innerHeight = Math.max(1, canvasHeight - topPadding - bottomPadding);
        var plotBottom = canvasHeight - bottomPadding;
        var barTotalWidth = innerWidth / data.length;
        var barWidth = Math.max(12, Math.min(160, barTotalWidth * 0.74));
        var labelAngle = -Math.PI / 8;

        drawGrid(ctx, {
            canvasWidth: canvasWidth,
            leftPadding: leftPadding,
            rightPadding: rightPadding,
            plotBottom: plotBottom,
            innerHeight: innerHeight,
            maxValue: maxValue
        });

        data.forEach(function (item, index) {
            var value = Number(item.valor_total || 0);
            var label = item.categoria || 'Sem categoria';
            var barHeight = (value / maxValue) * innerHeight;
            var slotX = leftPadding + index * barTotalWidth;
            var x = slotX + (barTotalWidth - barWidth) / 2;
            var y = plotBottom - barHeight;

            drawBar(ctx, x, y, barWidth, barHeight);
            drawValueLabel(ctx, value, x + barWidth / 2, y);
            drawAxisLabel(ctx, label, x + barWidth / 2, plotBottom + 14, labelAngle, Math.max(48, Math.min(110, barTotalWidth + 22)));
        });

        attachChartHover(canvas, data, {
            leftPadding: leftPadding,
            barTotalWidth: barTotalWidth
        });
    }

    function drawGrid(ctx, layout) {
        var ticks = 5;

        ctx.strokeStyle = 'rgba(148, 163, 184, 0.12)';
        ctx.lineWidth = 1;
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (var t = 0; t <= ticks; t += 1) {
            var value = (layout.maxValue / ticks) * t;
            var y = layout.plotBottom - (value / layout.maxValue) * layout.innerHeight;

            ctx.beginPath();
            ctx.moveTo(layout.leftPadding - 6, y);
            ctx.lineTo(layout.canvasWidth - layout.rightPadding + 6, y);
            ctx.stroke();
            ctx.fillText(Math.round(value).toLocaleString(), layout.leftPadding - 10, y + 1);
        }
    }

    function drawBar(ctx, x, y, width, height) {
        var gradient = ctx.createLinearGradient(x, y, x, y + height);
        var radius = Math.min(8, width * 0.12);

        gradient.addColorStop(0, '#06b6d4');
        gradient.addColorStop(1, '#3b82f6');

        ctx.save();
        ctx.shadowColor = 'rgba(14, 165, 233, 0.12)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetY = 2;
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x, y + height);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawValueLabel(ctx, value, x, y) {
        ctx.fillStyle = '#cbd5e1';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        ctx.fillText(value.toLocaleString(), x, y - 6);
    }

    function drawAxisLabel(ctx, label, x, y, angle, maxWidth) {
        ctx.save();
        ctx.fillStyle = '#94a3b8';
        ctx.font = '12px sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';
        ctx.translate(x, y);
        ctx.rotate(angle);
        ctx.fillText(truncateText(ctx, label, maxWidth), 0, 0);
        ctx.restore();
    }

    function attachChartHover(canvas, data, layout) {
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
                zIndex: 50
            });
            parent.style.position = parent.style.position || 'relative';
            parent.appendChild(tooltip);
        }

        canvas.__chartHoverState = {
            data: data,
            layout: layout,
            tooltip: tooltip
        };

        if (canvas.__chartHoverAttached) {
            return;
        }

        canvas.addEventListener('mousemove', function (event) {
            var state = canvas.__chartHoverState;
            var rect = canvas.getBoundingClientRect();
            var x = event.clientX - rect.left - state.layout.leftPadding;
            var index = Math.floor(x / state.layout.barTotalWidth);

            if (index < 0 || index >= state.data.length) {
                state.tooltip.style.display = 'none';
                canvas.style.cursor = '';
                return;
            }

            var item = state.data[index];
            state.tooltip.innerHTML = '<strong>' + (item.categoria || '') + '</strong><br>' + Number(item.valor_total || 0).toLocaleString();
            state.tooltip.style.left = event.clientX - rect.left + 'px';
            state.tooltip.style.top = event.clientY - rect.top + 'px';
            state.tooltip.style.display = 'block';
            canvas.style.cursor = 'pointer';
        });

        canvas.addEventListener('mouseleave', function () {
            var state = canvas.__chartHoverState;
            if (state && state.tooltip) {
                state.tooltip.style.display = 'none';
            }
            canvas.style.cursor = '';
        });

        canvas.__chartHoverAttached = true;
    }

    function scheduleRender(canvas, data) {
        clearTimeout(canvas.__chartRenderTimer);
        canvas.__chartRenderTimer = setTimeout(function () {
            window.requestAnimationFrame(function () {
                renderCategoryChart(canvas, data);
            });
        }, 80);
    }

    function bindResponsiveRendering(canvas, data) {
        if (window.ResizeObserver) {
            var observer = new ResizeObserver(function () {
                scheduleRender(canvas, data);
            });

            observer.observe(canvas);
            if (canvas.parentElement) {
                observer.observe(canvas.parentElement);
            }
            canvas.__chartResizeObserver = observer;
        }

        window.addEventListener('resize', function () {
            scheduleRender(canvas, data);
        });

        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', function () {
                scheduleRender(canvas, data);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var charts = document.querySelectorAll('canvas[data-chart-type]');

        charts.forEach(function (canvas) {
            var type = canvas.getAttribute('data-chart-type');
            var dataset = parseDataAttribute(canvas.getAttribute('data-chart-values'));

            if (type === 'category-bar') {
                renderCategoryChart(canvas, dataset);
                bindResponsiveRendering(canvas, dataset);
            }
        });
    });

    window.ChartUtils = {
        renderCategoryChart: renderCategoryChart
    };
})();
