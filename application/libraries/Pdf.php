<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Include TCPDF library
require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');

class Pdf extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}