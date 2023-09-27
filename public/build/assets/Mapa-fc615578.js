import{_ as x}from"./AuthenticatedLayout-e4269cae.js";import{z as p,k as g,A as h,f as t,a as l,u as o,w as d,F as c,o as s,X as y,b as a,t as n,x as u,g as r,n as b}from"./app-fa012e40.js";import{c as k}from"./index-3cb9eb1b.js";import{_ as v}from"./BackButton-9e4f4214.js";import{Q as w,V as C,X as O}from"./disclosure-fd3ac3b2.js";import"./index-5d8dae98.js";import"./global-f5954534.js";const V={class:"text-gray-600 body-font"},B={class:"container px-5 py-5 mx-auto"},j={class:"flex flex-col text-center w-full mb-20"},z={class:"sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900"},N=a("p",{class:"lg:w-2/3 mx-auto leading-relaxed text-base"}," Visualizar las asignaturas asociadas a la carera ",-1),X={class:"flex flex-wrap -m-2"},$={class:"p-2 lg:w-1/3 md:w-1/2 w-full"},F={class:"h-full flex items-center border-gray-200 border p-4 rounded-lg"},I={class:"flex-grow"},M={class:"text-gray-900 title-font font-medium"},S={key:0,class:"text-gray-500"},U={key:1,class:"text-gray-500"},A={key:2,class:"text-gray-500"},D={key:0,class:"list-decimal"},E={class:"text-lg ml-4"},T={__name:"Mapa",props:{Carrera:Object,Unidades:Object},setup(_){const i=_;return p(!1),g({selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,generico:null}),h(()=>{}),(L,Q)=>(s(),t(c,null,[l(o(y),{title:i.title},null,8,["title"]),l(x,null,{default:d(()=>[a("section",V,[a("div",B,[a("div",j,[a("h1",z,n(i.Carrera.nombre),1),N]),a("div",X,[i.Carrera.materias_enum.length?(s(!0),t(c,{key:0},u(i.Carrera.materias_enum,e=>(s(),t("div",$,[l(o(w),{as:"div",class:"mt-2"},{default:d(({open:f})=>[l(o(C),{class:"flex w-full justify-between rounded-lg bg-sky-100 px-4 py-2 text-left text-sm font-medium text-sky-900 hover:bg-green-200 focus:outline-none focus-visible:ring focus-visible:ring-sky-500 focus-visible:ring-opacity-75"},{default:d(()=>[a("div",F,[a("div",I,[a("h2",M,n(e.enum)+" "+n(e.nombre),1),e.unidads.length==0?(s(),t("p",S,"sin unidades")):r("",!0),e.unidads.length==1?(s(),t("p",U,n(e.unidads.length)+" unidad",1)):r("",!0),e.unidads.length>1?(s(),t("p",A,n(e.unidads.length)+" unidades",1)):r("",!0)])]),l(o(k),{class:b([f?"rotate-180 transform":"","h-5 w-5 text-sky-500"])},null,8,["class"])]),_:2},1024),l(o(O),{class:"px-4 pt-4 pb-2 text-sm text-gray-500"},{default:d(()=>[e.unidads.length?(s(),t("ol",D,[(s(!0),t(c,null,u(e.unidads,m=>(s(),t("li",E,n(m.nombre),1))),256))])):r("",!0)]),_:2},1024)]),_:2},1024)]))),256)):r("",!0)])]),l(v,{ruta:"carrera.index",class:"text-center"},null,8,["ruta"])])]),_:1})],64))}};export{T as default};