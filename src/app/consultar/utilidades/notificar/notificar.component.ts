import { Component, OnInit } from '@angular/core';
import { ApiService, Curso, Notificacion } from 'src/app/servicios/api.service';
import { ExcelService } from 'src/app/servicios/excel.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-notificar',
  templateUrl: './notificar.component.html',
  styleUrls: ['./notificar.component.scss']
})
export class NotificarComponent implements OnInit {
  archivoCargado: boolean = false;
  data: any[] = [];
  notifSel: any[] = [];
  modalNotificar: boolean = false;
  notificando: boolean = false;
  numNotif: number = 0;
  porNotif: number = 0;
  errorToken: boolean = false;
  rutaToken: string = environment.ruta_api + 'assets/api/generaToken.php';
  tiposNotif: Array<{ tipo: string, label: string; }> = [
    { tipo: 'registro', label: 'Registro en plataforma' },
    { tipo: 'certificado', label: 'Generación de certificado' },
  ];
  tipoNotificacion: string = 'registro';
  constructor(private api: ApiService, private excel: ExcelService) { }
  ngOnInit(): void {
    this.api.generaToken().subscribe(res => {
      if (!res.token) this.errorToken = true;
    });
  }
  sueltaArchivo(archivo: File): void {
    this.archivoCargado = true;
    switch (this.tipoNotificacion) {
      case 'registro':
        this.procesaNotRegistro(archivo);
        break;
      case 'certificado':
        this.procesaNotCertificado(archivo);
        break;
    }
  }
  notificar(): void {
    this.notificando = true;
    this.numNotif = 0;
    this.porNotif = 0;
    switch (this.tipoNotificacion) {
      case 'registro':
        this.notificarRegistro();
        break;
      case 'certificado':
        this.notificarCertificado();
        break;
    }
  }
  notificarCertificado(): void {
    this.notifSel.forEach((participante: { nombre: string, email: string, curso: string; }) => {
      const notificacion: Notificacion = {
        nombre: participante.nombre,
        correo: participante.email,
        curso: participante.curso
      };
      this.api.sendMail(notificacion).subscribe(res => {
        if (res.error == null) {
          this.numNotif++;
          this.porNotif = Math.ceil((this.numNotif * 100) / this.data.length);
        }
      });
    });
  }
  notificarRegistro(): void {
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
  procesaNotCertificado(archivo: File): void {
    this.excel.readExcelFile(archivo).subscribe(res => {
      if (res) {
        this.data = [];
        res.forEach((row: string[], index: number) => {
          if (index > 0) this.data.push({ nombre: row[0] + ' ' + row[1], email: row[2], curso: row[4] });
        });
      }
    });
  }
  procesaNotRegistro(archivo: File): void {
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
        if (fila.course1 != '') {
          fila.cursos.push({
            curso: this.api.cursos.find((curso: Curso) => curso.corto == fila.course1)?.largo,
            inicio: new Date(new Date(fila.enroltimestart1).getTime() + dia).toLocaleDateString('co-ES', dateOp),
            fin: new Date(new Date(fila.enroltimestart1).getTime() + (parseInt(fila.enrolperiod1) * dia)).toLocaleDateString('co-ES', dateOp),
          });
        }
        if (fila.course2 != '') {
          fila.cursos.push({
            curso: this.api.cursos.find((curso: Curso) => curso.corto == fila.course2)?.largo,
            inicio: new Date(new Date(fila.enroltimestart2).getTime() + dia).toLocaleDateString('co-ES', dateOp),
            fin: new Date(new Date(fila.enroltimestart2).getTime() + (parseInt(fila.enrolperiod2) * dia)).toLocaleDateString('co-ES', dateOp),
          });
        }
        if (fila.course3 != '') {
          fila.cursos.push({
            curso: this.api.cursos.find((curso: Curso) => curso.corto == fila.course3)?.largo,
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
}
