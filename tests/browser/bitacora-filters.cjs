// Real Blade filters, Alpine state, and the production custom-select control.
// Run from the repository root. PHP_BINARY and CHROMIUM_PATH are optional.
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const http=require('node:http');
const {spawnSync}=require('node:child_process');
const {chromium}=require('playwright');
const root=path.resolve(__dirname,'../..');
let rendered,base;
const requests=[];
const server=http.createServer((req,res)=>{
  const url=new URL(req.url,base);
  if(url.pathname==='/bitacora' && req.headers.accept?.includes('application/json')) {
    requests.push(url);
    const pageUrl=new URL(url.href);
    pageUrl.searchParams.set('page','2');
    res.setHeader('Content-Type','application/json');
    res.end(JSON.stringify({success:true,html:`<p>Resultados cargados</p><a href="${pageUrl.href}">Página 2</a>`,total:26}));return;
  }
  if(url.pathname==='/bitacora') {
    const config=JSON.stringify(Object.fromEntries(url.searchParams)).replaceAll('<','\\u003c');
    res.setHeader('Content-Type','text/html; charset=utf-8');
    res.end(`<!doctype html><html><head><meta charset="utf-8"><style>[x-cloak]{display:none!important}</style><link rel="stylesheet" href="/resources/css/filter-selects.css"></head><body><main id="bitacora" x-data='gestionBitacora(${config})'>${rendered}<div id="contenedorTablaBitacora">Resultados iniciales</div><p role="alert" x-text="errorCarga"></p></main><script type="module" src="/fixture/bootstrap.js"></script></body></html>`);return;
  }
  if(url.pathname==='/fixture/bootstrap.js') {
    res.setHeader('Content-Type','application/javascript');
    res.end("import '/resources/js/filter-selects.js';import Alpine from '/node_modules/alpinejs/dist/module.esm.js';window.Alpine=Alpine;Alpine.start();");return;
  }
  const file=path.resolve(root,'.'+url.pathname);
  if(!file.startsWith(root+path.sep)||!fs.existsSync(file)||!fs.statSync(file).isFile()) {res.writeHead(404);res.end();return;}
  res.setHeader('Content-Type',file.endsWith('.css')?'text/css':'application/javascript');res.end(fs.readFileSync(file));
});
(async()=>{
  await new Promise(resolve=>server.listen(0,'127.0.0.1',resolve));base=`http://127.0.0.1:${server.address().port}`;
  const render=spawnSync(process.env.PHP_BINARY||'php',['tests/browser/render-bitacora-filters.php',base],{cwd:root,encoding:'utf8',timeout:60000});
  if(render.error||render.status!==0) throw new Error(render.error?.message||render.stderr);
  rendered=render.stdout;
  const browser=await chromium.launch({headless:true,executablePath:process.env.CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
  let passed=0,failed=0;
  async function check(name,test,query='') {
    const page=await browser.newPage();const errors=[];page.on('pageerror',error=>errors.push(error.message));
    try {requests.length=0;await page.goto(base+'/bitacora'+query);await page.waitForSelector('#bitacora-modulo + .filter-select__toggle');await test(page);assert.deepEqual(errors,[]);passed++;console.log('PASS '+name);}
    catch(error){failed++;console.error('FAIL '+name+': '+error.stack);}finally{await page.close();}
  }
  async function select(page,id,label) {
    const response=page.waitForResponse(r=>new URL(r.url()).pathname==='/bitacora'&&r.request().headers().accept?.includes('application/json'));
    await page.locator('#'+id+' + .filter-select__toggle').click();
    await page.getByRole('option',{name:label,exact:true}).click();
    await response;await page.waitForFunction(()=>!Alpine.$data(document.getElementById('bitacora')).cargandoBitacora);
  }
  const last=()=>requests.at(-1).searchParams;
  try {
    await check('module, state-change action and user each send the selected filter',async page=>{
      await select(page,'bitacora-modulo','Usuarios');assert.equal(last().get('modulo'),'Usuarios');
      await select(page,'bitacora-accion','CAMBIO_ESTADO');assert.equal(last().get('accion'),'CAMBIO_ESTADO');
      await select(page,'bitacora-usuario_id','Ana Demo (ana@example.test)');assert.equal(last().get('usuario_id'),'2');
      assert.equal(last().get('modulo'),'Usuarios');assert.equal(last().get('accion'),'CAMBIO_ESTADO');
    });
    await check('Filtrar keeps the combined selection',async page=>{
      await select(page,'bitacora-modulo','Productos');await select(page,'bitacora-accion','CREAR');await select(page,'bitacora-usuario_id','Luis Demo (luis@example.test)');
      const response=page.waitForResponse(r=>r.request().headers().accept?.includes('application/json'));
      await page.getByRole('button',{name:'Filtrar',exact:true}).click();await response;
      assert.equal(last().get('modulo'),'Productos');assert.equal(last().get('accion'),'CREAR');assert.equal(last().get('usuario_id'),'1');
    });
    await check('Limpiar filtros resets selections, labels and query',async page=>{
      await select(page,'bitacora-modulo','Usuarios');await select(page,'bitacora-accion','EDITAR');await select(page,'bitacora-usuario_id','Ana Demo (ana@example.test)');
      const response=page.waitForResponse(r=>r.request().headers().accept?.includes('application/json'));
      await page.getByRole('button',{name:'Limpiar Filtros',exact:true}).click();await response;
      for(const id of ['bitacora-modulo','bitacora-accion','bitacora-usuario_id']) assert.equal(await page.locator('#'+id).inputValue(),'');
      assert.equal(last().has('modulo'),false);assert.equal(last().has('accion'),false);assert.equal(last().has('usuario_id'),false);
      await page.waitForFunction(()=>document.querySelector('#bitacora-accion + button').getAttribute('aria-label').includes('Todas las acciones'));
    });
    await check('query filters restore visible selectors and survive submit',async page=>{
      await page.waitForFunction(()=>document.getElementById('bitacora-modulo').value==='Usuarios');
      assert.equal(await page.locator('#bitacora-accion').inputValue(),'CAMBIO_ESTADO');assert.equal(await page.locator('#bitacora-usuario_id').inputValue(),'2');
      await page.waitForFunction(()=>document.querySelector('#bitacora-usuario_id + button').getAttribute('aria-label').includes('Ana Demo'));
      const response=page.waitForResponse(r=>r.request().headers().accept?.includes('application/json'));
      await page.getByRole('button',{name:'Filtrar',exact:true}).click();await response;
      assert.equal(last().get('modulo'),'Usuarios');assert.equal(last().get('accion'),'CAMBIO_ESTADO');assert.equal(last().get('usuario_id'),'2');
    },'?modulo=Usuarios&accion=CAMBIO_ESTADO&usuario_id=2');
    await check('pagination keeps filters and a new selection starts at page one',async page=>{
      await select(page,'bitacora-modulo','Usuarios');await select(page,'bitacora-accion','CREAR');
      const response=page.waitForResponse(r=>r.request().headers().accept?.includes('application/json'));
      await page.getByRole('link',{name:'Página 2'}).click();await response;
      assert.equal(last().get('page'),'2');assert.equal(last().get('modulo'),'Usuarios');assert.equal(last().get('accion'),'CREAR');
      await select(page,'bitacora-accion','EDITAR');assert.equal(last().has('page'),false);assert.equal(last().get('accion'),'EDITAR');
    });
  }finally{await browser.close();await new Promise(resolve=>server.close(resolve));}
  console.log(`${passed}/${passed+failed} Bitácora browser checks passed`);process.exitCode=failed?1:0;
})().catch(error=>{console.error(error);server.close();process.exitCode=1;});
