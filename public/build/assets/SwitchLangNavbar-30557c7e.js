import{S as p,M as _}from"./index-3cb9eb1b.js";import{u as k,a as m}from"./index-5d8dae98.js";import{y as u,o as t,f as s,q as f,u as e,c as n,g as c,J as v,w as i,h as g,b as d}from"./app-fa012e40.js";const E={__name:"SwitchDarkMode",setup(h){const r=k(),a=m(r);return(l,o)=>{const y=u("tooltip");return t(),s("div",null,[f((t(),s("button",{onClick:o[0]||(o[0]=D=>e(a)()),class:"p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"},[e(r)?(t(),n(e(p),{key:0,class:"w-5 h-5 fill-current"})):c("",!0),e(r)?c("",!0):(t(),n(e(_),{key:1,class:"w-5 h-5 fill-current"}))])),[[y,l.lang().tooltip.dark_mode]])])}}},x={class:"hover:text-gray-400 hover:bg-gray-900 focus:bg-gray-900 focus:text-gray-400 inline-flex items-center justify-center p-2 rounded-md lg:hover:text-gray-500 dark:hover:text-gray-400 lg:hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none lg:focus:bg-gray-100 dark:focus:bg-gray-900 lg:focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"},b=d("span",{class:"w-5 h-5 fill-current"}," ES ",-1),w=d("span",{class:"w-5 h-5 fill-current"}," EN ",-1),N={__name:"SwitchLangNavbar",setup(h){const r=v().props.locale;return(a,l)=>{const o=u("tooltip");return t(),s("div",null,[f((t(),s("span",x,[e(r)=="es"?(t(),n(e(g),{key:0,href:a.route("setlang","en"),class:"flex items-center space-x-2"},{default:i(()=>[b]),_:1},8,["href"])):c("",!0),e(r)=="en"?(t(),n(e(g),{key:1,href:a.route("setlang","es"),class:"flex items-center space-x-2"},{default:i(()=>[w]),_:1},8,["href"])):c("",!0)])),[[o,e(r)=="es"?"Change to English":"Cambiar a español"]])])}}};export{N as _,E as a};