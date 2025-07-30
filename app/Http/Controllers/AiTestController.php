<?php

namespace App\Http\Controllers;

use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
                ->using(Provider::Anthropic, 'claude-3-haiku-20240307')
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