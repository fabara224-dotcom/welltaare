<?php

namespace App\Http\Controllers;

use App\Models\GraphicRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class GraphicRequestController extends Controller
{
    public function create()
    {
        return view('shop.graphic');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'regex:/^[+0-9 ().-]{8,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'company' => ['nullable', 'string', 'max:120'],
            'service' => ['required', 'in:'.implode(',', array_keys(GraphicRequest::SERVICES))],
            'description' => ['required', 'string', 'max:3000'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'deadline' => ['nullable', 'date', 'after:today'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ], [
            'customer_phone.regex' => 'Numéro de téléphone invalide.',
            'service.in' => 'Choisissez le type de prestation.',
            'deadline.after' => 'La date doit être dans le futur.',
            'attachment.mimes' => 'Fichier accepté : JPG, PNG, WEBP ou PDF.',
            'attachment.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        unset($data['attachment']);
        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('graphisme', 'public');
        }
        $data['reference'] = Order::newReference('GD');
        $data['user_id'] = auth()->id();
        $data['status'] = 'nouvelle';

        $req = GraphicRequest::create($data);
        session()->push('my_graphics', $req->reference);
        session()->flash('open_whatsapp', true);

        return redirect()->route('graphic.thanks', $req);
    }

    public function thanks(GraphicRequest $graphicRequest)
    {
        abort_unless(in_array($graphicRequest->reference, session('my_graphics', []), true)
            || ($graphicRequest->user_id && $graphicRequest->user_id === auth()->id()), 404);

        return view('shop.graphic-thanks', ['req' => $graphicRequest]);
    }
}
