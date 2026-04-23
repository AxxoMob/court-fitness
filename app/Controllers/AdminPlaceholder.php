<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Landing for any authenticated user whose role is `admin` or any role
 * court-fitness does not yet have a dashboard for. Copy confirmed by owner
 * on 2026-04-23: "Fitness administration features are coming soon."
 */
final class AdminPlaceholder extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (! session()->get('is_authenticated')) {
            return redirect()->to('/');
        }

        return view('admin_placeholder');
    }
}
