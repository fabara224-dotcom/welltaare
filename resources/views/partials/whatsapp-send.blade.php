{{-- Envoi automatique du récapitulatif à la boutique sur WhatsApp --}}
@php $waUrl = wa_link(setting('shop_whatsapp'), order_whatsapp_text($record)); @endphp
@if ($waUrl)
    <div class="wa-box">
        <svg aria-hidden="true"><use href="#i-wa"/></svg>
        <div>
            <b>{{ session('open_whatsapp') ? 'Ouverture de WhatsApp…' : 'Envoyez votre récapitulatif sur WhatsApp' }}</b>
            <small>Votre message est déjà rédigé : il ne reste qu'à appuyer sur « Envoyer » pour confirmer avec la boutique ({{ setting('shop_whatsapp') }}).</small>
        </div>
        <a class="btn btn-wa" href="{{ $waUrl }}" target="_blank" rel="noopener" id="wa-send">Envoyer sur WhatsApp</a>
    </div>
    @if (session('open_whatsapp'))
        <script>setTimeout(function () { window.location.href = @json($waUrl); }, 1400);</script>
    @endif
@endif
