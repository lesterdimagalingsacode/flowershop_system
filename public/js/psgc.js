// ─────────────────────────────────────────────
//  public/js/psgc.js — PSGC Address Picker
//  Reusable cascading Region → Province →
//  Municipality → Barangay + Street picker.
//
//  Usage:
//    AddressPicker.init({
//      municipalityEl : document.getElementById('municipality'),
//      barangayEl     : document.getElementById('barangay'),
//      streetEl       : document.getElementById('street'),
//      outputEl       : document.getElementById('delivery_address'),
//      form           : document.querySelector('form'),
//      provinceCode   : '0307700000',   // Aurora
//      defaultMuni    : 'baler',        // optional: pre-select by name
//    });
// ─────────────────────────────────────────────

const AddressPicker = (() => {

    let BASE = '/api/psgc'; // overridden by config.baseUrl if provided

    // ── Fetch helpers ─────────────────────────
    async function fetchJson(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`PSGC fetch failed: ${url}`);
        return res.json();
    }

    function sortByName(arr) {
        return arr.slice().sort((a, b) => a.name.localeCompare(b.name));
    }

    function setLoading(select, msg = 'Loading...') {
        select.innerHTML = `<option value="">— ${msg} —</option>`;
        select.disabled  = true;
    }

    function setError(select, msg = 'Failed to load. Refresh page.') {
        select.innerHTML = `<option value="">— ${msg} —</option>`;
        select.disabled  = true;
    }

    // ── Load municipalities for a province ────
    async function loadMunicipalities(provinceCode, muniEl, defaultMuni, onLoaded) {
        setLoading(muniEl, 'Loading municipalities...');
        try {
            const data = sortByName(
                await fetchJson(`${BASE}/municipalities?province=${provinceCode}`)
            );

            muniEl.innerHTML = '<option value="">— Select Municipality —</option>';
            data.forEach(m => {
                const opt        = document.createElement('option');
                opt.value        = m.code;
                opt.dataset.name = m.name;
                opt.textContent  = m.name;
                muniEl.appendChild(opt);
            });

            muniEl.disabled = false;

            // Pre-select default municipality if provided
            if (defaultMuni) {
                const match = [...muniEl.options].find(
                    o => o.textContent.toLowerCase().includes(defaultMuni.toLowerCase())
                );
                if (match) {
                    match.selected = true;
                    if (onLoaded) onLoaded(match.value, match.dataset.name);
                }
            }
        } catch (e) {
            setError(muniEl, 'Failed to load municipalities');
        }
    }

    // ── Load barangays for a municipality ─────
    async function loadBarangays(muniCode, brgyEl, onLoaded) {
        setLoading(brgyEl, 'Loading barangays...');
        try {
            const data = sortByName(
                await fetchJson(`${BASE}/barangays?municipality=${muniCode}`)
            );

            brgyEl.innerHTML = '<option value="">— Select Barangay —</option>';
            data.forEach(b => {
                const opt       = document.createElement('option');
                opt.value       = b.name;
                opt.textContent = b.name;
                brgyEl.appendChild(opt);
            });

            brgyEl.disabled = false;
            if (onLoaded) onLoaded();
        } catch (e) {
            setError(brgyEl, 'Failed to load barangays');
        }
    }

    // ── Assemble full address string ──────────
    function buildAddress(muniEl, brgyEl, streetEl, provinceName, regionName) {
        const muni   = muniEl.options[muniEl.selectedIndex]?.dataset.name || '';
        const brgy   = brgyEl.value;
        const street = streetEl ? streetEl.value.trim() : '';

        const parts = [street, brgy, muni, provinceName, regionName].filter(Boolean);
        return parts.join(', ');
    }

    // ── Public init ───────────────────────────
    function init(config) {
        const {
            municipalityEl,
            barangayEl,
            streetEl,
            outputEl,
            form,
            provinceCode,
            provinceName = 'Aurora',
            regionName   = 'Region III (Central Luzon)',
            defaultMuni  = '',
            baseUrl      = '',
        } = config;

        if (baseUrl) BASE = baseUrl + '/api/psgc';

        if (!municipalityEl || !barangayEl) {
            console.error('AddressPicker: municipalityEl and barangayEl are required.');
            return;
        }

        // ── Keep delivery_address updated live ────
        function updateOutput() {
            if (outputEl) {
                outputEl.value = buildAddress(
                    municipalityEl,
                    barangayEl,
                    streetEl,
                    provinceName,
                    regionName
                );
            }
        }

        // Load municipalities on init
        loadMunicipalities(
            provinceCode,
            municipalityEl,
            defaultMuni,
            (muniCode) => loadBarangays(muniCode, barangayEl, updateOutput)
        );

        // Municipality change → reload barangays, then update output
        municipalityEl.addEventListener('change', function () {
            if (this.value) {
                loadBarangays(this.value, barangayEl, updateOutput);
            } else {
                barangayEl.innerHTML = '<option value="">— Select municipality first —</option>';
                barangayEl.disabled  = true;
                updateOutput();
            }
        });

        // Barangay change → update output live
        barangayEl.addEventListener('change', updateOutput);

        // Street input → update output live
        if (streetEl) {
            streetEl.addEventListener('input', updateOutput);
        }

        // Also update on submit as a safety net
        if (form && outputEl) {
            form.addEventListener('submit', function (e) {
                updateOutput();

                // If still empty after update, block submit
                if (!outputEl.value.trim()) {
                    e.preventDefault();
                    alert('Please complete your delivery address.');
                }
            });
        }
    }
    
    return { init };

})();