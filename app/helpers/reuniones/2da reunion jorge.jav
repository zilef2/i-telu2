
// pre reunion
//! parametros IA
como controlar los siguientes parametros
    +promptTokens: 237
    +completionTokens: 259
    +totalTokens: 496

 // $respuesta = $result['choices'][0]["finishReason"];
// $respuesta = $result['choices'][0]["index"];
// $respuesta = $result['choices'][0]["logprobs"];
$usageEntrada = $result['usage']["promptTokens"];
$usageRespuesta = $result['usage']["completionTokens"];
$usageTotal = $result['usage']["totalTokens"];


//! roles 
me hace falta un rol: y los permisos de cada rol

//! que otros parametros?

// in: reunion
// post: reunion