<?php

namespace App\Controllers;

class Checkout extends BaseController
{
    public function index()
    {
        $cart = service('cart');
        
        $data = [
            'items' => $cart->contents(),
            'total' => $cart->total()
        ];

        return view('v_checkout', $data);
    }

    // Fungsi untuk mencari nama Kelurahan/Destinasi via AJAX
    public function searchDestination()
    {
        $search = $this->request->getGet('search');
        $apiKey = env('rajaongkir.key', 'QBPqCKVJd7b4cf0365b90b5btybqLBC2');

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->request('GET', 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'headers' => [
                    'Accept' => 'application/json',
                    'key'    => $apiKey
                ],
                'query' => [
                    'search' => $search,
                    'limit'  => 10
                ]
            ]);

            return $this->response->setJSON(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Fungsi untuk menghitung Ongkos Kirim via AJAX
    public function calculateCost()
    {
        $destination = $this->request->getPost('destination');
        $apiKey = env('rajaongkir.key', 'QBPqCKVJd7b4cf0365b90b5btybqLBC2');

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('POST', 'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'key'          => $apiKey
                ],
                'form_params' => [
                    'origin'      => '64999', // Default asal Pedurungan Tengah sesuai modul
                    'destination' => $destination,
                    'weight'      => 1000,
                    'courier'     => 'jne'
                ]
            ]);

            return $this->response->setJSON(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}