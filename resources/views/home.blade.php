<!DOCTYPE html>
<html>
<head>
    <title>Exemplo de Autenticação no Mercado Livre</title>
</head>
<body>
    <h1>Exemplo de Autenticação no Mercado Livre</h1>
    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif
    <a href="https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={{ $client_id }}&redirect_uri=http://localhost:8000/">Solicitar Permissão</a>
</body>
</html>