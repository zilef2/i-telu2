
//? pre-reunion 
//! parametros IA (a medias)
como controlar los siguientes parametros
    +promptTokens: 237
    +completionTokens: 259
    +totalTokens: 496

 // $respuesta = $result['choices'][0]["finishReason"];//le falta el guion bajo
// $respuesta = $result['choices'][0]["index"];
// $respuesta = $result['choices'][0]["logprobs"];
$usageEntrada = $result['usage']["promptTokens"];
$usageRespuesta = $result['usage']["completionTokens"];
$usageTotal = $result['usage']["totalTokens"];


//! roles (se hablo)
me hace falta un rol: y los permisos de cada rol




//? ********************************************** in: reunion (24jun 11am) **********************************************

//# PERMISOS
{
    admin -> crear universidad
    academico puede hacerlo todo (universidad)
    programa -> solo las carreras donde esta asignado
    carrera -> mostrar estudiantes -> solo ver el coordinador
    solo profesor puede crear sus ejercicios 

}
//# VISUALIZACION
{
    AsignaruserUni:: mostrar cedula en vez del correo
    (late) UNIVERSIDAD.index:: universidad -> solo numero de estudiantes

}

// # ARCHIVOS
{
    a cada asignatura hay que subir  un microcurriculum o carta descriptiva: unidads que tiene la materia
}


//# NUEVOS CRUD{
    OBJETIVOS{
        objetivo: enunciado
        (resultado de aprendizaje): indicador de que si se cumplio ese objetivo
        
        ejemplo
        :el estudiante entendera la legislacion en colombia
        :atravez de un ensayo
    }
    PARAMETROS{
        PROMPS
        gpt keys?: que cada quien pueda colocar su GPTKEY para preguntar
    }
}



//# GPT{
    -biblioteca de promps():crud promps

    -?cuando se generen las preguntas : se deben guardar


    -!con las credenciales de GPT: saber cuanto cuesta cada pregunta.
    para reducir los costos de uso de la IA
    {
        reducir los tickets: por promedio del numero de estudiantes que activamente pregunten
    }
    cuando se le gaste: la opcion gratiuta o poner su propia API
}

//? proxima: reunion (sin fecha (tentativa: 30jun))
//! parametrizacion
Que otras cosas deberian parametrizarse