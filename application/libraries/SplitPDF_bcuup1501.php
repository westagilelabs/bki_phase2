<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once('fpdf181/fpdf.php');
require_once('FPDI/fpdi.php');
require('class.pdf2text.php');
require_once('S3.php');

class SplitPDF {	
	
	private $bucket = "bbatchelor-uploads";
	
	function __construct($config = array())
	{
		log_message('debug', 'SplitPDF Class Initialized');
	}
	
	function split_pdf($filename, $end_directory = false)
	{
		$folders = [];
		$a = new PDF2Text();
		$a->setFilename($filename); 
		$a->decodePDF();
		$data = $a->output(); 
		//mkdir("split/1496/tmp", 0777, true);
		$d = explode("This Pay Period\nImportant Messages:",$data);
		for($i=0;$i<count($d);$i++)
		{
			$c =  trim($d[$i]);
			if(!empty($c)){
				$folders[] =  $c[0].$c[1].$c[2].$c[3];
			}	
		}
	
		$pdf = new FPDI();
		$pagecount = $pdf->setSourceFile($filename); // How many pages?
		//$this->load->library('s3');		
		$s3 = new S3();
		
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
						// Will make directories under end directory that don't exist
						// Provided that end directory exists and has the right permissions
						mkdir($new_path, 0777, true);
						
						//chown($new_path, "www-data www-data");
					}					
					$end_directory = $new_path."/";
					$new_filename = $end_directory.str_replace('.pdf', '', $filename).'_'.$i.".pdf";
					//var_dump($new_filename);exit;
					$new_filename1 = $dest.str_replace('.pdf', '', $filename).'_'.$i.".pdf";
					$new_pdf->Output($new_filename, "F");					
					$s3->putObjectFile($new_filename ,$this->bucket ,$new_filename1 ,S3::ACL_PUBLIC_READ );
					
				} catch (Exception $e) {
					return false;
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
