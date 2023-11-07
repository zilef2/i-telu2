import{r as v,v as h,w as y,o as w,c as d,b as c,u as o,f as k,F as T,k as u,X as S,d as e,l as O,t as g,e as P,m as a,bt as j,p as m,y as A,bH as N,bF as U,O as V,x as B}from"./app-8190fc74.js";import{_ as E}from"./AuthenticatedLayout-d2a8c51d.js";import{_ as M}from"./Breadcrumb-31fbe063.js";import"./index-d56bdc70.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./index-c7a22da3.js";import"./Toast-095c7b20.js";const D={class:""},F=e("div",{class:"p-2 w-full"},null,-1),z={class:"text-gray-600 body-font relative"},C={class:"container px-5 py-2 mx-auto"},R={class:"flex flex-col text-center w-full mb-8"},$={class:"sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"},H={class:"w-full mx-auto"},I=["onSubmit"],J={class:"flex flex-wrap -m-2"},X={class:"p-2 w-1/2"},q={class:"relative"},G=e("label",{for:"name",class:"leading-7 text-sm text-gray-600"},"Objetivo",-1),K={key:0,class:"w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"},L={key:1,class:"w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-800 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"},Q={class:"p-2 w-1/4"},W=e("label",{for:"price",class:"block text-sm font-medium leading-6 text-gray-900"},"Token restantes ",-1),Y={class:"relative mt-2 rounded-md shadow-sm"},Z=["value"],ee={class:"p-2 w-1/4"},te=e("label",{for:"price",class:"block text-sm font-medium leading-6 text-gray-900"},"Token restantes ",-1),se={class:"relative mt-2 rounded-md shadow-sm"},oe=e("option",{value:"Bachiller"},"Bachiller",-1),ie=e("option",{selected:"",value:"Universitario"},"Universitario",-1),re=e("option",{value:"Maestria"},"Maestria",-1),ae=e("option",{value:"Doctor"},"Doctor",-1),ne=[oe,ie,re,ae],le={class:"p-2 w-full"},de={class:"relative"},ce=e("label",{for:"pregunta",class:"leading-7 text-sm text-gray-600"},"Pregunta",-1),ue={class:"p-2 w-full h-full"},ge={class:"relative h-full"},me=e("label",{for:"message",class:"leading-7 text-sm text-gray-600"},"Respuesta ",-1),pe={class:"p-2 w-full"},_e={class:"relative"},fe=e("label",{for:"restarAlToken",class:"leading-7 text-sm text-gray-600"},"Esta pregunta consumio",-1),be={class:"p-2 w-full"},Pe={__name:"masPreguntas",props:{nuevaPregunta:String,respuesta:String,filters:Object,materia:Object,title:String,restarAlToken:Number,tresEjercicios:Object,breadcrumbs:Object,limite:Number,nivel:String,consumio:Number},setup(l){var _,f;const t=l,{_:xe,debounce:ve,pickBy:x}=B,n=v({params:{materiaid:(_=t.materia)==null?void 0:_.id,pregunta:"",nivel:"",restarAlToken:t.restarAlToken}}),s=h({materia:t.materia,pregunta:t.nuevaPregunta,restarAlToken:((f=n.params)==null?void 0:f.restarAlToken)+" Tokens",nivel:t.nivel?t.nivel:"Universitario",respuestagpt:t.respuesta});y(()=>{console.log("🧈 form ",s.restarAlToken)}),w(()=>{});const p=()=>{n.params.pregunta=N(U(s.pregunta)),n.params.nivel=s.nivel;let b=x(n.params);V.get(route("materia.masPreguntas",b,{onSuccess:()=>null,onError:()=>alert(JSON.stringify(s.errors,null,4)),onFinish:()=>null}))};return(b,i)=>(u(),d(T,null,[c(o(S),{title:t.title},null,8,["title"]),c(E,null,{default:k(()=>[c(M,{title:l.title,breadcrumbs:l.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",D,[F,e("section",z,[e("div",C,[e("div",R,[e("h1",$,[O("Preguntas generales de "),e("b",null,g(t.materia.nombre),1)])]),e("div",H,[e("form",{onSubmit:P(p,["prevent"])},[e("div",J,[e("div",X,[e("div",q,[G,t.materia.TodosObjetivos!=""?(u(),d("p",K,g(t.materia.TodosObjetivos),1)):(u(),d("p",L," Sin objetivos "))])]),e("div",Q,[W,e("div",Y,[e("input",{disabled:"",type:"text",name:"price",id:"price",value:t.limite,class:"block w-full rounded-md border-0 pl-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},null,8,Z)])]),e("div",ee,[te,e("div",se,[a(e("select",{id:"currency",name:"currency","onUpdate:modelValue":i[0]||(i[0]=r=>o(s).nivel=r),class:"block w-full rounded-md border-0 pl-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"},ne,512),[[j,o(s).nivel]])])]),e("div",le,[e("div",de,[ce,a(e("input",{type:"text",name:"price",id:"price","onUpdate:modelValue":i[1]||(i[1]=r=>o(s).pregunta=r),class:"block w-full rounded-md border-0 py-1.5 pl-2 pr-20 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6",placeholder:"Pregunte con fines academicos"},null,512),[[m,o(s).pregunta]])])]),a(e("div",ue,[e("div",ge,[me,a(e("textarea",{disabled:"","onUpdate:modelValue":i[2]||(i[2]=r=>o(s).respuestagpt=r),id:"message",name:"message",rows:"6",cols:"35",class:"h-auto resize-none w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-6 transition-colors duration-200 ease-in-out"},null,512),[[m,o(s).respuestagpt]])])],512),[[A,t.respuestagpt!=""]]),e("div",pe,[e("div",_e,[fe,a(e("input",{type:"text",name:"price",id:"price","onUpdate:modelValue":i[3]||(i[3]=r=>o(s).restarAlToken=r),class:"block w-full rounded-md border-0 py-1.5 pl-2 pr-20 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6",placeholder:"Normalmente se consume 1 token"},null,512),[[m,o(s).restarAlToken]])])]),e("div",be,[e("button",{onClick:p,class:"flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"},g(o(s).processing?"Espere...":"Preguntar"),1)])])],40,I)])])])])]),_:1})],64))}};export{Pe as default};