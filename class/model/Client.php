<?php
/**
 * @copyright  Copyright (C) GDP. All rights reserved.
 * @author Aerosaf aerosaf@gmail.com
 */
namespace Aero\Model;

defined('_EXE') or die;


class Client
{
    public function getClients($company=null)
    {
        // From Database
        $clients = [
            'default'=> [
                'classic' => 1,
                'standout' => 1,
                'premium' => 1
            ],
            'unilever' => [
                'classic' => 2,
                'standout' => 0,
                'premium' => 1,
            ],
            'apple' => [
                'classic' => 0,
                'standout' => 3,
                'premium' => 1
            ],
            'nike' => [
                'classic' => 0,
                'standout' => 0,
                'premium' => 4           
            ],
            'ford' => [
                'classic' => 4,
                'standout' => 1,
                'premium' => 3
            ]

        ];

        $result = $company == null ? $clients : [$company => $clients[$company]];

        return $result;
    }

    public function eligible($pack, $name = null, $param = null)
    {
        // From Database
        $discountPacks = [
            'freeClassic' => [
                'unilever' => [
                    'requirement' => 2,
                    'free' => 1
                ],
                'ford' => [
                    'requirement' => 4,
                    'free' => 1
                ]
            ],
            'standoutDrop' => [
                'apple'=> [
                    'requirement' => 0,
                    'discount' => 299.99
                ],
                'ford' => [
                    'requirement' => 0,
                    'discount' => 309.99
                ]
            ],
            'premiumDrop' => [
                'nike' => [
                    'requirement' => 4,
                    'discount' => 379.99
                ],
                'ford' => [
                    'requirement' => 3,
                    'discount' => 389.99
                ]
            ]
        ];

        return $param == null ? $discountPacks[$pack] : $discountPacks[$pack][$name][$param];
    }
}