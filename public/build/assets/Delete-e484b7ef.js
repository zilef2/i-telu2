import{v as f,c as _,b as d,f as c,k as g,d as o,t,l as u,u as n,q as b,e as h}from"./app-e4aec783.js";import{_ as v}from"./DangerButton-b3b2af7b.js";import{_ as y,a as k}from"./Modal-d321b06e.js";const x={class:"space-y-6"},w=["onSubmit"],S={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},D={__name:"Delete",props:{show:Boolean,title:String,universidad:Object},emits:["close"],setup(p,{emit:r}){const a=p,s=f({}),m=()=>{var e;s.delete(route("universidad.destroy",(e=a.universidad)==null?void 0:e.id),{preserveScroll:!0,onSuccess:()=>{r("close"),s.reset()},onError:()=>null,onFinish:()=>null})};return(e,l)=>(g(),_("section",x,[d(y,{show:a.show,onClose:l[1]||(l[1]=i=>r("close")),maxWidth:"lg"},{default:c(()=>{var i;return[o("form",{class:"p-6",onSubmit:h(m,["prevent"])},[o("h2",S,t(e.lang().label.delete)+" "+t(a.title),1),o("p",C,[u(t(e.lang().label.delete_confirm)+" ",1),o("b",null,t((i=a.universidad)==null?void 0:i.nombre)+" !?",1)]),o("div",$,[d(k,{disabled:n(s).processing,onClick:l[0]||(l[0]=B=>r("close"))},{default:c(()=>[u(t(e.lang().button.close),1)]),_:1},8,["disabled"]),d(v,{class:b(["ml-3",{"opacity-25":n(s).processing}]),disabled:n(s).processing,onClick:m},{default:c(()=>[u(t(n(s).processing?e.lang().button.delete+"...":e.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,w)]}),_:1},8,["show"])]))}};export{D as default};
