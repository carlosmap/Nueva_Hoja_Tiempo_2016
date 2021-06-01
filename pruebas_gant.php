
<?php
session_start();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

    function crea_fecha($vigencia,$mes)
    {
        //CONSULTAMOS EL ULTIMO DIA DEL MES DE LA VIGENCIA FINAL
        $cur_dia_mes=mssql_query("select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$vigencia."' + '".$mes."' + '01')))) AS ultimo_dia");
        if($datos_dia_mes=mssql_fetch_array($cur_dia_mes))
        {
            $ultimo_dia=$datos_dia_mes["ultimo_dia"];
        }
        return($ultimo_dia);
    
    }
    //$meses= array(1,2,3,4,5,6,7,8,9,10,11,12); //meses de un mes
    //$meses2= array(2,3,4,5,7,8,9,10,11);
    $f=0; $c=0; //define los valores iniciales de las filas y las columnas
    
    //$activid__inicio_fin= [][] array();
    //$vigencias= array(2013,2014);  ///select vigencias
    //foreach($vigencias as $val )  //while vigencias
    $cur_vigencia=mssql_query("select distinct vigencia from PlaneacionProyectos where id_proyecto=1547 order by vigencia");
    while($datos_vigencia=mssql_fetch_array($cur_vigencia))
    {
        //echo $val."<br>";   //select meses de la vigencia
        //foreach($meses2 as  $mex)    //while meses de la vigencia
        $cur_mes=mssql_query("select distinct mes from PlaneacionProyectos where id_proyecto=1547 and vigencia=".$datos_vigencia["vigencia"]." order by  mes");
        $can_reg=mssql_num_rows($cur_mes); //almacenamos la cantidad de registros extrahidos
        $id_sql=0;
        while($datos_mes=mssql_fetch_array($cur_mes))
        {
            $id_sql++; //permite saber la psocicion del identificacor interno de la consulta
            //$ind=$mex-1;  //asigna el valor del mes 
            //echo $meses[$ind]."<br>";
            //$fecha=$mex.' - '.$vigencias;
            
            $activid__inicio_fin[$f][$c]= $datos_vigencia["vigencia"]."-".$datos_mes["mes"]."-1"; //almacena la primera fecha del mes
            
            $mes_inicio=$datos_mes["mes"];  //alamcena el valor, para comparalo mas adelante, y encontrar el mes hasta donde va la planeacion
            //echo $activid__inicio_fin[$f][$c]." --<br>";

            while($datos_mes2=mssql_fetch_array($cur_mes)) //se ejecuta la consulta, para buscar hasta que mes exite planeación
            {
                $id_sql++; //permite saber la psocicion del identificacor interno de la consulta
                $mes_inicio++;
//echo $mes_inicio." */* <br>";
//echo "<br>  Ingresa *** ".$datos_mes2["mes"]." **  ".$mes_inicio."--- ".mssql_num_rows($cur_mes)." <br>";
                if($datos_mes2["mes"]!=$mes_inicio) //si el siguiente mes es diferente a la secuencia de meses, SE ALMACENA LA FECHA COMO FECHA FINAL
                {
                    echo "<br>  Ingresa *** ".$datos_mes2["mes"]."<br>";
                    $c++;
                    $ultimo_dia=crea_fecha($datos_vigencia["vigencia"],$datos_mes2["mes"]);
                    $activid__inicio_fin[$f][$c]=$datos_vigencia["vigencia"]."-".$datos_mes2["mes"]."-".$ultimo_dia; //se almacena el mes, hasta donde exista planeacion
                    break;    
                }
                //SI VALIDA SI ES EL ULTIMO REGISTRO DE LA CONSULTA, PARA PONER ESTE VALOR, COMO LA FECHA FINAL
                if($id_sql==$can_reg)
                {
                    $c++;
                    echo " ---- ".$datos_mes2["mes"]."<br>";
                    $ultimo_dia=crea_fecha($datos_vigencia["vigencia"],$datos_mes2["mes"]);
                   $activid__inicio_fin[$f][$c]=$datos_vigencia["vigencia"]."-".$datos_mes2["mes"]."-".$ultimo_dia; //se almacena el mes, hasta donde exista planeacion
                }
                
            }            
            echo "<br><br>".$activid__inicio_fin[$f][0]." ** ".$activid__inicio_fin[$f][1]."<br>";
            $f++; //aumenta la fila, para almacenar la siguiente fecha en la matriz
            $c=0;
           //
        }
    }
    		$tmp=serialize($activid__inicio_fin);  //Serializar el arreglo.
		$url=urlencode($tmp);  //Codificar URL. 


?>
<html>
    <head>
        <title></title>
    </head>
    <body>
        <img src='gant_chars5.php?act_array=<?=$url ?>&F=<?=$f ?>&f_i=<?=$activid__inicio_fin[0][0] ?>&f_f=<?=$activid__inicio_fin[$f-1][1] ?>'>
    </body>
</html>

