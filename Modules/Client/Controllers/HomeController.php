<?php

namespace Modules\Client\Controllers;

use Modules\Core\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct() {}

    public function AppStart()
    {
        return $this->successResponse([
            'sliders' => [
                [
                    'image' => url('assets/temp/start/1.jpg'),
                    'text' => trans('Core::messages.start.0.text'),
                    'subtext' => trans('Core::messages.start.0.subtext'),
                ],
                [
                    'image' => url('assets/temp/start/2.jpg'),
                    'text' => trans('Core::messages.start.1.text'),
                    'subtext' => trans('Core::messages.start.1.subtext'),
                ]
            ]
        ]);
    }

    public function HomeSliders()
    {
        return $this->successResponse([
            'sliders' => [
                [
                    'image' => url('assets/temp/sliders/1.jpg'),
                    'clickable' => 'link',
                    'link' => 'http://google.com',
                    'product_id' => null
                ],
                [
                    'image' => url('assets/temp/sliders/2.jpg'),
                    'clickable' => 'product',
                    'link' => null,
                    'product_id' => 1
                ],
                [
                    'image' => url('assets/temp/sliders/3.jpg'),
                    'clickable' => null,
                    'link' => null,
                    'product_id' => null
                ]
            ]
        ]);
    }

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
