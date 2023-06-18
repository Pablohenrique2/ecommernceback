<?php

namespace App\Http\Controllers;

use App\Models\TokenMercado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StoreController extends Controller
{
    public function listCategory()
    {
        $token = TokenMercado::findOrFail(1);
        $accessToken = $token->access_token;
        $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/sites/MLB/categories/all');
        return $response->json();
    }

    public function addProduct(Request $request)
    {
        $token = TokenMercado::findOrFail(1);
        $accessToken = $token->access_token;
       
        $dados['title'] = $request->titulo;
        $dados['category_id'] =  $request->selectedCategory;
        $dados['price'] = $request->price;
        $dados['currency_id'] ="BRL" ;
        $dados['available_quantity'] = $request->quantidade;
        $dados['buying_mode'] = "buy_it_now";
        $dados['condition'] = "new";
        $dados['listing_type_id'] = "bronze";
        $dados['attributes'][0] = [
            'id' => 'BRAND',
            'value_id' => null,
            'value_name' => 'Outras Marcas',
            'attribute_group_id' => 'OTHERS',
            'attribute_group_name' => 'Outros',
            'source' => 343434,
            'value_struct' => null
        ];

        $dados['pictures'][0]['source'] = $request->img;
       
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.mercadolibre.com/items', $dados);
        return  $response->json();


        // $text['plain_text'] = "teste descrição 12323";
        
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $accessToken,
        // ])->post('https://api.mercadolibre.com/items/'.$resultado['id'].'/description', $text);
        //  $resultado = $response->json();
    }

    public function listProduct()
    {
        $token = TokenMercado::findOrFail(1);
        $id_loja = $token->idloja ;
        $accessToken = $token->access_token;
        $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/users/'.$id_loja.'/items/search/?offser=0&limit=10');
        $results = $response->json();
        $allResults = [];
        foreach($results['results'] as $linhas) {
            $response = Http::withToken($accessToken)->get('https://api.mercadolibre.com/items/'.$linhas);
            $item = $response->json();
            $allResults[] = $item;
        }

        return $allResults;
    }

    public function updateProduct()
    {
        $dados['title'] = "Produto mas vendido";
        $dados['price'] = "967";
        $dados['available_quantity'] = "5";

        $token = TokenMercado::findOrFail(1);
        $accessToken = $token->access_token;
        $response = Http::withToken($accessToken)->put('https://api.mercadolibre.com/items/MLB3346874597', $dados);
        $results = $response->json();
        dd($results);
    }

    public function deleteProduct()
    {
        $dados['status'] = "closed";
        $token = TokenMercado::findOrFail(1);
        $accessToken = $token->access_token;
        $response = Http::withToken($accessToken)->put('https://api.mercadolibre.com/items/MLB3345684187',  $dados);

        $dados2['deleted'] = "true";
        $response = Http::withToken($accessToken)->put('https://api.mercadolibre.com/items/MLB3345684187',  $dados2);
        $results = $response->json();
        dd($results);
    }
}

