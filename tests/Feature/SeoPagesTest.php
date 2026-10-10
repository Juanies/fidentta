<?php

use Illuminate\Support\Facades\Route;

test('landing page exposes search and social metadata', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<title>Fidentta | Fidelización digital para negocios locales</title>', false)
        ->assertSee('<meta name="robots" content="index, follow">', false)
        ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/">', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('"@type":"Organization"', false);
});

test('not found page is helpful and excluded from search indexing', function () {
    config(['app.debug' => false]);

    $this->get('/pagina-que-no-existe')
        ->assertNotFound()
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
        ->assertSee('No encontramos esa página')
        ->assertSee('Ir al inicio');
});

test('server error page is helpful and excluded from search indexing', function () {
    config(['app.debug' => false]);
    Route::get('/test-server-error', fn () => abort(503));

    $this->get('/test-server-error')
        ->assertStatus(503)
        ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
        ->assertSee('Fidentta no está disponible ahora')
        ->assertSee('Iniciar sesión');
});
