<?php

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;

new #[Title('Pagos')] class extends Component {
    #[Computed]
    public function currentSubscription(): ?Subscription
    {
        return Auth::user()->subscription('fidentta');
    }

    #[Computed]
    public function invoices()
    {
        return Auth::user()->invoices();
    }

    public function cancelSubscription(): void
    {
        $subscription = Auth::user()->subscription('fidentta');

        if (!$subscription || !$subscription->valid() || $subscription->onGracePeriod()) {
            Flux::toast(variant: 'warning', text: __('No hay una suscripción activa que se pueda cancelar.'));

            return;
        }

        try {
            $subscription->cancel();
            Flux::toast(variant: 'success', text: __('La suscripción se cancelará al final del periodo actual.'));
        } catch (ApiConnectionException | ApiErrorException $exception) {
            report($exception);
            Flux::toast(variant: 'danger', text: __('No se pudo cancelar la suscripción. Inténtalo de nuevo.'));
        }
    }
}; ?>

<section class="w-full text-ink">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Pagos') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Pagos')" :subheading="__('Consulta tu suscripción y el historial de facturas')">
        <section class="border-y border-border py-5" aria-labelledby="subscription-heading">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 id="subscription-heading" class="text-sm font-semibold text-ink">{{ __('Suscripción actual') }}
                    </h2>
                    @if ($this->currentSubscription)
                        <p class="mt-2 text-sm text-ink2">
                            {{ match ($this->currentSubscription->stripe_status) {
                                'active' => __('Activa'),
                                'trialing' => __('Prueba gratuita'),
                                'past_due' => __('Pago pendiente'),
                                'incomplete' => __('Pago incompleto'),
                                default => ucfirst(str_replace('_', ' ', $this->currentSubscription->stripe_status)),
                            } }}
                        </p>
                        @if ($this->currentSubscription->onGracePeriod())
                            <p class="mt-2 text-sm text-amber-800">
                                {{ __('Se cancelará el :date.', ['date' => $this->currentSubscription->ends_at?->format('d/m/Y')]) }}
                            </p>
                        @endif
                    @else
                        <p class="mt-2 text-sm text-ink2">{{ __('No tienes una suscripción registrada.') }}</p>
                    @endif
                </div>

                @if ($this->currentSubscription?->valid() && !$this->currentSubscription->onGracePeriod())
                    <flux:button variant="danger" wire:click="cancelSubscription"
                        wire:confirm="{{ __('¿Quieres cancelar la suscripción? Seguirá activa hasta el final del periodo actual.') }}"
                        wire:loading.attr="disabled" wire:target="cancelSubscription">
                        {{ __('Cancelar suscripción') }}
                    </flux:button>
                @endif
            </div>
        </section>

        <section class="mt-8" aria-labelledby="invoices-heading">
            <div class="flex items-baseline justify-between gap-4">
                <h2 id="invoices-heading" class="text-sm font-semibold text-ink">{{ __('Facturas anteriores') }}</h2>
                <span class="text-xs text-ink2">{{ __('Últimas 24 facturas pagadas') }}</span>
            </div>

            <div class="mt-4 divide-y divide-border border-y border-border">
                @forelse ($this->invoices as $invoice)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                        <div>
                            <p class="text-sm font-medium text-ink">
                                {{ __('Factura :number', ['number' => $invoice->number ?: $invoice->id]) }}
                            </p>
                            <p class="mt-1 text-xs text-ink2">{{ $invoice->date()->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-ink">{{ $invoice->total() }}</span>
                            @if ($invoice->hosted_invoice_url)
                                <a href="{{ $invoice->hosted_invoice_url }}" target="_blank" rel="noopener noreferrer"
                                    class="text-sm font-semibold text-fidentta-teal underline underline-offset-4">
                                    {{ __('Ver factura') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-5 text-sm text-ink2">{{ __('Todavía no hay facturas pagadas.') }}</p>
                @endforelse
            </div>
        </section>
    </x-pages::settings.layout>
</section>
