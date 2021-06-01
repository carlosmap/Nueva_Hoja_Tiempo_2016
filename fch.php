<?php 

function getTime($tipo) 
{ 
    $fecha = getdate(); 
     
    $dia = $fecha[mday]; 
    $mes = $fecha[mon]; 
    $ano = $fecha[year]; 
     
    $hora = $fecha[hours] - 7; // HORAS A RESTAR DEL SERVER 
    $minuto = $fecha[minutes]; 
    $segundo = $fecha[seconds]; 
    $parte_del_dia = "am"; 
     

    if($hora < 0) 
    { 
        $hora = 24 + $hora; 
        $parte_del_dia = "pm"; 
        $dia = $dia - 1; 
         
        if($dia == 0) 
        { 
            if($mes == 5 || $mes == 7 || $mes == 10 || $mes == 12) 
            { 
                $dia = 30; 
                $mes = $mes - 1; 
            } 
            else if($mes == 3) 
            { 
                if($ano % 4 == 0) 
                { 
                    $dia = 27; 
                } 
                else 
                { 
                    $dia = 28; 
                } 
                $mes = $mes - 1; 
            } 
            else 
            { 
                $dia = 31; 
                $mes = $mes - 1; 
            } 
        } 
         
        if($mes == 0) 
        { 
            $mes = 12; 
            $ano = $ano - 1; 
        } 
    } 
     
    if($hora > 12) 
    { 
        $parte_del_dia = "pm"; 
        $hora = $hora - 12; 
    } 

//Día - Hora     
if ($tipo == 1)    return $dia."/".$mes."/".$ano." ".$hora.":".$minuto.":".$segundo." ".$parte_del_dia; 
//Hora - Fecha 
if ($tipo == 2)    return $hora.":".$minuto.":".$segundo." ".$parte_del_dia.". ".$dia."/".$mes."/".$ano; 
//Solo fecha 
if ($tipo == 3) return $dia."/".$mes."/".$ano; 
//Solo hora 
if ($tipo == 4) return $hora.":".$minuto.":".$segundo." ".$parte_del_dia; 


} 

?>