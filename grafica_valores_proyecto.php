<?php 
require_once ("../jpgraph/jpgraph.php");
require_once ("../jpgraph/jpgraph_bar.php");


$datay=array($val_proy,'','');
$datay2=array('',$val_planea,'');
$datay3=array('','',$val_fact);


//$datay=array( number_format(1111040368, "2", ",", "." ),'','');
//$datay2=array('',number_format(110080000, "2", ",", "." ),'');
//$datay3=array('','',number_format(104036288, "2", ",", "." ));


// Create the graph. These two calls are always required
//ancho , alto
$graph = new Graph(274,97,'auto');
$graph->SetScale("textlin");

//MARGEN IZQUIERDA DERECHA , SUPERIOR INFERIOR
//TAMBIEN PERMITE POSICIONAR EL GRAFICO
//$graph->Set90AndMargin('-100','','100','min');//('min','min',130,5);
//$graph->SetScale('linlin',10,15,20,35); 
$graph->SetMargin(10,20,10,15);
//$graph->SetMargin(10,20,10,15);
//$graph->SetMargin(10,40,250,50);
$graph->xaxis->SetTickLabels(array('','',''));
/*
$graph->img->SetAngle(90); 
/*
$graph->xaxis->SetTickLabels(array('Valor del proyecto','Valor planeado','Valor facturado'));
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,14);
*/
//ANGULO DE LOS VALORES DEL EJE X SUPERIOR
$graph->yaxis->SetLabelAngle(60);

//$graph->yaxis->SetPos(-30);
//$graph->xaxis->SetPos('min');
//$graph->xaxis->SetLabelAlign('left','center');

//$graph->yaxis->SetLabelAlign('left','center');

// Create the bar plots
$b1plot = new BarPlot($datay);
$b2plot = new BarPlot($datay2);
$b3plot = new BarPlot($datay3);

// ...and add it to the graPH

$graph->Add($b1plot);
$graph->Add($b2plot);
$graph->Add($b3plot);

//COLOR DE LAS BARRAS
//$b1plot->SetWeight(0);
$b1plot->SetFillGradient('#3e632d','#58a934',GRAD_WIDE_MIDVER);
//Grozor de las barras
$b1plot->SetWidth(17);

//$b2plot->SetWeight(0);
$b2plot->SetFillGradient('#963d3d',"#cd2323",GRAD_WIDE_MIDVER);
//Grozor de las barras
$b2plot->SetWidth(17);

//$b3plot->SetFillGradient('#000000','#58a934',GRAD_WIDE_MIDVER);
$b3plot->SetFillGradient('#435982',"#336cd5",GRAD_WIDE_MIDVER);
//Grozor de las barras
$b3plot->SetWidth(17);

//mostrando los valores de cada barra
/*
$b1plot->value->Show();
$b1plot->value->SetFont(FF_ARIAL,FS_NORMAL,12);
$b1plot->value->SetAlign('left','center');
$b1plot->value->SetColor("black","darkred");
$b1plot->value->SetFormat('$%.0f ');

$b2plot->value->Show();
$b2plot->value->SetFont(FF_ARIAL,FS_NORMAL,12);
$b2plot->value->SetAlign('left','center');
$b2plot->value->SetColor("black","black");
$b2plot->value->SetFormat('$%d ');

//$b2plot->value->SetFormatCallback(1000);


$b3plot->value->Show();
$b3plot->value->SetFont(FF_ARIAL,FS_NORMAL,12);
$b3plot->value->SetAlign('left','center');
$b3plot->value->SetColor("black","black");
$b3plot->value->SetFormat('$%.0f ');
*/
//// COLOR Y TEXTO DE LOS CUADROS, QUE REPRESENTAN LAS BARRAS
$b1plot->SetColor("#3e632d");
$b1plot->SetFillColor("#58a934");
$b1plot->SetLegend("Total");

$b2plot->SetColor("#ea1e19");
$b2plot->SetFillColor("#ea1e19");
$b2plot->SetLegend("Planeado");

//		$b3plot->SetFillGradient('#435982',"#336cd5",GRAD_WIDE_MIDVER);
$b3plot->SetColor("#435982");
$b3plot->SetFillColor("#336cd5");
$b3plot->SetLegend("Facturado");



//$b2plot->SetFont(FF_ARIAL, FS_BOLD, 14);

/*
		$b1plot->SetFillColor("#cd2323");
		$b1plot->SetLegend( ucwords( "Vl. Planeado"));

		$b2plot->SetFillColor("#58a934");
		$b2plot->SetLegend( ucwords( "Vl. Facturado"));
*/
//CUADRO EXTERNOQUE RODEA, LOS CUADROS
$graph->legend->SetFrameWeight(1);
$graph->legend->SetColumns(3);
$graph->legend->SetColor('#4E4E4E','white');
$graph->legend->SetFont(FF_ARIAL,FS_NORMAL,9);


//POSICION DEL CUADRO (HORIZONTAL)
$graph->legend->SetAbsPos(15,77,'right','top');
//$graph->SetAbsPos(220,240,'right','top');

/*
$band = new PlotBand(VERTICAL,BAND_RDIAG,11,"max",'khaki4');
$band->ShowFrame(true);
$band->SetOrder(DEPTH_BACK);
$graph->Add($band);
*/
//TITULO
//$graph->title->Set("Valores del proyecto");




// Display the graph
$graph->Stroke();
?> 