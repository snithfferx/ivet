<?php
    class ViewBuilderReference {
        public function viewBuilder(string $type, array $data) {
            try {
                if (is_array($data)) {
                    if ($type == "[error") {
                        if (isset($data['view'])) {
                            $result = $this->view($data);
                        } else {
                            $result = $this->message($data);
                        }
                    } elseif ($type == "messge") {
                        $result = $this->message($data);
                    } elseif ($type == "alert") {
                        $result = $this->message($data);
                    } else {
                        $result = $this->message($data);
                    }
                } else {
                    $result = $this->message($data);
                }
            } catch (Exception $exception) {
                $data = [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode(),
                    'line'    => $exception->getLine(),
                    'trace'   => $exception->getTraceAsString()
                ];
                $result = $this->message($data);
            }
            return $result;
        }
        protected function view (array $data) {
            $response = [
                'view' => [
                    'name' => "_latout.layout",
                    'type' => "view",
                    'data' => [
                        'module' => "_shared",
                        'layout' => "",
                        'template' => "templates",
            
                    ]
                ],
                'data' => [
                    'content' => [
                        'head' => [
                            'lang' => "en",
                            'title' => "APP | Name",
                            'meta' => [
                                ['name'=>"msapplication-TitleColor",'content'=>"#2196f3"],
                                ['name'=>"theme-color",'content'=>"#268bdb"],
                                ['name'=>"description" ,"content"=>'Compras, Ventas, Producción e Importaciones'],
                                ['name'=>"author",'content'=>"Snithfferx, Bytes4Run"]
                            ],
                            'icon' => [
                                "<link rel='icon' type='image/png' sizes='192x192' href='assets/img/brand_icons/brand_logo_192.png'>
                                <link rel='icon' type='image/png' sizes='32x32' href='assets/img/brand_icons/brand_logo_32.png'>
                                <link rel='icon' type='image/png' sizes='16x16' href='assets/img/brand_icons/brand_logo_16.png'>
                                <link rel='mask-icon' href='assets/img/brand_icons/brand_logo.svg' color='#563d7c'>"
                            ],
                            'css' => [
                                '<link rel="stylesheet" href="assets/css/bootstrap/fontawesome.min.css">
                                <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.min.css">'
                            ]
                        ],
                        'body' => [
                            "Esta es una vista."
                        ]
                    ]
                ]
            ];
            return $response;
        }
        protected function message (array $data) {
            $response = [
                'view' => [
                    'name' => "_latout.layout",
                    'type' => "view",
                    'data' => [
                        'module' => "_shared",
                        'layout' => "",
                        'template' => "templates",
            
                    ]
                ],
                'data' => [
                    'content' => [
                        'head' => [
                            'lang' => "en",
                            'title' => "APP | Name",
                            'meta' => [
                                ['name'=>"msapplication-TitleColor",'content'=>"#2196f3"],
                                ['name'=>"theme-color",'content'=>"#268bdb"],
                                ['name'=>"description" ,"content"=>'Compras, Ventas, Producción e Importaciones'],
                                ['name'=>"author",'content'=>"Snithfferx, Bytes4Run"]
                            ],
                            'icon' => [
                                "<link rel='icon' type='image/png' sizes='192x192' href='assets/img/brand_icons/brand_logo_192.png'>
                                <link rel='icon' type='image/png' sizes='32x32' href='assets/img/brand_icons/brand_logo_32.png'>
                                <link rel='icon' type='image/png' sizes='16x16' href='assets/img/brand_icons/brand_logo_16.png'>
                                <link rel='mask-icon' href='assets/img/brand_icons/brand_logo.svg' color='#563d7c'>"
                            ],
                            'css' => [
                                '<link rel="stylesheet" href="assets/css/bootstrap/fontawesome.min.css">
                                <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.min.css">'
                            ]
                        ],
                        'body' => [
                            "Esto es un Mensaje."
                        ]
                    ]
                ]
            ];
            return $response;
        }
    }
?>