/* court-fitness Plan Builder — client logic
 *
 * Responsibilities:
 *   - Render 7 day-accordions from week_of (one per date Mon..Sun)
 *   - Under each day, three session cards (morning/afternoon/evening)
 *   - Open the add-exercise modal scoped to (date, session)
 *   - Drilldown: Type → Category → Subcategory, filtered client-side
 *   - Toggle type-specific target fields based on the selected exercise_type
 *   - Maintain an `entries[]` array and mirror it into #entries_json on submit
 *   - Count exercises in the save bar
 *   - Training target combobox: switch to free-text when "+ Add custom…" picked
 *
 * No AJAX — taxonomy comes from inline JSON at page load.
 */
(function () {
    'use strict';

    const TYPE_CARDIO_RX  = /^Cardio/i;
    const TYPE_WEIGHTS_RX = /^Weights/i;
    const TYPE_AGILITY_RX = /^Agility/i;

    const PERIODS = [
        { key: 'morning',   label: 'Morning' },
        { key: 'afternoon', label: 'Afternoon' },
        { key: 'evening',   label: 'Evening' },
    ];

    const DAY_LABELS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const MONTHS     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // -------- data ----------
    const data = JSON.parse(document.getElementById('cf-taxonomy-data').textContent);
    const typesById        = new Map(data.types.map(t => [t.id, t]));
    const categoriesByType = groupBy(data.categories,    c => c.exercise_type_id);
    const subcatsByCat     = groupBy(data.subcategories, s => s.fitness_category_id);

    const entries = []; // each: { training_date, session_period, exercise_type_id, fitness_category_id, fitness_subcategory_id, target }
    let modalContext = null; // { date, period } — the card that opened the modal

    // -------- elements ----------
    const form            = document.getElementById('cf-plan-form');
    const weekOfInput     = document.getElementById('week_of');
    const accordionEl     = document.getElementById('cf-days-accordion');
    const entriesJsonHid  = document.getElementById('entries_json');
    const entriesCountEl  = document.getElementById('cf-entries-count');
    const targetSelect    = document.getElementById('training_target');
    const targetCustomInp = document.getElementById('training_target_custom');

    // Modal elements
    const modalEl       = document.getElementById('cf-exercise-modal');
    const modalTitleEl  = document.getElementById('cf-exercise-modal-title');
    const modalCtxEl    = document.getElementById('cf-modal-context');
    const typeSel       = document.getElementById('mx_type');
    const catSel        = document.getElementById('mx_category');
    const subSel        = document.getElementById('mx_subcategory');
    const addBtn        = document.getElementById('cf-modal-add');
    const targetGroups  = document.querySelectorAll('.cf-target-group');

    const modal = new bootstrap.Modal(modalEl);

    // -------- init ----------
    renderAccordion(weekOfInput.value);
    weekOfInput.addEventListener('change', () => {
        warnIfNotMonday(weekOfInput.value);
        renderAccordion(weekOfInput.value);
    });
    warnIfNotMonday(weekOfInput.value);

    targetSelect.addEventListener('change', () => {
        if (targetSelect.value === '__custom__') {
            targetCustomInp.classList.remove('d-none');
            targetCustomInp.focus();
        } else {
            targetCustomInp.classList.add('d-none');
            targetCustomInp.value = '';
        }
    });

    typeSel.addEventListener('change', onTypeChange);
    catSel.addEventListener('change',  onCategoryChange);
    addBtn.addEventListener('click',   onModalAdd);

    form.addEventListener('submit', (ev) => {
        entriesJsonHid.value = JSON.stringify(entries);
        if (entries.length === 0) {
            if (! confirm('This plan has no exercises. Save anyway?')) {
                ev.preventDefault();
            }
        }
    });

    // -------- rendering ----------
    function renderAccordion(dateStr) {
        accordionEl.innerHTML = '';
        if (! dateStr) return;
        const monday = parseDate(dateStr);
        if (! monday) return;

        for (let i = 0; i < 7; i++) {
            const d = addDays(monday, i);
            const dateIso = toIso(d);
            const headerLabel = `${DAY_LABELS[i]}, ${d.getDate()} ${MONTHS[d.getMonth()]}`;
            const itemId      = 'day-' + dateIso;

            const item = document.createElement('div');
            item.className = 'accordion-item';
            item.innerHTML = `
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#${itemId}">
                        <span>${headerLabel}</span>
                        <span class="badge bg-primary ms-2 cf-day-count" data-date="${dateIso}">0</span>
                    </button>
                </h3>
                <div id="${itemId}" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        ${PERIODS.map(p => sessionCardHtml(dateIso, p)).join('')}
                    </div>
                </div>
            `;
            accordionEl.appendChild(item);
        }

        // Wire the "+ Add exercise" buttons.
        accordionEl.querySelectorAll('[data-cf-add-exercise]').forEach(btn => {
            btn.addEventListener('click', () => {
                openModalFor(btn.getAttribute('data-date'), btn.getAttribute('data-period'));
            });
        });

        // Any entries that already match new dates should still render (e.g.
        // user changed the week_of — we drop entries outside the new window).
        pruneOutOfWindowEntries(monday);
        renderAllEntryChips();
        updateCount();
    }

    function sessionCardHtml(dateIso, period) {
        return `
            <div class="cf-session-card mb-3">
                <header class="d-flex justify-content-between align-items-center mb-2">
                    <strong>${period.label}</strong>
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            data-cf-add-exercise
                            data-date="${dateIso}" data-period="${period.key}">+ Add exercise</button>
                </header>
                <ul class="list-group list-group-flush cf-exercise-list"
                    data-date="${dateIso}" data-period="${period.key}"></ul>
            </div>
        `;
    }

    function renderAllEntryChips() {
        document.querySelectorAll('.cf-exercise-list').forEach(ul => { ul.innerHTML = ''; });

        entries.forEach((e, idx) => {
            const ul = document.querySelector(
                `.cf-exercise-list[data-date="${e.training_date}"][data-period="${e.session_period}"]`
            );
            if (! ul) return;

            const sub  = data.subcategories.find(s => s.id === e.fitness_subcategory_id);
            const type = typesById.get(e.exercise_type_id);
            const summary = summariseTarget(type.name, e.target);

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-start';
            li.innerHTML = `
                <div class="me-auto">
                    <div class="fw-semibold">${escapeHtml(sub ? sub.name : '—')}</div>
                    <small class="text-muted">${escapeHtml(type.name)} · ${escapeHtml(summary)}</small>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger" aria-label="Remove">&times;</button>
            `;
            li.querySelector('button').addEventListener('click', () => {
                entries.splice(idx, 1);
                renderAllEntryChips();
                updateCount();
            });
            ul.appendChild(li);
        });

        // Day-level count badges
        document.querySelectorAll('.cf-day-count').forEach(badge => {
            const date = badge.getAttribute('data-date');
            badge.textContent = entries.filter(e => e.training_date === date).length;
        });
    }

    function updateCount() {
        const n = entries.length;
        entriesCountEl.textContent = n + ' ' + (n === 1 ? 'exercise' : 'exercises');
    }

    // -------- modal behaviour ----------
    function openModalFor(date, period) {
        modalContext = { date, period };
        const periodLabel = PERIODS.find(p => p.key === period).label;
        const d = parseDate(date);
        const ctx = `${DAY_LABELS[(d.getDay() + 6) % 7]}, ${d.getDate()} ${MONTHS[d.getMonth()]} · ${periodLabel}`;
        modalCtxEl.textContent = ctx;
        modalTitleEl.textContent = 'Add exercise';

        // Reset modal form
        typeSel.value = '';
        catSel.innerHTML = '<option value="">— pick a category —</option>';
        catSel.disabled  = true;
        subSel.innerHTML = '<option value="">— pick an exercise —</option>';
        subSel.disabled  = true;
        targetGroups.forEach(g => g.classList.add('d-none'));
        clearTargetInputs();
        modal.show();
    }

    function onTypeChange() {
        const typeId = parseInt(typeSel.value, 10) || 0;
        catSel.innerHTML = '<option value="">— pick a category —</option>';
        subSel.innerHTML = '<option value="">— pick an exercise —</option>';
        catSel.disabled  = true;
        subSel.disabled  = true;
        targetGroups.forEach(g => g.classList.add('d-none'));

        if (! typeId) return;

        const cats = (categoriesByType.get(typeId) || []).slice().sort(byName);
        cats.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.name;
            catSel.appendChild(opt);
        });
        catSel.disabled = cats.length === 0;

        // Show target fields appropriate to the type
        const typeName = typesById.get(typeId).name;
        const groupKey = TYPE_CARDIO_RX.test(typeName)
            ? 'cardio'
            : TYPE_WEIGHTS_RX.test(typeName)
                ? 'weights'
                : TYPE_AGILITY_RX.test(typeName)
                    ? 'agility'
                    : null;
        if (groupKey) {
            document.querySelector(`.cf-target-group[data-target-group="${groupKey}"]`).classList.remove('d-none');
        }
    }

    function onCategoryChange() {
        const catId = parseInt(catSel.value, 10) || 0;
        subSel.innerHTML = '<option value="">— pick an exercise —</option>';
        if (! catId) {
            subSel.disabled = true;
            return;
        }
        const subs = (subcatsByCat.get(catId) || []).slice().sort(byName);
        subs.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            subSel.appendChild(opt);
        });
        subSel.disabled = subs.length === 0;
    }

    function onModalAdd() {
        const typeId = parseInt(typeSel.value, 10) || 0;
        const catId  = parseInt(catSel.value, 10)  || 0;
        const subId  = parseInt(subSel.value, 10)  || 0;
        if (! typeId || ! catId || ! subId) {
            alert('Pick a format, category and exercise first.');
            return;
        }

        const type = typesById.get(typeId);
        const target = readTargetFor(type.name);

        entries.push({
            training_date:          modalContext.date,
            session_period:         modalContext.period,
            exercise_type_id:       typeId,
            fitness_category_id:    catId,
            fitness_subcategory_id: subId,
            target,
        });
        renderAllEntryChips();
        updateCount();
        modal.hide();
    }

    function readTargetFor(typeName) {
        if (TYPE_CARDIO_RX.test(typeName)) {
            return {
                max_hr_pct:   numOrNull(document.getElementById('mx_max_hr_pct').value),
                duration_min: numOrNull(document.getElementById('mx_duration_min').value),
            };
        }
        if (TYPE_WEIGHTS_RX.test(typeName)) {
            return {
                sets:     numOrNull(document.getElementById('mx_sets').value),
                reps:     numOrNull(document.getElementById('mx_reps').value),
                weight:   numOrNull(document.getElementById('mx_weight').value),
                rest_sec: numOrNull(document.getElementById('mx_rest_sec_w').value),
            };
        }
        if (TYPE_AGILITY_RX.test(typeName)) {
            return {
                reps:     numOrNull(document.getElementById('mx_reps_a').value),
                rest_sec: numOrNull(document.getElementById('mx_rest_sec_a').value),
            };
        }
        return {};
    }

    function clearTargetInputs() {
        [
            'mx_max_hr_pct','mx_duration_min',
            'mx_sets','mx_reps','mx_weight','mx_rest_sec_w',
            'mx_reps_a','mx_rest_sec_a',
        ].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    }

    function summariseTarget(typeName, t) {
        if (! t) return '';
        if (TYPE_CARDIO_RX.test(typeName)) {
            const parts = [];
            if (t.duration_min) parts.push(t.duration_min + ' min');
            if (t.max_hr_pct)   parts.push('HR ' + t.max_hr_pct + '%');
            return parts.join(' · ') || 'no target set';
        }
        if (TYPE_WEIGHTS_RX.test(typeName)) {
            const sets = t.sets || '?';
            const reps = t.reps || '?';
            const w    = t.weight ? ' @ ' + t.weight : '';
            const rest = t.rest_sec ? ' · rest ' + t.rest_sec + 's' : '';
            return `${sets}×${reps}${w}${rest}`;
        }
        if (TYPE_AGILITY_RX.test(typeName)) {
            const parts = [];
            if (t.reps)     parts.push(t.reps + ' reps');
            if (t.rest_sec) parts.push('rest ' + t.rest_sec + 's');
            return parts.join(' · ') || 'no target set';
        }
        return '';
    }

    // -------- helpers ----------
    function warnIfNotMonday(dateStr) {
        const hint = document.getElementById('week_of_hint');
        const d = parseDate(dateStr);
        if (! d) { hint.textContent = 'Pick a date.'; hint.className = 'form-text text-danger'; return; }
        if (d.getDay() !== 1) {
            hint.textContent = 'That date is not a Monday. The server will reject the save.';
            hint.className = 'form-text text-danger';
        } else {
            hint.textContent = 'The week runs Monday through Sunday.';
            hint.className = 'form-text';
        }
    }

    function pruneOutOfWindowEntries(monday) {
        const startIso = toIso(monday);
        const endIso   = toIso(addDays(monday, 6));
        for (let i = entries.length - 1; i >= 0; i--) {
            const d = entries[i].training_date;
            if (d < startIso || d > endIso) entries.splice(i, 1);
        }
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
    function addDays(d, n) { return new Date(d.getTime() + n * 86400000); }
    function toIso(d)      { return d.toISOString().slice(0, 10); }
    function numOrNull(v)  { const n = Number(v); return Number.isFinite(n) && v !== '' ? n : null; }
    function byName(a, b)  { return a.name.localeCompare(b.name); }
    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
    }
})();
