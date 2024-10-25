<?php

namespace Modules\Vendor\Controllers;

use Modules\Core\Controllers\Controller;

class BTCarController extends Controller {


    public function __construct()
    {
        
    }

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

    

}
