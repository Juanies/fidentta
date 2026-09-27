<?php

use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\Attributes\Modelable;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads; // 1MB Max

    #[Validate('image|max:1024')]
    public $photo;

    #[Modelable]
    public array $registro = [
        'name' => 'Cafe laté',
        'errora' => false,
        'logo' => [
            'type' => 'text',
            'content' => 'C',
            'url' => null,
        ],
        'sellos' => 8,
        'recompensa' => 'Bebida gratis',
    ];
    public function updatedPhoto()
    {
        $this->validate();
        $path = $this->photo->store('photos', 'public');
        $this->registro['logo']['type'] = 'img';
        $this->registro['logo']['url'] = Storage::url($path);
    }

    protected function rules()
    {
        return ['registro.name' => 'required|min:3|max:10'];
    }

    public function updatedRegistroName(string $name)
    {
        $this->validateOnly('registro.name');

        $this->registro['logo']['content'] = substr($name, 0, 1);
    }
};

?>

<div>
    <div class="max-w-2xl ">
        <h2 class="text-3xl  font-semibold tracking-tight text-text sm:text-5xl">Empecemos por tu negocio
        </h2>
        <p class="mt-4 text-base leading-7 text-text-secondary/70 sm:text-lg">Estos datos aparecerán en la tarjeta
            de tus clientes.
        </p>
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <div class="mt-8 space-y-2">
                <label for="nombre-negocio" class="block text-sm font-semibold text-text">Nombre del negocio</label>
                <input wire:model.live='registro.name' id="nombre-negocio" type="text"
                    class="w-full rounded-xl border border-white/15 bg-white/[0.06] px-4 py-3 text-text outline-none transition placeholder:text-text-secondary/50 focus:border-fidentta-cyan focus:bg-white/[0.1] focus:ring-2 focus:ring-fidentta-cyan/20">
                @error('registro.name')
                    <span class="block mt-1 text-sm text-red-400"> {{ $message }} </span>
                @enderror
                <span class="block text-xs text-text-secondary/60">Puedes editarlo cuando quieras.</span>
            </div>
            <div class="mt-8 space-y-3">
                <div>
                    <p class="text-sm font-semibold text-text">Logo (Opcional)</p>
                    <p class="mt-1 text-xs text-text-secondary/60">Elige cómo quieres identificar tu tarjeta.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">


                </div>
                <div class="mt-4">
                    <input wire:model.live='photo' type="file" accept="image/*"
                        class="block w-full text-sm text-text-secondary/60 file:mr-4 file:rounded-lg file:border-0 file:bg-fidentta-cyan file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-fidentta-cyan/90" />
                </div>
                @error('photo')
                    <span class="error">{{ $message }}</span>
                @enderror

                <span class="block text-xs text-text-secondary/60">Recomendado: PNG o SVG con fondo
                    transparente.</span>


            </div>
        </div>
        <div class="col-span-2">
            @php
                $sellosPreview = $registro['sellos'] ?? 8;
                $previewBackground =
                    $registro['tarjeta'] === 'upload'
                        ? $tarjeta['fondo'] ?? 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)'
                        : 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)';
                $previewTextColor = $tarjeta['texto'] ?? '#F8FAFC';
            @endphp

            <div class="rounded-2xl border border-white/10 p-4 h-fit shadow-2xl shadow-fidentta-purple/10"
                style="background: {{ $previewBackground }}; color: {{ $previewTextColor }};">
                <div class="flex items-center gap-4">
                    @if ($registro['logo']['url'])
                        <img src="{{ $registro['logo']['url'] }}" alt="Logo del negocio"
                            class="h-10 w-10 rounded-full border border-white/20 bg-white/10 object-cover" />
                    @else
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-lg font-bold">
                            {{ strtoupper(substr($registro['logo']['content'] ?? 'Cafe laté', 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-base font-semibold">{{ $registro['name'] }}</span>
                </div>

                <div class="mt-5 grid grid-cols-4 gap-3">
                    @for ($i = 0; $i < $sellosPreview; $i++)
                        <div class="flex items-center justify-center">
                            <div
                                class="h-14 w-14 rounded-full border border-white/30 bg-white/10 shadow-inner shadow-white/20">
                            </div>
                        </div>
                    @endfor
                </div>

                <p class="mt-5 text-sm opacity-85">Completa los {{ $registro['sellos'] ?? 8 }} sellos y consigue:
                    {{ $registro['recompensa'] ?? 'tu recompensa' }}</p>
                <div class="bg-white p-4 rounded-lg mb-8 w-fit mx-auto mt-12">
                    <svg width="120" height="120" viewBox="0 0 21 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.75 8.75C12.693 8.75 13.164 8.75 13.457 8.457C13.75 8.164 13.75 7.693 13.75 6.75C13.75 5.807 13.75 5.336 14.043 5.043C14.336 4.75 14.807 4.75 15.75 4.75M4.75 11.75H6.75C7.693 11.75 8.164 11.75 8.457 12.043C8.75 12.336 8.75 12.807 8.75 13.75V15.75M5.125 15.5H5M4.75 19.75C4.286 19.75 4.053 19.75 3.858 19.728C3.07013 19.6392 2.33575 19.2855 1.77511 18.7249C1.21447 18.1643 0.860796 17.4299 0.772 16.642C0.75 16.447 0.75 16.214 0.75 15.75M15.75 19.75C16.214 19.75 16.447 19.75 16.642 19.728C17.4299 19.6392 18.1643 19.2855 18.7249 18.7249C19.2855 18.1643 19.6392 17.4299 19.728 16.642C19.75 16.447 19.75 16.214 19.75 15.75M4.75 0.75C4.286 0.75 4.053 0.75 3.858 0.772C3.07013 0.860796 2.33575 1.21447 1.77511 1.77511C1.21447 2.33575 0.860796 3.07013 0.772 3.858C0.75 4.053 0.75 4.286 0.75 4.75M15.75 0.75C16.214 0.75 16.447 0.75 16.642 0.772C17.4299 0.860796 18.1643 1.21447 18.7249 1.77511C19.2855 2.33575 19.6392 3.07013 19.728 3.858C19.75 4.053 19.75 4.286 19.75 4.75M5.043 5.043C4.75 5.336 4.75 5.807 4.75 6.75C4.75 7.693 4.75 8.164 5.043 8.457C5.336 8.75 5.807 8.75 6.75 8.75C7.693 8.75 8.164 8.75 8.457 8.457C8.75 8.164 8.75 7.693 8.75 6.75C8.75 5.807 8.75 5.336 8.457 5.043C8.164 4.75 7.693 4.75 6.75 4.75C5.807 4.75 5.336 4.75 5.043 5.043ZM5.25 15.5C5.25 15.5663 5.22366 15.6299 5.17678 15.6768C5.12989 15.7237 5.0663 15.75 5 15.75C4.9337 15.75 4.87011 15.7237 4.82322 15.6768C4.77634 15.6299 4.75 15.5663 4.75 15.5C4.75 15.4337 4.77634 15.3701 4.82322 15.3232C4.87011 15.2763 4.9337 15.25 5 15.25C5.0663 15.25 5.12989 15.2763 5.17678 15.3232C5.22366 15.3701 5.25 15.4337 5.25 15.5ZM12.043 12.043C11.75 12.336 11.75 12.807 11.75 13.75C11.75 14.693 11.75 15.164 12.043 15.457C12.336 15.75 12.807 15.75 13.75 15.75C14.693 15.75 15.164 15.75 15.457 15.457C15.75 15.164 15.75 14.693 15.75 13.75C15.75 12.807 15.75 12.336 15.457 12.043C15.164 11.75 14.693 11.75 13.75 11.75C12.807 11.75 12.336 11.75 12.043 12.043Z"
                            stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

            </div>
        </div>
    </div>


</div>
