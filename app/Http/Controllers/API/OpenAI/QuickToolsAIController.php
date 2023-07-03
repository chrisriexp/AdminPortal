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

        $data = [];

        foreach($request->history as $entry){
            if($entry['type'] == 'prompt'){
                array_push($data, [
                    'role'=> 'user',
                    'content'=> $entry['text'] 
                ]);
            }else if($entry['type']  == 'response'){
                array_push($data, [
                    'role'=> 'assistant',
                    'content'=> $entry['text'] 
                ]);
            }
        }

        array_push($data, [
            'role'=> 'user',
            'content'=> $prompt
        ]);

        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $data,
            'max_tokens' => 500,
        ]);

        $response = $result['choices'][0]['message']['content'];

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
                    'type'=> 'prompt',
                    'text'=> $prompt
                ],
                (object) [
                    'type'=> 'response',
                    'text'=> $response
                ]
            ]
        ];

        return response()->json($response, 200);
    }
}
