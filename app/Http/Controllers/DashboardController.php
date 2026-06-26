<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_assets'    => Asset::count(),
            'in_use'          => Asset::where('status', AssetStatus::InUse->value)->count(),
            'spare'           => Asset::where('status', AssetStatus::Spare->value)->count(),
            'service'         => Asset::where('status', AssetStatus::Service->value)->count(),
            'broken'          => Asset::where('status', AssetStatus::Broken->value)->count(),
            'total_users'     => User::count(),
            'total_locations' => Location::count(),
        ];

        // 5 aset terbaru
        $latestAssets = Asset::with(['category', 'location'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'latestAssets'));
    }
}
