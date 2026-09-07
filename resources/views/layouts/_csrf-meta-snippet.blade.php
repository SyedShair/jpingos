{{-- Add this single line inside <head> in resources/views/layouts/app.blade.php,
     anywhere above @stack('scripts')/@stack('styles'). The gallery JS reads
     it to attach X-CSRF-TOKEN headers on its fetch()/Dropzone requests. --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
