<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    protected AsaasService $asaas;

    public function __construct(AsaasService $asaas)
    {
        $this->asaas = $asaas;
    }

    /**
     * Show billing plans (for logged in users)
     */
    public function index()
    {
        return Inertia::render('Billing/Index', [
            'plans' => Plan::all(),
            'user_credits' => auth()->user()->credits
        ]);
    }

    /**
     * Create a new payment via Asaas
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'method' => 'required|in:PIX,CREDIT_CARD',
            'cpf_cnpj' => 'nullable|string',
            'phone' => 'nullable|string',
            // Credit Card Fields
            'holder_name' => 'required_if:method,CREDIT_CARD',
            'card_number' => 'required_if:method,CREDIT_CARD',
            'expiry' => 'required_if:method,CREDIT_CARD',
            'cvv' => 'required_if:method,CREDIT_CARD',
            'postal_code' => 'required_if:method,CREDIT_CARD',
            'address_number' => 'required_if:method,CREDIT_CARD',
        ]);

        $user = auth()->user();
        $plan = Plan::find($request->plan_id);
        $tenant = $user->tenant;

        // If we received billing data, update tenant
        if ($request->cpf_cnpj) {
            $tenant->update([
                'tax_id' => $request->cpf_cnpj,
                'phone' => $request->phone
            ]);
        }

        $customerId = $tenant->asaas_customer_id;

        if (!$customerId) {
            if (!$request->cpf_cnpj) {
                 return response()->json(['needs_data' => true], 422);
            }
            $customerId = $this->asaas->createCustomer($user, [
                'cpf_cnpj' => $request->cpf_cnpj,
                'phone' => $request->phone
            ]);

            if (!$customerId) {
                return response()->json(['error' => 'Erro ao criar cliente no gateway de pagamento.'], 422);
            }

            $tenant->update(['asaas_customer_id' => $customerId]);
        }

        $data = [
            'customer_id' => $customerId,
            'amount' => $plan->monthly_price,
            'method' => $request->method,
            'description' => "Compra de créditos: Pacote {$plan->name}",
            'external_reference' => "plan_{$plan->id}"
        ];

        if ($request->method === 'CREDIT_CARD') {
            $expiryParts = explode('/', $request->expiry);
            $data['creditCard'] = [
                'holderName' => $request->holder_name,
                'number' => preg_replace('/\D/', '', $request->card_number),
                'expiryMonth' => trim($expiryParts[0]),
                'expiryYear' => '20' . trim($expiryParts[1] ?? ''),
                'ccv' => $request->cvv,
            ];

            $data['creditCardHolderInfo'] = [
                'name' => $user->name,
                'email' => $user->email,
                'cpfCnpj' => $tenant->tax_id ?? $request->cpf_cnpj,
                'postalCode' => preg_replace('/\D/', '', $request->postal_code),
                'addressNumber' => $request->address_number,
                'mobilePhone' => $tenant->phone ?? $request->phone,
            ];
            $data['remoteIp'] = $request->ip();
        }

        $paymentData = $this->asaas->createPayment($data);

        if (!$paymentData) {
            return back()->with('error', 'Erro ao processar pagamento com o Asaas.');
        }

        // Save local payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'asaas_id' => $paymentData['id'],
            'payment_method' => $request->method,
            'amount' => $plan->monthly_price,
            'status' => strtolower($paymentData['status'] ?? 'pending'),
            'invoice_url' => $paymentData['invoiceUrl'],
            'asaas_response' => $paymentData
        ]);

        if ($request->method === 'PIX') {
            $pixData = $this->asaas->getPixQrCode($paymentData['id']);
            return response()->json([
                'payment' => $payment,
                'pix' => $pixData
            ]);
        }

        // Direct Credit Card Logic
        if ($request->method === 'CREDIT_CARD') {
            if (in_array($paymentData['status'], ['CONFIRMED', 'RECEIVED'])) {
                // Manually trigger credit addition if confirmed immediately (direct processing)
                if ($payment->status !== 'confirmed') {
                    $payment->update(['status' => 'confirmed']);
                    $user->increment('credits', $plan->image_limit);
                }
                return response()->json([
                    'success' => true,
                    'payment' => $payment
                ]);
            }
        }

        return response()->json([
            'payment' => $payment,
            'checkout_url' => $paymentData['invoiceUrl']
        ]);
    }

    /**
     * Handle Asaas Webhooks
     */
    public function webhook(Request $request)
    {
        $event = $request->input('event');
        $paymentId = $request->input('payment.id');

        Log::info("Asaas Webhook Received: {$event} for Payment: {$paymentId}");

        $payment = Payment::where('asaas_id', $paymentId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if (in_array($event, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'])) {
            if ($payment->status !== 'confirmed') {
                $payment->update(['status' => 'confirmed']);
                
                // Add credits to user
                $user = $payment->user;
                $plan = $payment->plan;
                
                $user->increment('credits', $plan->image_limit);
                
                Log::info("Credits added to user {$user->id}: {$plan->image_limit}");
            }
        }

        return response()->json(['success' => true]);
    }
}
