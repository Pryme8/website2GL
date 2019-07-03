<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Babylon Template</title>

        <style>
            html, body {
                overflow: hidden;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            #renderCanvas {
                width: 100%;
                height: 100%;
                touch-action: none;
				position:absolute;
            }
			
			.data-block {				
				width: 100%;
                height: 100%;
				position:absolute;
				z-index:-1;				
			}
        </style>

        <script src="https://preview.babylonjs.com/babylon.js"></script>
        <script src="https://code.jquery.com/pep/0.4.3/pep.js"></script>
		<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
		<script src=https://preview.babylonjs.com/inspector/babylon.inspector.bundle.js></script>
		
    </head>

   <body>

    <canvas id="renderCanvas" touch-action="none"></canvas>

    <script>
		class GetData{
			constructor(name, url, target, scene){				
				this._id = name
				this._scene = scene
				this._target = target
				this._texture = new BABYLON.DynamicTexture('dt', {width:1, height:1}, scene, false, 1)
				
				let xhttp = new XMLHttpRequest()		
				
				var self = this
				function complete(response){
					var data = document.createElement('div')					
					data.innerHTML = response
					data.classList.add('data-block')
					data.setAttribute('id', name)
					self.data = data
					document.body.appendChild(self.data)
					self.update()
				}
	
				xhttp.onreadystatechange = function() {		
					if(this.status == 404 || this.status == 403){
						console.log("Error: Can't Obtain Data Response - code("+this.status+")")
						this.abort()
						return false
					}			
					if (this.readyState == 4 && this.status == 200){     			
						complete(this.responseText)
					}		
				}
				
				xhttp.open("POST", "getWebsite.php", true);
				xhttp.send(JSON.stringify({url}));
			}

			update(){		
				self = this
				html2canvas(document.getElementById(this.id), {}).then(_canvas => {
					self.texture.dispose()
					var _ctx = _canvas.getContext('2d')
					var dt = new BABYLON.DynamicTexture('dt', {width:_canvas.width, height:_canvas.height}, self.scene, false, 1)
					var dctx = dt._context
					var _iDat = _ctx.getImageData(0,0,_canvas.width, _canvas.height)						
					dctx.putImageData(_iDat, 0, 0);							
					dt.update(false)				
					self.texture = dt
					self.target.material = new BABYLON.StandardMaterial('test', self.scene)
					self.target.material.diffuseTexture = dt
				})
			}						
			
			get id(){return this._id}
			set id(v){this._id = v}			
			
			get data(){return this._data}
			set data(v){this._data = v}
			
			get scene(){return this._scene}
			
			get texture(){return this._texture}
			set texture(v){this._texture = v}
			
			get target(){return this._target}
			set target(v){this._target = v}
			
		}	
	
        var canvas = document.getElementById("renderCanvas"); // Get the canvas element 
        var engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
        var createScene = function () {
            var scene = new BABYLON.Scene(engine);
            var camera = new BABYLON.ArcRotateCamera("Camera", Math.PI / 2, Math.PI / 2, 2, new BABYLON.Vector3(0,0,5), scene);
            camera.attachControl(canvas, true);
            var light1 = new BABYLON.HemisphericLight("light1", new BABYLON.Vector3(1, 1, 0), scene);
            var light2 = new BABYLON.PointLight("light2", new BABYLON.Vector3(0, 1, -1), scene);
            var box = BABYLON.MeshBuilder.CreateBox("output", {size:2}, scene);		
			
			var dataTexture = new GetData('testGrab','http://google.com', box, scene)		
			
			
			
            return scene;
        };


        var scene = createScene(); 


        engine.runRenderLoop(function () { 
                scene.render();
        });

        window.addEventListener("resize", function () { 
                engine.resize();
        });
		
		
		
	
		
		
		
    </script>

   </body>

</html>