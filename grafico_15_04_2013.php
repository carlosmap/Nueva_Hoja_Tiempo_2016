<?php

include ("../jpgraph/jpgraph.php");
include ("../jpgraph/jpgraph_bar.php");

$val_proy=$_GET['val_proy'];
$val_fact=$_GET['val_fact'];
$titulo=$_GET['titulo'];

$datay=array(0,$val_proy);
$datay2=array($val_fact,0);
//$datax=array('V. Proyecto','V. Facturado');


// Create the graph. These two calls are always required
$graph = new Graph(350,220,'auto');
$graph->SetScale("textlin");
//$graph->SetShadow(); 

//$theme_class="DefaultTheme";
//$graph->SetTheme(new $theme_class());

// set major and minor tick positions manually
$graph->yaxis->SetTickPositions(array(0,11,120,10500000));

//$graph->SetBox(false);


//$graph->ygrid->SetColor('gray');
//$graph->ygrid->SetFill(false);

//leyndas en horizontal, para las barras
$graph->xaxis->SetTickLabels(array('V. Proyecto','V. Facturado'));
$graph->xaxis->SetPos("min"); 
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

// Create the bar plots
$b1plot = new BarPlot($datay);
/*
$b1plot->SetWidth(0.6);
$b1plot->SetLegend('V. Proyecto','green');
*/
//$b1plot->SetFillColor("blue");
$b2plot= new BarPlot($datay2);
//$b2plot->SetFillColor("red");
//$b1plot->SetAlign("right"); //alinear grafico
// ...and add it to the graPH
//$graph->Add($b1plot);



//$b1plot->SetColor("red");
//$b2plot->SetColor("blue");
//$BARL=array('#303de1','#c43434');
$b1plot->SetFillGradient('#3e632d','#58a934',GRAD_WIDE_MIDVER);
$b2plot->SetFillGradient('#963d3d',"#cd2323",GRAD_WIDE_MIDVER);

//$totalplot = new AccBarPlot(array($b1plot,$b2plot));
$totalplot = new GroupBarPlot(array($b1plot,$b2plot));
$graph->Add($totalplot);
//$b1plot->SetFillGradient('#c43434',"white",GRAD_MIDVER);

//$b1plot->SetFillColor(array(SetFillGradient('#303de1',"white",GRAD_MIDVER),SetFillGradient('#c43434',"white",GRAD_MIDVER))); ///color de las barras

//$b1plot->setWidth(0,5);
//$b1plot->SetShadow(); //sombra sober las barras (funciona)
//$b1plot->setValuePos('center');
//$b1plot->SetPattern('PATTERN_CROSS2');

//$b1plot->SetWidth(45);
$graph->title->Set($titulo);
//$graph->yscale->ticks->SupressZeroLabel(false); 
// Display the graph
$graph->Stroke();
?>