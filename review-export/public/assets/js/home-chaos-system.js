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
        return clamp01(scrolled / total);
    }

    function round2(x) {
        return Math.round(x * 100) / 100;
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
        while (sysSvg.firstChild) sysSvg.removeChild(sysSvg.firstChild);
        sysPaths = [];

        var vr = viz.getBoundingClientRect();
        var vw = vr.width;
        var vh = vr.height;
        if (vw < 8 || vh < 8) return;

        sysSvg.setAttribute('viewBox', '0 0 ' + round2(vw) + ' ' + round2(vh));
        sysSvg.setAttribute('preserveAspectRatio', 'none');

        var defs = document.createElementNS(NS, 'defs');
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
        var mkUrl = 'url(#home-cs-arw)';

        function box(el) {
            var r = el.getBoundingClientRect();
            return {
                left: r.left - vr.left,
                right: r.right - vr.left,
                top: r.top - vr.top,
                bottom: r.bottom - vr.top,
                cx: r.left + r.width / 2 - vr.left,
                cy: r.top + r.height / 2 - vr.top
            };
        }

        function addPath(d, markerEnd, cls) {
            var pth = document.createElementNS(NS, 'path');
            pth.setAttribute('d', d);
            if (markerEnd) pth.setAttribute('marker-end', markerEnd);
            if (cls) pth.setAttribute('class', cls);
            sysSvg.appendChild(pth);
            sysPaths.push(pth);
        }

        if (icons.length === 0) {
            dashOffsetSysPaths();
            return;
        }

        var iconBoxes = icons.map(function (el) {
            return box(el);
        });
        var yBar =
            Math.max.apply(
                null,
                iconBoxes.map(function (b) {
                    return b.bottom;
                })
            ) + 8;
        var xs = iconBoxes.map(function (b) {
            return b.cx;
        });
        var xL = Math.min.apply(null, xs);
        var xR = Math.max.apply(null, xs);
        var hubIcons = (xL + xR) / 2;

        iconBoxes.forEach(function (b) {
            addPath(
                'M ' +
                    round2(b.cx) +
                    ' ' +
                    round2(b.bottom) +
                    ' L ' +
                    round2(b.cx) +
                    ' ' +
                    round2(yBar),
                null,
                null
            );
        });
        addPath('M ' + round2(xL) + ' ' + round2(yBar) + ' L ' + round2(xR) + ' ' + round2(yBar), null, null);

        var rows = stack.querySelectorAll('.home-cs__stack-row');
        var outcome = stack.querySelector('.home-cs__stack-outcome');
        if (rows.length === 0) {
            dashOffsetSysPaths();
            return;
        }

        var narrow = isNarrow();
        var sg = box(stack);
        var hubX = sg.left + sg.width / 2;

        if (Math.abs(hubIcons - hubX) > 3) {
            addPath(
                'M ' + round2(hubIcons) + ' ' + round2(yBar) + ' L ' + round2(hubX) + ' ' + round2(yBar),
                null,
                null
            );
        }

        var firstTop = box(rows[0]).top;
        addPath(
            'M ' + round2(hubX) + ' ' + round2(yBar) + ' L ' + round2(hubX) + ' ' + round2(firstTop - 6),
            mkUrl,
            null
        );

        for (var j = 0; j < rows.length - 1; j++) {
            var ra = box(rows[j]);
            var rb = box(rows[j + 1]);
            addPath(
                'M ' +
                    round2(hubX) +
                    ' ' +
                    round2(ra.bottom + 2) +
                    ' L ' +
                    round2(hubX) +
                    ' ' +
                    round2(rb.top - 4),
                mkUrl,
                null
            );
        }

        if (outcome) {
            var rLast = box(rows[rows.length - 1]);
            var ob = box(outcome);
            addPath(
                'M ' +
                    round2(hubX) +
                    ' ' +
                    round2(rLast.bottom + 2) +
                    ' L ' +
                    round2(hubX) +
                    ' ' +
                    round2(ob.top - 4),
                mkUrl,
                null
            );
        }

        if (!narrow) {
            var benefits = viz.querySelectorAll('.home-cs__benefit-card');
            for (var bi = 0; bi < benefits.length; bi++) {
                var bg = box(benefits[bi]);
                var targetRow = rows[Math.min(bi, rows.length - 1)];
                var tBox = box(targetRow);
                var xStart = bg.left + 2;
                var yMid = bg.cy;
                var xMid = sg.right + (hubX - sg.right) * 0.5;
                var xEnd = sg.right + 4;
                var yEnd = tBox.cy;
                addPath(
                    'M ' +
                        round2(xStart) +
                        ' ' +
                        round2(yMid) +
                        ' L ' +
                        round2(xMid) +
                        ' ' +
                        round2(yMid) +
                        ' L ' +
                        round2(xEnd) +
                        ' ' +
                        round2(yEnd),
                    mkUrl,
                    'home-cs__flow-benefit'
                );
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
            var mx = (ax + bx) / 2 + (a - b) * 8;
            var my = (ay + by) / 2 + Math.sin(a + b) * 14;
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
        var pos = iconPixelPositions(w, h);
        icons.forEach(function (el, i) {
            var fn = pos.finals[i];
            el.style.transform =
                'translate(' + Math.round(fn.x) + 'px,' + Math.round(fn.y) + 'px)';
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
        if (section) section.style.setProperty('--cs-p', String(p));
        if (sticky) sticky.style.setProperty('--cs-p', String(p));

        var rect = stage.getBoundingClientRect();
        var w = rect.width;
        var h = rect.height;
        if (w < 20 || h < 20) return;

        var pos = iconPixelPositions(w, h);
        var moveT = easeMove(p);

        icons.forEach(function (el, i) {
            var ch = pos.chaos[i];
            var fn = pos.finals[i];
            var x = ch.x + (fn.x - ch.x) * moveT;
            var y = ch.y + (fn.y - ch.y) * moveT;
            el.style.transform =
                'translate(' + Math.round(x) + 'px,' + Math.round(y) + 'px)';
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
        lastStageRect = null;
        targetP = getScrollProgress();
        smoothP = targetP;
        applyProgress(smoothP);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize);

    var ro = typeof ResizeObserver !== 'undefined'
        ? new ResizeObserver(function () {
              onResize();
          })
        : null;
    if (ro) {
        ro.observe(stage);
        ro.observe(viz);
    }

    onScroll();
    smoothP = targetP;
    applyProgress(smoothP);
})();
