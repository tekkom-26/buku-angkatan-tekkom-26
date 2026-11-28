import {
    forceCenter,
    forceCollide,
    forceLink,
    forceManyBody,
    forceSimulation,
    forceX,
    forceY,
} from 'd3-force';

const ASPECT_AXES = [
    ['E', 'I'],
    ['N', 'S'],
    ['T', 'F'],
    ['J', 'P'],
];

const ASPECT_ANGLES = {
    N: -Math.PI / 2,
    T: -Math.PI / 4,
    J: 0,
    I: Math.PI / 4,
    S: Math.PI / 2,
    F: (3 * Math.PI) / 4,
    P: Math.PI,
    E: (-3 * Math.PI) / 4,
};

function parseHexToRgb(hex) {
    const clean = (hex || '').trim().replace('#', '');
    if (clean.length === 3) {
        return {
            r: parseInt(clean[0] + clean[0], 16),
            g: parseInt(clean[1] + clean[1], 16),
            b: parseInt(clean[2] + clean[2], 16),
        };
    }
    if (clean.length === 6) {
        return {
            r: parseInt(clean.slice(0, 2), 16),
            g: parseInt(clean.slice(2, 4), 16),
            b: parseInt(clean.slice(4, 6), 16),
        };
    }
    return null;
}

function rgba(colorValue, alpha) {
    const rgb = parseHexToRgb(colorValue);
    if (!rgb) {
        return colorValue;
    }
    return `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha})`;
}

function readThemePalette() {
    const styles = getComputedStyle(document.documentElement);
    const get = (varName, fallback) => styles.getPropertyValue(varName).trim() || fallback;

    return {
        canvasBase: get('--color-canvas-base', '#141414'),
        surfaceCard: get('--color-surface-card', '#252525'),
        surfaceElevated: get('--color-surface-elevated', '#2D2D2D'),
        gold: get('--color-brand-gold', '#D4AF37'),
        goldGlow: get('--color-brand-gold-glow', '#F5D061'),
        red: get('--color-brand-red', '#690F1D'),
        redHover: get('--color-brand-red-hover', '#8A1527'),
        textPrimary: get('--color-text-primary', '#F4F4F5'),
        textSecondary: get('--color-text-secondary', '#C4C4CC'),
        textMuted: get('--color-text-muted', '#9E9EA9'),
        fontDisplay: get('--font-display', 'Space Grotesk, ui-sans-serif, system-ui, sans-serif'),
        fontBody: get('--font-body', 'Inter, ui-sans-serif, system-ui, sans-serif'),
        fontMono: get('--font-mono', 'JetBrains Mono, ui-monospace, monospace'),
    };
}

export function normalizeAspectList(aspects) {
    const byAxis = new Map();
    for (const raw of aspects || []) {
        const letter = String(raw || '').trim().toUpperCase();
        ASPECT_AXES.forEach((pair, axisIndex) => {
            if (pair.includes(letter)) {
                byAxis.set(axisIndex, letter);
            }
        });
    }
    return [...byAxis.entries()]
        .sort((a, b) => a[0] - b[0])
        .map(([, letter]) => letter);
}

export function toggleAspectInArray(currentAspects, aspect) {
    const letter = String(aspect || '').trim().toUpperCase();
    const axisIndex = ASPECT_AXES.findIndex((pair) => pair.includes(letter));
    if (axisIndex === -1) {
        return normalizeAspectList(currentAspects);
    }
    const normalized = normalizeAspectList(currentAspects);
    if (normalized.includes(letter)) {
        return normalized.filter((item) => item !== letter);
    }
    const pair = ASPECT_AXES[axisIndex];
    return normalizeAspectList([
        ...normalized.filter((item) => !pair.includes(item)),
        letter,
    ]);
}

export function createMbtiObsidianGraph(canvas, payload, callbacks = {}) {
    if (!canvas) {
        return null;
    }

    const ctx = canvas.getContext('2d');
    const palette = readThemePalette();

    const state = {
        activeAspects: normalizeAspectList(payload.activeAspects || []),
        searchQuery: '',
        repelStrength: 135,
        linkDistance: 145,
        showLinks: true,
        showStudentLabels: false,
        hoveredNode: null,
        selectedStudentNode: null,
        draggedNode: null,
        isPanning: false,
        activePointers: new Map(),
        pinchState: null,
        wasMultiTouch: false,
        pointerDownPos: null,
        lastPointerPos: null,
        pointerMovedDist: 0,
        panX: 0,
        panY: 0,
        scale: 1,
        hasInitializedView: false,
        width: 800,
        height: 600,
        dpr: Math.min(window.devicePixelRatio || 1, 2),
        destroyed: false,
        rafId: null,
    };

    const nodes = [];
    const links = [];
    const nodeById = new Map();
    const neighbors = new Map();

    const addNeighbor = (a, b) => {
        if (!neighbors.has(a)) neighbors.set(a, new Set());
        if (!neighbors.has(b)) neighbors.set(b, new Set());
        neighbors.get(a).add(b);
        neighbors.get(b).add(a);
    };

    // 1. Center TEKKOM 26 Hub
    const centerNode = {
        id: 'center:tekkom26',
        kind: 'center',
        label: payload.center?.label || 'TEKKOM 26',
        radius: 26,
        x: 0,
        y: 0,
    };
    nodes.push(centerNode);
    nodeById.set(centerNode.id, centerNode);

    // 2. 8 Aspect Hubs
    const aspectEntries = Object.values(payload.aspectNodes || {});
    for (const aspect of aspectEntries) {
        const angle = ASPECT_ANGLES[aspect.letter] ?? 0;
        const dist = 165;
        const node = {
            id: `aspect:${aspect.letter}`,
            kind: 'aspect',
            letter: aspect.letter,
            label: aspect.label,
            axis: aspect.axis,
            baseCount: aspect.count ?? 0,
            radius: 17,
            x: Math.cos(angle) * dist,
            y: Math.sin(angle) * dist,
        };
        nodes.push(node);
        nodeById.set(node.id, node);

        links.push({
            id: `hub:${aspect.letter}`,
            source: centerNode.id,
            target: node.id,
            kind: 'hub',
            aspectLetter: aspect.letter,
        });
        addNeighbor(centerNode.id, node.id);
    }

    // 3. Student Nodes & 4 Aspect Connections Each
    const studentList = payload.studentNodes || [];
    studentList.forEach((student, idx) => {
        let sx = 0;
        let sy = 0;
        for (const letter of student.aspects || []) {
            const ang = ASPECT_ANGLES[letter] ?? 0;
            sx += Math.cos(ang);
            sy += Math.sin(ang);
        }
        const meanAngle = Math.atan2(sy, sx) + ((idx % 7) - 3) * 0.08;
        const spread = 240 + (idx % 5) * 28;

        const node = {
            id: `student:${student.nim}`,
            kind: 'student',
            nim: student.nim,
            name: student.name,
            nickname: student.nickname,
            class_name: student.class_name,
            mbti_code: student.mbti_code,
            mbti_title: student.mbti_title,
            temperament_code: student.temperament_code,
            temperament_name: student.temperament_name,
            aspects: student.aspects || [],
            pcts: student.pcts || {},
            avatar_url: student.avatar_url || null,
            url: student.url,
            radius: 5.5,
            x: Math.cos(meanAngle) * spread,
            y: Math.sin(meanAngle) * spread,
        };
        nodes.push(node);
        nodeById.set(node.id, node);

        for (const letter of node.aspects) {
            const targetId = `aspect:${letter}`;
            if (nodeById.has(targetId)) {
                links.push({
                    id: `edge:${student.nim}:${letter}`,
                    source: node.id,
                    target: targetId,
                    kind: 'student',
                    aspectLetter: letter,
                    studentNim: student.nim,
                });
                addNeighbor(node.id, targetId);
            }
        }
    });

    const linkForce = forceLink(links)
        .id((d) => d.id)
        .distance((l) => (l.kind === 'hub' ? state.linkDistance * 1.15 : state.linkDistance))
        .strength((l) => (l.kind === 'hub' ? 0.85 : 0.24));

    const manyBodyForce = forceManyBody()
        .strength((d) => {
            if (d.kind === 'center') return -state.repelStrength * 3.4;
            if (d.kind === 'aspect') return -state.repelStrength * 2.2;
            return -state.repelStrength;
        });

    const collideForce = forceCollide()
        .radius((d) => {
            if (d.kind === 'center') return d.radius + 22;
            if (d.kind === 'aspect') return d.radius + 14;
            return d.radius + 5;
        })
        .iterations(2);

    const simulation = forceSimulation(nodes)
        .force('link', linkForce)
        .force('charge', manyBodyForce)
        .force('collide', collideForce)
        .force('center', forceCenter(0, 0).strength(0.06))
        .force('x', forceX(0).strength(0.035))
        .force('y', forceY(0).strength(0.035))
        .alphaDecay(0.022)
        .velocityDecay(0.36);

    function getDefaultView(width) {
        if (width < 640) {
            return { panX: 0, panY: 26, scale: 0.48 };
        }
        return { panX: 0, panY: 0, scale: 1 };
    }

    function resize() {
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            return;
        }
        state.dpr = Math.min(window.devicePixelRatio || 1, 2);
        state.width = rect.width;
        state.height = rect.height;
        canvas.width = Math.round(rect.width * state.dpr);
        canvas.height = Math.round(rect.height * state.dpr);

        if (!state.hasInitializedView) {
            const view = getDefaultView(rect.width);
            state.panX = view.panX;
            state.panY = view.panY;
            state.scale = view.scale;
            state.hasInitializedView = true;
        }
    }

    function screenToWorld(sx, sy) {
        return {
            x: (sx - state.width / 2 - state.panX) / state.scale,
            y: (sy - state.height / 2 - state.panY) / state.scale,
        };
    }

    function findNodeAt(screenX, screenY) {
        const world = screenToWorld(screenX, screenY);
        const pad = Math.max(8, 12 / state.scale);

        // Prefer smaller foreground student/aspect nodes when overlapping
        for (let i = nodes.length - 1; i >= 0; i--) {
            const node = nodes[i];
            const dx = world.x - node.x;
            const dy = world.y - node.y;
            const hitRadius = node.radius + (node.kind === 'student' ? pad : 6);
            if (dx * dx + dy * dy <= hitRadius * hitRadius) {
                return node;
            }
        }
        return null;
    }

    function doesStudentMatchFilter(studentNode) {
        if (state.activeAspects.length > 0) {
            for (const needed of state.activeAspects) {
                if (!studentNode.aspects.includes(needed)) {
                    return false;
                }
            }
        }
        const q = state.searchQuery.trim().toLowerCase();
        if (q !== '') {
            const matchesText =
                studentNode.name.toLowerCase().includes(q) ||
                (studentNode.nickname || '').toLowerCase().includes(q) ||
                studentNode.nim.toLowerCase().includes(q) ||
                (studentNode.mbti_code || '').toLowerCase().includes(q);
            if (!matchesText) {
                return false;
            }
        }
        return true;
    }

    function hasActiveFilterOrSearch() {
        return state.activeAspects.length > 0 || state.searchQuery.trim() !== '';
    }

    function computeDynamicAspectCounts() {
        const counts = {};
        for (const aspectNode of nodes) {
            if (aspectNode.kind !== 'aspect') continue;
            const preview = state.activeAspects.includes(aspectNode.letter)
                ? state.activeAspects
                : toggleAspectInArray(state.activeAspects, aspectNode.letter);

            let matched = 0;
            const q = state.searchQuery.trim().toLowerCase();
            for (const node of nodes) {
                if (node.kind !== 'student') continue;
                let ok = true;
                for (const needed of preview) {
                    if (!node.aspects.includes(needed)) {
                        ok = false;
                        break;
                    }
                }
                if (ok && q !== '') {
                    ok =
                        node.name.toLowerCase().includes(q) ||
                        (node.nickname || '').toLowerCase().includes(q) ||
                        node.nim.toLowerCase().includes(q) ||
                        (node.mbti_code || '').toLowerCase().includes(q);
                }
                if (ok) matched++;
            }
            counts[aspectNode.letter] = matched;
        }
        return counts;
    }

    function emitStats() {
        let matchedCount = 0;
        for (const node of nodes) {
            if (node.kind === 'student' && doesStudentMatchFilter(node)) {
                matchedCount++;
            }
        }
        if (typeof callbacks.onStatsChange === 'function') {
            callbacks.onStatsChange({
                matchedCount,
                aspectCounts: computeDynamicAspectCounts(),
            });
        }
    }

    function draw() {
        if (state.destroyed) return;

        ctx.save();
        ctx.scale(state.dpr, state.dpr);
        ctx.clearRect(0, 0, state.width, state.height);

        ctx.translate(state.width / 2 + state.panX, state.height / 2 + state.panY);
        ctx.scale(state.scale, state.scale);

        const focusNode = state.hoveredNode || state.selectedStudentNode;
        const focusNeighbors = focusNode ? neighbors.get(focusNode.id) || new Set() : null;
        const filtering = hasActiveFilterOrSearch();
        // Hovering a student keeps the rest of the graph lit and highlights only the node, links, and label.
        const dimUnfocused = Boolean(
            focusNode && focusNode.kind !== 'student'
        ) || Boolean(!state.hoveredNode && state.selectedStudentNode);

        // Cache student matches once per frame
        const studentMatchedMap = new Map();
        for (const node of nodes) {
            if (node.kind === 'student') {
                studentMatchedMap.set(node.id, doesStudentMatchFilter(node));
            }
        }

        // 1. Draw Edges
        for (const link of links) {
            const s = link.source;
            const t = link.target;
            const isFocusedEdge =
                focusNode && (s.id === focusNode.id || t.id === focusNode.id);

            let strokeColor = rgba(palette.gold, 0.11);
            let lineWidth = 0.85 / Math.sqrt(state.scale);

            if (isFocusedEdge) {
                strokeColor = rgba(palette.goldGlow, 0.9);
                lineWidth = 1.7 / Math.sqrt(state.scale);
            } else if (dimUnfocused) {
                if (!state.showLinks) continue;
                strokeColor = rgba(palette.textMuted, 0.035);
                lineWidth = 0.6 / Math.sqrt(state.scale);
            } else if (filtering) {
                if (link.kind === 'hub') {
                    const isHubActive = state.activeAspects.includes(link.aspectLetter);
                    strokeColor = isHubActive
                        ? rgba(palette.gold, 0.55)
                        : rgba(palette.textMuted, 0.06);
                    lineWidth = isHubActive ? 1.4 / Math.sqrt(state.scale) : 0.7 / Math.sqrt(state.scale);
                } else {
                    const studentOk = studentMatchedMap.get(s.id);
                    const touchesActiveAspect =
                        state.activeAspects.length === 0 ||
                        state.activeAspects.includes(link.aspectLetter);

                    if (studentOk && touchesActiveAspect) {
                        strokeColor = rgba(palette.gold, 0.45);
                        lineWidth = 1.25 / Math.sqrt(state.scale);
                    } else if (studentOk) {
                        strokeColor = rgba(palette.gold, 0.16);
                    } else {
                        if (!state.showLinks) continue;
                        strokeColor = rgba(palette.textMuted, 0.03);
                    }
                }
            } else {
                if (!state.showLinks) continue;
                if (link.kind === 'hub') {
                    strokeColor = rgba(palette.gold, 0.28);
                    lineWidth = 1.2 / Math.sqrt(state.scale);
                } else {
                    // Idle state: soft dimmed lines ("redup halus")
                    strokeColor = rgba(palette.gold, 0.11);
                }
            }

            ctx.beginPath();
            ctx.moveTo(s.x, s.y);
            ctx.lineTo(t.x, t.y);
            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = lineWidth;
            ctx.stroke();
        }

        // 2. Draw Student Nodes
        for (const node of nodes) {
            if (node.kind !== 'student') continue;

            const isMatched = studentMatchedMap.get(node.id);
            const isFocusTarget = focusNode && focusNode.id === node.id;
            const isFocusNeighbor = focusNode && focusNeighbors?.has(node.id);

            let alpha = 0.65;
            let fillColor = palette.textSecondary;
            let strokeColor = palette.canvasBase;
            let drawRadius = node.radius;

            if (isFocusTarget) {
                alpha = 1;
                fillColor = palette.goldGlow;
                strokeColor = palette.redHover;
                drawRadius = node.radius + 3;
            } else if (isFocusNeighbor && dimUnfocused) {
                alpha = 0.95;
                fillColor = palette.gold;
                drawRadius = node.radius + 1;
            } else if (dimUnfocused) {
                alpha = 0.12;
                fillColor = palette.textMuted;
            } else if (filtering) {
                if (isMatched) {
                    alpha = 0.96;
                    fillColor = palette.gold;
                    drawRadius = node.radius + 1.4;
                } else {
                    alpha = 0.14;
                    fillColor = palette.textMuted;
                }
            }

            ctx.save();
            ctx.globalAlpha = alpha;

            if (isFocusTarget || (filtering && isMatched && !focusNode)) {
                ctx.beginPath();
                ctx.arc(node.x, node.y, drawRadius + 4.5, 0, Math.PI * 2);
                ctx.fillStyle = rgba(palette.goldGlow, isFocusTarget ? 0.24 : 0.14);
                ctx.fill();
            }

            ctx.beginPath();
            ctx.arc(node.x, node.y, drawRadius, 0, Math.PI * 2);
            ctx.fillStyle = fillColor;
            ctx.fill();
            ctx.lineWidth = (isFocusTarget ? 1.8 : 0.9) / Math.sqrt(state.scale);
            ctx.strokeStyle = strokeColor;
            ctx.stroke();

            // Regular student label when aspect-focused, searched, zoomed in, or toggled on (focused student label renders on top pass)
            const shouldDrawLabel =
                !isFocusTarget &&
                ((isFocusNeighbor && focusNode?.kind === 'aspect') ||
                    (state.searchQuery.trim() !== '' && isMatched) ||
                    ((state.showStudentLabels || state.scale >= 1.55) && (!filtering || isMatched)));

            if (shouldDrawLabel) {
                const fontSize = Math.max(8, Math.min(13, 10.5 / Math.sqrt(state.scale)));
                ctx.font = `500 ${fontSize}px ${palette.fontBody}`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillStyle = palette.textPrimary;
                ctx.fillText(node.nickname || node.name, node.x, node.y + drawRadius + 4);
            }

            ctx.restore();
        }

        // 3. Draw 8 Aspect Hub Nodes
        for (const node of nodes) {
            if (node.kind !== 'aspect') continue;

            const isActive = state.activeAspects.includes(node.letter);
            const isFocused = focusNode && (focusNode.id === node.id || focusNeighbors?.has(node.id));

            let alpha = 1;
            if (dimUnfocused && !isFocused && !isActive) {
                alpha = 0.25;
            }

            ctx.save();
            ctx.globalAlpha = alpha;

            if (isActive || isFocused) {
                ctx.beginPath();
                ctx.arc(node.x, node.y, node.radius + 6, 0, Math.PI * 2);
                ctx.fillStyle = rgba(isActive ? palette.gold : palette.goldGlow, 0.22);
                ctx.fill();
            }

            ctx.beginPath();
            ctx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
            ctx.fillStyle = isActive ? palette.red : palette.surfaceElevated;
            ctx.fill();
            ctx.lineWidth = (isActive || isFocused ? 2.2 : 1.4) / Math.sqrt(state.scale);
            ctx.strokeStyle = isActive || isFocused ? palette.goldGlow : rgba(palette.gold, 0.55);
            ctx.stroke();

            ctx.font = `700 13px ${palette.fontDisplay}`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = isActive ? palette.textPrimary : palette.gold;
            ctx.fillText(node.letter, node.x, node.y - 1);

            ctx.font = `500 9px ${palette.fontMono}`;
            ctx.textBaseline = 'top';
            ctx.fillStyle = isActive || isFocused ? palette.goldGlow : palette.textSecondary;
            ctx.fillText(node.label, node.x, node.y + node.radius + 4);

            ctx.restore();
        }

        // 4. Draw Center Hub (TEKKOM 26)
        ctx.save();
        if (dimUnfocused && focusNode.id !== centerNode.id && !focusNeighbors?.has(centerNode.id)) {
            ctx.globalAlpha = 0.38;
        }
        ctx.beginPath();
        ctx.arc(centerNode.x, centerNode.y, centerNode.radius + 5, 0, Math.PI * 2);
        ctx.fillStyle = rgba(palette.gold, 0.12);
        ctx.fill();

        ctx.beginPath();
        ctx.arc(centerNode.x, centerNode.y, centerNode.radius, 0, Math.PI * 2);
        ctx.fillStyle = palette.red;
        ctx.fill();
        ctx.lineWidth = 2;
        ctx.strokeStyle = palette.gold;
        ctx.stroke();

        ctx.font = `700 10px ${palette.fontDisplay}`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = palette.textPrimary;
        ctx.fillText('TEKKOM', centerNode.x, centerNode.y - 5);
        ctx.fillStyle = palette.gold;
        ctx.font = `700 11px ${palette.fontMono}`;
        ctx.fillText('26', centerNode.x, centerNode.y + 8);
        ctx.restore();

        // 5. Topmost Pass: Highlighted Student Label Pill (always legible without darkening the graph)
        if (focusNode && focusNode.kind === 'student') {
            ctx.save();
            const invScale = 1 / Math.sqrt(state.scale);
            const fontSize = Math.max(10, Math.min(14, 12 * invScale));
            const labelText = `${focusNode.nickname || focusNode.name} · ${focusNode.mbti_code}`;
            ctx.font = `700 ${fontSize}px ${palette.fontDisplay}`;
            const textMetrics = ctx.measureText(labelText);
            const padX = 8 * invScale;
            const padY = 4 * invScale;
            const boxW = textMetrics.width + padX * 2;
            const boxH = fontSize + padY * 2;
            const boxX = focusNode.x - boxW / 2;
            const boxY = focusNode.y + focusNode.radius + 8 * invScale;
            const cornerR = 6 * invScale;

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(boxX, boxY, boxW, boxH, cornerR);
            } else {
                ctx.rect(boxX, boxY, boxW, boxH);
            }
            ctx.fillStyle = rgba(palette.surfaceCard, 0.94);
            ctx.fill();
            ctx.lineWidth = 1.2 * invScale;
            ctx.strokeStyle = palette.goldGlow;
            ctx.stroke();

            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = palette.goldGlow;
            ctx.fillText(labelText, focusNode.x, boxY + boxH / 2);
            ctx.restore();
        }

        ctx.restore();
    }

    function renderLoop() {
        if (state.destroyed) return;
        draw();
        state.rafId = requestAnimationFrame(renderLoop);
    }

    // Pointer Events (Pan, Zoom, Node Drag, Click vs Drag detection)
    function getLocalCoords(clientX, clientY) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: clientX - rect.left,
            y: clientY - rect.top,
        };
    }

    function getPinchMetrics() {
        const points = [...state.activePointers.values()];
        if (points.length < 2) {
            return null;
        }
        const [p1, p2] = points;
        return {
            dist: Math.max(10, Math.hypot(p2.x - p1.x, p2.y - p1.y)),
            midX: (p1.x + p2.x) / 2,
            midY: (p1.y + p2.y) / 2,
        };
    }

    function onPointerDown(e) {
        const pos = getLocalCoords(e.clientX, e.clientY);
        state.activePointers.set(e.pointerId, pos);

        if (canvas.setPointerCapture && e.pointerId !== undefined) {
            try {
                canvas.setPointerCapture(e.pointerId);
            } catch (_) {
                // Ignore synthetic events
            }
        }

        if (state.activePointers.size >= 2) {
            state.wasMultiTouch = true;
            if (state.draggedNode) {
                state.draggedNode.fx = null;
                state.draggedNode.fy = null;
                state.draggedNode = null;
                simulation.alphaTarget(0);
            }
            state.isPanning = false;
            state.pinchState = getPinchMetrics();
            canvas.style.cursor = 'grabbing';
            return;
        }

        state.wasMultiTouch = false;
        state.pointerDownPos = pos;
        state.lastPointerPos = pos;
        state.pointerMovedDist = 0;

        const hit = findNodeAt(pos.x, pos.y);
        if (hit) {
            state.draggedNode = hit;
            hit.fx = hit.x;
            hit.fy = hit.y;
            simulation.alphaTarget(0.28).restart();
            canvas.style.cursor = 'grabbing';
        } else {
            state.isPanning = true;
            canvas.style.cursor = 'grabbing';
        }
    }

    function onPointerMove(e) {
        const pos = getLocalCoords(e.clientX, e.clientY);
        if (state.activePointers.has(e.pointerId)) {
            state.activePointers.set(e.pointerId, pos);
        }

        if (state.activePointers.size >= 2) {
            const metrics = getPinchMetrics();
            if (metrics && state.pinchState) {
                const scaleFactor = metrics.dist / state.pinchState.dist;
                const nextScale = Math.max(0.28, Math.min(3.5, state.scale * scaleFactor));
                const ratio = nextScale / state.scale;

                const offsetX = metrics.midX - state.width / 2 - state.panX;
                const offsetY = metrics.midY - state.height / 2 - state.panY;

                state.panX = state.panX - offsetX * (ratio - 1) + (metrics.midX - state.pinchState.midX);
                state.panY = state.panY - offsetY * (ratio - 1) + (metrics.midY - state.pinchState.midY);
                state.scale = nextScale;
            }
            state.pinchState = metrics;
            return;
        }

        if (state.draggedNode) {
            const dx = pos.x - state.lastPointerPos.x;
            const dy = pos.y - state.lastPointerPos.y;
            state.pointerMovedDist += Math.hypot(dx, dy);

            const world = screenToWorld(pos.x, pos.y);
            state.draggedNode.fx = world.x;
            state.draggedNode.fy = world.y;
            state.lastPointerPos = pos;
            return;
        }

        if (state.isPanning) {
            const dx = pos.x - state.lastPointerPos.x;
            const dy = pos.y - state.lastPointerPos.y;
            state.pointerMovedDist += Math.hypot(dx, dy);
            state.panX += dx;
            state.panY += dy;
            state.lastPointerPos = pos;
            return;
        }

        const hit = findNodeAt(pos.x, pos.y);
        if (hit !== state.hoveredNode) {
            state.hoveredNode = hit;
            canvas.style.cursor = hit ? 'pointer' : 'grab';
            if (typeof callbacks.onHoverNode === 'function') {
                callbacks.onHoverNode(hit);
            }
        }
    }

    function onPointerUp(e) {
        state.activePointers.delete(e.pointerId);

        if (canvas.releasePointerCapture && e.pointerId !== undefined) {
            try {
                canvas.releasePointerCapture(e.pointerId);
            } catch (_) {
                // Ignore if not captured
            }
        }

        if (state.activePointers.size >= 2) {
            state.pinchState = getPinchMetrics();
            return;
        }

        if (state.activePointers.size === 1) {
            state.pinchState = null;
            const [remaining] = state.activePointers.values();
            state.lastPointerPos = remaining;
            state.isPanning = true;
            state.pointerMovedDist = 999;
            return;
        }

        state.pinchState = null;

        if (state.wasMultiTouch) {
            state.wasMultiTouch = false;
            state.isPanning = false;
            state.draggedNode = null;
            canvas.style.cursor = 'grab';
            return;
        }

        const wasClick = state.pointerMovedDist < 6;

        if (state.draggedNode) {
            const clickedNode = state.draggedNode;
            clickedNode.fx = null;
            clickedNode.fy = null;
            state.draggedNode = null;
            simulation.alphaTarget(0);

            if (wasClick) {
                if (clickedNode.kind === 'student') {
                    state.selectedStudentNode = clickedNode;
                    if (typeof callbacks.onSelectStudent === 'function') {
                        callbacks.onSelectStudent(clickedNode);
                    }
                } else if (clickedNode.kind === 'aspect') {
                    if (typeof callbacks.onToggleAspect === 'function') {
                        callbacks.onToggleAspect(clickedNode.letter);
                    }
                }
            }
        } else if (state.isPanning) {
            state.isPanning = false;
            if (wasClick && state.selectedStudentNode) {
                state.selectedStudentNode = null;
                if (typeof callbacks.onSelectStudent === 'function') {
                    callbacks.onSelectStudent(null);
                }
            }
        }

        const pos = getLocalCoords(e.clientX, e.clientY);
        const hit = findNodeAt(pos.x, pos.y);
        canvas.style.cursor = hit ? 'pointer' : 'grab';
    }

    function onWheel(e) {
        e.preventDefault();
        const pos = getLocalCoords(e.clientX, e.clientY);
        const zoomFactor = e.deltaY < 0 ? 1.12 : 0.89;
        const nextScale = Math.max(0.28, Math.min(3.5, state.scale * zoomFactor));
        const ratio = nextScale / state.scale;

        const offsetX = pos.x - state.width / 2 - state.panX;
        const offsetY = pos.y - state.height / 2 - state.panY;

        state.panX -= offsetX * (ratio - 1);
        state.panY -= offsetY * (ratio - 1);
        state.scale = nextScale;
    }

    function onPointerLeave() {
        if (!state.draggedNode && !state.isPanning && state.hoveredNode) {
            state.hoveredNode = null;
            if (typeof callbacks.onHoverNode === 'function') {
                callbacks.onHoverNode(null);
            }
        }
    }

    resize();
    window.addEventListener('resize', resize);
    canvas.addEventListener('pointerdown', onPointerDown);
    canvas.addEventListener('pointermove', onPointerMove);
    canvas.addEventListener('pointerup', onPointerUp);
    canvas.addEventListener('pointercancel', onPointerUp);
    canvas.addEventListener('pointerleave', onPointerLeave);
    canvas.addEventListener('wheel', onWheel, { passive: false });

    emitStats();
    renderLoop();

    return {
        setActiveAspects(aspects) {
            state.activeAspects = normalizeAspectList(aspects);
            emitStats();
        },
        setSearchQuery(query) {
            state.searchQuery = String(query || '');
            emitStats();
        },
        setRepelStrength(val) {
            state.repelStrength = Number(val) || 135;
            manyBodyForce.strength((d) => {
                if (d.kind === 'center') return -state.repelStrength * 3.4;
                if (d.kind === 'aspect') return -state.repelStrength * 2.2;
                return -state.repelStrength;
            });
            simulation.alpha(0.35).restart();
        },
        setLinkDistance(val) {
            state.linkDistance = Number(val) || 145;
            linkForce.distance((l) => (l.kind === 'hub' ? state.linkDistance * 1.15 : state.linkDistance));
            simulation.alpha(0.35).restart();
        },
        setShowLinks(flag) {
            state.showLinks = Boolean(flag);
        },
        setShowStudentLabels(flag) {
            state.showStudentLabels = Boolean(flag);
        },
        clearSelection() {
            state.selectedStudentNode = null;
        },
        zoomBy(factor) {
            state.scale = Math.max(0.28, Math.min(3.5, state.scale * factor));
        },
        resetCamera() {
            const view = getDefaultView(state.width);
            state.panX = view.panX;
            state.panY = view.panY;
            state.scale = view.scale;
        },
        reheat() {
            simulation.alpha(0.75).restart();
        },
        destroy() {
            state.destroyed = true;
            if (state.rafId) cancelAnimationFrame(state.rafId);
            simulation.stop();
            window.removeEventListener('resize', resize);
            canvas.removeEventListener('pointerdown', onPointerDown);
            canvas.removeEventListener('pointermove', onPointerMove);
            canvas.removeEventListener('pointerup', onPointerUp);
            canvas.removeEventListener('pointercancel', onPointerUp);
            canvas.removeEventListener('pointerleave', onPointerLeave);
            canvas.removeEventListener('wheel', onWheel);
        },
    };
}
