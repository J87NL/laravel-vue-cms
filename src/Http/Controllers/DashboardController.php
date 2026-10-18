<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render(config('vue-cms.pages.dashboard'));
    }
}
