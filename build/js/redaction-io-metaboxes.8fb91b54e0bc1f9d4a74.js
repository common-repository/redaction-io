(()=>{"use strict";function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}function t(t,o){for(var r=0;r<o.length;r++){var n=o[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,(i=n.key,a=void 0,a=function(t,o){if("object"!==e(t)||null===t)return t;var r=t[Symbol.toPrimitive];if(void 0!==r){var n=r.call(t,o||"default");if("object"!==e(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===o?String:Number)(t)}(i,"string"),"symbol"===e(a)?a:String(a)),n)}var i,a}const o=new(function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.moduleName="box"}var o,r,n;return o=e,r=[{key:"init",value:function(){}},{key:"DOMReady",value:function(){this.isStillPendingGeneration(),this.onClickOnGenerateButton()}},{key:"isStillPendingGeneration",value:function(){var e=this;if(document.querySelector("#redaction-io_cpt")){var t=document.querySelector('input#post_ID[name="post_ID"]').value,o=new FormData;o.append("action","redaction_io_xhr_still_pending_generation"),o.append("post_ID",t),fetch(redaction_io_ajax_object.link,{method:"POST",credentials:"same-origin",body:o}).then((function(e){return e.json()})).then((function(o){o.still_pending&&(document.querySelector(".redaction-io_cpt_step").classList.remove("--isHidden"),document.querySelector(".redaction-io_cpt_step_progress").classList.remove("isHidden"),e.disabledForm(),e.setProgressBar(o.step_message,o.current_stage,o.total_nb_stage),e.XHRVerifyStage(o.task_id,t))})).catch((function(e){console.error(e)}))}}},{key:"onClickOnGenerateButton",value:function(){var e=this;document.querySelector(".redaction-io_cpt_launch_submit").addEventListener("click",(function(t){var o=document.querySelector(".redaction-io_cpt_launch_wrapper"),r=(document.querySelector(".redaction-io_cpt_step_message"),o.querySelector("input").value),n=o.querySelector("select").value;document.querySelector(".redaction-io_cpt_step").classList.remove("--isHidden"),""===r?e.setErrorMessage("Please enter a keyword"):(e.disabledForm(),e.XHRLaunchGenerateTask(r,n)),t.preventDefault}))}},{key:"XHRLaunchGenerateTask",value:function(e,t){var o=this,r=document.querySelector('input#post_ID[name="post_ID"]').value,n=new FormData;n.append("action","redaction_io_xhr_generate_content"),n.append("keyword",e),n.append("post_ID",r),n.append("lang",t),fetch(redaction_io_ajax_object.link,{method:"POST",credentials:"same-origin",body:n}).then((function(e){return e.json()})).then((function(e){e&&(e.error?o.setErrorMessage(e.message):(document.querySelector(".redaction-io_cpt_step_progress").classList.remove("isHidden"),o.setProgressBar(e.current_stage_message,e.current_stage,e.total_nb_stage),o.XHRVerifyStage(e.task_id,r)))})).catch((function(e){console.error(e)}))}},{key:"XHRVerifyStage",value:function(e,t){var o=this,r=new FormData;r.append("action","redaction_io_xhr_verify_stage"),r.append("task_id",e),r.append("post_ID",t);var n=setInterval((function(){fetch(redaction_io_ajax_object.link,{method:"POST",credentials:"same-origin",body:r}).then((function(e){return e.json()})).then((function(e){if(e)if(e.error){clearInterval(n);var r=document.querySelector(".redaction-io_cpt_step_message");r.querySelector("span").classList.add("redaction-io-error"),r.querySelector("span").innerHTML=e.error.message,r.querySelector("strong").classList.add("isHidden")}else{var i=document.querySelector(".redaction-io_cpt_step_message");if(i.querySelector("span").classList.remove("redaction-io-error"),i.querySelector("span").classList.remove("isHidden"),500!==e.status_http&&""!==e.status_http&&(o.setProgressBar(e.current_stage_message,e.current_stage,e.total_nb_stage),e.current_stage===e.total_nb_stage)){clearInterval(n);var a=e.h1;o.removeDisabledFields(),o.setTitle(a),o.setContentEditor(e.format_html),o.setSlug(e.slug,a,t)}}})).catch((function(e){console.error(e)}))}),1e4)}},{key:"setContentEditor",value:function(e){if(void 0!==tinymce.editors.content)tinymce.activeEditor.setContent(e);else if(void 0!==wp.data){var t=wp.blocks.createBlock("core/html",{content:e});wp.data.dispatch("core/editor").insertBlocks(t),wp.data.select("core/block-editor").getBlocks().forEach((function(e){wp.data.dispatch("core/editor").replaceBlocks(e.clientId,wp.blocks.rawHandler({HTML:wp.blocks.getBlockContent(e)}))}))}}},{key:"setTitle",value:function(e){if(void 0!==tinymce.editors.content){var t=document.querySelector('input#title[name="post_title"]'),o=document.querySelector("#title-prompt-text");t.value=e,o.classList.add("screen-reader-text")}else void 0!==wp.data&&wp.data.dispatch("core/editor").editPost({title:e})}},{key:"setSEOContent",value:function(e){document.querySelector('input[name="'+e.title.name+'"]').value=e.title.value,document.querySelector('input[name="'+e.description.name+'"]').innerHTML=e.description.value}},{key:"setSlug",value:function(e,t,o){void 0!==tinymce.editors.content||void 0!==wp.data&&wp.data.dispatch("core/editor").editPost({slug:e})}},{key:"removeDisabledFields",value:function(){var e=document.querySelector(".redaction-io_cpt_launch_submit"),t=document.querySelector(".redaction-io_cpt_launch_wrapper");e.removeAttribute("disabled"),t.querySelector("input").removeAttribute("disabled","disabled"),t.querySelector("select").removeAttribute("disabled","disabled")}},{key:"setErrorMessage",value:function(e){var t=document.querySelector(".redaction-io_cpt_step_message");t.querySelector("span").classList.add("redaction-io-error"),t.querySelector("span").innerHTML=e,t.querySelector("strong").classList.add("isHidden"),this.removeDisabledFields()}},{key:"setProgressBar",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,t=arguments.length>1?arguments[1]:void 0,o=arguments.length>2?arguments[2]:void 0,r=12.5,n=null,i=document.querySelector(".redaction-io_cpt_step_progress");if(t&&(n=r*t)>100&&(n=100),null!==n&&(i.querySelector(".redaction-io_cpt_step_progress_bar").innerHTML=n+"%",i.querySelector(".redaction-io_cpt_step_progress_bar").style.width=n+"%"),e){var a=document.querySelector(".redaction-io_cpt_step_message");a.querySelector("span").innerHTML=e,t&&o&&(a.querySelector("strong").innerHTML="["+t+"/"+o+"]")}}},{key:"disabledForm",value:function(){var e=document.querySelector(".redaction-io_cpt_launch_submit"),t=document.querySelector(".redaction-io_cpt_launch_wrapper"),o=document.querySelector(".redaction-io_cpt_step_message");e.setAttribute("disabled","disabled"),t.querySelector("input").setAttribute("disabled","disabled"),t.querySelector("select").setAttribute("disabled","disabled"),o.querySelector("span").classList.remove("redaction-io-error"),o.querySelector("strong").classList.remove("isHidden")}}],r&&t(o.prototype,r),n&&t(o,n),Object.defineProperty(o,"prototype",{writable:!1}),e}());function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}function n(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,(i=n.key,a=void 0,a=function(e,t){if("object"!==r(e)||null===e)return e;var o=e[Symbol.toPrimitive];if(void 0!==o){var n=o.call(e,t||"default");if("object"!==r(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(i,"string"),"symbol"===r(a)?a:String(a)),n)}var i,a}new(function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.moduleName="app-metaboxes",this.modules={},this.init()}var t,r,i;return t=e,r=[{key:"init",value:function(){window.app=this,this.requireModules(),window.app.initModule("boxModule")}},{key:"requireModules",value:function(){this.modules.boxModule=o}},{key:"initModule",value:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;t||(t=this.modules),t[e]?(t[e].init instanceof Function?t[e].init():console.debug("no init() method for module named : "+e),t[e].DOMReady instanceof Function?"loading"===document.readyState?document.addEventListener("DOMContentLoaded",(function(){t[e].DOMReady()})):t[e].DOMReady():console.debug("no DOMReady() method for module named : "+e)):console.debug("no module :"+e)}}],r&&n(t.prototype,r),i&&n(t,i),Object.defineProperty(t,"prototype",{writable:!1}),e}())})();