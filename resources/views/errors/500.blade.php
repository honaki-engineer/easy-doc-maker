{{-- resources/views/errors/500.blade.php --}}
<x-errors.error-page 
    :code="500" 
    title="Internal Server Error" 
    message="サーバーエラーが発生しました。<br>ご迷惑をおかけしております。" 
/>