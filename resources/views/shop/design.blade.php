@extends('layouts.shop')
@section('title', 'Création sur mesure')
@section('description', 'Faites créer votre robe, costume africain ou grand boubou sur mesure par l\'atelier COMPLEXE WELLTAARÉ COLLECTION à Conakry.')

@section('content')
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb"><a href="{{ route('home') }}">Accueil</a> / <span>Création sur mesure</span></nav>
        <h1>Création sur mesure</h1>
        <p>Décrivez la tenue dont vous rêvez. Nous vous envoyons un croquis et un devis sous 48 h.</p>
    </div>
</section>

<div class="container design-layout">
    <aside class="design-aside">
        @include('partials.visual', ['key' => 'atelier', 'fallback' => 'art-femme-2', 'label' => 'De votre idée<br>à votre silhouette.', 'labelStyle' => 'font-size:1.6rem'])
        <ol class="steps" style="margin:0">
            <li style="border-color:var(--line)"><div><b>Votre demande</b><small style="color:var(--muted)">Description, mesures et photo d'inspiration.</small></div></li>
            <li style="border-color:var(--line)"><div><b>Croquis & devis</b><small style="color:var(--muted)">Envoyés par WhatsApp sous 48 h.</small></div></li>
            <li style="border-color:var(--line)"><div><b>Couture</b><small style="color:var(--muted)">Après validation et acompte.</small></div></li>
            <li style="border-color:var(--line)"><div><b>Essayage & livraison</b><small style="color:var(--muted)">Retouches puis remise de votre pièce.</small></div></li>
        </ol>
        <div class="measure-help"><b>Prendre ses mesures :</b> utilisez un mètre ruban souple, en cm, sur des vêtements légers. Le tour de poitrine se prend à l'endroit le plus fort, le tour de taille au creux de la taille. Pas sûr(e) ? Laissez vide, nous les prendrons en boutique.</div>
    </aside>

    <form method="post" action="{{ route('design.store') }}" enctype="multipart/form-data" class="checkout-form" data-once novalidate>
        @csrf
        @if ($errors->any())<div class="alert alert-err">Vérifiez les champs en rouge.</div>@endif

        <fieldset>
            <legend>La tenue</legend>
            <div class="gender-switch">
                <input type="radio" id="g-f" name="gender" value="femme" @checked(old('gender', 'femme') === 'femme')><label for="g-f">Pour femme</label>
                <input type="radio" id="g-h" name="gender" value="homme" @checked(old('gender') === 'homme')><label for="g-h">Pour homme</label>
            </div>
            <div class="grid-2">
                <div class="field @error('garment_type') has-error @enderror"><label for="garment_type">Type de vêtement *</label><input id="garment_type" name="garment_type" value="{{ old('garment_type') }}" list="garments" placeholder="Robe de soirée, costume, boubou…">
                    <datalist id="garments"><option>Robe de soirée</option><option>Robe moulante</option><option>Tenue de mariage</option><option>Ensemble jupe & top</option><option>Costume africain</option><option>Grand boubou</option><option>Ensemble chemise & pantalon</option><option>Kaftan</option></datalist>
                    @error('garment_type')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="occasion">Occasion</label><input id="occasion" name="occasion" value="{{ old('occasion') }}" placeholder="Mariage, baptême, soirée, Tabaski…"></div>
            </div>
            <div class="field"><label for="fabric">Tissu souhaité</label><input id="fabric" name="fabric" value="{{ old('fabric') }}" placeholder="Wax, bazin riche, satin, dentelle, kente, lin…"></div>
            <div class="field @error('description') has-error @enderror"><label for="description">Description du modèle *</label><textarea id="description" name="description" placeholder="Coupe, longueur, décolleté, manches, broderies, couleurs…">{{ old('description') }}</textarea>@error('description')<span class="error">{{ $message }}</span>@enderror</div>
            <label class="upload @error('inspiration') has-error @enderror" for="inspiration">
                <span class="label">Photo d'inspiration (facultatif)</span><br>
                <small style="color:var(--muted)">JPG, PNG ou WEBP, 6 Mo maximum</small>
                <input type="file" id="inspiration" name="inspiration" accept="image/jpeg,image/png,image/webp">
                @error('inspiration')<span class="error-text">{{ $message }}</span>@enderror
            </label>
        </fieldset>

        <fieldset>
            <legend>Vos mesures <small style="font-family:var(--f-body);font-size:.9rem;color:var(--muted)">(en cm, facultatif)</small></legend>
            <div class="grid-4">
                @foreach (\App\Models\DesignRequest::MEASUREMENTS as $key => $label)
                    <div class="field"><label for="m-{{ $key }}" style="font-size:.66rem">{{ $label }}</label><input id="m-{{ $key }}" name="measurements[{{ $key }}]" value="{{ old('measurements.'.$key) }}" inputmode="decimal"></div>
                @endforeach
            </div>
            @error('measurements.*')<span class="error-text">Les mesures doivent être des nombres en centimètres.</span>@enderror
        </fieldset>

        <fieldset>
            <legend>Budget & délai</legend>
            <div class="grid-2">
                <div class="field"><label for="budget">Budget indicatif (GNF)</label><input id="budget" name="budget" value="{{ old('budget') }}" inputmode="numeric" placeholder="1500000"></div>
                <div class="field @error('deadline') has-error @enderror"><label for="deadline">Pour quelle date ?</label><input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" min="{{ now()->addDay()->format('Y-m-d') }}">@error('deadline')<span class="error">{{ $message }}</span>@enderror</div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Vos coordonnées</legend>
            <div class="field @error('customer_name') has-error @enderror"><label for="customer_name">Nom complet *</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" autocomplete="name">@error('customer_name')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="grid-2">
                <div class="field @error('customer_phone') has-error @enderror"><label for="customer_phone">Téléphone / WhatsApp *</label><input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" inputmode="tel" placeholder="+224 6xx xx xx xx">@error('customer_phone')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="customer_email">E-mail (facultatif)</label><input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"></div>
            </div>
        </fieldset>

        <button type="submit" class="btn btn-accent" data-label="Envoi…">Envoyer ma demande</button>
    </form>
</div>
@endsection
