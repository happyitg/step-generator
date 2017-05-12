<?php


// include classe PHPExcel pour la facture

error_reporting(E_ALL);
require_once './Classes/PHPExcel.php';
require_once './Classes/PHPExcel/Writer/Excel2007.php';


function get_candle( $current_arrow, $previous_arrow, $current_candle){

	switch ($current_arrow . ';' . $previous_arrow ){
		
		// cas ou le joueur est face à l'écran
		case '1000;0001':
		case '0001;1000':
			return 'face' ; break;
		
		// cas ou le joueur est tourné vers la droite
		case '0010;0001':
		case '0001;0010':
		case '1000;0100':
		case '0100;1000':
			return 'right'; break ;
			
		case '0001;0100':
		case '0100;0001':
		case '0010;1000':
		case '1000;0010':
			return 'left'; break;
			
		case '0100;0010':
		case '0010;0100':
			return $current_candle; break;
		default :
			return 'unknown'; break;
	
	}

}
		
// ini_set pour mémoire PHP

// ini_set("memory_limit",'1024M');
// ini_set("max_execution_time",'600');
// ini_set('display_errors', 1);

// debug.php

$debug = $_POST['debug'];
$tab_steps = explode("<br>" , $_POST['steps']);

$name_excel="Debug - ".date('Ymd_His').".xlsx";

$objPHPExcel = new PHPExcel();
// $objPHPExcel->getActiveSheet()->setTitle('Debug');

//On ajoute les entètes du fichier
//Attention, un utf8_encode() est nécessaire pour les caractères comme 'é', 'è', ..
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Left');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Down');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Up');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Right');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Candle');

//Gérer la taille de la colonne
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6);


/*
//Gérer le style de la police
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
 
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
//Mettre une bordure sur une case
$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
*/

$li=2;

$tab_column = 
array (
	'1000' => 'A',
	'0100' => 'B',
	'0010' => 'C',
	'0001' => 'D'
);

$current_candle = 'face';
$previous_arrow = '';

$count_candle_left = 0;
$count_candle_right = 0;
$count_candle_face = 0;
$count_candle_change = 0;
// echo "<pre>";
// print_r($tab_steps);
// echo "</pre>";

for ($i=0; $i< count($tab_steps); $i++){

// ajout de la fleche
	$current_arrow = $tab_steps[$i];
	
	
	if (($current_arrow != ',') && ($current_arrow != NULL) && ($current_arrow != '0000')){
		
		$current_column = $tab_column[$current_arrow];
		
		$objPHPExcel->getActiveSheet()->getRowDimension($li)->setRowHeight(25);
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./itg_arrows/'.$current_arrow.'.jpg');
		$objDrawing->setHeight(30);
		$objDrawing->setWidth(30);
		$objDrawing->setCoordinates($current_column.$li);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		
		$previous_candle = $current_candle ;
		$current_candle = get_candle ($current_arrow , $previous_arrow, $previous_candle);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$li, $current_candle);
		
		
		$previous_arrow = $current_arrow;
		if ($previous_candle != $current_candle) $count_candle_change ++;
		if ($current_candle == 'left') $count_candle_left ++;
		else if ($current_candle == 'right') $count_candle_right ++;
		else if ($current_candle == 'face')$count_candle_face ++;
		$li++;
		
	}
}
$li++;
$li++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$li, 'left candles :');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$li, $count_candle_left);
$li++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$li, 'right candles :');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$li, $count_candle_right);
$li++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$li, 'face candles :');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$li, $count_candle_face);
$li++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$li, 'changed candles :');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$li, $count_candle_change);
$li++;
// Sauvegarder notre fichier xlsx
// $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
// $objWriter->save('/tmp/'.$name_excel);

// extraire le fichier excel du navigateur

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$name_excel.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>