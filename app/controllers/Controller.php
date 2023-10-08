<?php

/**
 *
 * PNEU XPARTNER B2B basic controller.
 *
 * @package     xPartnerPneuB2B
 * @version     1.3
 * @copyright   Copyright (c) 2022 Open Mage / Stanislav Puffler (https://www.openmage.cz)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Controller {

    protected $f3;
    protected $view;
    protected $db;

    protected $seasonChars = array("L", "Z", "W");
    protected $widths = array(125,135,145,155,165,175,185,195,205,215,225,235,245,255,265,275,285,295,305,315,325);
    protected $heights = array(25,30,35,40,45,50,55,60,65,70,75,80);
    protected $diameters = array("R10", "R12", "R13", "R14", "R15", "R16", "R17", "R18", "R19", "R20", "R21", "R22", "R23", "R24");

    function beforeroute() {

    }

    function afterroute() {

    }

    function __construct() {

        $f3 = Base::instance();
        $view = View::instance();

        $db = new DB\SQL(
            $f3->get('db_dns'),
            $f3->get('db_user'),
            $f3->get('db_pass'),
            array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION )
        );

	    $this->f3=$f3;
	    $this->view=$view;
	    $this->db=$db;
    }

}

?>
