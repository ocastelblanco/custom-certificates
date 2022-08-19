import { Component, OnInit } from '@angular/core';
import { ApiService, Calificaciones, ErrorEnvio, Nombres, Notificacion } from 'src/app/servicios/api.service';
import { ExcelService } from 'src/app/servicios/excel.service';
import { PdfService } from 'src/app/servicios/pdf.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-generar',
  templateUrl: './generar.component.html',
  styleUrls: ['./generar.component.scss']
})
export class GenerarComponent implements OnInit {
  data!: Calificaciones[];
  calSel: Calificaciones[] = [];
  nombres: Nombres = {
    nombres: 'Nombres',
    apellidos: 'Apellidos',
    email: 'Email',
    identificacion: '# Identidad',
    coursename: 'Curso',
    notaFinal: 'Nota',
    fecha: 'Fecha de evaluación'
  };
  modalNotificar: boolean = false;
  notificando: boolean = false;
  numNotif: number = 0;
  porNotif: number = 0;
  errores: ErrorEnvio[] = [];
  generados: number = 0;
  notificados: number = 0;
  errorToken: boolean = false;
  rutaToken: string = environment.ruta_api + 'assets/api/generaToken.php';
  constructor(private api: ApiService, private excel: ExcelService, private pdf: PdfService) { }
  ngOnInit(): void {
    this.generaData();
    this.api.generaToken().subscribe(res => {
      if (!res.token) this.errorToken = true;
    });
  }
  generaData(): void {
    this.data = [];
    this.api.listUsers().subscribe(c => this.data = c);
  }
  exportar(data: Calificaciones[]): void {
    const salida: Nombres[] = [this.nombres];
    data.forEach(r => {
      salida.push({
        nombres: r.nombres,
        apellidos: r.apellidos,
        email: r.email,
        identificacion: r.identificacion,
        coursename: r.coursename,
        notaFinal: r.notaFinal,
        fecha: r.fecha
      });
    });
    let hoy: Date = new Date(Date.now());
    let nomExcel: string = 'Usuarios_ACG_';
    nomExcel += hoy.getFullYear();
    nomExcel += this.pdf.dosDig(hoy.getMonth() + 1);
    nomExcel += this.pdf.dosDig(hoy.getDate());
    nomExcel += this.pdf.dosDig(hoy.getHours());
    nomExcel += this.pdf.dosDig(hoy.getMinutes());
    nomExcel += this.pdf.dosDig(hoy.getSeconds());
    this.excel.exportAsExcelFile(salida, nomExcel, true);
  }
  notificar(num: number = 0) {
    this.notificando = true;
    const cert: Calificaciones = this.calSel[num];
    const notificacion: Notificacion = {
      nombre: cert.nombres + ' ' + cert.apellidos,
      curso: cert.fullname,
      correo: cert.email
    };
    this.api.postCert(cert).subscribe(res => {
      if (res.error) console.log(res);
      const certid: string = res.id;
      this.generados++;
      this.api.sendMail(notificacion).subscribe(r => {
        if (r.error) {
          this.errores.push({
            userid: cert.userid,
            nombre: notificacion.nombre,
            email: cert.email,
            error: r.error
          });
        }
        this.api.postNot(certid).subscribe(n => this.notificados++);
        num++;
        this.numNotif = num;
        this.porNotif = Math.ceil(num / this.calSel.length * 100);
        window.setTimeout(() => {
          num < this.calSel.length ? this.notificar(num) : null;
        }, 500);
      });
    });
  }
  cerrarModal(): void {
    this.generaData();
    this.modalNotificar = false;
    this.notificando = false;
    this.calSel = [];
    this.numNotif = 0;
    this.porNotif = 0;
  }
}
