import{r as V,v as B,a as N,O as A,w as D,c as a,b as m,u as n,f as g,F as x,x as E,k as s,X as P,d as e,l as _,t as i,s as y,g as k,m as w,y as U,h as v,A as $,B as F}from"./app-e4aec783.js";import{_ as M}from"./AuthenticatedLayout-262f04fb.js";import{_ as L}from"./Breadcrumb-3bf15452.js";import{_ as q}from"./TextInput-0d3578f0.js";import{C as T}from"./index-1d64f8a7.js";import{_ as X}from"./InfoButton-fdfad07d.js";import"./index-c5c5be5a.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./Toast-501ce206.js";const z={class:"text-gray-600 body-font"},G={class:"container px-5 py-1 mx-auto"},H={class:"flex flex-col text-center w-full mb-12"},J=e("h1",{class:"sm:text-3xl text-2xl font-medium title-font mb-2 text-gray-900"},"Matricular a la materia",-1),K={class:"w-full mx-auto leading-relaxed text-base"},Q={class:"flex space-x-2 text-center mx-auto"},R={class:"flex flex-wrap -m-2"},W={class:"h-full flex items-center"},Y=e("img",{alt:"team",class:"w-16 h-16 bg-gray-100 object-cover object-center flex-shrink-0 rounded-full mr-4",src:"https://dummyimage.com/80x80"},null,-1),Z={class:"mx-0.5 px-2"},ee=["value"],te={class:"flex-grow"},se={class:"text-gray-900 title-font font-medium"},re={class:"text-xs text-gray-500 title-font font-medium"},ae={key:1,class:"p-1 w-full border-gray-200 border rounded-lg text-center"},oe=e("p",{class:"w-full mx-auto leading-relaxed text-base text-red-700"}," No existen usuarios con este criterio ",-1),le=[oe],ie={key:0,class:"flex flex-wrap my-8"},ce={class:"p-4 xl:w-1/3 sm:w-1/2 w-full"},ne=e("h2",{class:"font-medium title-font tracking-widest text-gray-900 mb-4 text-xl text-center sm:text-left"}," Inscritos",-1),de={class:"flex flex-col sm:items-start sm:text-left text-center items-center -mb-1 space-y-2.5"},me=e("span",{class:"bg-indigo-100 text-indigo-500 w-4 h-4 mr-2 rounded-full inline-flex items-center justify-center"},[e("svg",{fill:"none",stroke:"currentColor","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"3",class:"w-3 h-3",viewBox:"0 0 24 24"},[e("path",{d:"M20 6L9 17l-5-5"})])],-1),ke={__name:"AsignarUsers",props:{title:String,breadcrumbs:Object,filters:Object,usuariosPorInscribir:Object,universidad:Object,carrera:Object,materia:Object,inscritos:Object},setup(u){var b;const t=u,{_:j,debounce:I,pickBy:C}=E,d=V({params:{search:t.filters.search},selectedId:[]}),c=B({selectedId:[],materiaid:(b=t.materia)==null?void 0:b.id});N(()=>j.cloneDeep(d.params),I(()=>{let o=C(d.params);A.get(route("materia.index"),o,{replace:!0,preserveState:!0,preserveScroll:!0})},150)),D(()=>{});const O=()=>{c.post(route("materia.SubmitAsignarUsers"),{preserveScroll:!0,onSuccess:()=>{c.reset()},onError:()=>null,onFinish:()=>null})};return(o,l)=>{const S=F("tooltip");return s(),a(x,null,[m(n(P),{title:t.title},null,8,["title"]),m(M,null,{default:g(()=>{var h;return[m(L,{title:u.title,breadcrumbs:u.breadcrumbs},null,8,["title","breadcrumbs"]),e("section",z,[e("div",G,[e("div",H,[J,e("p",K,[_(" Estudiantes de "),e("b",null,i(t.carrera.nombre),1),_(" que no pertenecen a "),e("b",null,i((h=t.materia)==null?void 0:h.nombre),1)]),t.numberPermissions>1?(s(),y(q,{key:0,modelValue:d.params.search,"onUpdate:modelValue":l[0]||(l[0]=r=>d.params.search=r),type:"text",class:"my-4 mx-auto block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg",placeholder:"Nombre, correo"},null,8,["modelValue"])):k("",!0),e("div",Q,[w((s(),y(X,{onClick:O,class:"px-3 py-1.5"},{default:g(()=>[m(n(T),{class:"w-5 h-5"})]),_:1})),[[U,n(c).selectedId.length!=0&&o.can(["delete user"])],[S,o.Asignar]])])]),e("div",R,[t.usuariosPorInscribir.length>0?(s(!0),a(x,{key:0},v(t.usuariosPorInscribir,(r,f)=>(s(),a("div",{key:f,class:"p-1 xl:w-1/4 lg:w-1/2 md:w-1/2 w-full border-gray-200 border rounded-lg"},[e("div",W,[Y,e("div",Z,[w(e("input",{type:"checkbox",onChange:l[1]||(l[1]=(...p)=>o.select&&o.select(...p)),"onUpdate:modelValue":l[2]||(l[2]=p=>n(c).selectedId=p),value:r.id,class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"},null,40,ee),[[$,n(c).selectedId]])]),e("div",te,[e("h2",se,i(r.name),1),e("small",re,i(r.email),1)])])]))),128)):(s(),a("div",ae,le))]),t.inscritos.length>0?(s(),a("div",ie,[e("div",ce,[ne,e("nav",de,[(s(!0),a(x,null,v(t.inscritos,(r,f)=>(s(),a("p",{key:f},[me,_(i(r.name)+" ("+i(r.email)+") ",1)]))),128))])])])):k("",!0)])])]}),_:1})],64)}}};export{ke as default};
