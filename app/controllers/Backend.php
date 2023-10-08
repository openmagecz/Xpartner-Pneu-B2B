<?php

/**
 *
 * PNEU XPARTNER B2B backend operations related to the XML download, data parsing and later importing to MySQL database.
 *
 * @package     xPartnerPneuB2B
 * @version     1.7
 * @copyright   Copyright (c) 2022 Open Mage / Stanislav Puffler (https://www.openmage.cz)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Backend extends Controller {
    
    private $url = 'https://xpartner.net.pl/index.php?a=b2b_api_offerFile&m=getFile&hash=abcdefg1234567';
    private $file = '../data/xpartner.xml';
    private $dom;

    /**
     * Main method / constructor
     */
    public function index() {

        // Script start time
        $time_start = microtime(true);

        // Check if index method is called using Command Line Interface
        $sapi_type = php_sapi_name();
        if(substr($sapi_type, 0, 3) == 'cli') {

            // Download and save XML file from xPartner.net.pl
            if(file_exists($this->file)) unlink($this->file);
            $this->downloadRawXML($this->url, $this->file);

            // Check if XML is valid
            if($this->checkXML($this->file)) {

                /* Import XML nodes using FOREACH cycle and database inserts */

                // Prepare DOM object
                $this->dom = $this->createDOMDocument($this->file);

                /*
                 *
                 * Start MySQL transaction to be sure inserts are done ones together.
                 * But first, try it without transaction, to see, which SQL insert is faulty.
                 * 
                 */
                $this->db->begin();

                // Truncate table before each update
                $query = "TRUNCATE TABLE pneux";
                $this->db->exec($query);

                // Get table rows from XML file row nodes
                $rows = $this->dom->getElementsByTagName("row");
                $rowsCount = $rows->length; // about 13 651 rows in DB now incl. complete wheel ones, 11 627 final results

                foreach ( $rows as $row )  {
                    
                    // Get all data from row and count attributes
                    $columns = $row->getElementsByTagName("column");
                    $columnsLength = $columns->length;

                    if($columnsLength == 42) {

                        /* Filter rows with value "Complete Wheel" in columnNumber 36, which is 35 in array started by 0 */
                        $tcompletewheel = $columns->item(35)->nodeValue;
                        if($tcompletewheel == "KOMPLETNE KOŁO") continue;

                        /* Filter rows with width outside of 125 to 325 range with steps of 10 */
                        $twidth = str_replace('"', '', $columns->item(28)->nodeValue);
                        if(strpos($twidth, ',') !== false) continue;
                        if(!in_array(intval($twidth), $this->widths)) continue;
                        $twidth = $this->db->quote($twidth);

                        /* Filter rows with width outside of 25 to 80 range with steps of 5 */
                        $theight = str_replace('"', '', $columns->item(25)->nodeValue);
                        if(!in_array(intval($theight), $this->heights)) continue;
                        $theight = $this->db->quote($theight);

                        /* Filter rows with width outside of R12 to R24 range */
                        $tdiameter = str_replace('"', '', $columns->item(27)->nodeValue);
                        if(strpos($tdiameter, '-') !== false) continue;
                        if(strpos($tdiameter, ',') !== false) continue;
                        if(!in_array($tdiameter, $this->diameters)) continue;
                        $tdiameter = $this->db->quote($tdiameter);

                        /* Get the name of tyres */
                        $tname = $columns->item(3)->nodeValue;
                        
                        // remove DEMO substring from the name
                        $tname = str_replace("DEMO","",$tname);

                        // remove season from tyre name for specific producers (LING LONG and GOOD-YEAR)
                        $tname = str_replace("LINGLONG Z", "LINGLONG ",$tname);
                        $tname = str_replace("LINGLONG L", "LINGLONG ",$tname);
                        $tname = str_replace("LINGLONG W", "LINGLONG ",$tname);
                        $tname = str_replace("GOOD-YEAR Z", "GOODYEAR ",$tname);
                        $tname = str_replace("GOOD-YEAR L", "GOODYEAR ",$tname);
                        $tname = str_replace("GOOD-YEAR W", "GOODYEAR ",$tname);
                        $tname = str_replace("GOOD-YEAR ", "GOODYEAR ",$tname);

                        // remove year in [] within name
                        $tname = preg_replace('/\[[^\[\]]*\]/', '', $tname);

                        // remove extra spaces within name
                        $tname = preg_replace( '/\s+/', ' ', $tname);

                        /* Remove part of the year first char (Z,L,W) from name */
                        $tproducer = $columns->item(20)->nodeValue;
                        if($tproducer == "CHIŃSKIE") continue;
                        
                        // check if name starts with tyres producer, if not, do not change anything
                        if(strpos($tname,$tproducer) !== FALSE) {
                            
                            $tnameWP = ltrim(str_replace($tproducer, "", $tname));
                            $tseason = substr($tnameWP, 0, 1);

                            if(in_array($tseason, $this->seasonChars)) {
                                $tname = $tproducer . " " . substr($tnameWP, 1);
                            }

                        }

                        $tproducer = $this->db->quote($tproducer);

                        // sanitaze empty noncharacter attributes and numeric params with inches " breaking SQL inserts
                        $tloadindex = $columns->item(21)->nodeValue;
                        if($tloadindex == "") $tloadindex = "NULL";

                        // calculate final price using given business margin
                        $margin = 250;
                        $roundto = 10;
                        $price = ceil(($columns->item(11)->nodeValue + $margin)/$roundto) * $roundto;

                        // translate season to Czech
                        $season = $columns->item(26)->nodeValue;
                        switch($season)
                        {
                            case "WIELOSEZONOWA":
                                $season = $this->db->quote("celoroční");
                                break;
                            case "LETNIA":
                                $season = $this->db->quote("letní");
                                break;
                            case "ZIMOWA":
                                $season = $this->db->quote("zimní");
                                break;
                            case "NIE KLASYFIKOWANA SEZONOWO":
                                $season = $this->db->quote("neznámé");
                                break;
                            default:
                                $season = $this->db->quote("neznámé");
                                break;
                        }

                        $query = "INSERT INTO `pneux` (`ordinal`, `index`, `name`, `qty`, `netprice`, `grossprice`, `finalprice`, `weight`, 
                        `ean1`, `ean2`, `ean3`, `assortment`, `producer`, `loadindex`, `speedindex`, `class`, `tread`, `height`, `season`, 
                        `diameter`, `width`, `type`, `labels`, `rollingresistance`, `wetgrip`, `noise`, `noiseemission`, `completewheel`, 
                        `year`, `retreaded`, `cosmeticdefect`) VALUES (
                        " . $columns->item(0)->nodeValue . ", " . $columns->item(1)->nodeValue . ", " . $this->db->quote($tname) . ", 
                        " . $columns->item(4)->nodeValue . ", " . $columns->item(10)->nodeValue . ", " . $columns->item(11)->nodeValue . ", 
                        " . $price . ", " . $this->db->quote($columns->item(12)->nodeValue) . ", 
                        " . $this->db->quote($columns->item(14)->nodeValue) . ", " . $this->db->quote($columns->item(15)->nodeValue) . ", 
                        " . $this->db->quote($columns->item(16)->nodeValue) . ", " . $this->db->quote($columns->item(19)->nodeValue) . ", 
                        " . $tproducer . ", " . $tloadindex . ", 
                        " . $this->db->quote($columns->item(22)->nodeValue) . ", " . $this->db->quote($columns->item(23)->nodeValue) . ", 
                        " . $this->db->quote($columns->item(24)->nodeValue) . ", " . $theight . ", " . $season . ", 
                        " . $tdiameter . ", " . $twidth . ", " . $this->db->quote($columns->item(29)->nodeValue) . ", 
                        " . $this->db->quote($columns->item(30)->nodeValue) . ", " . $this->db->quote($columns->item(31)->nodeValue) . ", " . $this->db->quote($columns->item(32)->nodeValue) . ", 
                        " . $this->db->quote($columns->item(33)->nodeValue) . ", " . $this->db->quote($columns->item(34)->nodeValue) . ", " . $this->db->quote($tcompletewheel) . ", 
                        " . $this->db->quote($columns->item(36)->nodeValue) . ", " . $this->db->quote($columns->item(37)->nodeValue) . ", " . $this->db->quote($columns->item(38)->nodeValue) . " 
                        )";

                        try {

                            $this->db->exec($query);

                        } catch (Exception $e) {

                            echo $e->getMessage() . "\n";
                            echo $query . "\n";

                        }
                        
                    } else {
                        echo "Data column count is not 42. XML feed is not valid for import to MySQL.";
                    }

                }

                // Commit inserts transaction into MySQL
                $this->db->commit();

            } else {
                echo "HTTP/1.1 403 Forbidden. Already started or too many requests, please try later.";
            }

        /* Do not run main routine outside of shell, browser calls denied */
        } else {
            echo "You're not allowed to execute this operation from your web browser. This is backend part of the application.";
        }

        /* Script end time */
        $time_end = microtime(true);

        /* Execution time in minutes */
        $execution_time = ($time_end - $time_start)/60;
        echo "\n Total Execution Time: " . $execution_time . " minutes\n";
    }

    /**
     *
     * Download raw XML file.
     *
     * @param string $xml XML feed URL
     * @param string $pathTo Local XML file name
     *
     */
    protected function downloadRawXML($xml, $pathTo) {

        $ch = curl_init($xml);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url,
        ));

        $data = curl_exec($ch);
        curl_close($ch);

        file_put_contents($pathTo, $data);

    }

    /**
     *
     * Creates DOM Document from given XML file
     *
     * @param string $xml
     * @return DOMDocument
     *
     */
    protected function createDOMDocument($xml) {

        $doc = new DOMDocument("1.0", "UTF-8");
        $doc->preserveWhiteSpace = false;
        $doc->load($xml);
        $doc->formatOutput = true;

        return $doc;

    }

    /**
     *
     * @param string $xmlfile Local XML file name
     *
     * return boolean
     *
     */
    private function checkXML($xmlfile) {

    	if(file_exists($xmlfile)) {

    		$xmlcontent = file_get_contents($xmlfile);
    		$xmlstarts = substr($xmlcontent, 0, 38);

    		if($xmlstarts == '<?xml version="1.0" encoding="utf-8"?>') {
       			return true;
    		} else {
    			return false;
    		}

    	} else {
    		return false;
    	}

    }    
    
}
