<?php
define('SUBJECT', $data['params']->subject);
define('KLASS', $data['params']->class);
define('SCH_NAME', $data['sch']->name);
define('MOTTO', $data['sch']->motto);
define('E_TIME', $data['check']->duration);
// Include the main TCPDF library (search for installation path).
require APPROOT . '/views/TCPDF-main/tcpdf.php';

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
	private $klass = KLASS;
	private $sub = SUBJECT;
	private $year = SCH_SESSION;
	private $time = E_TIME;
	//Page header
	public function Header()
	{
		// Set font
		$this->setFont('helvetica', 'B', 17);
		// Title
		$this->Cell(0, 15, SCH_NAME, 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(6);
		// Set font
		$this->setFont('helvetica', 'I', 10);
		// Title
		$this->Cell(0, 15, MOTTO, 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(6);
		// Set font
		$this->setFont('helvetica', 'B', 11);
		// Title
		$this->Cell(0, 15, TERM . ' Examination', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		// Set font
		$this->setFont('helvetica', 'N', 11);
		$html = "
              <div class='row'>
                 <div class='col-md-3'>
                   <p><strong>Subject:</strong> $this->sub</p> 
                   <p><strong>Session:</strong> $this->year</p> 
                </div>
              </div>
              <style>
                h3{font-size: 12px; text-transform: lowercase !important;}  
                p{font-size: 11px; line-height: 0px;} 
                span{font-size: 12px;}
             </style>
            ";


		$html3 = "
              <div class='row'>
                 <div class='col-md-3'>
                   <p><strong>Time:</strong> $this->time </p>  
                  <p><strong>Class:</strong> $this->klass <strong></p>
                </div>
              </div>
              <style> 
                p{font-size: 11px; line-height: 0px;} 
                div{padding-right: 10px;}
             </style>
            ";
		$html2 = "
              <hr>
            ";

		$this->WriteHtmlCell(80, 10, '', 10, $html);
		$this->WriteHtmlCell(60, 20, 156, 10, $html3);
		$this->WriteHtmlCell('', 20, '', 22, $html2);
		//$this->Image($img_file, 170, 28, 32, 32, '', '', '', false, 300, '', false, false, 0);
	}

	// Page footer
	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->setY(-15);
		// Set font
		$this->setFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator('GoodScores');
$pdf->setAuthor('Nneli Ifeanyi');
$pdf->setTitle('GoodScores Sample Output');
$pdf->setSubject('Exam Questions Document');
$pdf->setKeywords('Stanvic, Stanvic Concepts, GoodScores, Teachers Aid, Set Exam Questions Online');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// add a page
$pdf->AddPage();


// set font
$pdf->setFont('times', 'BI', 12);
// set some text to print
$txt = <<<EOD
Objectives Questions
EOD;
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
// ----------------------------------------------------------


// set font
$pdf->setFont('times', 'N', 11);
// set some text to print
$txt = <<<EOD
Answer all questions in this section
--------------------------------------------------
EOD;
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
// ----------------------------------------------------------

// Loop through objectives questions
$num = 0;
foreach ($data['obj'] as $obj) {
	$num++;
	$pdf->setFont('times', 'N', 12);
	if (empty($obj->opt3)) {
		$obj->opt3 = '';
	} else {
		$obj->opt3 = '<b>(c)</b>  ' . $obj->opt3 . '&nbsp;';
	}

	if (empty($obj->opt4)) {
		$obj->opt4 = '';
	} else {
		$obj->opt4 = '<b>(d)</b>  ' . $obj->opt4;
	}
	$subtable = '<table>
				<tr>
					<td width="21"><b>' . $num . ')</b></td>
					<td style="width:667px;">' . $obj->question . '<br>
					<span style="font-size:12px;"><b>(a)</b> ' . $obj->opt1 . '&nbsp;<b>(b)</b> ' . $obj->opt2 . '&nbsp;' . $obj->opt3 . $obj->opt4 . '
					</span></td>
				</tr>
			</table>';
	$pdf->writeHTML($subtable, true, false, true, false, '');
} // End foreach loop

// ---------------------------------------------------------
// Output centered horizontal line
$pdf->setFont('times', 'N', 10);
$hr = <<<EOD

 -------------------------------------------------------
EOD;
$pdf->Write(0, $hr, '', 0, 'C', true, 0, false, false, 0);
// Output a centered horizontal line Ends
//--------------------------------------------------------------
// Output a centered text string with bold font and italic font style 12 pixels size
$pdf->setFont('times', 'BI', 12);
$hr2 = <<<EOD
Theory Questions
EOD;
$pdf->Write(0, $hr2, '', 0, 'C', true, 0, false, false, 0);
// Output a centered text string Ends
// ------------------------------------------------------------

// Output a centered text string with normal font 10 pixels size
$pdf->setFont('times', 'N', 10);
$amt = $data['check']->choice;
$ins = <<<EOD
Answer any $amt questions of your choice
-------------------------------------------------------
EOD;
$pdf->Write(0, $ins, '', 0, 'C', true, 0, false, false, 0);
// Output a centered text string Ends..
// -------------------------------------------------------------

// Loop through theory questions
$num2 = '';
foreach ($data['theory'] as $theory) {
	$num2++;
	$pull_each = $this->postModel->pullEach($theory->questionID, $theory->paperID);
	$pdf->setFont('times', 'N', 11);
	$subtable2 = '<table>
				<tr>
					<td width="25"><b>' . $num2 . 'a)</b></td>
					<td style="width:667px;">' . $pull_each->questionA . '<br>';
	if (!empty($pull_each->Ai)) {
		$subtable2 .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Ai . '</span>
						
		';
	}
	if (!empty($pull_each->Aii)) {
		$subtable2 .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Aii . '</span>
						
		';
	}
	if (!empty($pull_each->Aiii)) {
		$subtable2 .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Aiii . '</span>
						
		';
	}
	if (!empty($pull_each->Ai)) {
		$subtable2 .= '
					<span><b>(iv)&nbsp;</b>' . $pull_each->Ai . '</span>
						
		';
	}
	$subtable2 .= '			
					</td>
				</tr>
			</table>';

	if (!empty($pull_each->questionB)) {
		$subtable2 .= '<table>
				<tr>
					<td width="25"><b>' . $num2 . 'b)</b></td>
					<td style="width:667px;">' . $pull_each->questionB . '<br>';
	}


	if (!empty($pull_each->Bi)) {
		$subtable2 .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Bi . '</span>
						
		';
	}
	if (!empty($pull_each->Bii)) {
		$subtable2 .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Bii . '</span>
						
		';
	}
	if (!empty($pull_each->Biii)) {
		$subtable2 .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Biii . '</span>
						
		';
	}
	if (!empty($pull_each->Biv)) {
		$subtable2 .= '
					<span><b>(iv)&nbsp;</b>' . $pull_each->Biv . '</span>
						
		';
	}
	$subtable2 .= '			
					</td>
				</tr>
			</table>';

	if (!empty($pull_each->questionC)) {
		$subtable2 .= '<table>
				<tr>
					<td width="25"><b>' . $num2 . 'c)</b></td>
					<td style="width:667px;">' . $pull_each->questionC . '<br>';
	}


	if (!empty($pull_each->Ci)) {
		$subtable2 .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Ci . '</span>
						
		';
	}
	if (!empty($pull_each->Cii)) {
		$subtable2 .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Cii . '</span>
						
		';
	}
	if (!empty($pull_each->Ciii)) {
		$subtable2 .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Ciii . '</span>
						
		';
	}

	$subtable2 .= '			
					</td>
				</tr>
			</table>';

	if (!empty($pull_each->questionD)) {
		$subtable2 .= '<table>
				<tr>
					<td width="25"><b>' . $num2 . 'd)</b></td>
					<td style="width:667px;">' . $pull_each->questionD . '<br>';
	}



	$subtable2 .= '			
					</td>
				</tr>
			</table>';
	//$pdf->writeHTML($subtable2, true, false, true, false, '');
} // End foreach loop



//Close and output PDF document
$pdf->Output('outputed.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
