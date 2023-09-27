import{k as w,v as V,m as k,f,a as o,w as m,o as g,b as i,t as c,u as l,F as $,x,d as v,n as C,e as B}from"./app-fa012e40.js";import{_ as b}from"./InputError-bab8a326.js";import{_ as y}from"./InputLabel-0db2c892.js";import{_ as E,a as F}from"./SecondaryButton-8bd0ef43.js";import{_ as N}from"./PrimaryButton-f0e54c8a.js";import{_ as j}from"./TextInput-a37ed436.js";import{_ as U}from"./SelectInput-d6ecf7ad.js";const q={class:"space-y-6"},O=["onSubmit"],P={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},z={class:"my-6 grid grid-cols-2 gap-6"},D={class:"flex justify-end"},J={__name:"Edit",props:{show:Boolean,title:String,carrera:Object,PapaSelect:Object},emits:["close"],setup(p,{emit:u}){const r=p,h=w({multipleSelect:!1}),e=V({nombre:"",descripcion:"",universidad_id:1,enum:1,codigo:1}),S=[{idd:"enum",label:"Numeración",type:"number",value:e.enum},{idd:"nombre",label:"nombre",type:"text",value:e.nombre},{idd:"codigo",label:"codigo",type:"text",value:e.codigo}],_=()=>{var s;e.put(route("carrera.update",(s=r.carrera)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{u("close"),e.reset(),h.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};return k(()=>{var s,d,a,n,t;r.show&&(e.errors={},e.nombre=(s=r.carrera)==null?void 0:s.nombre,e.descripcion=(d=r.carrera)==null?void 0:d.descripcion,e.universidad_id=(a=r.carrera)==null?void 0:a.universidad_id,e.enum=(n=r.carrera)==null?void 0:n.enum,e.codigo=(t=r.carrera)==null?void 0:t.codigo)}),(s,d)=>(g(),f("section",q,[o(E,{show:r.show,onClose:d[2]||(d[2]=a=>u("close"))},{default:m(()=>[i("form",{class:"p-6",onSubmit:B(_,["prevent"])},[i("h2",P,c(s.lang().label.edit)+" "+c(r.title),1),i("div",z,[i("div",null,[o(y,{for:"universidad_id",value:s.lang().label.universidad},null,8,["value"]),o(U,{id:"universidad_id",class:"mt-1 block w-full",modelValue:l(e).universidad_id,"onUpdate:modelValue":d[0]||(d[0]=a=>l(e).universidad_id=a),required:"",dataSet:p.PapaSelect},null,8,["modelValue","dataSet"]),o(b,{class:"mt-2",message:l(e).errors.universidad_id},null,8,["message"])]),(g(),f($,null,x(S,(a,n)=>i("div",{key:n},[o(y,{value:a.label},null,8,["value"]),o(j,{id:a.idd,type:a.type,class:"mt-1 block w-full",modelValue:l(e)[a.idd],"onUpdate:modelValue":t=>l(e)[a.idd]=t,required:"",placeholder:a.label,error:l(e).errors[a.idd]},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"]),o(b,{class:"mt-2",message:l(e).errors[a.idd]},null,8,["message"])])),64))]),i("div",D,[o(F,{disabled:l(e).processing,onClick:d[1]||(d[1]=a=>u("close"))},{default:m(()=>[v(c(s.lang().button.close),1)]),_:1},8,["disabled"]),o(N,{class:C(["ml-3",{"opacity-25":l(e).processing}]),disabled:l(e).processing,onClick:_},{default:m(()=>[v(c(l(e).processing?s.lang().button.save+"...":s.lang().button.save),1)]),_:1},8,["class","disabled"])])],40,O)]),_:1},8,["show"])]))}};export{J as default};