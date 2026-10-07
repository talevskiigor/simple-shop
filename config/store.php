<?php

return [
    // Explicitly enabled in local/staging environments; existing live behavior is unchanged.
    'sandbox' => (bool) env('STORE_SANDBOX', false),
];
