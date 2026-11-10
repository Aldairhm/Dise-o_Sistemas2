/*
 * Real Chromium checks for asynchronous loading feedback. The fixtures expose the
 * same IDs used by Blade and load the production JavaScript unchanged. Network
 * responses are held/reordered to exercise states that fast local requests hide.
 * Run: node tests/browser/loading-feedback.cjs
 * Requires Playwright, Chromium and installed PHP dependencies.
 * PHP_BINARY and CHROMIUM_PATH can override executable locations.
 */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const { spawnSync } = require('node:child_process');
const { chromium } = require('playwright');

const root = path.resolve(__dirname, '../..');
const rendered = spawnSync(process.env.PHP_BINARY || 'php', ['tests/browser/render-loading-buttons.php'], { cwd: root, encoding: 'utf8', timeout: 60000 });
if (rendered.error || rendered.status !== 0) {
  throw new Error('Cannot render the real Blade button fixtures. Set PHP_BINARY to your PHP executable. ' + (rendered.error?.message || rendered.stderr));
}
const componentHtml = rendered.stdout;
const shared = `<meta name="csrf-token" content="fixture-token"><style>[x-cloak]{display:none!important}.hidden{display:none!important}.invisible{visibility:hidden}button{min-width:80px;min-height:30px}#tableSkeleton,#tableSkeletonCategorias{height:40px}</style>
<script>window.feedback=[];window.Swal={fire:async options=>{feedback.push(options);return{isConfirmed:false}},mixin:()=>({fire:async options=>{feedback.push(options);return{isConfirmed:false}}}),close(){},showLoading(){}};window.Sortable=class {destroy(){}};</script>`;
const users = `${shared}<input id="searchInput"><select id="roleFilter"><option>todos</option></select><select id="statusFilter"><option>todos</option></select>
<button id="btnViewTable"></button><button id="btnViewCards"></button><div id="tableSkeleton" class="hidden" role="status">Cargando</div>
<div id="usersTableWrapper">Original table <a class="pagination-link" href="/usuarios?page=2">Page 2</a></div><div id="usersCardsWrapper" class="hidden"><div id="usersCardsGrid">Original cards</div></div>
<div id="kpiTotal"></div><div id="kpiAdmins"></div><div id="kpiVendedores"></div><div id="kpiActivos"></div>
<div id="userModal"><form id="userForm"><input id="userId"><input id="input_nombre_real" value="User"><input id="input_telefono" value="7890-1234"><input id="input_username" value="user@example.test"><input id="input_rol" value="vendedor"><input id="input_estado" value="1"><input id="input_password" value="Password123!"><input id="input_password_confirmation" value="Password123!"><button type="submit" id="btnSubmitModal"><span id="btnSubmitContent">Guardar</span><span id="btnSubmitSpinner" class="hidden">Guardando</span></button></form></div>
<div id="modalAlertError" class="hidden"><div id="modalAlertErrorText"></div></div>
<script type="module" src="/resources/js/usuarios.js"></script><script>document.getElementById('userForm').addEventListener('submit',e=>window.handleUserSubmit(e));</script>`;
const categories = `${shared}<script>window.APP_ROUTES={index:'/categorias',store:'/categorias',update:'/categorias',toggleStatus:'/categorias',reorder:'/categorias/reorder',export:'/categorias/export'};</script>
<input id="searchInput"><button id="clearSearchBtn"></button><button id="btnViewTable"></button><button id="btnViewCards"></button><button id="tabTodas"></button><button id="tabActivas"></button><button id="tabInactivos"></button>
<div id="tableSkeletonCategorias" class="hidden" role="status">Cargando</div><div id="tableWrapper">Original categories</div><div id="cardsWrapper" class="hidden">Original cards</div>
<div id="kpiTotal"></div><div id="kpiActivas"></div><div id="kpiInactivos"></div><form id="categoriaForm"></form>
<script type="module" src="/resources/js/categorias.js"></script>`;
const nativeForm = `${shared}<form id="nativeForm" action="/native-submit" method="post" target="result" data-loading-form data-loading-target="#nativeContent" data-loading-skeleton="#nativeSkeleton"><input name="value" required id="requiredValue"><button type="submit" id="nativeButton" data-loading-label="Guardando..." name="intent" value="save"><span>Guardar</span></button></form><iframe name="result" hidden></iframe><div id="nativeContent">Previous result</div><div id="nativeSkeleton" class="hidden" role="status">Cargando</div><i id="legacySpinner" class="fas fa-spinner fa-spin"></i><i id="legacyNotch" class="fas fa-circle-notch fa-spin"></i><div class="ax-skeleton-shimmer" id="shimmer" style="width:100px;height:24px"></div><script type="module" src="/resources/js/loading-states.js"></script><link rel="stylesheet" href="/resources/css/loading.css">`;
const commissionDetails = `${shared}<script>window.ROUTES={update:'/comisiones',pendientes:'/comisiones/pendientes'};</script>
<div id="modalDetalleComision" class="hidden"><div id="detLoadingSkeleton" role="status">Cargando detalle</div><div id="detContentContainer" class="hidden"><div id="detEstadoBadge"></div><div id="detNotasCompletas">Original notes</div></div></div>
<div id="liquidarResumenBox" class="hidden"></div><div id="liquidarEmptyBox" class="hidden"></div><div id="liquidarLoadingBox" class="hidden">Cargando pendientes</div><div id="liquidarDetalleLista" class="hidden"></div><button id="btnConfirmLiquidar">Continuar</button><div id="liquidarTotalMonto"></div><div id="liquidarCountBadge"></div>
<script src="/fixture/commission-source.js"></script>`;
const components = `${shared}${componentHtml}<link rel="stylesheet" href="/resources/css/loading.css"><script defer src="/node_modules/alpinejs/dist/cdn.min.js"></script>`;
const images = `${shared}<div id="imageFrame"><div id="v-modal-image-loading" class="hidden" role="status">Cargando imagen</div><span id="v-modal-image-error" class="hidden">Imagen no disponible</span><img id="v-modal-img" style="width:100px;height:100px"><a id="v-modal-download">Descargar</a></div><script src="/fixture/image-source.js"></script>`;
let nativePosts = 0;
const nativeBodies=[];
const server = http.createServer((req,res)=>{
  const pathname = new URL(req.url,'http://localhost').pathname;
  const fixture = {'/fixture/users':users,'/fixture/categories':categories,'/fixture/native':nativeForm,'/fixture/commissions':commissionDetails,'/fixture/components':components,'/fixture/images':images}[pathname];
  if(fixture){res.setHeader('Content-Type','text/html; charset=utf-8');res.end(`<!doctype html><html lang="es"><body>${fixture}</body></html>`);return;}
  if(pathname==='/fixture/image-source.js') {
    const source=fs.readFileSync(path.join(root,'resources/views/componentsHome/modalViewProduct.blade.php'),'utf8');
    res.setHeader('Content-Type','application/javascript');
    res.end(source.slice(source.indexOf('        let productImageRequest'),source.indexOf('        function openModal(')));return;
  }
  if(pathname==='/fixture/commission-source.js'){
    const modal=fs.readFileSync(path.join(root,'resources/views/comisiones/modals.blade.php'),'utf8');
    const index=fs.readFileSync(path.join(root,'resources/views/comisiones/index.blade.php'),'utf8');
    const code=modal.slice(modal.indexOf('<script>')+8,modal.lastIndexOf('</script>'));
    // Load the complete production functions, including their request guards.
    const pending=index.slice(index.indexOf('    async function cargarPendientesVendedor('),index.indexOf('    function toggleDetallePendientes('));
    const total=index.slice(index.indexOf('    function recalcularTotalSeleccionado('),index.indexOf('    // ── Editar comisión'));
    res.setHeader('Content-Type','application/javascript');res.end(code+'\n'+pending+'\n'+total);return;
  }
  if(pathname==='/native-submit'){let body='';req.on('data',chunk=>body+=chunk);req.on('end',()=>{nativePosts++;nativeBodies.push(body);res.end('saved');});return;}
  const file=path.resolve(root,'.'+pathname);
  if(!file.startsWith(root+path.sep)||!fs.existsSync(file)||!fs.statSync(file).isFile()){res.writeHead(404);res.end();return;}
  res.setHeader('Content-Type',file.endsWith('.css')?'text/css':'application/javascript');
  let source=fs.readFileSync(file,'utf8');
  // Tailwind imports/directives are compiled by Vite, not interpreted by browsers.
  // Keep actual ordinary selectors/keyframes here for the animation assertion.
  if(file.endsWith('app.css'))source=source.replace(/^@import[^;]+;/gm,'');
  res.end(source);
});
const wait = async predicate => {const end=Date.now()+5000;while(!predicate()){if(Date.now()>end)throw Error('Timed out waiting for intercepted request');await new Promise(resolve=>setTimeout(resolve,10));}};
const fulfill=async(route,json,status=200)=>{try{await route.fulfill({status,contentType:'application/json',body:JSON.stringify(json)});}catch(error){if(!/closed|cancel|handled/i.test(error.message))throw error;}};
const userData = html => ({success:true,html,cards:'<article>Latest cards</article>',stats:{total:1,admins:0,vendedores:1,activos:1}});
const categoryData = text=>({table:`<div>${text}</div>`,cards:`<article>${text}</article>`,stats:{total:1,activas:1,inactivos:0}});
const results=[];
async function check(name,fn){try{await fn();results.push({name,passed:true});console.log(`PASS ${name}`);}catch(error){results.push({name,passed:false});console.error(`FAIL ${name}: ${error.stack || error.message}`);}}

(async()=>{
  await new Promise(resolve=>server.listen(0,'127.0.0.1',resolve));
  const base=`http://127.0.0.1:${server.address().port}`;
  const browser=await chromium.launch({headless:true,executablePath:process.env.CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
  try{
    await check('users: pagination shows its existing skeleton and restores content',async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',e=>errors.push(e.message));
      await page.route('**/usuarios?*',r=>pending.push(r));
      await page.goto(base+'/fixture/users');await page.waitForFunction(()=>typeof window.applyFilters==='function');
      await page.click('.pagination-link');await wait(()=>pending.length===1);
      assert.equal(await page.locator('#tableSkeleton').isVisible(),true);
      assert.equal(await page.locator('#usersTableWrapper').isVisible(),false);
      await fulfill(pending[0],userData('Page two'));await page.waitForFunction(()=>document.getElementById('usersTableWrapper').textContent==='Page two');
      assert.equal(await page.locator('#tableSkeleton').isVisible(),false);assert.equal(await page.locator('#usersTableWrapper').isVisible(),true);assert.deepEqual(errors,[]);await page.close();
    });
    await check('users: latest filter wins and an HTTP error restores the selected view',async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',e=>errors.push(e.message));
      await page.route('**/usuarios?*',r=>pending.push(r));await page.goto(base+'/fixture/users');await page.waitForFunction(()=>typeof window.applyFilters==='function');
      await page.evaluate(()=>{document.getElementById('searchInput').value='slow';void applyFilters();});await wait(()=>pending.length===1);
      await page.evaluate(()=>{document.getElementById('searchInput').value='latest';void applyFilters();});await wait(()=>pending.length===2);
      assert.equal(await page.locator('#tableSkeleton').isVisible(),true);
      await fulfill(pending[1],userData('Latest table'));await page.waitForFunction(()=>document.getElementById('usersTableWrapper').textContent==='Latest table');
      await fulfill(pending[0],userData('Stale table'));assert.equal(await page.locator('#usersTableWrapper').textContent(),'Latest table');
      await page.evaluate(()=>{setView('cards');void applyFilters();});await wait(()=>pending.length===3);
      await fulfill(pending[2],{message:'Temporary failure'},500);await page.waitForFunction(()=>document.getElementById('tableSkeleton').classList.contains('hidden'));
      assert.equal(await page.locator('#usersCardsWrapper').isVisible(),true);assert.equal(await page.locator('#usersTableWrapper').isVisible(),false);assert.equal(await page.locator('#usersTableWrapper').textContent(),'Latest table');assert.deepEqual(errors,[]);await page.close();
    });
    await check('users: invalid input never starts a spinner; failed save resets and duplicate submit is blocked',async()=>{
      const page=await browser.newPage();const pending=[];await page.route('**/usuarios',r=>pending.push(r));await page.goto(base+'/fixture/users');await page.waitForFunction(()=>typeof window.handleUserSubmit==='function');
      await page.fill('#input_telefono','123');await page.evaluate(()=>document.getElementById('userForm').requestSubmit());
      assert.equal(pending.length,0);assert.equal(await page.locator('#btnSubmitSpinner').isVisible(),false);assert.equal(await page.locator('#btnSubmitModal').isDisabled(),false);
      await page.fill('#input_telefono','7890-1234');await page.evaluate(()=>document.getElementById('userForm').requestSubmit());await wait(()=>pending.length===1);
      assert.equal(await page.locator('#btnSubmitSpinner').isVisible(),true);assert.equal(await page.locator('#btnSubmitModal').isDisabled(),true);
      await page.evaluate(()=>document.getElementById('userForm').requestSubmit());assert.equal(pending.length,1);
      await fulfill(pending[0],{message:'Cannot save'},500);await page.waitForFunction(()=>!document.getElementById('btnSubmitModal').disabled);
      assert.equal(await page.locator('#btnSubmitSpinner').isVisible(),false);assert.equal(await page.locator('#btnSubmitContent').isVisible(),true);assert.equal(await page.locator('#modalAlertError').isVisible(),true);await page.close();
    });
    await check('categories: skeleton remains for latest request and HTTP errors recover previous content',async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',e=>errors.push(e.message));
      await page.route('**/categorias?*',r=>pending.push(r));await page.goto(base+'/fixture/categories');await wait(()=>pending.length===1);
      assert.equal(await page.locator('#tableSkeletonCategorias').isVisible(),true);await fulfill(pending[0],categoryData('Loaded categories'));await page.waitForFunction(()=>document.getElementById('tableSkeletonCategorias').classList.contains('hidden'));
      await page.evaluate(()=>setEstado('activas'));await wait(()=>pending.length===2);await page.evaluate(()=>setEstado('inactivos'));await wait(()=>pending.length===3);
      assert.equal(await page.locator('#tableSkeletonCategorias').isVisible(),true);await fulfill(pending[2],categoryData('Newest categories'));await page.waitForFunction(()=>document.getElementById('tableWrapper').textContent==='Newest categories');await fulfill(pending[1],categoryData('Stale categories'));
      assert.equal(await page.locator('#tableWrapper').textContent(),'Newest categories');await page.evaluate(()=>{setView('cards');void refreshContent();});await wait(()=>pending.length===4);await fulfill(pending[3],{message:'Cannot load'},500);await page.waitForFunction(()=>document.getElementById('tableSkeletonCategorias').classList.contains('hidden'));
      assert.equal(await page.locator('#cardsWrapper').isVisible(),true);assert.equal(await page.locator('#cardsWrapper').textContent(),'Newest categories');assert.deepEqual(errors,[]);await page.close();
    });
    await check('commissions: failed detail load preserves nodes and successful reopen restores content',async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',e=>errors.push(e.message));
      await page.route('**/comisiones/*',r=>pending.push(r));await page.goto(base+'/fixture/commissions');await page.waitForFunction(()=>typeof window.verDetalleComision==='function');
      await page.evaluate(()=>{void verDetalleComision(1);});await wait(()=>pending.length===1);assert.equal(await page.locator('#detLoadingSkeleton').isVisible(),true);
      await fulfill(pending[0],{message:'Temporary detail error'},500);await page.waitForSelector('#detErrorCarga');assert.equal(await page.locator('#detLoadingSkeleton').isVisible(),false);assert.equal(await page.locator('#detNotasCompletas').count(),1);
      await page.evaluate(()=>{closeDetalleModal();void verDetalleComision(2);});await wait(()=>pending.length===2);assert.equal(await page.locator('#detLoadingSkeleton').isVisible(),true);assert.equal(await page.locator('#detErrorCarga').count(),0);
      await fulfill(pending[1],{success:true,comision:{id:2,estado:'Pendiente',monto:20,notas:'Loaded after retry',vendedor:{nombre:'Seller'}}});await page.waitForFunction(()=>document.getElementById('detNotasCompletas').textContent==='Loaded after retry');assert.equal(await page.locator('#detLoadingSkeleton').isVisible(),false);assert.equal(await page.locator('#detContentContainer').isVisible(),true);assert.deepEqual(errors,[]);await page.close();
    });
    await check('commissions: changing seller keeps latest loading active and clearing selection cancels it',async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',e=>errors.push(e.message));
      await page.route('**/comisiones/pendientes/*',r=>pending.push(r));await page.goto(base+'/fixture/commissions');await page.waitForFunction(()=>typeof window.cargarPendientesVendedor==='function');
      await page.evaluate(()=>{void cargarPendientesVendedor(1);});await wait(()=>pending.length===1);await page.evaluate(()=>{void cargarPendientesVendedor(2);});await wait(()=>pending.length===2);
      assert.equal(await page.locator('#liquidarLoadingBox').isVisible(),true);assert.equal(await page.locator('#btnConfirmLiquidar').isDisabled(),true);
      await fulfill(pending[0],{success:true,cantidad:0,comisiones:[]});assert.equal(await page.locator('#liquidarLoadingBox').isVisible(),true);
      await fulfill(pending[1],{success:true,cantidad:1,total:20,comisiones:[{id:2,monto:20,concepto:'Newest seller bonus'}]});await page.waitForFunction(()=>document.getElementById('liquidarResumenBox').classList.contains('hidden')===false);assert.equal(await page.locator('#liquidarLoadingBox').isVisible(),false);assert.equal(await page.locator('#btnConfirmLiquidar').isDisabled(),false);assert.match(await page.locator('#liquidarDetalleLista').textContent(),/Newest seller bonus/);
      await page.evaluate(()=>{void cargarPendientesVendedor(3);});await wait(()=>pending.length===3);await page.evaluate(()=>{void cargarPendientesVendedor('');});assert.equal(await page.locator('#liquidarLoadingBox').isVisible(),false);assert.equal(await page.locator('#btnConfirmLiquidar').isDisabled(),true);assert.equal(await page.locator('#liquidarDetalleLista').textContent(),'');assert.deepEqual(errors,[]);await page.close();
    });
    await check('native forms: validation, duplicate protection, spinner and pageshow restoration',async()=>{
      const page=await browser.newPage();await page.goto(base+'/fixture/native');await page.waitForFunction(()=>window.AxLoading);
      await page.click('#nativeButton');assert.equal(await page.locator('#nativeButton').isDisabled(),false);assert.equal(nativePosts,0);
      await page.fill('#requiredValue','valid');await page.click('#nativeButton');await wait(()=>nativePosts===1);
      assert.equal(await page.locator('#nativeButton').getAttribute('aria-busy'),'true');assert.equal(await page.locator('#nativeButton .ax-spinner').isVisible(),true);assert.equal(await page.locator('#nativeContent').isVisible(),false);assert.equal(await page.locator('#nativeSkeleton').isVisible(),true);assert.equal(new URLSearchParams(nativeBodies[0]).get('intent'),'save');
      await page.evaluate(()=>{const form=document.getElementById('nativeForm');form.addEventListener('submit',e=>queueMicrotask(()=>window.duplicatePrevented=e.defaultPrevented),{once:true});form.requestSubmit(document.getElementById('nativeButton'));});await page.waitForFunction(()=>window.duplicatePrevented===true);assert.equal(nativePosts,1);
      await page.evaluate(()=>window.dispatchEvent(new PageTransitionEvent('pageshow',{persisted:true})));assert.equal(await page.locator('#nativeButton').isDisabled(),false);assert.equal(await page.locator('#nativeButton').textContent(),'Guardar');assert.equal(await page.locator('#nativeContent').isVisible(),true);assert.equal(await page.locator('#nativeSkeleton').isVisible(),false);
      await page.close();
    });
    await check('shared Blade button: static and Alpine loading states stay visible and disabled correctly', async()=>{
      const page=await browser.newPage();const errors=[];page.on('pageerror',error=>errors.push(error.message));
      await page.goto(base+'/fixture/components');await page.waitForFunction(()=>window.Alpine?.$data(document.getElementById('componentScope')));
      assert.equal(await page.locator('#componentIdle').isDisabled(),false);
      assert.equal(await page.locator('#componentIdle [role=status]').isVisible(),false);
      assert.equal(await page.locator('#componentBusy').isDisabled(),true);
      assert.equal(await page.locator('#componentBusy [role=status]').isVisible(),true);
      await page.locator('#componentDynamic [role=status]').waitFor({ state: 'hidden' });assert.equal(await page.locator('#componentDynamic [role=status]').isVisible(),false);
      await page.evaluate(()=>{Alpine.$data(document.getElementById('componentScope')).saving=true;});
      await page.waitForFunction(()=>document.getElementById('componentDynamic').disabled);
      await page.locator('#componentDynamic [role=status]').waitFor({ state: 'visible' });
      assert.equal(await page.locator('#componentDynamic [role=status]').isVisible(),true);
      assert.equal(await page.locator('#componentDynamic').getAttribute('aria-busy'),'true');
      await page.evaluate(()=>{const data=Alpine.$data(document.getElementById('componentScope'));data.saving=false;data.blocked=true;});
      await page.waitForFunction(()=>document.getElementById('componentDynamic').getAttribute('aria-busy')==='false');
      assert.equal(await page.locator('#componentDynamic').isDisabled(),true);
      await page.locator('#componentDynamic [role=status]').waitFor({ state: 'hidden' });assert.equal(await page.locator('#componentDynamic [role=status]').isVisible(),false);
      await page.evaluate(()=>{Alpine.$data(document.getElementById('componentScope')).blocked=false;});
      await page.waitForFunction(()=>!document.getElementById('componentDynamic').disabled);assert.deepEqual(errors,[]);await page.close();
    });
    await check('product image: skeleton follows the latest image and recovers from failure', async()=>{
      const page=await browser.newPage();const pending=[];const errors=[];page.on('pageerror',error=>errors.push(error.message));
      await page.route('**/image-*.svg',route=>pending.push(route));await page.goto(base+'/fixture/images');await page.waitForFunction(()=>typeof loadProductModalImage==='function');
      await page.evaluate(()=>loadProductModalImage('/image-old.svg'));await wait(()=>pending.length===1);
      assert.equal(await page.locator('#v-modal-image-loading').isVisible(),true);
      await page.evaluate(()=>loadProductModalImage('/image-new.svg'));await wait(()=>pending.length===2);
      await pending[0].fulfill({status:200,contentType:'image/svg+xml',body:'<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"/>'});
      assert.equal(await page.locator('#v-modal-image-loading').isVisible(),true);
      await pending[1].fulfill({status:200,contentType:'image/svg+xml',body:'<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"/>'});
      await page.waitForFunction(()=>document.getElementById('v-modal-img').getAttribute('src')==='/image-new.svg');
      assert.equal(await page.locator('#v-modal-image-loading').isVisible(),false);assert.equal(await page.locator('#v-modal-img').isVisible(),true);
      await page.evaluate(()=>loadProductModalImage('/image-error.svg'));await wait(()=>pending.length===3);await pending[2].fulfill({status:500,body:'Failed image'});
      await page.waitForFunction(()=>!document.getElementById('v-modal-image-error').classList.contains('hidden'));
      assert.equal(await page.locator('#v-modal-image-loading').isVisible(),false);assert.equal(await page.locator('#imageFrame').getAttribute('aria-busy'),'false');
      await page.evaluate(()=>loadProductModalImage(''));assert.equal(await page.locator('#v-modal-image-loading').isVisible(),false);assert.deepEqual(errors,[]);await page.close();
    });
    await check('shared skeleton shimmer animates and respects reduced motion',async()=>{
      const page=await browser.newPage();await page.goto(base+'/fixture/native');await page.waitForFunction(()=>getComputedStyle(document.getElementById('shimmer')).animationName!=='none');
      const normal=await page.locator('#shimmer').evaluate(el=>getComputedStyle(el).animationName);assert.notEqual(normal,'none');
      for (const selector of ['#legacySpinner','#legacyNotch']) {
        const style=await page.locator(selector).evaluate(el=>({animation:getComputedStyle(el).animationName,border:getComputedStyle(el).borderTopWidth,width:el.getBoundingClientRect().width}));
        assert.notEqual(style.animation,'none');assert.ok(parseFloat(style.border)>0);assert.ok(style.width>0);
      }
      await page.emulateMedia({reducedMotion:'reduce'});
      assert.equal(await page.locator('#shimmer').evaluate(el=>getComputedStyle(el).animationName),'none');assert.equal(await page.locator('#legacySpinner').evaluate(el=>getComputedStyle(el).animationName),'none');await page.close();
    });
  }finally{await browser.close();await new Promise(resolve=>server.close(resolve));}
  const failed=results.filter(result=>!result.passed).length;
  console.log(`\n${results.length-failed}/${results.length} browser loading checks passed`);
  process.exitCode=failed?1:0;
})().catch(error=>{console.error(error);server.close();process.exitCode=1;});
