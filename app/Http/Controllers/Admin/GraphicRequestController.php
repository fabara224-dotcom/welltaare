<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GraphicRequest;
use Illuminate\Http\Request;

class GraphicRequestController extends Controller
{
    public function index(Request $request)
    {
        $q = GraphicRequest::latest();
        if (array_key_exists($s = $request->query('statut'), GraphicRequest::STATUSES)) {
            $q->where('status', $s);
        }

        return view('admin.graphics.index', [
            'items' => $q->paginate(20)->withQueryString(),
            'counts' => GraphicRequest::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    public function show(GraphicRequest $graphicRequest)
    {
        return view('admin.graphics.show', ['req' => $graphicRequest]);
    }

    public function update(Request $request, GraphicRequest $graphicRequest)
    {
        $graphicRequest->update($request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(GraphicRequest::STATUSES))],
            'quote_amount' => ['nullable', 'integer', 'min:0'],
            'admin_note' => ['nullable', 'string', 'max:3000'],
        ]));

        return back()->with('status', 'Demande mise à jour.');
    }
}
