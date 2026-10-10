@php
    $status = $exception->getStatusCode();
    [$heading, $description] = match ($status) {
        503 => [
            'Fidentta no está disponible ahora',
            'Estamos realizando tareas de mantenimiento. Vuelve a intentarlo en unos minutos.',
        ],
        default => [
            'Algo no ha salido bien',
            'Se ha producido un problema al cargar la página. Vuelve a intentarlo en unos instantes.',
        ],
    };
@endphp

@include('errors.page', compact('status', 'heading', 'description'))
