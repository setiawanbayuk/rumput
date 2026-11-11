<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EsuketPushDelivery;
use App\Models\EsuketPushDevice;
use Illuminate\Http\Request;

class EsuketPushMonitorController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);
        $search = trim($filters['q'] ?? '');

        $devicesQuery = EsuketPushDevice::query()
            ->with('user')
            ->latest('updated_at');
        $deliveriesQuery = EsuketPushDelivery::query()
            ->with(['device.user', 'surat'])
            ->latest('updated_at');

        if ($search !== '') {
            $devicesQuery->where(function ($query) use ($search) {
                $query->where('device_id', 'like', '%' . $search . '%')
                    ->orWhere('platform', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nik', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });

            $deliveriesQuery->where(function ($query) use ($search) {
                $query->where('event_name', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhereHas('device', function ($deviceQuery) use ($search) {
                        $deviceQuery->where('device_id', 'like', '%' . $search . '%')
                            ->orWhere('platform', 'like', '%' . $search . '%')
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('nik', 'like', '%' . $search . '%')
                                    ->orWhere('email', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        return view('admin.esuket-push.index', [
            'devices' => $devicesQuery->paginate(20, ['*'], 'devices_page')->withQueryString(),
            'deliveries' => $deliveriesQuery->paginate(20, ['*'], 'deliveries_page')->withQueryString(),
            'search' => $search,
            'deviceCount' => EsuketPushDevice::count(),
            'deliveryCounts' => EsuketPushDelivery::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }
}
