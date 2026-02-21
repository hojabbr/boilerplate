<?php

namespace App\Domains\Dashboard\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('dashboard', [
            'messages' => [
                'title' => __('common.dashboard'),
            ],
        ]);
    }
}
