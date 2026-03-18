<?php

if (! function_exists('format_enum_label')) {
    /**
     * Convert a snake_case enum value to a human-readable label, or return 'N/A' when null/empty.
     */
    function format_enum_label(?string $value): string
    {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return ucwords(str_replace('_', ' ', $value));
    }
}
