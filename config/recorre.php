<?php

return [
    'admin_email' => env('RECORRE_ADMIN_EMAIL', env('OPTIMIZE_CLEAR_EMAIL')),
    // Compatible con la API de búsqueda de Nominatim; puede cambiarse desde el entorno.
    'geocoding_url' => env('RECORRE_GEOCODING_URL', 'https://nominatim.openstreetmap.org/search'),
];
