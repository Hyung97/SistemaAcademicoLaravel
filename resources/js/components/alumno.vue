<template>
    <form v-on:submit.prevent="guardaralumno" v-on:reset="limpiar">
        <div class="row">
            <div class="col-sm-5">
                <div class="row p-2">
                    <div class="col-sm text-center text-white bg-primary">
                        <div class="row">
                            <div class="col-11">
                                <h5>REGISTRO DE alumnos</h5>
                            </div>
                            <div class="col-1 align-middle" >
                                <button type="button" onclick="appVue.forms['alumno'].mostrar=false" class="btn-close" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm">CODIGO:</div>
                    <div class="col-sm">
                        <input v-model="alumno.codigo" required type="text" class="form-control form-control-sm" >
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm">NOMBRE: </div>
                    <div class="col-sm">
                        <input v-model="alumno.nombre" required pattern="[A-ZÑña-z0-9, ]{5,65}" type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm">DEPARTAMENTO: </div>
                    <div class="col-sm">
                        <input v-model="alumno.departamento" required pattern="[A-ZÑña-z0-9, ]{5,65}" type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm">MUNICIPIO: </div>
                    <div class="col-sm">
                        <input v-model="alumno.municipio" required pattern="[A-ZÑña-z0-9, ]{5,65}" type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm">SEXO: </div>
                    <div class="col-sm">
                        <input v-model="alumno.sexo" required pattern="[A-ZÑña-z0-9, ]{5,65}" type="text" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm text-center">
                        <input type="submit" value="Guardar" class="btn btn-dark">
                        <input type="reset" value="Limpiar" class="btn btn-warning">
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-sm text-center">
                        <div v-if="status" class="alert" v-bind:class="[error ? 'alert-danger' : 'alert-success']">
                            {{ msg }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm"></div>
            <div class="col-sm-6 p-2">
                <div class="row text-center text-white bg-primary">
                    <div class="col"><h5>ALUMNOS REGISTRADOS</h5></div>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <td colspan="5">
                                        <input v-model="buscar" v-on:keyup="buscandoalumno" type="text" class="form-control form-contro-sm" placeholder="Buscar alumnos">
                                    </td>
                                </tr>
                                <tr>
                                    <th>CODIGO</th>
                                    <th>NOMBRE</th>
                                    <th>DEPARTAMENTO</th>
                                    <th>MUNICIPIO</th>
                                    <th>SEXO</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pro in alumnos" v-bind:key="pro.idAlumno" v-on:click="mostraralumno(pro)">
                                    <td>{{ pro.codigo }}</td>
                                    <td>{{ pro.nombre }}</td>
                                    <td>{{ pro.departamento }}</td>
                                    <td>{{ pro.municipio }}</td>
                                    <td>{{ pro.sexo }}</td>
                                    <td>
                                        <a @click.stop="eliminaralumno(pro)" class="btn btn-danger">DEL</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    var generarIdUnicoDesdeFecha=()=>{
        let fecha = new Date();//03/02/2021
        return Math.floor(fecha.getTime()/1000).toString(16);
    };
    export default {
        data:()=>{
            return {
                accion : 'nuevo',
                msg    : '',
                status : false,
                error  : false,
                buscar : "",
                alumno:{
                    id        : 0,
                    idAlumno : 0,
                    codigo    : '',
                    nombre    : '',
                    direccion : '',
                    municipio: '',
                    sexo: '',
                },
                alumnos:[]
            }
        },
        methods:{
            buscandoalumno(){
                this.alumnos = this.alumnos.filter((element,index,alumnos) => element.nombre.toUpperCase().indexOf(this.buscar.toUpperCase())>=0 || element.codigo.toUpperCase().indexOf(this.buscar.toUpperCase())>=0 );
                if( this.buscar.length<=0){
                    this.obtenerDatos();
                }
            },
            buscandoCodigoalumno(store){
                let buscarCodigo = new Promise( (resolver,rechazar)=>{
                    let index = store.index("codigo"),
                        data = index.get(this.alumno.codigo);
                    data.onsuccess=evt=>{
                        resolver(data);
                    };
                    data.onerror=evt=>{
                        rechazar(data);
                    };
                });
                return buscarCodigo;
            },
            async guardaralumno(){
                /**
                 * webSQL -> DB Relacional en el navegador
                 * localStorage -> BD NOSQL clave/valor
                 * indexedDB -> BD NOSQL clave/valor
                 */
                let store = this.abrirStore("tblalumnos",'readwrite'),
                    duplicado = false;
                if( this.accion=='nuevo' ){
                    this.alumno.idAlumno = generarIdUnicoDesdeFecha();
                    
                    let data = await this.buscandoCodigoalumno(store);
                    duplicado = data.result!=undefined;
                }
                if( duplicado==false){
                    if( this.accion=='nuevo' ){
                        const resp = await axios.post('alumnos',this.alumno);
                        this.alumno.id = resp.data;
                    } else {
                        const resp = await axios.put(`alumnos/${this.alumno.id}`,this.alumno);
                    }
                    let tabla = this.abrirStore("tblalumnos",'readwrite'),
                        query = tabla.put(this.alumno);
                    query.onsuccess=event=>{
                        this.obtenerDatos();
                        this.limpiar();
                        
                        this.mostrarMsg('Registro se guardo con exito',false);
                    };
                    query.onerror=event=>{
                        this.mostrarMsg('Error al guardar el registro',true);
                        console.log( event );
                    };
                } else{
                    this.mostrarMsg('Codigo de alumno duplicado',true);
                }
            },
            mostrarMsg(msg, error){
                this.status = true;
                this.msg = msg;
                this.error = error;
                this.quitarMsg(3);
            },
            quitarMsg(time){
                setTimeout(()=>{
                    this.status=false;
                    this.msg = '';
                    this.error = false;
                }, time*1000);
            },
            obtenerDatos(){
                let store = this.abrirStore('tblalumnos','readonly'),
                    data = store.getAll();
                data.onsuccess=async resp=>{
                    if( data.result.length===0 ){
                        const alumnos = await axios.get('alumnos');
                        this.alumnos = alumnos.data;
                        let tabla = this.abrirStore('tblalumnos','readwrite');
                        this.alumnos.forEach(element => {
                            let alumno = {
                                id        : element.id,
                                idAlumno : element.idAlumno,
                                codigo    : element.codigo,
                                nombre    : element.nombre,
                                direccion : element.direccion,
                                telefono  : element.telefono,
                            };
                            tabla.put(alumno);
                        });
                    } else {
                        this.alumnos = data.result;
                    }
                };
            },
            mostraralumno(pro){
                this.alumno = pro;
                this.accion='modificar';
            },
            limpiar(){
                this.accion='nuevo';
                this.alumno.idAlumno='';
                this.alumno.codigo='';
                this.alumno.nombre='';
                this.alumno.direccion='';
                this.alumno.telefono='';
                this.obtenerDatos();
            },
            async eliminaralumno(pro){
                if( confirm(`Esta seguro que desea eliminar el alumno:  ${pro.nombre}`) ){
                    const data = await axios.delete(`alumnos/${pro.id}`);
                    
                    let store = this.abrirStore("tblalumnos",'readwrite'),
                        req = store.delete(pro.idAlumno);
                    req.onsuccess=resp=>{
                        this.mostrarMsg('Registro eliminado con exito',true);
                        this.obtenerDatos();
                    };
                    req.onerror=resp=>{
                        this.mostrarMsg('Error al eliminar el registro',true);
                        console.log( resp );
                    };
                }
            },
            abrirStore(store,modo){
                let tx = db.transaction(store,modo);
                return tx.objectStore(store);
            }
        },
        created(){
            //this.obtenerDatos();
        },
    }
</script>