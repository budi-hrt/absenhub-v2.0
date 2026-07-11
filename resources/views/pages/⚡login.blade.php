<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.empty')] #[Title('Login')] class extends Component {
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required')]
    public string $password = '';

    public function mount()
    {
        if (auth()->user()) {
            return redirect(auth()->user()->hasRole('karyawan') ? '/dashboard' : '/');
        }
    }

    public function login()
    {
        $credentials = $this->validate();

        if (auth()->attempt($credentials)) {
            request()->session()->regenerate();

            $user = auth()->user();
            return redirect()->intended($user->hasRole('karyawan') ? '/dashboard' : '/');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
};
?>

<div class="min-h-screen flex items-center justify-center bg-base-200 px-4">
    <div class="bg-base-100 shadow-2xl rounded-2xl w-full max-w-md p-10">
        <div class="text-center mb-8">
            <span class="text-3xl font-black text-primary">AbsenHub</span>
            <p class="text-base-content/50 text-sm mt-2">Silakan login untuk melanjutkan</p>
        </div>

        <form wire:submit="login" class="flex flex-col gap-5">
            <div>
                <label class="text-sm font-medium text-base-content mb-1 block">E-mail</label>
                <input type="email" wire:model="email" placeholder="E-mail" autocomplete="email"
                    class="input input-bordered w-full focus:input-primary" />
                @error('email') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-base-content mb-1 block">Password</label>
                <div x-data="{ show: false }" class="join w-full">
                    <input x-bind:type="show ? 'text' : 'password'" wire:model="password" placeholder="Password" autocomplete="current-password"
                        class="input input-bordered join-item w-full focus:input-primary" />
                    <button type="button" x-on:click="show = !show"
                        class="btn btn-outline join-item border-base-300">
                        <span x-show="!show"><x-icon name="o-eye-slash" class="w-5 h-5" /></span>
                        <span x-show="show"><x-icon name="o-eye" class="w-5 h-5" /></span>
                    </button>
                </div>
                @error('password') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                class="btn btn-primary w-full py-3 font-semibold mt-2">
                <span wire:loading.remove>Login</span>
                <span wire:loading class="loading loading-spinner loading-sm"></span>
            </button>
        </form>
    </div>
</div>
