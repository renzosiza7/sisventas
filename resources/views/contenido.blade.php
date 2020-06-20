@extends('principal')

@section('contenido')



@if(Auth::check())

    @if (Auth::user()->idrol == 1)    

    <template v-if="menu == 'inicio'">

        <dashboard-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'categorias'">

        <categoria-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'marcas'">

        <marca-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'articulos'">

        <articulo-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'tipo_gastos'">

        <tipo-gasto-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'clientes'">

        <cliente-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'proveedores'">

        <proveedor-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'roles'">

        <rol-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'usuarios'">

        <usuario-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'ingresos'">

        <ingreso-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'ventas'">

        <venta-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'servicios'">

        <servicio-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'caja'">

        <caja-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'consulta_ingreso'">

        <consulta-ingreso-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'consulta_venta'">

        <consulta-venta-component :key="renderKey" :ruta="ruta"/>

    </template>

    @elseif (Auth::user()->idrol == 2)

    <template v-if="menu == 'inicio'">

        <dashboard-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'clientes'">

        <cliente-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'ventas'">

        <venta-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'servicios'">

        <servicio-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>    

    <template v-if="menu == 'consulta_venta'">

        <consulta-venta-component :key="renderKey" :ruta="ruta"/>

    </template>    

    @elseif (Auth::user()->idrol == 3)

    <template v-if="menu == 'inicio'">

        <dashboard-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'categorias'">

        <categoria-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'marcas'">

        <marca-component :key="renderKey" :ruta="ruta"/>

    </template>    

    <template v-if="menu == 'articulos'">

        <articulo-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'proveedores'">

        <proveedor-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'ingresos'">

        <ingreso-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'consulta_ingreso'">

        <consulta-ingreso-component :key="renderKey" :ruta="ruta"/>

    </template>

    @elseif (Auth::user()->idrol == 4)

    <template v-if="menu == 'inicio'">

        <dashboard-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'clientes'">

        <cliente-component :key="renderKey" :ruta="ruta"/>

    </template>

    <template v-if="menu == 'ventas'">

        <venta-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'servicios'">

        <servicio-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>    

    <template v-if="menu == 'consulta_venta'">

        <consulta-venta-component :key="renderKey" :ruta="ruta"/>

    </template>    

    <template v-if="menu == 'caja'">

        <caja-component :key="renderKey" :ruta="ruta" :idrol="{{ Auth::user()->idrol }}" :iduser="{{ Auth::user()->id }}"/>

    </template>

    <template v-if="menu == 'tipo_gastos'">

        <tipo-gasto-component :key="renderKey" :ruta="ruta"/>

    </template>

    @else



    @endif

@endif



@endsection