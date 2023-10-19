import{r as B,v as z,a as E,w as M,o as N,c as f,b as o,f as V,k as b,d as n,t as p,q as d,u as a,F,h as P,g as y,m as D,A,l as C,s as L,e as R}from"./app-e4aec783.js";import{_ as m}from"./InputError-ff01e7fb.js";import{_ as u}from"./InputLabel-776359c0.js";import{_ as T,a as G}from"./Modal-d321b06e.js";import{_ as H}from"./PrimaryButton-62c6067b.js";import{_ as c}from"./TextInput-0d3578f0.js";import{_ as I}from"./SelectInput-e07a183c.js";const J={class:"space-y-6"},K=["onSubmit"],Q={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},W={class:"my-6 grid grid-cols-2 gap-8"},X={key:0,class:"flex"},Y={class:"flex justify-end"},se={__name:"Edit",props:{show:Boolean,title:String,materia:Object,carrerasSelect:Object,MateriasRequisitoSelect:Object,numberPermissions:Number},emits:["close"],setup(i,{emit:j}){var w;const t=i,v=B({unaVez:!0}),e=z({nombre:"",descripcion:"",carrera_id:"",cuantosObj:(w=t.materia)==null?void 0:w.objetivs,codigo:"",enum:0,activar:0,objetivo:[]});E(e.objetivo,(l,r)=>{t.show&&(e.cuantosObj=(typeof(e==null?void 0:e.objetivo[0])<"u")+(typeof(e==null?void 0:e.objetivo[1])<"u")+(typeof(e==null?void 0:e.objetivo[2])<"u")+(typeof(e==null?void 0:e.objetivo[3])<"u")+(typeof(e==null?void 0:e.objetivo[4])<"u"))});const h=()=>{var l;e.put(route("materia.update",(l=t.materia)==null?void 0:l.id),{preserveScroll:!0,onSuccess:()=>{j("close"),e.reset(),v.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};return M(()=>{var l,r,s,g,k,O,S,q;t.show?(e.errors={},v.unaVez&&(e.nombre=(l=t.materia)==null?void 0:l.nombre,e.descripcion=(r=t.materia)==null?void 0:r.descripcion,e.carrera_id=(s=t.materia)==null?void 0:s.carrera_id,e.cuantosObj=(g=t.materia)==null?void 0:g.objetivs,e.enum=(k=t.materia)==null?void 0:k.enum,e.codigo=(O=t.materia)==null?void 0:O.codigo,(S=t.materia)==null||S.objetivos.forEach((Z,U)=>{var $;e.objetivo[U]=($=t.materia)==null?void 0:$.objetivos[U].nombre}),e.activar=(!!((q=t.materia)!=null&&q.activa)).valueOf(),v.unaVez=!1)):v.unaVez=!0}),N(()=>{}),(l,r)=>(b(),f("section",J,[o(T,{show:t.show,onClose:r[8]||(r[8]=s=>j("close"))},{default:V(()=>[n("form",{class:"p-6",onSubmit:R(h,["prevent"])},[n("h2",Q,p(l.lang().label.edit)+" "+p(t.title),1),n("div",W,[n("div",null,[o(u,{for:"carrera_id",value:l.lang().label.carrera},null,8,["value"]),o(I,{id:"carrera_id",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).carrera_id,"onUpdate:modelValue":r[0]||(r[0]=s=>a(e).carrera_id=s),required:"",dataSet:t.carrerasSelect,disabled:!i.materia.activa},null,8,["modelValue","dataSet","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.carrera_id},null,8,["message"])]),n("div",null,[o(u,{for:"enum",value:l.lang().label.enum},null,8,["value"]),o(c,{id:"enum",type:"number",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).enum,"onUpdate:modelValue":r[1]||(r[1]=s=>a(e).enum=s),required:"",placeholder:l.lang().placeholder.enum,error:a(e).errors.enum,disabled:!i.materia.activa},null,8,["modelValue","placeholder","error","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.enum},null,8,["message"])]),n("div",null,[o(u,{for:"nombre",value:l.lang().label.name},null,8,["value"]),o(c,{id:"nombre",type:"text",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).nombre,"onUpdate:modelValue":r[2]||(r[2]=s=>a(e).nombre=s),required:"",placeholder:l.lang().placeholder.nombre,error:a(e).errors.nombre,disabled:!i.materia.activa},null,8,["modelValue","placeholder","error","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.nombre},null,8,["message"])]),n("div",null,[o(u,{for:"codigo",value:l.lang().label.codigo},null,8,["value"]),o(c,{id:"codigo",type:"text",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).codigo,"onUpdate:modelValue":r[3]||(r[3]=s=>a(e).codigo=s),required:"",placeholder:l.lang().placeholder.codigo,error:a(e).errors.codigo,disabled:!i.materia.activa},null,8,["modelValue","placeholder","error","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.codigo},null,8,["message"])]),n("div",null,[o(u,{for:"descripcion",value:l.lang().label.descripcion},null,8,["value"]),o(c,{id:"descripcion",type:"text",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).descripcion,"onUpdate:modelValue":r[4]||(r[4]=s=>a(e).descripcion=s),required:"",placeholder:l.lang().placeholder.descripcion,error:a(e).errors.descripcion,disabled:!i.materia.activa},null,8,["modelValue","placeholder","error","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.descripcion},null,8,["message"])]),n("div",null,[o(u,{for:"cuantosObj",value:l.lang().label.cuantosObj},null,8,["value"]),o(c,{id:"cuantosObj",type:"number",min:"0",max:"3",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).cuantosObj,"onUpdate:modelValue":r[5]||(r[5]=s=>a(e).cuantosObj=s),modelModifiers:{number:!0},required:"",placeholder:l.lang().placeholder.cuantosObj,disabled:!i.materia.activa},null,8,["modelValue","placeholder","disabled","class"])]),a(e).cuantosObj>0?(b(!0),f(F,{key:0},P(a(e).cuantosObj,s=>(b(),f("div",null,[o(u,{for:"",value:l.lang().label.objetivo+s},null,8,["value"]),o(c,{id:"objetivo",type:"text",min:"0",max:"3",class:d(["mt-1 block w-full",{"bg-gray-300":!i.materia.activa}]),modelValue:a(e).objetivo[s-1],"onUpdate:modelValue":g=>a(e).objetivo[s-1]=g,required:"",placeholder:l.lang().placeholder.objetivo,disabled:!i.materia.activa},null,8,["modelValue","onUpdate:modelValue","placeholder","disabled","class"]),o(m,{class:"mt-2",message:a(e).errors.objetivo},null,8,["message"])]))),256)):y("",!0)]),i.numberPermissions>2?(b(),f("div",X,[D(n("input",{type:"checkbox","onUpdate:modelValue":r[6]||(r[6]=s=>a(e).activar=s),name:"activar",class:"mt-1 flex w-6 h-6 mr-2"},null,512),[[A,a(e).activar]]),o(u,{for:"activar",value:l.lang().label.activar,class:"mt-1"},null,8,["value"])])):y("",!0),n("div",Y,[o(G,{disabled:a(e).processing,onClick:r[7]||(r[7]=s=>j("close"))},{default:V(()=>[C(p(l.lang().button.close),1)]),_:1},8,["disabled"]),i.materia.activa||i.numberPermissions>2?(b(),L(H,{key:0,class:d(["ml-3",{"opacity-25":a(e).processing}]),disabled:a(e).processing,onClick:h},{default:V(()=>[C(p(a(e).processing?l.lang().button.save+"...":l.lang().button.save),1)]),_:1},8,["class","disabled"])):y("",!0)])],40,K)]),_:1},8,["show"])]))}};export{se as default};
