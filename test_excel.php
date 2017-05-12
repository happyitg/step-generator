<?php

/** Report des erreurs */
error_reporting(E_ALL);
 
/** Chemin vers notre dossier contenant la librairie **/
// set_include_path(get_include_path() . PATH_SEPARATOR . 'PHPExcel/Classes/');
 
/** PHPExcel */
require_once './Classes/PHPExcel.php';
/** PHPExcel_Writer_Excel2007 */
 
include './Classes/PHPExcel/Writer/Excel2007.php';
 
// On crée notre objet Excel

$name_excel="Test_new_excel_made_the_".date('d-m-Y')."_at_".date('H-i-s').".xlsx";

$objPHPExcel = new PHPExcel();
 
//On ajoute les entètes du fichier
//Attention, un utf8_encode() est nécessaire pour les caractères comme 'é', 'è', ..
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Left');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Down');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Up');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Right');

 
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
$current_column = 'A';
$current_arrow = '1000';

for ($i=2; $i< 10; $i++){
// ajout de la fleche

	$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(25);
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setPath('./itg_arrows/'.$current_arrow.'.jpg');
	$objDrawing->setHeight(30);
	$objDrawing->setWidth(30);
	$objDrawing->setCoordinates($current_column.$i);
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


}
 
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