import{v as p,w as _,c as g,b as d,f as c,k as b,d as a,t as s,u as n,l as f,q as h,e as y}from"./app-8190fc74.js";import{_ as k}from"./DangerButton-7b937436.js";import{_ as w,a as x}from"./Modal-41b4ce14.js";const v={class:"space-y-6"},S=["onSubmit"],B={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},C={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},$={class:"mt-6 flex justify-end"},j={__name:"DeleteBulk",props:{show:Boolean,title:String,selectedId:Object},emits:["close"],setup(u,{emit:r}){const t=u,e=p({id:[]}),m=()=>{e.post(route("Plan.destroy-bulk"),{preserveScroll:!0,onSuccess:()=>{r("close"),e.reset()},onError:()=>null,onFinish:()=>null})};return _(()=>{t.show&&(e.id=t.selectedId)}),(o,l)=>(b(),g("section",v,[d(w,{show:t.show,onClose:l[1]||(l[1]=i=>r("close")),maxWidth:"lg"},{default:c(()=>{var i;return[a("form",{class:"p-6",onSubmit:y(m,["prevent"])},[a("h2",B,s(o.lang().label.delete)+" "+s(t.title),1),a("p",C,s(o.lang().label.delete_confirm)+" "+s((i=t.selectedId)==null?void 0:i.length)+" "+s(t.title)+"? ",1),a("div",$,[d(x,{disabled:n(e).processing,onClick:l[0]||(l[0]=E=>r("close"))},{default:c(()=>[f(s(o.lang().button.close),1)]),_:1},8,["disabled"]),d(k,{class:h(["ml-3",{"opacity-25":n(e).processing}]),disabled:n(e).processing,onClick:m},{default:c(()=>[f(s(n(e).processing?o.lang().button.delete+"...":o.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,S)]}),_:1},8,["show"])]))}};export{j as default};