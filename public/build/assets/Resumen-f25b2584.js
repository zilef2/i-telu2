import{_ as m}from"./AuthenticatedLayout-d2a8c51d.js";import{r as u,v as b,o as f,w as h,c as l,b as t,u as r,f as i,F as _,k as c,X as x,d as e,t as d,n}from"./app-8190fc74.js";import{_ as g}from"./Breadcrumb-31fbe063.js";import{_ as p}from"./BackButton-2206ebc1.js";import"./index-d56bdc70.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./index-c7a22da3.js";import"./Toast-095c7b20.js";const v={class:"text-gray-600 body-font"},y={class:"container px-5 my-8 mx-auto border border-b-2 border-x-0 border-black"},R={class:"text-center"},w={class:"title-font sm:text-xl text-lg my-5 font-medium text-gray-900"},C={class:"flex flex-wrap -m-4"},k={class:"p-4 w-full text-justify"},j={class:"h-full bg-white bg-opacity-75 px-8 pt-16 pb-24 rounded-lg overflow-hidden text-center relative"},F={key:0,class:"leading-relaxed mx-auto max-w-2xl text-lg text-justify mb-3 font-medium"},N={key:1,class:"leading-relaxed mx-auto text-red-400 text-lg text-justify mb-3 font-bold"},D={class:"grid grid-cols-2"},P=e("a",{class:"text-indigo-500 inline-flex items-center"},"¿Cuál es el tema central o la principal conclusión del documento que se describe en el PDF? ",-1),O=e("a",{class:"text-indigo-500 inline-flex items-center"},"¿Cuáles son los puntos clave o los argumentos principales presentados en el PDF? ",-1),B=e("a",{class:"text-indigo-500 inline-flex items-center"},"¿Quiénes son los autores y cuál es su credibilidad en relación con el tema del PDF? ",-1),$=e("a",{class:"text-indigo-500 inline-flex items-center"},"¿Cuál es el contexto o la relevancia del contenido del PDF en el campo o la industria a la que pertenece? ",-1),z={__name:"Resumen",props:{breadcrumbs:Object,title:String,numberPermissions:Number,ChatResumen:Object,archivinid:Number,materia_id:Number,archivo:Object},setup(s){const a=s;return u({tamanin:"",TamanoMAX:0,sumOfpesos:0}),b({archivo:null,nombre:"",peso:0,type:"",user_id:"",materia_id:""}),f(()=>{}),h(()=>{}),(o,q)=>(c(),l(_,null,[t(r(x),{title:a.title},null,8,["title"]),t(m,null,{default:i(()=>[t(g,{title:s.title,breadcrumbs:s.breadcrumbs},null,8,["title","breadcrumbs"]),e("section",v,[e("div",y,[e("div",R,[e("h1",w,d(s.archivo.nombre),1)]),e("div",C,[e("div",k,[e("div",j,[s.ChatResumen[2]!=="NotTokens"?(c(),l("p",F,d(s.ChatResumen[1]),1)):(c(),l("p",N,d(s.ChatResumen[1]),1))])])]),e("div",D,[t(r(n),{href:o.route("generarResumen",[a.archivo.id,1]),class:"mt-4 mb-1 border-2 border-b-blue-300"},{default:i(()=>[P]),_:1},8,["href"]),t(r(n),{href:o.route("generarResumen",[a.archivo.id,2]),class:"mt-4 mb-1 border-2 border-b-blue-300"},{default:i(()=>[O]),_:1},8,["href"]),t(r(n),{href:o.route("generarResumen",[a.archivo.id,3]),class:"my-1 border-2 border-b-blue-300"},{default:i(()=>[B]),_:1},8,["href"]),t(r(n),{href:o.route("generarResumen",[a.archivo.id,4]),class:"my-1 border-2 border-b-blue-300"},{default:i(()=>[$]),_:1},8,["href"])]),t(p,{ruta:"materia.Archivos",id1:a.materia_id,class:"my-12 text-center"},null,8,["ruta","id1"])])])]),_:1})],64))}};export{z as default};