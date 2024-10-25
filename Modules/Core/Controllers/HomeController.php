<?php

namespace Modules\Core\Controllers;


use Modules\Core\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct() {}

    
    public function aboutApp()
    {
        return $this->successResponse([
            'text' => 'About App Text'
        ]);
    }

    public function faq()
    {
        return $this->successResponse([
            [
                'question' => 'Question 1',
                'answer' => 'Answer 1',
            ],
            [
                'question' => 'Question 2',
                'answer' => 'Answer 2',
            ],
            [
                'question' => 'Question 3',
                'answer' => 'Answer 3',
            ]
        ]);
    }

    public function privacyPolicy()
    {
        return $this->successResponse([
            'text' => 'privacy Policy Text'
        ]);
    }

    public function termsAndConditions()
    {
        return $this->successResponse([
            'text' => 'Terms and conditions Text'
        ]);
    }
}
