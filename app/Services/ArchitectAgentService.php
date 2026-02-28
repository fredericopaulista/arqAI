<?php

namespace App\Services;

use App\Models\Project;
use App\Models\AiConversation;

class ArchitectAgentService
{
    /**
     * Get the system prompt from settings.
     */
    public function getSystemPrompt(): string
    {
        return \App\Models\Setting::get('agent_prompt', 'Você é um arquiteto especialista em design de interiores...');
    }

    /**
     * Get the next question for the briefing session.
     */
    public function getNextQuestion(Project $project): ?string
    {
        $conversationCount = $project->conversations()->where('role', 'assistant')->count();

        $questions = [
            "Para começar, qual o tipo de ambiente que vamos transformar? (Ex: Sala, Quarto, Cozinha, Escritório)",
            "Qual estilo de decoração você prefere? (Ex: Moderno, Minimalista, Industrial, Rústico, Clássico)",
            "Quais são as cores predominantes que você gostaria de ver no ambiente?",
            "Existem restrições ou necessidades específicas quanto aos móveis? (Ex: mesa para 6 pessoas, sofá de canto)",
            "Qual a sensação que você quer que esse ambiente transmita? (Ex: Aconchego, Produtividade, Elegância)",
            "Você tem uma estimativa de orçamento para essa transformação? (Baixo, Médio ou Alto investimento)",
        ];

        if ($conversationCount < count($questions)) {
            return $questions[$conversationCount];
        }

        return null; // Briefing complete
    }

    /**
     * Process user response and return the next question or completion using LLM.
     */
    public function processResponse(Project $project, string $message): array
    {
        // Save user message
        $project->conversations()->create([
            'role' => 'user',
            'message' => $message
        ]);

        // Get history
        $history = $project->conversations()->orderBy('id')->get()->map(function($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->message
            ];
        })->toArray();

        // Get config
        $apiKey = \App\Models\Setting::get('ai_api_key');
        $model = \App\Models\Setting::get('ai_model', 'gpt-4o');
        $systemPrompt = $this->getSystemPrompt();

        // Prepare messages for LLM
        $messages = array_merge([['role' => 'system', 'content' => $systemPrompt]], $history);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                throw new \Exception("Erro na API da IA: " . $response->body());
            }

            $aiMessage = $response->json('choices.0.message.content');

            // Safety: Strip any JSON blocks if the AI accidentally included them
            $aiMessage = preg_replace('/```json.*?```/s', '', $aiMessage);
            $aiMessage = preg_replace('/\{.*?\}/s', '', $aiMessage); // Aggressive fallback for raw JSON
            $aiMessage = trim($aiMessage);

            // Detect completion
            if (str_contains($aiMessage, '[FINALIZADO]')) {
                $cleanMessage = trim(str_replace('[FINALIZADO]', '', $aiMessage));
                
                if ($cleanMessage) {
                    $project->conversations()->create([
                        'role' => 'assistant',
                        'message' => $cleanMessage
                    ]);
                }

                $project->update(['status' => 'generating']);
                $this->structureBriefingWithAi($project);

                return [
                    'status' => 'completed',
                    'message' => $cleanMessage ?: "Perfeito! Já coletei todas as informações. Estou preparando sua proposta."
                ];
            }

            // Save assistant message
            $project->conversations()->create([
                'role' => 'assistant',
                'message' => $aiMessage
            ]);

            return [
                'status' => 'collecting',
                'message' => $aiMessage
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Chat AI error: " . $e->getMessage());
            return [
                'status' => 'collecting',
                'message' => "Desculpe, tive um problema técnico. Pode repetir o que disse?"
            ];
        }
    }

    /**
     * Use AI to structure the conversation into a JSON briefing.
     */
    public function structureBriefingWithAi(Project $project)
    {
        $conversations = $project->conversations()->orderBy('id')->get()->map(function($msg) {
            return $msg->role . ": " . $msg->message;
        })->implode("\n");

        $apiKey = \App\Models\Setting::get('ai_api_key');
        $model = \App\Models\Setting::get('ai_model', 'gpt-4o');

        $prompt = "Com base na conversa abaixo, extraia os dados para um briefing arquitetônico em formato JSON puro.\n\n" .
                  "Campos:\n" .
                  "- tipo_ambiente (Ex: Sala de estar)\n" .
                  "- estilo (Ex: Moderno)\n" .
                  "- cores (Ex: Tons pastéis)\n" .
                  "- necessidades (Ex: Sofá grande)\n" .
                  "- sensacao (Ex: Aconchego)\n" .
                  "- orcamento (Ex: Médio)\n\n" .
                  "Retorne APENAS o JSON.\n\n" .
                  "CONVERSA:\n" . $conversations;

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0,
            ]);

            $jsonContent = $response->json('choices.0.message.content');
            // Clean markdown if present
            $jsonContent = trim(str_replace(['```json', '```'], '', $jsonContent));
            $briefing = json_decode($jsonContent, true);

            $project->update([
                'briefing_json' => $briefing ?: []
            ]);
            
            \App\Jobs\GenerateArchitectImageJob::dispatch($project);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Briefing structuring error: " . $e->getMessage());
            // Fallback empty or basic info
        }
    }
}
