<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * $Id: S3.php 47 2009-07-20 01:25:40Z don.schonknecht $
 *
 * Copyright (c) 2008, Donovan Schönknecht.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Amazon S3 is a trademark of Amazon.com, Inc. or its affiliates.
 */


require_once('fpdf181/fpdf.php');
require_once('FPDI/fpdi.php');
require('class.pdf2text.php');
require_once('S3.php');
include 'vendor/autoload.php';//http://www.pdfparser.org/documentation

class SplitPDF {	
	
	private $bucket = "bbatchelor-uploads";
	
	function __construct($config = array())
	{
		log_message('debug', 'SplitPDF Class Initialized');
	}
	
	function split_pdf($filename, $end_directory = false,$fname)
	{
		$folders = [];	
		
		$parser = new \Smalot\PdfParser\Parser();
		try{
		$pdf    = $parser->parseFile($filename);
		}catch(Exception $e){
			var_dump($e);exit;
			return false;
		}
		$data = $pdf->getText();			
		
		//checking for AP EFT file
		$data1 = explode("Seq",$data);
		$dc = count($data1);
		if($dc > 1){
			$format = 1;			
		}
		else{
			$data2 = explode("30058",$data);
			//echo "one ....";var_dump($data2);exit;
			if(count($data2) > 1){
				$d2c = count($data2);
				for($i=1;$i<$d2c;$i++){
					$a = trim($data2[$i]);
					
					$empid = $a[0].$a[1].$a[2].$a[3];			
					$folders[] = intval($empid);
				}
			}
			if($folders[0] == 0){
				//e Employee's name, address, and ZIP code
				//TODO :3rd file
				/*
				$data3 = explode("Employee",$data);	
				$d3c = count($data3);
				for($i=0;$i<$d3c;$i++){
					
					if($i % 10 ==0){
						var_dump($data3($i));
						
					}
				}*/
				return false;
			}
		}
		//exit;
		//initialize for CI
		$this->CI =&get_instance();
		$this->CI->load->model('usermodel');
		
		if($format == 1){
			for($i=0;$i<$dc; $i++){
				if($dc % 2 !=0){ //for odd indexes
					$d1 = preg_split('/\s/',trim($data1[$i]));
					
					///lets assume first name and last name we got here
					$name = trim($d1[0]." ".$d1[1]);
					$eid = $this->CI->usermodel->getEmpIdByName($name);
					if(!empty($eid))
					{
						$folders[] = $eid;
					}
				}			
			}
		}
		
		//echo "<pre>";var_dump($folders);exit;
		
		try{
			$pdf = new FPDI();
			$pagecount = $pdf->setSourceFile($filename); // How many pages?
		}catch(Exception $e){
			return false;
		}
		//$folders = array_unique($folders);
		//$this->load->library('s3');			
		$s3 = new S3();
		//echo count($folders)." - ".$pagecount;exit;
		if(count($folders) == $pagecount)
		{
			// Split each page into a new PDF
			for ($i = 1; $i <= $pagecount; $i++) {
				$new_pdf = new FPDI();
				$new_pdf->AddPage();
				$new_pdf->setSourceFile($filename);
				$new_pdf->useTemplate($new_pdf->importPage($i));

				try {					
					$new_path = "split/".$folders[$i-1];
					$dest = $folders[$i-1]."/";
					if (!is_dir($new_path))
					{
						mkdir($new_path, 0777, true);
					}					
					$end_directory = $new_path."/";
					$date = date('Y-m-d');
					$new_filename = $end_directory.str_replace('.pdf', '', $fname).'_'.$date.".pdf";
					//var_dump($new_filename);exit;
					$new_filename1 = $dest.str_replace('.pdf', '', $fname).'_'.$date.".pdf";
					$new_pdf->Output($new_filename, "F");					
					$s3->putObjectFile($new_filename ,$this->bucket ,$new_filename1 ,S3::ACL_PUBLIC_READ );
					
				} catch (Exception $e) {
					return false;
					//echo "<pre>";var_dump($e);exit;
				}
			}
			return true;
		}
		else{
			//If page count is not matched with users count
			return false;
		}
	}
	 

}
