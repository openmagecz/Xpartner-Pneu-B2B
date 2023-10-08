<?php

/**
 *
 * PNEU XPARTNER B2B web catalog, entry point of the web application - bootstrap.
 *
 * @package     xPartnerPneuB2B
 * @version     1.2
 * @copyright   Copyright (c) 2023 Open Mage / Stanislav Puffler (https://www.openmage.cz)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

// PSR autoload
require_once('vendor/autoload.php');

// base instance of F3 framework
$f3 = Base::instance();

// Own configuration outside of index.php in its own config files
$f3->config('app/config/setup.cfg');
$f3->config('app/config/routes.cfg');

$f3->set('ONERROR',
    function($f3) {
        if($f3->get('ERROR.code') == '404'){
            $f3->reroute('/stranka-nenalezena');
        } else {
            echo $this->f3->get('ERROR.text');
        }
    }
);

$f3->run();
?>
