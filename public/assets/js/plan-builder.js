/* court-fitness Plan Builder + Plan Show — inline-grid client logic
 *
 * Canonical UX in .ai/core/plan_builder_ux.md (LOCKED 2026-04-23).
 *
 * Modes (read from inline JSON #cf-taxonomy-data.mode):
 *   - 'new'          : Plan Builder. Create flow. Targets editable, no actuals.
 *   - 'coach-edit'   : Coach plan-show. Targets + Actuals both editable.
 *   - 'player-edit'  : Player plan-show. Targets readonly. Actuals editable.
 *
 * Layout (per plan_builder_ux.md §1, the LTAT screen transcription):
 *
 *   Each "block" represents (training_date, session_period, format).
 *   Inside a block, one row per exercise: [+] [category] [sub-category ▼] [cells...] [-]
 *   Cells per format (Cardio ≠ Weights ≠ Agility per §2.4):
 *     - Cardio : max_hr_pct, duration_min
 *     - Weights: sets, reps, weight, rest_sec
 *     - Agility: reps, rest_sec
 *   In show modes each metric has a paired (target, actual) input; the partial's CSS
 *   visually distinguishes them (target = muted background, actual = green-tinted).
 *
 * Submission shape:
 *   The hidden input #entries_json carries an array of entries. Each entry:
 *     {
 *       id?: number,                       // present on update; absent on create
 *       training_date: 'YYYY-MM-DD',
 *       session_period: 'morning'|'afternoon'|'evening',
 *       exercise_type_id: int,
 *       fitness_category_id: int,
 *       fitness_subcategory_id: int,
 *       target?: { ...metric: number|null },
 *       actual?: { ...metric: number|null },
 *     }
 *   Server controllers (Coach\Plans::store/update, Player\Plans::update) decide
 *   which fields they accept based on role.
 */
(function () {
    'use strict';

    // -------- Format → cell-set mapping (per exercise_json_shapes.md) ----------
    const TYPE_CARDIO_RX  = /^Cardio/i;
    const TYPE_WEIGHTS_RX = /^Weights/i;
    const TYPE_AGILITY_RX = /^Agility/i;

    // Cell counts per format (per owner directive, 2026-04-26, confirmed against
    // .ai/research-notes/screenshots/same-day-3-sessions.jpg):
    //   Cardio  = 10 cells   Weights = 4 cells   Agility = 6 cells
    // ALL cells are typable on every row regardless of format (the LTAT screenshot's
    // greying is presentational only — `secondary: true` here means "render a muted
    // visual style", NOT "disable the input"). Keys are derived from the cell labels
    // and become the JSON shape stored in plan_entries.target_json / actual_json
    // (see .ai/core/exercise_json_shapes.md).
    const CELLS_BY_FORMAT = {
        cardio:  [
            { key: 'target',       label: 'Target',     min: 0, step: 1 },
            { key: 'max_hr_pct',   label: 'Max HR %',   min: 40, max: 100, step: 1 },
            { key: 'duration_min', label: 'Duration',   min: 1, max: 300, step: 1 },
            { key: 'sets',         label: 'Sets',       min: 1, max: 20, step: 1, secondary: true },
            { key: 'reps',         label: 'Reps',       min: 1, max: 100, step: 1, secondary: true },
            { key: 'work',         label: 'Work',       min: 0, step: 1, secondary: true },
            { key: 'set_rest_sec', label: 'Set Rest',   min: 0, max: 600, step: 1, secondary: true },
            { key: 'set_count',    label: 'Set',        min: 0, step: 1, secondary: true },
            { key: 'rep_count',    label: 'Rep',        min: 0, step: 1, secondary: true },
            { key: 'weight',       label: 'Weight',     min: 0, max: 500, step: 0.5, secondary: true },
        ],
        weights: [
            { key: 'weight',       label: 'Weight',     min: 0, max: 500, step: 0.5 },
            { key: 'sets',         label: 'Sets',       min: 1, max: 20, step: 1 },
            { key: 'reps',         label: 'Reps',       min: 1, max: 100, step: 1 },
            { key: 'set_rest_sec', label: 'Set Rest',   min: 0, max: 600, step: 1 },
        ],
        agility: [
            { key: 'sets',         label: 'Sets',       min: 1, max: 20, step: 1 },
            { key: 'reps',         label: 'Reps',       min: 1, max: 100, step: 1 },
            { key: 'works',        label: 'Works',      min: 0, step: 1 },
            { key: 'rests',        label: 'Rests',      min: 0, step: 1 },
            { key: 'total_work',   label: 'Total Work', min: 0, step: 1 },
            { key: 'work_rest',    label: 'Work Rest',  min: 0, max: 600, step: 1 },
        ],
    };

    function formatKeyForType(typeName) {
        if (TYPE_CARDIO_RX.test(typeName))  return 'cardio';
        if (TYPE_WEIGHTS_RX.test(typeName)) return 'weights';
        if (TYPE_AGILITY_RX.test(typeName)) return 'agility';
        return null;
    }

    const PERIODS = [
        { key: 'morning',   label: 'Morning' },
        { key: 'afternoon', label: 'Afternoon' },
        { key: 'evening',   label: 'Evening' },
    ];

    const DAY_LABELS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const MONTHS     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // -------- Boot ----------
    const dataEl = document.getElementById('cf-taxonomy-data');
    if (!dataEl) return; // not on a plan page
    const data = JSON.parse(dataEl.textContent);

    const mode             = data.mode;                 // 'new' | 'coach-edit' | 'player-edit'
    const canEditTargets   = !!data.can_edit_target;
    const canEditActuals   = !!data.can_edit_actual;
    const showActualCells  = canEditActuals;             // actuals only render in show modes

    const typesById        = new Map(data.types.map(t => [t.id, t]));
    const categoriesById   = new Map(data.categories.map(c => [c.id, c]));
    const subcatsById      = new Map(data.subcategories.map(s => [s.id, s]));
    const categoriesByType = groupBy(data.categories,    c => c.exercise_type_id);
    const subcatsByCat     = groupBy(data.subcategories, s => s.fitness_category_id);

    // Working state — array of blocks. Each block has rows; each row mirrors an entry.
    // Block shape: { id, training_date, session_period, format_key, exercise_type_id, rows: [...] }
    // Row   shape: { id?, fitness_category_id, fitness_subcategory_id, target:{...}, actual:{...},
    //                audit_name, audit_role, actual_at }
    const state = { blocks: [] };

    // -------- DOM elements ----------
    const form           = document.getElementById('cf-plan-form');
    const blocksEl       = document.getElementById('cf-blocks');
    const emptyMsgEl     = document.getElementById('cf-empty-blocks');
    const addBlockBtn    = document.getElementById('cf-add-block');
    const entriesJsonHid = document.getElementById('entries_json');
    const entriesCountEl = document.getElementById('cf-entries-count');
    const weekOfInput    = document.getElementById('week_of');             // present in 'new' only
    const targetSelect   = document.getElementById('training_target');
    const targetCustomInp = document.getElementById('training_target_custom');

    // -------- New-mode fundamentals wiring ----------
    if (mode === 'new' && weekOfInput) {
        weekOfInput.addEventListener('change', () => warnIfNotMonday(weekOfInput.value));
        warnIfNotMonday(weekOfInput.value);
    }
    if (mode === 'new' && targetSelect) {
        targetSelect.addEventListener('change', () => {
            if (targetSelect.value === '__custom__') {
                targetCustomInp.classList.remove('d-none');
                targetCustomInp.focus();
            } else {
                targetCustomInp.classList.add('d-none');
                targetCustomInp.value = '';
            }
        });
    }

    // -------- Hydrate from server-provided entries (show modes) ----------
    if (data.entries && data.entries.length > 0) {
        // Group entries into blocks keyed by (date, period, type).
        const byKey = new Map();
        data.entries.forEach(e => {
            const key = `${e.training_date}|${e.session_period}|${e.exercise_type_id}`;
            if (!byKey.has(key)) {
                byKey.set(key, {
                    id: 'b' + byKey.size,
                    training_date:    e.training_date,
                    session_period:   e.session_period,
                    exercise_type_id: e.exercise_type_id,
                    rows: [],
                });
            }
            byKey.get(key).rows.push({
                id:                     e.id || null,
                fitness_category_id:    e.fitness_category_id,
                fitness_subcategory_id: e.fitness_subcategory_id,
                target:                 sanitiseBag(e.target),
                actual:                 sanitiseBag(e.actual),
                audit_name:             e.audit_name || null,
                audit_role:             e.audit_role || null,
                actual_at:              e.actual_at  || null,
            });
        });
        state.blocks = Array.from(byKey.values());
    }

    // -------- Add-block button ----------
    addBlockBtn.addEventListener('click', () => {
        const defaultDate = (weekOfInput && weekOfInput.value) || data.entries?.[0]?.training_date || todayIso();
        const firstType   = data.types[0];
        if (!firstType) {
            alert('No exercise types are configured. Contact support.');
            return;
        }
        state.blocks.push({
            id: 'b' + Date.now() + Math.random().toString(36).slice(2, 6),
            training_date:    defaultDate,
            session_period:   'morning',
            exercise_type_id: firstType.id,
            rows: [],
        });
        const newBlockIdx = state.blocks.length - 1;
        addRowToBlock(newBlockIdx); // start with one empty row — saves a click
        render();
    });

    // -------- Submit ----------
    form.addEventListener('submit', (ev) => {
        const entries = serialiseEntries();
        entriesJsonHid.value = JSON.stringify(entries);

        if (entries.length === 0) {
            if (! confirm('This plan has no exercises. Save anyway?')) {
                ev.preventDefault();
            }
        }
    });

    // -------- Render ----------
    render();

    function render() {
        blocksEl.innerHTML = '';
        if (state.blocks.length === 0) {
            const p = document.createElement('p');
            p.className = 'cf-empty-blocks';
            p.textContent = emptyMsgEl ? emptyMsgEl.textContent : 'No training dates yet.';
            blocksEl.appendChild(p);
            updateCount();
            return;
        }

        state.blocks.forEach((block, blockIdx) => {
            blocksEl.appendChild(renderBlock(block, blockIdx));
        });
        updateCount();
    }

    function renderBlock(block, blockIdx) {
        const wrap = document.createElement('div');
        wrap.className = 'cf-block';
        wrap.setAttribute('data-block-idx', String(blockIdx));

        // Block head
        const head = document.createElement('div');
        head.className = 'cf-block__head';
        head.innerHTML = `
            <span class="cf-block__head-icon" data-cf-remove-block title="Remove this date+session">−</span>
            <div class="cf-block__head-cell">
                <label class="form-label cf-cell__label">Training date</label>
                <input type="date" class="form-control" value="${esc(block.training_date)}" data-cf-block-date>
            </div>
            <div class="cf-block__head-cell">
                <label class="form-label cf-cell__label">Session</label>
                <select class="form-select" data-cf-block-period>
                    ${PERIODS.map(p => `<option value="${p.key}"${p.key === block.session_period ? ' selected' : ''}>${p.label}</option>`).join('')}
                </select>
            </div>
            <div class="cf-block__head-cell">
                <label class="form-label cf-cell__label">Format</label>
                <select class="form-select" data-cf-block-type>
                    ${data.types.map(t => `<option value="${t.id}"${t.id === block.exercise_type_id ? ' selected' : ''}>${esc(t.name)}</option>`).join('')}
                </select>
            </div>
            <div></div>
        `;
        wrap.appendChild(head);

        head.querySelector('[data-cf-remove-block]').addEventListener('click', () => {
            if (confirm('Remove this whole training date / session and its exercises?')) {
                state.blocks.splice(blockIdx, 1);
                render();
            }
        });
        head.querySelector('[data-cf-block-date]').addEventListener('change', (ev) => {
            block.training_date = ev.target.value;
        });
        head.querySelector('[data-cf-block-period]').addEventListener('change', (ev) => {
            block.session_period = ev.target.value;
        });
        head.querySelector('[data-cf-block-type]').addEventListener('change', (ev) => {
            block.exercise_type_id = parseInt(ev.target.value, 10) || 0;
            // Clear rows whose category no longer matches the new type — silently drop targets/actuals.
            const validCatIds = new Set((categoriesByType.get(block.exercise_type_id) || []).map(c => c.id));
            block.rows.forEach(r => {
                if (! validCatIds.has(r.fitness_category_id)) {
                    r.fitness_category_id    = 0;
                    r.fitness_subcategory_id = 0;
                    r.target = {};
                }
            });
            render();
        });

        // Block body
        const body = document.createElement('div');
        body.className = 'cf-block__body';
        wrap.appendChild(body);

        block.rows.forEach((row, rowIdx) => {
            body.appendChild(renderRow(block, blockIdx, row, rowIdx));
        });

        // The redundant "+ Add exercise" button at the bottom of each block was
        // removed 2026-04-27 (Fix 4, owner-approved): the row-level [+] icon
        // already inserts a new exercise row after the current one — clearer
        // affordance, and inserts in the middle of a block when the coach wants
        // to. Owner: "If this is correct then we can remove 'Add Exercise'."

        return wrap;
    }

    function renderRow(block, blockIdx, row, rowIdx) {
        const rowEl = document.createElement('div');
        rowEl.className = 'cf-row';

        const typeName  = (typesById.get(block.exercise_type_id) || {}).name || '';
        const formatKey = formatKeyForType(typeName) || 'cardio';
        const cellDefs  = CELLS_BY_FORMAT[formatKey] || [];

        // Add/remove buttons on the left
        const ar = document.createElement('div');
        ar.className = 'cf-row__addremove';
        ar.innerHTML = `
            <button type="button" class="cf-row__add" title="Add exercise after this">+</button>
            <button type="button" class="cf-row__remove" title="Remove this exercise">−</button>
        `;
        ar.querySelector('.cf-row__add').addEventListener('click', () => {
            addRowToBlock(blockIdx, rowIdx + 1);
            render();
        });
        ar.querySelector('.cf-row__remove').addEventListener('click', () => {
            block.rows.splice(rowIdx, 1);
            render();
        });
        rowEl.appendChild(ar);

        // Category dropdown — explicit cascade (Format → CATEGORY → Sub-category, per owner 2026-04-26).
        // Options scoped to the block's Format. Picking a Category clears the Sub-category if it no
        // longer matches.
        const catWrap = document.createElement('div');
        catWrap.className = 'cf-row__cat';
        const catSel = document.createElement('select');
        catSel.className = 'form-select form-select-sm';
        catSel.disabled = !canEditTargets;
        const validCats = (categoriesByType.get(block.exercise_type_id) || []).slice().sort(byName);
        const catOpts = ['<option value="">— pick category —</option>'];
        validCats.forEach(cat => {
            catOpts.push(`<option value="${cat.id}"${cat.id === row.fitness_category_id ? ' selected' : ''}>${esc(cat.name)}</option>`);
        });
        catSel.innerHTML = catOpts.join('');
        catSel.addEventListener('change', () => {
            const catId = parseInt(catSel.value, 10) || 0;
            row.fitness_category_id = catId;
            // If the previously-picked sub-category no longer belongs to the new category, clear it.
            const sub = subcatsById.get(row.fitness_subcategory_id);
            if (! sub || sub.fitness_category_id !== catId) {
                row.fitness_subcategory_id = 0;
            }
            render();
        });
        catWrap.appendChild(catSel);
        rowEl.appendChild(catWrap);

        // Sub-category dropdown — options scoped to the ROW's chosen Category.
        // Stays disabled until a Category is picked (cascading-cascade).
        const subWrap = document.createElement('div');
        subWrap.className = 'cf-row__sub';
        const subSel = document.createElement('select');
        subSel.className = 'form-select form-select-sm';
        subSel.disabled = !canEditTargets || row.fitness_category_id === 0;
        const subOpts = ['<option value="">— pick exercise —</option>'];
        if (row.fitness_category_id) {
            const subs = (subcatsByCat.get(row.fitness_category_id) || []).slice().sort(byName);
            subs.forEach(s => {
                subOpts.push(`<option value="${s.id}"${s.id === row.fitness_subcategory_id ? ' selected' : ''}>${esc(s.name)}</option>`);
            });
        }
        subSel.innerHTML = subOpts.join('');
        subSel.addEventListener('change', () => {
            row.fitness_subcategory_id = parseInt(subSel.value, 10) || 0;
            // No re-render needed — only the cells depend on Format (block-level), and Sub-category change
            // doesn't shift cells in the current Sprint 1 model. Keep DOM stable so the user's input focus
            // doesn't get reset.
        });
        subWrap.appendChild(subSel);
        rowEl.appendChild(subWrap);

        // Cells — target [+ actual]
        const cellsWrap = document.createElement('div');
        cellsWrap.className = 'cf-row__cells cf-cells';
        cellDefs.forEach(def => {
            cellsWrap.appendChild(renderCell(row, def));
        });
        rowEl.appendChild(cellsWrap);

        // Audit display ("Logged by ... · Xm ago") if we have an actual logged on this row
        if (row.audit_name && row.actual_at) {
            const audit = document.createElement('div');
            audit.className = 'cf-audit';
            audit.textContent = `Logged by ${formatAuditName(row.audit_name, row.audit_role)} · ${formatRelativeTime(row.actual_at)}`;
            rowEl.appendChild(audit);
        }

        return rowEl;
    }

    function renderCell(row, def) {
        const cell = document.createElement('div');
        cell.className = 'cf-cell' + (def.secondary ? ' cf-cell--secondary' : '');
        cell.innerHTML = `<span class="cf-cell__label">${esc(def.label)}</span>`;

        const inputs = document.createElement('div');
        inputs.className = 'cf-cell__inputs';

        // Target input
        const tgt = document.createElement('input');
        tgt.type = 'number';
        tgt.className = 'cf-target';
        tgt.placeholder = 'Target';
        tgt.title = `Target ${def.label}`;
        if (def.min  !== undefined) tgt.min  = String(def.min);
        if (def.max  !== undefined) tgt.max  = String(def.max);
        if (def.step !== undefined) tgt.step = String(def.step);
        tgt.value = (row.target && row.target[def.key] != null) ? row.target[def.key] : '';
        if (! canEditTargets) tgt.readOnly = true;
        tgt.addEventListener('input', () => {
            row.target = row.target || {};
            row.target[def.key] = numOrNull(tgt.value);
        });
        inputs.appendChild(tgt);

        // Actual input — only on show modes
        if (showActualCells) {
            const act = document.createElement('input');
            act.type = 'number';
            act.className = 'cf-actual';
            act.placeholder = 'Actual';
            act.title = `Actual ${def.label}`;
            if (def.min  !== undefined) act.min  = String(def.min);
            if (def.max  !== undefined) act.max  = String(def.max);
            if (def.step !== undefined) act.step = String(def.step);
            act.value = (row.actual && row.actual[def.key] != null) ? row.actual[def.key] : '';
            if (! canEditActuals) act.readOnly = true;
            act.addEventListener('input', () => {
                row.actual = row.actual || {};
                row.actual[def.key] = numOrNull(act.value);
            });
            inputs.appendChild(act);
        }

        cell.appendChild(inputs);
        return cell;
    }

    // -------- Mutators ----------
    function addRowToBlock(blockIdx, atIdx) {
        const block = state.blocks[blockIdx];
        const row = {
            id: null,
            fitness_category_id: 0,
            fitness_subcategory_id: 0,
            target: {},
            actual: {},
            audit_name: null,
            audit_role: null,
            actual_at: null,
        };
        if (typeof atIdx === 'number') {
            block.rows.splice(atIdx, 0, row);
        } else {
            block.rows.push(row);
        }
    }

    // -------- Submit serialisation ----------
    function serialiseEntries() {
        const out = [];
        state.blocks.forEach(block => {
            block.rows.forEach(row => {
                if (! row.fitness_subcategory_id) return; // skip empty rows
                const e = {
                    training_date:          block.training_date,
                    session_period:         block.session_period,
                    exercise_type_id:       block.exercise_type_id,
                    fitness_category_id:    row.fitness_category_id,
                    fitness_subcategory_id: row.fitness_subcategory_id,
                };
                if (row.id) e.id = row.id;
                e.target = row.target || {};
                if (showActualCells) e.actual = row.actual || {};
                out.push(e);
            });
        });
        return out;
    }

    function updateCount() {
        let n = 0;
        state.blocks.forEach(b => { n += b.rows.filter(r => r.fitness_subcategory_id).length; });
        if (entriesCountEl) {
            entriesCountEl.textContent = n + ' ' + (n === 1 ? 'exercise' : 'exercises');
        }
    }

    // -------- Helpers ----------
    function warnIfNotMonday(dateStr) {
        const hint = document.getElementById('week_of_hint');
        if (! hint) return;
        const d = parseDate(dateStr);
        if (! d) { hint.textContent = 'Pick a date.'; hint.className = 'form-text text-danger'; return; }
        if (d.getUTCDay() !== 1) {
            hint.textContent = 'That date is not a Monday. The server will reject the save.';
            hint.className = 'form-text text-danger';
        } else {
            hint.textContent = 'The week runs Monday through Sunday.';
            hint.className = 'form-text';
        }
    }

    function sanitiseBag(v) {
        if (Array.isArray(v)) return {};      // shouldn't happen, but null-safe
        if (v && typeof v === 'object') return v;
        return {};
    }

    function groupBy(arr, keyFn) {
        const m = new Map();
        arr.forEach(x => {
            const k = keyFn(x);
            if (! m.has(k)) m.set(k, []);
            m.get(k).push(x);
        });
        return m;
    }

    function parseDate(iso) {
        if (! iso) return null;
        const parts = iso.split('-');
        if (parts.length !== 3) return null;
        const d = new Date(Date.UTC(+parts[0], +parts[1] - 1, +parts[2]));
        return isNaN(d.getTime()) ? null : d;
    }
    function todayIso() { return new Date().toISOString().slice(0, 10); }
    function numOrNull(v) {
        if (v === '' || v == null) return null;
        const n = Number(v);
        return Number.isFinite(n) ? n : null;
    }
    function byName(a, b) { return a.name.localeCompare(b.name); }

    function formatAuditName(name, role) {
        if (role === 'coach') return 'Coach ' + name;
        return name;
    }
    function formatRelativeTime(iso) {
        if (! iso) return '';
        const then = new Date(iso.replace(' ', 'T') + 'Z');
        if (isNaN(then.getTime())) return iso;
        const sec = Math.max(0, Math.floor((Date.now() - then.getTime()) / 1000));
        if (sec < 60)        return 'just now';
        if (sec < 3600)      return Math.floor(sec / 60)   + 'm ago';
        if (sec < 86400)     return Math.floor(sec / 3600) + 'h ago';
        if (sec < 86400 * 7) return Math.floor(sec / 86400) + 'd ago';
        return then.toLocaleDateString();
    }

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
    }
})();
