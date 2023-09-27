import{k as j,v as C,A as N,m as P,f as s,b as e,d as m,t as n,F as h,x as f,a as b,w as g,u as d,n as _,g as y,aZ as Q,o,h as v}from"./app-fa012e40.js";const G={class:"space-y-4"},H={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},O={class:"container px-5 py-2 mx-auto"},R={class:"flex flex-col text-center w-full mb-1"},z={class:"block mx-auto text-lg text-gray-900 dark:text-white"},D={class:"text-sky-600 dark:text-white"},I={class:"text-sky-500"},A={class:"text-gray-600 body-font"},B={class:"container px-5 py-8 mx-auto"},V={class:"flex flex-wrap -m-4"},$={class:"h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden"},q={class:"px-4 py-1 my-2"},F={class:"tracking-widest text-md title-font font-medium text-gray-400 mb-1 pb-1"},L={key:0,class:"mt-4"},M={class:"text-center dark:text-white text-gray-800 ml-2 hover:text-sky-300"},U=["onClick"],J={key:1,class:"flex flex-col"},Z=e("div",{class:"flex items-center"},[e("p",{class:"text-gray-300 text-sm"},"¡Sin subtopicos!")],-1),K=[Z],W={class:"text-center mt-8"},X={class:"p-2 w-full"},Y={class:"flex mx-auto text-sky-800 bg-indigo-200 border-0 py-1 px-8 focus:outline-none rounded text-lg"},ee={key:0},te={key:0},se={key:0,class:"body-font relative"},oe={class:"container px-5 pt-6 mx-auto"},ne={class:"flex flex-col text-center w-full mt-4"},ie={class:"sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-white"},le={class:"lg:w-2/3 mx-auto leading-relaxed text-xl text-gray-600 dark:text-white"},ae={class:"lg:w-2/3 mx-auto leading-relaxed text-base text-gray-500 dark:text-white my-4"},re={class:"w-full flex mt-6 mx-auto"},ce={class:"grow ..."},de={class:"text-justify font-sans"},ue=e("p",{class:"mb-4 mt-12 text-center text-lg font-sans"}," ¡Recuerde que esto es un mensaje generado por Inteligencia artificial, Por favor verifique que los resultados sean consistentes! ",-1),me={key:0,class:"w-full flex mt-6 mx-auto"},xe={class:"w-full mt-1 mx-auto"},be={class:"w-full flex items-center justify-center dark:bg-gray-800 bg-gray-100"},_e={class:"w-full mx-auto py-6"},pe=e("h1",{class:"text-xl text-center font-bold mb-6"}," Aprender más ",-1),he={class:"bg-white dark:bg-gray-600 px-6 py-4 my-3 w-3/4 mx-auto shadow rounded-md flex items-center"},fe={class:"w-full text-center mx-auto"},ge={key:1,class:"w-full flex item-center mt-10"},ye={class:"text-red-400 mx-auto text-xl"},ve={key:1,class:"body-font relative"},we={class:"container px-5 pt-6 mx-auto"},ke={class:"flex flex-col text-center w-full mt-4"},Se={class:"text-2xl font-medium title-font mb-4 text-red-600"},je={__name:"versionEstudiante",props:{fromController:Object,filters:Object,respuesta:String,temaSelec:String,subtopicoSelec:Object,usuario:Object,materia:Object,numberPermissions:Number,restarAlToken:Number,limite:Number,respuestaEQH:String,laRespuesta:String,notvalidbyteacher:Boolean},emits:["EstudianteToGPT","formNext"],setup(u,{emit:w}){const t=u,c=j({params:{actionEQH:0,laRespuesta:""},nivel:1,pregunta:"",respuestagpt:"",nivelSelect:""}),k=r=>{w("EstudianteToGPT",r)},i=C({pregunta:"",respuestagpt:t.respuesta,temaSelec:"",subtopicoSelec:"",respuesta1:"",actionEQH:0,materiaid:t.materia.id}),x=r=>{i.actionEQH=r,i.post(route("materia.actionEQH"),{preserveScroll:!0,onSuccess:()=>null,onError:()=>alert(JSON.stringify(i.errors,null,4)),onFinish:()=>null})};return N(()=>{}),P(()=>{i.temaSelec=t.temaSelec,i.subtopicoSelec=t.subtopicoSelec,i.respuesta1=t.respuesta}),(r,a)=>{const S=Q("zonalecciones");return o(),s("div",null,[e("div",null,[e("div",G,[e("div",H,[e("div",O,[e("div",R,[e("h3",z,[m("Materia: "),e("b",null,n(t.materia.nombre),1)]),e("p",D,[e("b",I,n(t.limite),1),m(" Token disponibles ")])])]),e("section",A,[e("div",B,[e("div",V,[(o(!0),s(h,null,f(u.fromController,(l,E)=>(o(),s("div",{key:E,class:_(["p-4 w-full md:w-1/2 xl:1/3",l.id==c.temaIDSelected?"bg-blue-100 dark:bg-gray-200":""])},[e("div",$,[e("div",q,[e("h2",F,n(l.nombre),1),l.sub.length?(o(),s("div",L,[(o(!0),s(h,null,f(l.sub,(p,T)=>(o(),s("div",{key:T,class:"grid grid-cols-2 gap-2 items-center"},[e("p",M,n(p.nombre),1),e("button",{onClick:Ee=>k(p.id),class:"mx-auto text-white bg-indigo-500 border-0 px-0.5 my-3 focus:outline-none hover:bg-indigo-900 rounded text-lg"},n(d(i).processing?"...":"Comenzar Lección"),9,U)]))),128))])):(o(),s("div",J,K))])])],2))),128))]),e("div",W,[b(d(v),{href:r.route("materia.index"),class:"text-center my-4 border border-sky-700 bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline"},{default:g(()=>[m(" Regresar ")]),_:1},8,["href"])])])]),b(S,{temaIDSelected:c.temaIDSelected,ejercicio:c.ejercicio,subtopSelected:c.subtopSelected,temaSelectedName:c.temaSelectedName,Unidad:c.temaReal,nivelSelect:t.nivelSelect,onSubmitGPT:a[0]||(a[0]=l=>r.submitToGPT(l))},null,8,["temaIDSelected","ejercicio","subtopSelected","temaSelectedName","Unidad","nivelSelect"])])]),e("div",X,[e("button",Y,n(d(i).processing?"Por favor, espere...":""),1)]),t.limite>0?(o(),s("div",ee,[t.respuesta!=""?(o(),s("div",te,[t.limite>-1&&!d(i).processing?(o(),s("section",se,[e("div",oe,[e("div",ne,[e("h1",ie,n(u.temaSelec.nombre),1),e("p",le,n(u.subtopicoSelec.nombre),1),e("p",ae," Tokens consumidos: "+n(t.restarAlToken),1)]),e("div",re,[e("div",{class:_(["flex-none w-14 ...",{"w-44":t.respuesta.length>500}])},null,2),e("div",ce,[e("p",de,n(t.respuesta),1),ue]),e("div",{class:_(["flex-none w-14 ...",{"w-44":t.respuesta.length>500}])},null,2)]),t.notvalidbyteacher?(o(),s("div",me,[e("div",xe,[e("div",be,[e("div",_e,[pe,e("div",he,[e("div",fe,[e("button",{type:"button",onClick:a[1]||(a[1]=l=>x(4)),class:"border border-sky-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-blue-300 focus:outline-none focus:shadow-outline"}," Simplificar "),e("button",{type:"button",onClick:a[2]||(a[2]=l=>x(1)),class:"border border-green-500 bg-green-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-green-600 focus:outline-none focus:shadow-outline"}," Ejemplos "),e("button",{type:"button",onClick:a[3]||(a[3]=l=>x(2)),class:"border border-yellow-500 bg-yellow-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-yellow-600 focus:outline-none focus:shadow-outline"}," Quiz "),b(d(v),{href:r.route("materia.index"),class:"my-4 border text-left border-sky-700 bg-black text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-sky-600 focus:outline-none focus:shadow-outline"},{default:g(()=>[m(" Regresar ")]),_:1},8,["href"])])])])])])])):y("",!0)])])):y("",!0)])):(o(),s("div",ge,[e("p",ye,n(t.respuesta),1)]))])):(o(),s("div",ve,[e("div",we,[e("div",ke,[e("h1",Se,n(t.respuesta),1)])])]))])])}}};export{je as default};
