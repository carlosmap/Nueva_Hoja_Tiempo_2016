<?php

include ("../jpgraph/jpgraph.php");
include ("../jpgraph/jpgraph_bar.php");


//////////////////////////GRAFICO BARRAS "TOTALES DIVISIÓN" EN (htPlanProyectos04.PHP)
	function graficos_barra($val_planeado,$val_fact,$titulo,$nom_div)
	{
//		$valoresY=cal_val($val_proy[1],);
//		$valoresY=escala1($valoresY);

/*		$datay=array(0,$val_fact[1]);
		$datay2=array($val_fact[1],0);
*/

		$datay2=array(0,30000000,40000000);

		// Create the graph. These two calls are always required
		$graph = new Graph(995,289,'auto');
		$graph->SetMargin( 150, 20, 30, 100 );
		$graph->SetScale("textlin");
		// set major and minor tick positions manually

//		$graph->yaxis->SetTickPositions(0,20000000,40000000,600000000);
		//$graph->yaxis->SetTickPositions(array(0,11,120,10500000));		
		
		//leyndas en horizontal, para las barras
		$graph->xaxis->SetTickLabels($nom_div);
//		$graph->xaxis->SetFont(FF_FONT1,FS_BOLD,5);
//		$graph->xaxis->SetLabelAngle(50);

//		$graph->xaxis->SetSize(0.3);

		$graph->xaxis->SetPos("min"); 
		$graph->yaxis->HideLine(false);
		$graph->yaxis->HideTicks(false,false);
		
		// Create the bar plots
		$b2plot= new BarPlot($val_planeado);
		$b1plot = new BarPlot($val_fact);
	
		// ...and add it to the graPH
		//$graph->Add($b1plot);
		//$BARL=array('#303de1','#c43434');

		$b1plot->SetFillGradient('#3e632d','#58a934',GRAD_WIDE_MIDVER);
		$b1plot->SetWidth( "20" ); //TAMAÑO DE LAS BARRAS


		$b2plot->SetFillGradient('#963d3d',"#cd2323",GRAD_WIDE_MIDVER);
		$b2plot->SetWidth( "20" ); //TAMAÑO DE LAS BARRAS

		
		//$totalplot = new AccBarPlot(array($b1plot,$b2plot));
		$totalplot = new GroupBarPlot(array($b2plot,$b1plot));
		$graph->Add($totalplot);

		$b1plot->SetFillColor("#cd2323");
		$b1plot->SetLegend( ucwords( "Vl. Planeado"));

		$b2plot->SetFillColor("#58a934");
		$b2plot->SetLegend( ucwords( "Vl. Facturado"));

		$graph->title->Set($titulo);
		// Display the graph
		$graph->Stroke();
//	 	return "<img src='".$graph->Stroke()."' border='0' width='695' height='289' />";
	}

	function desco_url($arrai)
	{
		$tmp = stripslashes($arrai);
		$tmp= urldecode($tmp);
		$datos = unserialize($tmp);
		return $datos;  
	}

	$val_planeado=$_GET['val_planeados'];
	$titulo=$_GET['titulo'];
	$nom_div=desco_url($_GET['nom_div']);
	$val_fact=desco_url($_GET['val_facturado']);
	$val_planeado=desco_url($_GET['val_planeado']);

		//almacenamos los datos del array en otro array, y asi utilizarlo en la actualizacion
// 		$nom_div=array_recibe($nom_div);

	graficos_barra($val_planeado,$val_fact,$titulo,$nom_div);
	

?>