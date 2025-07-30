<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class AiTestController extends Controller
{
    public function index()
    {
        return Inertia::render('AI/Test');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:1|max:1000',
        ]);

        try {
            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-3.5-turbo')
                ->withPrompt($request->input('prompt'))
                ->withMaxTokens(500)
                ->asText();

            return response()->json([
                'success' => true,
                'response' => $response->text,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
