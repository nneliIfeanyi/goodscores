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

	// Page footer
	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->setY(-10);
		// Set font
		$this->setFont('helvetica', 'I', 7);
		// Page number
		//$this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, 'Powered by Stanvic Concepts', 0, false, 'C', 0, '', 0, false, 'T', 'M');
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

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

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


// ---------------------------------------------------------
// add a page
$pdf->AddPage();
$motto = $data['sch']->motto;
$name = $data['sch']->name;
$klass = KLASS;
$sub = SUBJECT;
$year = SCH_SESSION;
$time = E_TIME;

// Set font
$pdf->setFont('helvetica', 'B', 17);
// Title
$pdf->Cell(0, 0, SCH_NAME, 0, false, 'C', 0, '', 2, false, 'M', 'M');
$pdf->Ln(6);
// Set font
$pdf->setFont('helvetica', 'I', 10);
// Title
$pdf->Cell(0, 15, MOTTO, 0, false, 'C', 0, '', 0, false, 'M', 'M');
$pdf->Ln(6);
// Set font
$pdf->setFont('times', 'B', 11);
// Title
$pdf->Cell(0, 15, TERM . ' Examination', 0, false, 'C', 0, '', 0, false, 'M', 'M');
$pdf->setFont('times', 'N', 12);
$html = "
              <div class='row'>
                 <div class='col-md-3'>
                   <p><strong>Subject: </strong> $sub</p> 
                   <p><strong>Session: </strong> $year</p> 
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
                   <p><strong>Time: </strong> $time </p>  
                  <p><strong>Class: </strong> $klass <strong></p>
                </div>
              </div>
              <style> 
                p{font-size: 11px; line-height: 0px;} 
                div{padding-right: 10px;}
             </style>
            ";

$hr = "
              <hr>
            ";
$pdf->WriteHtmlCell('', 20, '', 25, $hr);
$pdf->WriteHtmlCell(80, 10, '', 13, $html);
$pdf->WriteHtmlCell(60, 20, 156, 13, $html3);
// -------------------------------------------------------------

$pdf->Ln(15);
$pdf->setFont('times', 'BI', 12);
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
					<span style="font-size:13px;"><b>(a)</b> ' . $obj->opt1 . '&nbsp;<b>(b)</b> ' . $obj->opt2 . '&nbsp;' . $obj->opt3 . $obj->opt4 . '
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
	$table = '<table>
				<tr>
					<td width="25"><b>' . $num2 . 'a)</b></td>
					<td style="width:667px;">' . $pull_each->questionA . '<br>';
	if (!empty($pull_each->Ai)) {
		$table .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Ai . '</span>
						
		';
	}
	if (!empty($pull_each->Aii)) {
		$table .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Aii . '</span>
						
		';
	}
	if (!empty($pull_each->Aiii)) {
		$table .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Aiii . '</span>
						
		';
	}
	if (!empty($pull_each->Aiv)) {
		$table .= '
					<span><b>(iv)&nbsp;</b>' . $pull_each->Aiv . '</span>';
	}
	$table .= '</td>
	</tr>
</table>';

	if (!empty($pull_each->questionB)) {
		$table .= '<table>
		<tr>
					<td width="25"><b>' . $num2 . 'b)</b></td>
					<td style="width:667px;">' . $pull_each->questionB . '<br>';
	}


	if (!empty($pull_each->Bi)) {
		$table .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Bi . '</span>
						
		';
	}
	if (!empty($pull_each->Bii)) {
		$table .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Bii . '</span>
						
		';
	}
	if (!empty($pull_each->Biii)) {
		$table .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Biii . '</span>
						
		';
	}
	if (!empty($pull_each->Biv)) {
		$table .= '
					<span><b>(iv)&nbsp;</b>' . $pull_each->Biv . '</span>';
	}
	$table .= '</td>
	</tr>
</table>';



	if (!empty($pull_each->questionC)) {
		$table .= '<table>
		<tr>
					<td width="25"><b>' . $num2 . 'c)</b></td>
					<td style="width:667px;">' . $pull_each->questionC . '<br>';
					if (!empty($pull_each->Ci)) {
		$table .= '
					<span><b>(i)&nbsp;</b>' . $pull_each->Ci . '</span>
						
		';
	}
	if (!empty($pull_each->Cii)) {
		$table .= '
					<span><b>(ii)&nbsp;</b>' . $pull_each->Cii . '</span>
						
		';
	}
	if (!empty($pull_each->Ciii)) {
		$table .= '
					<span><b>(iii)&nbsp;</b>' . $pull_each->Ciii . '</span>
						
		';
	}
		$table .= '</tr>
	</table';
	}

	if (!empty($pull_each->questionD)) {
		$table2 = '<table>
		<tr>
					<td width="25"><b>' . $num2 . 'd)</b></td>
					<td style="width:667px;">' . $pull_each->questionD . '</td>
		</tr>
	</table';
	}

	$pdf->writeHTML($table, true, false, true, false, '');
	$pdf->writeHTML($table2, true, false, true, false, '');
} // End foreach loop



//Close and output PDF document
$pdf->Output('outputed.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
