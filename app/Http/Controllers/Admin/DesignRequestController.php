<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignRequest;
use Illuminate\Http\Request;

class DesignRequestController extends Controller
{
    public function index(Request $request)
    {
        $q = DesignRequest::latest();
        if (array_key_exists($s = $request->query('statut'), DesignRequest::STATUSES)) {
            $q->where('status', $s);
        }

        return view('admin.designs.index', [
            'designs' => $q->paginate(20)->withQueryString(),
            'counts' => DesignRequest::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    public function show(DesignRequest $designRequest)
    {
        return view('admin.designs.show', ['design' => $designRequest]);
    }

    public function update(Request $request, DesignRequest $designRequest)
    {
        $designRequest->update($request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(DesignRequest::STATUSES))],
            'quote_amount' => ['nullable', 'integer', 'min:0'],
            'admin_note' => ['nullable', 'string', 'max:3000'],
        ]));

        return back()->with('status', 'Demande mise à jour.');
    }
}
