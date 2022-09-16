import { Component, OnInit } from '@angular/core';
import { ApiService, Notificacion, ErrorEnvio } from 'src/app/servicios/api.service';
import { SesionService } from 'src/app/servicios/sesion.service';
import { Certificado, PdfService } from 'src/app/servicios/pdf.service';
import { ExcelService, ReporteXLSX } from 'src/app/servicios/excel.service';
import { environment } from 'src/environments/environment';
import { FileSaverService } from 'ngx-filesaver';
import { jsPDF } from 'jspdf';
import * as JSZip from 'jszip';

@Component({
  selector: 'app-descargar',
  templateUrl: './descargar.component.html',
  styleUrls: ['./descargar.component.scss']
})
export class DescargarComponent implements OnInit {
  data!: Certificado[];
  certSel: Certificado[] = [];
  encabezadoExcel: ReporteXLSX = {
    nombres: "Nombres y Apellidos",
    identificacion: "# Identificación",
    certificado: "Número de Certificado",
    curso: "Evento",
    intensidad: "Intensidad horaria",
    fecha: "Fecha de emisión",
    ubicacion: "Ciudad/País",
    modalidad: "Modalidad",
    empresa: "Empresa",
  };
  abreModal: boolean = false;
  modalNotificar: boolean = false;
  notificando: boolean = false;
  numNotif: number = 0;
  porNotif: number = 0;
  errores: ErrorEnvio[] = [];
  notificados: number = 0;
  errorToken: boolean = false;
  rutaToken: string = environment.ruta_api + 'assets/api/generaToken.php';
  precarga: boolean = false;
  constructor(
    public sesion: SesionService,
    private api: ApiService,
    private pdf: PdfService,
    private fs: FileSaverService,
    private excel: ExcelService
  ) { }
  ngOnInit(): void {
    this.precarga = true;
    window.setTimeout(() => {
      this.precarga = false;
      this.generaData();
    }, 3000);
    this.api.generaToken().subscribe(res => {
      if (!res.token) this.errorToken = true;
    });
  }
  generaData(): void {
    this.data = [];
    let id: string | null = null;
    if (this.sesion.perfil) {
      id = this.sesion.perfil.admin ? null : this.sesion.perfil.id;
      this.api.getCert(id).subscribe(r => this.data = r);
    }
  }
  fecha(s: string): number {
    let salida: number;
    if (s === '0') {
      salida = 0;
    } else {
      salida = parseInt(s) * 1000;
    }
    return salida;
  }
  crearPdf() {
    this.abreModal = true;
    const zip: JSZip = new JSZip();
    this.certSel.forEach(cert => {
      const pdf: jsPDF = this.pdf.get(cert);
      const date: number = parseInt(cert.fecha) * 1000;
      const fecha: string = this.pdf.dosDig(new Date(date).getMonth()) + '-' + new Date(date).getFullYear();
      const nombrePDF: string = cert.id + '-' + cert.coursename + '-' + cert.idnumber + '-' + fecha + '.pdf';
      zip.file(nombrePDF, pdf.output('blob'));
    });
    let hoy: Date = new Date(Date.now());
    let nomZIP: string = 'Cert_ACG_';
    nomZIP += hoy.getFullYear();
    nomZIP += this.pdf.dosDig(hoy.getMonth() + 1);
    nomZIP += this.pdf.dosDig(hoy.getDate());
    nomZIP += this.pdf.dosDig(hoy.getHours());
    nomZIP += this.pdf.dosDig(hoy.getMinutes());
    nomZIP += this.pdf.dosDig(hoy.getSeconds());
    nomZIP += '.zip';
    zip.generateAsync({ type: 'blob' }).then(res => {
      this.fs.save(res, nomZIP);
      this.abreModal = false;
    });
  }
  notificar(num: number = 0): void {
    this.notificando = true;
    const cert: Certificado = this.certSel[num];
    const notificacion: Notificacion = {
      nombre: cert.firstname + ' ' + cert.lastname,
      curso: cert.fullname,
      correo: cert.email
    };
    // Envía emails
    this.api.sendMail(notificacion).subscribe(r => {
      if (r.error) {
        this.errores.push({
          userid: cert.userid,
          nombre: notificacion.nombre,
          email: cert.email,
          error: r.error
        });
      }
      this.api.postNot(cert.id).subscribe(n => this.notificados++);
      num++;
      this.numNotif = num;
      this.porNotif = Math.ceil(num / this.certSel.length * 100);
      window.setTimeout(() => num < this.certSel.length ? this.notificar(num) : null, 500);
    });
  }
  cerrarModal(): void {
    this.generaData();
    this.modalNotificar = false;
    this.notificando = false;
    this.certSel = [];
    this.numNotif = 0;
    this.porNotif = 0;
  }
  crearExcel(): void {
    const salida: ReporteXLSX[] = [this.encabezadoExcel];
    this.certSel.forEach(cert => {
      const fecha: Date = new Date(parseInt(cert.fecha) * 1000);
      salida.push({
        nombres: cert.firstname + ' ' + cert.lastname,
        identificacion: cert.idnumber,
        certificado: 'CV-' + cert.id,
        curso: cert.fullname,
        intensidad: cert.intensidad,
        fecha: this.pdf.dosDig(fecha.getMonth() + 1) + '-' + fecha.getFullYear(),
        ubicacion: cert.city + ', ' + cert.country,
        modalidad: 'Virtual',
        empresa: cert.institution
      });
    });
    let hoy: Date = new Date(Date.now());
    let nomExcel: string = 'Certificados_ACG_';
    nomExcel += hoy.getFullYear();
    nomExcel += this.pdf.dosDig(hoy.getMonth() + 1);
    nomExcel += this.pdf.dosDig(hoy.getDate());
    nomExcel += this.pdf.dosDig(hoy.getHours());
    nomExcel += this.pdf.dosDig(hoy.getMinutes());
    nomExcel += this.pdf.dosDig(hoy.getSeconds());
    this.excel.exportAsExcelFile(salida, nomExcel, true);
  }
}
