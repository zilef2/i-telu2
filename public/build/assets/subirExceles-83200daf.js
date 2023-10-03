import{i as U,r as D,v as E,w as j,c as l,a as o,u as t,b as g,F as C,o as a,X as N,d as e,t as r,m as d,f as _,j as v,k as x,n as b,e as c,Q as w}from"./app-6467c706.js";import{_ as $}from"./AuthenticatedLayout-6da35bc8.js";import{_ as z}from"./Breadcrumb-26092690.js";import{_ as B}from"./SelectInput-a92191a9.js";import{_ as F}from"./InputError-0260cbdd.js";import{D as y}from"./index-b72a4a48.js";import"./vue-datepicker-d66207f8.js";/* empty css             */import V from"./SubirCarreras-f677e933.js";import{v as L}from"./global-8f2c086a.js";import"./index-23384e0f.js";const M={class:"space-y-4"},A={key:0,class:"px-2 sm:px-0"},O={class:"rounded-lg overflow-hidden w-fit"},q={class:"flex max-w-screen-xl shadow-lg rounded-lg"},X=e("div",{class:"bg-yellow-600 py-4 px-2 rounded-l-lg flex items-center"},[e("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 16 16",class:"fill-current text-white",width:"20",height:"20"},[e("path",{"fill-rule":"evenodd",d:"M8.22 1.754a.25.25 0 00-.44 0L1.698 13.132a.25.25 0 00.22.368h12.164a.25.25 0 00.22-.368L8.22 1.754zm-1.763-.707c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0114.082 15H1.918a1.75 1.75 0 01-1.543-2.575L6.457 1.047zM9 11a1 1 0 11-2 0 1 1 0 012 0zm-.25-5.25a.75.75 0 00-1.5 0v2.5a.75.75 0 001.5 0v-2.5z"})])],-1),H={class:"px-8 py-6 bg-white rounded-r-lg flex justify-between items-center w-full border border-l-transparent border-gray-200"},P={class:"relative bg-white dark:bg-gray-800 shadow sm:rounded-lg"},Q={class:"text-gray-600 body-font"},T={class:"container px-1 py-12 mx-auto"},G={key:0,class:"flex flex-wrap -m-4"},J={class:"p-2 lg:w-1/2 w-full xl:w-1/3"},K={class:"h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden"},R={class:"p-3"},W=e("h3",{class:"title-font text-lg font-medium text-gray-900 mb-3"},"Subir estudiantes",-1),Y=e("p",{class:"leading-relaxed mb-3"}," Este formulario solo permite cargar estudiantes",-1),Z=["onSubmit"],ee={class:"w-full"},se={class:"mt-6"},te={key:0,for:"fileInput",class:"bg-sky-300 text-white font-bold py-2 px-4 rounded"},le={key:1,for:"fileInput",class:"bg-sky-500 text-white font-bold py-2 px-4 rounded"},ae={key:2,class:"w-full my-2 text-green-600"},oe=["value"],ie={class:"w-auto"},re=e("h2",{class:"text-xl text-gray-900 dark:text-white"},"El formato necesita las siguientes columnas",-1),ne=e("ul",{class:"list-decimal my-6 mx-5"},[e("li",{class:"text-lg"},"Nombre"),e("li",{class:"text-lg"},[c("Correo: "),e("small",{class:"text-lg"},"Cada correo debe ser unico")]),e("li",{class:"text-lg"},[c("Identificacion: "),e("small",{class:"text-lg"},"Debe ser un numero")]),e("li",{class:"text-lg"},[c("Sexo: "),e("small",{class:"text-lg"},"Femenino, masculino u otro")]),e("li",{class:"text-lg"},"Fecha de nacimiento"),e("li",{class:"text-lg"},"Semestre"),e("li",{class:"text-lg"},[c("Nivel: "),e("small",{class:"text-lg"},"Primaria, bachillerato, tecnologia, profesional,especializacion,maestría,doctorado")]),e("li",{class:"text-lg"},[c("Cargo: "),e("small",{class:"text-lg"},"estudiante, profesor")])],-1),ce={class:"flex items-center flex-wrap my-6"},de=e("a",{class:"text-gray-500 inline-flex items-center md:mb-2 lg:mb-0"},"Numero de Usuarios: ",-1),ue={class:"text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200"},me=e("svg",{class:"w-1 h-4 mr-1",stroke:"currentColor","stroke-width":"2",fill:"none","stroke-linecap":"round","stroke-linejoin":"round",viewBox:"0 0 24 24"},[e("path",{d:"M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"}),e("circle",{cx:"12",cy:"12",r:"3"})],-1),pe={class:"p-4 lg:w-1/2 w-full xl:w-1/3"},he={class:"h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden"},ge={class:"p-6"},fe=e("h3",{class:"title-font text-lg font-medium text-gray-900 mb-3"},"Matricular estudiantes ",-1),_e=e("p",{class:"leading-relaxed mb-3"},"Seleccione la universidad de los estudiantes",-1),ve=["onSubmit"],xe={class:"mt-6"},be={key:0,for:"file_matricular_user",class:"bg-sky-300 text-white font-bold py-2 px-4 rounded"},we={key:1,for:"file_matricular_user",class:"bg-sky-500 text-white font-bold py-2 px-4 rounded"},ye={key:2,class:"w-full my-2 text-green-600"},Se=["value"],ke={key:1,class:"w-auto"},Ie={class:"w-auto text-red-600 text-lg my-4"},Ue=e("h2",{class:"text-xl text-gray-900 dark:text-white"},"El formato requiere las siguientes columnas",-1),De=e("ul",{class:"list-decimal my-4 mx-5"},[e("li",{class:"text-lg"},"usuario: del estudiante a inscribir"),e("li",{class:"text-lg"},"codigo de la carrera"),e("li",{class:"text-lg"},"codigo de la materia")],-1),Ee=e("div",{class:"p-4 max-w-md mx-auto"},[e("p",{class:""},"Ejemplo:"),e("div",{class:"relative overflow-hidden"},[e("img",{src:"/EXCELmatricularEstudiantes.png",alt:"imagen excel matriculas",class:"rounded-lg transition-transform duration-500 transform hover:scale-110"})])],-1),je={class:"p-4 lg:w-1/2 w-full xl:w-1/3"},qe={__name:"subirExceles",props:{title:String,breadcrumbs:Object,numUsuarios:Number,UniversidadSelect:Object,flash:Object},setup(h){const u=h;U(()=>{u.flash.warning&&(n.warnn=u.flash.warning)});const n=D({UniversidadSelect:null,isOpen:!1,tooltipSettings:{content:"",shown:!1,triggers:[]},tooltipSettings2:{content:"",shown:!1,triggers:[]}}),s=E({archivo1:null,archivo2_matricular:null,universidadID:0});j(()=>{});function S(){s.post(route("user.uploadEstudiantes"),{preserveScroll:!0,onSuccess:()=>{},onError:()=>null,onFinish:()=>null})}function k(){console.log("🧈 debu form.universidadID:",s.universidadID),s.universidadID===null||s.universidadID==0?n.tooltipSettings2.content="Seleccione universidad":s.post(route("user.uploadUniversidad"),{preserveScroll:!0,onSuccess:()=>{s.reset()},onError:()=>null,onFinish:()=>{n.tooltipSettings2.shown=!1}})}return n.UniversidadSelect=L(n.UniversidadSelect,u.UniversidadSelect,"una"),(i,m)=>{const f=w("Button"),I=w("InputLabel");return a(),l(C,null,[o(t(N),{title:u.title},null,8,["title"]),o($,null,{default:g(()=>[o(z,{title:h.title,breadcrumbs:h.breadcrumbs},null,8,["title","breadcrumbs"]),e("div",M,[i.$page.props.flash.warning?(a(),l("div",A,[e("div",O,[e("div",q,[X,e("div",H,[e("div",null,r(i.$page.props.flash.warning?i.$page.props.flash.warning:""),1)])])])])):d("",!0),e("div",P,[e("section",Q,[e("div",T,[i.can(["create user"])?(a(),l("div",G,[e("div",J,[e("div",K,[o(t(y),{class:"mt-5 h-12 lg:h-24 w-full object-cover object-center"}),e("div",R,[W,Y,e("form",{onSubmit:_(S,["prevent"]),id:"upload",class:"flex flex-col text-center"},[e("div",ee,[e("div",se,[t(s).archivo1?(a(),l("label",te," Seleccionar Archivo ")):(a(),l("label",le," Seleccionar archivo ")),e("input",{id:"fileInput",type:"file",onInput:m[0]||(m[0]=p=>t(s).archivo1=p.target.files[0]),accept:"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",class:"opacity-0 relative inset-0 w-full my-1 cursor-pointer"},null,32),t(s).archivo1?(a(),l("p",ae,r(t(s).archivo1.name),1)):d("",!0)]),t(s).progress?(a(),l("progress",{key:0,value:t(s).progress.percentage,max:"100",class:"bg-sky-200"},r(t(s).progress.percentage)+"% ",9,oe)):d("",!0)]),e("div",ie,[v(o(f,{disabled:t(s).archivo1==null,class:b([{"bg-sky-500":t(s).archivo1!==null},"w-32 rounded-none my-4 px-4 py-2 text-white"])},{default:g(()=>[c(r(i.lang().button.subir),1)]),_:1},8,["disabled","class"]),[[x,i.can(["create user"])]])])],40,Z),re,ne,e("div",ce,[de,e("span",ue,[me,c(" "+r(u.numUsuarios),1)])])])])]),e("div",pe,[e("div",he,[o(t(y),{class:"mt-5 h-12 lg:h-20 w-full object-cover object-center"}),e("div",ge,[fe,_e,e("form",{onSubmit:_(k,["prevent"]),id:"upload2"},[e("div",null,[o(I,{for:"universidadID",value:i.lang().label.carrera},null,8,["value"]),o(B,{id:"universidadID",class:"mt-1 block w-full",modelValue:t(s).universidadID,"onUpdate:modelValue":m[1]||(m[1]=p=>t(s).universidadID=p),required:"",dataSet:n.UniversidadSelect},null,8,["modelValue","dataSet"]),o(F,{class:"mt-2",message:t(s).errors.universidadID},null,8,["message"])]),e("div",xe,[t(s).archivo2_matricular?(a(),l("label",be," Seleccionar Archivo ")):(a(),l("label",we," Seleccionar archivo ")),e("input",{id:"file_matricular_user",type:"file",onInput:m[2]||(m[2]=p=>t(s).archivo2_matricular=p.target.files[0]),accept:"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",class:"opacity-0 relative inset-0 w-full my-1 cursor-pointer"},null,32),t(s).archivo2_matricular?(a(),l("p",ye,r(t(s).archivo2_matricular.name),1)):d("",!0)]),t(s).progress?(a(),l("progress",{key:0,value:t(s).progress.percentage,max:"100",class:"bg-sky-200 my-2"},r(t(s).progress.percentage)+"% ",9,Se)):d("",!0),t(s).archivo2_matricular?(a(),l("div",ke,[v(o(f,{disabled:t(s).archivo2_matricular==null,class:b([{"bg-sky-500":t(s).archivo2_matricular!==null},"w-32 rounded-none my-4 px-4 py-2 text-white"])},{default:g(()=>[c(r(i.lang().button.subir)+" archivo ",1)]),_:1},8,["disabled","class"]),[[x,i.can(["create user"])]])])):d("",!0),e("div",Ie,r(n.tooltipSettings2.content),1)],40,ve),Ue,De,Ee])])]),e("div",je,[o(V,{UniversidadSelect:n.UniversidadSelect,flash:u.flash},null,8,["UniversidadSelect","flash"])])])):d("",!0)])])])])]),_:1})],64)}}};export{qe as default};
