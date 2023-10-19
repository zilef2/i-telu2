import{r as j,J as B,a as P,O as D,c as m,b as r,u as p,f,F as v,x as N,k as i,X as U,d as s,m as c,y,l as b,t as n,s as _,g as A,h as T,A as E,B as F}from"./app-e4aec783.js";import{_ as G}from"./AuthenticatedLayout-262f04fb.js";import{_ as J}from"./Breadcrumb-3bf15452.js";import{_ as L}from"./TextInput-0d3578f0.js";import{_ as M}from"./PrimaryButton-62c6067b.js";import{_ as X}from"./InfoButton-fdfad07d.js";import{_ as q}from"./SelectInput-e07a183c.js";import{_ as x}from"./DangerButton-b3b2af7b.js";import{_ as z}from"./Pagination-461d5218.js";import{T as $,a as w,P as H}from"./index-1d64f8a7.js";import K from"./Create-2d9342b5.js";import Q from"./Edit-26a6e3d7.js";import R from"./Delete-a507ea8c.js";import W from"./DeleteBulk-646e0093.js";import{_ as Y}from"./Checkbox-e21ec82f.js";import Z from"./Permission-25bea118.js";import"./index-c5c5be5a.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./Toast-501ce206.js";import"./InputError-ff01e7fb.js";import"./InputLabel-776359c0.js";import"./Modal-d321b06e.js";const ee={class:"space-y-4"},se={class:"px-4 sm:px-0"},te={class:"rounded-lg overflow-hidden w-fit"},le={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},oe={class:"flex justify-between p-2"},re={class:"flex space-x-2"},ae={class:"overflow-x-auto scrollbar-table"},ne={class:"w-full"},ie={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},de={class:"dark:bg-gray-900/50 text-left"},pe={class:"px-2 py-4 text-center"},ce=s("th",{class:"px-2 py-4 text-center"},"#",-1),me={class:"flex justify-between items-center"},ue={class:"flex justify-between items-center"},fe=s("span",null,"Guard",-1),he={class:"px-2 py-4"},ye={class:"flex justify-between items-center"},_e=s("th",{class:"px-2 py-4 sr-only"},"Action",-1),ge={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},be=["value"],we={class:"whitespace-nowrap py-4 px-2 sm:py-3 text-center"},ke={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ve={class:"whitespace-nowrap py-4 px-2 sm:py-3"},xe=["onClick"],$e=["onClick"],Oe={key:2,class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ce={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Ie={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Se={class:"flex justify-center items-center"},Ve={class:"rounded-md overflow-hidden"},je={class:"flex justify-between items-center p-2 border-t border-gray-200 dark:border-gray-700"},Ze={__name:"Index",props:{title:String,filters:Object,roles:Object,permissions:Object,breadcrumbs:Object,perPage:Number},setup(h){const a=h,{_:O,debounce:C,pickBy:I}=N,e=j({params:{search:a.filters.search,field:a.filters.field,order:a.filters.order,perPage:a.perPage},selectedId:[],multipleSelect:!1,createOpen:!1,editOpen:!1,deleteOpen:!1,deleteBulkOpen:!1,permissionOpen:!1,role:null,dataSet:B().props.app.perpage}),g=o=>{e.params.field=o,e.params.order=e.params.order==="asc"?"desc":"asc"};P(()=>O.cloneDeep(e.params),C(()=>{let o=I(e.params);D.get(route("role.index"),o,{replace:!0,preserveState:!0,preserveScroll:!0})},150));const S=o=>{var t;o.target.checked===!1?e.selectedId=[]:(t=a.roles)==null||t.data.forEach(d=>{e.selectedId.push(d.id)})},V=()=>{var o;((o=a.roles)==null?void 0:o.data.length)==e.selectedId.length?e.multipleSelect=!0:e.multipleSelect=!1};return(o,t)=>{const d=F("tooltip");return i(),m(v,null,[r(p(U),{title:a.title},null,8,["title"]),r(G,null,{default:f(()=>[r(J,{title:h.title,breadcrumbs:h.breadcrumbs},null,8,["title","breadcrumbs"]),s("div",ee,[s("div",se,[s("div",te,[c(r(M,{class:"rounded-none",onClick:t[0]||(t[0]=l=>e.createOpen=!0)},{default:f(()=>[b(n(o.lang().button.add),1)]),_:1},512),[[y,o.can(["create role"])]]),r(K,{show:e.createOpen,onClose:t[1]||(t[1]=l=>e.createOpen=!1),permissions:a.permissions,title:a.title},null,8,["show","permissions","title"]),r(Q,{show:e.editOpen,onClose:t[2]||(t[2]=l=>e.editOpen=!1),role:e.role,permissions:a.permissions,title:a.title},null,8,["show","role","permissions","title"]),r(R,{show:e.deleteOpen,onClose:t[3]||(t[3]=l=>e.deleteOpen=!1),role:e.role,title:a.title},null,8,["show","role","title"]),r(W,{show:e.deleteBulkOpen,onClose:t[4]||(t[4]=l=>(e.deleteBulkOpen=!1,e.multipleSelect=!1,e.selectedId=[])),selectedId:e.selectedId,title:a.title},null,8,["show","selectedId","title"]),r(Z,{show:e.permissionOpen,onClose:t[5]||(t[5]=l=>e.permissionOpen=!1),role:e.role,title:a.title},null,8,["show","role","title"])])]),s("div",le,[s("div",oe,[s("div",re,[r(q,{modelValue:e.params.perPage,"onUpdate:modelValue":t[6]||(t[6]=l=>e.params.perPage=l),dataSet:e.dataSet},null,8,["modelValue","dataSet"]),c((i(),_(x,{onClick:t[7]||(t[7]=l=>e.deleteBulkOpen=!0),class:"px-3 py-1.5"},{default:f(()=>[r(p($),{class:"w-5 h-5"})]),_:1})),[[y,e.selectedId.length!=0&&o.can(["delete role"])],[d,o.lang().tooltip.delete_selected]])]),a.numberPermissions>1?(i(),_(L,{key:0,modelValue:e.params.search,"onUpdate:modelValue":t[8]||(t[8]=l=>e.params.search=l),type:"text",class:"block w-3/6 md:w-2/6 lg:w-1/6 rounded-lg",placeholder:o.lang().placeholder.search},null,8,["modelValue","placeholder"])):A("",!0)]),s("div",ae,[s("table",ne,[s("thead",ie,[s("tr",de,[s("th",pe,[r(Y,{checked:e.multipleSelect,"onUpdate:checked":t[9]||(t[9]=l=>e.multipleSelect=l),onChange:S},null,8,["checked"])]),ce,s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[10]||(t[10]=l=>g("name"))},[s("div",me,[s("span",null,n(o.lang().label.name),1),r(p(w),{class:"w-4 h-4"})])]),s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[11]||(t[11]=l=>g("email"))},[s("div",ue,[fe,r(p(w),{class:"w-4 h-4"})])]),s("th",he,n(o.lang().label.permission),1),s("th",{class:"px-2 py-4 cursor-pointer",onClick:t[12]||(t[12]=l=>g("updated_at"))},[s("div",ye,[s("span",null,n(o.lang().label.updated),1),r(p(w),{class:"w-4 h-4"})])]),_e])]),s("tbody",null,[(i(!0),m(v,null,T(h.roles.data,(l,k)=>(i(),m("tr",{key:k,class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},[s("td",ge,[c(s("input",{class:"rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-gray-800 dark:checked:bg-primary dark:checked:border-primary",type:"checkbox",onChange:V,value:l.id,"onUpdate:modelValue":t[13]||(t[13]=u=>e.selectedId=u)},null,40,be),[[E,e.selectedId]])]),s("td",we,n(++k),1),s("td",ke,n(l.name.replace(/_/g," ")),1),s("td",ve,n(l.guard_name),1),l.permissions.length==a.permissions.length?c((i(),m("td",{key:0,onClick:u=>(e.permissionOpen=!0,e.role=l),class:"whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline"},[b(n(o.lang().label.all_permission),1)],8,xe)),[[d,o.lang().tooltip.detail]]):l.permissions.length!=0?c((i(),m("td",{key:1,onClick:u=>(e.permissionOpen=!0,e.role=l),class:"whitespace-nowrap py-4 px-2 sm:py-3 cursor-pointer text-blue-600 dark:text-blue-400 font-bold underline"},[b(n(l.permissions.length)+" "+n(o.lang().label.permission),1)],8,$e)),[[d,o.lang().tooltip.detail]]):(i(),m("td",Oe,n(o.lang().label.no_permission),1)),s("td",Ce,n(l.updated_at),1),s("td",Ie,[s("div",Se,[s("div",Ve,[c((i(),_(X,{type:"button",onClick:u=>(e.editOpen=!0,e.role=l),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[r(p(H),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[y,o.can(["update role"])],[d,o.lang().tooltip.edit]]),c((i(),_(x,{type:"button",onClick:u=>(e.deleteOpen=!0,e.role=l),class:"px-2 py-1.5 rounded-none"},{default:f(()=>[r(p($),{class:"w-4 h-4"})]),_:2},1032,["onClick"])),[[y,o.can(["delete role"])],[d,o.lang().tooltip.delete]])])])])]))),128))])])]),s("div",je,[r(z,{links:a.roles,filters:e.params},null,8,["links","filters"])])])])]),_:1})],64)}}};export{Ze as default};
