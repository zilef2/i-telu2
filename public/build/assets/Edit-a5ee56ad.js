import{r as w,v as V,w as k,c as f,b as i,f as m,k as g,d as o,t as c,u as l,F as $,h as x,l as v,q as C,e as B}from"./app-e4aec783.js";import{_ as b}from"./InputError-ff01e7fb.js";import{_ as y}from"./InputLabel-776359c0.js";import{_ as E,a as F}from"./Modal-d321b06e.js";import{_ as N}from"./PrimaryButton-62c6067b.js";import{_ as j}from"./TextInput-0d3578f0.js";import{_ as q}from"./SelectInput-e07a183c.js";const U={class:"space-y-6"},O=["onSubmit"],P={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},z={class:"my-6 grid grid-cols-2 gap-6"},D={class:"flex justify-end"},J={__name:"Edit",props:{show:Boolean,title:String,carrera:Object,PapaSelect:Object},emits:["close"],setup(p,{emit:u}){const r=p,h=w({multipleSelect:!1}),e=V({nombre:"",descripcion:"",universidad_id:1,enum:1,codigo:1}),S=[{idd:"enum",label:"Numeración",type:"number",value:e.enum},{idd:"nombre",label:"nombre",type:"text",value:e.nombre},{idd:"codigo",label:"codigo",type:"text",value:e.codigo}],_=()=>{var s;e.put(route("carrera.update",(s=r.carrera)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{u("close"),e.reset(),h.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};return k(()=>{var s,d,a,n,t;r.show&&(e.errors={},e.nombre=(s=r.carrera)==null?void 0:s.nombre,e.descripcion=(d=r.carrera)==null?void 0:d.descripcion,e.universidad_id=(a=r.carrera)==null?void 0:a.universidad_id,e.enum=(n=r.carrera)==null?void 0:n.enum,e.codigo=(t=r.carrera)==null?void 0:t.codigo)}),(s,d)=>(g(),f("section",U,[i(E,{show:r.show,onClose:d[2]||(d[2]=a=>u("close"))},{default:m(()=>[o("form",{class:"p-6",onSubmit:B(_,["prevent"])},[o("h2",P,c(s.lang().label.edit)+" "+c(r.title),1),o("div",z,[o("div",null,[i(y,{for:"universidad_id",value:s.lang().label.universidad},null,8,["value"]),i(q,{id:"universidad_id",class:"mt-1 block w-full",modelValue:l(e).universidad_id,"onUpdate:modelValue":d[0]||(d[0]=a=>l(e).universidad_id=a),required:"",dataSet:p.PapaSelect},null,8,["modelValue","dataSet"]),i(b,{class:"mt-2",message:l(e).errors.universidad_id},null,8,["message"])]),(g(),f($,null,x(S,(a,n)=>o("div",{key:n},[i(y,{value:a.label},null,8,["value"]),i(j,{id:a.idd,type:a.type,class:"mt-1 block w-full",modelValue:l(e)[a.idd],"onUpdate:modelValue":t=>l(e)[a.idd]=t,required:"",placeholder:a.label,error:l(e).errors[a.idd]},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"]),i(b,{class:"mt-2",message:l(e).errors[a.idd]},null,8,["message"])])),64))]),o("div",D,[i(F,{disabled:l(e).processing,onClick:d[1]||(d[1]=a=>u("close"))},{default:m(()=>[v(c(s.lang().button.close),1)]),_:1},8,["disabled"]),i(N,{class:C(["ml-3",{"opacity-25":l(e).processing}]),disabled:l(e).processing,onClick:_},{default:m(()=>[v(c(l(e).processing?s.lang().button.save+"...":s.lang().button.save),1)]),_:1},8,["class","disabled"])])],40,O)]),_:1},8,["show"])]))}};export{J as default};
