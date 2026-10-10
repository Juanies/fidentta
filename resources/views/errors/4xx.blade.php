@php
    $status = $exception->getStatusCode();
    [$heading, $description] = match ($status) {
        403 => ['No tienes acceso a esta página', 'Tu cuenta no tiene permiso para ver este contenido.'],
        404 => ['No encontramos esa página', 'Puede que el enlace haya cambiado o que la dirección esté mal escrita.'],
        419 => ['La sesión ha caducado', 'Vuelve a iniciar el proceso para continuar de forma segura.'],
        429 => ['Demasiadas solicitudes', 'Espera un momento antes de volver a intentarlo.'],
        default => ['No podemos mostrar esta página', 'Comprueba la dirección o vuelve al inicio de Fidentta.'],
    };
@endphp

@include('errors.page', compact('status', 'heading', 'description'))
