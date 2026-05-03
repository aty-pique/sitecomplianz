/**
 * Section « chaos → système » : progression scroll (sticky + piste longue).
 * Pas de blocage du wheel : le défilement naturel pilote une timeline 0→1,
 * lissée par lerp + requestAnimationFrame.
 */
(function () {
    'use strict';

    var root = document.querySelector('[data-home-cs]');
    if (!root) return;

    var section = root.closest('.home-cs');
    var sticky = root.querySelector('.home-cs__sticky');
    var stage = root.querySelector('[data-home-cs-stage]');
    var viz = root.querySelector('[data-home-cs-viz]');
    var bridge = root.querySelector('[data-home-cs-bridge]');
    var chaosSvg = root.querySelector('[data-home-cs-chaos-svg]');
    var sysSvg = root.querySelector('[data-home-cs-sys-svg]');
    var stack = root.querySelector('[data-home-cs-stack]');
    var icons = Array.prototype.slice.call(root.querySelectorAll('.home-cs__icon'));

    /* Sans ces prérequis l’anim ne tourne pas : fallback pour ne pas laisser la pile en opacity:0 */
    if (!stage || !viz || !sysSvg || icons.length === 0 || !stack) {
        if (section) {
            section.classList.add('home-cs--reduced');
            section.style.setProperty('--cs-p', '1');
        }
        if (sticky) sticky.style.setProperty('--cs-p', '1');
        return;
    }

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) {
        section.classList.add('home-cs--reduced');
        if (sticky) sticky.style.setProperty('--cs-p', '1');
        if (section) section.style.setProperty('--cs-p', '1');
        layoutFinalState();
        return;
    }

    /** Positions chaos : dispersion large sur toute la scène (fractions 0–1) */
    var CHAOS_FRAC = [
        [0.04, 0.14], [0.86, 0.18], [0.12, 0.82], [0.92, 0.48],
        [0.36, 0.08], [0.62, 0.88], [0.07, 0.42], [0.52, 0.36]
    ];

    var pairsChaos = [
        [0, 3], [1, 5], [2, 6], [4, 7], [0, 7], [2, 4], [1, 6],
        [0, 4], [3, 6], [2, 7], [1, 4], [5, 6]
    ];

    var NS = 'http://www.w3.org/2000/svg';

    var targetP = 0;
    var smoothP = 0;
    var rafId = 0;
    var chaosPathEl = null;
    var sysPaths = [];
    var lastStageRect = null;
    var layoutRetryCount = 0;

    function clamp01(t) {
        return Math.min(1, Math.max(0, t));
    }

    function smoothstep(t) {
        t = clamp01(t);
        return t * t * (3 - 2 * t);
    }

    function easeMove(p) {
        var t = clamp01((p - 0.18) / 0.55);
        return smoothstep(t);
    }

    function isNarrow() {
        return window.innerWidth < 900;
    }

    function getScrollProgress() {
        var rect = root.getBoundingClientRect();
        var vh = window.innerHeight || 1;
        var total = root.offsetHeight - vh;
        if (total <= 0) return 0;
        var scrolled = -rect.top;
        var t = scrolled / total;
        if (typeof t !== 'number' || !isFinite(t)) return 0;
        return clamp01(t);
    }

    function round2(x) {
        return Math.round(x * 100) / 100;
    }

    /** Rejette NaN / ±Infinity / types non numériques avant écriture dans les paths SVG */
    function isValidPoint() {
        var i;
        for (i = 0; i < arguments.length; i++) {
            if (!Number.isFinite(arguments[i])) return false;
        }
        return true;
    }

    function iconPixelPositions(w, h) {
        var narrow = isNarrow();
        var n = icons.length;
        var chaosLeft = narrow ? 0.05 : 0.05;
        var chaosRight = narrow ? 0.95 : 0.95;
        var chaosTop = narrow ? 0.1 : 0.1;
        var chaosBot = narrow ? 0.9 : 0.9;

        var chaos = icons.map(function (_, i) {
            var fi = CHAOS_FRAC[i % CHAOS_FRAC.length];
            var x = chaosLeft + fi[0] * (chaosRight - chaosLeft);
            var y = chaosTop + fi[1] * (chaosBot - chaosTop);
            return { x: x * w, y: y * h };
        });

        var rowY = narrow ? h * 0.14 : h * 0.11;
        var gap = narrow ? 12 : 18;
        var approxW = narrow ? 56 : 72;
        var span = Math.min(w * 0.96, n * approxW + Math.max(0, n - 1) * gap);
        var left0 = (w - span) / 2;
        var finals = icons.map(function (_, i) {
            return {
                x: left0 + approxW / 2 + i * (approxW + gap),
                y: rowY
            };
        });

        return { chaos: chaos, finals: finals, w: w, h: h };
    }

    function ensureChaosPath() {
        if (!chaosSvg) return;
        if (!chaosPathEl) {
            chaosPathEl = document.createElementNS(NS, 'path');
            chaosSvg.appendChild(chaosPathEl);
        }
    }

    function dashOffsetSysPaths() {
        sysPaths.forEach(function (pth) {
            try {
                var len = pth.getTotalLength();
                pth.style.strokeDasharray = String(len);
                pth.style.strokeDashoffset = String(len);
            } catch (e) {
                /* ignore */
            }
        });
    }

    function ensureSysPaths() {
        if (!sysSvg || !viz || !stack) return;

        var vr = viz.getBoundingClientRect();
        var vw = vr.width;
        var vh = vr.height;
        /* Ne jamais vider le SVG si les dimensions ne sont pas encore valides */
        if (!isValidPoint(vw, vh) || vw < 8 || vh < 8) return;

        sysSvg.setAttribute('viewBox', '0 0 ' + round2(vw) + ' ' + round2(vh));
        sysSvg.setAttribute('preserveAspectRatio', 'none');

        var defs = sysSvg.querySelector('defs');
        var layer = sysSvg.querySelector('[data-home-cs-sys-layer]');
        if (!defs || !layer) {
            while (sysSvg.firstChild) sysSvg.removeChild(sysSvg.firstChild);
            defs = document.createElementNS(NS, 'defs');
            var mk = document.createElementNS(NS, 'marker');
            mk.setAttribute('id', 'home-cs-arw');
            mk.setAttribute('viewBox', '0 0 10 10');
            mk.setAttribute('refX', '9');
            mk.setAttribute('refY', '5');
            mk.setAttribute('markerWidth', '6');
            mk.setAttribute('markerHeight', '6');
            mk.setAttribute('orient', 'auto');
            mk.setAttribute('markerUnits', 'userSpaceOnUse');
            var mkp = document.createElementNS(NS, 'path');
            mkp.setAttribute('d', 'M0 0 L10 5 L0 10 z');
            mkp.setAttribute('fill', '#15803d');
            mk.appendChild(mkp);
            defs.appendChild(mk);
            sysSvg.appendChild(defs);
            layer = document.createElementNS(NS, 'g');
            layer.setAttribute('data-home-cs-sys-layer', '1');
            sysSvg.appendChild(layer);
        } else {
            while (layer.firstChild) layer.removeChild(layer.firstChild);
        }

        sysPaths = [];
        var mkUrl = 'url(#home-cs-arw)';

        function box(el) {
            var r = el.getBoundingClientRect();
            var left = r.left - vr.left;
            var right = r.right - vr.left;
            var top = r.top - vr.top;
            var bottom = r.bottom - vr.top;
            var width = r.width;
            var height = r.height;
            var cx = left + width / 2;
            var cy = top + height / 2;
            if (!isValidPoint(left, right, top, bottom, width, height, cx, cy)) {
                return null;
            }
            return {
                left: left,
                right: right,
                top: top,
                bottom: bottom,
                width: width,
                height: height,
                cx: cx,
                cy: cy
            };
        }

        function appendPathD(d, markerEnd, cls) {
            if (!d || d.indexOf('NaN') !== -1 || d.indexOf('Infinity') !== -1) return;
            var pth = document.createElementNS(NS, 'path');
            pth.setAttribute('d', d);
            if (markerEnd) pth.setAttribute('marker-end', markerEnd);
            if (cls) pth.setAttribute('class', cls);
            layer.appendChild(pth);
            sysPaths.push(pth);
        }

        function addLinePath(markerEnd, cls, x1, y1, x2, y2) {
            if (!isValidPoint(x1, y1, x2, y2)) return;
            appendPathD(
                'M ' + round2(x1) + ' ' + round2(y1) + ' L ' + round2(x2) + ' ' + round2(y2),
                markerEnd,
                cls
            );
        }

        function addBentPath(markerEnd, cls, x1, y1, xm, ym, x2, y2) {
            if (!isValidPoint(x1, y1, xm, ym, x2, y2)) return;
            appendPathD(
                'M ' +
                    round2(x1) +
                    ' ' +
                    round2(y1) +
                    ' L ' +
                    round2(xm) +
                    ' ' +
                    round2(ym) +
                    ' L ' +
                    round2(x2) +
                    ' ' +
                    round2(y2),
                markerEnd,
                cls
            );
        }

        if (icons.length === 0) {
            dashOffsetSysPaths();
            return;
        }

        var iconBoxes = icons
            .map(function (el) {
                return box(el);
            })
            .filter(function (b) {
                return b !== null;
            });
        if (iconBoxes.length === 0) {
            dashOffsetSysPaths();
            return;
        }

        var yBar =
            Math.max.apply(
                null,
                iconBoxes.map(function (b) {
                    return b.bottom;
                })
            ) + 8;
        if (!isValidPoint(yBar)) {
            dashOffsetSysPaths();
            return;
        }

        var xs = iconBoxes.map(function (b) {
            return b.cx;
        });
        var xL = Math.min.apply(null, xs);
        var xR = Math.max.apply(null, xs);
        if (!isValidPoint(xL, xR)) {
            dashOffsetSysPaths();
            return;
        }

        var hubIcons = (xL + xR) / 2;

        iconBoxes.forEach(function (b) {
            addLinePath(null, null, b.cx, b.bottom, b.cx, yBar);
        });
        addLinePath(null, null, xL, yBar, xR, yBar);

        var rows = stack.querySelectorAll('.home-cs__stack-row');
        var outcome = stack.querySelector('.home-cs__stack-outcome');
        if (rows.length === 0) {
            dashOffsetSysPaths();
            return;
        }

        var narrow = isNarrow();
        var sg = box(stack);
        if (!sg) {
            dashOffsetSysPaths();
            return;
        }

        var hubX = sg.cx;
        if (!isValidPoint(hubX)) {
            dashOffsetSysPaths();
            return;
        }

        if (isValidPoint(hubIcons) && Math.abs(hubIcons - hubX) > 3) {
            addLinePath(null, null, hubIcons, yBar, hubX, yBar);
        }

        var firstRowBox = box(rows[0]);
        if (!firstRowBox) {
            dashOffsetSysPaths();
            return;
        }

        addLinePath(mkUrl, null, hubX, yBar, hubX, firstRowBox.top - 6);

        for (var j = 0; j < rows.length - 1; j++) {
            var ra = box(rows[j]);
            var rb = box(rows[j + 1]);
            if (!ra || !rb) continue;
            addLinePath(mkUrl, null, hubX, ra.bottom + 2, hubX, rb.top - 4);
        }

        if (outcome) {
            var rLast = box(rows[rows.length - 1]);
            var ob = box(outcome);
            if (rLast && ob) {
                addLinePath(mkUrl, null, hubX, rLast.bottom + 2, hubX, ob.top - 4);
            }
        }

        if (!narrow) {
            var benefits = viz.querySelectorAll('.home-cs__benefit-card');
            for (var bi = 0; bi < benefits.length; bi++) {
                var bg = box(benefits[bi]);
                var targetRow = rows[Math.min(bi, rows.length - 1)];
                var tBox = box(targetRow);
                if (!bg || !tBox) continue;
                var xStart = bg.left + 2;
                var yMid = bg.cy;
                var xMid = sg.right + (hubX - sg.right) * 0.5;
                var xEnd = sg.right + 4;
                var yEnd = tBox.cy;
                addBentPath(mkUrl, 'home-cs__flow-benefit', xStart, yMid, xMid, yMid, xEnd, yEnd);
            }
        }

        dashOffsetSysPaths();
    }

    function updateChaosSpaghetti(centers, p) {
        ensureChaosPath();
        if (!chaosPathEl) return;

        var fade = clamp01(1 - smoothstep((p - 0.08) / 0.38));
        chaosPathEl.style.opacity = String(fade);
        if (fade < 0.02) return;

        var d = '';
        pairsChaos.forEach(function (pair) {
            var a = pair[0];
            var b = pair[1];
            if (a >= centers.length || b >= centers.length) return;
            if (!centers[a] || !centers[b]) return;
            var ax = centers[a].x;
            var ay = centers[a].y;
            var bx = centers[b].x;
            var by = centers[b].y;
            if (!isValidPoint(ax, ay, bx, by)) return;
            var mx = (ax + bx) / 2 + (a - b) * 8;
            var my = (ay + by) / 2 + Math.sin(a + b) * 14;
            if (!isValidPoint(mx, my)) return;
            d += 'M ' + ax + ' ' + ay + ' Q ' + mx + ' ' + my + ' ' + bx + ' ' + by + ' ';
        });
        chaosPathEl.setAttribute('d', d.trim());
    }

    function updateSysDraw(p) {
        var q = smoothstep((p - 0.48) / 0.48);
        sysPaths.forEach(function (pth) {
            try {
                var len = pth.getTotalLength();
                pth.style.strokeDashoffset = String(len * (1 - q));
            } catch (e) {
                /* ignore */
            }
        });
        if (sysSvg) sysSvg.style.opacity = String(smoothstep((p - 0.42) / 0.15));
    }

    function updateStackVisibility(p) {
        if (!stack) return;
        if (typeof p !== 'number' || !isFinite(p)) return;
        var base = smoothstep((p - 0.58) / 0.32);
        var rows = stack.querySelectorAll('.home-cs__stack-row');
        var outcome = stack.querySelector('.home-cs__stack-outcome');
        rows.forEach(function (row, i) {
            var stagger = clamp01(base - i * 0.06);
            row.style.opacity = String(stagger);
            row.style.transform = 'translateY(' + (1 - stagger) * 12 + 'px)';
        });
        if (outcome) {
            var ob = smoothstep((p - 0.72) / 0.22);
            outcome.style.opacity = String(ob);
            outcome.style.transform = 'translateY(' + (1 - ob) * 10 + 'px)';
        }
    }

    function applyBridge(p) {
        if (!bridge) return;
        var px = (p - 0.5) * 14;
        bridge.style.transform = 'translateY(' + px + 'px)';
    }

    function layoutFinalState() {
        var rect = stage.getBoundingClientRect();
        var w = rect.width;
        var h = rect.height;
        if (!isValidPoint(w, h) || w < 8 || h < 8) return;
        var pos = iconPixelPositions(w, h);
        icons.forEach(function (el, i) {
            var fn = pos.finals[i];
            if (!fn) return;
            var x = Math.round(fn.x);
            var y = Math.round(fn.y);
            if (!isFinite(x) || !isFinite(y)) return;
            el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
        });
        requestAnimationFrame(function () {
            ensureSysPaths();
            updateSysDraw(1);
        });
        updateStackVisibility(1);
        if (chaosPathEl) chaosPathEl.style.opacity = '0';
        if (sysSvg) sysSvg.style.opacity = '1';
    }

    function tick() {
        smoothP += (targetP - smoothP) * 0.09;
        if (!isFinite(smoothP)) smoothP = targetP;
        if (Math.abs(targetP - smoothP) < 0.0004) smoothP = targetP;

        applyProgress(smoothP);

        if (Math.abs(targetP - smoothP) > 0.0015) {
            rafId = requestAnimationFrame(tick);
        } else {
            rafId = 0;
        }
    }

    function scheduleTick() {
        if (rafId) return;
        rafId = requestAnimationFrame(tick);
    }

    function applyProgress(p) {
        if (typeof p !== 'number' || !isFinite(p)) p = 0;
        p = clamp01(p);
        if (section) section.style.setProperty('--cs-p', String(p));
        if (sticky) sticky.style.setProperty('--cs-p', String(p));

        var rect = stage.getBoundingClientRect();
        var w = rect.width;
        var h = rect.height;
        if (!isValidPoint(w, h) || w < 20 || h < 20) {
            if (layoutRetryCount < 12) {
                layoutRetryCount += 1;
                requestAnimationFrame(function () {
                    applyProgress(smoothP);
                });
            }
            return;
        }
        layoutRetryCount = 0;

        var pos = iconPixelPositions(w, h);
        var moveT = easeMove(p);

        icons.forEach(function (el, i) {
            var ch = pos.chaos[i];
            var fn = pos.finals[i];
            var x = Math.round(ch.x + (fn.x - ch.x) * moveT);
            var y = Math.round(ch.y + (fn.y - ch.y) * moveT);
            if (!isFinite(x) || !isFinite(y)) return;
            el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
        });

        if (
            !lastStageRect ||
            lastStageRect.w !== w ||
            lastStageRect.h !== h
        ) {
            lastStageRect = { w: w, h: h };
        }

        ensureSysPaths();

        var centers = icons.map(function (el, i) {
            var ch = pos.chaos[i];
            var fn = pos.finals[i];
            return {
                x: ch.x + (fn.x - ch.x) * moveT,
                y: ch.y + (fn.y - ch.y) * moveT
            };
        });

        updateChaosSpaghetti(centers, p);
        updateSysDraw(p);
        updateStackVisibility(p);
        applyBridge(p);
    }

    function onScroll() {
        targetP = getScrollProgress();
        scheduleTick();
    }

    function onResize() {
        layoutRetryCount = 0;
        lastStageRect = null;
        targetP = getScrollProgress();
        smoothP = targetP;
        applyProgress(smoothP);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize);

    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            onScroll();
            smoothP = targetP;
            applyProgress(smoothP);
        });
    });
})();
