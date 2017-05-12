<?php

// pour réafficher la page d'index lorsque le formulaire est rempli

include ("index.php");



/* a gérer 

drills 
candle
nb de mesure
vitesse de mesure

*/

function next_arrow_v2($foot, $current, $wrong){

	switch ($foot.';'.$current){
		
		case "d;0001":
			$array=array('1000', '0100', '0010');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
		case "d;0010":
			$array=array('1000', '0100');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
		case "d;0100":
			$array=array('1000', '0010');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
		case "g;1000":
			$array=array('0001', '0100', '0010');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
		case "g;0100":
			$array=array('0010', '0001');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
		case "g;0010":
			$array=array('0100', '0001');
			shuffle($array);
			if ($array[0] == $wrong) return $array[1];
			else return $array[0];
		break;
	
	
	}

}

function next_arrow($foot, $current){

	switch ($foot.';'.$current){
		
		case "d;0001":
			$array=array('1000', '0100', '0010');
			shuffle($array);
			return $array[0];
		break;
		case "d;0010":
			$array=array('1000', '0100');
			shuffle($array);
			return $array[0];
		break;
		case "d;0100":
			$array=array('1000', '0010');
			shuffle($array);
			return $array[0];
		break;
		case "g;1000":
			$array=array('0001', '0100', '0010');
			shuffle($array);
			return $array[0];
		break;
		case "g;0100":
			$array=array('0010', '0001');
			shuffle($array);
			return $array[0];
		break;
		case "g;0010":
			$array=array('0100', '0001');
			shuffle($array);
			return $array[0];
		break;
	
	
	}

}

// partie PHP

if (!isset($_POST['send'])) die("Faut passer par le formulaire petit malin ;)");
if (!isset($_POST['nbeat'])) die("Pas de nombre de beat renseigné petit malin ;)");
if (($_POST['nbeat']) > 100) die("Nombre de beat limité à 100 max");

$foot=$_POST['foot'];
$current=$_POST['current'];
$nbeat=$_POST['nbeat'];
$galore=$_POST['galore'];


// gestion du nombre de fleches à répétition => pas faire de drills
// si up4 == up2 et up3 == up1 => début d'un drill, donc $wrong sera égale à ce qui devrait etre up0 en cas de drill => donc up4

// désigne les fleches avant current, certains patern feront ressortir des fleches interdites

$arrow_memory = array (
1 => '1',
2 => '2',
3 => '3',
4 => '4',
5 => '5',
6 => '6',
7 => '7',
8 => '8',
9 => '9',
10 => '10' );


$count_wrong_drill = 0;
$count_wrong_repeat = 0;

$wrong='0';

$result='';


$debug = "count_wrong_drill;count_wrong_repeat\n";



for ($i=0; $i<$nbeat; $i++){

	for ($j=1; $j<=$galore; $j++){
	
		$result .= $current."<br>";
		
		for ($g=count($arrow_memory); $g> 1 ; $g--){
			
			$arrow_memory[$g] = $arrow_memory[$g -1];
		}
		
		$arrow_memory[1]=$current;
		
		
		// echo "<pre>";
		// print_r($arrow_memory);
		// echo "</pre>";
		
		// gestion de chart après une séquence de drill annulé, pour éviter d'avoir des drills de 4 fleches trop partout.
		/*
		if (($arrow_memory[3] == $arrow_memory[1]) && ($count_wrong_drill >= 2 )){
			
			$wrong = $arrow_memory[2] ;
			$count_wrong_drill ++;
			if ($count_wrong_drill == 10) $count_wrong_drill = 0;
			
		}
		
		*/
		
		// gestion de chart après une séquence de repeat annulé
		//else 
			if ((($arrow_memory[4] == $arrow_memory[2]) 
					|| ($arrow_memory[6] == $arrow_memory[2]) 
					|| ($arrow_memory[8] == $arrow_memory[2]))
					&& ($count_wrong_repeat >= 1)){
			
			$wrong = $arrow_memory[2] ;
			$count_wrong_repeat ++;
			if ($count_wrong_repeat == 10) $count_wrong_repeat = 0;
		
		}
			
		// début d'un drill
		/*
		else if (( $arrow_memory[4] == $arrow_memory[2] ) && ($arrow_memory[3] == $arrow_memory[1])) {
			
			$wrong = $arrow_memory[2] ;
			$count_wrong_drill ++;
			
		}
		*/
		// début d'une box (4 fleches regroupés, un drill de 4 fleches si on préfère)
		// pas de gestion de count drill vu que l'on ne veut aucune box
		
		else if ($arrow_memory[3] == $arrow_memory[1]) {
			
			$wrong = $arrow_memory[2] ;
			
		}
		
		
		// début d'une même flèche qui se répète plus de 4 fois => pas de 5eme fois
		else if (($arrow_memory[8] == $arrow_memory[6]) &&($arrow_memory[8]== $arrow_memory[4]) && ($arrow_memory[8] == $arrow_memory[2])) {
		
			$wrong = $arrow_memory[2];
			$count_wrong_repeat ++;
		
		}
		
		$debug .= $count_wrong_drill.';'.$count_wrong_repeat."\n";
		
		if ($wrong != '0'){
			
			$current=next_arrow_v2($foot, $current, $wrong);
			
			$wrong = '0';
			
		}
		
		else $current=next_arrow($foot, $current);
		
		// if ($foot=='d') $foot = 'g';
		// else $foot='d';
		$foot = ($foot == 'd') ? 'g' : 'd' ;
		
	}
	
	$result .=  ",<br>";

}

// dernière fleche du rush qui a été calculé à la dernière boucle
$result .= $current."<br>0000<br>0000<br>0000<br>";


?>
<center>
<form action = "extract_step.php" name="form" method ="POST">
<input type="hidden" name="steps" value="<?php echo $result;?>" >
<input type="submit" value="Steps extract in txt">
</form>

<form action = "extract_debug_excel.php" name="form" method ="POST">
<input type="hidden" name="debug" value="<?php echo $debug;?>" >
<input type="hidden" name="steps" value="<?php echo $result;?>" >
<input type="submit" value="Debug extract in xlsx">
</form>
</center>
