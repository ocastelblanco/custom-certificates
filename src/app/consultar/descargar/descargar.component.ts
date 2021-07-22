import { Component, OnInit } from '@angular/core';
import { ApiService, Notificacion, ErrorEnvio } from 'src/app/servicios/api.service';
import { SesionService } from 'src/app/servicios/sesion.service';
import { Certificado, PdfService } from 'src/app/servicios/pdf.service';
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
  abreModal: boolean = false;
  modalNotificar: boolean = false;
  notificando: boolean = false;
  numNotif: number = 0;
  porNotif: number = 0;
  errores: ErrorEnvio[] = [];
  notificados: number = 0;
  constructor(
    public sesion: SesionService,
    private api: ApiService,
    private pdf: PdfService,
    private fs: FileSaverService
  ) { }
  ngOnInit(): void {
    this.generaData();
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
    let salida: number
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
}
