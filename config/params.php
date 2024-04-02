<?php

return [
    'bsVersion' => '5.x',
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'endpoints' => [
        'service' => [
            //'url' => 'https://connektaqa.siesacloud.com/api/v3/ejecutarconsulta',
            'url' => 'https://serviciosconnekta.siesacloud.com/api/v3/ejecutarconsulta',
            'conniKey' => 'Connikey-grupomayorista-QJBYOFU3',
            'conniToken' => 'QJBYOFU3RTFVNKMWRDFRNUEWSDJSNVQ2SJNJMLU3RZJAOESZVJDLMW',
            'idCompania' => '8203',
        ],
    ],

    'proyectoNombre' => 'GRUMALOG traspaso',
    'tipodocumento_traspaso' => '2TB',
    'tipodocumento_crossdocking' => '2TA',
    'tituloTraspaso' => 'TRASPASO DE MERCANCIA',
    'grupo' => 'Grupo mayorista S.A',
    'nit' => '900.091.175',
    'direccion'=> 'Cr 32 14-25',
    'tel'=> '3229200',
        
    'icon-framework' => \kartik\icons\Icon::FAS,  // Font Awesome Icon framework
];
