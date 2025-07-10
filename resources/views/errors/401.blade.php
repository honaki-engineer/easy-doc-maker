{{-- resources/views/errors/401.blade.php --}}
<x-errors.error-page 
    :code="401" 
    title="Unauthorized" 
    message="ログインが必要です。<br>認証後に再度お試しください。" 
/>