<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class DesignRequestController extends Controller
{
    public function create()
    {
        return view('shop.design');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'regex:/^[+0-9 ().-]{8,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'gender' => ['required', 'in:femme,homme'],
            'garment_type' => ['required', 'string', 'max:120'],
            'occasion' => ['nullable', 'string', 'max:120'],
            'fabric' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:3000'],
            'measurements' => ['nullable', 'array'],
            'measurements.*' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'deadline' => ['nullable', 'date', 'after:today'],
            'inspiration' => ['nullable', 'image', 'max:6144'],
        ], [
            'customer_phone.regex' => 'Numéro de téléphone invalide.',
            'deadline.after' => 'La date doit être dans le futur.',
            'inspiration.image' => 'Le fichier doit être une image (JPG, PNG, WEBP).',
            'inspiration.max' => 'L\'image ne doit pas dépasser 6 Mo.',
        ]);

        $data['measurements'] = array_filter($data['measurements'] ?? [], fn ($v) => $v !== null && $v !== '');
        unset($data['inspiration']);
        if ($request->hasFile('inspiration')) {
            $data['inspiration_path'] = $request->file('inspiration')->store('inspirations', 'public');
        }
        $data['reference'] = Order::newReference('SM');
        $data['user_id'] = auth()->id();
        $data['status'] = 'nouvelle';

        $design = DesignRequest::create($data);
        session()->push('my_designs', $design->reference);
        session()->flash('open_whatsapp', true);

        return redirect()->route('design.thanks', $design);
    }

    public function thanks(DesignRequest $designRequest)
    {
        abort_unless(in_array($designRequest->reference, session('my_designs', []), true)
            || ($designRequest->user_id && $designRequest->user_id === auth()->id()), 404);

        return view('shop.design-thanks', ['design' => $designRequest]);
    }
}
