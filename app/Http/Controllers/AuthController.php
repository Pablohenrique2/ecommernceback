<?php

namespace App\Http\Controllers;

use App\Models\TokenMercado;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirectToMercadoLivre()
    {
        $clientId = env('MERCADOLIVRE_CLIENT_ID');
        $redirectUri = env('MERCADOLIVRE_REDIRECT_URI');

        return redirect("https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}");
    }

    public function handleMercadoLivreCallback(Request $request)
    {
        
        $code = $request->code;
        $clientId = env('MERCADOLIVRE_CLIENT_ID');
        $clientSecret = env('MERCADOLIVRE_CLIENT_SECRET');
        $redirectUri = env('MERCADOLIVRE_REDIRECT_URI');
        $codeVerifier = Str::random(64);

        // Verifica se já existe um código no banco de dados
        $dbCode = DB::select('SELECT code FROM tokenmercado WHERE id = ?', [1]);

        if (!empty($dbCode) && $dbCode[0]->code) {
            // Existe um código no banco de dados, verificar apenas a expiração
            $accessToken = $this->getAccessTokenFromDatabase();

            if ($this->isAccessTokenExpired($accessToken)) {
                // O código expirou, faça uma nova solicitação com o código armazenado no banco de dados
                $accessToken = $this->requestAccessToken($clientId, $clientSecret, $dbCode );
            }
        } else {
            // Não há código no banco de dados, utilizar o código fornecido na solicitação atual
            $accessToken = $this->createAccessToken($clientId, $clientSecret, $codeVerifier, $redirectUri, $code);
            
            // Salvar o código no banco de dados
            DB::update('UPDATE tokenmercado SET code = ? WHERE id = ?', [$code, 1]);
            $this->getUser();
        }
        
        
        return redirect('http://localhost:8080/#/home');
    }

    // Função para obter o token de acesso do banco de dados
    private function getAccessTokenFromDatabase()
    {
        return DB::select('SELECT access_token, expires_at FROM tokenmercado WHERE id = ?', [1]);
    }

    // Função para verificar se o token de acesso expirou
    private function isAccessTokenExpired($accessToken)
    {
        $brasiliaTimeZone = new CarbonTimeZone('America/Sao_Paulo');
        $currentDateTime = Carbon::now($brasiliaTimeZone);

        if ($currentDateTime >= $accessToken[0]->expires_at)  {
            // O token expirou
           return true;
        } else {
            // O token ainda é válido
           return false;
        }
    }

    // Função para solicitar o token de acesso
    private function createAccessToken($clientId, $clientSecret, $codeVerifier, $redirectUri, $code)
    {
        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'code_verifier' => $codeVerifier,
        ]);
       
        $accessToken = $response->json();
        $expiresInSeconds = $accessToken['expires_in'];
        $expirationDateTime = Carbon::now()->addSeconds($expiresInSeconds);
        $expiresInHours = $expirationDateTime->diffInHours();
        $brasiliaTimeZone = new CarbonTimeZone('America/Sao_Paulo');
        $currentDateTime = Carbon::now($brasiliaTimeZone);

        $expirationDateTimeWithHours = $currentDateTime->copy()->addHours($expiresInHours);

        DB::update('UPDATE tokenmercado SET access_token = ?, code = ?, expires_at = ? WHERE id = ?', [
            $accessToken['access_token'],
            $code,
            $expirationDateTimeWithHours,
            1
        ]);
        return $accessToken;
    }

    private function requestAccessToken($clientId, $clientSecret, $code)
    {
        $response = Http::asForm()->post('https://api.mercadolibre.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $code[0]->code,
        ]);
        
        $accessToken = $response->json();
        dd($accessToken);
        $expiresInSeconds = $accessToken['expires_in'];
        $expirationDateTime = Carbon::now()->addSeconds($expiresInSeconds);
        $expiresInHours = $expirationDateTime->diffInHours();
        $brasiliaTimeZone = new CarbonTimeZone('America/Sao_Paulo');
        $currentDateTime = Carbon::now($brasiliaTimeZone);

        $expirationDateTimeWithHours = $currentDateTime->copy()->addHours($expiresInHours);
        // Atualizar o token de acesso no banco de dados
        DB::update('UPDATE tokenmercado SET access_token = ?, code = ?, expires_at = ? WHERE id = ?', [
            $accessToken['access_token'],
            $accessToken['code'],
            $expirationDateTimeWithHours,
            1
        ]);

        return $accessToken;
    }

    public function getUser()
    {
        $token = TokenMercado::findOrFail(1);
        $accessToken = $token->access_token;
        $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/users/me');
        $response->json();

        DB::update('UPDATE tokenmercado SET idloja = ?, first_name = ? WHERE id = ?', [
            $response['id'],
            $response['first_name'],
            1
        ]);
    }
}