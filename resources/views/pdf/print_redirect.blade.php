<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>印刷中...</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    {{-- `iframe`を使って、PDFファイルを埋め込み表示 --}}
    <iframe id="pdfFrame" src="{{ $pdfUrl }}"></iframe>

    <script>
        const iframe = document.getElementById('pdfFrame');
        iframe.onload = () => { // `iframe`が読み込み完了したタイミング
            iframe.contentWindow.focus(); // `iframe`の中のPDF表示にフォーカスを合わせる。 → print() する前に必要なステップ。
            iframe.contentWindow.print(); // `iframe`の中のPDFに対して印刷ダイアログを表示する
        };
    </script>
</body>
</html>
