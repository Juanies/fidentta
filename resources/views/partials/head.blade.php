<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $isLandingPage = request()->routeIs('home');
    $seoTitle = $isLandingPage
        ? 'Fidentta | Fidelización digital para negocios locales'
        : (filled($title ?? null)
            ? $title . ' - ' . config('app.name', 'Fidentta')
            : config('app.name', 'Fidentta'));
    $seoDescription =
        'Crea tarjetas digitales de fidelidad con sellos, recompensas y un código QR para cada local. Sin app para tus clientes.';
    $canonicalUrl = rtrim(config('app.url'), '/') . '/';
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $isLandingPage ? $seoDescription : 'Área privada de gestión de Fidentta.' }}">
<meta name="robots" content="{{ $isLandingPage ? 'index, follow' : 'noindex, nofollow' }}">
@if ($isLandingPage)
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Fidentta">
    <meta property="og:locale" content="es_ES">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:image:alt" content="Logotipo de Fidentta">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Fidentta',
            'url' => $canonicalUrl,
            'logo' => asset('logo.png'),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
<script>
    window.Flux.applyAppearance('light');
</script>
