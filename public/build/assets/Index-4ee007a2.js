import{k as N,J as P,l as L,O as M,v as T,m as D,f as y,a as r,u as i,w as u,F as k,p as U,o as d,X as z,b as e,g as w,q as m,s as f,d as v,t as n,c as h,h as A,x as H,n as E,B as F,y as q}from"./app-fa012e40.js";import{_ as J}from"./AuthenticatedLayout-e4269cae.js";import{_ as X}from"./Breadcrumb-a1f90ab0.js";import{_ as G}from"./TextInput-a37ed436.js";import{_ as K}from"./PrimaryButton-f0e54c8a.js";import{_ as Q}from"./InfoButton-44b47ffa.js";import{_ as R}from"./SelectInput-d6ecf7ad.js";import{_ as C}from"./DangerButton-83083e16.js";import{_ as W}from"./Pagination-2b288850.js";import{l as Y,T as $,p as Z,a as p,b as ee,P as se}from"./index-3cb9eb1b.js";import te from"./Create-1d0e065e.js";import le from"./Edit-4f09a975.js";import ae from"./Delete-174adb12.js";import re from"./DeleteBulk-405418fb.js";import{_ as oe}from"./Checkbox-43286906.js";import{C as ne}from"./global-f5954534.js";import"./index-5d8dae98.js";import"./InputError-bab8a326.js";import"./InputLabel-0db2c892.js";import"./SecondaryButton-8bd0ef43.js";import"./vue-datepicker-2655f816.js";/* empty css             */const ie={class:"space-y-4"},de={key:0,class:"px-2 sm:px-0"},ce={class:"rounded-lg overflow-hidden w-fit"},pe={class:"flex max-w-screen-xl shadow-lg rounded-lg"},me=e("div",{class:"bg-yellow-600 py-4 px-2 rounded-l-lg flex items-center"},[e("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16",class:"fill-current text-white",width:"20",height:"20"},[e("path",{"fill-rule":"evenodd",d:"M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z"})])],-1),ue={class:"px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200"},fe=["innerHTML"],he={key:1,class:"px-2 sm:px-0"},_e={class:"rounded-lg overflow-hidden w-fit"},ye={class:"flex max-w-screen-xl shadow-lg rounded-lg"},we={class:"bg-white py-4 px-2 rounded-l-lg flex items-center"},ge={class:"px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200"},be=["innerHTML"],xe={class:"px-4 sm:px-0"},ve={class:"rounded-lg overflow-hidden w-fit"},ke={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},Ce={class:"flex justify-between p-2"},$e={class:"flex space-x-2"},Oe={class:"overflow-x-auto scrollbar-table"},je={class:"w-full"},Ie={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},Se={class:"dark:bg-gray-900/50 text-left"},Be={class:"px-2 py-4 text-center"},Ve=e("th",{class:"px-2 py-4 text-center"},"#",-1),Ne={class:"flex justify-between items-center"},Pe={class:"px-2 py-4"},Le={class:"flex justify-between items-center"},Me={class:"flex justify-between items-center"},Te={class:"flex justify-between items-center"},De={class:"flex justify-between items-center"},Ue={class:"flex justify-between items-center"},ze={class:"flex justify-between items-center"},Ae={class:"flex justify-between items-center"},He={class:"flex justify-between items-center"},Ee=e("th",{class:"px-2 py-4"},"Accion",-1),Fe={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},qe=["value"],Je={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},Xe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ge={class:"flex justify-start items-center"},Ke={class:"flex justify-start items-center text-sm text-gray-600"},Qe={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Re={class:"whitespace-nowrap py-4 px-2 sm:py-3"},We={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ye={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},Ze={class:"whitespace-nowrap py-4 px-2 sm:py-3"},es={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},ss={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},ts={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},ls={class:"whitespace-nowrap text-center py-4 px-2 sm:py-3"},as={class:"whitespace-nowrap py-4 px-2 sm:py-3"},rs={class:"flex justify-center items-center"},os={class:"rounded-md overflow-hidden"},ns={class:"flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700"},Bs={__name:"Index",props:{title:String,filters:Object,users:Object,roles:Object,breadcrumbs:Object,perPage:Number,numberPermissions:Number,flash:Object},setup(g){const o=g,{_:O,debounce:j,pickBy:I}=U,s=N({params:{search:o.filters.search,field:o.filters.field,order:o.filters.order,perPage:o.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,user:null,ArchivoNombre:"",dataSet:P().props.app.perpage}),c=a=>{s.params.field=a,console.log("🧈 debu data.params.field:",s.params.field),s.params.order=s.params.order==="asc"?"desc":"asc",console.log("🧈 debu data.params.order:",s.params.order)};L(()=>O.cloneDeep(s.params),j(()=>{let a=I(s.params);M.get(route("user.index"),a,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const S=a=>{var t;a.target.checked===!1?s.selectedId=[]:(t=o.users)==null||t.data.forEach(_=>{s.selectedId.push(_.id)})},B=()=>{var a;((a=o.users)==null?void 0:a.data.length)==s.selectedId.length?s.multipleSelect=!0:s.multipleSelect=!1},V=T({archivo1:""});return D(()=>{var a;s.ArchivoNombre=(a=V.archivo1)==null?void 0:a.name}),(a,t)=>{const _=q("tooltip");return d(),y(k,null,[r(i(z),{title:o.title},null,8,["title"]),r(J,null,{default:u(()=>[r(X,{title:g.title,breadcrumbs:g.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",ie,[o.flash.warning?(d(),y("div",de,[e("div",ce,[e("div",pe,[me,e("div",ue,[e("p",{innerHTML:o==null?void 0:o.flash.warning},null,8,fe)])])])])):w("",!0),o.flash.success?(d(),y("div",he,[e("div",_e,[e("div",ye,[e("div",we,[r(i(Y),{class:"ml-4 w-16 h-16 text-primary dark:text-white bg-white"})]),e("div",ge,[e("p",{innerHTML:o==null?void 0:o.flash.success},null,8,be)])])])])):w("",!0),e("div",xe,[e("div",ve,[m(r(K,{class:"rounded-none",onClick:t[0]||(t[0]=l=>s.createOpen=!0)},{default:u(()=>[v(n(a.lang().button.add),1)]),_:1},512),[[f,a.can(["create user"])]]),a.can(["create user"])?(d(),h(te,{key:0,show:s.createOpen,onClose:t[1]||(t[1]=l=>s.createOpen=!1),roles:o.roles,title:o.title},null,8,["show","roles","title"])):w("",!0),a.can(["update user"])?(d(),h(le,{key:1,show:s.editOpen,onClose:t[2]||(t[2]=l=>s.editOpen=!1),user:s.user,roles:o.roles,title:o.title},null,8,["show","user","roles","title"])):w("",!0),r(ae,{show:s.deleteOpen,onClose:t[3]||(t[3]=l=>s.deleteOpen=!1),user:s.user,title:o.title},null,8,["show","user","title"]),r(re,{show:s.deleteBulkOpen,onClose:t[4]||(t[4]=l=>(s.deleteBulkOpen=!1,s.multipleSelect=!1,s.selectedId=[])),selectedId:s.selectedId,title:o.title},null,8,["show","selectedId","title"])])]),e("div",ke,[e("div",Ce,[e("div",$e,[r(R,{modelValue:s.params.perPage,"onUpdate:modelValue":t[5]||(t[5]=l=>s.params.perPage=l),dataSet:s.dataSet},null,8,["modelValue","dataSet"]),m((d(),h(C,{onClick:t[6]||(t[6]=l=>s.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:u(()=>[r(i($),{class:"w-5 h-5"})]),_:1})),[[f,s.selectedId.length!=0&&a.can(["delete user"])],[_,a.lang().tooltip.delete_selected]]),m(r(i(A),{href:a.route("subirexceles"),type:"button",class:"px-2 -mb-1.5 py-1.5 rounded-none hover:bg-blue-500"},{default:u(()=>[r(i(Z),{class:"w-9 h-9"}),v("Subir excel ")]),_:1},8,["href"]),[[f,a.can(["create user"])]])]),o.numberPermissions>1?(d(),h(G,{key:0,modelValue:s.params.search,"onUpdate:modelValue":t[7]||(t[7]=l=>s.params.search=l),type:"text",class:"block w-4/6 md:w-3/6 lg:w-2/6 rounded-lg",placeholder:"Nombre, correo, nivel o ID "},null,8,["modelValue"])):w("",!0)]),e("div",Oe,[e("table",je,[e("thead",Ie,[e("tr",Se,[e("th",Be,[r(oe,{checked:s.multipleSelect,"onUpdate:checked":t[8]||(t[8]=l=>s.multipleSelect=l),onChange:S},null,8,["checked"])]),Ve,e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[9]||(t[9]=l=>c("name"))},[e("div",Ne,[e("span",null,n(a.lang().label.name),1),r(i(p),{class:"w-4 h-4"})])]),e("th",Pe,n(a.lang().label.role),1),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[10]||(t[10]=l=>c("identificacion"))},[e("div",Le,[e("span",null,n(a.lang().label.identificacion),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[11]||(t[11]=l=>c("sexo"))},[e("div",Me,[e("span",null,n(a.lang().label.sexo),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[12]||(t[12]=l=>c("fecha_nacimiento"))},[e("div",Te,[e("span",null,n(a.lang().label.edad),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[13]||(t[13]=l=>c("pgrado"))},[e("div",De,[e("span",null,n(a.lang().label.pgrado),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[14]||(t[14]=l=>c("semestre"))},[e("div",Ue,[e("span",null,n(a.lang().label.semestre),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[15]||(t[15]=l=>c("limite_token_general"))},[e("div",ze,[e("span",null,n(a.lang().label.limite_token_general),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[16]||(t[16]=l=>c("limite_token_leccion"))},[e("div",Ae,[e("span",null,n(a.lang().label.limite_token_lec),1),r(i(p),{class:"w-4 h-4"})])]),e("th",{class:"px-2 py-4 cursor-pointer",onClick:t[17]||(t[17]=l=>c("pgrado"))},[e("div",He,[e("span",null,n(a.lang().label.pgrado),1),r(i(p),{class:"w-4 h-4"})])]),Ee])]),e("tbody",null,[(d(!0),y(k,null,H(g.users.data,(l,b)=>(d(),y("tr",{key:b,class:E(["border-t border-gray-200 dark:border-gray-700 hover:bg-sky-100 hover:dark:bg-gray-900/20",b%2==0?"bg-gray-100 dark:bg-gray-800":""])},[e("td",Fe,[m(e("input",{class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",onChange:B,value:l.id,"onUpdate:modelValue":t[18]||(t[18]=x=>s.selectedId=x)},null,40,qe),[[F,s.selectedId]])]),e("td",Je,n(++b),1),e("td",Xe,[e("span",Ge,[v(n(l.name)+" ",1),m(r(i(ee),{class:"ml-[2px] w-4 h-4 text-primary dark:text-white"},null,512),[[f,l.email_verified_at]])]),e("span",Ke,n(l.email),1)]),e("td",Qe,n(l.roles.length==0?"not selected":l.roles[0].name),1),e("td",Re,n(l.identificacion),1),e("td",We,n(l.sexo),1),e("td",Ye,n(i(ne)(l.fecha_nacimiento)),1),e("td",Ze,n(l.pgrado),1),e("td",es,n(l.semestre),1),e("td",ss,n(l.limite_token_general),1),e("td",ts,n(l.limite_token_leccion),1),e("td",ls,n(l.pgrado),1),e("td",as,[e("div",rs,[e("div",os,[m((d(),h(Q,{type:"button",onClick:x=>(s.editOpen=!0,s.user=l),class:"px-2 py-1.5 rounded-none"},{default:u(()=>[r(i(se),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[f,a.can(["update user"])],[_,a.lang().tooltip.edit]]),m((d(),h(C,{type:"button",onClick:x=>(s.deleteOpen=!0,s.user=l),class:"px-2 py-1.5 rounded-none"},{default:u(()=>[r(i($),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[f,a.can(["delete user"])],[_,a.lang().tooltip.delete]])])])])],2))),128))])])]),e("div",ns,[r(W,{links:o.users,filters:s.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{Bs as default};
