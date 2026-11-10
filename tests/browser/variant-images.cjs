// Execute the real Blade variant form and production JavaScript in Chromium.
// Run from the repository root; PHP_BINARY and CHROMIUM_PATH are optional.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const { spawnSync } = require('node:child_process');
const { chromium } = require('playwright');
const root = path.resolve(__dirname, '../..');
const render = spawnSync(process.env.PHP_BINARY || 'php', ['tests/browser/render-variant-images.php'], { cwd: root, encoding: 'utf8', timeout: 60000 });
if (render.error || render.status !== 0) throw new Error(render.error?.message || render.stderr);
const fixture = `<!doctype html><meta name="csrf-token" content="fixture-token"><style>.hidden{display:none!important}.invisible{visibility:hidden}</style>
<button type="button" id="btnAgregarVariante">Nueva variante</button><div id="variantsContainer"><button type="button" class="btn-editar-variante" data-id="7">Editar variante</button></div>
${render.stdout}
<script>window.PRODUCTOS_ROUTES={storeVariante:'/productos/1/variantes',updateVariante:'/variantes',getVariante:'/variantes'};window.Swal={fire:async()=>({isConfirmed:false}),mixin:()=>({fire(){}})};</script>
<script type="module" src="/resources/js/loading-states.js"></script><script type="module" src="/resources/js/productos.js"></script>`;
const server = http.createServer((req, res) => {
  if (req.url === '/') { res.setHeader('Content-Type','text/html; charset=utf-8');res.end(fixture);return; }
  const file = path.resolve(root, '.' + new URL(req.url,'http://localhost').pathname);
  if (!file.startsWith(root + path.sep) || !fs.existsSync(file) || !fs.statSync(file).isFile()) { res.writeHead(404);res.end();return; }
  res.setHeader('Content-Type','application/javascript');res.end(fs.readFileSync(file));
});
const png = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=','base64');
const photo = name => ({name, mimeType:'image/png',buffer:png});
function files(request) {
  return [...request.postDataBuffer().toString('latin1').matchAll(/name="imagenes\[\]"; filename="([^"]*)"/g)].map(match=>match[1]);
}
const results=[];
(async()=>{
  await new Promise(resolve=>server.listen(0,'127.0.0.1',resolve));
  const base = `http://127.0.0.1:${server.address().port}`;
  const browser=await chromium.launch({headless:true,executablePath:process.env.CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
  async function check(name, run, edit=false) {
    const page=await browser.newPage();const errors=[];page.on('pageerror',error=>errors.push(error.message));
    try {
      await page.route('**/variantes/7', async route=>{
        if(route.request().method()==='GET') await route.fulfill({json:{success:true,variante:{id:7,nombre_variante:'Azul',sku:'VAR-AZU-001',estado:1,costo_promedio:0,porcentaje_ganancia:20,precio_venta:0,imagenes:[{id:11,url:'/old.png',es_principal:1}]}}});
        else await route.fulfill({status:422,json:{success:false,message:'Fixture keeps the modal open'}});
      });
      await page.route('**/productos/1/variantes',route=>route.fulfill({status:422,json:{success:false,message:'Fixture keeps the modal open'}}));
      await page.goto(base);await page.waitForFunction(()=>window.AxLoading);
      await page.click(edit?'.btn-editar-variante':'#btnAgregarVariante');
      await page.locator('#modalVariante').waitFor({state:'visible'});
      await page.fill('#nombre_variante','Azul');await page.fill('#porcentaje_ganancia','20');
      await run(page);
      assert.deepEqual(errors,[]);results.push(true);console.log('PASS '+name);
    } catch(error) {results.push(false);console.error('FAIL '+name+': '+error.stack);} finally {await page.close();}
  }
  async function submit(page, edit=false) {
    const pending=page.waitForRequest(request=>request.method()==='POST' && new URL(request.url()).pathname===(edit?'/variantes/7':'/productos/1/variantes'));
    await page.click('#btnGuardarVariante');return pending;
  }
  try {
    await check('one selected image is uploaded once',async page=>{
      await page.setInputFiles('#imagenesVarianteInput',photo('azul.png'));
      await page.locator('.preview-img-container.is-new').waitFor();
      assert.deepEqual(files(await submit(page)),['azul.png']);
    });
    await check('multiple images keep their order and selected main image',async page=>{
      await page.setInputFiles('#imagenesVarianteInput',[photo('primera.png'),photo('principal.png')]);
      await page.waitForFunction(()=>document.querySelectorAll('.preview-img-container.is-new').length===2);
      await page.locator('.preview-img-container.is-new .btn-star-img').nth(1).click();
      const request=await submit(page);assert.deepEqual(files(request),['primera.png','principal.png']);
      assert.match(request.postDataBuffer().toString('latin1'),/name="imagen_principal_index"\r\n\r\n1\r\n/);
    });
    await check('removed images stay excluded after another selection',async page=>{
      await page.setInputFiles('#imagenesVarianteInput',[photo('eliminar.png'),photo('conservar.png')]);
      await page.waitForFunction(()=>document.querySelectorAll('.preview-img-container.is-new').length===2);
      await page.locator('.preview-img-container.is-new').first().locator('button').first().click();
      await page.setInputFiles('#imagenesVarianteInput',photo('nueva.png'));
      await page.waitForFunction(()=>document.querySelectorAll('.preview-img-container.is-new').length===2);
      assert.deepEqual(files(await submit(page)),['conservar.png','nueva.png']);
    });
    await check('picker and drag-and-drop share one upload queue',async page=>{
      await page.setInputFiles('#imagenesVarianteInput',photo('seleccionada.png'));
      await page.locator('.preview-img-container.is-new').waitFor();
      await page.evaluate(bytes=>{
        const transfer=new DataTransfer();
        transfer.items.add(new File([new Uint8Array(bytes)],'arrastrada.png',{type:'image/png'}));
        document.getElementById('dropZoneImagenes').dispatchEvent(new DragEvent('drop',{bubbles:true,cancelable:true,dataTransfer:transfer}));
      },[...png]);
      await page.waitForFunction(()=>document.querySelectorAll('.preview-img-container.is-new').length===2);
      assert.deepEqual(files(await submit(page)),['seleccionada.png','arrastrada.png']);
    });
    await check('removing a newly selected photo while editing does not upload it',async page=>{
      await page.locator('.preview-img-container.is-existing').waitFor();
      await page.setInputFiles('#imagenesVarianteInput',photo('quitada.png'));
      await page.locator('.preview-img-container.is-new').waitFor();
      await page.locator('.preview-img-container.is-new button').first().click();
      assert.deepEqual(files(await submit(page,true)),[]);
    },true);
    await check('edit uploads only the new image and keeps the existing main image',async page=>{
      await page.locator('.preview-img-container.is-existing').waitFor();
      await page.setInputFiles('#imagenesVarianteInput',photo('adicional.png'));
      await page.locator('.preview-img-container.is-new').waitFor();
      const request=await submit(page,true);assert.deepEqual(files(request),['adicional.png']);
      assert.match(request.postDataBuffer().toString('latin1'),/name="imagen_existente_principal_id"\r\n\r\n11\r\n/);
    },true);
  } finally {await browser.close();await new Promise(resolve=>server.close(resolve));}
  console.log(`${results.filter(Boolean).length}/${results.length} variant image checks passed`);process.exitCode=results.every(Boolean)?0:1;
})().catch(error=>{console.error(error);server.close();process.exitCode=1;});
