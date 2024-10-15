<?php
// DB Params
//error_reporting(E_ALL);
// define("DB_HOST", "localhost");
// define("DB_USER", "root");
// define("DB_PASS", "");
// define("DB_NAME", "goodscores");

// // App Root
// define('APPROOT', dirname(dirname(__FILE__)));
// // URL Root
// define('URLROOT', 'http://localhost/goodscores');
// // Site Name
// define('SITENAME', 'GoodScores');

define("DB_HOST", "localhost");
define("DB_USER", "stanvicc_concepts");
define("DB_PASS", "Ebenezer@30");
define("DB_NAME", "stanvicc_goodscores");

// App Root
define('APPROOT', dirname(dirname(__FILE__)));
// URL Root
define('URLROOT', 'https://goodscores.stanvic.com.ng');
// Site Name
define('SITENAME', 'Goodscores');



$month = date('M');
$term = '';
$year = date('Y');
switch ($month) {
    case 'Jan':
        $term = '2nd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Feb':
        $term = '2nd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Mar':
        $term = '2nd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Apr':
        $term = '2nd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'May':
        $term = '3rd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Jun':
        $term = '3rd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Jul':
        $term = '3rd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Aug':
        $term = '3rd term';
        $sch_session = $year - 1 . "/" . $year;
        break;
    case 'Sep':
        $term = '1st term';
        $sch_session = $year . "/" . $year + 1;
        break;
    case 'Oct':
        $term = '1st term';
        $sch_session = $year . "/" . $year + 1;
        break;
    case 'Nov':
        $term = '1st term';
        $sch_session = $year . "/" . $year + 1;
        break;
    case 'Dec':
        $term = '1st term';
        $sch_session = $year . "/" . $year + 1;
        break;
    default:
        $term = 'No active term found';
}
// Define Current Term
define('TERM', $term);
// Define Current School Session
define('SCH_SESSION', $sch_session);
