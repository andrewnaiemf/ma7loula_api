<?php

namespace Modules\Core\Controllers;


use Modules\Core\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct() {}
    public function AppStart()
    {
        return $this->successResponse([
       
                'repair_instalation_service'=>\App\Models\Setting::first()?->repaire_installation_service??0,
                'tax_percentage'=>\App\Models\Setting::first()?->tax_percentage??10
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
