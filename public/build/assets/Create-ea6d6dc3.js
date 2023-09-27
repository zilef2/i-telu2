import{k as S,v as x,m as C,f as u,a as l,w as p,o as f,b as t,t as d,u as a,F as $,x as V,q as B,B as j,d as _,n as E,e as F}from"./app-fa012e40.js";import{_ as h}from"./InputError-bab8a326.js";import{_ as m}from"./InputLabel-0db2c892.js";import{_ as N,a as U}from"./SecondaryButton-8bd0ef43.js";import{_ as q}from"./PrimaryButton-f0e54c8a.js";import{_ as D}from"./TextInput-a37ed436.js";import{_ as M}from"./Checkbox-43286906.js";const z={class:"space-y-6"},A=["onSubmit"],L={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},O={class:"my-6 space-y-4"},T={class:"flex justify-start items-center space-x-2 mt-2"},G={class:"grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mt-2"},H=["id","value"],I={class:"flex justify-end"},Y={__name:"Create",props:{show:Boolean,title:String,permissions:Object},emits:["close"],setup(v,{emit:c}){const i=v,n=S({multipleSelect:!1}),e=x({name:"",guard_name:"web",permissions:[]}),g=()=>{e.post(route("role.store"),{preserveScroll:!0,onSuccess:()=>{c("close"),e.reset(),n.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};C(()=>{i.show&&(e.errors={})});const b=r=>{r.target.checked===!1?e.permissions=[]:(e.permissions=[],i.permissions.forEach(s=>{e.permissions.push(s.id)}))},k=()=>{i.permissions.length==e.permissions.length?n.multipleSelect=!0:n.multipleSelect=!1};return(r,s)=>(f(),u("section",z,[l(N,{show:i.show,onClose:s[4]||(s[4]=o=>c("close"))},{default:p(()=>[t("form",{class:"p-6",onSubmit:F(g,["prevent"])},[t("h2",L,d(r.lang().label.add)+" "+d(i.title),1),t("div",O,[t("div",null,[l(m,{for:"name",value:r.lang().label.role},null,8,["value"]),l(D,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:a(e).name,"onUpdate:modelValue":s[0]||(s[0]=o=>a(e).name=o),required:"",placeholder:r.lang().placeholder.name,error:a(e).errors.name},null,8,["modelValue","placeholder","error"]),l(h,{class:"mt-2",message:a(e).errors.name},null,8,["message"])]),t("div",null,[l(m,{value:r.lang().label.permission},null,8,["value"]),l(h,{class:"mt-2",message:a(e).errors.permissions},null,8,["message"]),t("div",T,[l(M,{id:"permission-all",checked:n.multipleSelect,"onUpdate:checked":s[1]||(s[1]=o=>n.multipleSelect=o),onChange:b},null,8,["checked"]),l(m,{for:"permission-all",value:r.lang().label.check_all},null,8,["value"])]),t("div",G,[(f(!0),u($,null,V(i.permissions,(o,y)=>(f(),u("div",{class:"flex items-center justify-start space-x-2",key:y},[B(t("input",{onChange:k,class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",id:"permission_"+o.id,value:o.id,"onUpdate:modelValue":s[2]||(s[2]=w=>a(e).permissions=w)},null,40,H),[[j,a(e).permissions]]),l(m,{for:"permission_"+o.id,value:o.name},null,8,["for","value"])]))),128))])])]),t("div",I,[l(U,{disabled:a(e).processing,onClick:s[3]||(s[3]=o=>c("close"))},{default:p(()=>[_(d(r.lang().button.close),1)]),_:1},8,["disabled"]),l(q,{class:E(["ml-3",{"opacity-25":a(e).processing}]),disabled:a(e).processing,onClick:g},{default:p(()=>[_(d(a(e).processing?r.lang().button.add+"...":r.lang().button.add),1)]),_:1},8,["class","disabled"])])],40,A)]),_:1},8,["show"])]))}};export{Y as default};