<?php // content="text/plain; charset=utf-8"
// Gantt example  ganttmonthyearex2.php

include ("../jpgraph/jpgraph.php");
include ("../jpgraph/jpgraph_gantt.php");

$act_array=$_GET['act_array'];
$filas=$_GET['F'];

$tmp = stripslashes($act_array);
$tmp= urldecode($tmp);
$act_array = unserialize($tmp);

$array_barras = array();

 
$graph = new GanttGraph(765,119,'auto');

//DEFINICIÓN DEL TITULO
$graph->title->Set($title);
 
// Setup some "very" nonstandard colors

$graph->SetMarginColor('white@0.8');
//COLOR DEL BORDE INTERNO
$graph->SetBox(true,'black:0.6',2);
//COLOR DEL BORDE EXTERNO
$graph->SetFrame(FALSE,'darkgreen',4);
$graph->scale->divider->SetColor('yellow:0.6');
$graph->scale->dividerh->SetColor('yellow:0.6');
 
// Explicitely set the date range 
// (Autoscaling will of course also work)

//RANGO DE MESES A MOSTRAR EN LA GRAFICAS
$graph->SetDateRange($f_i,$f_f);
 
// Display month and year scale with the gridlines
$graph->ShowHeaders(GANTT_HMONTH | GANTT_HYEAR);
$graph->scale->month->grid->SetColor('gray');
$graph->scale->month->grid->Show(true);
$graph->scale->year->grid->SetColor('gray');
$graph->scale->year->grid->Show(true);

for($f=0;$f<$filas;$f++)
{
	$activity1 = new GanttBar(0,"",$act_array[$f][0],$act_array[$f][1],"");

	// Yellow diagonal line pattern on a red background
	//COLOR DE LAS BARRAS
	$activity1->SetPattern(GANTT_SOLID,"#".$color,55);
	// Set absolute height of activity
	$activity1->SetHeight(16);
	$graph->Add($activity1);

}
/*
$activity1 = new GanttBar(0,"Escala de actividades","2001-12-21","2001-12-26","");
 
// Yellow diagonal line pattern on a red background
//COLOR DE LAS BARRAS
$activity1->SetPattern(GANTT_SOLID,"#58a934",55);
// Set absolute height of activity
$activity1->SetHeight(16);


$activity3 = new GanttBar(0,"","2002-02-10","2002-09-20","");
$activity3->SetPattern(GANTT_SOLID,"#58a934",55); 
// Set absolute height of activity
$activity3->SetHeight(16);
 
// Format the bar for the second activity
// ($row,$title,$startdate,$enddate)
$activity2 = new GanttBar(0,"","2001-12-31","2002-1-2","");
 
// ADjust font for caption
$activity2->SetPattern(GANTT_SOLID,"#58a934",55);
 
// Set absolute height of activity
$activity2->SetHeight(16);

 
// Finally add the bar to the graph
$graph->Add($activity1);
$graph->Add($activity2);
$graph->Add($activity3);
*/
$graph->Stroke();
 
?>