<?php

use App\Models\LokasiAbsen;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public ?int $lokasiId = null;

    public string $nama_lokasi = '';

    public string $latitude = '';

    public string $longitude = '';

    public int $radius = 100;

    public bool $is_active = true;

    public function mount(): void
    {
        $lokasi = LokasiAbsen::first() ?? new LokasiAbsen([
            'nama_lokasi' => 'Kantor Pusat',
            'latitude' => -6.175392,
            'longitude' => 106.827153,
            'radius' => 100,
            'is_active' => true,
        ]);

        $this->lokasiId = $lokasi->id;
        $this->nama_lokasi = $lokasi->nama_lokasi;
        $this->latitude = (string) $lokasi->latitude;
        $this->longitude = (string) $lokasi->longitude;
        $this->radius = $lokasi->radius;
        $this->is_active = $lokasi->is_active;
    }

    public function save(): void
    {
        $this->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:5|max:10000',
            'is_active' => 'boolean',
        ]);

        $lokasi = LokasiAbsen::updateOrCreate(
            ['id' => $this->lokasiId],
            [
                'nama_lokasi' => $this->nama_lokasi,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->radius,
                'is_active' => $this->is_active,
            ],
        );

        $this->lokasiId = $lokasi->id;
        $this->success('Pengaturan lokasi absensi berhasil disimpan.', position: 'toast-top toast-end');
    }
};
?>

<div>
    {{-- Load Leaflet CSS and JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <x-header title="Pengaturan Lokasi Utama" subtitle="Tentukan titik pusat absensi (Geo-fence) untuk karyawan" separator progress-indicator />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Card --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card shadow separator>
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        <x-icon name="o-map-pin" class="w-5 h-5 text-primary" />
                        <span class="text-base font-bold">Detail Lokasi Kantor</span>
                    </div>
                </x-slot:title>

                <x-form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input wire:model="nama_lokasi" label="Nama Lokasi / Kantor" placeholder="Contoh: Kantor Pusat (HQ)" required />
                        </div>
                        <x-input wire:model="latitude" label="Latitude" placeholder="Contoh: -6.175392" required />
                        <x-input wire:model="longitude" label="Longitude" placeholder="Contoh: 106.827153" required />
                        <x-input wire:model="radius" label="Radius Jangkauan (Meter)" type="number" min="5" placeholder="Contoh: 100" suffix="Meter" required />
                        <div class="flex items-end pb-3">
                            <x-checkbox wire:model="is_active" label="Aktifkan Geo-fencing" class="checkbox-primary" />
                        </div>
                    </div>

                    <x-slot:actions>
                        <x-button label="Simpan Perubahan" class="btn-primary" type="submit" spinner="save" />
                    </x-slot:actions>
                </x-form>
            </x-card>

            {{-- Map Display Card --}}
            <x-card shadow>
                <x-slot:title>
                    <div class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-2">
                            <x-icon name="o-map" class="w-5 h-5 text-success" />
                            <span class="text-base font-bold">Peta Koordinat & Radius</span>
                        </div>
                        <span class="text-[10px] text-base-content/40 font-normal">Klik/geser marker pada peta untuk mengubah lokasi</span>
                    </div>
                </x-slot:title>

                <div class="w-full" style="height: 380px;">
                    <div wire:ignore 
                         x-data="mapPicker({
                             lat: @entangle('latitude'),
                             lng: @entangle('longitude'),
                             radius: @entangle('radius')
                         })"
                         x-init="initMap()"
                         class="w-full h-full rounded-xl border border-base-200 shadow-inner z-10"
                         id="map-container">
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Help Card --}}
        <div>
            <x-card shadow class="bg-base-200/50">
                <x-slot:title>
                    <span class="text-sm font-bold">Panduan Lokasi GPS</span>
                </x-slot:title>
                <div class="space-y-4 text-xs leading-relaxed text-base-content/70">
                    <p>
                        Sistem ini menggunakan koordinat GPS untuk membatasi lokasi check-in/out karyawan.
                    </p>
                    <div class="alert alert-info py-2 px-3 text-[11px] shadow-none">
                        <x-icon name="o-information-circle" class="w-4 h-4 text-info-content shrink-0" />
                        <span><strong>Dinas Luar Bypass:</strong> Karyawan dengan status "Dinas Luar" dibebaskan dari radius ini dan direkam lokasi aslinya.</span>
                    </div>
                    <div class="border-t border-base-300 pt-3">
                        <p class="font-bold text-base-content mb-1">Cara menentukan koordinat:</p>
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Klik pada bagian mana pun di **Peta** untuk memindahkan marker ke posisi tersebut.</li>
                            <li>Atau geser (**drag**) ikon marker langsung ke titik yang diinginkan.</li>
                            <li>Nilai Latitude dan Longitude di kolom input akan otomatis diperbarui secara *real-time*.</li>
                            <li>Sesuaikan **Radius Jangkauan** untuk melihat visual lingkaran wilayah absen pada peta.</li>
                        </ol>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Map Picker Alpine Script --}}
    @script
    <script>
        Alpine.data('mapPicker', (config) => ({
            lat: config.lat,
            lng: config.lng,
            radius: config.radius,
            map: null,
            marker: null,
            circle: null,

            initMap() {
                // Ensure Leaflet is loaded before initializing
                if (typeof L === 'undefined') {
                    setTimeout(() => this.initMap(), 100);
                    return;
                }

                const initialLat = parseFloat(this.lat) || -6.175392;
                const initialLng = parseFloat(this.lng) || 106.827153;
                const initialRadius = parseInt(this.radius) || 100;

                // Create Map
                this.map = L.map('map-container').setView([initialLat, initialLng], 16);

                // OpenStreetMap Tile Layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                // Draggable Marker
                this.marker = L.marker([initialLat, initialLng], { 
                    draggable: true 
                }).addTo(this.map);

                // Radius Circle (visual guide)
                this.circle = L.circle([initialLat, initialLng], {
                    color: '#10b981',      // emerald-500
                    fillColor: '#10b981',  // emerald-500
                    fillOpacity: 0.15,
                    radius: initialRadius
                }).addTo(this.map);

                // Handle Marker Drag
                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.lat = pos.lat.toFixed(8);
                    this.lng = pos.lng.toFixed(8);
                    this.circle.setLatLng(pos);
                    this.map.panTo(pos);
                });

                // Handle Map Click (moves marker & updates coordinates)
                this.map.on('click', (e) => {
                    const pos = e.latlng;
                    this.marker.setLatLng(pos);
                    this.circle.setLatLng(pos);
                    this.lat = pos.lat.toFixed(8);
                    this.lng = pos.lng.toFixed(8);
                    this.map.panTo(pos);
                });

                // Watch external inputs (latitude changes)
                this.$watch('lat', (val) => {
                    const lat = parseFloat(val);
                    const lng = parseFloat(this.lng);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const pos = [lat, lng];
                        this.marker.setLatLng(pos);
                        this.circle.setLatLng(pos);
                        this.map.setView(pos);
                    }
                });

                // Watch external inputs (longitude changes)
                this.$watch('lng', (val) => {
                    const lat = parseFloat(this.lat);
                    const lng = parseFloat(val);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const pos = [lat, lng];
                        this.marker.setLatLng(pos);
                        this.circle.setLatLng(pos);
                        this.map.setView(pos);
                    }
                });

                // Watch radius changes
                this.$watch('radius', (val) => {
                    const radius = parseInt(val);
                    if (!isNaN(radius) && radius > 0) {
                        this.circle.setRadius(radius);
                    }
                });
            }
        }));
    </script>
    @endscript
</div>
