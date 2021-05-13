import { Component, OnInit } from '@angular/core';
import { ApiService } from 'src/app/servicios/api.service';
import { SesionService, User } from 'src/app/servicios/sesion.service';
import { Certificado, CrearPDFService } from 'src/app/servicios/crear-PDF.service';
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
  constructor(
    public sesion: SesionService,
    private api: ApiService,
    private crearPDF: CrearPDFService,
    private fs: FileSaverService
  ) { }
  ngOnInit(): void {
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
  pdf() {
    this.abreModal = true;
    const zip: JSZip = new JSZip();
    this.certSel.forEach(cert => {
      const pdf: jsPDF = this.crearPDF.get(cert);
      const date: number = parseInt(cert.fecha) * 1000;
      const fecha: string = this.crearPDF.dosDig(new Date(date).getMonth()) + '-' + new Date(date).getFullYear();
      const nombrePDF: string = cert.id + '-' + cert.coursename + '-' + cert.idnumber + '-' + fecha + '.pdf';
      zip.file(nombrePDF, pdf.output('blob'));
    });
    let hoy: Date = new Date(Date.now());
    let nomZIP: string = 'Cert_ACG_';
    nomZIP += hoy.getFullYear();
    nomZIP += this.crearPDF.dosDig(hoy.getMonth() + 1);
    nomZIP += this.crearPDF.dosDig(hoy.getDate());
    nomZIP += this.crearPDF.dosDig(hoy.getHours());
    nomZIP += this.crearPDF.dosDig(hoy.getMinutes());
    nomZIP += this.crearPDF.dosDig(hoy.getSeconds());
    nomZIP += '.zip';
    zip.generateAsync({ type: 'blob' }).then(res => {
      this.fs.save(res, nomZIP);
      this.abreModal = false;
    });
  }
}
