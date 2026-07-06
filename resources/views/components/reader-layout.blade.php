{{-- Layout khusus reader mode (section/konten) - tanpa bottom nav --}}
@props(['title' => 'LSM App'])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#6366f1" />
    <title>{{ $title }} — {{ config('app.name', 'LSM App') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #f8fafc; }

        /* prose styles untuk konten HTML */
        .content-body img  { max-width: 100%; height: auto; border-radius: 12px; margin: 8px 0; }
        .content-body p    { margin-bottom: 12px; line-height: 1.7; }
        .content-body h1,
        .content-body h2,
        .content-body h3   { font-weight: 700; margin: 16px 0 8px; }
        .content-body ul,
        .content-body ol   { padding-left: 20px; margin-bottom: 12px; }
        .content-body li   { margin-bottom: 4px; }
        .content-body a    { color: #6366f1; text-decoration: underline; }
        .content-body strong { font-weight: 700; }
        .content-body em   { font-style: italic; }
        .content-body table { width:100%; border-collapse:collapse; font-size:13px; }
        .content-body th,
        .content-body td   { border:1px solid #e2e8f0; padding:6px 10px; }
        .content-body th   { background:#f1f5f9; font-weight:600; }
    </style>
</head>
<body class="bg-slate-50">
    {{ $slot }}
</body>
</html>
