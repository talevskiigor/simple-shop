<?php

return [
    // Explicitly enabled in local/staging environments; existing live behavior is unchanged.
    'sandbox' => (bool) env('STORE_SANDBOX', false),
    // Permit public catalog indexing while retaining sandbox/payment safeguards.
    'allow_indexing' => (bool) env('STORE_ALLOW_INDEXING', false),
    // Production promotion must not implicitly turn third-party tracking on.
    'tracking_enabled' => (bool) env('STORE_TRACKING_ENABLED', false),
];
