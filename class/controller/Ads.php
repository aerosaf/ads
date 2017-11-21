<?php
/**
 * @copyright  Copyright (C) GDP. All rights reserved.
 * @author Aerosaf aerosaf@gmail.com
 */
namespace Aero\Controller;

defined('_EXE') or die;

require_once(PATH_MODEL.'/Client.php');

use \Aero\Model\Client;

class AdsApp
{
    public  function getDefaultRate($type) 
    {
        $defaultRate = [
            'classic' => 269.99,
            'standout' => 322.99,
            'premium' => 394.99
        ];

        return $defaultRate[$type];

    }

    public function getDiscounts($company)
    {
        $Client = new Client;
        $standOutDiscount = 0;
        $premiumDiscount = 0;
        
        foreach ($company as $name => $types) {
            foreach ($types as $type => $quantity) {
                // Apple gets a discount on Standout Ads where the price drops to $299.99 per ad.
                if ( $type == 'standout' && in_array( $name, array_keys( $Client->eligible('standoutDrop') ) ) ) {
                    $standOutDiscount = $quantity * ($this->getDefaultRate('standout') - $Client->eligible('standoutDrop', $name, 'discount'));
                }

                // Nike Gets a discount on Premium Ads where 4 or more are purchased. The price drops to $379.99 per ad.
                if ( $type == 'premium' && in_array( $name, array_keys( $Client->eligible('premiumDrop') ) ) ) {
                    if ( $quantity >= $Client->eligible('premiumDrop', $name, 'requirement') ) {
                        $premiumDiscount += $quantity * ($this->getDefaultRate('premium') - $Client->eligible('premiumDrop', $name, 'discount'));
                    }
                }
            }
        }

        return $premiumDiscount + $standOutDiscount;
    }

    public function getFreePacks($company)
    {
        $Client = new Client;
        $freePacks = [
            'classic'=>0,
            'standout'=>0,
            'premium'=>0,
        ];
        
        foreach ($company as $name => $types) {
            foreach ($types as $type => $quantity) {
                // Unilever gets a 3 for 2 deals on Classic Ads
                if ( in_array( $name, array_keys( $Client->eligible('freeClassic') ) ) ) {
                    if ( $quantity >= $Client->eligible('freeClassic', $name, 'requirement') ) {
                        $freePacks['classic'] += floor($quantity / $Client->eligible('freeClassic', $name, 'requirement') );
                    }
                }
            }
            
        }

        return $freePacks;
    }

    public function getSubTotal($company)
    {
        $sum = 0;

        foreach ($company as $name => $types) {
            foreach ($types as $type => $quantity) {
                $sum += ($this->getDefaultRate($type) * $quantity);
            }
        }

        return $sum;
    }

    public function getTotal($company) 
    {
        $discounts = $this->getDiscounts($company);
        $subTotal = $this->getSubTotal($company);

        return $subTotal - $discounts;
    }

    public function checkout($company)
    {
        $freePacks = $this->getFreePacks($company);

        foreach ($company as $name => $types) {
            $company[$name]['total'] = $this->getTotal($company);
            foreach ($types as $type => $quantity) {
                $company[$name][$type] += $freePacks[$type];
            }
        }

        return $company;
    }

    public function render()
    {   
        $name = isset($_GET['customer']) ? $_GET['customer'] : null;
        $content = $this->display('clients', $name);
        $frame = file_get_contents( PATH_VIEW.'/frame.html' );
        
        echo $output = str_replace("%%CONTENT%%", $content, $frame);
    }


    public function display($section = 'default', $name = null)
    {
        $output = '';

        switch($section) {
            case 'clients':
                if ($name == null) {
                    $output = $this->displayClients();
                } else {
                    $output = $this->displayClients($name);
                }
                break;
            case 'default':
                $output = $this->welcome();
                break;
            default:
                echo 'Opps';
        }

        return $output;
    }

    public function displayClients($company=null)
    {
        $Client = new Client;
        $Ads = new AdsApp;

        $output = '';
        $frame = file_get_contents( PATH_VIEW.'/default.html' );

        if ($company == null) {
            $clients = $Client->getClients();
            foreach ($clients as $name => $types) {
                $client =  $Ads->checkout($Client->getClients($name));
                $output .= $this->outputValue($frame, $client);
            }
        } else {
            $company = $Client->getClients($company);
            $client =  $Ads->checkout($Client->getClients(key($company)));
            $output = $this->outputValue($frame, $client);
        }

        return $output;
    }

    public function outputValue($output, $client)
    {
        foreach ($client as $name => $types) {
            $output = str_replace("%%NAME%%", $name, $output);

            $sku = '';
            $total = '';

            foreach ($types as $type => $quantity) {
                if ($type == 'total') {
                    $total = $quantity;
                } else {
                    $sku .= '<span class="'.$type.' btn">'.$type.' => '.$quantity.'</span>';
                }
            }

            $output = str_replace("%%SKU%%", $sku, $output);
            $output = str_replace("%%TOTAL%%", $total, $output);
        }

        return $output;
    }
}