import{o as l,c as s,m as d,A as x,r as y,w as k,t as c,a as w,d as h,F as v,g as _,n as b,R as M,O as Z}from"./app-6467c706.js";const L={key:0,xmlns:"http://www.w3.org/2000/svg","data-name":"Layer 1",width:"647.63626",height:"632.17383",viewBox:"0 0 647.63626 632.17383","xmlns:xlink":"http://www.w3.org/1999/xlink"},A=x('<path d="M687.3279,276.08691H512.81813a15.01828,15.01828,0,0,0-15,15v387.85l-2,.61005-42.81006,13.11a8.00676,8.00676,0,0,1-9.98974-5.31L315.678,271.39691a8.00313,8.00313,0,0,1,5.31006-9.99l65.97022-20.2,191.25-58.54,65.96972-20.2a7.98927,7.98927,0,0,1,9.99024,5.3l32.5498,106.32Z" transform="translate(-276.18187 -133.91309)" fill="#f2f2f2"></path><path d="M725.408,274.08691l-39.23-128.14a16.99368,16.99368,0,0,0-21.23-11.28l-92.75,28.39L380.95827,221.60693l-92.75,28.4a17.0152,17.0152,0,0,0-11.28028,21.23l134.08008,437.93a17.02661,17.02661,0,0,0,16.26026,12.03,16.78926,16.78926,0,0,0,4.96972-.75l63.58008-19.46,2-.62v-2.09l-2,.61-64.16992,19.65a15.01489,15.01489,0,0,1-18.73-9.95l-134.06983-437.94a14.97935,14.97935,0,0,1,9.94971-18.73l92.75-28.4,191.24024-58.54,92.75-28.4a15.15551,15.15551,0,0,1,4.40966-.66,15.01461,15.01461,0,0,1,14.32032,10.61l39.0498,127.56.62012,2h2.08008Z" transform="translate(-276.18187 -133.91309)" fill="#3f3d56"></path><path d="M398.86279,261.73389a9.0157,9.0157,0,0,1-8.61133-6.3667l-12.88037-42.07178a8.99884,8.99884,0,0,1,5.9712-11.24023l175.939-53.86377a9.00867,9.00867,0,0,1,11.24072,5.9707l12.88037,42.07227a9.01029,9.01029,0,0,1-5.9707,11.24072L401.49219,261.33887A8.976,8.976,0,0,1,398.86279,261.73389Z" transform="translate(-276.18187 -133.91309)" class="text-primary fill-current"></path><circle cx="190.15351" cy="24.95465" r="20" class="text-primary fill-current"></circle><circle cx="190.15351" cy="24.95465" r="12.66462" fill="#fff"></circle><path d="M878.81836,716.08691h-338a8.50981,8.50981,0,0,1-8.5-8.5v-405a8.50951,8.50951,0,0,1,8.5-8.5h338a8.50982,8.50982,0,0,1,8.5,8.5v405A8.51013,8.51013,0,0,1,878.81836,716.08691Z" transform="translate(-276.18187 -133.91309)" fill="#e6e6e6"></path><path d="M723.31813,274.08691h-210.5a17.02411,17.02411,0,0,0-17,17v407.8l2-.61v-407.19a15.01828,15.01828,0,0,1,15-15H723.93825Zm183.5,0h-394a17.02411,17.02411,0,0,0-17,17v458a17.0241,17.0241,0,0,0,17,17h394a17.0241,17.0241,0,0,0,17-17v-458A17.02411,17.02411,0,0,0,906.81813,274.08691Zm15,475a15.01828,15.01828,0,0,1-15,15h-394a15.01828,15.01828,0,0,1-15-15v-458a15.01828,15.01828,0,0,1,15-15h394a15.01828,15.01828,0,0,1,15,15Z" transform="translate(-276.18187 -133.91309)" fill="#3f3d56"></path><path d="M801.81836,318.08691h-184a9.01015,9.01015,0,0,1-9-9v-44a9.01016,9.01016,0,0,1,9-9h184a9.01016,9.01016,0,0,1,9,9v44A9.01015,9.01015,0,0,1,801.81836,318.08691Z" transform="translate(-276.18187 -133.91309)" class="text-primary fill-current"></path><circle cx="433.63626" cy="105.17383" r="20" class="text-primary fill-current"></circle><circle cx="433.63626" cy="105.17383" r="12.18187" fill="#fff"></circle>',10),B=[A],P={__name:"Icon",props:{name:String},setup(a){const e=a;return(n,f)=>e.name==="nodata"?(l(),s("svg",L,B)):d("",!0)}},S={key:0,class:"ml-2 mx-2"},C={key:1,class:"flex flex-col space-y-2 mx-auto p-6 text-lg"},H={key:2},N={class:"hidden lg:flex justify-center items-center rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700"},O=["onClick","innerHTML","disabled"],j={__name:"Pagination",props:{links:Object,filters:Object},setup(a){var m,p,u,g;const e=a,n=y({params:{search:(m=e.filters)==null?void 0:m.search,field:(p=e.filters)==null?void 0:p.field,order:(u=e.filters)==null?void 0:u.order,perPage:(g=e.filters)==null?void 0:g.perPage}}),f=t=>{let i=M.pickBy(n.params);Z.get(t,i,{replace:!0,preserveState:!0,preserveScroll:!0})};return k(()=>{var t,i,r,o;n.params.search=(t=e.filters)==null?void 0:t.search,n.params.field=(i=e.filters)==null?void 0:i.field,n.params.order=(r=e.filters)==null?void 0:r.order,n.params.perPage=(o=e.filters)==null?void 0:o.perPage}),(t,i)=>(l(),s(v,null,[a.links.data.length!=0?(l(),s("div",S,c(a.links.from)+"-"+c(a.links.to)+" "+c(t.lang().label.of)+" "+c(a.links.total),1)):d("",!0),a.links.data.length==0?(l(),s("div",C,[w(P,{name:"nodata",class:"w-auto h-16"}),h("p",null,c(t.lang().label.no_data),1)])):d("",!0),a.links.links.length>3?(l(),s("div",H,[h("ul",N,[(l(!0),s(v,null,_(a.links.links,(r,o)=>(l(),s("li",{key:o},[h("button",{onClick:V=>f(r.url),class:b(["px-4 py-2 hover:bg-primary hover:text-white",{"bg-primary text-white":r.active}]),innerHTML:r.label,disabled:r.url==null},null,10,O)]))),128))])])):d("",!0)],64))}};export{j as _};
