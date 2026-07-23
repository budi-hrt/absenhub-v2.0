<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Profil Saya')] class extends Component {
    use Toast, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $telp_karyawan = '';
    public string $bio = '';

    public $photo;

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        $this->name = $karyawan?->nama_karyawan ?? $user->name;
        $this->email = $karyawan?->email_karyawan ?? $user->email;
        $this->telp_karyawan = $karyawan?->telp_karyawan ?? '';
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telp_karyawan' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($user->karyawan) {
            $user->karyawan->update([
                'nama_karyawan' => $this->name,
                'email_karyawan' => $this->email,
                'telp_karyawan' => $this->telp_karyawan,
            ]);
        }

        $this->success('Data profil berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $user = auth()->user();
        $path = $this->photo->store('karyawan-foto', 'public');

        $user->update(['face_photo' => $path]);

        if ($user->karyawan) {
            $user->karyawan->update(['foto_karyawan' => $path]);
        }

        $this->success('Foto profil berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->success('Kata sandi berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function with(): array
    {
        $user = auth()->user();
        $karyawan = $user->karyawan()->with(['jabatan', 'status'])->first();

        return [
            'user' => $user,
            'karyawan' => $karyawan,
        ];
    }
}; ?>

<div>
    <?php if(auth()->user()->hasRole('karyawan')): ?>
        
        <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Profil Saya','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $attributes = $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $component = $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>

        <div class="relative bg-base-100 rounded-[32px] overflow-hidden border border-base-300 shadow-sm mb-6 md:mb-8">
            
            <div class="h-32 md:h-48 bg-gradient-to-r from-primary to-secondary relative">
                <div class="absolute inset-0 bg-black/10"></div>
            </div>

            
            <div class="px-6 md:px-10 pb-8 relative">
                <div class="flex flex-col md:flex-row md:items-end gap-4 md:gap-6 -mt-16 md:-mt-20 mb-6">
                    <div class="relative w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-base-100 bg-base-200 overflow-hidden shrink-0 shadow-md">
                        <?php if($user->face_photo): ?>
                            <img src="<?php echo e(Storage::url($user->face_photo)); ?>" alt="Profile Photo" class="w-full h-full object-cover" />
                        <?php elseif($karyawan && $karyawan->foto_karyawan): ?>
                            <img src="<?php echo e(Storage::url($karyawan->foto_karyawan)); ?>" alt="Profile Photo" class="w-full h-full object-cover" />
                        <?php else: ?>
                            <img src="https://i.pravatar.cc/150?img=9" alt="Default Avatar" class="w-full h-full object-cover" />
                        <?php endif; ?>
                    </div>

                    <div class="flex-1 pb-2">
                        <h2 class="text-2xl md:text-3xl font-bold text-base-content"><?php echo e($karyawan?->nama_karyawan ?? $user->name); ?></h2>
                        <p class="text-primary font-medium text-sm md:text-base mt-1"><?php echo e($karyawan?->jabatan?->nama_jabatan ?? 'Karyawan'); ?></p>
                    </div>

                    <div class="pb-2">
                        <div class="badge <?php echo e($karyawan?->is_active ? 'badge-success' : 'badge-error'); ?> badge-lg font-semibold">
                            <?php echo e($karyawan?->is_active ? 'Aktif' : 'Non-Aktif'); ?>

                        </div>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-6">
                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-identification'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">NIK</p>
                        </div>
                        <p class="text-base font-medium pl-8"><?php echo e($karyawan?->nik ?? '-'); ?></p>
                    </div>

                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-briefcase'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Status Pekerjaan</p>
                        </div>
                        <p class="text-base font-medium pl-8"><?php echo e($karyawan?->status?->nama_status ?? '-'); ?></p>
                    </div>

                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-envelope'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Email</p>
                        </div>
                        <p class="text-base font-medium pl-8"><?php echo e($karyawan?->email_karyawan ?? $user->email); ?></p>
                    </div>

                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-phone'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Nomor Telepon</p>
                        </div>
                        <p class="text-base font-medium pl-8"><?php echo e($karyawan?->telp_karyawan ?? '-'); ?></p>
                    </div>

                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-calendar-days'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Tanggal Lahir</p>
                        </div>
                        <p class="text-base font-medium pl-8">
                            <?php if($karyawan && $karyawan->tanggal_lahir): ?>
                                <?php echo e(Carbon::parse($karyawan->tanggal_lahir)->locale('id')->isoFormat('D MMMM Y')); ?>

                                <span class="text-xs text-base-content/50 ml-1">(<?php echo e(Carbon::parse($karyawan->tanggal_lahir)->age); ?> tahun)</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>

                    
                    <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                        <div class="flex items-center gap-3 mb-1">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-building-office-2'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
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
                            <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Bergabung Sejak</p>
                        </div>
                        <p class="text-base font-medium pl-8">
                            <?php if($karyawan && $karyawan->tanggal_masuk): ?>
                                <?php echo e(Carbon::parse($karyawan->tanggal_masuk)->locale('id')->isoFormat('D MMMM Y')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="mb-24 flex flex-col gap-3">
            <form wire:submit="logout">
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Keluar (Logout)','icon' => 'o-arrow-right-on-rectangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'btn-error w-full shadow-sm text-error-content font-bold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
            </form>
        </div>
    <?php else: ?>
        
        <div class="space-y-6">
            <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Pengaturan Profil Admin','subtitle' => 'Kelola data pribadi, foto profil, dan kata sandi akun Anda','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $attributes = $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $component = $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Column: Profile Overview -->
                <aside class="lg:col-span-4 flex flex-col items-center text-center p-6 bg-base-100 rounded-2xl border border-base-300 shadow-sm relative">
                    <div class="relative group">
                        <div class="w-36 h-36 rounded-full border-4 border-base-200 overflow-hidden bg-base-200 relative shadow-md">
                            <?php if($photo): ?>
                                <img src="<?php echo e($photo->temporaryUrl()); ?>" alt="Preview" class="w-full h-full object-cover" />
                            <?php elseif($user->face_photo): ?>
                                <img src="<?php echo e(Storage::url($user->face_photo)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover" />
                            <?php elseif($karyawan && $karyawan->foto_karyawan): ?>
                                <img src="<?php echo e(Storage::url($karyawan->foto_karyawan)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover" />
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary font-bold text-2xl">
                                    <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Camera upload trigger -->
                        <label for="profile-photo-input" class="absolute bottom-1 right-1 bg-primary text-primary-content p-2.5 rounded-full shadow-lg hover:bg-primary/90 transition-all cursor-pointer active:scale-95 flex items-center justify-center">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-camera'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </label>
                        <input type="file" id="profile-photo-input" wire:model="photo" class="hidden" accept="image/*">
                    </div>

                    <div wire:loading wire:target="photo" class="mt-2 text-xs font-semibold text-primary animate-pulse">
                        Mengunggah foto...
                    </div>

                    <h2 class="mt-4 font-bold text-xl md:text-2xl text-base-content"><?php echo e($user->name); ?></h2>
                    <p class="text-xs md:text-sm text-base-content/70 mt-0.5">
                        <?php echo e($user->email); ?>

                    </p>

                    <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full uppercase tracking-wider">
                            <?php echo e($user->getRoleNames()->first() ?? 'ADMIN'); ?>

                        </span>
                    </div>
                </aside>

                <!-- Right Column: Forms -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Card 1: Data Pribadi -->
                    <div class="bg-base-100 rounded-2xl border border-base-300 shadow-sm overflow-hidden">
                        <div class="h-1 bg-primary w-full"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-6">
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-user'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-primary']); ?>
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
                                <h3 class="text-lg font-bold text-base-content">Perbarui Data Pribadi</h3>
                            </div>

                            <form wire:submit="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Nama Lengkap</label>
                                    <input type="text" wire:model="name" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-[11px] mt-0.5"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Alamat Email</label>
                                    <input type="email" wire:model="email" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-[11px] mt-0.5"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="text-xs font-semibold text-base-content/80">Bio / Catatan Singkat</label>
                                    <textarea wire:model="bio" rows="3" placeholder="Tuliskan catatan singkat tentang Anda..." class="textarea textarea-bordered text-xs font-medium focus:textarea-primary resize-none"></textarea>
                                </div>

                                <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm text-xs gap-1.5 shadow-sm">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
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
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Card 2: Ubah Kata Sandi -->
                    <div class="bg-base-100 rounded-2xl border border-base-300 shadow-sm overflow-hidden">
                        <div class="h-1 bg-primary w-full"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-6">
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-lock-closed'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-primary']); ?>
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
                                <h3 class="text-lg font-bold text-base-content">Ubah Kata Sandi</h3>
                            </div>

                            <form wire:submit="updatePassword" class="space-y-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Kata Sandi Saat Ini</label>
                                    <input type="password" wire:model="current_password" placeholder="••••••••" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-[11px] mt-0.5"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-semibold text-base-content/80">Kata Sandi Baru</label>
                                        <input type="password" wire:model="new_password" placeholder="Min. 8 karakter" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                        <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-[11px] mt-0.5"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-semibold text-base-content/80">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" wire:model="new_password_confirmation" placeholder="Ulangi kata sandi" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    </div>
                                </div>

                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="btn btn-neutral btn-sm text-xs gap-1.5 shadow-sm">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-key'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
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
                                        Perbarui Kata Sandi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\pages\karyawan\profile.blade.php ENDPATH**/ ?>