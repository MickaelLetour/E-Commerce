<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail {
    private $api_key = '126349527c80eb61fe2097648b6c4d66';

    private $api_key_secret ='597ac26b6596d659a582f1acd7b24a47';

    public function send($to_email, $to_name, $subject, $content) {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "mickael.letour@gmail.com",
                        'Name' => "La Boutique Portugaise"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2358811,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}

