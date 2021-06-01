<?php 
include ("../jpgraph/jpgraph.php");
include ("../jpgraph/jpgraph_bar.php");


$datay=array($duracion,$m_transcurridos,$ejecutado);


// Create the graph. These two calls are always required
$graph = new Graph(765,169,'auto');
$graph->SetScale("textlin");

$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);

$graph->Set90AndMargin(150,60,40,40);
$graph->img->SetAngle(90); 

// set major and minor tick positions manually
$graph->SetBox(false);

//$graph->ygrid->SetColor('gray');
$graph->ygrid->Show(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels(array('Duración del proyecto','Tiempo transcurrido','Tiempo Ejecutado'));


$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xaxis->SetLabelAlign('right','center');

//$graph->xaxis->SetTitle("Meses",'center');

// For background to be gradient, setfill is needed first.
//$graph->SetBackgroundGradient('#00CED1', '#FFFFFF', GRAD_HOR, BGRAD_PLOT);

// Create the bar plots
$b1plot = new BarPlot($datay);

// ...and add it to the graPH
$graph->Add($b1plot);

$b1plot->SetWeight(0);
$b1plot->SetFillGradient('#3e632d','#58a934',GRAD_WIDE_MIDVER);
$b1plot->SetWidth(17);

//mostrando los valores de cada barra
$b1plot->value->Show();
$b1plot->value->SetFont(FF_ARIAL,FS_NORMAL,8);
$b1plot->value->SetAlign('left','center');
$b1plot->value->SetColor("black","darkred");
$b1plot->value->SetFormat('%.0f Meses');


// Display the graph
$graph->Stroke();
?> 