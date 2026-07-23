<?php
use App\Models\Absen;
use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Models\PengajuanAbsen;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
?>

<div class="space-y-6">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-base-content tracking-tight">HR Pulse</h1>
            <p class="text-base text-base-content/70 mt-1">Monitoring data karyawan dan absensi secara real-time.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-base-content/70 bg-base-200 px-4 py-2 rounded-lg flex items-center gap-2 border border-base-300">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-calendar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                <?php echo e(now()->isoFormat('MMMM YYYY')); ?>

            </span>
        </div>
    </header>

    <!-- Row 1: Summary Cards (Minimalist 4 Cards) -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Total Karyawan -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-xl md:text-2xl font-bold text-base-content mt-0.5"><?php echo e(number_format($this->counts['totalAktif'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-users'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
        </div>

        <!-- Card 2: Karyawan Tetap -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Karyawan Tetap</p>
                <h3 class="text-xl md:text-2xl font-bold text-success mt-0.5"><?php echo e(number_format($this->counts['tetap'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-success/10 text-success flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check-badge'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
        </div>

        <!-- Card 3: Karyawan Kontrak -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Karyawan Kontrak</p>
                <h3 class="text-xl md:text-2xl font-bold text-info mt-0.5"><?php echo e(number_format($this->counts['kontrak'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-info/10 text-info flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-identification'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
        </div>

        <!-- Card 4: Kontrak Hampir Habis -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Kontrak Exipring</p>
                <h3 class="text-xl md:text-2xl font-bold text-error mt-0.5"><?php echo e(number_format($this->kontrakExpiringCount)); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Row 2: Charts -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Annual Attendance Line Chart -->
        <div class="lg:col-span-8 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-base-content">Statistik Absensi Tahunan</h3>
                    <select wire:model.live="selectedYear" class="select select-bordered select-xs text-xs font-semibold text-base-content/80">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->listTahun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($y); ?>">Tahun <?php echo e($y); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                <div class="h-64 w-full relative px-2 pb-8 pt-4">
                    <!-- Grid Lines -->
                    <div class="absolute inset-x-0 bottom-8 border-b border-base-300"></div>
                    <div class="absolute inset-x-0 top-1/2 border-b border-base-300/40"></div>
                    <div class="absolute inset-x-0 top-1/4 border-b border-base-300/40"></div>

                    <!-- Line Chart SVG -->
                    <svg class="w-full h-48 overflow-visible" viewBox="0 0 1000 200" preserveAspectRatio="none">
                        <!-- Izin (Primary Color) -->
                        <path d="<?php echo e($this->annualStats['izinPath']); ?>" fill="none" stroke="currentColor" class="text-primary" stroke-width="3" stroke-linecap="round" />
                        <!-- Sakit (Warning Color) -->
                        <path d="<?php echo e($this->annualStats['sakitPath']); ?>" fill="none" stroke="currentColor" class="text-warning" stroke-width="3" stroke-linecap="round" />
                        <!-- Alpa (Error Color) -->
                        <path d="<?php echo e($this->annualStats['alpaPath']); ?>" fill="none" stroke="currentColor" class="text-error" stroke-width="3" stroke-linecap="round" />
                    </svg>

                    <!-- X-Axis Labels -->
                    <div class="flex justify-between mt-2 px-1 text-xs text-base-content/70 font-medium">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->annualStats['months']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <span><?php echo e($month); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Minimal Legend -->
            <div class="flex justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-primary rounded-full"></div>
                    <span class="text-xs text-base-content/70">Izin</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-warning rounded-full"></div>
                    <span class="text-xs text-base-content/70">Sakit</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-error rounded-full"></div>
                    <span class="text-xs text-base-content/70">Alpa</span>
                </div>
            </div>
        </div>

        <!-- Right: Attendance Distribution Donut Chart -->
        <div class="lg:col-span-4 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <h3 class="text-xl font-semibold text-base-content mb-6">Distribusi Kehadiran</h3>
            
            <div class="flex-grow flex items-center justify-center relative min-h-[180px]">
                <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-base-200" stroke-width="12"></circle>
                    <!-- Cuti (Success) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-success" stroke-width="12" stroke-dasharray="<?php echo e($this->attendanceDistribution['dashCuti']); ?>"></circle>
                    <!-- Alpa (Error) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-error" stroke-width="12" stroke-dasharray="<?php echo e($this->attendanceDistribution['dashAlpa']); ?>" stroke-dashoffset="<?php echo e($this->attendanceDistribution['offsetAlpa']); ?>"></circle>
                    <!-- Sakit (Warning) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-warning" stroke-width="12" stroke-dasharray="<?php echo e($this->attendanceDistribution['dashSakit']); ?>" stroke-dashoffset="<?php echo e($this->attendanceDistribution['offsetSakit']); ?>"></circle>
                    <!-- Izin (Primary) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-primary" stroke-width="12" stroke-dasharray="<?php echo e($this->attendanceDistribution['dashIzin']); ?>" stroke-dashoffset="<?php echo e($this->attendanceDistribution['offsetIzin']); ?>"></circle>
                </svg>
                <div class="absolute text-center">
                    <p class="text-[10px] text-base-content/60 uppercase tracking-wider font-semibold">Status</p>
                    <p class="text-lg font-bold text-base-content">Absensi</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-success"></div>
                    <span class="text-xs text-base-content/70">Cuti (<?php echo e($this->attendanceDistribution['cutiPct']); ?>%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-error"></div>
                    <span class="text-xs text-base-content/70">Alpa (<?php echo e($this->attendanceDistribution['alpaPct']); ?>%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-warning"></div>
                    <span class="text-xs text-base-content/70">Sakit (<?php echo e($this->attendanceDistribution['sakitPct']); ?>%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                    <span class="text-xs text-base-content/70">Izin (<?php echo e($this->attendanceDistribution['izinPct']); ?>%)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 3: Lists / Tables -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Contract Expiry Table -->
        <div class="lg:col-span-8 bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-base-300 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-base-content">Kontrak Kerja Mau Jatuh Tempo</h3>
                <a href="<?php echo e(route('kontrak.index')); ?>" wire:navigate class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    Lihat Semua
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-right'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-base-200/60 text-xs font-semibold text-base-content/70 uppercase tracking-wider">
                            <th class="px-6 py-3">Nama Karyawan</th>
                            <th class="px-6 py-3">Departemen</th>
                            <th class="px-6 py-3">Tanggal Berakhir</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-300/60 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->expiringContracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $karyawan = $item['karyawan'];
                                $kontrak = $item['kontrak'];
                                $isUrgent = $item['is_urgent'];
                            ?>
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold text-xs">
                                            <?php echo e(strtoupper(substr($karyawan?->nama_karyawan ?? 'K', 0, 2))); ?>

                                        </div>
                                        <span class="font-medium text-base-content"><?php echo e($karyawan?->nama_karyawan ?? '-'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-base-content/70">
                                    <?php echo e($karyawan?->jabatan?->nama_jabatan ?? 'General'); ?>

                                </td>
                                <td class="px-6 py-4 text-base-content/70">
                                    <?php echo e($kontrak->tanggal_akhir ? $kontrak->tanggal_akhir->format('d M Y') : '-'); ?>

                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if($isUrgent): ?>
                                        <span class="badge badge-error badge-sm font-semibold">Urgent</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary badge-outline badge-sm font-semibold">In Review</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-base-content/60">
                                    Tidak ada data kontrak yang mau jatuh tempo dalam waktu dekat.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Top Performance List -->
        <div class="lg:col-span-4 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-semibold text-base-content mb-6">5 Top Performa Absensi</h3>
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->topPerformers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-base-200/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-secondary text-secondary-content flex items-center justify-center font-bold text-xs">
                                    <?php echo e(strtoupper(substr($item['karyawan']->nama_karyawan, 0, 2))); ?>

                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-base-content"><?php echo e($item['karyawan']->nama_karyawan); ?></p>
                                    <p class="text-xs text-base-content/60"><?php echo e($item['karyawan']->jabatan?->nama_jabatan ?? 'Staf'); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-primary mb-1"><?php echo e($item['rate']); ?>%</p>
                                <div class="w-16 h-1.5 bg-base-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary" style="width: <?php echo e($item['rate']); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-xs text-base-content/60 text-center py-4">Belum ada data karyawan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/914987f1.blade.php ENDPATH**/ ?>