{{-- resources/views/components/layout/head.blade.php kepala dokumen bersama layout publik dan admin --}}
@props(['title'])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#000000">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])