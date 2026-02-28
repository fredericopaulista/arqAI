<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('asaas_api_key', '');
        $mode = Setting::get('asaas_mode', 'sandbox');
        $this->baseUrl = $mode === 'production' 
            ? 'https://www.asaas.com/api/v3' 
            : 'https://sandbox.asaas.com/api/v3';
        
        Log::info("AsaasService Initialized. Mode: {$mode}. Key length: " . strlen($this->apiKey));
    }

    protected function request()
    {
        if (empty($this->apiKey)) {
            Log::error("Asaas API Key is empty! Check your settings.");
        }

        return Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function createCustomer($user, $data = [])
    {
        $response = $this->request()->post($this->baseUrl . '/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => $data['cpf_cnpj'] ?? null,
            'mobilePhone' => $data['phone'] ?? null,
        ]);

        if ($response->failed()) {
            Log::error("Asaas Customer Error: " . $response->body());
            return null;
        }

        return $response->json('id');
    }

    public function createPayment($data)
    {
        $payload = [
            'customer' => $data['customer_id'],
            'billingType' => $data['method'], // PIX, CREDIT_CARD
            'value' => $data['amount'],
            'dueDate' => now()->addDays(3)->format('Y-m-d'),
            'description' => $data['description'] ?? 'Compra de créditos ArqAI',
            'externalReference' => $data['external_reference'] ?? null,
        ];

        if ($data['method'] === 'CREDIT_CARD' && isset($data['creditCard'])) {
            $payload['creditCard'] = [
                'holderName' => $data['creditCard']['holderName'],
                'number' => $data['creditCard']['number'],
                'expiryMonth' => $data['creditCard']['expiryMonth'],
                'expiryYear' => $data['creditCard']['expiryYear'],
                'ccv' => $data['creditCard']['cvv'],
            ];

            $payload['creditCardHolderInfo'] = [
                'name' => $data['creditCardHolderInfo']['name'],
                'email' => $data['creditCardHolderInfo']['email'],
                'cpfCnpj' => $data['creditCardHolderInfo']['cpfCnpj'],
                'postalCode' => $data['creditCardHolderInfo']['postalCode'],
                'addressNumber' => $data['creditCardHolderInfo']['addressNumber'],
                'mobilePhone' => $data['creditCardHolderInfo']['mobilePhone'],
                'addressComplement' => $data['creditCardHolderInfo']['addressComplement'] ?? null,
            ];

            $payload['remoteIp'] = $data['remoteIp'] ?? request()->ip();
        }

        Log::info("Asaas Payment Payload: " . json_encode($payload));
        $response = $this->request()->post($this->baseUrl . '/payments', $payload);

        if ($response->failed()) {
            Log::error("Asaas Payment Error: " . $response->body());
            return null;
        }

        return $response->json();
    }

    public function getPixQrCode($paymentId)
    {
        $response = $this->request()->get($this->baseUrl . "/payments/{$paymentId}/pixQrCode");
        
        if ($response->failed()) {
            Log::error("Asaas PIX QR Error: " . $response->body());
            return null;
        }

        return $response->json();
    }
}
