import{v as f,m as _,f as g,a as l,w as d,o as b,b as t,t as r,u as s,d as u,n as h,e as v}from"./app-fa012e40.js";import{_ as w}from"./InputError-bab8a326.js";import{_ as y}from"./InputLabel-0db2c892.js";import{_ as $,a as k}from"./SecondaryButton-8bd0ef43.js";import{_ as C}from"./PrimaryButton-f0e54c8a.js";import{_ as S}from"./TextInput-a37ed436.js";const V={class:"space-y-6"},B=["onSubmit"],x={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},E={class:"my-6 space-y-4"},N={class:"flex justify-end"},T={__name:"Create",props:{show:Boolean,title:String},emits:["close"],setup(p,{emit:n}){const i=p,e=f({name:""}),c=()=>{e.post(route("permission.store"),{preserveScroll:!0,onSuccess:()=>{n("close"),e.reset()},onError:()=>null,onFinish:()=>null})};return _(()=>{i.show&&(e.errors={})}),(o,a)=>(b(),g("section",V,[l($,{show:i.show,onClose:a[2]||(a[2]=m=>n("close"))},{default:d(()=>[t("form",{class:"p-6",onSubmit:v(c,["prevent"])},[t("h2",x,r(o.lang().label.add)+" "+r(i.title),1),t("div",E,[t("div",null,[l(y,{for:"name",value:o.lang().label.role},null,8,["value"]),l(S,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:s(e).name,"onUpdate:modelValue":a[0]||(a[0]=m=>s(e).name=m),required:"",placeholder:o.lang().placeholder.name,error:s(e).errors.name},null,8,["modelValue","placeholder","error"]),l(w,{class:"mt-2",message:s(e).errors.name},null,8,["message"])])]),t("div",N,[l(k,{disabled:s(e).processing,onClick:a[1]||(a[1]=m=>n("close"))},{default:d(()=>[u(r(o.lang().button.close),1)]),_:1},8,["disabled"]),l(C,{class:h(["ml-3",{"opacity-25":s(e).processing}]),disabled:s(e).processing,onClick:c},{default:d(()=>[u(r(s(e).processing?o.lang().button.add+"...":o.lang().button.add),1)]),_:1},8,["class","disabled"])])],40,B)]),_:1},8,["show"])]))}};export{T as default};
