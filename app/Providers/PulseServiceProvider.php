<?php

use Laravel\Pulse\Facades\Pulse;

Pulse::authorize(function ($request) {
    return auth()->check();
});
