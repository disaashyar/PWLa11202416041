<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TransaksiController extends BaseController
{
    protected $cart;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->cart = service('cart');
    }

    public function index()
    {  
        // SINKRON 100%: Menambahkan variabel total harga bawaan modul dosen
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto' => $this->request->getPost('foto')
            ]
        ]);
        
        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang. 
            <a href="' . base_url('keranjang') . '">Lihat</a>'
        );
        
        return redirect()->to(base_url('/'));
    } 

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $item) {
            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata(
            'success',
            'Keranjang berhasil diperbarui'
        );

        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);

        session()->setFlashdata(
            'success',
            'Produk berhasil dihapus dari keranjang'
        );

        return redirect()->to(base_url('keranjang'));
    }

    // SINKRON 100%: Fungsi cart_clear sesuai screenshot modul dosen kamu
    public function cart_clear()
    {
        $this->cart->destroy();

        session()->setFlashdata(
            'success',
            'Keranjang berhasil dikosongkan'
        );

        return redirect()->to(base_url('keranjang'));
    }

    // =========================================================================
    // TAMBAHAN FUNGSI BARU SESUAI INSTRUKSI DOSEN DI BAWAH FUNCTION CART_CLEAR
    // =========================================================================

    public function checkout()
    {
        // Sediakan data keranjang dan nominal total belanja untuk ditampilkan pada file view v_checkout
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_checkout', $data);
    }

    // Fungsi pendukung AJAX RajaOngkir - Cari Kelurahan
    public function searchDestination()
    {
        $search = $this->request->getGet('search');
        $apiKey = env('rajaongkir.key', 'QBPqCKVJd7b4cf0365b90b5btybqLBC2');
        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->request('GET', 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'headers' => ['Accept' => 'application/json', 'key' => $apiKey],
                'query'   => ['search' => $search, 'limit' => 10]
            ]);
            return $this->response->setJSON(json_decode($response->getBody(), true));
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Fungsi pendukung AJAX RajaOngkir - Hitung Biaya Cost
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
                    'origin'      => '64999', // Default asal Pedurungan Tengah dari modul dosen
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