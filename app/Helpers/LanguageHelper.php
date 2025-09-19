<?php

use Illuminate\Support\Facades\Http;
use App\Models\languag;
use App\Models\PostMedia;


function language_code($code){
    $language = languag::where('name', $code)->first();
    return $language->code??'en';
}

if (!function_exists('detectLanguage')) {
    function detectLanguage($text)
    {
        $prompt = "Identify the ISO 639-1 language code (like 'en' for English, 'hi' for Hindi) of the following text:\n\n" . $text . "\n\nRespond only with the language code.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo-0125',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0,
        ]);

        $code = trim($response->json('choices.0.message.content') ?? 'en');
        return substr($code, 0, 2);
    }
}

if (!function_exists('translateMessageWithOpenAI')) {
    function translateMessageWithOpenAI($text, $toLanguage = 'English', $fromLanguage = null)
    {
        // dd($fromLanguage);
        $prompt = "Translate the following message";

        if ($fromLanguage) {
            $prompt .= " from $fromLanguage";
        }

        $prompt .= " to $toLanguage:\n\n$text";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo-0125',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2,
        ]);
        // dd($response->json());

        return trim($response->json('choices.0.message.content') ?? '');
    }
}

function stringConvertToArray($data){
    return explode(", ",$data);
}

function post_media_get($id){
    return PostMedia::where('post_id', $id)->get();
}


