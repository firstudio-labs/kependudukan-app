<x-layout>
    <div class="p-4 mt-14">
        <div class="bg-white p-4 rounded shadow">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-semibold">Warungku - Semua Produk</h1>
                <a href="{{ route('user.warungku.my') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Produk Saya</a>
            </div>

            <form method="GET" class="mb-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="flex-1 border rounded p-2" />
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded" title="Cari">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <button type="button" id="toggleFilter" class="px-4 py-2 border rounded" title="Filter">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </div>
                </div>

                <style>
                    /* Efek tirai turun-naik, tanpa menyisakan ruang saat tertutup */
                    .filter-panel{max-height:0;opacity:0;overflow:hidden;margin-top:0;transition:max-height .28s ease,opacity .22s ease,margin-top .28s ease}
                    .filter-panel.open{max-height:1000px;opacity:1;margin-top:.75rem}
                </style>
                <div id="filterPanel" class="filter-panel grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Klasifikasi</label>
                        <select id="klasifikasiFilter" name="klasifikasi" class="w-full border rounded p-2">
                            <option value="">Semua Klasifikasi</option>
                            @foreach($klass as $k)
                                <option value="{{ $k }}" {{ request('klasifikasi') == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Jenis</label>
                        <select id="jenisFilter" name="jenis_id" class="w-full border rounded p-2">
                            <option value="">Semua Jenis</option>
                            @foreach($jenis as $j)
                                <option value="{{ $j->id }}" data-klasifikasi="{{ $j->klasifikasi }}" {{ request('jenis_id') == $j->id ? 'selected' : '' }}>{{ $j->jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Provinsi</label>
                        <select id="province_code" class="w-full border rounded p-2"><option value="">Pilih Provinsi</option></select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Kabupaten</label>
                        <select id="district_code" class="w-full border rounded p-2" disabled><option value="">Pilih Kabupaten</option></select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Kecamatan</label>
                        <select id="subdistrict_code" class="w-full border rounded p-2" disabled><option value="">Pilih Kecamatan</option></select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs text-gray-500 mb-1">Desa</label>
                        <select id="village_code" class="w-full border rounded p-2" disabled><option value="">Pilih Desa</option></select>
                    </div>

                    <input type="hidden" id="province_id" name="province_id" value="{{ request('province_id') }}" />
                    <input type="hidden" id="district_id" name="district_id" value="{{ request('district_id') }}" />
                    <input type="hidden" id="subdistrict_id" name="sub_district_id" value="{{ request('sub_district_id') }}" />
                    <input type="hidden" id="village_id" name="village_id" value="{{ request('village_id') }}" />

                    <div class="col-span-1 md:col-span-4 flex justify-end gap-2 mt-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded" title="Terapkan Filter">
                            <i class="fa-solid fa-filter"></i>
                            <span class="ml-1 hidden md:inline">Terapkan Filter</span>
                        </button>
                    </div>
                </div>
            </form>

            <div id="filterDivider" class="border-t border-gray-200 my-3"></div>

            <script>
                // Filter dependent Jenis by selected klasifikasi
                (function(){
                    const panel = document.getElementById('filterPanel');
                    const toggleBtn = document.getElementById('toggleFilter');
                    if(toggleBtn && panel){
                        const divider = document.getElementById('filterDivider');
                        toggleBtn.addEventListener('click', ()=> { panel.classList.toggle('open'); /* divider tetap tampil sebagai batas */ });
                        // Auto-open jika ada parameter filter selain search
                        const hasFilter = '{{ request('klasifikasi') || request('jenis_id') || request('province_id') || request('district_id') || request('sub_district_id') || request('village_id') ? '1' : '' }}';
                        if(hasFilter){ panel.classList.add('open'); }
                    }
                    const klas = document.getElementById('klasifikasiFilter');
                    const jenis = document.getElementById('jenisFilter');
                    if(!klas || !jenis) return;
                    function applyFilter(){
                        const klasVal = klas.value;
                        Array.from(jenis.options).forEach(opt => {
                            if(!opt.value){ opt.hidden = false; return; }
                            const group = opt.getAttribute('data-klasifikasi');
                            opt.hidden = klasVal && group !== klasVal;
                        });
                        // If selected jenis not match parent, reset
                        const sel = jenis.options[jenis.selectedIndex];
                        if(sel && sel.getAttribute('data-klasifikasi') && sel.getAttribute('data-klasifikasi') !== klasVal){
                            jenis.value = '';
                        }
                    }
                    klas.addEventListener('change', applyFilter);
                    applyFilter();
                })();

                // Load wilayah dropdowns (provinsi -> kabupaten -> desa)
                (function(){
                    const prov = document.getElementById('province_code');
                    const dist = document.getElementById('district_code');
                    const vill = document.getElementById('village_code');
                    const provId = document.getElementById('province_id');
                    const distId = document.getElementById('district_id');
                    const villId = document.getElementById('village_id');

                    // fetch helpers
                    async function fetchJson(url){ const r = await fetch(url); return await r.json(); }

                    async function loadProvinces(){
                        const data = await fetchJson('/location/provinces');
                        const arr = Array.isArray(data) ? data : (data.data || []);
                        prov.innerHTML = '<option value="">Pilih Provinsi</option>';
                        arr.forEach(p=>{ const o=document.createElement('option'); o.value=p.code; o.textContent=p.name; o.setAttribute('data-id', p.id); prov.appendChild(o); });
                        if(provId.value){ const sel=arr.find(x=>x.id==provId.value); if(sel){ prov.value = sel.code; } }
                        prov.dispatchEvent(new Event('change'));
                    }

                    prov.addEventListener('change', async ()=>{
                        const code = prov.value; const id = prov.selectedOptions[0]?.getAttribute('data-id')||''; provId.value=id;
                        dist.innerHTML = '<option value="">Pilih Kabupaten</option>'; dist.disabled = true; vill.innerHTML='<option value="">Pilih Desa</option>'; vill.disabled=true; villId.value='';
                        if(!code) return;
                        const data = await fetchJson(`/location/districts/${code}`); const arr = Array.isArray(data)?data:(data.data||[]);
                        arr.forEach(d=>{ const o=document.createElement('option'); o.value=d.code; o.textContent=d.name; o.setAttribute('data-id', d.id); dist.appendChild(o); });
                        dist.disabled=false;
                        if(distId.value){ const sel=arr.find(x=>x.id==distId.value); if(sel){ dist.value = sel.code; } }
                        dist.dispatchEvent(new Event('change'));
                    });

                    dist.addEventListener('change', async ()=>{
                        const code = dist.value; const id = dist.selectedOptions[0]?.getAttribute('data-id')||''; distId.value=id;
                        const sub = document.getElementById('subdistrict_code');
                        sub.innerHTML='<option value="">Pilih Kecamatan</option>'; sub.disabled=true; document.getElementById('subdistrict_id').value='';
                        vill.innerHTML='<option value="">Pilih Desa</option>'; vill.disabled=true; villId.value='';
                        if(!code) return;
                        const kec = await fetchJson(`/location/sub-districts/${code}`); const kecArr = Array.isArray(kec)?kec:(kec.data||[]);
                        kecArr.forEach(s=>{ const o=document.createElement('option'); o.value=s.code; o.textContent=s.name; o.setAttribute('data-id', s.id); sub.appendChild(o); });
                        sub.disabled=false;
                        // preselect if query has sub_district_id
                        const subId = document.getElementById('subdistrict_id').value;
                        if(subId){ const sel=kecArr.find(x=>x.id==subId); if(sel){ sub.value = sel.code; } }
                        sub.dispatchEvent(new Event('change'));
                    });

                    const sub = document.getElementById('subdistrict_code');
                    sub.addEventListener('change', async ()=>{
                        const code = sub.value; const id = sub.selectedOptions[0]?.getAttribute('data-id')||''; document.getElementById('subdistrict_id').value=id;
                        vill.innerHTML='<option value="">Pilih Desa</option>'; vill.disabled=true; villId.value='';
                        if(!code) return;
                        const desa = await fetchJson(`/location/villages/${code}`); const desaArr = Array.isArray(desa)?desa:(desa.data||[]);
                        desaArr.forEach(v=>{ const o=document.createElement('option'); o.value=v.code; o.textContent=v.name; o.setAttribute('data-id', v.id); vill.appendChild(o); });
                        vill.disabled=false;
                        if(villId.value){ const sel=desaArr.find(x=>x.id==villId.value); if(sel){ vill.value = sel.code; } }
                    });

                    vill.addEventListener('change', ()=>{
                        const id = vill.selectedOptions[0]?.getAttribute('data-id')||''; villId.value=id;
                    });

                    loadProvinces();
                })();
            </script>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($items as $item)
                    <a href="{{ route('user.warungku.show', $item->id) }}" class="border rounded p-3 hover:shadow">
                        <img src="{{ $item->foto_url ?? asset('images/statistik.jpg') }}" class="w-full h-40 object-cover rounded mb-2" />
                        <div class="font-medium mb-1">{{ $item->nama_produk }}</div>
                        <div class="text-xs text-gray-600 line-clamp-2 mb-1">{{ Str::limit($item->deskripsi, 90) }}</div>
                        <div class="text-sm text-green-700">Rp {{ number_format($item->harga,0,',','.') }}</div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div>
</x-layout>


