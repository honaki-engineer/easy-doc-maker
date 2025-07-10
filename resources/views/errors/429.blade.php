{{-- resources/views/errors/429.blade.php --}}
<x-errors.error-page 
    :code="429" 
    title="Too Many Requests" 
    message="アクセスが集中しています。<br>しばらくしてから再度お試しください。" 
/>