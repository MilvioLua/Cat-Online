<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

	/*	
	   *	Developed by: DBCinfotech
       *	Date	: 20 November, 2015
       *	Bizpro Stock Manager ERP
       *	http://codecanyon.net/user/dbcinfotech
    */

class Barcode_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	// DECLARATION: CREATES BARCODE OF A PARTICULAR PRODUCT USING SERIAL NUMBER OF THE PRODUCT
	function create_barcode($serial_number)
	{
		// side effect: includes the font file for barcodes
		require_once('assets/barcode/class/BCGFontFile.php');

		// side effect: includes the color classes for barcodes
		require_once('assets/barcode/class/BCGColor.php');

		// side effect: includes the drawing classes for barcodes
		require_once('assets/barcode/class/BCGDrawing.php');

		// side effect: includes the barcode technology
		require_once('assets/barcode/class/BCGcode39.barcode.php');
		
		// Loading Font
		$font          = new BCGFontFile('assets/barcode/font/Arial.ttf', 18);
		// Don't forget to sanitize user inputs
		$text          = $serial_number;
		// The arguments are R, G, B for color.
		$color_black   = new BCGColor(0, 0, 0);
		$color_white   = new BCGColor(255, 255, 255);
		$drawException = null;
		try {
			$code = new BCGcode39();
			$code->setScale(2); // Resolution
			$code->setThickness(30); // Thickness
			$code->setForegroundColor($color_black); // Color of bars
			$code->setBackgroundColor($color_white); // Color of spaces
			$code->setFont($font); // Font (or 0)
			$code->parse($text); // Text
            $code->clearLabels();
		}
		catch (Exception $exception) {
			$drawException = $exception;
		}
		/* Here is the list of the arguments
		1 - Filename (empty : display on screen)
		2 - Background color */
		$drawing = new BCGDrawing('', $color_white);
		if ($drawException) {
			$drawing->drawException($drawException);
		} else {
			$drawing->setBarcode($code);
			$drawing->draw();
		}
		// Header that says it is an image (remove it if you save the barcode to a file)
		header('Content-Type: image/png');
		header('Content-Disposition: inline; filename="barcode.png"');
		// Draw (or save) the image into PNG format.
		$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
	}
}

/* End of file Barcode_model.php */
/* Location: ./application/models/Barcode_model.php */