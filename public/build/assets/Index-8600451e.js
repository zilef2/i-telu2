import{r as x,a as k,O,c as m,b as o,u as n,f as u,F as _,x as C,k as c,X as S,d as e,m as p,y as f,h as $,t as d,s as j,bJ as b,B}from"./app-8190fc74.js";import{_ as D}from"./AuthenticatedLayout-d2a8c51d.js";import{_ as I}from"./Breadcrumb-31fbe063.js";import{P as N}from"./index-c7a22da3.js";import P from"./Create-987cfd19.js";import T from"./Edit-620ae775.js";import E from"./Delete-6d8cef35.js";import{_ as F}from"./InfoButton-c8303f2a.js";import"./index-d56bdc70.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./Toast-095c7b20.js";import"./InputError-1b18cc94.js";import"./InputLabel-668bb308.js";import"./Modal-41b4ce14.js";import"./PrimaryButton-ea2d3d80.js";import"./TextInput-100d83c4.js";import"./SelectInput-1ca4357c.js";import"./DangerButton-7b937436.js";const V={class:"space-y-4"},A={class:"px-4 sm:px-0"},J={class:"rounded-lg overflow-hidden w-fit"},L={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},X=e("div",{class:"flex justify-between p-2"},[e("div",{class:"flex space-x-2"})],-1),q={class:"overflow-x-auto scrollbar-table"},z={class:"w-full"},G={class:"uppercase text-sm border-t border-gray-200 dark:border-gray-700"},H={class:"dark:bg-gray-900 text-left"},K={class:"flex justify-between items-center"},M={class:"border-t border-gray-200 dark:border-gray-700 hover:bg-gray-200/30 hover:dark:bg-gray-900/20"},Q={class:"whitespace-nowrap py-4 px-2 sm:py-3"},R={class:"flex justify-start items-center"},U={class:"rounded-md overflow-hidden"},W={class:"whitespace-nowrap py-4 px-2 sm:py-3 flex-wrap"},Y={class:"whitespace-nowrap py-4 px-2 sm:py-3"},Z={class:"whitespace-nowrap py-4 px-2 sm:py-3"},ve={__name:"Index",props:{title:String,filters:Object,breadcrumbs:Object,fromController:Object,nombresTabla:Array},setup(i){const t=i,{_:h,debounce:v,pickBy:y}=C;console.log("🧈 debu fromController:",t.fromController);const r=x({params:{search:t.filters.search,field:t.filters.field,order:t.filters.order},selectedId:[],createOpen:!1,editOpen:!1,deleteOpen:!1,generico:null});return console.log(t.fromController),k(()=>h.cloneDeep(r.params),v(()=>{let a=y(r.params);O.get(route("parametro.index"),a,{replace:!0,preserveState:!0,preserveScroll:!0})},150)),(a,s)=>{const w=B("tooltip");return c(),m(_,null,[o(n(S),{title:t.title},null,8,["title"]),o(D,null,{default:u(()=>[o(I,{title:i.title,breadcrumbs:i.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",V,[e("div",A,[e("div",J,[p(o(P,{show:r.createOpen,onClose:s[0]||(s[0]=l=>r.createOpen=!1),title:t.title,parametrosSelect:r.parametrosSelect},null,8,["show","title","parametrosSelect"]),[[f,a.can(["create parametro"])]]),p(o(T,{show:r.editOpen,onClose:s[1]||(s[1]=l=>r.editOpen=!1),parametrous:t.fromController,title:t.title,parametrosSelect:r.parametrosSelect},null,8,["show","parametrous","title","parametrosSelect"]),[[f,a.can(["edit parametro"])]]),p(o(E,{show:r.deleteOpen,onClose:s[2]||(s[2]=l=>r.deleteOpen=!1),parametro:t.fromController,title:t.title},null,8,["show","parametro","title"]),[[f,a.can(["delete parametro"])]])])]),e("div",L,[X,e("div",q,[e("table",z,[e("thead",G,[e("tr",H,[(c(!0),m(_,null,$(i.nombresTabla[0],(l,g)=>(c(),m("th",{key:g,class:"px-2 py-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800"},[e("div",K,[e("span",null,d(l),1)])]))),128))])]),e("tbody",null,[e("tr",M,[e("td",Q,[e("div",R,[e("div",U,[p((c(),j(F,{type:"button",onClick:s[3]||(s[3]=l=>r.editOpen=!0),class:"px-2 py-1.5 rounded-none"},{default:u(()=>[o(n(N),{class:"w-4 h-4"})]),_:1})),[[w,a.lang().tooltip.edit]])])])]),e("td",W,d(n(b)(t.fromController.prompEjercicios,50)),1),e("td",Y,d(n(b)(t.fromController.prompObjetivos,50)),1),e("td",Z,d(t.fromController.NumeroTicketDefecto),1)])])])])])])]),_:1})],64)}}};export{ve as default};
