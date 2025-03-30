(function(u,a){typeof exports=="object"&&typeof module<"u"?a(exports,require("vue"),require("axios")):typeof define=="function"&&define.amd?define(["exports","vue","axios"],a):(u=typeof globalThis<"u"?globalThis:u||self,a(u["@honed/upload"]={},u.Vue,u.axios))})(this,function(u,a,g){"use strict";function E(h,s={}){if(!h)throw new Error("A URL is required to use the uploader.");const v=a.ref(1),d=a.ref((s.files||[]).map((e,r)=>({...e,id:v.value++,remove:()=>p(r)}))),f=a.ref(!1),l=a.reactive({}),b=a.computed(()=>Object.keys(l).length>0);function m(e){e.forEach(r=>{y(r)})}function y(e){const r=v.value++,t=a.reactive({id:r,name:e.name,size:e.size,type:e.type,extension:e.name.split(".").pop(),progress:0,status:"pending",source:e,upload:()=>{U(t.source,{onStart:()=>t.status="uploading",onUploadSuccess:()=>t.status="completed",onError:()=>t.status="error",onUploadError:()=>t.status="error",onProgress:o=>t.progress=o})},remove:()=>p(r)});d.value.unshift(t),s.waited||t.upload()}async function U(e,r={}){function t(o,c){var n,i;(n=s==null?void 0:s[o])==null||n.call(s,c),(i=r==null?void 0:r[o])==null||i.call(r,c)}t("onStart",e),g.post(h,{name:e.name,size:e.size,type:e.type,meta:{...s.meta,...r.meta}}).then(({data:o})=>{t("onSuccess",o.data);const c=new FormData;Object.entries(o.inputs).forEach(([n,i])=>c.append(n,i)),c.append("file",e),g.post(o.attributes.action,c,{onUploadProgress:n=>{if(n.total){const i=Math.round(n.loaded*100/n.total);t("onProgress",i)}}}).then(({data:n})=>t("onUploadSuccess",n.data)).catch(n=>t("onUploadError",n))}).catch(o=>{Object.assign(l,o.response.data),t("onError",o)}).finally(()=>t("onFinish"))}function p(e){d.value=d.value.filter(({id:r})=>r!==e)}function S(){d.value=[]}function j(){return{ondragover:e=>{e.preventDefault(),f.value=!0},ondrop:e=>{var r;e.preventDefault(),m(Array.from(((r=e.dataTransfer)==null?void 0:r.files)||[])),f.value=!1},ondragleave:e=>{e.preventDefault(),f.value=!1}}}function w(){var e;return{type:"file",multiple:((e=s.upload)==null?void 0:e.multiple)??!1,onChange:r=>{const t=r.target;m(Array.from(t.files||[]))}}}function x(e){return typeof e=="string"?e:URL.createObjectURL(e)}return a.reactive({files:d,dragging:f,errors:l,hasErrors:b,addFiles:m,add:y,remove:p,clear:S,upload:U,preview:x,dragRegion:j,bind:w})}u.useUpload=E,Object.defineProperty(u,Symbol.toStringTag,{value:"Module"})});
