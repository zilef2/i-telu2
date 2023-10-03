import{r as D,v as U,w as N,o as f,c as w,a as o,b as h,d as e,f as I,t as r,u as t,e as b,n as P,F as R,g as M,J,h as W,O as H,i as X,p as G,X as K,j as V,k as F,l as x,m as C,q as Q,s as Y}from"./app-6467c706.js";import{_ as Z}from"./AuthenticatedLayout-6da35bc8.js";import{_ as ee}from"./Breadcrumb-26092690.js";import{_ as j}from"./TextInput-d4362326.js";import{_ as E}from"./PrimaryButton-037173fe.js";import{_ as q}from"./SelectInput-a92191a9.js";import{_ as O}from"./DangerButton-180b4b60.js";import{_ as se}from"./Pagination-11770208.js";import{T,C as g,P as te}from"./index-b72a4a48.js";import{_ as S}from"./InputError-0260cbdd.js";import{_ as $}from"./InputLabel-04b443e6.js";import{a as z,_ as B}from"./SecondaryButton-b7ca80cc.js";import{_ as le}from"./Checkbox-ea56e211.js";import{_ as oe}from"./InfoButton-4b9404f5.js";import"./index-156de5d7.js";import{s as _}from"./global-8f2c086a.js";import"./index-23384e0f.js";const re={class:"space-y-6"},ne=["onSubmit"],ae={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},de={class:"font-serif text-gray-800 dark:text-gray-100"},ie={class:"my-6 grid xs:grid-cols-1 sm:grid-cols-2 gap-6"},ce={class:"flex justify-end"},pe={__name:"Create",props:{show:Boolean,title:String,UnidadsSelect:Object},emits:["close"],setup(v,{emit:i}){const m=v,y=D({multipleSelect:!1}),d=U({nombre:"",enum:"",codigo:"",descripcion:"",resultado_aprendizaje:"",unidad_id:0}),s=()=>{d.post(route("articulo.store"),{preserveScroll:!0,onSuccess:()=>{i("close"),d.reset(),y.multipleSelect=!1},onError:()=>alert(JSON.stringify(d.errors,null,4)),onFinish:()=>null})};return N(()=>{m.show&&(d.errors={})}),(a,u)=>(f(),w("section",re,[o(B,{show:m.show,onClose:u[6]||(u[6]=c=>i("close"))},{default:h(()=>[e("form",{class:"p-6",onSubmit:I(s,["prevent"])},[e("h2",ae,r(a.lang().label.add)+" "+r(m.title),1),e("h2",de,r(a.lang().LongTexts.markObligatory),1),e("div",ie,[e("div",null,[o($,{for:"unidad_id",value:a.lang().label.materia+"*"},null,8,["value"]),o(q,{name:"unidad_id",class:"mt-1 block w-full",modelValue:t(d).unidad_id,"onUpdate:modelValue":u[0]||(u[0]=c=>t(d).unidad_id=c),required:"",dataSet:v.UnidadsSelect},null,8,["modelValue","dataSet"]),o(S,{class:"mt-2",message:t(d).errors.unidad_id},null,8,["message"])]),e("div",null,[o($,{for:"enum",value:a.lang().label.enumTema},null,8,["value"]),o(j,{id:"enum",type:"number",class:"mt-1 block w-full",modelValue:t(d).enum,"onUpdate:modelValue":u[1]||(u[1]=c=>t(d).enum=c),required:"",placeholder:a.lang().placeholder.enum,error:t(d).errors.enum},null,8,["modelValue","placeholder","error"]),o(S,{class:"mt-2",message:t(d).errors.enum},null,8,["message"])]),e("div",null,[o($,{for:"nombre",value:a.lang().label.name+"*"},null,8,["value"]),o(j,{id:"nombre",type:"text",class:"mt-1 block w-full",modelValue:t(d).nombre,"onUpdate:modelValue":u[2]||(u[2]=c=>t(d).nombre=c),required:"",placeholder:a.lang().placeholder.nombre,error:t(d).errors.nombre},null,8,["modelValue","placeholder","error"]),o(S,{class:"mt-2",message:t(d).errors.nombre},null,8,["message"])]),e("div",null,[o($,{for:"resultado_aprendizaje",value:a.lang().label.resultado_aprendizaje},null,8,["value"]),o(j,{id:"resultado_aprendizaje",type:"text",class:"mt-1 block w-full",modelValue:t(d).resultado_aprendizaje,"onUpdate:modelValue":u[3]||(u[3]=c=>t(d).resultado_aprendizaje=c),required:"",placeholder:a.lang().placeholder.resultado_aprendizaje,error:t(d).errors.resultado_aprendizaje},null,8,["modelValue","placeholder","error"]),o(S,{class:"mt-2",message:t(d).errors.resultado_aprendizaje},null,8,["message"])]),e("div",null,[o($,{for:"descripcion",value:a.lang().label.descripcion},null,8,["value"]),o(j,{id:"descripcion",type:"text",class:"mt-1 block w-full",modelValue:t(d).descripcion,"onUpdate:modelValue":u[4]||(u[4]=c=>t(d).descripcion=c),required:"",placeholder:a.lang().placeholder.descripcion,error:t(d).errors.descripcion},null,8,["modelValue","placeholder","error"]),o(S,{class:"mt-2",message:t(d).errors.descripcion},null,8,["message"])])]),e("div",ce,[o(z,{disabled:t(d).processing,onClick:u[5]||(u[5]=c=>i("close"))},{default:h(()=>[b(r(a.lang().button.close),1)]),_:1},8,["disabled"]),o(E,{class:P(["ml-3",{"opacity-25":t(d).processing}]),disabled:t(d).processing,onClick:s},{default:h(()=>[b(r(t(d).processing?a.lang().button.add+"...":a.lang().button.add),1)]),_:1},8,["class","disabled"])])],40,ne)]),_:1},8,["show"])]))}},ue={class:"space-y-6"},me=["onSubmit"],be={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},ye={class:"my-6 grid grid-cols-2 gap-6"},fe={class:"flex justify-end"},he={__name:"Edit",props:{show:Boolean,title:String,articulo:Object,UnidadsSelect:Object},emits:["close"],setup(v,{emit:i}){const m=v,y=D({multipleSelect:!1}),s=U({...Object.fromEntries(["nombre","descripcion","unidad_id","enum","resultado_aprendizaje"].map(c=>[c,""]))}),a=[{idd:"enum",label:"enumTema",type:"number",value:s.enum},{idd:"nombre",label:"nombre",type:"text",value:s.nombre},{idd:"resultado_aprendizaje",label:"resultado_aprendizaje",type:"text",value:s.resultado_aprendizaje},{idd:"descripcion",label:"descripcion",type:"text",value:s.descripcion}],u=()=>{var c;s.put(route("articulo.update",(c=m.articulo)==null?void 0:c.id),{preserveScroll:!0,onSuccess:()=>{i("close"),s.reset(),y.multipleSelect=!1},onError:()=>null,onFinish:()=>null})};return N(()=>{var c,p,l,k;m.show&&(s.errors={},s.nombre=(c=m.articulo)==null?void 0:c.nombre,s.descripcion=(p=m.articulo)==null?void 0:p.descripcion,s.unidad_id=(l=m.articulo)==null?void 0:l.unidad_id,s.enum=(k=m.articulo)==null?void 0:k.enum)}),(c,p)=>(f(),w("section",ue,[o(B,{show:m.show,onClose:p[2]||(p[2]=l=>i("close"))},{default:h(()=>[e("form",{class:"p-6",onSubmit:I(u,["prevent"])},[e("h2",be,r(c.lang().label.edit)+" "+r(m.title),1),e("div",ye,[e("div",null,[o($,{for:"unidad_id",value:c.lang().label.Unidad},null,8,["value"]),o(q,{name:"unidad_id",class:"mt-1 block w-full",modelValue:t(s).unidad_id,"onUpdate:modelValue":p[0]||(p[0]=l=>t(s).unidad_id=l),required:"",dataSet:v.UnidadsSelect},null,8,["modelValue","dataSet"]),o(S,{class:"mt-2",message:t(s).errors.unidad_id},null,8,["message"])]),(f(),w(R,null,M(a,(l,k)=>e("div",{key:k},[o($,{for:l.label,value:c.lang().label[l.label]},null,8,["for","value"]),o(j,{id:l.idd,type:l.type,class:"mt-1 block w-full",modelValue:t(s)[l.idd],"onUpdate:modelValue":n=>t(s)[l.idd]=n,required:"",placeholder:l.label,error:t(s).errors[l.idd]},null,8,["id","type","modelValue","onUpdate:modelValue","placeholder","error"])])),64))]),e("div",fe,[o(z,{disabled:t(s).processing,onClick:p[1]||(p[1]=l=>i("close"))},{default:h(()=>[b(r(c.lang().button.close),1)]),_:1},8,["disabled"]),o(E,{class:P(["ml-3",{"opacity-25":t(s).processing}]),disabled:t(s).processing,onClick:u},{default:h(()=>[b(r(t(s).processing?c.lang().button.save+"...":c.lang().button.save),1)]),_:1},8,["class","disabled"])])],40,me)]),_:1},8,["show"])]))}},ge={class:"space-y-6"},ve=["onSubmit"],_e={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},ke={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},we={class:"mt-6 flex justify-end"},xe={__name:"Delete",props:{show:Boolean,title:String,articulo:Object},emits:["close"],setup(v,{emit:i}){const m=v,y=U({}),d=()=>{var s;y.delete(route("articulo.destroy",(s=m.articulo)==null?void 0:s.id),{preserveScroll:!0,onSuccess:()=>{i("close"),y.reset()},onError:()=>null,onFinish:()=>null})};return(s,a)=>(f(),w("section",ge,[o(B,{show:m.show,onClose:a[1]||(a[1]=u=>i("close")),maxWidth:"lg"},{default:h(()=>{var u;return[e("form",{class:"p-6",onSubmit:I(d,["prevent"])},[e("h2",_e,r(s.lang().label.delete)+" "+r(m.title),1),e("p",ke,[b(r(s.lang().label.delete_confirm)+" ",1),e("b",null,r((u=m.articulo)==null?void 0:u.nombre)+" !?",1)]),e("div",we,[o(z,{disabled:t(y).processing,onClick:a[0]||(a[0]=c=>i("close"))},{default:h(()=>[b(r(s.lang().button.close),1)]),_:1},8,["disabled"]),o(O,{class:P(["ml-3",{"opacity-25":t(y).processing}]),disabled:t(y).processing,onClick:d},{default:h(()=>[b(r(t(y).processing?s.lang().button.delete+"...":s.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,ve)]}),_:1},8,["show"])]))}},$e={class:"space-y-6"},Ce=["onSubmit"],Se={class:"text-lg font-medium text-gray-900 dark:text-gray-100"},je={class:"mt-1 text-sm text-gray-600 dark:text-gray-400"},Ve={class:"mt-6 flex justify-end"},Oe={__name:"DeleteBulk",props:{show:Boolean,title:String,selectedId:Object},emits:["close"],setup(v,{emit:i}){const m=v,y=U({id:[]}),d=()=>{y.post(route("articulo.destroy-bulk"),{preserveScroll:!0,onSuccess:()=>{i("close"),y.reset()},onError:()=>null,onFinish:()=>null})};return N(()=>{m.show&&(y.id=m.selectedId)}),(s,a)=>(f(),w("section",$e,[o(B,{show:m.show,onClose:a[1]||(a[1]=u=>i("close")),maxWidth:"lg"},{default:h(()=>{var u;return[e("form",{class:"p-6",onSubmit:I(d,["prevent"])},[e("h2",Se,r(s.lang().label.delete)+" "+r(m.title),1),e("p",je,r(s.lang().label.delete_confirm)+" "+r((u=m.selectedId)==null?void 0:u.length)+" "+r(m.title)+"? ",1),e("div",Ve,[o(z,{disabled:t(y).processing,onClick:a[0]||(a[0]=c=>i("close"))},{default:h(()=>[b(r(s.lang().button.close),1)]),_:1},8,["disabled"]),o(O,{class:P(["ml-3",{"opacity-25":t(y).processing}]),disabled:t(y).processing,onClick:d},{default:h(()=>[b(r(t(y).processing?s.lang().button.delete+"...":s.lang().button.delete),1)]),_:1},8,["class","disabled"])])],40,Ce)]}),_:1},8,["show"])]))}},Ue={class:"space-y-4"},Ie={class:"px-4 sm:px-0"},Pe={class:"rounded-lg overflow-hidden w-fit"},ze={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},Be={class:"flex justify-between p-2"},Ae={class:"flex space-x-2"},Re={class:"overflow-x-auto scrollbar-table"},De={class:"w-full"},Ne={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},Ee={class:"dark:bg-gray-900 text-left"},qe={class:"px-2 py-4 text-center"},Fe={key:0,class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},Te={class:"flex justify-between items-center"},Me=e("span",null," Acciones ",-1),Le={class:"flex justify-between items-center"},Je=e("span",null," Nick ",-1),We={class:"flex justify-between items-center"},He={class:"flex justify-between items-center"},Xe={class:"flex justify-between items-center"},Ge={class:"flex justify-between items-center"},Ke={class:"flex justify-between items-center"},Qe={class:"flex justify-between items-center"},Ye={class:"flex justify-between items-center"},Ze={class:"flex justify-between items-center"},es={class:"flex justify-between items-center"},ss={class:"flex justify-between items-center"},ts={class:"flex justify-between items-center"},ls={class:"flex justify-between items-center"},os={class:"flex justify-between items-center"},rs={class:"flex justify-between items-center"},ns={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},as=["value"],ds={key:0,class:"whitespace-nowrap py-4 px-2 sm:py-3"},is={class:"flex justify-start items-center"},cs={class:"rounded-md overflow-hidden"},ps={class:"whitespace-nowrap py-4 px-2 sm:py-3"},us={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ms={class:"whitespace-nowrap py-4 px-2 sm:py-3"},bs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ys={class:"whitespace-nowrap py-4 px-2 sm:py-3"},fs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},hs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},gs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},vs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},_s={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ks={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ws={class:"whitespace-nowrap py-4 px-2 sm:py-3"},xs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},$s={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Cs={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ss={class:"flex justify-betwween items-center p-2 border-t border-gray-200 dark:border-gray-700"},Ls={__name:"Index",props:{title:String,filters:Object,breadcrumbs:Object,perPage:Number,fromController:Object,HijoSelec:Object,numberPermissions:Number},setup(v){const i=v,{_:m,debounce:y,pickBy:d}=G,s=D({params:{search:i.filters.search,field:i.filters.field,order:i.filters.order,perPage:i.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,dataSet:J().props.app.perpage}),a=p=>{console.log("🧈 debu field:",p),s.params.field=p.replace(/ /g,"_"),s.params.order=s.params.order==="asc"?"desc":"asc"};W(()=>m.cloneDeep(s.params),y(()=>{let p=d(s.params);H.get(route("Articulo.index"),p,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const u=p=>{var l;p.target.checked===!1?s.selectedId=[]:(l=i.fromController)==null||l.data.forEach(k=>{s.selectedId.push(k.id)})},c=()=>{var p;((p=i.fromController)==null?void 0:p.data.length)==s.selectedId.length?s.multipleSelect=!0:s.multipleSelect=!1};return X(()=>{}),(p,l)=>{const k=Y("tooltip");return f(),w(R,null,[o(t(K),{title:i.title},null,8,["title"]),o(Z,null,{default:h(()=>[o(ee,{title:v.title,breadcrumbs:v.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",Ue,[e("div",Ie,[e("div",Pe,[V(o(E,{class:"rounded-none",onClick:l[0]||(l[0]=n=>s.createOpen=!0)},{default:h(()=>[b(r(p.lang().button.add),1)]),_:1},512),[[F,p.can(["create articulo"])]]),p.can(["create articulo"])?(f(),x(pe,{key:0,show:s.createOpen,onClose:l[1]||(l[1]=n=>s.createOpen=!1),title:i.title,UnidadsSelect:s.UnidadsSelect},null,8,["show","title","UnidadsSelect"])):C("",!0),p.can(["update articulo"])?(f(),x(he,{key:1,show:s.editOpen,onClose:l[2]||(l[2]=n=>s.editOpen=!1),articulo:s.generico,title:i.title,UnidadsSelect:s.UnidadsSelect},null,8,["show","articulo","title","UnidadsSelect"])):C("",!0),p.can(["delete articulo"])?(f(),x(xe,{key:2,show:s.deleteOpen,onClose:l[3]||(l[3]=n=>s.deleteOpen=!1),articulo:s.generico,title:i.title},null,8,["show","articulo","title"])):C("",!0),o(Oe,{show:s.deleteBulkOpen,onClose:l[4]||(l[4]=n=>(s.deleteBulkOpen=!1,s.multipleSelect=!1,s.selectedId=[])),selectedId:s.selectedId,title:i.title},null,8,["show","selectedId","title"])])]),e("div",ze,[e("div",Be,[e("div",Ae,[o(q,{modelValue:s.params.perPage,"onUpdate:modelValue":l[5]||(l[5]=n=>s.params.perPage=n),dataSet:s.dataSet},null,8,["modelValue","dataSet"]),V((f(),x(O,{onClick:l[6]||(l[6]=n=>s.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:h(()=>[o(t(T),{class:"w-5 h-5"})]),_:1})),[[F,s.selectedId.length!=0],[k,p.lang().tooltip.delete_selected]])]),i.numberPermissions>1?(f(),x(j,{key:0,modelValue:s.params.search,"onUpdate:modelValue":l[7]||(l[7]=n=>s.params.search=n),type:"text",class:"block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg",placeholder:p.lang().placeholder.search},null,8,["modelValue","placeholder"])):C("",!0)]),e("div",Re,[e("table",De,[e("thead",Ne,[e("tr",Ee,[e("th",qe,[o(le,{checked:s.multipleSelect,"onUpdate:checked":l[8]||(l[8]=n=>s.multipleSelect=n),onChange:u},null,8,["checked"])]),i.numberPermissions>1?(f(),w("th",Fe,[e("div",Te,[Me,o(t(g),{class:"w-4 h-4"})])])):C("",!0),e("th",{onClick:l[9]||(l[9]=n=>a("nick")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Le,[Je,b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[10]||(l[10]=n=>a("version")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",We,[e("span",null,r(t(_)("version")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[11]||(l[11]=n=>a("Portada")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",He,[e("span",null,r(t(_)("Portada")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[12]||(l[12]=n=>a("Resumen")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Xe,[e("span",null,r(t(_)("Resumen")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[13]||(l[13]=n=>a("Palabras_Clave")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Ge,[e("span",null,r(t(_)("Palabras_Clave")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[14]||(l[14]=n=>a("Introduccion")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Ke,[e("span",null,r(t(_)("Introduccion")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[15]||(l[15]=n=>a("Revision_de_la_Literatura")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Qe,[e("span",null,r(t(_)("Revision_de_la_Literatura")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[16]||(l[16]=n=>a("Metodologia")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Ye,[e("span",null,r(t(_)("Metodologia")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[17]||(l[17]=n=>a("Resultados")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",Ze,[e("span",null,r(t(_)("Resultados")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[18]||(l[18]=n=>a("Discusion")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",es,[e("span",null,r(t(_)("Discusion")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[19]||(l[19]=n=>a("Conclusiones")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",ss,[e("span",null,r(t(_)("Conclusiones")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[20]||(l[20]=n=>a("Agradecimientos")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",ts,[e("span",null,r(t(_)("Agradecimientos")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[21]||(l[21]=n=>a("Referencias")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",ls,[e("span",null,r(t(_)("Referencias")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[22]||(l[22]=n=>a("Anexos_o_Apendices")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",os,[e("span",null,r(t(_)("Anexos_o_Apendices")),1),b(),o(t(g),{class:"w-4 h-4"})])]),e("th",{onClick:l[23]||(l[23]=n=>a("user_id")),class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",rs,[e("span",null,r(t(_)("Autor")),1),b(),o(t(g),{class:"w-4 h-4"})])])])]),e("tbody",null,[(f(!0),w(R,null,M(v.fromController.data,(n,L)=>(f(),w("tr",{key:L,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[e("td",ns,[V(e("input",{type:"checkbox",onChange:c,value:n.id,"onUpdate:modelValue":l[24]||(l[24]=A=>s.selectedId=A),class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary"},null,40,as),[[Q,s.selectedId]])]),i.numberPermissions>1?(f(),w("td",ds,[e("div",is,[e("div",cs,[V((f(),x(oe,{type:"button",onClick:A=>(s.editOpen=!0,s.generico=n),class:"px-2 py-1.5 rounded-none"},{default:h(()=>[o(t(te),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[k,p.lang().tooltip.edit]]),V((f(),x(O,{type:"button",onClick:A=>(s.deleteOpen=!0,s.generico=n),class:"px-2 py-1.5 rounded-none"},{default:h(()=>[o(t(T),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[k,p.lang().tooltip.delete]])])])])):C("",!0),e("td",ps,r(n.nick),1),e("td",us,r(n.version),1),e("td",ms,r(n.Portada),1),e("td",bs,r(n.Resumen),1),e("td",ys,r(n.Palabras_Clave),1),e("td",fs,r(n.Introduccion),1),e("td",hs,r(n.Revision_de_la_Literatura),1),e("td",gs,r(n.Metodologia),1),e("td",vs,r(n.Resultados),1),e("td",_s,r(n.Discusion),1),e("td",ks,r(n.Conclusiones),1),e("td",ws,r(n.Agradecimientos),1),e("td",xs,r(n.Referencias),1),e("td",$s,r(n.Anexos_o_Apendices),1),e("td",Cs,r(n.user_id),1)]))),128))])])]),e("div",Ss,[o(se,{links:i.fromController,filters:s.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{Ls as default};
