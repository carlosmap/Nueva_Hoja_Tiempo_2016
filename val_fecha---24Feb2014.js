/*
var nav4 = window.Event ? true : false;
function acceptNum(evt){   
var key = nav4 ? evt.which : evt.keyCode;   
return (key <= 13 || (key>= 48 && key <= 57) ||  (key ==47) );
}
//*/
function compare_fecha(fecha, fecha2)  
  {  
		var xMonth=fecha.substring(0, 2);  
		var xDay=fecha.substring(3, 5);  
		var xYear=fecha.substring(6,10);  
		var yMonth=fecha2.substring(0, 2);  
		var yDay=fecha2.substring(3, 5);  
		var yYear=fecha2.substring(6,10);  
	//si el año de la fecha ingresada es menor a la fecha actual
    if (xYear>yYear)  
    {  
        return(true)  
    }  
    else  
    {  
      if (xYear == yYear)  
      {  
		//si el mes de la fecha ingresada es menor  a la fecha actual
        if (xMonth> yMonth)  
        {  
            return(true)  
        }  
        else  
        {   
			//si el mes ingresado y el actual son iguales			
          if (xMonth == yMonth)  
          {  
			//si el dia de la fecha ingresada es menor a la de la fecha actual
            if (xDay> yDay)  
              return(true);  
			else if (xDay==yDay)  
              return(true);  
            else  
              return(false);  
          }  
		  
          else  
            return(false);  
        }  
      }
	  //si el año de la fecha ingresada es mayor a la actual	  
      else  
        return(false);  
    }  
} 


function vigenciaMes(fecha, fecha2)
{
	var xMonth=fecha.substring(0, 2);  
	var xYear=fecha.substring(6,10);  

	var yMonth=fecha2.substring(0, 2);  
	var yYear=fecha2.substring(6,10);  

	if(yYear!=xYear)
	{  
		return(false); 
	}  
	else if(yYear==xYear)
	{  
		if(xMonth!=yMonth)
		{  
			return(false); 
		}  
		else if(xMonth==yMonth)
		{  
			return(true); 
		}  
	}  
}