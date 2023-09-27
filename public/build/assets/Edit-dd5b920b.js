import{k as U,v as w,A as V,m as k,f,a as n,w as u,o as g,b as d,t as m,F as $,x as j,u as l,d as b,n as C,e as E}from"./app-fa012e40.js";import{_ as v}from"./InputError-bab8a326.js";import{_ as y}from"./InputLabel-0db2c892.js";import{_ as N,a as x}from"./SecondaryButton-8bd0ef43.js";import{_ as B}from"./PrimaryButton-f0e54c8a.js";import{_ as F}from"./TextInput-a37ed436.js";import{_ as M}from"./SelectInput-d6ecf7ad.js";const O={class:"space-y-6"},q=["onSubmit"],z={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},A={class:"my-6 grid grid-cols-2 gap-6"},D={class:"flex justify-end"},Q={__name:"Edit",props:{show:Boolean,title:String,Unidad:Object,MateriasSelect:Object},emits:["close"],setup(p,{emit:c}){const o=p,h=U({multipleSelect:!1}),e=w({...Object.fromEntries(["nombre","descripcion","materia_id","enum","codigo"].map(s=>[s,""]))}),S=[{idd:"enum",label:"enumUnidad",type:"number",value:e.enum},{idd:"nombre",label:"nombre",type:"text",value:e.nombre}];V(()=>{});const _=()=>{var s;e.put(route("Unidad.update",(s=o.Unidad)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{c("close"),e.reset(),h.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};return k(()=>{var s,t,a,i,r;o.show&&(e.errors={},e.nombre=(s=o.Unidad)==null?void 0:s.nombre,e.descripcion=(t=o.Unidad)==null?void 0:t.descripcion,e.materia_id=(a=o.Unidad)==null?void 0:a.materia_id,e.enum=(i=o.Unidad)==null?void 0:i.enum,e.codigo=(r=o.Unidad)==null?void 0:r.codigo)}),(s,t)=>(g(),f("section",O,[n(N,{show:o.show,onClose:t[2]||(t[2]=a=>c("close"))},{default:u(()=>[d("form",{class:"p-6",onSubmit:E(_,["prevent"])},[d("h2",z,m(s.lang().label.edit)+" "+m(o.title),1),d("div",A,[(g(),f($,null,j(S,(a,i)=>d("div",{key:i},[n(y,{for:a.label,value:s.lang().label[a.label]},null,8,["for","value"]),n(F,{id:a.idd,type:a.type,class:"mt-1 block w-full",modelValue:l(e)[a.idd],"onUpdate:modelValue":r=>l(e)[a.idd]=r,required:"",placeholder:a.label,error:l(e).errors[a.idd]},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"]),n(v,{class:"mt-2",message:l(e).errors[a.idd]},null,8,["message"])])),64)),d("div",null,[n(y,{for:"materia_id",value:s.lang().label.materia},null,8,["value"]),n(M,{name:"materia_id",class:"mt-1 block w-full",modelValue:l(e).materia_id,"onUpdate:modelValue":t[0]||(t[0]=a=>l(e).materia_id=a),required:"",dataSet:p.MateriasSelect},null,8,["modelValue","dataSet"]),n(v,{class:"mt-2",message:l(e).errors.materia_id},null,8,["message"])])]),d("div",D,[n(x,{disabled:l(e).processing,onClick:t[1]||(t[1]=a=>c("close"))},{default:u(()=>[b(m(s.lang().button.close),1)]),_:1},8,["disabled"]),n(B,{class:C(["ml-3",{"opacity-25":l(e).processing}]),disabled:l(e).processing,onClick:_},{default:u(()=>[b(m(l(e).processing?s.lang().button.save+"...":s.lang().button.save),1)]),_:1},8,["class","disabled"])])],40,q)]),_:1},8,["show"])]))}};export{Q as default};