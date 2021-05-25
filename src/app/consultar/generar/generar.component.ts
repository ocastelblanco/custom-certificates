import { Component, OnInit } from '@angular/core';
import { ApiService, Calificaciones, Nombres } from 'src/app/servicios/api.service';
import { ExcelService } from 'src/app/servicios/excel.service';
import { PdfService } from 'src/app/servicios/pdf.service';

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
    fecha: 'Última fecha'
  };
  constructor(private api: ApiService, private excel: ExcelService, private pdf: PdfService) { }
  ngOnInit(): void {
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
}
