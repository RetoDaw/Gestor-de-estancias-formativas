<script setup>
import "../assets/css/calendario.css";
const calendario = {
    fecIni: '01/03/2026',
    fecFin: '01/06/2026'
}
const horario = {
    lunes: {
        horaIni: [8,14],
        horaFin: [13,17]
    },
    martes: {
        horaIni: [8,14],
        horaFin: [13,17]
    },
    miercoles: {
        horaIni: [8,14],
        horaFin: [13,17]
    },
    jueves: {
        horaIni: [8,14],
        horaFin: [13,17]
    },
    viernes: {
        horaIni: [8],
        horaFin: [15]
    }
}

function ponerHorario(dia){
    let horas = [];
    for(let i = 0;i < dia.horaIni.length;i++){
        horas.push(`${dia.horaIni[i]}:00 - ${dia.horaFin[i]}:00`);
    }
    return horas.join(', ');
}
</script>
<template>
    <h1>Calendario</h1>
    <div id="calendario">
        <table>
            <tbody>
                <tr>
                    <td>
                        <b>Fecha de Inicio:</b> {{ calendario.fecIni }}
                    </td>
                    <td>
                        <b>Fecha de Fin:</b> {{ calendario.fecFin }}
                    </td>
                </tr>
                <tr v-for="(dia,nombre) in horario" :key="nombre">
                    <td colspan="2"><b>{{ nombre }}:</b> {{ ponerHorario(dia) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>