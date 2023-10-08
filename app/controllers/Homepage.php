<?php

/**
 *
 * PNEU XPARTNER B2B homepage / search results controller.
 *
 * @package     xPartnerPneuB2B
 * @version     1.0
 * @copyright   Copyright (c) 2022 Open Mage / Stanislav Puffler (https://www.openmage.cz)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Homepage extends Controller {

    /**
     * Main method for search results / constructor
     */
    public function index() {

        /* Clear pneux array on each index method access first */
        $this->f3->clear('pneux');

        /* Prepare data collections for search form select boxes */
        $this->f3->set('sirky', $this->db->exec('SELECT DISTINCT `width` FROM `pneux` ORDER BY `width`'));
        $this->f3->set('profily', $this->db->exec('SELECT DISTINCT `height` FROM `pneux` ORDER BY `height`'));
        $this->f3->set('prumery', $this->db->exec('SELECT DISTINCT `diameter` FROM `pneux` ORDER BY `diameter`'));
        $this->f3->set('rocniobdobi', $this->db->exec('SELECT DISTINCT `season` FROM `pneux` ORDER BY `season`'));

        /* First check if form was submitted using GET method and if there are some values */
        if($_GET['sirka'] == "all" && $_GET['profil'] == "all" && $_GET['prumer'] == "all" && $_GET['obdobi'] == "all") {

            if($this->f3->exists('pneux')) $this->f3->clear('pneux');

        } else {

            $width = "all";
            $height = "all";
            $diameter = "all";
            $season = "all";

            // Get tyre width and check for meaningful value
            if($_GET['sirka'] !== "all" && in_array($_GET['sirka'], $this->widths)) $width = $_GET['sirka'];
            if($_GET['profil'] !== "all" && in_array($_GET['profil'], $this->heights)) $height = $_GET['profil'];
            if($_GET['prumer'] !== "all" && in_array($_GET['prumer'], $this->diameters)) $diameter = $_GET['prumer'];
            if($_GET['obdobi'] !== "all") $season = $_GET['obdobi'];

            /* Construct the search query */
            $searchQuery = 'SELECT `name`, `qty`, `finalprice`, `producer`, `speedindex`, `width`, `height`, `diameter` FROM `pneux`';


            $params = array();
            if($width !== "all") {
                $params["width"] = $width;
            }
            if($height !== "all") {
                $params["height"] = $height;
            }
            if($diameter !== "all") {
                $params["diameter"] = $diameter;
            }
            if($season !== "all") {
                $params["season"] = $season;
            }

            $NumberOfParams = count($params);

            // Do we have a where clause ?
            if ($NumberOfParams > 0) {
                $whereClause = " WHERE ";
                $i = 0;
                foreach ($params as $ParamName => $ParamValue) {
                    $whereClause .= "`" . $ParamName . "` = \"" . $ParamValue . "\"";
                    // Are we not at last param ?
                    if (++$i < $NumberOfParams) {
                        $whereClause .= " AND ";
                    }
                }
                $searchQuery .= $whereClause . " AND `finalprice` > 499 AND `qty` > 1";
            }

            $searchQuery .= ' ORDER BY finalprice ASC, qty DESC';

            // DEBUG ONLY
            $this->f3->set('query', $searchQuery);            

            /* Get the search results */
            $this->f3->set('pneux', $this->db->exec($searchQuery));

        }

        /* Render homepage view */
        echo $this->view->render('HomepageView.php','text/html');  
    }
    
}