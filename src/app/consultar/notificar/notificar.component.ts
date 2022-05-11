import { Component } from '@angular/core';
import { ApiService } from 'src/app/servicios/api.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-notificar',
  templateUrl: './notificar.component.html',
  styleUrls: ['./notificar.component.scss']
})
export class NotificarComponent {
  nombresCursos: any = {
    "ACG Calidad - Campus Virtual": "ACG CALIDAD",
    "EM_calidad_analitica": "CURSO VIRTUAL GESTIÓN ANALÍTICA EN EL LABORATORIO CLÍNICO",
    "EM_paciente_seguro": "CURSO VIRTUAL HERRAMIENTAS PRÁCTICAS PARA LA IMPLEMENTACIÓN, MONITOREO Y MEJORAMIENTO DEL PROGRAMA DE SEGURIDAD DEL PACIENTE",
    "EM_gestion_riesgo": "CURSO VIRTUAL GESTIÓN DEL RIESGO APLICADO EN EL LABORATORIO CLINICO",
    "EM_seis_sigma": "CURSO VIRTUAL IMPLEMENTE LA ESTRATEGIA SEIS SIGMA EN EL LABORATORIO CLINICO",
    "EM_bioseguridad-covid19": "CURSO VIRTUAL IMPLEMENTACIÓN DE PROTOCOLOS DE BIOSEGURIDAD PARA LABORATORIOS CLÍNICOS DURANTE LA PANDEMIA COVID-19",
    "bioseguridad-covid19": "CURSO VIRTUAL IMPLEMENTACIÓN DE PROTOCOLOS DE BIOSEGURIDAD PARA LABORATORIOS CLÍNICOS DURANTE LA PANDEMIA COVID-19",
    "EMI_diplo_calidad": "DIPLOMADO DE CALIDAD",
    "EMI_diplo_calidad_2020-11-05": "DIPLOMADO DE CALIDAD NOV 2020",
    "EMI_diplo_calidad_2021-02-22": "DIPLOMADO DE CALIDAD FEB 2021",
    "EMI_diplo_calidad_2021-08": "DIPLOMADO DE CALIDAD AGO 2021",
    "EM_seis_sigma_julio-2021": "SEIS SIGMA JULIO 2021",
    "EMI_poct_no_bact": "CURSO VIRTUAL POCT PARA NO BACTERIÓLOGOS",
    "EM_salud_trabajo": "CURSO VIRTUAL SISTEMAS DE GESTIÓN Y SEGURIDAD EN SALUD EN EL TRABAJO",
    "SEM_Diplom_Calidad": "Semilla DIPLOMADO DE CALIDAD"
  };
  archivoCargado: boolean = false;
  data: any[] = [];
  notifSel: any[] = [];
  modalNotificar: boolean = false;
  notificando: boolean = false;
  numNotif: number = 0;
  porNotif: number = 0;
  errorToken: boolean = false;
  rutaToken: string = environment.ruta_api + 'assets/api/generaToken.php';
  constructor(private api: ApiService) { }
  sueltaArchivo(archivo: File): void {
    this.archivoCargado = true;
    const reader: FileReader = new FileReader();
    const dateOp: any = { year: 'numeric', month: 'long', day: 'numeric' };
    const dia: number = 24 * 60 * 60 * 1000;
    reader.readAsText(archivo);
    reader.onload = () => {
      this.data = [];
      const resultado: string = reader.result as string;
      const filas: string[] = resultado.split('\n');
      const encabezados: string[] = filas[0].split(',');
      for (let i = 1; i < filas.length; i++) {
        const filaRaw: string[] = filas[i].split(',');
        const fila: any = {};
        encabezados.forEach((encabezado: string, pos: number) => {
          fila[encabezado] = filaRaw[pos];
        });
        fila.cursos = [];
        if (fila.course1 == 'EM_seis_sigma' && fila.course2 == 'EM_calidad_analitica') {
          fila.cursos.push({
            curso: 'CURSO INTEGRADO DE CONTROL DE CALIDAD',
            inicio: new Date(new Date(fila.enroltimestart1).getTime() + dia).toLocaleDateString('co-ES', dateOp),
            fin: new Date(new Date(fila.enroltimestart1).getTime() + (parseInt(fila.enrolperiod1) * dia)).toLocaleDateString('co-ES', dateOp),
          });
        } else {
          if (fila.course1 != '') {
            fila.cursos.push({
              curso: this.nombresCursos[fila.course1],
              inicio: new Date(new Date(fila.enroltimestart1).getTime() + dia).toLocaleDateString('co-ES', dateOp),
              fin: new Date(new Date(fila.enroltimestart1).getTime() + (parseInt(fila.enrolperiod1) * dia)).toLocaleDateString('co-ES', dateOp),
            });
          }
          if (fila.course2 != '') {
            fila.cursos.push({
              curso: this.nombresCursos[fila.course2],
              inicio: new Date(new Date(fila.enroltimestart2).getTime() + dia).toLocaleDateString('co-ES', dateOp),
              fin: new Date(new Date(fila.enroltimestart2).getTime() + (parseInt(fila.enrolperiod2) * dia)).toLocaleDateString('co-ES', dateOp),
            });
          }
        }
        if (fila.course3 != '') {
          fila.cursos.push({
            curso: this.nombresCursos[fila.course3],
            inicio: new Date(new Date(fila.enroltimestart3).getTime() + dia).toLocaleDateString('co-ES', dateOp),
            fin: new Date(new Date(fila.enroltimestart3).getTime() + (parseInt(fila.enrolperiod3) * dia)).toLocaleDateString('co-ES', dateOp),
          });
        }
        fila.cursos.forEach((curso: any) => {
          this.data.push({
            nombre: fila.firstname + ' ' + fila.lastname,
            email: fila.email,
            curso: curso.curso,
            inicio: curso.inicio,
            fin: curso.fin,
            username: fila.username,
            password: fila.password,
          });
        });
      }
    };
  }
  notificar(): void {
    this.notificando = true;
    this.numNotif = 0;
    this.porNotif = 0;
    this.notifSel.forEach(participante => {
      this.api.notificaNuevos(participante).subscribe(res => {
        if (res.error == null) {
          this.numNotif++;
          this.porNotif = Math.round((this.numNotif * 100) / this.data.length);
        }
      });
    });
  }
  cerrarModal(): void {
    this.modalNotificar = false;
    this.notificando = false;
    this.notifSel = [];
    this.numNotif = 0;
    this.porNotif = 0;
  }
}
