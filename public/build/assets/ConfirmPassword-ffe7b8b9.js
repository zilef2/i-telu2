import{v as d,l as p,b as t,o as c,a as r,u as o,X as u,d as e,t as i,e as f,n as _,f as w}from"./app-6467c706.js";import{_ as g,a as b}from"./AuntheticationIllustration-6517294f.js";import{_ as h}from"./InputError-0260cbdd.js";import{_ as v}from"./InputLabel-04b443e6.js";import{_ as y}from"./PrimaryButton-037173fe.js";import{_ as V}from"./TextInput-d4362326.js";import"./index-23384e0f.js";import"./SwitchDarkMode-dabce9b5.js";import"./index-b72a4a48.js";const $={class:"mb-4 text-sm text-gray-600 dark:text-gray-400"},k=["onSubmit"],B={class:"flex justify-end mt-4"},P={__name:"ConfirmPassword",setup(C){const s=d({password:""}),n=()=>{s.post(route("password.confirm"),{onFinish:()=>s.reset()})};return(a,l)=>(c(),p(g,null,{illustration:t(()=>[r(b,{type:"login",class:"w-72 h-auto"})]),default:t(()=>[r(o(u),{title:a.lang().label.password_confirmation},null,8,["title"]),e("div",$,i(a.lang().label.confirm_password),1),e("form",{onSubmit:w(n,["prevent"])},[e("div",null,[r(v,{for:"password",value:a.lang().label.password},null,8,["value"]),r(V,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:o(s).password,"onUpdate:modelValue":l[0]||(l[0]=m=>o(s).password=m),required:"",autocomplete:"current-password",autofocus:"",placeholder:a.lang().placeholder.password,error:o(s).errors.password},null,8,["modelValue","placeholder","error"]),r(h,{class:"mt-2",message:o(s).errors.password},null,8,["message"])]),e("div",B,[r(y,{class:_(["ml-4",{"opacity-25":o(s).processing}]),disabled:o(s).processing},{default:t(()=>[f(i(o(s).processing?a.lang().button.confirm_password+"...":a.lang().button.confirm_password),1)]),_:1},8,["class","disabled"])])],40,k)]),_:1}))}};export{P as default};
