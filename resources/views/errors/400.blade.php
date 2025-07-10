{{-- resources/views/errors/400.blade.php --}}
<x-errors.error-page 
    :code="400" 
    title="Bad Request" 
    message="リクエストに不備があるようです。<br>もう一度お試しください。" 
/>