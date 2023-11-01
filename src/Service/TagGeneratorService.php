<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class TagGeneratorService
{
    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getResponse(array $messages): array
    {
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $_ENV['CHAT_GPT_KEY']
        ];

        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [$messages]
        ];

        $response = (new Client())->post(
            'https://api.openai.com/v1/chat/completions',
            [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => $headers
            ]
        )->getBody()->getContents();

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}