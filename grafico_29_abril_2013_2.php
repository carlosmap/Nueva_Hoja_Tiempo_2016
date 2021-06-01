<?php

include ("../jpgraph/jpgraph.php");
include ("../jpgraph/jpgraph_bar.php");

/*
	//funcion, para calcular los vlaroes del eje y en miles
	function escala1( $my1 ){
		$p = $lim = 0;
		if( $my1 <= 10 ){
			$lim = 10;
			$inc =  1;
		}
		
		if( ( $my1 > 10 ) && ( $my1 <= 100 ) ){
			if( $my1 < 50 ){
				$lim = 50;
				$inc =  10;
			}
			else{
				$lim = 100;
				$inc =  20;
			}
			
		}
		
		if( ( $my1 > 100 ) && ( $my1 <= 1000 ) ){
			if( $my1 < 500 )
				$lim = 500;
			else
				$lim = 1000;
			$inc =  100;
		}
		
		if( ( $my1 > 1000 ) && ( $my1 <= 10000 ) ){
			if( $my1 < 5000 )
				$lim = 5000;
			else
				$lim = 10000;
			$inc = 1000;
		}
		
		if( ( $my1 > 10000 ) && ( $my1 <= 100000 ) ){
			$lim = $my1;
			if( $my1 < 50000 )
				$lim = 50000;
			else
				$lim = 100000;
			$inc = 5000;
		}
		
		if( ( $my1 > 100000 ) && ( $my1 <= 1000000 ) ){
			$lim = $my1;
			if( $my1 < 500000 )
				$lim = 500000;
			else
				$lim = 1000000;
			$inc = 100000;					
		}


		if (( 1000000 <  $my1 )&& ( $my1 <= 10000000 ) )
		{
			$lim = $my1;

			if( $my1 < 5000000)
				$lim = 5000000;
			else
				$lim = 10000000;
			$inc = 1000000;		

		}

		if (( 10000000 <  $my1 )&& ( $my1 <= 100000000 ) )
		{
			$lim = $my1;

			if( $my1 < 50000000)
				$lim = 50000000;
			else
				$lim = 100000000;
			$inc = 10000000;		

		}

		if (( 100000000 <  $my1 )&& ( $my1 <= 1000000000 ) )
		{
			$lim = $my1;

			if( $my1 < 500000000)
				$lim = 500000000;
			else
				$lim = 1000000000;
			$inc = 100000000;		

		}

		if (( 1000000000 <  $my1 )&& ( $my1 <= 10000000000 ) )
		{
			$lim = $my1;

			if( $my1 < 5000000000)
				$lim = 5000000000;
			else
				$lim = 10000000000;
			$inc = 1000000000;		

		}

		if (( 10000000000 <  $my1 )&& ( $my1 <= 100000000000 ) )
		{
			$lim = $my1;

			if( $my1 < 50000000000)
				$lim = 50000000000;
			else
				$lim = 100000000000;
			$inc = 10000000000;		

		}

		if (( 100000000000 <  $my1 )&& ( $my1 <= 1000000000000 ) )
		{
			$lim = $my1;

			if( $my1 < 500000000000)
				$lim = 500000000000;
			else
				$lim = 1000000000000;
			$inc = 100000000000;		

		}

		$c=0;
		for( $i = 0; $i < $lim; $i++ )
		{
//			$p = number_format($p, "", "", "." );
			$esc1[$c] =  $p;
			$p += $inc;
			$i=$esc1[$c];
			$c++;
		}

		return $esc1;	
	}
	//calcula cual es el  mayor valor
	function cal_val($val1,$val2,$val3)
	{
		$val1= (int) $val1;
		$val2=(int) $val2;
		if(($val1<$val2)&&($val3<$val2))
			$val=$val2;

		else if(($val2<$val1)&&($val3<$val1))
			$val=$val1;

		else
			$val=$val3;

		return($val);
	}
*/



//////////////////////////GRAFICO BARRAS "VALORES DEL PROYECTO" EN (htPlanProyectos04.PHP)
	function graficos_barra($val_proy,$val_planea,$val_fact,$titulo)
	{

//		$valoresY=cal_val($val_proy,$val_fact,$val_planea);
//		$valoresY=escala1($valoresY);
		$datay=array($val_proy,0,0);
		$datay2=array(0,$val_planea,0);
		$datay3=array(0,0,$val_fact);

		// Create the graph. These two calls are always required
		$graph = new Graph(995,289,'auto');
		$graph->SetMargin( 150, 30, 30, 30 );
		$graph->SetScale("textlin");
		// set major and minor tick positions manually

//		$graph->yaxis->SetTickPositions($valoresY);

		//$graph->yaxis->SetTickPositions(array(0,11,120,10500000));		
		
		//leyndas en horizontal, para las barras
		$graph->xaxis->SetTickLabels(array('Vl. Proyecto','Vl. Planeado', 'Vl. Facturado'));
		$graph->xaxis->SetPos("min"); 
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		// Create the bar plots


		$b1plot = new BarPlot($datay);
		
		$b2plot= new BarPlot($datay2);
		$b3plot= new BarPlot($datay3);

		// ...and add it to the graPH
		//$graph->Add($b1plot);
		//$BARL=array('#303de1','#c43434');
		$b1plot->SetFillGradient('#3e632d','#58a934',GRAD_WIDE_MIDVER);
		$b2plot->SetFillGradient('#963d3d',"#cd2323",GRAD_WIDE_MIDVER);
		$b3plot->SetFillGradient('#435982',"#336cd5",GRAD_WIDE_MIDVER);
		
		//$totalplot = new AccBarPlot(array($b1plot,$b2plot));



		$totalplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));
//$totalplot->SetWidth(0.6);
		$graph->Add($totalplot);

		$graph->title->Set($titulo);
		// Display the graph
		$graph->Stroke();
//	 	return "<img src='".$graph->Stroke()."' border='0' width='695' height='289' />";
	}


	$val_proy=$_GET['val_proy'];
	$val_fact=$_GET['val_fact'];
	$val_planea=$_GET['val_planea'];

	$titulo=$_GET['titulo'];
	
//	$valoresY=cal_val($val_proy,$val_fact);
//	$valoresY=escala1($valoresY);

	graficos_barra($val_proy,$val_planea,$val_fact,$titulo);
	

?>