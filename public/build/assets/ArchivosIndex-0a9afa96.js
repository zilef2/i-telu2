import{k,v as M,A as D,m as F,f as d,a as i,u as s,w as h,F as f,o as m,X as A,b as e,e as B,t as a,q as E,C as N,g as x,x as S,h as _,d as b}from"./app-fa012e40.js";import{_ as j}from"./AuthenticatedLayout-e4269cae.js";import{_ as O}from"./Breadcrumb-a1f90ab0.js";import{f as P}from"./global-f5954534.js";import{_ as v}from"./BackButton-9e4f4214.js";import"./index-5d8dae98.js";import"./index-3cb9eb1b.js";const V={class:"text-gray-600 body-font"},C={class:"container px-5 mx-auto mb-12"},T=e("div",{class:"flex flex-nowrap -m-4 text-center"},[e("div",{class:"px-4 lg:w-full"},[e("div",{class:"h-full bg-gray-100 bg-opacity-75 px-8 pt-8 pb-1 rounded-lg overflow-hidden text-center relative"},[e("h1",{class:"title-font sm:text-xl text-lg font-medium text-gray-900"},"Formulario para guardar documentos (PDF)")])])],-1),X=["onSubmit"],$={class:"my-4 w-full"},z={for:"archivou",class:"w-full underline text-sky-700"},I=["value"],R={key:1,type:"submit",class:"w-1/2 mx-auto border border-white-500 bg-sky-500 text-white rounded-md px-4 py-2 m-2 transition duration-500 ease select-none hover:bg-yellow-600 focus:outline-none focus:shadow-outline"},q={class:"container px-5 my-8 mx-auto border border-y-2 border-x-0 border-black"},L={class:"text-center"},U={class:"title-font sm:text-xl text-lg my-5 font-medium text-gray-900"},G={class:"title-font sm:text-lg text-md my-5 text-gray-800"},H={class:"title-font sm:text-lg text-md my-5 text-gray-800"},J={class:"flex flex-wrap -m-4"},K={class:"h-full bg-gray-100 bg-opacity-75 px-8 pt-16 pb-24 rounded-lg overflow-hidden text-center relative"},Q={class:"tracking-widest text-xs title-font font-medium text-gray-400 mb-1"},W={class:"title-font sm:text-2xl text-xl font-medium text-gray-900 mb-3"},Y={class:"leading-relaxed mb-3"},Z={class:"grid grid-cols-1"},ee=e("a",{class:"text-indigo-500 inline-flex items-center"},"Visualizar archivo ",-1),te=e("a",{class:"text-indigo-500 inline-flex items-center"},[b("Resumir archivo "),e("svg",{class:"w-4 h-4 ml-2",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2",fill:"none","stroke-linecap":"round","stroke-linejoin":"round"},[e("path",{d:"M5 12h14"}),b(),e("path",{d:"M12 5l7 7-7 7"})])],-1),se=["src"],de={__name:"ArchivosIndex",props:{breadcrumbs:Object,title:String,numberPermissions:Number,archivos:Object,materia:Object},emits:["close"],setup(u,{emit:g}){const l=u,r=k({tamanin:"",TamanoMAX:0,sumOfpesos:0}),t=M({archivo:null,nombre:"",peso:0,type:"",user_id:"",materia_id:""}),y=()=>{t.materia_id=l.materia.id,t.post(route("materia.storeArchivos"),{preserveScroll:!0,onSuccess:()=>{g("close"),t.reset()},onError:()=>null,onFinish:()=>null})};D(()=>{r.TamanoMAX=1024*1024*12,l.archivos.forEach((n,c,o)=>{r.sumOfpesos+=n.peso})});const w=()=>{t.archivo&&(t.type=t.archivo.type,t.type=="application/pdf"?(t.peso=Math.round(t.archivo.size/(1024*1024)),t.peso<r.TamanoMAX?(t.nombre=t.archivo.name.slice(0,-4),r.tamanin="El archivo pesa aproximadamente "+t.peso+" MB"):r.mensajes="El peso del archivo supera los 12MB"):r.mensajes="El archivo debe ser un PDF")};return F(()=>{}),(n,c)=>(m(),d(f,null,[i(s(A),{title:l.title},null,8,["title"]),i(j,null,{default:h(()=>[i(O,{title:u.title,breadcrumbs:u.breadcrumbs},null,8,["title","breadcrumbs"]),e("section",V,[e("div",C,[T,e("form",{onSubmit:B(y,["prevent"]),enctype:"multipart/form-data",class:"grid mx-[120px] text-center"},[e("p",$,a(r.tamanin),1),E(e("input",{type:"text","onUpdate:modelValue":c[0]||(c[0]=o=>s(t).nombre=o),placeholder:"Digite el nombre del archivo",class:"w-1/2 mx-auto my-4"},null,512),[[N,s(t).nombre]]),e("label",z,a(s(t).archivo?s(t).archivo.name:"No hay archivo seleccionado"),1),e("input",{id:"archivou",type:"file",onInput:c[1]||(c[1]=o=>s(t).archivo=o.target.files[0]),onChange:w,accept:"application/pdf",class:"my-6 w-full hidden"},null,32),s(t).progress?(m(),d("progress",{key:0,value:s(t).progress.percentage,max:"100",class:"w-full"},a(s(t).progress.percentage)+"% ",9,I)):x("",!0),s(t).archivo?(m(),d("button",R," Subir archivo ")):x("",!0)],40,X),i(v,{ruta:"materia.index",class:"my-8 text-center"},null,8,["ruta"])]),e("div",q,[e("div",L,[e("h1",U,"Archivos relacionados con "+a(u.materia.nombre),1),e("h1",G,"Numero de archivos almacenados "+a(l.archivos.length),1),e("h1",H,"Peso total: "+a(r.sumOfpesos)+" MB",1)]),e("div",J,[(m(!0),d(f,null,S(l.archivos,(o,p)=>(m(),d("div",{key:p,class:"p-4 xl:w-1/3 lg:w-1/2 sm:w-full"},[e("div",K,[e("h2",Q,"Doc "+a(p+1),1),e("h1",W,a(o.nombre),1),e("p",Y,a(s(P)(o.updated_at)),1),e("div",Z,[i(s(_),{href:n.route("vistaPDF",o.id),class:"my-1"},{default:h(()=>[ee]),_:2},1032,["href"]),i(s(_),{href:n.route("generarResumen",o.id),class:"my-2 pb-12"},{default:h(()=>[te]),_:2},1032,["href"])]),e("embed",{src:n.route("verPdf",o.id),type:"application/pdf",height:"600px",class:"w-full mt-8"},null,8,se)])]))),128))]),i(v,{ruta:"materia.index",class:"my-12 text-center"},null,8,["ruta"])])])]),_:1})],64))}};export{de as default};
