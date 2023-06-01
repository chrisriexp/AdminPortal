<?php

namespace App\Http\Controllers\API\OpenAI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class QuickToolsAIController extends Controller
{
    public function prompt(Request $request){
        $prompt = $request->prompt;

        $result = OpenAI::completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 500,
        ]);

        $response = $result['choices'][0]['text'];

        $logInfo = [
            'prompt'=> $prompt,
            'response'=> $response,
            'result'=> $response
        ];

        Log::channel('open_ai')->info("Quick Tools AI Request - ", (array) $logInfo);
        
        $response = [
            'success'=> true,
            'data'=> [
                (object) [
                    'type'=> 'response',
                    'text'=> $response
                ]
            ]
        ];

        return response()->json($response, 200);
    }
}
