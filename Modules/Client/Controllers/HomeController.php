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
                ],
                'repair_instalation_service'=>\App\Models\Setting::first()?->repaire_installation_service??0,
                'tax_percentage'=>\App\Models\Setting::first()?->tax_percentage??10
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
            'text' => \App\Models\Setting::first()?->about_us??''
        ]);
    }

    public function faq()
    {
        $faq = [];
        $setting = \App\Models\Setting::first();
        if(isset($setting->id)){
            for ($i = 1; $i <= 10; $i++) {
                $question = $setting->{'question' . $i};
                $answer = $setting->{'answer' . $i};

                if (!empty($question) && !empty($answer)) {
                    $faq[] = [
                        'question' => $question,
                        'answer' => $answer,
                    ];
                }
            }

        }
        return $this->successResponse($faq);
    }

    public function privacyPolicy()
    {
        return $this->successResponse([
            'text' => \App\Models\Setting::first()?->privacy_policy??''
        ]);
    }

    public function termsAndConditions()
    {
        return $this->successResponse([
            'text' => \App\Models\Setting::first()?->terms_and_conditions??''
        ]);
    }
}
