<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Accès refusé</title>
<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#0B0A09;color:#F3ECDD;font-family:Georgia,serif;text-align:center;padding:24px}a{color:#D4AF5A}p{color:#9D9384;font-family:system-ui,sans-serif}</style></head>
<body><div><h1 style="font-weight:400">Accès refusé</h1><p>{{ $exception->getMessage() ?: 'Vous n\'avez pas accès à cette page.' }}</p><p><a href="{{ url('/') }}">Retour</a></p></div></body></html>
