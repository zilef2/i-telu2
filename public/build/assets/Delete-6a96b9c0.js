import{v as f,c as _,b as c,f as d,k as g,d as o,t,l as m,u as r,q as b,e as h}from"./app-8190fc74.js";import{_ as y}from"./DangerButton-7b937436.js";import{_ as k,a as x}from"./Modal-41b4ce14.js";const v={class:"space-y-6"},w=["onSubmit"],S={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},D={__name:"Delete",props:{show:Boolean,title:String,carrera:Object},emits:["close"],setup(p,{emit:n}){const a=p,s=f({}),u=()=>{var e;s.delete(route("carrera.destroy",(e=a.carrera)==null?void 0:e.id),{preserveScroll:!0,onSuccess:()=>{n("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(e,l)=>(g(),_("section",v,[c(k,{show:a.show,onClose:l[1]||(l[1]=i=>n("close")),maxWidth:"lg"},{default:d(()=>{var i;return[o("form",{class:"p-6",onSubmit:h(u,["prevent"])},[o("h2",S,t(e.lang().label.delete)+" "+t(a.title),1),o("p",C,[m(t(e.lang().label.delete_confirm)+" ",1),o("b",null,t((i=a.carrera)==null?void 0:i.nombre)+" !?",1)]),o("div",$,[c(x,{disabled:r(s).processing,onClick:l[0]||(l[0]=B=>n("close"))},{default:d(()=>[m(t(e.lang().button.close),1)]),_:1},8,["disabled"]),c(y,{class:b(["ml-3",{"opacity-25":r(s).processing}]),disabled:r(s).processing,onClick:u},{default:d(()=>[m(t(r(s).processing?e.lang().button.delete+"...":e.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,w)]}),_:1},8,["show"])]))}};export{D as default};